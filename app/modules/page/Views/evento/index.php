<?php
function getHour($date)
{
  $post = date('H', strtotime($date)) > 12 ? 'pm' : 'am';
  return date('h:i', strtotime($date)) . ' ' . $post;
}

$max = intval($this->evento->evento_invitados_socio);
if (intval($this->invitadosPorSocio) > 0) {
  $max -= intval($this->invitadosPorSocio);
}
?>
<div class="container event-detail-bx">
  <div class="row">
    <div class="col-12">
      <div class="col-12 mb-4">
        <a href="/" class="btn-transparent">Regresar</a>
      </div>
    </div>
    <div class="col-md-5">
      <div class="img-bx">
        <img src="/images/<?php echo $this->evento->evento_imagen_evento ?>" alt="">
      </div>
    </div>
    <div class="col-md-7 event-detail">
      <div class="row">
        <div class="col-12">
          <h3>
            <?php echo $this->evento->evento_titulo ?>
          </h3>
        </div>
        <div class="col-12">
          <div class="event-text">
            <?php echo $this->evento->evento_descripcion ?>
          </div>
        </div>
        <div class="col-md-6">
          <p class="event-text">
            <strong>Fecha del evento:</strong> <?php echo date('d-m-Y', strtotime($this->evento->evento_fecha)) ?>
          </p>
        </div>
        <!-- <div class="col-md-6">
          <p class="event-text">
            <strong>Fecha Inicio:</strong> <?php echo date('d-m-Y', strtotime($this->evento->evento_fecha_inicio)) ?>
          </p>
        </div>
        <div class="col-md-6">
          <p class="event-text">
            <strong>Fecha Final:</strong> <?php echo date('d-m-Y', strtotime($this->evento->evento_fecha_fin)) ?>
          </p>
        </div> -->
        <div class="col-md-6">
          <p class="event-text">
            <strong>Hora:</strong> <?php echo getHour($this->evento->evento_fecha_inicio); ?>
          </p>
        </div>
        <div class="col-md-6">
          <p class="event-text">
            <strong>Lugar:</strong> <?php echo $this->evento->evento_lugar; ?>
          </p>
        </div>
      
       
        <?php if ($this->evento->evento_politica_reserva != '') { ?>
          <div class="col-md-6">
            <p class="event-text">
              <strong>Políticas:</strong> <span class="modal-open-span" data-bs-toggle="modal"
                data-bs-target="#policiesModal">Ver políticas</span>
            </p>
          </div>
        <?php } ?>
        <?php if ($this->conferencista) { ?>
          <div class="col-md-6">
            <p class="event-text">
              <strong>Conferencista:</strong> <span class="modal-open-span" data-bs-toggle="modal"
                data-bs-target="#conferencistaModal">Ver más</s>
            </p>
          </div>
        <?php } ?>
        <!-- <div class="col-md-6">
          <p class="event-text">
            <strong>Cupo disponible:</strong>
            <?php echo (intval($this->evento->evento_cupo_maximo) - intval($this->sumaInvitadosTotalEvento)) > 0 ? (intval($this->evento->evento_cupo_maximo) - intval($this->sumaInvitadosTotalEvento)) : 0; ?>
          </p>
        </div> -->
        <div class="col-md-6">
          <p class="event-text">
            <strong>Máximo de invitados por socio:</strong> <?php echo $this->evento->evento_invitados_socio; ?>
          </p>
        </div>
        <?php if ($max > 0 && (intval($this->evento->evento_cupo_maximo) - intval($this->sumaInvitadosTotalEvento) > 0)) { ?>
          <div class="col-12 mt-4">
            <a href="/page/evento/reservar" class="event-btn">Reservar</a>
          </div>
        <?php } else { ?>
          <div class="col-12 mt-2">
            <div class="alert alert-danger" role="alert">
              Ya no hay cupo disponible para este evento o has superado el máximo de invitados para este evento.
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="policiesModal" tabindex="-1" role="dialog" aria-labelledby="modalTitleId"
  aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="modalTitleId">Políticas de reserva</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <?php echo $this->evento->evento_politica_reserva ?>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="conferencistaModal" tabindex="-1" role="dialog" aria-labelledby="modalTitleId"
  aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="modalTitleId"><?php echo $this->conferencista->conferencista_nombre ?></h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row text-center">
            <div class="col-12 d-flex justify-content-center">
              <img src="/images/<?php echo $this->conferencista->conferencista_foto ?>" alt="">
            </div>
            <div class="col-12">
              <strong><?php echo $this->conferencista->conferencista_nombre ?></strong>
            </div>
            <div class="col-12">
              <?php echo $this->conferencista->conferencista_descripcion ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>