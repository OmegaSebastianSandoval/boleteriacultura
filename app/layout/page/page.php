<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title><?= $this->_titlepage ?></title>
  <?php $infopageModel = new Page_Model_DbTable_Informacion();
  $infopage = $infopageModel->getById(1);
  ?>
  <!-- Jquery -->
  <script src="/components/jquery/jquery-3.6.0.min.js"></script>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="/components/bootstrap/css/bootstrap.min.css">
  <!-- Slick CSS -->
  <link rel="stylesheet" href="/components/slick/slick/slick.css">
  <link rel="stylesheet" href="/components/slick/slick/slick-theme.css">
  <!-- Slick -->
  <script type="text/javascript" src="/components/slick/slick/slick.min.js"></script>
  <!-- Global CSS -->
  <link rel="stylesheet" href="/skins/page/css/global.css?v=3">
  <link rel="stylesheet" href="/skins/page/css/responsive.css?v=3">

  <!-- FontAwesome -->
  <link rel="stylesheet" href="/components/Font-Awesome/css/all.css">
  <!-- <link rel="stylesheet" href="/components/boxicons/css/boxicons.min.css"> -->


  <link rel="shortcut icon" href="/favicon.png?v=1.01">

  <script type="text/javascript" id="www-widgetapi-script" src="https://s.ytimg.com/yts/jsbin/www-widgetapi-vflS50iB-/www-widgetapi.js" async=""></script>


  <!-- Bootstrap Js -->
  <script src="/components/bootstrap/js/bootstrap.bundle.min.js"></script>



  <!-- SweetAlert -->
  <script src="/components/sweetalert/sweetalert.js"></script>


  <!-- Main Js -->
  <script src="/skins/page/js/main.js?v=5"></script>

  <!-- Recaptcha -->
  <meta name="description" content="<?= $this->_data['meta_description']; ?>" />
  <meta name=" keywords" content="<?= $this->_data['meta_keywords']; ?>" />
  <?php echo $this->_data['scripts'];  ?>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <header>
    <?= $this->_data['header']; ?>
  </header>
  <main class="contenedor-general">
    <?= $this->_content ?>
  </main>
  <footer>
    <?= $this->_data['footer']; ?>
  </footer>
  <?= $this->_data['adicionales']; ?>

</body>
<style>
  body {
    <?php if ($this->_data['evento_imagenfondo']): ?>background-image: url('/images/<?= $this->_data['evento_imagenfondo']; ?>');
    <?php endif; ?><?php if ($this->_data['evento_colorfondo']): ?>background-color: <?= $this->_data['evento_colorfondo']; ?>;
    <?php endif; ?>
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      background-attachment: fixed;
      width: 100%;
      min-height: 100vh;
  }
</style>

</html>