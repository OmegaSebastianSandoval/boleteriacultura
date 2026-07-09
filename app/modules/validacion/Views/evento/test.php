<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Lector PDF417</title>
</head>
<body>
  <h1>Escanear cédula PDF417</h1>
  <video id="video" width="400" height="300" style="border:1px solid #ccc"></video>
  <p id="output"></p>

  <!-- ZXing library -->
  <script src="https://unpkg.com/@zxing/library@latest"></script>
  <script>
    const codeReader = new ZXing.BrowserMultiFormatReader();
    const hints = new Map();
    hints.set(ZXing.DecodeHintType.POSSIBLE_FORMATS, [ZXing.BarcodeFormat.PDF_417]);

    codeReader.decodeFromVideoDevice(null, 'video', (result, err) => {
      if (result) {
        document.getElementById('output').innerText = "Detectado: " + result.text;
        console.log(result.text);
      }
      if (err && !(err instanceof ZXing.NotFoundException)) {
        console.error(err);
      }
    });
  </script>
</body>
</html>
