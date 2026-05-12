<?php
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';
require 'funciones.php';
if ( !empty($_POST)) {
  
  // insert data
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  $sql = "INSERT INTO clientes (nombre,razon_social,cuit,cond_fiscal,id_cond_fiscal,direccion,telefono,email,pide_factura,id_usuario) VALUES (?,?,?,?,?,?,?,?,?,?)";
  $q = $pdo->prepare($sql);
  $pideFactura = isset($_POST['pide_factura']) ? 1 : 0;
  $q->execute(array($_POST['nombre'],$_POST['razon_social'],$_POST['cuit'],$_POST['cond_fiscal'],$_POST['id_cond_fiscal'],$_POST['direccion'],$_POST['telefono'],$_POST['email'],$pideFactura,$_SESSION["user"]['id']));
  
  Database::disconnect();
  
  header("Location: listarClientes.php");
}

$tax_types=getTiposCondicionesIvaReceptor();?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head_forms.php');?>
	<link rel="stylesheet" type="text/css" href="assets/css/select2.css">
  </head>
  <body class="light-only">
    <!-- Loader ends-->
    <!-- page-wrapper Start-->
    <div class="page-wrapper">
	    <?php include('header.php');?>
	  
      <!-- Page Header Start-->
      <div class="page-body-wrapper">
		    <?php include('menu.php');?>
        <!-- Page Sidebar Start-->
        <!-- Right sidebar Ends-->
        <div class="page-body">
          <div class="container-fluid">
            <div class="page-header">
              <div class="row">
                <div class="col-10">
                  <div class="page-header-left">
                    <h3><?php include("title.php"); ?></h3>
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="#"><i data-feather="home"></i></a></li>
                      <li class="breadcrumb-item">Nuevo Cliente</li>
                    </ol>
                  </div>
                </div>
                <!-- Bookmark Start-->
                <div class="col-2">
                  <div class="bookmark pull-right">
                    <ul>
                      <li><a  target="_blank" data-container="body" data-toggle="popover" data-placement="top" title="" data-original-title="<?php echo date('d-m-Y');?>"><i data-feather="calendar"></i></a></li>
                    </ul>
                  </div>
                </div>
                <!-- Bookmark Ends-->
              </div>
            </div>
          </div>
          <!-- Container-fluid starts-->
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-12">
                <div class="card">
                  <div class="card-header">
                    <h5>Nuevo Cliente</h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="nuevoCliente.php">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Pide factura</label>
                            <div class="col-sm-9">
                              <input name="pide_factura" id="pide_factura" type="checkbox" value="1">
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">CUIT <span id="req_cuit" style="display:none;color:#dc3545;">*</span></label>
                            <div class="col-sm-9"><input name="cuit" id="cuit" type="number" maxlength="99" class="form-control" value=""></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Condicion Fiscal <span id="req_cond_fiscal" style="display:none;color:#dc3545;">*</span></label>
                            <div class="col-sm-9">
                              <input type="hidden" name="id_cond_fiscal" id="id_cond_fiscal" value="6">
                              <select name="cond_fiscal" id="cond_fiscal" class="form-control">
                                <option value="">- Seleccione -</option><?php
                                foreach ($tax_types as $value) {
                                  $selected = ($value->Id == 6) ? "selected" : ""; // Por defecto Monotributo?>  
                                  <option <?=$selected?> data-id="<?=$value->Id?>"><?=$value->Desc?></option><?php
                                }?>
                              </select>
                              <small style="color:#6c757d;">Sugerida automáticamente según tipo de persona en padrón A13. Verificar antes de guardar.</small>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Razon Social <span id="req_razon_social" style="display:none;color:#dc3545;">*</span></label>
                            <div class="col-sm-9"><input name="razon_social" id="razon_social" type="text" maxlength="99" class="form-control" value=""></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Nombre</label>
                            <div class="col-sm-9"><input name="nombre" id="nombre" type="text" maxlength="99" class="form-control" value="" required="required" autofocus></div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-9">
                              <small id="padron_feedback" style="display:none;"></small>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Direccion</label>
                            <div class="col-sm-9"><input name="direccion" id="direccion" type="text" maxlength="99" class="form-control" value=""></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Telefono</label>
                            <div class="col-sm-9"><input name="telefono" type="number" maxlength="99" class="form-control" value=""></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">E-mail</label>
                            <div class="col-sm-9"><input name="email" type="text" maxlength="99" class="form-control" value=""></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Crear</button>
						            <a href="listarClientes.php" class="btn btn-light">Volver</a>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- Container-fluid Ends-->
        </div>
        <!-- footer start-->
		    <?php include("footer.php"); ?>
      </div>
    </div>
    <!-- latest jquery-->
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap js-->
    <script src="assets/js/bootstrap/popper.min.js"></script>
    <script src="assets/js/bootstrap/bootstrap.js"></script>
    <!-- feather icon js-->
    <script src="assets/js/icons/feather-icon/feather.min.js"></script>
    <script src="assets/js/icons/feather-icon/feather-icon.js"></script>
    <!-- Sidebar jquery-->
    <script src="assets/js/sidebar-menu.js"></script>
    <script src="assets/js/config.js"></script>
    <!-- Plugins JS start-->
    <script src="assets/js/typeahead/handlebars.js"></script>
    <script src="assets/js/typeahead/typeahead.bundle.js"></script>
    <script src="assets/js/typeahead/typeahead.custom.js"></script>
    <script src="assets/js/chat-menu.js"></script>
    <script src="assets/js/tooltip-init.js"></script>
    <script src="assets/js/typeahead-search/handlebars.js"></script>
    <script src="assets/js/typeahead-search/typeahead-custom.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="assets/js/script.js"></script>
    <!-- Plugin used-->
	  <script src="assets/js/select2/select2.full.min.js"></script>
    <script src="assets/js/select2/select2-custom.js"></script>
    
    <script>
    $(document).ready(function(){
      function normalizarCuit(cuit) {
        return String(cuit || '').replace(/\D/g, '');
      }

      function setPadronFeedback(texto, tipo) {
        var $fb = $('#padron_feedback');
        if (!texto) {
          $fb.hide().text('');
          return;
        }

        var color = '#0f7a0f';
        if (tipo === 'error') {
          color = '#b71c1c';
        } else if (tipo === 'warn') {
          color = '#7a5f00';
        }

        $fb.css('color', color).text(texto).show();
      }

      function consultarPadronA13() {
        if (!$('#pide_factura').is(':checked')) {
          return;
        }

        var cuit = normalizarCuit($('#cuit').val());
        if (cuit.length !== 11) {
          setPadronFeedback('Ingrese un CUIT válido de 11 dígitos para consultar padrón.', 'warn');
          return;
        }

        setPadronFeedback('Consultando padrón A13...', 'warn');

        $.ajax({
          url: 'ajaxBuscarClientePadronA13.php',
          type: 'POST',
          dataType: 'json',
          data: { cuit: cuit },
          success: function(response) {
            if (!response || !response.ok) {
              setPadronFeedback((response && response.message) ? response.message : 'No se pudo consultar padrón A13.', 'error');
              return;
            }

            if (response.data && response.data.razon_social) {
              $('#razon_social').val(response.data.razon_social);
            }

            if (response.data && response.data.cond_fiscal_sugerida) {
              $('#cond_fiscal').val(response.data.cond_fiscal_sugerida);
            }

            if (response.data && response.data.id_cond_fiscal_sugerida) {
              $('#id_cond_fiscal').val(response.data.id_cond_fiscal_sugerida);
            }

            var direccionActual = $.trim($('#direccion').val());
            if (response.data && response.data.direccion_sugerida && direccionActual === '') {
              $('#direccion').val(response.data.direccion_sugerida);
            }

            if (response.data && response.data.nombre_sugerido) {
              $('#nombre').val(response.data.nombre_sugerido);
            }

            setPadronFeedback('Datos obtenidos de padrón A13. La condición fiscal es sugerida por inferencia.', 'ok');
          },
          error: function() {
            setPadronFeedback('Error al consultar padrón A13.', 'error');
          }
        });
      }

      function actualizarObligatoriedadFactura() {
        var pideFactura = $('#pide_factura').is(':checked');
        $('#cuit').prop('required', pideFactura);
        $('#razon_social').prop('required', pideFactura);
        $('#cond_fiscal').prop('required', pideFactura);
        $('#req_razon_social').toggle(pideFactura);
        $('#req_cuit').toggle(pideFactura);
        $('#req_cond_fiscal').toggle(pideFactura);
      }

      // Manejar cambio en el select de condición fiscal
      $('#cond_fiscal').change(function(){
        var selectedOption = $(this).find('option:selected');
        var condFiscalId = selectedOption.data('id');
        $('#id_cond_fiscal').val(condFiscalId);
      });

      $('#pide_factura').change(function(){
        actualizarObligatoriedadFactura();
        if ($('#pide_factura').is(':checked')) {
          consultarPadronA13();
        } else {
          setPadronFeedback('', '');
        }
      });

      $('#cuit').on('blur', function(){
        consultarPadronA13();
      });
      
      // Establecer valor inicial si hay opción por defecto seleccionada
      var initialSelected = $('#cond_fiscal option:selected');
      if(initialSelected.length && initialSelected.data('id')){
        $('#id_cond_fiscal').val(initialSelected.data('id'));
      }

      actualizarObligatoriedadFactura();
    });
    </script>
  </body>
</html>