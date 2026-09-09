<?php
require("config.php");
if (empty($_SESSION['user'])) {
  header('Location: index.php');
  exit;
}
require_once 'database.php';
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
  header('Location: listarCobros.php');
  exit;
}

$pdo = Database::connect();
$q = $pdo->prepare('SELECT co.*, c.nombre AS cliente, e.razon_social AS emisor, e.cuit AS emisor_cuit, f.homologacion, f.id AS factura_existente, f.estado AS estado_factura, f.cae, f.error_arca, f.intentos_arca FROM cobros co LEFT JOIN clientes c ON c.id=co.id_cliente LEFT JOIN emisores e ON e.id=co.id_emisor LEFT JOIN facturas f ON f.id=co.id_factura WHERE co.id=?');
$q->execute(array($id));
$data = $q->fetch(PDO::FETCH_ASSOC);

if (!$data) {
  http_response_code(404);
  exit('Cobro no encontrado');
}

$q = $pdo->prepare('SELECT * FROM facturas WHERE id_factura_asociada=?');
$q->execute(array($data['id_factura']));
$notaCredito = $q->fetch(PDO::FETCH_ASSOC);

$q = $pdo->prepare('SELECT descripcion,precio,cantidad FROM factura_detalle WHERE id_factura=? ORDER BY id');
$q->execute(array($data['id_factura']));
$items = $q->fetchAll(PDO::FETCH_ASSOC);

if (empty($data['id_factura'])) {
  $q = $pdo->prepare('SELECT descripcion,precio,cantidad FROM cobro_detalle WHERE id_cobro=? ORDER BY id');
  $q->execute(array($id));
  $items = $q->fetchAll(PDO::FETCH_ASSOC);
}

if(empty($items)){
  $items[] = array('descripcion'=>$data['detalle'],'precio'=>$data['monto_pesos'],'cantidad'=>1);
}

Database::disconnect();

