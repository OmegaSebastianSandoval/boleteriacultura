<div class="botonera-fixed">
	<div class="header-blanco">
		<div class="container">
			<div class="row">
				<div class="col-sm-8">
					<div class="redes">
						<?php if($this->infopage->info_pagina_telefono) {?>
							<?php $telefono = intval(preg_replace('/[^0-9]+/', '', $this->infopage->info_pagina_telefono), 10);  ?>
							<a href="tel:<?php echo $telefono; ?>" target="_blank" class="red">
								<i class="fas fa-phone"></i>
								<span><?php echo $this->infopage->info_pagina_telefono ?></span>
							</a>
							<a href="https://api.whatsapp.com/send?phone=<?php echo $this->infopage->info_pagina_whatsapp; ?>" target="_blank" class="red" >
								<i class="fab fa-whatsapp"></i>
								<span><?php echo $this->infopage->info_pagina_whatsapp ?></span>
							</a>
						<?php } ?>
						<a href="mailto:<?php echo $this->infopage->info_pagina_correos_contacto; ?>" target="_blank" class="red" >
							<i class="fas fa-envelope"></i>
							<span><?php echo $this->infopage->info_pagina_correos_contacto; ?></span>
						</a>
					</div>
				</div>
				<div class="col-sm-4">
					<div>
					<div class="header-redes">
						<?php if($this->infopage->info_pagina_facebook) {?>
							<a href="<?php echo $this->infopage->info_pagina_facebook ?>" target="_blank" class="red">
								<i class="fab fa-facebook-f"></i>
							</a>
						<?php } ?>
						<?php if($this->infopage->info_pagina_twitter) {?>
							<a href="<?php echo $this->infopage->info_pagina_twitter ?>" target="_blank" class="red">
								<i class="fab fa-twitter"></i>
							</a>
						<?php } ?>
						<?php if($this->infopage->info_pagina_instagram) {?>
							<a href="<?php echo $this->infopage->info_pagina_instagram ?>" target="_blank" class="red">
								<i class="fab fa-instagram"></i>
							</a>
						<?php } ?>
						<?php if($this->infopage->info_pagina_pinterest) {?>
							<a href="<?php echo $this->infopage->info_pagina_pinterest ?>" target="_blank" class="red">
								<i class="fab fa-pinterest-p"></i>
							</a>
						<?php } ?>
						<?php if($this->infopage->info_pagina_youtube) {?>
							<a href="<?php echo $this->infopage->info_pagina_youtube ?>" target="_blank" class="red">
								<i class="fab fa-youtube"></i>
							</a>
						<?php } ?>
						<?php if($this->infopage->info_pagina_linkedin) {?>
							<a href="<?php echo $this->infopage->info_pagina_linkedin ?>" target="_blank" class="red">
								<i class="fab fa-linkedin-in"></i>
							</a>
						<?php } ?>
						<?php if($this->infopage->info_pagina_google) {?>
							<a href="<?php echo $this->infopage->info_pagina_google ?>" target="_blank" class="red">
								<i class="fab fa-google-plus-g"></i>
							</a>
						<?php } ?>
						<?php if($this->infopage->info_pagina_flickr) {?>
							<a href="<?php echo $this->infopage->info_pagina_flickr ?>" target="_blank" class="red">
								<i class="fab fa-flickr"></i>
							</a>
						<?php } ?>
					</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="header-content">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<div><a  class="menu-responsive"><i class="fas fa-bars"></i></a></div>
					<nav>
					<div><a class="menu-responsive-cierra"><i class="fas fa-times-circle"></i></a></div>
						<ul>
							<li class="linea"><a href="/page/index"><span>Inicio</span></a></li>
							
							<li><a href="/page/programacion"><span>Programación</span></a></li>
							
							<li><a href="/page/sedes"><span>Sedes</span></a></li>
							
							<li><a href="/page/restaurante"><span>Restaurante</span></a></li>
							
							<li><a href="/page/bar"><span>Bar</span></a></li>
							
							<li><a href="/page/clubsocial"><span>Club Social</span></a></li>
							
							<li><a href="/page/videos"><span>Videos</span></a></li>
							
							<li><a href="/page/fotos"><span>Fotos</span></a></li>
							
							<li><a href="/page/contactenos"><span>Contáctenos</span></a></li>
						</ul>
					</nav>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="banner-header" style="background-image:url(/skins/page/images/banner.jpg)">
	<div class="container">
		<div class="row no-gutters">
			<div class="col-sm-4">
				<div class="logo"><img src="/skins/page/images/logogaleria.png" alt=""></div>
			</div>
			<div class="col-sm-4">
				<div>
					<p class="rumba">La Rumba en Bogotá</p>
					<p class="salsa">Salsa, Son & Caribe</p>
				</div>
			</div>
			<div align="right"class="col-sm-4">
				<div class="reserva">
					<div ><a class="btn btn-sm btn-rosado" href="/page/programacion/reserva">Reserva en Línea</a></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="fondo-amarillo">

