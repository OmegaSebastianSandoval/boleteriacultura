<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>
        <?= $this->_titlepage ?>
    </title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBWYVxdF4VwIPfmB65X2kMt342GbUXApwQ&sensor=true">
    </script>
    <?php $infopageModel = new Page_Model_DbTable_Informacion();
    $infopage = $infopageModel->getById(1);
    ?>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/components/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css"
        rel="stylesheet">
    <link rel="stylesheet" href="/components/bootstrap-datepicker/css/bootstrap-datepicker3.standalone.min.css">
    <!-- Fileinput -->
    <link rel="stylesheet" href="/components/bootstrap-fileinput/css/fileinput.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="/components/Font-Awesome/css/all.css">
    <!-- Colorpicker -->
    <link rel="stylesheet" href="/components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css">
    <!-- Date range picker  -->
    <link rel="stylesheet" type="text/css" href="/components/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="/components/boxicons/css/boxicons.min.css">
    <!-- Global CSS -->
    <link rel="stylesheet" href="/skins/administracion/css/global.css?v=1.10">
    <link rel="shortcut icon" href="/images/<?= $infopage->info_pagina_favicon; ?>">

    <script type="text/javascript">
        var map;
        var longitude = 0;
        var latitude = 0;
        var icon = '/skins/administracion/images/ubicacion.png';
        var point = false;
        var zoom = 10;

        function setValuesMap(longitud, latitud, punto, zoomm, icono) {
            longitude = longitud;
            latitude = latitud;
            if (punto) {
                point = punto;
            }
            if (zoomm) {
                zoom = zoomm;
            }
            if (icono) {
                icon = icono
            }
        }

        function initializeMap() {
            var mapOptions = {
                zoom: parseInt(zoom),
                center: new google.maps.LatLng(longitude, longitude),
            };
            // Place a draggable marker on the map
            map = new google.maps.Map(document.getElementById('map'), mapOptions);
            if (point == true) {
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(longitude, latitude),
                    map: map,
                    icon: icon
                });
            }
            map.setCenter(new google.maps.LatLng(longitude, latitude));
        }
    </script>
</head>

<body>
    <header>
        <?= $this->_data['panel_header']; ?>
    </header>
    <div class="container-fluid panel p-0">
        <div class="d-flex justify-content-start">
        <nav id="panel-botones">
            <?= $this->_data['panel_botones']; ?>
        </nav>
        <article id="contenido_panel" class="w-100">
            <section id="contenido_general">
            <?= $this->_content ?>
            </section>
        </article>
        </div>
    </div>
    <footer class="panel-derechos col-md-12">&copy;Todos los Derechos Reservados <?php echo date('Y'); ?> -
        Dise&ntilde;ado por Omega Soluciones Web
    </footer>
    <!-- Jquery -->
    <script src="/components/jquery/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap Js -->
    <script src="/components/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert -->
    <script src="/components/sweetalert/sweetalert.js"></script>
    <script src="/components/bootstrap-datepicker/js/bootstrap-datepicker.min.js">
    </script>
    <script src="/components/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js">
    </script>
    <script src="/components/bootstrap-validator/dist/validator.min.js">
    </script>
    <!-- File Input -->
    <script src="/components/bootstrap-fileinput/js/fileinput.min.js"></script>
    <script src="/components/bootstrap-fileinput/js/locales/es.js"></script>
    <!-- Tiny -->
    <script src="/components/tinymce/tinymce.min.js"></script>
    <script src="/components/bootstrap-switch/dist/js/bootstrap-switch.min.js"></script>
    <script src="/components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>
    <!-- Date Range Picker -->
    <script type="text/javascript" src="/components/momentjs/moment.min.js"></script>
    <script type="text/javascript" src="/components/daterangepicker/daterangepicker.min.js"></script>

    <!-- main Js -->
    <script src="/skins/administracion/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var btn = document.getElementById('toggle-menu');
            var panelBotones = document.getElementById('panel-botones');
            var contenidoPanel = document.getElementById('contenido_panel');
            btn.addEventListener('click', function() {
                if (!panelBotones.classList.contains('menu-cerrado')) {
                    panelBotones.classList.add('menu-cerrado');
                    contenidoPanel.classList.add('contenido-expandido');
                } else {
                    panelBotones.classList.remove('menu-cerrado');
                    contenidoPanel.classList.remove('contenido-expandido');
                }
            });
        });
    </script>

</body>

</html>