<?php

/**
 * Cifrado/ofuscación de IDs que viajan por la URL en el flujo de compra.
 *
 * enc_id($id)  -> token base64url (AES-256-CBC con IV aleatorio). Ofusca el id
 *                 para que no sea enumerable en la URL.
 * dec_id($tok) -> id numérico original, o null si el token es inválido.
 *                 Acepta también ids numéricos crudos (retrocompatibilidad con
 *                 enlaces viejos y puntos aún no migrados).
 *
 * IMPORTANTE: esto ofusca el id, NO reemplaza la verificación de propiedad
 * (que el socio en sesión sea el dueño de la reserva). Ambas cosas se usan juntas.
 *
 * La clave se deriva con SHA-256 (32 bytes exactos para AES-256), a partir de la
 * variable de entorno ID_CIPHER_KEY si existe, o de una constante dedicada. Es una
 * clave DISTINTA a la de leerpdf ('omeganogal2025') para no mezclar dominios.
 */

if (!function_exists('_id_cipher_key')) {
  function _id_cipher_key()
  {
    $raw = getenv('ID_CIPHER_KEY');
    if (!$raw) {
      // Clave dedicada por defecto. En producción se puede sobrescribir con la
      // variable de entorno ID_CIPHER_KEY sin tocar el código.
      $raw = 'nogal_boleteria_id_cipher_2026_v1_clave_secreta';
    }
    // SHA-256 (hex, 64 chars) recortado a 32 bytes => clave estable de 32 bytes.
    return substr(hash('sha256', $raw), 0, 32);
  }
}

if (!function_exists('enc_id')) {
  function enc_id($id)
  {
    if ($id === null || $id === '') {
      return '';
    }
    $iv = openssl_random_pseudo_bytes(16);
    $ct = openssl_encrypt((string) $id, 'AES-256-CBC', _id_cipher_key(), OPENSSL_RAW_DATA, $iv);
    if ($ct === false) {
      return '';
    }
    $b64 = base64_encode($iv . $ct);
    // base64url: seguro para URL y no lo altera el sanitizador de parámetros.
    return rtrim(strtr($b64, '+/', '-_'), '=');
  }
}

if (!function_exists('dec_id')) {
  function dec_id($token)
  {
    if ($token === null || $token === '') {
      return null;
    }
    $token = trim((string) $token);

    // Un id numérico crudo NO se acepta: así se impide la enumeración por URL
    // (p. ej. ?id=89). Solo se admiten tokens cifrados válidos.
    if (ctype_digit($token)) {
      return null;
    }

    // Debe ser un token base64url válido.
    if (!preg_match('/^[A-Za-z0-9_-]+$/', $token)) {
      return null;
    }

    $b64 = strtr($token, '-_', '+/');
    $pad = strlen($b64) % 4;
    if ($pad) {
      $b64 .= str_repeat('=', 4 - $pad);
    }
    $data = base64_decode($b64, true);
    if ($data === false || strlen($data) <= 16) {
      return null;
    }
    $iv = substr($data, 0, 16);
    $ct = substr($data, 16);
    $plain = openssl_decrypt($ct, 'AES-256-CBC', _id_cipher_key(), OPENSSL_RAW_DATA, $iv);
    if ($plain === false) {
      return null;
    }
    $plain = trim($plain);
    // El resultado debe ser un id numérico plausible.
    if (!ctype_digit($plain)) {
      return null;
    }
    return (int) $plain;
  }
}