</div>
<div class="header-responsive">
	<div class="header-content">
		<div class="container">
			<div class="row">
				<div class="col-sm-6">
					<a href="/page/index"><div class="logo-responsive"><img src="/skins/page/images/logogaleria.png" alt=""></div></a>
				</div>
				<div class="col-sm-6">
					<div class="row">
						<div class="col-6">
							
						</div>
						<div class="col-6">
							<div class="lista-responsive">
								<a class="menu-responsive"><i class="fas fa-bars"></i></a>
							</div>		
						</div>	
					</div>
					<nav>
					<div><a class="menu-responsive-cierra"><i class="fas fa-times-circle"></i></a></div>
						<ul>
							<li class="linea"><a href="/page/index"><span>Inicio</span></a></li>
							
							<li><a href="/page/programacion"><span>Programación</span></a></li>
							
							<li><a href="/page/sedes"><span>Sedes</span></a></li>
							
							<li><a href="/page/restaurante"><span>Restaurante</span></a></li>
							
							<li><a href="/page/bar"><span>Bar</span></a></li>
							
							<li><a href="/page/clubsocial"><span>Club Social</span></a></li>
							
							<li><a href="/page/videos"><span>Videos</span></a></li>
							
							<li><a href="/page/fotos"><span>Fotos</span></a></li>
							
							<li><a href="/page/contactenos"><span>Contáctenos</span></a></li>
						</ul>
						<div class="redes-responsive">
							<?php if($this->infopage->info_pagina_telefono) {?>
								<?php $telefono = intval(preg_replace('/[^0-9]+/', '', $this->infopage->info_pagina_telefono), 10);  ?>
								<a href="tel:<?php echo $telefono; ?>" target="_blank" class="red">
									<i class="fas fa-phone"></i>
									<span><?php echo $this->infopage->info_pagina_telefono ?></span>
								</a>
							<?php } ?>
							<a href="https://api.whatsapp.com/send?phone=<?php echo $whatsapp; ?>" target="_blank" class="red" >
								<i class="fab fa-whatsapp"></i>
								<span><?php echo $this->infopage->info_pagina_whatsapp ?></span>
							</a>
							<a href="mailto:<?php echo $this->infopage->info_pagina_correos_contacto; ?>" target="_blank" class="red" >
								<i class="fas fa-envelope"></i>
								<span><?php echo $this->infopage->info_pagina_correos_contacto; ?></span>
							</a>
						</div>
						<div class="header-redes-responsive">
							<?php if($this->infopage->info_pagina_facebook) {?>
								<a href="<?php echo $this->infopage->info_pagina_facebook ?>" target="_blank" class="red">
									<i class="fab fa-facebook-f"></i>
								</a>
							<?php } ?>
							<?php if($this->infopage->info_pagina_twitter) {?>
								<a href="<?php echo $this->infopage->info_pagina_twitter ?>" target="_blank" class="red">
									<i class="fab fa-twitter"></i>
								</a>
							<?php } ?>
							<?php if($this->infopage->info_pagina_instagram) {?>
								<a href="<?php echo $this->infopage->info_pagina_instagram ?>" target="_blank" class="red">
									<i class="fab fa-instagram"></i>
								</a>
							<?php } ?>
							<?php if($this->infopage->info_pagina_pinterest) {?>
								<a href="<?php echo $this->infopage->info_pagina_pinterest ?>" target="_blank" class="red">
									<i class="fab fa-pinterest-p"></i>
								</a>
							<?php } ?>
							<?php if($this->infopage->info_pagina_youtube) {?>
								<a href="<?php echo $this->infopage->info_pagina_youtube ?>" target="_blank" class="red">
									<i class="fab fa-youtube"></i>
								</a>
							<?php } ?>
							<?php if($this->infopage->info_pagina_linkedin) {?>
								<a href="<?php echo $this->infopage->info_pagina_linkedin ?>" target="_blank" class="red">
									<i class="fab fa-linkedin-in"></i>
								</a>
							<?php } ?>
							<?php if($this->infopage->info_pagina_google) {?>
								<a href="<?php echo $this->infopage->info_pagina_google ?>" target="_blank" class="red">
									<i class="fab fa-google-plus-g"></i>
								</a>
							<?php } ?>
							<?php if($this->infopage->info_pagina_flickr) {?>
								<a href="<?php echo $this->infopage->info_pagina_flickr ?>" target="_blank" class="red">
									<i class="fab fa-flickr"></i>
								</a>
							<?php } ?>
						</div>
					</nav>
				</div>
			</div>
		</div>
	</div>
	<div class="btn-rosado-responsive">
		<a class="btn btn-sm btn-rosado" href="/page/programacion/reserva?>">Reserva en Línea</a>
	</div>
</div>
<div class="fondo-amarillo-responsive">
	
</div>