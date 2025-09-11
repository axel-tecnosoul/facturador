<?php
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';
if ( !empty($_POST)) {
  $modoDebug=1;
  if($modoDebug==1){
    var_dump($_POST);
  }

  // insert data
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  try{
    $cotizacion_usd=filter_var($_POST['cotizacion_usd'],FILTER_VALIDATE_FLOAT);
    $monto_pesos=null;
    if(isset($_POST['monto_pesos'])){
      $monto_pesos=filter_var($_POST['monto_pesos'],FILTER_VALIDATE_FLOAT);
    }
    $id_cliente=filter_var($_POST['id_cliente'],FILTER_VALIDATE_INT);
    $fecha_cobro=$_POST['fecha_cobro'];
    if($fecha_cobro==""){
      $fecha_cobro=null;
    }

    $pdo->beginTransaction();
    $idFactura=null;

    if(isset($_POST['generar_factura'])){
      $totalFactura=0;
      $itemsSan=array();
      if(isset($_POST['items'])){
        foreach($_POST['items'] as $item){
          if(!isset($item['precio'], $item['cantidad'])){
            $pdo->rollBack();
            die('Datos de ítems incompletos');
          }
          $desc=trim($item['descripcion']);
          $precio=filter_var($item['precio'],FILTER_VALIDATE_FLOAT);
          $cantidad=filter_var($item['cantidad'],FILTER_VALIDATE_INT);
          if($desc==='' || $precio===false || $cantidad===false || $cantidad<1){
            $pdo->rollBack();
            die('Datos de ítems inválidos');
          }
          $desc=htmlspecialchars($desc,ENT_QUOTES,'UTF-8');
          $itemsSan[]=['descripcion'=>$desc,'precio'=>$precio,'cantidad'=>$cantidad];
          $totalFactura+=floatval($precio)*intval($cantidad);
        }
      }
      $postedTotal=filter_var($_POST['total_factura'],FILTER_VALIDATE_FLOAT);
      if($postedTotal===false || abs($totalFactura-$postedTotal)>0.01){
        $pdo->rollBack();
        die('Total de factura inconsistente');
      }
      $sqlF="INSERT INTO facturas (id_cliente,fecha,total) VALUES (?,?,?)";
      $q=$pdo->prepare($sqlF);
      $q->execute(array($id_cliente,$_POST['fecha_factura'],$totalFactura));
      $idFactura=$pdo->lastInsertId();

      if(!empty($itemsSan)){
        $sqlD="INSERT INTO factura_detalle (id_factura,descripcion,precio,cantidad) VALUES (?,?,?,?)";
        $q=$pdo->prepare($sqlD);
        foreach($itemsSan as $item){
          $q->execute(array($idFactura,$item['descripcion'],$item['precio'],$item['cantidad']));
        }
      }
      if($monto_pesos===null || $monto_pesos===false){
        $monto_pesos=$totalFactura;
      }
    }

    $monto_dolares=0;
    if($cotizacion_usd>0 && $monto_pesos!==null && $monto_pesos!==false){
      $monto_dolares=$monto_pesos/$cotizacion_usd;
    }

    $detalle=htmlspecialchars($_POST['detalle'],ENT_QUOTES,'UTF-8');
    if (trim($detalle) === '' && !$idFactura) {
      $pdo->rollBack();
      error('Debe generar una factura o completar el detalle');
    }
    $sql = "INSERT INTO cobros (id_cliente,fecha_factura,fecha_cobro,monto_pesos,cotizacion_usd,monto_dolares,detalle,id_usuario,id_factura) VALUES (?,?,?,?,?,?,?,?,?)";
    $q = $pdo->prepare($sql);
    $q->execute(array($id_cliente,$_POST['fecha_factura'],$fecha_cobro,$monto_pesos,$cotizacion_usd,$monto_dolares,$detalle,$_SESSION["user"]['id'],$idFactura));

    $pdo->commit();

    Database::disconnect();

    if($idFactura){
      require_once 'informarFacturaARCA.php';
      $resultadoArca = informarFacturaARCA($idFactura);
      if(stripos($resultadoArca,'Error') !== false){
        echo "Error en la comunicación con ARCA: $resultadoArca";
        exit;
      }
    }

    header("Location: listarCobros.php");
  }catch(Exception $e){
    if($pdo->inTransaction()){
      $pdo->rollBack();
    }
    die('Error al procesar el cobro: '.$e->getMessage());
  }
}
$hoy=date("Y-m-d")?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head_forms.php');?>
    <link rel="stylesheet" type="text/css" href="assets/css/select2.css">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap-select-1.13.14/dist/css/bootstrap-select.min.css">
    <style>
      .multiselect{
        color:#212529 !important;
        background-color:#fff;
        border-color:#ccc;
      }
    </style>
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
                      <li class="breadcrumb-item">Nuevo Cobro</li>
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
                    <h5>Nuevo Cobro</h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="nuevoCobro.php">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Cliente</label>
                            <div class="col-sm-3">
                              <select name="id_cliente" class="form-control form-control-sm filtraTabla selectpicker multiselect w-100" data-live-search="true" required>
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $sql = "SELECT c.id AS id_cliente,c.razon_social,c.pide_factura FROM clientes c";
                                foreach ($pdo->query($sql) as $row) {?>
                                  <option value="<?=$row["id_cliente"]?>" data-pide-factura="<?=$row["pide_factura"]?>"><?=$row["razon_social"]?></option><?php
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                            <label class="col-sm-3 col-form-label">Fecha factura</label>
                            <div class="col-sm-3"><input name="fecha_factura" type="date" class="form-control" value="<?=$hoy?>"></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Generar factura</label>
                            <div class="col-sm-9"><input type="checkbox" name="generar_factura" id="generar_factura"></div>
                          </div>
                          <div id="factura_section">
                            <table class="table table-sm">
                              <thead>
                                <tr><th>Descripción</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th><th></th></tr>
                              </thead>
                              <tbody id="detalles"></tbody>
                              <tfoot>
                                <tr>
                                  <td colspan="4" class="text-right"><strong>Monto factura:</strong></td>
                                  <td><input name="monto_pesos" type="number" class="form-control" value="" required></td>
                                </tr>
                              </tfoot>
                            </table>
                            <button type="button" class="btn btn-secondary btn-sm" id="add_item">Agregar ítem</button>
                            <input type="hidden" name="total_factura" id="total_factura" value="0">
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Detalle</label>
                            <div class="col-sm-9"><input name="detalle" type="text" maxlength="99" class="form-control" value=""></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Fecha de cobro</label>
                            <div class="col-sm-3"><input name="fecha_cobro" type="date" class="form-control"></div>
                            <label class="col-sm-3 col-form-label">Cotizacion USD</label>
                            <div class="col-sm-3"><input name="cotizacion_usd" type="number" maxlength="99" class="form-control" value=""></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Crear</button>
						            <a href="listarCobros.php" class="btn btn-light">Volver</a>
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
    <script src="assets/js/chat-menu.js"></script>
    <script src="assets/js/tooltip-init.js"></script>
    <script src="vendor/bootstrap-select-1.13.14/dist/js/bootstrap-select.js"></script>
    <script src="vendor/bootstrap-select-1.13.14/js/i18n/defaults-es_ES.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="assets/js/script.js"></script>
    <!-- Plugin used-->
	  <script src="assets/js/select2/select2.full.min.js"></script>
      <script src="assets/js/select2/select2-custom.js"></script>
      <script>
        $(function(){
          function formatNumber(num){
            return num.toLocaleString('es-AR',{minimumFractionDigits:0,maximumFractionDigits:0});
          }
          function addRow(){
            var row='<tr>'+
              '<td><input type="text" name="items[][descripcion]" class="form-control" required></td>'+
              '<td><input type="number" step="0.01" name="items[][precio]" class="form-control precio" required></td>'+
              '<td><input type="number" name="items[][cantidad]" class="form-control cantidad" value="1" required></td>'+
              '<td class="subtotal">0</td>'+
              '<td><button type="button" class="btn btn-danger btn-sm remove">X</button></td>'+
              '</tr>';
            $('#detalles').append(row);
            calcTotal();
          }
          function calcTotal(){
            var total=0;
            $('#detalles tr').each(function(){
              var p=parseFloat($(this).find('.precio').val())||0;
              var c=parseFloat($(this).find('.cantidad').val())||0;
              var st=p*c;
              total+=st;
              $(this).find('.subtotal').text(formatNumber(st));
            });
            $('#total_factura').val(total.toFixed(2));
            if($('input[name="monto_pesos"]').prop('disabled')){
              $('input[name="monto_pesos"]').val(formatNumber(total));
            }
          }
          $('#generar_factura').on('change',function(){
            if($(this).is(':checked')){
              if($('#detalles tr').length===0){addRow();}
              calcTotal();
              $('input[name="monto_pesos"]').prop('disabled',true);
              $('input[name="detalle"]').val('').closest('.form-group').hide();
            }else{
              $('input[name="monto_pesos"]').prop('disabled',false);
              $('input[name="detalle"]').closest('.form-group').show();
            }
          });
          $('#add_item').on('click',function(){
            addRow();
          });
          $('#detalles').on('click','.remove',function(){
            $(this).closest('tr').remove();
            calcTotal();
          });
          $('#detalles').on('input','.precio, .cantidad',function(){
            calcTotal();
          });
          $('select[name="id_cliente"]').on('changed.bs.select', function(){
            var pideFactura = $('option:selected', this).data('pide-factura');
            $('#generar_factura').prop('checked', pideFactura==1).trigger('change');
          });
          $('form.theme-form').on('submit',function(){
            $('#detalles tr').each(function(){
              if($(this).find('input[name$="[precio]"]').length===0){
                $('<input>').attr({type:'hidden',name:'items[][precio]',value:'0'}).appendTo($(this));
              }
              if($(this).find('input[name$="[cantidad]"]').length===0){
                $('<input>').attr({type:'hidden',name:'items[][cantidad]',value:'1'}).appendTo($(this));
              }
            });
          });
        });
  </script>
  </body>
  </html>
