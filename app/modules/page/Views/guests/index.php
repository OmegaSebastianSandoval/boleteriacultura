<style>
  /* ── VIEWPORT LAYOUT ── */
  .contenedor-general {
    height: calc(100vh - 60px - 30px);
    display: flex; flex-direction: column;
    overflow: hidden; padding-bottom: 0 !important;
  }

  .card-principal {
    background-color: rgba(21, 25, 29, 1);
    backdrop-filter: blur(22px); -webkit-backdrop-filter: blur(22px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 6px; padding: 16px 16px 0;
    flex: 1; min-height: 0;
    display: flex; flex-direction: column;
    overflow: hidden; margin-bottom: 0 !important;
  }

  .rl-page {
    max-width: 100%; flex: 1; min-height: 0;
    display: flex; flex-direction: column;
    overflow: hidden; padding-top: 0.5rem; padding-bottom: 0;
  }

  /* ── TOPBAR (shared) ── */
  .rv-topbar {
    display: flex; align-items: center;
    justify-content: space-between; gap: 0.75rem; flex-wrap: wrap;
    flex-shrink: 0; margin-bottom: 0.75rem;
  }

  .sb-back-btn {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 6px 16px; border-radius: 20px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #fff !important; font-size: 0.82rem; font-weight: 600;
    text-decoration: none;
    backdrop-filter: blur(12px); transition: background 0.18s;
  }
  .sb-back-btn:hover { background: rgba(255, 255, 255, 0.13); }

  .rv-topbar .step-badge {
    background: rgba(255, 255, 255, 0.07);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 20px;
    padding: 6px 16px 6px 12px;
    font-size: 0.82rem;
    font-weight: 600;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    transition: border-color 0.3s ease;
  }

  .rv-topbar .step-badge i {
    color: #ffc107;
    font-size: 0.78rem;
  }

  .rv-topbar .step-text {
    font-weight: 700;
    letter-spacing: 0.3px;
  }

  .rv-topbar .step-divider {
    opacity: 0.3;
    margin: 0 6px;
    font-size: 0.9rem;
  }

  .rv-topbar .step-name {
    color: white;
    font-weight: 500;
    font-size: 0.9rem;
  }

  /* ── LISTADO VIEW ── */
  .gl-listado-wrap {
    flex: 1; min-height: 0;
    overflow-y: auto; overflow-x: hidden;
    padding-bottom: 12px; padding-right: 8px;
  }
  .gl-listado-wrap::-webkit-scrollbar { width: 5px; }
  .gl-listado-wrap::-webkit-scrollbar-track { background: transparent; }
  .gl-listado-wrap::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.12); border-radius: 4px; }

  .gl-listado-header {
    display: flex; justify-content: space-between; align-items: center;
    gap: 1rem; flex-wrap: wrap;
    margin-bottom: 1.25rem; padding-bottom: 0.75rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    position: sticky; top: 0; z-index: 10;
    background: rgba(21, 25, 29, 1);
    padding-top: 0.25rem;
  }
  .gl-listado-title { font-size: 1.3rem; font-weight: 700; color: #fff; margin: 0; }
  .gl-listado-subtitle { font-size: 0.78rem; color: white; margin-top: 2px; }

  .gl-action-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: #ffc107;
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: black !important; border-radius: 20px;
    font-size: 0.78rem; font-weight: 600; padding: 8px 18px;
    text-decoration: none; transition: background 0.18s;
    cursor: pointer;
  }
  .gl-action-btn:hover { background: rgba(255, 255, 255, 0.13); border-color: #ffc107; color: white !important; }

  /* Reserva cards */
  .reserva-card {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 6px;
    transition: border-color 0.2s;
  }
  .reserva-card:hover { border-color: rgba(255, 255, 255, 0.2); }
  .reserva-card .card-header {
    background: rgba(255, 255, 255, 0.04);
    border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    padding: 12px 14px;
  }
  .reserva-card .rnum { color: #fff; font-size: 0.95rem; font-weight: 700; }
  .reserva-card .rnum span {
    color: rgba(255, 255, 255, 0.3); font-size: 0.62rem;
    text-transform: uppercase; letter-spacing: 1.5px; display: block; font-weight: 600;
  }
  .reserva-card .card-body { padding: 14px; }

  .rl-badge {
    font-size: 0.73rem; font-weight: 700; letter-spacing: 0.06em;
    text-transform: uppercase; padding: 3px 10px; border-radius: 20px;
  }
  .rl-badge-success { background: rgba(40,167,69,0.15); color: #6fcf97; border: 1px solid rgba(40,167,69,0.3); }
  .rl-badge-warning { background: rgba(255,193,7,0.12); color: #ffc107; border: 1px solid rgba(255,193,7,0.3); }
  .rl-badge-secondary { background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.4); border: 1px solid rgba(255,255,255,0.1); }

  .rl-meta {
    font-size: 0.73rem; color: rgba(255,255,255,0.38);
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
    margin-bottom: 10px; font-weight: 600;
  }

  .rl-meta-person {
    font-size: 0.8rem;
    background: #ffc107;
    border-color: #ffc107;
    color: #111;
    padding: 0px 10px;
    border-radius: 20px;
  }

  .rl-status-line {
    padding: 6px 10px; margin-bottom: 5px;
    font-size: 0.8rem; display: flex; align-items: center; gap: 7px;
    font-weight: 600; border-radius: 4px;
  }
  .rl-status-line.warn { background: rgba(255,193,7,0.08); color: #ffc107; border-left: 2px solid #ffc107; }
  .rl-status-line.ok { background: rgba(40,167,69,0.08); color: #6fcf97; border-left: 2px solid #28a745; }
  .rl-status-line.muted { background: rgba(255,255,255,0.04); color: white; border-left: 2px solid rgba(255,255,255,0.15); }

  .rl-btn-action {
    display: block; width: 100%; padding: 9px 14px;
    font-size: 0.72rem; font-weight: 700; letter-spacing: 0.08em;
    text-transform: uppercase; text-align: center; text-decoration: none;
    border-radius: 20px; border: 1.5px solid;
    transition: background 0.18s, color 0.18s; cursor: pointer; margin-top: 12px;
  }
  .rl-btn-action.primary { background: #ffc107; border-color: #ffc107; color: #111; }
  .rl-btn-action.primary:hover { background: #e6ac00; border-color: #e6ac00; color: #111; }
  .rl-btn-action.success { background: transparent; border-color: rgba(111,207,151,0.5); color: #6fcf97; }
  .rl-btn-action.success:hover { background: rgba(40,167,69,0.15); color: #6fcf97; }
  .rl-btn-action.disabled-btn { background: rgba(255,255,255,0.04); border-color: rgba(255,255,255,0.08); color: rgba(255,255,255,0.18); cursor: not-allowed; }

  .rl-empty {
    text-align: center; padding: 50px 20px;
    border: 1px dashed rgba(255,255,255,0.1); border-radius: 6px;
    background: rgba(255,255,255,0.02);
  }
  .rl-empty i { font-size: 2.5rem; color: rgba(255,255,255,0.12); display: block; margin-bottom: 14px; }
  .rl-empty h4 { color: rgba(255,255,255,0.25); font-size: 1.2rem; margin-bottom: 6px; }
  .rl-empty p { color: rgba(255,255,255,0.18); font-size: 0.82rem; }

  /* ── FORM VIEW ── */
  .gl-body {
    flex: 1; min-height: 0;
    overflow-y: auto; overflow-x: hidden;
    padding: 0 8px 8px;
  }
  .gl-body::-webkit-scrollbar { width: 5px; }
  .gl-body::-webkit-scrollbar-track { background: transparent; }
  .gl-body::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.12); border-radius: 4px; }

  .gl-footer {
    flex-shrink: 0;
    display: flex; align-items: center;
    justify-content: space-between; gap: 1rem;
    padding: 0.65rem 0; margin-top: 0.25rem;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    flex-wrap: wrap;
  }
  .gl-footer-hint {
    font-size: 0.72rem; color: rgba(255,255,255,0.3);
    font-style: italic;
  }

  .gl-save-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    box-sizing: border-box; height: 34px;
    background: #ffc107; color: #111 !important;
    border: none; border-radius: 20px;
    font-size: 0.78rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;
    padding: 0 22px; cursor: pointer;
    transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
    box-shadow: 0 3px 12px rgba(255, 193, 7, 0.3);
    margin-left: auto;
  }
  .gl-save-btn:hover:not(:disabled) {
    background: #e6ac00; transform: translateY(-1px);
    box-shadow: 0 5px 18px rgba(255, 193, 7, 0.45);
  }
  .gl-save-btn:disabled {
    background: rgba(255,255,255,0.07) !important;
    color: rgba(255,255,255,0.2) !important;
    cursor: not-allowed; box-shadow: none; transform: none;
  }

  .gl-excel-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.15);
    color: #fff; border-radius: 8px;
    font-size: 0.78rem; font-weight: 600; padding: 8px 18px;
    cursor: pointer; transition: background 0.18s;
  }
  .gl-excel-btn:hover { background: rgba(255,255,255,0.12); }

  /* Invitado cards */
  .nogal-card {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 6px; overflow: hidden;
  }
  .nogal-card .card-body { padding: 14px 16px; }

  .numero-circular {
    width: 22px; height: 22px;
    background-color: #ffc107; color: #111;
    border-radius: 50%; display: flex;
    justify-content: center; align-items: center;
    font-weight: 700; font-size: 10px; flex-shrink: 0;
  }

  .titulo-nombre { flex: 1; margin-bottom: 0; }
  .titulo-nombre b {
    font-size: 0.82rem; font-weight: 700;
    color: rgba(255,255,255,0.88); letter-spacing: 0.03em; text-transform: uppercase;
  }
  .linea-titulo {
    width: 100%; margin: 5px 0;
    background-color: rgba(255,255,255,0.08);
    height: 1px; border: none; opacity: 1;
  }

  /* Form inputs */
  .form-control {
    border-radius: 6px;
    border: 1px solid rgba(255,255,255,0.15);
    font-size: 0.82rem; padding: 8px 12px;
    color: #fff; background: rgba(255,255,255,0.06);
    transition: border-color 0.15s, background 0.15s;
  }
  .form-control:focus {
    border-color: #ffc107; background: rgba(255,255,255,0.09);
    box-shadow: none; outline: none; color: #fff;
  }
  .form-control::placeholder { color: rgba(255,255,255,0.22); font-size: 0.78rem; }
  .form-control.is-invalid { border-color: #dc3545; }
  .invalid-feedback { font-size: 0.72rem; margin-top: 3px; color: #ef9a9a; }

  .confirm-email,
  .confirm-document { border-left: 2px solid rgba(255,193,7,0.45); }
  .confirm-email:focus,
  .confirm-document:focus { border-left: 2px solid #ffc107; }
  .confirm-email.is-invalid,
  .confirm-document.is-invalid { border-left: 2px solid #dc3545; }

  .inv-info-row {
    display: flex; flex-wrap: wrap; gap: 10px;
    font-size: 0.75rem; color: rgba(255,255,255,0.38);
  }
  .inv-info-row .item strong { color: rgba(255,255,255,0.7); font-weight: 600; }

  /* ── NUEVO LAYOUT INVITADOS ── */
  .inv-counter {
    display: flex; align-items: center; gap: 14px;
    padding: 9px 14px; border-radius: 8px; margin-bottom: 12px;
    background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07);
  }
  .inv-counter-label {
    font-size: 0.6rem; font-weight: 700; letter-spacing: 0.12em;
    text-transform: uppercase; color: rgba(255,255,255,0.25);
    white-space: nowrap; flex-shrink: 0;
  }
  .inv-counter-bar {
    flex: 1; height: 5px; border-radius: 3px;
    background: rgba(255,255,255,0.08); overflow: hidden;
  }
  .inv-counter-fill {
    height: 100%; border-radius: 3px;
    background: linear-gradient(90deg, #ffc107, #ffe066);
    transition: width 0.4s ease;
  }
  .inv-counter-text { font-size: 0.8rem; color: rgba(255,255,255,0.45); white-space: nowrap; flex-shrink: 0; }
  .inv-counter-text strong { color: #fff; }
  .inv-pend-txt { color: #ffc107; }
  .inv-counter-divider { color: rgba(255,255,255,0.1); flex-shrink: 0; }
  .inv-counter-ambiente {
    font-size: 0.9rem; font-weight: 700; color: white;
    white-space: nowrap; flex-shrink: 0; overflow: hidden; text-overflow: ellipsis; max-width: 350px;
  }

  .inv-panel-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 11px 14px; border-bottom: 1px solid rgba(255,255,255,0.07);
    font-size: 0.9rem; font-weight: 700; color: white;
    text-transform: uppercase; letter-spacing: 0.07em; flex-shrink: 0;
  }
  .inv-count-badge {
    font-size: 0.68rem; font-weight: 700; padding: 2px 10px; border-radius: 20px;
    background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.45);
  }
  .inv-count-badge.warn { background: rgba(255,193,7,0.1); border-color: rgba(255,193,7,0.3); color: #ffc107; }
  .inv-count-badge.ok   { background: rgba(40,167,69,0.1);  border-color: rgba(40,167,69,0.3);  color: #6fcf97; }

  .inv-reg-list { overflow-y: auto; padding: 4px 0; }
  .inv-reg-list::-webkit-scrollbar { width: 3px; }
  .inv-reg-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }

  .inv-reg-item {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 14px; transition: background 0.15s;
    border-bottom: 1px solid rgba(255,255,255,0.04);
  }
  .inv-reg-item:last-child { border-bottom: none; }
  .inv-reg-item:hover { background: rgba(255,255,255,0.03); }

  .inv-reg-num {
    width: 20px; height: 20px; flex-shrink: 0;
    background: #ffc107; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 9px; font-weight: 700; color: black;
  }
  .inv-reg-info { flex: 1; min-width: 0; }
  .inv-reg-name {
    font-size: 0.85rem; font-weight: 600; color: rgba(255,255,255,0.85);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }
  .inv-reg-doc { font-size: 0.72rem; color: rgba(255,255,255,0.3); margin-top: 1px; }

  .inv-menu-tag {
    font-size: 0.68rem; font-weight: 600; margin-top: 3px;
    color: rgba(111,207,151,0.85); display: flex; align-items: center; gap: 4px;
  }
  .inv-menu-tag i { font-size: 0.62rem; }
  .inv-menu-tag.pend { color: #ffc107; }

  /* Filas de invitados ya registrados que solo faltan elegir menú:
     mismas columnas que una fila normal de la tabla, solo que nombre/apellido/documento
     quedan de solo lectura (ya vienen guardados) y el menú sí es editable. */
  .inv-row-registrado {
    background: rgba(255,193,7,0.05);
    border-left: 3px solid rgba(255,193,7,0.4);
  }
  .inv-row-registrado .inv-row-num { color: #ffc107; }
  .inv-input.inv-readonly {
    display: flex; align-items: center;
    min-height: 34px; color: rgba(255,255,255,0.6);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }

  .inv-tipo-badge {
    font-size: 0.62rem; font-weight: 700; padding: 2px 8px;
    border-radius: 20px; text-transform: uppercase; letter-spacing: 0.05em; flex-shrink: 0;
  }
  .inv-tipo-a { background: rgba(255,193,7,0.12); color: #ffc107; border: 1px solid rgba(255,193,7,0.25); }
  .inv-tipo-s { background: rgba(111,207,151,0.1); color: #6fcf97; border: 1px solid rgba(111,207,151,0.25); }
  .inv-tipo-p { background: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.08); }

  .inv-reg-empty, .inv-all-done {
    display: flex; flex-direction: column; align-items: center; gap: 8px;
    padding: 28px 16px; color: rgba(255,255,255,0.22);
    font-size: 0.8rem; text-align: center;
  }
  .inv-reg-empty i { font-size: 1.6rem; opacity: 0.35; }
  .inv-all-done i { font-size: 1.8rem; color: #6fcf97; opacity: 0.7; }
  .inv-all-done p { color: rgba(255,255,255,0.3); margin: 0; font-size: 0.82rem; }

  .inv-table-wrap { overflow-x: auto; padding: 6px; }
  .inv-table { width: 100%; border-collapse: collapse; }
  .inv-table thead th {
    padding: 7px 8px; font-size: 0.70rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em; text-align: center;
    color: white; border-bottom: 1px solid rgba(255,255,255,0.07);
    white-space: nowrap;
  }
  .inv-table .inv-row td { padding: 4px 3px; vertical-align: middle; }
  .inv-row-num {
    width: 32px; text-align: center;
    font-size: 0.72rem; font-weight: 700; color: white;
  }
  .inv-table .inv-input { font-size: 0.88rem; padding: 7px 9px; width: 100%; min-width: 88px; }
  .inv-row.has-data { background: rgba(255,255,255,0.02); }
  .inv-row.has-data .inv-row-num { color: #ffc107; }

  /* Términos */
  .rl-terms-invoice-bar {
    display: flex; justify-content: space-between;
    align-items: flex-end; flex-wrap: wrap; gap: 12px;
  }
  .gl-terms-title {
    font-size: 0.68rem; font-weight: 700; letter-spacing: 2px;
    text-transform: uppercase; color: rgba(255,255,255,0.35); margin-bottom: 10px;
  }
  .form-check-label { font-size: 0.8rem; color: rgba(255,255,255,0.65); }
  .form-check-label a { color: #ffc107; font-weight: 600; text-decoration: underline; text-underline-offset: 2px; }
  .form-check-input {
    border: 1.5px solid rgba(255,255,255,0.28) !important;
    border-radius: 3px !important; background: transparent !important;
  }
  .form-check-input:checked {
    background-color: #ffc107 !important; border-color: #ffc107 !important;
  }

  .rl-invoice-trigger {
    display: inline-flex; align-items: center; gap: 8px;
    border: 1px solid rgba(255,255,255,0.15); padding: 8px 14px; border-radius: 8px;
    font-size: 0.72rem; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase;
    color: rgba(255,255,255,0.6); cursor: pointer;
    background: rgba(255,255,255,0.06);
    transition: background 0.18s, border-color 0.18s;
  }
  .rl-invoice-trigger:hover { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.25); color: #fff; }


  /* Modals */
  .modal-content {
    border: 1px solid rgba(255,255,255,0.1); border-radius: 8px;
    background: rgba(14,14,14,0.97); backdrop-filter: blur(20px);
    box-shadow: 0 8px 40px rgba(0,0,0,0.6);
  }

  /* Toast de aviso del campo Dirección: más bajo de alto */
  .direccion-toast-sm .swal2-title { font-size: 0.8rem; margin: 0; }
  .direccion-toast-sm .swal2-icon { width: 1.6em; height: 1.6em; margin: 0 0.5em 0 0; }
  .direccion-toast-sm.swal2-icon-content { font-size: 1em; }

  /* Modal de facturación: fondo blanco (en vez del glass oscuro del resto de modales) */
  #modalFactura .modal-content,
  #modalVerFactura .modal-content {
    background: #ffffff;
    backdrop-filter: none;
    -webkit-backdrop-filter: none;
    box-shadow: 0 8px 40px rgba(0,0,0,0.25);
  }
  #modalFactura .modal-header,
  #modalFactura .modal-footer,
  #modalVerFactura .modal-header,
  #modalVerFactura .modal-footer {
    border-color: rgba(0,0,0,0.1);
  }
  #modalFactura .modal-title,
  #modalVerFactura .modal-title { color: #212529; }
  #modalFactura .modal-body,
  #modalVerFactura .modal-body { color: rgba(0,0,0,0.75); }
  #modalFactura .btn-close,
  #modalVerFactura .btn-close { filter: none; opacity: 0.6; }
  #modalFactura .btn-close:hover,
  #modalVerFactura .btn-close:hover { opacity: 1; }
  #modalFactura .form-label,
  #modalVerFactura .form-label { color: rgba(0,0,0,0.55); }
  #modalFactura .form-control,
  #modalVerFactura .form-control {
    color: #212529;
    background: #fff;
    border: 1px solid rgba(0,0,0,0.18);
  }
  #modalFactura .form-control:focus,
  #modalVerFactura .form-control:focus {
    background: #fff;
    border-color: #ffc107;
  }
  #modalFactura .btn-secondary,
  #modalVerFactura .btn-secondary {
    background: #e9ecef;
    border: 1px solid rgba(0,0,0,0.12);
    color: #495057;
  }
  #modalVerFactura .form-control[readonly] {
    background: #f8f9fa;
  }
  .modal-header { border-bottom: 1px solid rgba(255,255,255,0.08); padding: 14px 18px; }
  .modal-title { font-size: 1rem; font-weight: 700; color: #fff; }
  .modal-body { color: rgba(255,255,255,0.72); font-size: 0.85rem; }
  .modal-footer { border-top: 1px solid rgba(255,255,255,0.08); padding: 12px 18px; }
  .modal .btn-close { filter: invert(1) opacity(0.5); }
  .modal .btn-close:hover { opacity: 1; }
  .modal .btn-primary {
    background: #ffc107; border-color: #ffc107; color: #111;
    border-radius: 8px; font-size: 0.78rem; font-weight: 700;
  }
  .modal .btn-primary:hover { background: #e6ac00; border-color: #e6ac00; }
  .modal .btn-secondary {
    background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.15);
    color: rgba(255,255,255,0.55); border-radius: 8px; font-size: 0.78rem; font-weight: 600;
  }
  .modal .form-label {
    font-size: 0.7rem; font-weight: 600; letter-spacing: 0.04em;
    text-transform: uppercase; color: rgba(255,255,255,0.38); margin-bottom: 4px;
  }

  /* Dynamic table row controls */
  .inv-table-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 8px 6px 6px;
    border-top: 1px solid rgba(255,255,255,0.06);
    flex-shrink: 0;
    flex-wrap: wrap;
  }
  .inv-add-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    box-sizing: border-box; height: 34px;
    background: rgba(255,193,7,0.1); border: 1px dashed rgba(255,193,7,0.35);
    color: #ffc107; border-radius: 6px;
    font-size: 0.78rem; font-weight: 600; padding: 0 14px;
    cursor: pointer; transition: background 0.15s, border-color 0.15s;
  }
  .inv-add-btn:hover:not(:disabled) { background: rgba(255,193,7,0.18); border-color: rgba(255,193,7,0.6); }
  .inv-add-btn:disabled { opacity: 0.35; cursor: not-allowed; }

  .inv-remove-btn {
    background: rgba(255,107,107,0.12);
    color: #ff6b6b; font-size: 0.8rem;
    padding: 4px 7px; border-radius: 4px; cursor: pointer;
    transition: color 0.15s, background 0.15s; line-height: 1;
    border: 2px solid rgba(255,107,107,0.12);
  }
  .inv-remove-btn:hover { color: #ff6b6b; background: rgba(255,107,107,0.12); }

  .inv-row-actions { display: flex; align-items: center; justify-content: center; gap: 6px; }

  .inv-send-row-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    height: 30px; padding: 0 14px; border-radius: 15px; flex-shrink: 0; white-space: nowrap;
    background: rgba(255,193,7,0.22); border: 1.5px solid #ffc107;
    color: #ffe27a; font-size: 0.72rem; font-weight: 700; cursor: pointer;
    transition: background 0.15s, color 0.15s, opacity 0.15s;
  }
  .inv-send-row-btn:hover:not(:disabled) { background: #ffc107; color: #111; }
  .inv-send-row-btn:disabled:not(.reenvio-agotado) { opacity: 0.3; cursor: not-allowed; }
  .inv-send-row-btn.enviada {
    background: rgba(40,167,69,0.35); border: 1.5px solid #3ddc84; color: #eafff2;
  }
  .inv-send-row-btn.enviada:hover:not(:disabled) { background: #3ddc84; color: #0b2e1a; }
  .inv-send-row-btn.reenvio-agotado {
    opacity: 1; background: rgba(40,167,69,0.2); border: 1.5px solid rgba(61,220,132,0.65);
    color: #a4f0c2; cursor: default;
  }
  .inv-ver-boleta-btn {
    display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;
    width: 30px; height: 30px; border-radius: 50%;
    background: rgba(255,255,255,0.1); border: 1.5px solid rgba(255,255,255,0.35);
    color: #fff; font-size: 0.8rem; cursor: pointer;
    transition: background 0.15s, border-color 0.15s;
  }
  .inv-ver-boleta-btn:hover { background: #ffffff; border-color: #ffffff; color: #111; }

  .inv-confirm-cell { display: flex; align-items: center; justify-content: center; }
  .inv-confirm-check { color: #6fcf97; font-size: 1rem; }

  .inv-menu-radios { display: flex; gap: 6px; flex-wrap: wrap; justify-content: center; align-items: center; }
  .inv-menu-radio {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 12px; border-radius: 20px; cursor: pointer;
    border: 1.5px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.1);
    font-size: 0.74rem; font-weight: 700; color: rgba(255,255,255,0.9);
    transition: background 0.15s, border-color 0.15s, color 0.15s;
    white-space: nowrap; margin: 0;
  }
  .inv-menu-radio:hover { border-color: rgba(255,255,255,0.7); background: rgba(255,255,255,0.16); }
  .inv-menu-radio input[type="radio"] { accent-color: #ffc107; margin: 0; cursor: pointer; width: 14px; height: 14px; }
  .inv-menu-radio.is-checked { background: #ffc107; border-color: #ffc107; color: #111; }
  .inv-menu-radios.is-locked .inv-menu-radio { opacity: 0.5; cursor: not-allowed; }
  .inv-menu-radios.is-locked .inv-menu-radio:hover { border-color: rgba(255,255,255,0.4); background: rgba(255,255,255,0.1); }
  .inv-menu-radios.is-locked .inv-menu-radio input[type="radio"] { cursor: not-allowed; }

  .inv-empty-state {
    display: flex; flex-direction: column; align-items: center; gap: 8px;
    padding: 32px 16px; color: rgba(255,255,255,0.22); font-size: 0.8rem; text-align: center;
  }

  .inv-send-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: linear-gradient(135deg, rgba(111,207,151,0.15), rgba(111,207,151,0.08));
    border: 1px solid rgba(111,207,151,0.3); color: #6fcf97;
    border-radius: 6px; font-size: 0.78rem; font-weight: 600;
    padding: 8px 16px; width: 100%; justify-content: center;
    cursor: pointer; transition: background 0.15s, border-color 0.15s;
  }
  .inv-send-btn:hover:not(:disabled) {
    background: linear-gradient(135deg, rgba(111,207,151,0.25), rgba(111,207,151,0.14));
    border-color: rgba(111,207,151,0.55);
  }
  .inv-send-btn:disabled {
    opacity: 0.3; cursor: not-allowed;
    background: rgba(255,255,255,0.04); color: rgba(255,255,255,0.3);
    border-color: rgba(255,255,255,0.1);
  }

  .gl-footer-actions { display: flex; align-items: center; gap: 10px; margin-left: auto; flex-wrap: wrap; }

  .rl-invoice-trigger-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    box-sizing: border-box; height: 34px;
    border: none; padding: 0 22px; border-radius: 20px;
    font-size: 0.78rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;
    color: #111 !important; cursor: pointer; white-space: nowrap;
    background: #ffc107;
    box-shadow: 0 3px 12px rgba(255, 193, 7, 0.3);
    transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
  }
  .rl-invoice-trigger-btn:hover {
    background: #e6ac00;
    transform: translateY(-1px);
    box-shadow: 0 5px 18px rgba(255, 193, 7, 0.45);
  }

  .rl-invoice-trigger-btn.rl-invoice-view-btn {
    color: #fff !important;
    background: #28a745;
    box-shadow: 0 3px 12px rgba(40, 167, 69, 0.3);
  }
  .rl-invoice-trigger-btn.rl-invoice-view-btn:hover {
    background: #218838;
    box-shadow: 0 5px 18px rgba(40, 167, 69, 0.45);
  }

  .rl-invoice-trigger-btn.rl-exit-btn {
    color: #fff !important;
    background: rgba(255,255,255,0.1);
    border: 1.5px solid rgba(255,255,255,0.3);
    box-shadow: none;
    text-decoration: none;
  }
  .rl-invoice-trigger-btn.rl-exit-btn:hover {
    background: rgba(220,53,69,0.85);
    border-color: #dc3545;
    box-shadow: 0 5px 18px rgba(220,53,69,0.35);
    transform: translateY(-1px);
  }

  /* ── RESPONSIVE ── */
  @media (max-width: 767px) {

    /* Liberar la cadena de overflow/height fija para que la página fluya */
    .contenedor-general {
      height: auto !important; min-height: 0 !important;
      overflow: visible !important;
    }
    .card-principal {
      overflow: visible !important; flex: none !important;
      padding: 12px 10px 0 !important;
      margin-bottom: 30px !important;
    }
    .rl-page {
      overflow: visible !important; flex: none !important;
    }
    .gl-body {
      overflow: visible !important; height: auto !important;
      flex: none !important; padding: 0 2px 8px !important;
    }

    /* Topbar compacto */
    .rv-topbar { gap: 0.4rem; }
    .rv-topbar > div { flex-wrap: nowrap !important; align-items: center; }
    .rv-topbar .step-badge { font-size: 0.72rem; padding: 5px 10px; white-space: nowrap; flex-wrap: nowrap; }
    .rv-topbar .step-name  { display: none; }
    .rv-topbar .step-divider { display: none; }
    .gl-action-btn { font-size: 0.7rem; padding: 6px 10px; white-space: nowrap; }

    /* Listado header */
    .gl-listado-header { flex-direction: column; align-items: flex-start; }

    /* Indicador de progreso: apilar en dos líneas */
    .inv-counter { flex-wrap: wrap; gap: 6px 10px; }
    .inv-counter-label   { order: 1; }
    .inv-counter-ambiente { order: 2; max-width: calc(100% - 80px); font-size: 0.82rem; }
    .inv-counter-divider { display: none; }
    .inv-counter-bar  { order: 3; flex: 1 0 100%; }
    .inv-counter-text { order: 4; width: 100%; }

    /* Paneles: sin altura fija al apilar */
    .nogal-card.h-100 { height: auto !important; }
    .inv-reg-list { max-height: 220px; }

    /* Tabla de invitados */
    .inv-table-wrap { padding: 4px 2px; }
    .inv-table thead th { font-size: 0.58rem; padding: 5px 3px; letter-spacing: 0; }
    .inv-table .inv-row td { padding: 3px 2px; }
    .inv-table .inv-input { font-size: 0.8rem; padding: 6px 6px; min-width: 74px; }
    .inv-row-num { width: 22px; font-size: 0.65rem; }

    /* Panel add-btn + guardar: apilados y a todo el ancho */
    .inv-table-footer { flex-direction: column; align-items: stretch; }
    .inv-add-btn { width: 100%; justify-content: center; }

    /* Footer */
    .gl-footer {
      flex-direction: column; align-items: stretch; gap: 0.5rem;
    }
    .gl-footer-hint { display: none; }
    .gl-save-btn { width: 100%; justify-content: center; margin-left: 0; }
    .gl-footer-actions { flex-direction: column; align-items: stretch; width: 100%; margin-left: 0; }
    .rl-invoice-trigger-btn { width: 100%; justify-content: center; }

    /* Términos */
    .rl-terms-invoice-bar { flex-direction: column; }

    /* Listado de reservas cards */
    .rl-meta { flex-wrap: wrap; }
  }

  /* Modal de publicidad: fondo realmente transparente (anula el .modal-content oscuro/blur general) */
  #popupGuests .modal-content {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
  }
  #popupGuests .modal-content { position: relative; }

  /* Botón de cerrar: ícono propio (no usa .btn-close de Bootstrap, así evitamos sus variables/filters) */
  #popupGuests .gl-popup-close-btn {
    position: absolute;
    top: -14px;
    right: -14px;
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: 0;
    padding: 0;
    margin: 0;
    cursor: pointer;
    z-index: 2;
  }
  #popupGuests .gl-popup-close-btn i {
    color: #ffffff !important;
    font-size: 18px;
    filter: drop-shadow(0 1px 3px rgba(0,0,0,0.9));
  }
  #popupGuests .gl-popup-close-btn:hover i { color: #ffe082 !important; }

  /* Modal de publicidad: la imagen no debe ocupar toda la pantalla */
  .gl-popup-dialog { max-width: 420px; }
  .gl-popup-img {
    display: block;
    margin: 0 auto;
    max-width: 100%;
    max-height: 60vh;
    width: auto;
    height: auto;
    object-fit: contain;
    border-radius: 8px;
  }
</style>

<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

<div class="rl-page container py-2 py-lg-3 mb-3 mb-lg-5">
  <div class="card-principal">
    <?php if ($this->listado): ?>
    <!-- ── VISTA DE LISTADO DE RESERVAS ── -->
    <div class="gl-listado-wrap">

      <div class="gl-listado-header">
        <div>
          <h2 class="gl-listado-title">Mis Reservas</h2>
          <p class="gl-listado-subtitle">Gestiona invitados, factura electrónica y accede a tus boletas.</p>
        </div>
        <a href="/page/evento" class="gl-action-btn">
          <i class="fa-solid fa-plus"></i> Realizar otra compra
        </a>
      </div>

      <?php if (is_countable($this->reservas) && count($this->reservas) > 0): ?>
        <div class="row g-3">
          <?php foreach ($this->reservas as $reserva): ?>
            <div class="col-md-3 col-lg-3">
              <div class="reserva-card h-100 d-flex flex-column">
                <div class="card-header">
                  <div class="rnum">
                    #<?= $reserva->id ?> <?= htmlspecialchars($reserva->nombre_ambiente) ?>
                  </div>
                </div>

                <div class="card-body d-flex flex-column" style="flex:1;">
                  <div class="rl-meta">
                    <div class="rl-meta-person">
                      <i class="fas fa-users"></i>
                      <?= $reserva->total_personas ?> persona<?= $reserva->total_personas != 1 ? 's' : '' ?>
                    </div>
                    <div class="rl-meta-badge">
                      <?php
                        $badgeClass = in_array($reserva->estado, [2, 3, 11]) ? 'rl-badge-success' : 'rl-badge-warning';
                      ?>
                      <span class="rl-badge <?= $badgeClass ?>"><?= htmlspecialchars($reserva->estado_texto) ?></span>
                    </div>
                  </div>

                  <div style="flex:1;">
                    <?php if ($reserva->faltan_invitados): ?>
                      <div class="rl-status-line warn">
                        <i class="fas fa-exclamation-triangle" style="font-size:0.65rem;"></i>
                        Faltan datos de invitados
                      </div>
                    <?php endif; ?>

                    <?php if (!$reserva->factura_completa): ?>
                      <div class="rl-status-line warn">
                        <i class="fas fa-file-invoice" style="font-size:0.65rem;"></i>
                        Falta factura electrónica
                      </div>
                    <?php endif; ?>

                    <?php if ($reserva->qrs_generados > 0): ?>
                      <div class="rl-status-line ok">
                        <i class="fas fa-check" style="font-size:0.65rem;"></i>
                        QRs: <?= $reserva->qrs_generados ?>/<?= $reserva->boletas_esperadas ?> generados
                      </div>
                    <?php elseif ($reserva->qr_anteriores > 0): ?>
                      <div class="rl-status-line muted">
                        <i class="fas fa-circle-notch fa-spin" style="font-size:0.65rem;"></i>
                        QRs en proceso…
                      </div>
                    <?php else: ?>
                      <div class="rl-status-line muted">
                        <i class="fas fa-times" style="font-size:0.65rem;"></i>
                        Sin QRs generados
                      </div>
                    <?php endif; ?>
                  </div>

                  <?php if ($reserva->id == 896): ?>
                    <span class="rl-btn-action disabled-btn">
                      <i class="fas fa-clock me-1"></i>Procesando
                    </span>
                  <?php elseif ($reserva->puede_ver): ?>
                    <a href="/page/servicios/info?id=<?= enc_id($reserva->id) ?>" class="rl-btn-action success">
                      <i class="fas fa-eye me-1"></i>Ver Boletas
                    </a>
                  <?php elseif ($reserva->puede_gestionar): ?>
                    <a href="/page/guests?id=<?= enc_id($reserva->id) ?>" class="rl-btn-action primary">
                      <i class="fas fa-edit me-1"></i>Completar Datos
                    </a>
                  <?php else: ?>
                    <span class="rl-btn-action disabled-btn">
                      <i class="fas fa-clock me-1"></i>Procesando
                    </span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

      <?php else: ?>
        <div class="rl-empty">
          <i class="fas fa-calendar-alt"></i>
          <h4>Sin reservas activas</h4>
          <p>No tienes reservas pendientes o confirmadas.</p>
        </div>
      <?php endif; ?>
    </div>

    <?php else: ?>
    <!-- ── VISTA DE EDICIÓN DE INVITADOS ── -->

    <!-- Encabezado: regresar · step -->
    <div class="rv-topbar">
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="/page/guests" class="sb-back-btn">
          <i class="fa-solid fa-angle-left"></i>&nbsp; Volver
        </a>
        <div class="step-indicator">
          <div class="step-badge">
            <i class="fa-solid fa-ticket me-2"></i>
            <span class="step-text">Paso 5 de 5</span>
            <span class="step-divider">•</span>
            <span class="step-name">Registro de boletas</span>
          </div>
        </div>
      </div>
      <a href="/page/evento" class="gl-action-btn">
        <i class="fa-solid fa-plus"></i> Realizar otra compra
      </a>
    </div>

    <!-- Cuerpo scrollable -->
    <div class="gl-body">

      <?php if ($this->mostrarModalExcel): ?>
        <div class="text-center mb-3">
          <button type="button" class="gl-excel-btn" data-bs-toggle="modal" data-bs-target="#modalCargarExcel">
            <i class="fas fa-file-excel me-1"></i>Cargar Invitados desde Excel
          </button>
        </div>
      <?php endif; ?>

      <form method="post" action="/page/guests/editarinvitados" id="form-guests" class="form-guests">
        <input type="hidden" name="reserva_id" value="<?= enc_id($this->reserva->id) ?>">
        <?php
          $registrados = []; $pendientes = []; $cnt = 1;
          foreach ($this->invitados as $inv) {
            $esReg = ($inv->invitadoReserva_estado_invitado == 'A' || $inv->invitadoReserva_estado_invitado == 'S')
              || ($inv->invitadoReserva_estado_invitado == 'P'
                  && $inv->documento_invitado != ''
                  && $inv->invitadoReserva_nombre_invitado != ''
                  && !str_starts_with($inv->invitadoReserva_nombre_invitado, 'Invitado'));
            if ($esReg) $registrados[] = ['obj' => $inv, 'num' => $cnt];
            else        $pendientes[]  = ['obj' => $inv, 'num' => $cnt];
            $cnt++;
          }
          $total     = count($this->invitados);
          $totalReg  = count($registrados);
          $totalPend = count($pendientes);
          $pct       = $total > 0 ? round(($totalReg / $total) * 100) : 0;
        ?>

        <!-- Indicador de progreso -->
        <div class="inv-counter">
          <span class="inv-counter-ambiente"><?= htmlspecialchars($this->nombreAmbiente ?: '—') ?></span>
          <span class="inv-counter-divider">|</span>
          <div class="inv-counter-bar">
            <div class="inv-counter-fill" style="width:<?= $pct ?>%"></div>
          </div>
          <div class="inv-counter-text">
            <strong><?= $totalReg ?></strong> / <?= $total ?> registrados
            <?php if ($totalPend > 0): ?>
              &nbsp;·&nbsp; <span class="inv-pend-txt"><?= $totalPend ?> pendiente<?= $totalPend > 1 ? 's' : '' ?></span>
            <?php endif; ?>
          </div>
        </div>

        <div class="row g-3 mb-3">

          <!-- ── Tabla única de invitados (registrados + pendientes) ── -->
          <div class="col-12">
            <div class="nogal-card h-100 d-flex flex-column">
              <div class="inv-panel-header">
                <span>Agregar invitados</span>
                <?php if ($totalPend > 0): ?>
                  <span class="inv-count-badge warn" id="slotsBadge"><?= $totalPend ?> cupo<?= $totalPend > 1 ? 's' : '' ?> disponible<?= $totalPend > 1 ? 's' : '' ?></span>
                <?php else: ?>
                  <span class="inv-count-badge ok"><i class="fas fa-check me-1"></i>Completo</span>
                <?php endif; ?>
              </div>

              <?php if ($totalPend > 0 || $totalReg > 0): ?>
                <?php if ($totalReg > 0): ?>
                  <div class="alert alert-warning py-2 px-3 mx-2 mt-2 mb-0" style="font-size:0.82rem;">
                    <i class="fa-solid fa-paper-plane me-1"></i>
                    Puede asignar el menú y enviar la boleta de cada invitado registrado de forma individual, sin necesidad de esperar a completar la información de todos los invitados de la reserva. La boleta de los invitados, socios o cosocios será enviada directamente al responsable de la reserva.
                  </div>
                <?php endif; ?>
                <div class="inv-table-wrap flex-grow-1">
                  <table class="inv-table">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Tipo</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Documento</th>
                        <th>Confirmar doc.</th>
                        <?php if ($this->menuHabilitado): ?>
                        <th>Menú</th>
                        <?php endif; ?>
                        <th>Boleteria</th>
                      </tr>
                    </thead>
                    <tbody id="invTableRegistrados">
                      <?php foreach ($registrados as $entry): $reg = $entry['obj']; $menuActual = $reg->invitadoReserva_menu ?: 'normal'; ?>
                        <tr class="inv-row-registrado<?= $reg->boleta_enviada ? ' inv-row-enviada' : '' ?>" data-inv-id="<?= $reg->id_invitado ?>"<?= $reg->boleta_enviada ? ' data-enviada="1"' : '' ?>>
                          <td class="inv-row-num"><?= $entry['num'] ?></td>
                          <td class="text-center">
                            <span class="inv-tipo-badge inv-tipo-<?= strtolower($reg->invitadoReserva_estado_invitado) ?>">
                              <?= $reg->invitadoReserva_estado_invitado === 'A' ? 'Socio' : ($reg->invitadoReserva_estado_invitado === 'S' ? 'Cosocio' : 'Invitado') ?>
                            </span>
                          </td>
                          <td>
                            <div class="inv-input inv-readonly"><?= htmlspecialchars($reg->invitadoReserva_nombre_invitado) ?></div>
                            <input type="hidden" name="invitados[<?= $reg->id_invitado ?>][id_invitado]" value="<?= $reg->id_invitado ?>">
                            <input type="hidden" name="invitados[<?= $reg->id_invitado ?>][invitadoReserva_nombre_invitado]" value="<?= htmlspecialchars($reg->invitadoReserva_nombre_invitado) ?>">
                            <input type="hidden" name="invitados[<?= $reg->id_invitado ?>][invitadoReserva_correo_invitado]" value="<?= htmlspecialchars($reg->invitadoReserva_correo_invitado ?? '') ?>">
                            <input type="hidden" name="invitados[<?= $reg->id_invitado ?>][invitadoReserva_fecha_nacimiento]" value="<?= htmlspecialchars($reg->invitadoReserva_fecha_nacimiento ?? '1990-01-01') ?>">
                            <input type="hidden" name="invitados[<?= $reg->id_invitado ?>][invitadoReserva_telefono]" value="<?= htmlspecialchars($reg->invitadoReserva_telefono ?? '') ?>">
                            <?php if (!$this->menuHabilitado): ?>
                              <input type="hidden" name="invitados[<?= $reg->id_invitado ?>][invitadoReserva_menu]" value="normal">
                            <?php endif; ?>
                          </td>
                          <td>
                            <div class="inv-input inv-readonly"><?= htmlspecialchars($reg->invitadoReserva_apellido_invitado) ?></div>
                            <input type="hidden" name="invitados[<?= $reg->id_invitado ?>][invitadoReserva_apellido_invitado]" value="<?= htmlspecialchars($reg->invitadoReserva_apellido_invitado) ?>">
                          </td>
                          <td>
                            <div class="inv-input inv-readonly"><?= htmlspecialchars($reg->documento_invitado ?: '—') ?></div>
                            <input type="hidden" name="invitados[<?= $reg->id_invitado ?>][documento_invitado]" value="<?= htmlspecialchars($reg->documento_invitado) ?>">
                          </td>
                          <td class="inv-confirm-cell">
                            <i class="fa-solid fa-circle-check inv-confirm-check" data-bs-toggle="tooltip" title="Documento confirmado"></i>
                          </td>
                          <?php if ($this->menuHabilitado): ?>
                          <td>
                            <div class="inv-menu-radios<?= $reg->boleta_enviada ? ' is-locked' : '' ?>">
                              <label class="inv-menu-radio">
                                <input type="radio" name="invitados[<?= $reg->id_invitado ?>][invitadoReserva_menu]" value="normal" <?= $menuActual === 'normal' ? 'checked' : '' ?> <?= $reg->boleta_enviada ? 'disabled' : '' ?>>
                                <span>Normal</span>
                              </label>
                              <label class="inv-menu-radio">
                                <input type="radio" name="invitados[<?= $reg->id_invitado ?>][invitadoReserva_menu]" value="vegetariano" <?= $menuActual === 'vegetariano' ? 'checked' : '' ?> <?= $reg->boleta_enviada ? 'disabled' : '' ?>>
                                <span>Vegetariano</span>
                              </label>
                            </div>
                            <?php if ($reg->boleta_enviada): ?>
                              <input type="hidden" name="invitados[<?= $reg->id_invitado ?>][invitadoReserva_menu]" value="<?= htmlspecialchars($menuActual) ?>">
                            <?php endif; ?>
                          </td>
                          <?php endif; ?>
                          <?php
                            $tituloEnvio = $reg->boleta_reenvio_agotado
                              ? 'Ya se reenvió esta boleta'
                              : ($reg->boleta_enviada ? 'Reenviar boleta' : 'Enviar boleta');
                            $labelEnvio = $reg->boleta_reenvio_agotado
                              ? 'Boleta reenviada'
                              : ($reg->boleta_enviada ? 'Reenviar boleta' : 'Enviar boleta');
                          ?>
                          <td class="text-center">
                            <div class="inv-row-actions">
                              <button type="button" class="inv-send-row-btn<?= $reg->boleta_enviada ? ' enviada' : '' ?><?= $reg->boleta_reenvio_agotado ? ' reenvio-agotado' : '' ?>"
                                data-bs-toggle="tooltip" title="<?= $tituloEnvio ?>" disabled>
                                <i class="fa-solid fa-paper-plane"></i> <?= $labelEnvio ?>
                              </button>
                              <?php if ($reg->boleta_enviada && $reg->boleta_uid): ?>
                                <button type="button" class="inv-ver-boleta-btn"
                                  data-bs-toggle="modal" data-bs-target="#modalVerBoletaGuest"
                                  data-uid="<?= htmlspecialchars($reg->boleta_uid) ?>"
                                  data-nombre="<?= htmlspecialchars(trim($reg->invitadoReserva_nombre_invitado . ' ' . $reg->invitadoReserva_apellido_invitado)) ?>"
                                  title="Ver boleta">
                                  <i class="fa-solid fa-eye"></i>
                                </button>
                              <?php endif; ?>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                    <tbody id="invTableBody"></tbody>
                  </table>
                  <?php if ($totalPend > 0): ?>
                    <div id="invEmptyState" class="inv-empty-state">
                      <i class="fas fa-user-plus fa-2x mb-1 opacity-50"></i>
                      <span>Haz clic en <strong>+</strong> para agregar invitados</span>
                    </div>
                  <?php endif; ?>
                </div>
                <div class="inv-table-footer">
                  <?php if ($totalPend > 0): ?>
                    <button type="button" id="btnAgregarFila" class="inv-add-btn">
                      <i class="fas fa-plus"></i> Agregar invitado
                    </button>
                  <?php else: ?>
                    <span></span>
                  <?php endif; ?>
                  <button type="submit" form="form-guests" class="gl-save-btn" id="guardarBtn" disabled>
                    Guardar datos &nbsp;<i class="fa-solid fa-floppy-disk"></i>
                  </button>
                </div>
              <?php else: ?>
                <div class="inv-all-done flex-grow-1 justify-content-center">
                  <i class="fas fa-check-circle"></i>
                  <p>Todos los invitados han sido registrados.</p>
                </div>
              <?php endif; ?>
            </div>
          </div>

        </div>

        <!-- Términos y factura -->
        <?php if (is_countable($this->terminos) && count($this->terminos) > 0): ?>
        <div class="nogal-card mb-3">
          <div class="card-body">
              <p class="gl-terms-title">Términos y condiciones</p>

            <div class="rl-terms-invoice-bar">
              <?php if ($this->terminos): ?>
                <?php foreach ($this->terminos as $termino): ?>
                  <div class="form-check mb-2">
                    <input class="form-check-input termino-checkbox" type="checkbox"
                      id="termino_<?= $termino->termino_id ?>" required>
                    <label class="form-check-label" for="termino_<?= $termino->termino_id ?>">
                      Acepto
                      <?php if ($termino->termino_enlace): ?>
                        <a href="<?= htmlspecialchars($termino->termino_enlace) ?>" target="_blank">
                          <?= htmlspecialchars($termino->termino_titulo) ?>
                        </a>
                      <?php elseif ($termino->termino_texto): ?>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalTerminoGuest_<?= $termino->termino_id ?>">
                          <?= htmlspecialchars($termino->termino_titulo) ?>
                        </a>
                      <?php else: ?>
                        <?= htmlspecialchars($termino->termino_titulo) ?>
                      <?php endif; ?>
                    </label>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endif; ?>

      </form>

    </div><!-- /gl-body -->

    <!-- Footer fijo: menú (imagen promocional) y factura -->
    <div class="gl-footer">
      <div class="gl-footer-actions">
        <?php if ($this->popupGuests && $this->popupGuests->publicidad_estado == 1): ?>
          <button type="button" class="rl-invoice-trigger-btn" data-bs-toggle="modal" data-bs-target="#popupGuests">
            <i class="fa-solid fa-utensils"></i> Menú
          </button>
        <?php endif; ?>
        <?php
        $facturaCompleta = $this->reserva->reserva_fact_nit && $this->reserva->reserva_fact_razon
          && $this->reserva->reserva_fact_mail && $this->reserva->reserva_fact_dire && $this->reserva->reserva_fact_tele;
        ?>
        <?php if ($facturaCompleta): ?>
          <button type="button" class="rl-invoice-trigger-btn rl-invoice-view-btn" data-bs-toggle="modal" data-bs-target="#modalVerFactura">
            <i class="fa-solid fa-file-invoice"></i> Ver datos de factura electrónica
          </button>
        <?php else: ?>
          <button type="button" class="rl-invoice-trigger-btn" data-bs-toggle="modal" data-bs-target="#modalFactura">
            <i class="fa-solid fa-file-invoice"></i> Ingresar datos para factura electrónica
          </button>
        <?php endif; ?>
        <a href="/" class="rl-invoice-trigger-btn rl-exit-btn" id="btnSalirRegistro">
          <i class="fa-solid fa-right-from-bracket"></i> Salir del registro de invitados
        </a>
      </div>
    </div>

    <?php endif; ?>
  </div>
</div>

<?php if (!$this->listado): ?>

<?php if ($this->mostrarModalExcel): ?>
<div class="modal fade" id="modalCargarExcel" tabindex="-1" aria-labelledby="modalCargarExcelLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCargarExcelLabel">Cargar Invitados desde Excel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p style="font-size:0.82rem;color:rgba(255,255,255,0.6);margin-bottom:10px;">
          Seleccione un archivo Excel con columnas: <strong>Nombre</strong>, <strong>Apellido</strong>, <strong>Documento</strong>.
        </p>
        <p style="font-size:0.82rem;color:rgba(255,255,255,0.6);margin-bottom:14px;">
          Descargue un <a href="/corte/ejemplocargue.xlsx" target="_blank" style="color:#ffc107;font-weight:600;">archivo de ejemplo</a> para ver el formato requerido.
        </p>
        <div class="mb-3">
          <label for="excelFile" class="form-label">Archivo Excel (.xlsx o .xls)</label>
          <input type="file" id="excelFile" accept=".xlsx,.xls" class="form-control">
        </div>
        <div id="excelErrors" class="mt-2" style="display:none;font-size:0.8rem;color:#ef9a9a;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="cargarExcelBtn">Cargar y Validar</button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Modal Factura Electrónica -->
<div class="modal fade" id="modalFactura" tabindex="-1" aria-labelledby="modalFacturaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalFacturaLabel">Datos para Factura Electrónica</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <?php
        // Limpia la dirección del socio: solo letras, números, espacios, # y - (quita / * , . y demás símbolos raros)
        $direccionLimpia = preg_replace('/[^\p{L}\p{N}\s#-]/u', '', $this->socio->sbe_dire ?? '');
        $direccionLimpia = trim(preg_replace('/\s+/', ' ', $direccionLimpia));
        ?>
        <div id="facturaMensaje" class="alert d-none" role="alert"></div>
        <form id="formFacturaE">
          <input type="hidden" name="id" value="<?= enc_id($this->reserva->id); ?>">
          <div class="alert alert-info" role="alert">
            La facturación electrónica debe emitirse a nombre de la persona o entidad responsable. Dato obligatorio.
          </div>
          <div class="row mt-3">
            <div class="col-md-6 mb-2">
              <label for="nit" class="form-label">NIT o Cédula</label>
              <input type="text" class="form-control" id="nit" name="nit"
                value="<?= $this->reserva->reserva_documento; ?>" required>
            </div>
            <div class="col-md-6 mb-2">
              <label for="razon_social" class="form-label">Razón Social</label>
              <input type="text" class="form-control" id="razon_social" name="razon_social"
                value="<?= $this->reserva->reserva_nombre_cliente . " " . $this->reserva->reserva_apellido_cliente; ?>" required>
            </div>
            <div class="col-md-6 mb-2">
              <label for="direccion" class="form-label">Dirección</label>
              <input type="text" class="form-control" id="direccion" name="direccion"
                value="<?= htmlspecialchars($direccionLimpia) ?>" required>
            </div>
            <div class="col-md-6 mb-2">
              <label for="telefono" class="form-label">Teléfono</label>
              <input type="text" class="form-control" id="telefono" name="telefono"
                value="<?= $this->reserva->reserva_telefono ?>" required>
            </div>
          </div>
          <div class="mb-2">
            <label for="correo_factura" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" id="correo_factura" name="correo_factura"
              value="<?= $this->reserva->reserva_correo ?>" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btnGuardarFactura">Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: ver boleta ya enviada (PDF generado al momento del envío) -->
<div class="modal fade" id="modalVerBoletaGuest" tabindex="-1" aria-labelledby="modalVerBoletaGuestLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalVerBoletaGuestLabel">
          <i class="fa-solid fa-ticket"></i> Boleta — <span id="verBoletaGuestNombre"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-0">
        <iframe id="verBoletaGuestFrame" src="" title="Boleta" style="width:100%;height:70vh;border:0;background:#fff;"></iframe>
      </div>
      <div class="modal-footer">
        <a id="verBoletaGuestDescargar" href="#" download class="btn btn-primary" target="_blank">
          <i class="fa-solid fa-download"></i> Descargar
        </a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<?php if ($facturaCompleta): ?>
<!-- Modal Ver Datos de Factura Electrónica -->
<div class="modal fade" id="modalVerFactura" tabindex="-1" aria-labelledby="modalVerFacturaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalVerFacturaLabel">Datos de Factura Electrónica</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="row mt-1">
          <div class="col-md-6 mb-2">
            <label class="form-label">NIT o Cédula</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($this->reserva->reserva_fact_nit) ?>" readonly>
          </div>
          <div class="col-md-6 mb-2">
            <label class="form-label">Razón Social</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($this->reserva->reserva_fact_razon) ?>" readonly>
          </div>
          <div class="col-md-6 mb-2">
            <label class="form-label">Dirección</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($this->reserva->reserva_fact_dire) ?>" readonly>
          </div>
          <div class="col-md-6 mb-2">
            <label class="form-label">Teléfono</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($this->reserva->reserva_fact_tele) ?>" readonly>
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label">Correo electrónico</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($this->reserva->reserva_fact_mail) ?>" readonly>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Modales de términos con texto -->
<?php if ($this->terminos): ?>
  <?php foreach ($this->terminos as $termino): ?>
    <?php if ($termino->termino_texto): ?>
      <div class="modal fade" id="modalTerminoGuest_<?= $termino->termino_id ?>" tabindex="-1"
        aria-labelledby="modalTerminoGuest_<?= $termino->termino_id ?>Label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalTerminoGuest_<?= $termino->termino_id ?>Label">
                <?= htmlspecialchars($termino->termino_titulo) ?>
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" style="font-size:0.85rem;line-height:1.6;">
              <?= $termino->termino_texto ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                onclick="aceptarTerminoGuest(<?= $termino->termino_id ?>)">
                Acepto los Términos
              </button>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
<?php endif; ?>

<?php if ($this->popupGuests && $this->popupGuests->publicidad_estado == 1): ?>
  <!-- Modal PopUp (imagen/banner) -->
  <div class="modal fade" id="popupGuests" tabindex="-1" aria-labelledby="popupGuestsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered gl-popup-dialog">
      <div class="modal-content">
        <button type="button" class="gl-popup-close-btn" data-bs-dismiss="modal" aria-label="Cerrar">
          <i class="fas fa-times"></i>
        </button>
        <div class="modal-body">
          <?php if ($this->popupGuests->publicidad_video != ""): ?>
            <div class="fondo-video-youtube">
              <div class="banner-video-youtube" id="videobannerGuests<?= $this->popupGuests->publicidad_id ?>"
                data-video="<?= $this->id_youtube($this->popupGuests->publicidad_video) ?>"></div>
            </div>
          <?php endif; ?>
          <?php if ($this->popupGuests->publicidad_imagen != ""): ?>
            <?php if ($this->popupGuests->publicidad_enlace != ""): ?>
              <a href="<?= $this->popupGuests->publicidad_enlace ?>"
                <?= $this->popupGuests->publicidad_tipo_enlace == 1 ? "target='_blank'" : '' ?>>
            <?php endif; ?>
            <img class="gl-popup-img d-none d-md-block" src="/images/<?= $this->popupGuests->publicidad_imagen ?>"
              alt="Imagen PopUp <?= $this->popupGuests->publicidad_nombre ?>">
            <img class="gl-popup-img d-block d-md-none"
              src="/images/<?= $this->popupGuests->publicidad_imagenresponsive ?>"
              alt="Imagen PopUp <?= $this->popupGuests->publicidad_nombre ?>">
            <?php if ($this->popupGuests->publicidad_enlace != ""): ?>
              </a>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var popupGuestsEl = document.getElementById('popupGuests');
      if (popupGuestsEl) {
        var modalPopupGuests = new bootstrap.Modal(popupGuestsEl);
        modalPopupGuests.show();
      }
    });
  </script>
<?php endif; ?>

<?php if ($this->errorBoleta): ?>
  <script>
    swal.fire({
      icon: 'error',
      title: 'Error al registrar boleta',
      text: '<?= htmlspecialchars($this->errorBoleta) ?>',
      confirmButtonText: 'Aceptar'
    });
  </script>
<?php endif; ?>

<script>
  // Dirección: bloquea caracteres especiales (/ * , . etc.) mientras se escribe o pega
  (function () {
    const direccionInput = document.getElementById('direccion');
    if (!direccionInput) return;
    let avisoActivo = false;
    direccionInput.addEventListener('input', function () {
      const limpio = this.value.replace(/[^\p{L}\p{N}\s#-]/gu, '');
      if (limpio !== this.value) {
        this.value = limpio;
        if (!avisoActivo) {
          avisoActivo = true;
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'warning',
            title: 'No se permiten caracteres especiales (/ * , . etc.) en la dirección',
            showConfirmButton: false,
            timer: 2200,
            timerProgressBar: true,
            padding: '0.5em 0.9em',
            customClass: { popup: 'direccion-toast-sm' }
          });
          setTimeout(() => { avisoActivo = false; }, 2500);
        }
      }
    });
  })();

  // Salir del registro de invitados: confirma antes de redirigir al inicio,
  // por si hay cambios sin guardar en el formulario.
  (function () {
    const btnSalir = document.getElementById('btnSalirRegistro');
    if (!btnSalir) return;
    btnSalir.addEventListener('click', function (e) {
      e.preventDefault();
      const destino = this.href;
      Swal.fire({
        title: '¿Salir del registro de invitados?',
        text: 'Si tienes cambios sin guardar, se perderán.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, salir',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = destino;
        }
      });
    });
  })();

  $('#btnGuardarFactura').on('click', function() {
    Swal.fire({
      title: '¿Desea guardar los datos de factura electrónica?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, guardar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        let datos = $('#formFacturaE').serialize();
        console.log(datos);
        $.ajax({
          url: '/page/guests/factura',
          type: 'POST',
          dataType: 'json',
          data: datos,
          success: function(res) {
            console.log("adsf" + res);
            if (res.status === 'success') {
              $('#modalFactura').modal('hide');
              Swal.fire({
                icon: 'success',
                title: '¡Guardado!',
                text: res.message,
                timer: 2000,
                showConfirmButton: false
              }).then(() => {
                // Recargar para que el botón cambie a "Ver datos de factura electrónica"
                // y la modal de solo-lectura muestre los datos recién guardados.
                window.location.reload();
              });
            } else {
              Swal.fire({ icon: 'error', title: 'Error', text: res.message });
            }
          },
          error: function(xhr) {
            Swal.fire({
              icon: 'error',
              title: 'Error de comunicación',
              html: `<pre style="text-align:left">${xhr.responseText || 'Sin respuesta'}</pre>`
            });
          }
        });
      }
    });
  });

</script>

<?php if ($totalPend > 0): ?>
<script>
  var pendientesPool = <?= json_encode(array_map(fn($e) => ['id' => $e['obj']->id_invitado, 'num' => $e['num']], $pendientes)) ?>;
  var totalPendienteInicial = <?= $totalPend ?>;
</script>
<?php else: ?>
<script>var pendientesPool = []; var totalPendienteInicial = 0;</script>
<?php endif; ?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-guests');
    const guardarBtn = document.getElementById('guardarBtn');

    const checkboxesTerminos = document.querySelectorAll('.termino-checkbox');
    const btnAgregarFila = document.getElementById('btnAgregarFila');
    const invTableBody = document.getElementById('invTableBody');
    const invEmptyState = document.getElementById('invEmptyState');
    const slotsBadge = document.getElementById('slotsBadge');

    const availableSlots = typeof pendientesPool !== 'undefined' ? [...pendientesPool] : [];
    const totalPend = typeof totalPendienteInicial !== 'undefined' ? totalPendienteInicial : 0;
    const menuHabilitado = <?= $this->menuHabilitado ? 'true' : 'false' ?>;

    function initTooltips(scope) {
      if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) return;
      (scope || document).querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        const existente = bootstrap.Tooltip.getInstance(el);
        if (existente) existente.dispose();
        new bootstrap.Tooltip(el);
      });
    }
    initTooltips(form);

    function initMenuRadios(scope) {
      (scope || document).querySelectorAll('.inv-menu-radios').forEach(group => {
        group.querySelectorAll('.inv-menu-radio').forEach(label => {
          const radio = label.querySelector('input[type="radio"]');
          label.classList.toggle('is-checked', radio.checked);
          radio.addEventListener('change', () => {
            group.querySelectorAll('.inv-menu-radio').forEach(l => l.classList.remove('is-checked'));
            if (radio.checked) label.classList.add('is-checked');
          });
        });
      });
    }
    initMenuRadios(document);

    // Bloquea permanentemente una fila cuya boleta ya fue enviada: nada queda editable,
    // salvo el propio botón de enviar (para poder reenviar el correo si hace falta).
    function bloquearFila(row) {
      row.querySelectorAll('input[type="text"]').forEach(el => { el.readOnly = true; });
      row.querySelectorAll('input[type="radio"]').forEach(el => {
        if (el.checked && !row.querySelector(`input[type="hidden"][name="${el.name}"]`)) {
          const hidden = document.createElement('input');
          hidden.type = 'hidden';
          hidden.name = el.name;
          hidden.value = el.value;
          el.closest('.inv-menu-radios')?.insertAdjacentElement('afterend', hidden);
        }
        el.disabled = true;
      });
      row.querySelector('.inv-menu-radios')?.classList.add('is-locked');
      row.querySelector('.inv-remove-btn')?.remove();
      row.classList.add('inv-row-enviada');
    }

    document.querySelectorAll('tr[data-enviada="1"]').forEach(bloquearFila);

    function mostrarToast(mensaje) {
      const toast = document.createElement('div');
      toast.className = 'toast align-items-center text-white bg-danger border-0 position-fixed bottom-0 end-0 m-3';
      toast.setAttribute('role', 'alert');
      toast.style.zIndex = '9999';
      toast.innerHTML = `<div class="d-flex"><div class="toast-body">${mensaje}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
      document.body.appendChild(toast);
      const bsToast = new bootstrap.Toast(toast, { delay: 5000 });
      bsToast.show();
      toast.addEventListener('hidden.bs.toast', () => toast.remove());
    }

    function mostrarError(input, mensaje) {
      if (!input) return;
      input.classList.add('is-invalid');
      let errorDiv = input.parentNode.querySelector('.invalid-feedback');
      if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.classList.add('invalid-feedback');
        input.parentNode.appendChild(errorDiv);
      }
      errorDiv.style.display = 'block';
      errorDiv.innerText = mensaje;
    }

    function limpiarError(input) {
      if (!input || input.getAttribute('data-duplicado') === '1') return;
      input.classList.remove('is-invalid');
      const errorDiv = input.parentNode.querySelector('.invalid-feedback');
      if (errorDiv) { errorDiv.style.display = 'none'; errorDiv.innerText = ''; }
    }

    function esDocumentoConsecutivo(doc) {
      if (doc.length < 4) return false;
      let asc = true, desc = true;
      for (let i = 1; i < doc.length; i++) {
        if (parseInt(doc[i]) !== parseInt(doc[i-1]) + 1) asc = false;
        if (parseInt(doc[i]) !== parseInt(doc[i-1]) - 1) desc = false;
      }
      return asc || desc;
    }

    function renumerarFilas() {
      invTableBody.querySelectorAll('tr.inv-row').forEach((row, i) => {
        const cell = row.querySelector('.inv-row-num');
        if (cell) cell.textContent = i + 1;
      });
    }

    function updateSlotsBadge() {
      const rem = availableSlots.length;
      if (slotsBadge) {
        slotsBadge.textContent = rem + ' slot' + (rem !== 1 ? 's' : '') + ' disponible' + (rem !== 1 ? 's' : '');
        slotsBadge.className = 'inv-count-badge ' + (rem > 0 ? 'warn' : 'ok');
      }
      if (btnAgregarFila) btnAgregarFila.disabled = rem === 0;
      if (invEmptyState) {
        invEmptyState.style.display = (invTableBody && invTableBody.querySelectorAll('tr').length > 0) ? 'none' : 'flex';
      }
    }

    function crearFila(invitado) {
      const tr = document.createElement('tr');
      tr.className = 'inv-row';
      tr.dataset.invId = invitado.id;
      tr.innerHTML = `
        <td class="inv-row-num">${invitado.num}</td>
        <td class="text-center">
          <span class="inv-tipo-badge inv-tipo-p">Invitado</span>
        </td>
        <td>
          <input type="hidden" name="invitados[${invitado.id}][id_invitado]" value="${invitado.id}">
          <input type="hidden" name="invitados[${invitado.id}][invitadoReserva_fecha_nacimiento]" value="1990-01-01">
          <input type="text" class="form-control inv-input" placeholder="Nombre" maxlength="50"
            name="invitados[${invitado.id}][invitadoReserva_nombre_invitado]">
        </td>
        <td>
          <input type="text" class="form-control inv-input" placeholder="Apellido" maxlength="50"
            name="invitados[${invitado.id}][invitadoReserva_apellido_invitado]">
        </td>
        <td>
          <input type="text" class="form-control inv-input" placeholder="Documento" maxlength="20"
            name="invitados[${invitado.id}][documento_invitado]">
        </td>
        <td>
          <input type="text" class="form-control inv-input confirm-document" placeholder="Confirmar" maxlength="20"
            name="invitados[${invitado.id}][confirmar_documento]" autocomplete="off">
          ${menuHabilitado ? '' : `<input type="hidden" name="invitados[${invitado.id}][invitadoReserva_menu]" value="normal">`}
        </td>
        ${menuHabilitado ? `
        <td>
          <div class="inv-menu-radios">
            <label class="inv-menu-radio">
              <input type="radio" name="invitados[${invitado.id}][invitadoReserva_menu]" value="normal" checked>
              <span>Normal</span>
            </label>
            <label class="inv-menu-radio">
              <input type="radio" name="invitados[${invitado.id}][invitadoReserva_menu]" value="vegetariano">
              <span>Vegetariano</span>
            </label>
          </div>
        </td>
        ` : ''}
        <td class="text-center">
          <div class="inv-row-actions">
            <button type="button" class="inv-send-row-btn d-none" data-bs-toggle="tooltip" title="Enviar boleta" disabled>
              <i class="fa-solid fa-paper-plane"></i> Enviar boleta
            </button>
            <button type="button" class="inv-remove-btn" data-bs-toggle="tooltip" title="Quitar fila">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </td>
      `;
      initTooltips(tr);

      // Nombre y Apellido: solo letras (acentos, ñ, ü y espacios)
      const soloLetras = function () {
        const limpio = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');
        if (limpio !== this.value) this.value = limpio;
      };
      const nombreInput = tr.querySelector('input[name*="invitadoReserva_nombre_invitado"]');
      const apellidoInput = tr.querySelector('input[name*="invitadoReserva_apellido_invitado"]');
      if (nombreInput) nombreInput.addEventListener('input', soloLetras);
      if (apellidoInput) apellidoInput.addEventListener('input', soloLetras);

      const docInput = tr.querySelector('input[name*="documento_invitado"]');
      docInput.addEventListener('input', function() {
        // Documento: solo dígitos
        const soloDigitos = this.value.replace(/\D/g, '');
        if (soloDigitos !== this.value) this.value = soloDigitos;
        const valor = this.value.trim();
        if (valor.length >= 7) {
          fetch('/page/guests/validardocumento', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'documento=' + encodeURIComponent(valor)
          })
          .then(r => r.json())
          .then(res => {
            if (!res.status) {
              this.setAttribute('data-duplicado', '1');
              mostrarError(this, res.message ?? 'Este documento ya está registrado en otra reserva.');
              if (guardarBtn) guardarBtn.disabled = true;
            } else {
              this.removeAttribute('data-duplicado');
              limpiarError(this);
              validarCampos();
            }
          })
          .catch(() => {
            this.setAttribute('data-duplicado', '1');
            mostrarError(this, 'No se pudo validar el documento.');
            if (guardarBtn) guardarBtn.disabled = true;
          });
        } else {
          this.removeAttribute('data-duplicado');
          limpiarError(this);
          validarCampos();
        }
      });

      const confirmarInput = tr.querySelector('.confirm-document');
      confirmarInput.addEventListener('input', function () {
        // Confirmar documento: solo dígitos
        const soloDigitos = this.value.replace(/\D/g, '');
        if (soloDigitos !== this.value) this.value = soloDigitos;
      });
      confirmarInput.addEventListener('paste', e => { e.preventDefault(); mostrarToast('No se permite copiar y pegar en los campos de confirmación.'); });
      confirmarInput.addEventListener('drop', e => e.preventDefault());
      confirmarInput.addEventListener('contextmenu', e => e.preventDefault());

      tr.querySelector('.inv-remove-btn').addEventListener('click', function() {
        availableSlots.push(invitado);
        tr.remove();
        renumerarFilas();
        updateSlotsBadge();
        validarCampos();
      });

      return tr;
    }

    if (btnAgregarFila) {
      btnAgregarFila.addEventListener('click', function() {
        if (availableSlots.length === 0) return;
        const next = availableSlots.shift();
        const tr = crearFila(next);
        invTableBody.appendChild(tr);
        if (invEmptyState) invEmptyState.style.display = 'none';
        tr.querySelector('input[type="text"]').focus();
        initMenuRadios(tr);
        renumerarFilas();
        updateSlotsBadge();
        validarCampos();
      });
    }

    function validarCampos() {
      let completo = true;
      const documentos = new Set();
      const currentRows = form.querySelectorAll('.inv-row');

      const docsDuplicados = form.querySelectorAll('input[name*="documento_invitado"][data-duplicado="1"]');
      if (docsDuplicados.length > 0) completo = false;

      // No se exige agregar los invitados pendientes para poder guardar: se puede guardar
      // en cualquier momento (p.ej. solo para actualizar el menú de los ya registrados) y
      // completar el resto de cupos más adelante. Las filas que sí se agreguen deben ser válidas.

      const todosTerminosAceptados = Array.from(checkboxesTerminos).every(c => c.checked);

      currentRows.forEach(row => {
        let filaValida = true;
        const sendBtn = row.querySelector('.inv-send-row-btn');

        const nombre = row.querySelector('input[name*="invitadoReserva_nombre_invitado"]');
        const apellido = row.querySelector('input[name*="invitadoReserva_apellido_invitado"]');
        const documento = row.querySelector('input[name*="documento_invitado"]');
        const confirmarDocumento = row.querySelector('input[name*="confirmar_documento"]');

        if (!nombre || !documento || !confirmarDocumento) return;

        const nombreValue = nombre.value.trim();
        const apellidoValue = apellido ? apellido.value.trim() : '';
        const docLimpio = documento.value.trim();

        if (!nombreValue && !docLimpio) {
          row.classList.remove('has-data');
          [nombre, apellido, documento, confirmarDocumento].forEach(el => { if (el) limpiarError(el); });
          completo = false;
          if (sendBtn) sendBtn.disabled = true;
          return;
        }

        row.classList.add('has-data');

        const soloLetrasRe = /^[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+$/;

        if (!nombreValue || nombreValue.length < 3) {
          mostrarError(nombre, 'Mínimo 3 caracteres.');
          completo = false; filaValida = false;
        } else if (!soloLetrasRe.test(nombreValue)) {
          mostrarError(nombre, 'Solo se permiten letras.');
          completo = false; filaValida = false;
        } else { limpiarError(nombre); }

        if (apellido && (!apellidoValue || apellidoValue.length < 3)) {
          mostrarError(apellido, 'Mínimo 3 caracteres.');
          completo = false; filaValida = false;
        } else if (apellido && !soloLetrasRe.test(apellidoValue)) {
          mostrarError(apellido, 'Solo se permiten letras.');
          completo = false; filaValida = false;
        } else { limpiarError(apellido); }

        if (!/^[0-9]{7,20}$/.test(docLimpio)) {
          mostrarError(documento, 'Entre 7 y 20 dígitos numéricos.');
          completo = false; filaValida = false;
        } else if (esDocumentoConsecutivo(docLimpio)) {
          mostrarError(documento, 'No puede ser consecutivo.');
          completo = false; filaValida = false;
        } else if (documentos.has(docLimpio)) {
          mostrarError(documento, 'Documento duplicado en el formulario.');
          completo = false; filaValida = false;
        } else {
          documentos.add(docLimpio);
          limpiarError(documento);
        }

        if (docLimpio !== confirmarDocumento.value.trim()) {
          mostrarError(confirmarDocumento, 'Los documentos no coinciden.');
          completo = false; filaValida = false;
        } else if (docLimpio && confirmarDocumento.value.trim()) {
          limpiarError(confirmarDocumento);
        }

        if (row.querySelector('[data-duplicado="1"]')) filaValida = false;

        if (sendBtn) sendBtn.disabled = !filaValida || !todosTerminosAceptados;
      });

      // Filas de invitados ya registrados: sus datos ya son válidos, solo dependen de los términos.
      // Si ya se usó el único reenvío permitido, el botón queda bloqueado sin importar los términos.
      document.querySelectorAll('#invTableRegistrados .inv-send-row-btn').forEach(btn => {
        if (btn.classList.contains('reenvio-agotado')) { btn.disabled = true; return; }
        btn.disabled = !todosTerminosAceptados;
      });

      if (!todosTerminosAceptados) completo = false;

      if (guardarBtn) guardarBtn.disabled = !completo;
      return completo;
    }

    window.validarCampos = validarCampos;

    form.addEventListener('input', validarCampos);
    form.addEventListener('change', validarCampos);
    checkboxesTerminos.forEach(cb => cb.addEventListener('change', validarCampos));

    validarCampos();

    form.addEventListener('submit', function(e) {
      e.preventDefault();

      if (!validarCampos()) {
        mostrarToast('Por favor, complete los campos correctamente y acepte los términos.');
        return;
      }

      if (guardarBtn) {
        guardarBtn.disabled = true;
        guardarBtn.innerHTML = 'Guardando... <i class="fa-solid fa-spinner fa-spin ms-1"></i>';
      }
      form.submit();
    });

    // Inserta (o revela) el botón "Ver boleta" de la fila una vez que ya existe el PDF generado.
    function mostrarBotonVerBoleta(row, uid, nombreCompleto) {
      if (!uid) return;
      const actions = row.querySelector('.inv-row-actions') || row.querySelector('td:last-child');
      if (!actions) return;
      let verBtn = actions.querySelector('.inv-ver-boleta-btn');
      if (!verBtn) {
        verBtn = document.createElement('button');
        verBtn.type = 'button';
        verBtn.className = 'inv-ver-boleta-btn';
        verBtn.setAttribute('data-bs-toggle', 'modal');
        verBtn.setAttribute('data-bs-target', '#modalVerBoletaGuest');
        verBtn.setAttribute('title', 'Ver boleta');
        verBtn.innerHTML = '<i class="fa-solid fa-eye"></i>';
        actions.appendChild(verBtn);
        initTooltips(actions);
      }
      verBtn.setAttribute('data-uid', uid);
      verBtn.setAttribute('data-nombre', nombreCompleto.trim());
    }

    // Envío individual de boleta por fila (ya registrados o pendientes recién completados)
    function enviarBoletaFila(btn) {
      const row = btn.closest('tr');
      const idInvitado = row.dataset.invId;
      const nombre = row.querySelector('[name*="invitadoReserva_nombre_invitado"]')?.value.trim() ?? '';
      const apellido = row.querySelector('[name*="invitadoReserva_apellido_invitado"]')?.value.trim() ?? '';
      const documento = row.querySelector('[name*="documento_invitado"]')?.value.trim() ?? '';
      const confirmarEl = row.querySelector('[name*="confirmar_documento"]');
      const confirmar = confirmarEl ? confirmarEl.value.trim() : documento;
      const menuEl = row.querySelector('[name*="invitadoReserva_menu"]:checked') || row.querySelector('[name*="invitadoReserva_menu"]');
      const menu = menuEl ? menuEl.value : 'normal';
      const reservaId = document.querySelector('input[name="reserva_id"]').value;

      const originalHtml = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

      fetch('/page/guests/enviaruno', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          reserva_id: reservaId,
          id_invitado: idInvitado,
          invitadoReserva_nombre_invitado: nombre,
          invitadoReserva_apellido_invitado: apellido,
          documento_invitado: documento,
          confirmar_documento: confirmar,
          invitadoReserva_menu: menu
        })
      })
      .then(r => r.json())
      .then(res => {
        if (res.status) {
          bloquearFila(row);
          btn.classList.add('enviada');
          mostrarBotonVerBoleta(row, res.boleta_uid, nombre + ' ' + apellido);
          if (res.reenvio_agotado) {
            // Ya se consumió el único reenvío permitido: el botón queda bloqueado para siempre.
            btn.classList.add('reenvio-agotado');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Boleta reenviada';
            const tooltip = bootstrap.Tooltip.getInstance(btn);
            if (tooltip) { tooltip.setContent({ '.tooltip-inner': 'Ya se reenvió esta boleta' }); }
            else { btn.setAttribute('title', 'Ya se reenvió esta boleta'); initTooltips(row); }
          } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Reenviar boleta';
            const tooltip = bootstrap.Tooltip.getInstance(btn);
            if (tooltip) { tooltip.setContent({ '.tooltip-inner': 'Reenviar boleta' }); }
            else { btn.setAttribute('title', 'Reenviar boleta'); initTooltips(row); }
          }
          Swal.fire({
            toast: true, position: 'top-end', icon: 'success',
            title: res.reenviada ? 'Boleta reenviada' : 'Boleta enviada',
            showConfirmButton: false, timer: 1800, timerProgressBar: true
          });
        } else if (res.limite_alcanzado) {
          // El backend rechazó el intento porque ya se había usado el único reenvío permitido.
          btn.classList.add('reenvio-agotado');
          btn.disabled = true;
          btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Boleta reenviada';
          const tooltip = bootstrap.Tooltip.getInstance(btn);
          if (tooltip) { tooltip.setContent({ '.tooltip-inner': 'Ya se reenvió esta boleta' }); }
          else { btn.setAttribute('title', 'Ya se reenvió esta boleta'); initTooltips(row); }
          Swal.fire({ icon: 'warning', title: 'Reenvío no disponible', text: res.message });
        } else {
          btn.disabled = false;
          btn.innerHTML = originalHtml;
          Swal.fire({ icon: 'error', title: 'No se pudo enviar', text: res.message || 'Intente de nuevo.' });
        }
      })
      .catch(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo procesar la solicitud.' });
      });
    }

    form.addEventListener('click', function(e) {
      const btn = e.target.closest('.inv-send-row-btn');
      if (btn && !btn.disabled) enviarBoletaFila(btn);
    });

    // Modal "Ver boleta": se llena con los data-* del botón que la abrió
    const modalVerBoleta = document.getElementById('modalVerBoletaGuest');
    if (modalVerBoleta) {
      const frame = document.getElementById('verBoletaGuestFrame');
      const nombreEl = document.getElementById('verBoletaGuestNombre');
      const descargarEl = document.getElementById('verBoletaGuestDescargar');
      modalVerBoleta.addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        if (!trigger) return;
        const uid = trigger.getAttribute('data-uid') || '';
        const nombre = trigger.getAttribute('data-nombre') || '';
        const url = uid ? '/pdfs_news/boleta_cena_' + encodeURIComponent(uid) + '.pdf' : '';
        frame.src = url;
        descargarEl.href = url;
        nombreEl.textContent = nombre;
      });
      modalVerBoleta.addEventListener('hidden.bs.modal', function () {
        frame.src = '';
      });
    }
  });

  function aceptarTerminoGuest(terminoId) {
    const checkbox = document.getElementById('termino_' + terminoId);
    if (checkbox) {
      checkbox.checked = true;
      checkbox.dispatchEvent(new Event('change'));
    }
  }
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<?php endif; ?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    validarSesion();
    setInterval(validarSesion, 10000);

    if (document.getElementById('modalCargarExcel')) {
      document.getElementById('cargarExcelBtn').addEventListener('click', function() {
        const fileInput = document.getElementById('excelFile');
        const file = fileInput.files[0];
        if (!file) { mostrarErrorExcel('Seleccione un archivo.'); return; }
        const reader = new FileReader();
        reader.onload = function(e) {
          const data = new Uint8Array(e.target.result);
          const workbook = XLSX.read(data, { type: 'array' });
          const sheetName = workbook.SheetNames[0];
          const worksheet = workbook.Sheets[sheetName];
          const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
          const headers = jsonData[0];
          if (!headers || headers.length < 3 || headers[0].toLowerCase() !== 'nombre' || headers[1].toLowerCase() !== 'apellido' || headers[2].toLowerCase() !== 'documento') {
            mostrarErrorExcel('El Excel debe tener columnas: Nombre, Apellido, Documento.');
            return;
          }
          const datos = [];
          for (let i = 1; i < jsonData.length; i++) {
            const row = jsonData[i];
            if (row && row.length >= 3) {
              datos.push({
                nombre: (row[0] || '').toString().trim(),
                apellido: (row[1] || '').toString().trim(),
                documento: (row[2] || '').toString().trim()
              });
            }
          }
          fetch('/page/guests/cargarExcel', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: 'reserva_id=' + encodeURIComponent(document.querySelector('input[name="reserva_id"]').value) + '&datos_excel=' + encodeURIComponent(JSON.stringify(datos))
            })
            .then(response => response.json())
            .then(res => {
              if (res.status) {
                llenarInputsConExcel(res.datos);
                $('#modalCargarExcel').modal('hide');
                Swal.fire({ icon: 'success', title: 'Éxito', text: 'Datos cargados correctamente.', confirmButtonText: 'Aceptar', confirmButtonColor: '#222220' })
                  .then(() => { validarCampos(); });
              } else {
                mostrarErrorExcel(res.message + (res.errores ? '<br>' + res.errores.join('<br>') : ''));
              }
            })
            .catch(() => { mostrarErrorExcel('Error al procesar el archivo.'); });
        };
        reader.readAsArrayBuffer(file);
      });
    }

    function mostrarErrorExcel(mensaje) {
      const div = document.getElementById('excelErrors');
      div.innerHTML = mensaje;
      div.style.display = 'block';
    }

    function llenarInputsConExcel(datos) {
      const cards = document.querySelectorAll('.nogal-card');
      let dataIndex = 0;
      for (let i = 0; i < cards.length && dataIndex < datos.length; i++) {
        const documentoInput = cards[i].querySelector('input[name*="documento_invitado"]');
        if (documentoInput && !documentoInput.value.trim()) {
          const dato = datos[dataIndex];
          const nombreInput = cards[i].querySelector('input[name*="invitadoReserva_nombre"]');
          const apellidoInput = cards[i].querySelector('input[name*="invitadoReserva_apellido"]');
          const confirmarDocumentoInput = cards[i].querySelector('input[name*="confirmar_documento"]');
          if (nombreInput) nombreInput.value = dato.nombre;
          if (apellidoInput) apellidoInput.value = dato.apellido;
          if (documentoInput) documentoInput.value = dato.documento;
          if (confirmarDocumentoInput) confirmarDocumentoInput.value = dato.documento;
          dataIndex++;
        }
      }
    }
  });
</script>