$anulado = $notaCredito && $notaCredito['estado'] === 'A' && !empty($notaCredito['cae']);
$totalFactura=0;
foreach($items as $it){
  $totalFactura+=floatval($it['precio'])*floatval($it['cantidad']);
}
?>
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
                      <li class="breadcrumb-item">Ver Cobro</li>
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
                    <h5>Ver Cobro</h5>
                  </div><?php
                    if ((int)$data['homologacion'] === 1) { ?>
                      <div class="alert alert-warning m-3" style="border:3px solid #b45309;font-size:18px;" role="alert"><strong>HOMOLOGACI&Oacute;N - REGISTRO DE PRUEBA SIN VALIDEZ FISCAL</strong></div><?php
                    }
                    if (!empty($data['id_factura']) && ($data['estado_factura'] !== 'A' || empty($data['cae']))) {?>
                      <div class="alert alert-warning m-3" role="alert">
                        <strong>Factura #<?=(int)$data['id_factura']?> sin autorizaci&oacute;n registrada</strong><br><?php
                        if (empty($data['factura_existente'])) { ?>
                          No se encontr&oacute; la factura vinculada a este cobro.<?php
                        } else { ?>
                          El cobro conserva su v&iacute;nculo con la factura, pero no tiene un CAE registrado.<br>
                          Intentos registrados: <?=(int)$data['intentos_arca']?>.<br><?php
                          if ($data['error_arca']) { ?>
                            Error: <?=htmlspecialchars((string)($data['error_arca']), ENT_QUOTES, 'UTF-8')?><br><?php
                          } else { ?>
                            No hay un error guardado que permita determinar la causa.<br><?php
                          }
                          if ($data['homologacion'] === null) { ?>
                            Ambiente de ARCA no registrado.<br><?php
                          }
                          if (!$notaCredito) {?>
                            No tiene una nota de cr&eacute;dito asociada.<?php
                          }
                        } ?>
                      </div><?php
                    }
                    if ($notaCredito) { ?>
                      <div class="alert <?=$anulado ? 'alert-danger' : 'alert-warning'?> m-3">
                        <strong><?=$anulado ? 'COBRO ANULADO POR NOTA DE CREDITO' : 'NC pendiente de conciliacion'?></strong><br>
                        Motivo: <?=htmlspecialchars((string)($notaCredito['motivo_nc']), ENT_QUOTES, 'UTF-8')?><br>
                        NC: <span class="text-nowrap"><?=!empty($notaCredito['numero_comprobante']) ? sprintf('%04d-%08d', $notaCredito['punto_venta'], $notaCredito['numero_comprobante']) : 'Pendiente'?></span> | CAE: <?=htmlspecialchars((string)($notaCredito['cae'] ?: 'Pendiente'), ENT_QUOTES, 'UTF-8')?>
                      </div><?php
                    } ?>
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Cliente</label>
                            <div class="col-sm-3">
                              <select name="id_cliente" id="id_cliente" class="form-control form-control-sm selectpicker multiselect w-100" disabled>
                                <option selected><?=htmlspecialchars((string)($data['cliente'] ?: 'Cliente no encontrado'), ENT_QUOTES, 'UTF-8')?></option>
                              </select>
                            </div>
                            <label class="col-sm-3 col-form-label">Fecha factura</label>
                            <div class="col-sm-3"><input name="fecha_factura" type="date" class="form-control" value="<?=$data["fecha_factura"]?>" disabled></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Emisor</label>
                            <div class="col-sm-9">
                              <select name="id_emisor" id="id_emisor" class="form-control form-control-sm selectpicker multiselect w-100" disabled>
                                <option selected><?=htmlspecialchars((string)($data['emisor'] ? $data['emisor'].' ('.$data['emisor_cuit'].')' : 'Sin emisor'), ENT_QUOTES, 'UTF-8')?></option>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="generar_factura">Generar factura</label>
                            <div class="col-sm-9"><input type="checkbox" name="generar_factura" id="generar_factura" <?php if($data['id_factura']) echo 'checked'; ?> disabled></div>
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
                                  <th></th>
                                </tr>
                              </thead>
                              <tbody id="detalles"><?php
                                foreach($items as $i => $it){ ?>
                                  <tr data-index="<?= $i ?>">
                                    <td><input type="text" name="items[<?= $i ?>][descripcion]" class="form-control" value="<?=htmlspecialchars(html_entity_decode((string)$it['descripcion'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8')?>" disabled></td>
                                    <td><input type="number" step="100" name="items[<?= $i ?>][precio]" class="form-control precio" value="<?= $it['precio'] ?>" disabled></td>
                                    <td><input type="number" name="items[<?= $i ?>][cantidad]" class="form-control cantidad" value="<?= $it['cantidad'] ?>" disabled></td>
                                    <td><input type="text" class="form-control text-right subtotal subtotal-input" value="<?=number_format(floatval($it['precio'])*floatval($it['cantidad']), 2, ',', '.')?>" readonly tabindex="-1"></td>
                                    <td class="acciones-cell"></td>
                                  </tr><?php
                                } ?>
                              </tbody>
                              <tfoot>
                                <tr id="monto_factura_preview_row">
                                  <td colspan="3" class="text-right align-middle">
                                    <label class="monto-factura-label">Monto factura</label>
                                  </td>
                                  <td><input id="monto_factura_preview" type="text" class="form-control monto-factura-preview text-right" value="<?=number_format($totalFactura, 2, ',', '.')?>" readonly tabindex="-1"></td>
                                  <td class="acciones-cell"></td>
                                </tr>
                              </tfoot>
                            </table>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Fecha de cobro</label>
                            <div class="col-sm-3"><input name="fecha_cobro" type="date" class="form-control" value="<?=$data["fecha_cobro"]?>" disabled></div>
                            <label class="col-sm-3 col-form-label">Cotizacion USD</label>
                            <div class="col-sm-3"><input name="cotizacion_usd" type="number" maxlength="99" class="form-control" value="<?=$data["cotizacion_usd"]?>" disabled></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3"><?php
                        if ($data['estado_factura'] === 'A' && !empty($data['cae'])) { ?>
                          <a class="btn btn-primary" target="_blank" rel="noopener" href="Facturas/facturaCobro.php?id=<?=(int)$data['id_factura']?>">Ver PDF de factura</a><?php
                        }
                        if ($anulado) { ?>
                          <a class="btn btn-danger" target="_blank" rel="noopener" href="Facturas/facturaCobro.php?id=<?=(int)$notaCredito['id']?>">Ver PDF de NC</a><?php
                        } ?>
                        <a href="listarCobros.php" class="btn btn-light">Volver</a>
                      </div>
                    </div>
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
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/bootstrap/popper.min.js"></script>
    <script src="assets/js/bootstrap/bootstrap.js"></script>
    <script src="assets/js/icons/feather-icon/feather.min.js"></script>
    <script src="assets/js/icons/feather-icon/feather-icon.js"></script>
    <script src="assets/js/sidebar-menu.js"></script>
    <script src="assets/js/config.js"></script>
    <script src="vendor/bootstrap-select-1.13.14/dist/js/bootstrap-select.js"></script>
    <script src="vendor/bootstrap-select-1.13.14/js/i18n/defaults-es_ES.js"></script>
    <script src="assets/js/script.js"></script>
  </body>
</html>
