<?php

/**
*
*/
class Core_Model_Upload_Image
{

    // Extensiones permitidas (deben coincidir con .file-image en main.js)
    private $_extensionesPermitidas = ["jpg", "jpeg", "png", "gif", "ico"];
    private $_width = 19200; // Ancho máximo por defecto
    private $_height = 19200; // Alto máximo por defecto
    // Peso máximo en bytes (2 MB, igual que maxFileSize:2048 en main.js). El valor
    // anterior (2000097152) eran ~1.86 GB por archivo: un desfase con el límite real
    // del frontend que permitía subir imágenes enormes con solo saltarse el JS.
    private $_size = 2097152;

    // MIME real esperado por extensión, detectado con fileinfo sobre el contenido del
    // archivo (no el header Content-Type que manda el navegador, que se falsifica con
    // solo editar la petición).
    private $_mimesPorExtension = [
        "jpg"  => ["image/jpeg"],
        "jpeg" => ["image/jpeg"],
        "png"  => ["image/png"],
        "gif"  => ["image/gif"],
        "ico"  => ["image/vnd.microsoft.icon", "image/x-icon", "image/icon", "image/ico"],
    ];

    public function changeConfig($extensiones,$size,$width,$height){
        // Nota: $extensiones ahora es una lista de extensiones (["jpg","png",...]),
        // no de mime-types como antes. Ningún controlador llama hoy a este método.
        if($extensiones!=null){
            $this->_extensionesPermitidas = $extensiones;
        }
        if($width!=null){
            $this->_width = $width;
        }
        if($height!=null){
            $this->_height = $height;
        }
        if($size!=null){
            $this->_size = $size;
        }
    }

    public function upload($image)
    {
        if (empty($_FILES[$image]) || $_FILES[$image]["error"] > 0) {
            return false;
        }

        $archivo = $_FILES[$image];

        if ($archivo['size'] <= 0 || $archivo['size'] > $this->_size) {
            return false;
        }

        // La extensión es lo único que decide si Apache llega a ejecutar el archivo
        // como PHP (AddHandler ...php .php8 .phtml en el .htaccess), así que es la
        // validación crítica: solo se acepta si está en la whitelist.
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->_extensionesPermitidas, true)) {
            return false;
        }

        // Segunda capa: el contenido real del archivo debe corresponder al tipo
        // declarado por su extensión (defensa adicional, no la única).
        $mimeReal = $this->detectarMime($archivo['tmp_name']);
        $mimesEsperados = $this->_mimesPorExtension[$extension] ?? [];
        if ($mimeReal !== null && $mimesEsperados && !in_array($mimeReal, $mimesEsperados, true)) {
            return false;
        }

        $filename = $this->clearName(pathinfo($archivo['name'], PATHINFO_FILENAME));
        if ($filename === '') {
            $filename = 'imagen';
        }
        $name = $filename.'.'.$extension;
        $ruta = IMAGE_PATH .$name ;
        if (file_exists($ruta)) {
            $increment = 0;
            while (file_exists($ruta)) {
                $increment++;
                $name =$filename.$increment.'.'.$extension;
                $ruta = IMAGE_PATH .$name;
            }
        }

        $origen = $archivo['tmp_name'];

        // El .ico no lo soporta GD (getimagesize/imagecreatefrom*): se copia tal cual,
        // sin redimensionar.
        if ($extension === 'ico') {
            return move_uploaded_file($origen, $ruta) ? $name : false;
        }

        $medidas = @getimagesize($origen);
        if (!$medidas) {
            return false; // el contenido no es una imagen válida, aunque extensión/MIME lo aparenten
        }
        list($ancho_orig, $alto_orig) = $medidas;

        $ancho_max = $this->_width;
        $alto_max = $this->_height;
        if ($ancho_orig > $ancho_max or $alto_orig > $alto_max) {
            $ratio_orig = $ancho_orig/$alto_orig;
            if ($ancho_max/$alto_max > $ratio_orig) {
               $ancho_max = $alto_max*$ratio_orig;
            } else {
               $alto_max = $ancho_max/$ratio_orig;
            }
            // Redimensionar
            $canvas = imagecreatetruecolor((int) $ancho_max, (int) $alto_max);
            switch ($extension) {
                case "jpg":
                case "jpeg":
                    $img = imagecreatefromjpeg($origen);
                    imagecopyresampled($canvas, $img, 0, 0, 0, 0, $ancho_max, $alto_max, $ancho_orig, $alto_orig);
                    imagejpeg($canvas, $ruta, 100);
                    return $name;
                case "gif":
                    $img = imagecreatefromgif($origen);
                    imagecopyresampled($canvas, $img, 0, 0, 0, 0, $ancho_max, $alto_max, $ancho_orig, $alto_orig);
                    imagegif($canvas, $ruta);
                    return $name;
                case "png":
                    $img = imagecreatefrompng($origen);
                    imagecopyresampled($canvas, $img, 0, 0, 0, 0, $ancho_max, $alto_max, $ancho_orig, $alto_orig);
                    imagepng($canvas, $ruta, 0);
                    return $name;
            }
            return false;
        } else {
            return move_uploaded_file($origen, $ruta) ? $name : false;
        }
    }


    public function delete($image)
    {
        if (file_exists(IMAGE_PATH.$image)) {
            unlink(IMAGE_PATH.$image);
            return true;
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
        $str = str_replace($simbolos[$i],"", $str);
        $i++;
        }
        //Quitar espacios
        $str = str_replace(" ","_",$str);
        //Pasar a minúsculas
        $str = strtolower($str);
        return $str;
    }
    public function uploadmultiple($image)
    {
            $limite_kb = 2000;
            $images = array();
            foreach($_FILES[$image]["tmp_name"] as $key=>$tmp_name)
            {
                if ($_FILES[$image]['error'][$key] > 0 || $_FILES[$image]['size'][$key] <= 0 || $_FILES[$image]['size'][$key] > $limite_kb * 1024) {
                    continue;
                }

                $extension = strtolower(pathinfo($_FILES[$image]['name'][$key], PATHINFO_EXTENSION));
                if (!in_array($extension, $this->_extensionesPermitidas, true) || $extension === 'ico') {
                    continue;
                }

                $mimeReal = $this->detectarMime($tmp_name);
                $mimesEsperados = $this->_mimesPorExtension[$extension] ?? [];
                if ($mimeReal !== null && $mimesEsperados && !in_array($mimeReal, $mimesEsperados, true)) {
                    continue;
                }

                $filename = $this->clearName(pathinfo($_FILES[$image]['name'][$key], PATHINFO_FILENAME));
                if ($filename === '') {
                    $filename = 'imagen';
                }
                $name = $filename.'.'.$extension;
                $ruta = IMAGE_PATH .$name ;
                if (file_exists($ruta)) {
                    $increment = 0;
                    while (file_exists($ruta)) {
                        $increment++;
                        $name =$filename.$increment.'.'.$extension;
                        $ruta = IMAGE_PATH .$name;
                    }
                }
                if (move_uploaded_file($tmp_name, $ruta)) {
                     $images[$key] = "images/".$name;
                }
            }
            if(count($images)>0){
                return $images;
            }
        return false;
    }

}
