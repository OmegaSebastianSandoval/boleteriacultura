<?php

/**
 *
 */
class Core_Model_Upload_Image
{

    private $_exts = array("image/jpg", "image/jpeg", "image/png", "image/gif", "image/vnd.microsoft.icon", "image/x-icon"); // Tipos de archivos soportados
    private $_width = 19200; // Ancho máximo por defecto
    private $_height = 1200; // Alto máximo por defecto
    private $_size = 2097152; // Peso máximo. MAX_FILE_SIZE sobrescribe este valor

    public function changeConfig($exts, $size, $width, $height)
    {
        if ($exts != null) {
            $this->_exts = $exts;
        }
        if ($width != null) {
            $this->_width = $width;
        }
        if ($height != null) {
            $this->_height = $height;
        }
        if ($size != null) {
            $this->_size = $size;
        }
    }
    public function uploadmultiple($image)
    {
        $permitidos = array("image/jpg", "image/jpeg", "image/gif", "image/png", "image/vnd.microsoft.icon", "image/x-icon");
        $limite_kb = 2000;
        $images = array();
        foreach ($_FILES[$image]["tmp_name"] as $key => $tmp_name) {
            if (in_array($_FILES[$image]['type'][$key], $permitidos) && $_FILES[$image]['size'][$key] <= $limite_kb * 1024) {
                $filename = '' . pathinfo($_FILES[$image]['name'][$key], PATHINFO_FILENAME);
                $filename = $this->clearName($filename);
                $extension = pathinfo($_FILES[$image]['name'][$key], PATHINFO_EXTENSION);
                $name = $filename . '.' . $extension;
                $ruta = IMAGE_PATH . $name;
                if (file_exists($ruta)) {
                    $increment = 0;
                    while (file_exists($ruta)) {
                        $increment++;
                        $name = $filename . $increment . '.' . $extension;
                        $ruta = IMAGE_PATH . $name;
                    }
                }
                if (move_uploaded_file($_FILES[$image]['tmp_name'][$key], $ruta)) {
                    echo $images[$key] = "images/" . $name;
                }
            }
        }
        if (count($images) > 0) {
            return $images;
        }
        return false;
    }
    public function upload($image)
    {
        if ($_FILES[$image]["error"] > 0) {
            print_r($_FILES[$image]["error"]);
        } else {
            if (in_array($_FILES[$image]['type'], $this->_exts) && $_FILES[$image]['size'] <= $this->_size) {
                $filename = '' . pathinfo($_FILES[$image]['name'], PATHINFO_FILENAME);
                $filename = $this->clearName($filename);
                $extension = pathinfo($_FILES[$image]['name'], PATHINFO_EXTENSION);
                $name = $filename . '.' . $extension;
                $list =  list($ancho_orig, $alto_orig) = getimagesize($_FILES[$image]['tmp_name']);
                $ruta = IMAGE_PATH . $name;
                if (file_exists($ruta)) {
                    $increment = 0;
                    while (file_exists($ruta)) {
                        $increment++;
                        $name = $filename . $increment . '.' . $extension;
                        $ruta = IMAGE_PATH . $name;
                    }
                }
                $origen = $_FILES[$image]['tmp_name'];
                $ancho_max = $this->_width;
                $alto_max = $this->_height;
                if ($ancho_orig > $ancho_max or $alto_orig > $alto_max) {
                    $ratio_orig = $ancho_orig / $alto_orig;
                    if ($ancho_max / $alto_max > $ratio_orig) {
                        $ancho_max = $alto_max * $ratio_orig;
                    } else {
                        $alto_max = $ancho_max / $ratio_orig;
                    }
                    // Redimensionar
                    $canvas = imagecreatetruecolor($ancho_max, $alto_max);
                    switch ($_FILES[$image]['type']) {
                        case "image/jpg":
                        case "image/jpeg":
                            $image = imagecreatefromjpeg($origen);
                            imagecopyresampled($canvas, $image, 0, 0, 0, 0, $ancho_max, $alto_max, $ancho_orig, $alto_orig);
                            imagejpeg($canvas, $ruta, 100);
                            return $name;
                            break;
                        case "image/gif":
                            $image = imagecreatefromgif($origen);
                            imagecopyresampled($canvas, $image, 0, 0, 0, 0, $ancho_max, $alto_max, $ancho_orig, $alto_orig);
                            imagegif($canvas, $ruta);
                            return $name;
                            break;
                        case "image/png":
                            $image = imagecreatefrompng($origen);
                            imagecopyresampled($canvas, $image, 0, 0, 0, 0, $ancho_max, $alto_max, $ancho_orig, $alto_orig);
                            imagepng($canvas, $ruta, 0);
                            return $name;
                            break;
                    }
                } else {
                    move_uploaded_file($origen, $ruta);
                    return $name;
                }
            }
        }
        return false;
    }


    public function delete($image)
    {
        if (file_exists(IMAGE_PATH . $image)) {
            unlink(IMAGE_PATH . $image);
            return true;
        }
        return false;
    }

    private function clearName($str)
    {
        //Quitar tildes y ñ
        $tildes = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ');
        $vocales = array('a', 'e', 'i', 'o', 'u', 'n', 'A', 'E', 'I', 'O', 'U', 'N');
        $str = str_replace($tildes, $vocales, $str);
        //Quitar símbolos
        $simbolos = array("=", "¿", "?", "¡", "!", "'", "%", "$", "€", "(", ")", "[", "]", "{", "}", "*", "+", "·", ".", "&lt; ", "&gt;");
        $i = 0;
        while ($simbolos[$i]) {
            $str = str_replace($simbolos[$i], "", $str);
            $i++;
        }
        //Quitar espacios
        $str = str_replace(" ", "_", $str);
        //Pasar a minúsculas
        $str = strtolower($str);
        return $str;
    }
}
