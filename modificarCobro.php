<?php
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';
if (!empty($_GET['id'])) {
  $id = $_GET['id'];
}
if ( null==$id ) {
  header("Location: listarCobros.php");
}

$items=array();
$totalFactura=0;
$bloquearEdicionFactura=false;
$attrDisabled='';
$bloqueoFacturaJs='false';

if ( !empty($_POST)) {

  // insert data
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  try{
    // obtener estado actual del cobro/factura
    $sqlAct = "SELECT id_factura, id_cliente, id_emisor, fecha_factura, monto_pesos, detalle FROM cobros WHERE id = ?";
    $qAct = $pdo->prepare($sqlAct);
    $qAct->execute(array($id));
    $rowAct = $qAct->fetch(PDO::FETCH_ASSOC);
    $idFacturaActual = $rowAct ? $rowAct['id_factura'] : null;

    $cotizacion_usd=filter_var($_POST['cotizacion_usd'],FILTER_VALIDATE_FLOAT);
    $id_cliente=filter_var($_POST['id_cliente'],FILTER_VALIDATE_INT);
    $fecha_cobro=$_POST['fecha_cobro'];
    if($fecha_cobro==""){
      $fecha_cobro=null;
    }
    $generarFactura = isset($_POST['generar_factura']);
    $id_emisor = !empty($_POST['id_emisor']) ? filter_var($_POST['id_emisor'],FILTER_VALIDATE_INT) : null;

    $pdo->beginTransaction();
    $idFactura=null;

    // Si el cobro ya tiene factura, no se permite modificar datos de factura ni del cobro ligado a facturacion.
    // Solo se permite actualizar fecha de cobro y cotizacion USD.
    if($idFacturaActual){
      $montoPesosActual = isset($rowAct['monto_pesos']) ? (float)$rowAct['monto_pesos'] : 0;
      $monto_dolares=0;
      if($cotizacion_usd>0){
        $monto_dolares=$montoPesosActual/$cotizacion_usd;
      }

      $sql = "UPDATE cobros set fecha_cobro = ?, cotizacion_usd = ?, monto_dolares = ? where id = ?";
      $q = $pdo->prepare($sql);
      $q->execute(array($fecha_cobro,$cotizacion_usd,$monto_dolares,$id));

      $pdo->commit();
      Database::disconnect();
      header("Location: listarCobros.php");
      exit;
    }

    // Procesar ítems siempre
    $totalFactura=0;
    $itemsSan=array();
    if(isset($_POST['items']) && is_array($_POST['items'])){
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

    if(empty($itemsSan)){
      $pdo->rollBack();
      die('Debe agregar al menos un ítem');
    }

    if($generarFactura){
      if($id_emisor===false || $id_emisor===null){
        $pdo->rollBack();
        die('Debe seleccionar un emisor para generar factura');
      }
      if(empty($_POST['fecha_factura'])){
        $pdo->rollBack();
        die('Debe indicar la fecha de factura');
      }
      $postedTotal=filter_var($_POST['total_factura'],FILTER_VALIDATE_FLOAT);
      if($postedTotal===false || abs($totalFactura-$postedTotal)>0.01){
        $pdo->rollBack();
        die('Total de factura inconsistente');
      }
      if($idFacturaActual){
        $sqlF="UPDATE facturas SET id_cliente = ?, fecha_cbte = ?, imp_total = ? WHERE id = ?";
        $qF=$pdo->prepare($sqlF);
        $qF->execute(array($id_cliente,$_POST['fecha_factura'],$totalFactura,$idFacturaActual));
        $idFactura=$idFacturaActual;
        $pdo->prepare("DELETE FROM factura_detalle WHERE id_factura = ?")->execute(array($idFactura));
      }else{
        $sqlF="INSERT INTO facturas (id_cliente,fecha_cbte,imp_total) VALUES (?,?,?)";
        $qF=$pdo->prepare($sqlF);
        $qF->execute(array($id_cliente,$_POST['fecha_factura'],$totalFactura));
        $idFactura=$pdo->lastInsertId();
      }
      if(!empty($itemsSan)){
        $sqlD="INSERT INTO factura_detalle (id_factura,descripcion,precio,cantidad) VALUES (?,?,?,?)";
        $qD=$pdo->prepare($sqlD);
        foreach($itemsSan as $it){
          $qD->execute(array($idFactura,$it['descripcion'],$it['precio'],$it['cantidad']));
        }
      }
    }else{
      if($idFacturaActual){
        $pdo->prepare("DELETE FROM factura_detalle WHERE id_factura = ?")->execute(array($idFacturaActual));
        $pdo->prepare("DELETE FROM facturas WHERE id = ?")->execute(array($idFacturaActual));
      }
    }

    $monto_pesos=$totalFactura;
    $monto_dolares=0;
    if($cotizacion_usd>0 && $monto_pesos!==null && $monto_pesos!==false){
      $monto_dolares=$monto_pesos/$cotizacion_usd;
    }

    $detalle = isset($itemsSan[0]['descripcion']) ? $itemsSan[0]['descripcion'] : '';
    $sql = "UPDATE cobros set id_cliente = ?, id_emisor = ?, fecha_factura = ?, fecha_cobro = ?, monto_pesos = ?, cotizacion_usd = ?, monto_dolares = ?, detalle = ?, id_factura = ? where id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($id_cliente,$id_emisor,$_POST['fecha_factura'],$fecha_cobro,$monto_pesos,$cotizacion_usd,$monto_dolares,$detalle,$idFactura,$id));

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

} else {

  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $sql = "SELECT co.id AS id_cobro,co.fecha_factura,co.fecha_cobro,co.detalle,co.monto_pesos,co.cotizacion_usd,co.id_cliente,co.id_emisor,co.id_factura FROM cobros co WHERE co.id = ? ";
  $q = $pdo->prepare($sql);
  $q->execute(array($id));
  $data = $q->fetch(PDO::FETCH_ASSOC);

  $items=array();
  $totalFactura=0;
  if($data && $data['id_factura']){
    $sqlDet="SELECT descripcion,precio,cantidad FROM factura_detalle WHERE id_factura = ?";
    $qDet=$pdo->prepare($sqlDet);
    $qDet->execute(array($data['id_factura']));
    while($rowDet=$qDet->fetch(PDO::FETCH_ASSOC)){
      $items[]=$rowDet;
      $totalFactura+=floatval($rowDet['precio'])*intval($rowDet['cantidad']);
    }
  }elseif($data){
    $descBase=trim((string)$data['detalle']);
    if($descBase===''){
      $descBase='Cobro';
    }
    $precioBase=floatval($data['monto_pesos']);
    if($precioBase<0){
      $precioBase=0;
    }
    $items[] = array('descripcion'=>$descBase,'precio'=>$precioBase,'cantidad'=>1);
    $totalFactura=$precioBase;
  }

  $bloquearEdicionFactura = ($data && !empty($data['id_factura']));
  if($bloquearEdicionFactura){
    $attrDisabled='disabled';
    $bloqueoFacturaJs='true';
  }

  Database::disconnect();
}?>
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
      .cobros-table{
        table-layout:fixed;
      }
      .cobros-table thead th{
        text-align:center;
        vertical-align:middle;
      }
      .cobros-table th:nth-child(5),
      .cobros-table td:nth-child(5),
      .cobros-table .acciones-cell{
        text-align:center;
        vertical-align:middle;
      }
      .cobros-table col.desc-col{ width:40%; }
      .cobros-table col.precio-col{ width:18%; }
      .cobros-table col.cantidad-col{ width:10%; }
      .cobros-table col.subtotal-col{ width:18%; }
      .cobros-table col.acciones-col{ width:14%; }
      .subtotal-input{
        min-height: calc(1.5em + 0.75rem + 2px);
      }
      .monto-factura-label{
        font-weight: 700;
        margin-bottom: 0;
      }
      .monto-factura-preview{
        min-height: calc(1.5em + 0.95rem + 2px);
        font-size: 1rem;
      }
      .item-trash-icon{
        width: 1rem;
        height: 1rem;
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
                      <li class="breadcrumb-item">Modificar Cobro</li>
                    </ol>
                  </div>
                </div>
                <!-- Bookmark Start-->
                <div class="col-2">
                  <div class="bookmark pull-right">
                    <ul>
                      <li><a  target="_blank" data-container="body" data-toggle="popover" data-placement="top" title="" data-original-title="<?=date('d-m-Y');?>"><i data-feather="calendar"></i></a></li>
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
                    <h5>Modificar Cobro</h5>
                  </div><?php
                  if(!empty($bloquearEdicionFactura)){ ?>
                    <div class="alert alert-warning m-3 mb-0">
                      Este cobro ya tiene factura generada. Solo se puede modificar la fecha de cobro y la cotizacion USD.
                    </div><?php
                  }?>
				          <form class="form theme-form" role="form" method="post" action="modificarCobro.php?id=<?=$id?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Cliente</label>
                            <div class="col-sm-3">
                              <select name="id_cliente" class="form-control form-control-sm filtraTabla selectpicker multiselect w-100" data-live-search="true" required <?=$attrDisabled; ?>>
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $sql = "SELECT c.id AS id_cliente,c.nombre,c.pide_factura FROM clientes c WHERE c.activo = 1";
                                foreach ($pdo->query($sql) as $row) {
                                  $selected="";
                                  if($data["id_cliente"]==$row["id_cliente"]){
                                    $selected=" selected";
                                  }?>
                                  <option value="<?=$row["id_cliente"]?>" data-pide-factura="<?=$row["pide_factura"]?>" <?=$selected?>><?=$row["nombre"]?></option><?php
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                            <label class="col-sm-3 col-form-label">Fecha factura</label>
                            <div class="col-sm-3"><input name="fecha_factura" type="date" class="form-control" value="<?=$data["fecha_factura"]?>" <?=$attrDisabled; ?>></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Emisor</label>
                            <div class="col-sm-9">
                              <select name="id_emisor" id="id_emisor" class="form-control form-control-sm filtraTabla selectpicker multiselect w-100" data-live-search="true" <?=$attrDisabled; ?>>
                                <option value="">Sin emisor</option><?php
                                $pdo = Database::connect();
                                $sql = "SELECT id, razon_social, cuit FROM emisores WHERE activo = 1 ORDER BY razon_social";
                                foreach ($pdo->query($sql) as $row) {
                                  $selected = "";
                                  if($data["id_emisor"] == $row["id"]){
                                    $selected = " selected";
                                  }?>
                                  <option value="<?=$row["id"]?>"<?=$selected?>><?=$row["razon_social"]?> (<?=$row["cuit"]?>)</option><?php
                                }
                                Database::disconnect();?>
                              </select>
                              <small id="emisor_help" class="text-danger" style="display:none;">Debe seleccionar un emisor para generar factura.</small>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="generar_factura">Generar factura</label>
                            <div class="col-sm-9"><input type="checkbox" name="generar_factura" id="generar_factura" <?php if($data['id_factura']) echo 'checked'; ?> <?=$attrDisabled; ?>></div>
                          </div>
                          <div id="factura_section">
                            <table class="table cobros-table">
                              <colgroup>
                                <col class="desc-col">
                                <col class="precio-col">
                                <col class="cantidad-col">
                                <col class="subtotal-col">
                                <col class="acciones-col">
                              </colgroup>
                              <thead>
                                <tr>
                                  <th>Descripción</th>
                                  <th>Precio</th>
                                  <th>Cantidad</th>
                                  <th>Subtotal</th>
                                  <th>Acciones</th>
                                </tr>
                              </thead>
                              <tbody id="detalles"><?php
                                foreach($items as $i => $it){ ?>
                                  <tr data-index="<?= $i ?>">
                                    <td><input type="text" name="items[<?= $i ?>][descripcion]" class="form-control" value="<?= $it['descripcion'] ?>" required <?=$attrDisabled; ?>></td>
                                    <td><input type="number" step="100" name="items[<?= $i ?>][precio]" class="form-control precio" value="<?= $it['precio'] ?>" required <?=$attrDisabled; ?>></td>
                                    <td><input type="number" name="items[<?= $i ?>][cantidad]" class="form-control cantidad" value="<?= $it['cantidad'] ?>" required <?=$attrDisabled; ?>></td>
                                    <td><input type="text" class="form-control text-right subtotal subtotal-input" value="0" readonly tabindex="-1"></td>
                                    <td class="acciones-cell"><button type="button" class="btn btn-danger btn-sm remove" title="Eliminar fila" <?=$attrDisabled; ?>><i data-feather="trash-2" class="item-trash-icon"></i></button></td>
                                  </tr><?php
                                } ?>
                              </tbody>
                              <tfoot>
                                <tr id="monto_factura_preview_row">
                                  <td colspan="3" class="text-right align-middle">
                                    <label class="monto-factura-label">Monto factura</label>
                                    <input type="hidden" name="total_factura" id="total_factura" value="<?=$totalFactura?>">
                                  </td>
                                  <td><input id="monto_factura_preview" type="text" class="form-control monto-factura-preview text-right" value="" readonly tabindex="-1"></td>
                                  <td class="acciones-cell"><button type="button" class="btn btn-secondary btn-sm" id="add_item" <?=$attrDisabled; ?>>Agregar ítem</button></td>
                                </tr>
                              </tfoot>
                            </table>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Fecha de cobro</label>
                            <div class="col-sm-3"><input name="fecha_cobro" type="date" class="form-control" value="<?=$data["fecha_cobro"]?>"></div>
                            <label class="col-sm-3 col-form-label">Cotizacion USD</label>
                            <div class="col-sm-3"><input name="cotizacion_usd" type="number" maxlength="99" class="form-control" value="<?=$data["cotizacion_usd"]?>"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Modificar</button>
						            <a href='listarCobros.php' class="btn btn-light">Volver</a>
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
          const bloqueoFactura = <?=$bloqueoFacturaJs; ?>;
          let itemIndex = $('#detalles tr').length;
          function syncFacturaRules(){
            if(bloqueoFactura){
              return;
            }
            var facturaActiva = $('#generar_factura').is(':checked');
            $('#id_emisor').prop('required',facturaActiva);
            $('input[name="fecha_factura"]').prop('required',facturaActiva);
            $('#emisor_help').toggle(facturaActiva && !$('#id_emisor').val());
            $('input[name*="[descripcion]"], .precio').prop('required',true);
            $('#add_item').prop('disabled',!facturaActiva);
            $('input[name*="[cantidad]"]').prop('readonly',!facturaActiva);
            if(!facturaActiva){
              $('input[name*="[cantidad]"]').val(1);
            }
            $('.selectpicker').selectpicker('refresh');
          }
          function formatNumber(num){
            return num.toLocaleString('es-AR',{minimumFractionDigits:0,maximumFractionDigits:0});
          }
          function addRow(){
            var idx = itemIndex++;
            var facturaActiva = $('#generar_factura').is(':checked');
            var cantidadReadonly = facturaActiva ? '' : ' readonly';
            var row='<tr data-index="'+idx+'">'+
              '<td><input type="text" name="items['+idx+'][descripcion]" class="form-control" required></td>'+
              '<td><input type="number" step="0.01" name="items['+idx+'][precio]" class="form-control precio" required></td>'+
              '<td><input type="number" name="items['+idx+'][cantidad]" class="form-control cantidad" value="1" required'+cantidadReadonly+'></td>'+
              '<td><input type="text" class="form-control text-right subtotal subtotal-input" value="0" readonly tabindex="-1"></td>'+
              '<td class="acciones-cell"><button type="button" class="btn btn-danger btn-sm remove" title="Eliminar fila"><i data-feather="trash-2" class="item-trash-icon"></i></button></td>'+
              '</tr>';
            $('#detalles').append(row);
            if(typeof feather !== 'undefined'){
              feather.replace();
            }
            calcTotal();
          }
          function calcTotal(){
            var total=0;
            $('#detalles tr').each(function(){
              var p=parseFloat($(this).find('.precio').val())||0;
              var c=parseFloat($(this).find('.cantidad').val())||0;
              var st=p*c;
              total+=st;
              $(this).find('.subtotal-input').val(formatNumber(st));
            });
            $('#total_factura').val(total.toFixed(2));
            $('#monto_factura_preview').val(formatNumber(total));
          }
          $('#generar_factura').on('change',function(){
            if(bloqueoFactura){
              return;
            }
            if($(this).is(':checked')){
              if($('#detalles tr').length===0){addRow();}
              calcTotal();
              $('#detalles tr').find('.cantidad').each(function(){
                $(this).prop('readonly',false);
              });
            }else{
              $('#detalles tr').find('.cantidad').each(function(){
                $(this).val(1).prop('readonly',true);
              });
            }
            syncFacturaRules();
          });
          $('#id_emisor').on('changed.bs.select change',function(){
            syncFacturaRules();
          });
          $('#add_item').on('click',function(){
            if(bloqueoFactura){
              return;
            }
            addRow();
          });
          $('#detalles').on('click','.remove',function(){
            if(bloqueoFactura){
              return;
            }
            $(this).closest('tr').remove();
            calcTotal();
          });
          $('#detalles').on('input','.precio, .cantidad',function(){
            if(bloqueoFactura){
              return;
            }
            calcTotal();
          });
          $('select[name="id_cliente"]').on('changed.bs.select', function(){
            if(bloqueoFactura){
              return;
            }
            var pideFactura = $('option:selected', this).data('pide-factura');
            $('#generar_factura').prop('checked', pideFactura==1).trigger('change');
          });
          $('form.theme-form').on('submit',function(){
            if(!bloqueoFactura){
              var facturaActiva = $('#generar_factura').is(':checked');
              if(facturaActiva && !$('#id_emisor').val()){
                $('#emisor_help').show();
                alert('Debe seleccionar un emisor para generar factura');
                return false;
              }
              if(facturaActiva && $('#detalles tr').length===0){
                alert('Debe agregar al menos un ítem para generar factura');
                return false;
              }
              if($('#detalles tr').length===0){
                alert('Debe agregar al menos un ítem');
                return false;
              }
            }
            $('#detalles tr').each(function(){
              var idx=$(this).data('index');
              var cantidadInput=$(this).find('input[name="items['+idx+'][cantidad]"]');
              if(cantidadInput.length>0){
                cantidadInput.prop('readonly',false);
              }else{
                $('<input>').attr({type:'hidden',name:'items['+idx+'][cantidad]',value:'1'}).appendTo($(this));
              }
              if($(this).find('input[name="items['+idx+'][precio]"]').length===0){
                $('<input>').attr({type:'hidden',name:'items['+idx+'][precio]',value:'0'}).appendTo($(this));
              }
            });
          });
          syncFacturaRules();
          $('#generar_factura').trigger('change');
          calcTotal();
        });
      </script>
    </body>
  </html>