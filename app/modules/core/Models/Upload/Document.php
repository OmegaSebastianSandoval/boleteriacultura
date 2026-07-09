<?php

/**
*
*/
class Core_Model_Upload_Document
{
    // Extensiones permitidas (deben coincidir con .file-document en main.js)
    private $_extensionesPermitidas = ["pdf", "xls", "xlsx", "doc", "docx"];

    // Límite de tamaño: se alinea con maxFileSize:2048 (KB) de .file-document en main.js
    private $_limiteBytes = 2048 * 1024;

    // MIME real esperado por extensión, detectado con fileinfo sobre el contenido del
    // archivo (no el header Content-Type que manda el navegador, que se falsifica con
    // solo editar la petición). xlsx/docx son en el fondo un ZIP (Office Open XML):
    // según la versión de libmagic puede reportarse como el mime específico o como
    // application/zip genérico, así que se aceptan ambos. doc/xls legacy (OLE) a veces
    // se reportan como application/octet-stream cuando libmagic no reconoce la variante.
    private $_mimesPorExtension = [
        "pdf"  => ["application/pdf"],
        "xls"  => ["application/vnd.ms-excel", "application/octet-stream", "application/x-ole-storage", "application/CDFV2"],
        "xlsx" => ["application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "application/zip"],
        "doc"  => ["application/msword", "application/octet-stream", "application/x-ole-storage", "application/CDFV2"],
        "docx" => ["application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/zip"],
    ];

    public function upload($document)
    {
        if (empty($_FILES[$document]) || $_FILES[$document]["error"] > 0) {
            return false;
        }

        $archivo = $_FILES[$document];

        if ($archivo['size'] <= 0 || $archivo['size'] > $this->_limiteBytes) {
            return false;
        }

        // La extensión es lo único que decide si Apache llega a ejecutar el archivo
        // como PHP (AddHandler ...php .php8 .phtml en el .htaccess), así que es la
        // validación crítica: solo se acepta si está en la whitelist, sin importar
        // qué extensión venga en el nombre original.
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->_extensionesPermitidas, true)) {
            return false;
        }

        // Segunda capa: el contenido real del archivo debe corresponder al tipo
        // declarado por su extensión (defensa adicional, no la única).
        $mimeReal = $this->detectarMime($archivo['tmp_name']);
        if ($mimeReal !== null && !in_array($mimeReal, $this->_mimesPorExtension[$extension], true)) {
            return false;
        }

        $filename = $this->clearName(pathinfo($archivo['name'], PATHINFO_FILENAME));
        if ($filename === '') {
            $filename = 'documento';
        }
        $name = $filename . '.' . $extension;
        $ruta = FILE_PATH . $name;
        if (file_exists($ruta)) {
            $increment = 0;
            while (file_exists($ruta)) {
                $increment++;
                $name = $filename . $increment . '.' . $extension;
                $ruta = FILE_PATH . $name;
            }
        }

        if (move_uploaded_file($archivo['tmp_name'], $ruta)) {
            return $name;
        }

        return false;
    }

    public function delete($document)
    {
        if (file_exists(FILE_PATH.$document)) {
            unlink(FILE_PATH.$document);
            return true;
        }
        return false;
    }

    public function uploadpublic($image,$name)
    {
        // $name siempre llega fijo desde el controlador (p.ej. "robots.txt",
        // "sitemap.xml"), nunca desde el usuario, así que no hace falta whitelist
        // de extensión aquí: el nombre de destino ya es seguro por construcción.
        if (empty($_FILES[$image]) || $_FILES[$image]["error"] > 0) {
            return false;
        }

        $archivo = $_FILES[$image];
        if ($archivo['size'] <= 0 || $archivo['size'] > $this->_limiteBytes) {
            return false;
        }

        $ruta = PUBLIC_PATH.$name;
        // move_uploaded_file ya sobrescribe el destino si existe: el unlink() previo
        // que había aquí antes borraba el archivo existente aunque la subida
        // fallara después, dejando el sitio sin robots.txt/sitemap.xml.
        if (move_uploaded_file($archivo['tmp_name'], $ruta)) {
            return $name;
        }
        return false;
    }

    private function detectarMime($rutaArchivo)
    {
        if (!function_exists('finfo_open') || !is_readable($rutaArchivo)) {
            return null; // sin fileinfo disponible, se confía solo en la whitelist de extensión
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (!$finfo) {
            return null;
        }
        $mime = finfo_file($finfo, $rutaArchivo);
        // Sin finfo_close(): desde PHP 8.1 finfo es un objeto que se libera solo al
        // salir de scope; llamar a la función explícitamente está deprecado.
        return $mime ?: null;
    }

    private function clearName($str)
    {
        //Quitar tildes y ñ
        $tildes = array('á','é','í','ó','ú','ñ','Á','É','Í','Ó','Ú','Ñ');
        $vocales = array('a','e','i','o','u','n','A','E','I','O','U','N');
        $str = str_replace($tildes,$vocales,$str);
        //Quitar símbolos
        $simbolos = array("=","¿","?","¡","!","'","%","$","€","(",")","[","]","{","}","*","+","·",".","&lt; ","&gt;");
        $i = 0;
        while($simbolos[$i]){
        $str = str_replace($simbolos[$i], "", $str);
        $i++;
        }
        //Quitar espacios
        $str = str_replace(" ","_",$str);
        //Pasar a minúsculas
        $str = strtolower($str);
        return $str;
    }
}
