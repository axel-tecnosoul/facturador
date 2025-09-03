<?php 
session_start(); 
if(empty($_SESSION['user'])){
	header("Location: index.php");
	die("Redirecting to index.php"); 
}

//$desde=date("Y-m-d");
$desde="";
$filtroDesde="";
$filtroDesdeC="";
if(isset($_GET["d"]) and $_GET["d"]!=""){
  $desde=$_GET["d"];
  $filtroDesde=" AND DATE(v.fecha_hora)>='".$desde."'";
  $filtroDesdeC=" AND DATE(cj.fecha_hora)>='".$desde."'";
}
//$hasta=date("Y-m-d");
$dia=date("d");
$meses_descontar=1;
if($dia<=5){
  $meses_descontar=2;
}
$hasta=date("Y-m-t",strtotime(date("Y-m-01")." -$meses_descontar month"));
$filtroHasta="";
$filtroHastaC="";
if(isset($_GET["h"]) and $_GET["h"]!=""){
  $hasta=$_GET["h"];
}
$filtroHasta=" AND DATE(v.fecha_hora)<='".$hasta."'";
$filtroHastaC=" AND DATE(cj.fecha_hora)<='".$hasta."'";
$id_proveedor=0;
$filtroProveedor="";
if(isset($_GET["p"]) and $_GET["p"]!=0){
  $id_proveedor=$_GET["p"];
  $filtroProveedor=" AND pr.id=".$id_proveedor;
}
$id_almacen=0;
$filtroAlmacen="";
if(isset($_GET["a"]) and $_GET["a"]!=0){
  $id_almacen=$_GET["a"];
  $filtroAlmacen=" AND a.id=".$id_almacen;
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head_tables.php');?>
    <link rel="stylesheet" type="text/css" href="assets/css/select2.css">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap-select-1.13.14/dist/css/bootstrap-select.min.css">
    <style>
      .select2-container{
        border: 1px solid #ccc;
        border-radius: 5px;
      }
    </style>
  </head>
  <body class="light-only">
    <!-- page-wrapper Start-->
    <div class="page-wrapper">
      <!-- Page Header Start-->
      <?php include('header.php');?>
     
      <!-- Page Header Ends                              -->
      <!-- Page Body Start-->
      <div class="page-body-wrapper">
        <!-- Page Sidebar Start-->
        <?php include('menu.php');?>
        <!-- Page Sidebar Ends-->
        <!-- Right sidebar Start-->
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
                      <li class="breadcrumb-item">Pagos Pendientes</li>
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
              <!-- Zero Configuration  Starts-->
              <div class="col-sm-12">
                <div class="card">
                  <div class="card-header">
                    <h5>Pagos Pendientes
                      &nbsp;<a href="#" id="marcarVentasRendidas" class="disabled"><img src="img/dolar.png" width="24" height="25" border="0" alt="Marcar Ventas Rendidas" id="pagado-masivo" title="Marcar Ventas Rendidas"></a>
                      &nbsp;<a href="exportPagosPendientes.php"><img src="img/xls.png" width="24" height="25" border="0" alt="Exportar Pagos Pendientes" title="Exportar Pagos Pendientes"></a>
                      <!-- <div id="total_pagos_pendientes" class="mr-2 d-inline"></div> -->
                    </h5>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <table class="table">
                        <tr>
                          <td class="text-right border-0 p-1">Desde: </td>
                          <td class="border-0 p-1"><input type="date" name="desde" id="desde" value="<?=$desde?>" class="form-control form-control-sm filtraTabla"></td>
                          <!-- <td rowspan="2" style="vertical-align: middle;" class="text-right border-0 p-1">Proveedores:</td> -->
                          <td rowspan="2" style="vertical-align: middle;width:20%" class="border-0 p-1">
                            <label style="margin-left: .5rem;" for="id_proveedor">Proveedores:</label><br>
                            <select name="id_proveedor" id="id_proveedor" class="js-example-basic-single w-100">
                              <option value="0">- Seleccione -</option><?php
                              include 'database.php';
                              $pdo = Database::connect();
                              $whereAlmacen="";
                              if ($_SESSION['user']['id_perfil'] == 2) {
                                $whereAlmacen= " AND pr.id_almacen = ".$_SESSION['user']['id_almacen']; 
                              }
                              $sql3 = "SELECT pr.id, CONCAT(pr.apellido, ' ', pr.nombre) AS proveedor, pr.id_almacen
                              FROM proveedores pr 
                              WHERE EXISTS (
                                SELECT 1
                                FROM ventas_detalle vd 
                                INNER JOIN ventas v ON vd.id_venta = v.id 
                                INNER JOIN productos p ON vd.id_producto = p.id 
                                WHERE p.id_proveedor = pr.id AND v.anulada = 0 AND vd.pagado = 0 $whereAlmacen
                              )
                              OR EXISTS (
                                SELECT 1
                                FROM canjes_detalle cd 
                                INNER JOIN canjes c ON cd.id_canje = c.id 
                                INNER JOIN productos p ON cd.id_producto = p.id 
                                WHERE p.id_proveedor = pr.id AND c.anulado = 0 AND cd.pagado = 0 $whereAlmacen
                              )";
                              //echo $sql3;
                              foreach ($pdo->query($sql3) as $row) {
                                $selected="";
                                if($row["id"]==$id_proveedor){
                                  $selected="selected";
                                }?>
                                <option value="<?=$row["id"]?>" <?=$selected?> data-id-almacen="<?=$row["id_almacen"]?>"><?="(".$row["id"].") ".$row["proveedor"]?></option><?php
                              }
                              Database::disconnect();?>
                            </select>
                          </td><?php
                          if($_SESSION['user']['id_perfil']==1){?>
                            <!-- <td rowspan="2" style="vertical-align: middle;" class="text-right border-0 p-1">Almacen:</td> -->
                            <td rowspan="2" style="vertical-align: middle;width:20%" class="border-0 p-1">
                              <label style="margin-left: .5rem;" for="id_almacen">Almacen:</label><br>
                              <select name="id_almacen" id="id_almacen" class="js-example-basic-single w-100">
                                <option value="0">- Seleccione -</option><?php
                                //include 'database.php';
                                $pdo = Database::connect();
                                $whereAlmacen="";
                                /*if ($_SESSION['user']['id_perfil'] == 2) {
                                  $whereAlmacen= " AND v.id_almacen = ".$_SESSION['user']['id_almacen']; 
                                }*/
                                $sql = "SELECT id,almacen FROM almacenes WHERE activo=1 $whereAlmacen";
                                echo $sql;
                                foreach ($pdo->query($sql) as $row) {
                                  $selected="";
                                  if($row["id"]==$id_almacen){
                                    $selected="selected";
                                  }?>
                                  <option value="<?=$row["id"]?>" <?=$selected?>><?=$row["almacen"]?></option><?php
                                }
                                Database::disconnect();?>
                              </select>
                            </td><?php
                          }?>
                          <td class="text-right border-0 p-1">Total pendiente: </td>
                          <td class="border-0 p-1" id="total_pagos_pendientes"></td>
                        </tr>
                        <tr>
                          <td class="text-right border-0 p-1">Hasta: </td>
                          <td class="border-0 p-1"><input type="date" name="hasta" id="hasta" value="<?=$hasta?>" class="form-control form-control-sm filtraTabla"></td>
                          <td class="text-right border-0 p-1">Total seleccionado: </td>
                          <td class="border-0 p-1" id="total_pagos_seleccionados"></td>
                        </tr>
                      </table>
                    </div>
                    <div class="dt-ext table-responsive">
                      <table class="display" id="dataTables-example666">
                        <thead>
                          <tr>
                            <th><input type="checkbox" id="seleccionar_todo"></th>
                            <!-- <th>ID</th>
                            <th>Proveedor</th> -->
                            <!-- <th>Almacen</th>
                            <th>Fecha/Hora</th>
                            <th>Código</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Cantidad</th>
                            <th>Forma de Pago</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                            <th>Deuda</th> -->
                            <th>ID</th>
                            <th>Fecha/Hora</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                            <th>Deuda</th>
                            <th>Cantidad</th>
                            <th class="d-none">Forma de Pago</th>
                            <th class="d-none">Almacen</th>
                            <th class="d-none">Código</th>
                            <th class="d-none">Categoría</th>
                            <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody><?php
                          $pdo = Database::connect();
                          //$sql = " SELECT vd.id AS id_detalle_venta, a.almacen, date_format(v.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora, p.codigo, c.categoria, p.descripcion, vd.cantidad, vd.precio, vd.subtotal, m.modalidad, vd.pagado, pr.nombre, pr.apellido, v.id_forma_pago, fp.forma_pago, v.id AS id_venta,vd.deuda_proveedor FROM ventas_detalle vd inner join ventas v on v.id = vd.id_venta inner join almacenes a on a.id = v.id_almacen inner join productos p on p.id = vd.id_producto inner join categorias c on c.id = p.id_categoria inner join modalidades m on m.id = vd.id_modalidad inner join proveedores pr on pr.id = p.id_proveedor inner join forma_pago fp on fp.id = v.id_forma_pago WHERE v.anulada = 0 and vd.id_modalidad = 40 and vd.pagado = 0 AND v.id_venta_cbte_relacionado IS NULL $filtroDesde $filtroHasta $filtroProveedor $filtroAlmacen";
                          $sql = " SELECT vd.id AS id_detalle_venta, a.almacen, date_format(v.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora, p.codigo, c.categoria, p.descripcion, vd.cantidad, vd.precio, vd.subtotal, m.modalidad, vd.pagado, pr.nombre, pr.apellido, v.id_forma_pago, fp.forma_pago, v.id AS id_venta,vd.deuda_proveedor FROM ventas_detalle vd inner join ventas v on v.id = vd.id_venta inner join productos p on p.id = vd.id_producto inner join categorias c on c.id = p.id_categoria inner join modalidades m on m.id = vd.id_modalidad inner join proveedores pr on pr.id = p.id_proveedor inner join almacenes a on a.id = pr.id_almacen inner join forma_pago fp on fp.id = v.id_forma_pago LEFT JOIN devoluciones_detalle de ON de.id_venta_detalle=vd.id WHERE v.anulada = 0 and vd.id_modalidad = 40 and vd.pagado = 0 AND v.id_venta_cbte_relacionado IS NULL AND de.id_devolucion IS NULL $filtroDesde $filtroHasta $filtroProveedor $filtroAlmacen";
                          //echo $sql;
                          if ($_SESSION['user']['id_perfil'] == 2) {
                            $sql .= " and a.id = ".$_SESSION['user']['id_almacen']; 
                          }
                          //echo $sql;
                          $total_deuda=0;
                          foreach ($pdo->query($sql) as $row) {
                            echo '<tr>';                          
                            echo '<td><input type="checkbox" class="pago_pendiente no-sort customer-selector" value="'. 'v/' . $row["id_detalle_venta"].'" /> </td>'; 
                            echo '<td>V#'. $row["id_venta"] . '</td>';
                            /*echo '<td>'. $row["id_detalle_venta"] . '</td>';
                            echo '<td>'. $row["nombre"] . ' ' . $row["apellido"] . '</td>';*/
                            echo '<td>'. $row["fecha_hora"] . 'hs</td>';
                            echo '<td>'. $row["descripcion"] . '</td>';
                            echo '<td>$'. number_format($row["precio"],2) . '</td>';
                            echo '<td>$'. number_format($row["subtotal"],2) . '</td>';
                            /*$subtotal = $row["subtotal"];
                            $modalidad = 0.4;
                            $deuda = 0;
                            if ($row["id_forma_pago"] == 1) {
                              $fp = 1;
                            } else {
                              $fp = 0.85;
                            }
                            $deuda = $subtotal*$modalidad*$fp;*/
                            $deuda = $row["deuda_proveedor"];
                            $total_deuda+=$deuda;
                            echo '<td> $'. number_format($deuda,2).'<label class="d-none deuda">'.$deuda.'</label></td>';
                            echo '<td>'. $row["cantidad"] . '</td>';
                            echo '<td class="d-none">'. $row["forma_pago"] . '</td>';
                            echo '<td class="d-none">'. $row["almacen"] . '</td>';
                            echo '<td class="d-none">'. $row["codigo"] . '</td>';
                            echo '<td class="d-none">'. $row["categoria"] . '</td>';
                            echo '<td><a href="verVenta.php?id='.$row["id_venta"].'" target="_blank" class="badge"><img src="img/eye.png" width="30" border="0" alt="Ver Venta" title="Ver Venta"></a></td>';
                            echo '</tr>';
                            
                          }
                          $sql2 = "SELECT cd.id AS id_detalle_canje, a.almacen, date_format(cj.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora, p.codigo, c.categoria, p.descripcion, cd.cantidad, cd.precio, cd.subtotal, m.modalidad, cd.pagado, pr.nombre, pr.apellido, cd.id_forma_pago, fp.forma_pago, cj.id AS id_canje,cd.deuda_proveedor FROM canjes_detalle cd inner join canjes cj on cj.id = cd.id_canje inner join productos p on p.id = cd.id_producto inner join categorias c on c.id = p.id_categoria inner join modalidades m on m.id = cd.id_modalidad inner join proveedores pr on pr.id = p.id_proveedor inner join almacenes a on a.id = pr.id_almacen left join forma_pago fp on fp.id = cd.id_forma_pago LEFT JOIN devoluciones_detalle de ON de.id_canje_detalle=cd.id WHERE cj.anulado = 0 and cd.id_modalidad = 40 and cd.pagado = 0 AND de.id_devolucion IS NULL $filtroDesdeC $filtroHastaC $filtroProveedor $filtroAlmacen";
                          foreach($pdo->query($sql2) as $row2){
                            echo '<tr>';                          
                            echo '<td><input type="checkbox" class="pago_pendiente no-sort customer-selector" value="'.'c/'.$row2["id_detalle_canje"].'" /> </td>';
                            echo '<td>C#'. $row2["id_canje"] . '</td>';
                            echo '<td>'. $row2["fecha_hora"] . 'hs</td>';
                            echo '<td>'. $row2["descripcion"] . '</td>';
                            echo '<td>$'. number_format($row2["precio"],2) . '</td>';
                            echo '<td>$'. number_format($row2["subtotal"],2) . '</td>';
                            $deuda = $row2["deuda_proveedor"];
                            $total_deuda+=$deuda;
                            echo '<td> $'. number_format($deuda,2).'<label class="d-none deuda">'.$deuda.'</label></td>';
                            echo '<td>'. $row2["cantidad"] . '</td>';
                            echo '<td class="d-none">'. $row2["forma_pago"] . '</td>';
                            echo '<td class="d-none">'. $row2["almacen"] . '</td>';
                            echo '<td class="d-none">'. $row2["codigo"] . '</td>';
                            echo '<td class="d-none">'. $row2["categoria"] . '</td>';
                            echo '<td><a href="verCanje.php?id='.$row2["id_canje"].'" target="_blank"><img src="img/eye.png" width="30" border="0" alt="Ver Canje" title="Ver Canje"></a></td>';
                            echo '</tr>';
                          }
                          Database::disconnect();?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div><?php
              $inicio_sesion_admin=0;
              $style_modal="";
              if ($_SESSION['user']['id_perfil'] == 1) {
                $inicio_sesion_admin=1;
                $style_modal="max-width: 800px;";
              }?>

              <!-- MODAL CERRAR CAJA -->
              <div class="modal fade" id="modalElijaCaja" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document" style="<?=$style_modal?>">
                  <div class="modal-content">
                    <form method="post" action="">
                      <div class="modal-header">
                        <!-- <h5 class="modal-title" id="exampleModalLabel">Seleccione de que caja egresa el dinero</h5> -->
                        <h5 class="modal-title" id="exampleModalLabel"><?php
                        if ($inicio_sesion_admin == 1) {
                          echo "Complete la información con respecto al egreso del dinero";
                        }else{
                          echo "Confirmación";
                        }?></h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                      </div>
                      <div class="modal-body"><?php
                        if ($inicio_sesion_admin == 1) {?>
                          <div class="form-group row mb-0">
                            <div class="col-sm-3">Caja: </div>
                            <div class="col-sm-9">
                              <label class="d-block" for="edo-ani1">
                                <input class="radio_animated" value="Grande" required id="edo-ani1" type="radio" name="tipo_caja"><label for="edo-ani1">Grande</label>
                              </label>
                              <label class="d-block" for="edo-ani">
                                <input class="radio_animated" value="Chica" required id="edo-ani" type="radio" name="tipo_caja"><label for="edo-ani">Chica</label>
                              </label>
                            </div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-3">Almacen: </div>
                            <div class="col-sm-9">
                              <select name="id_almacen" style="width: 100%;" class="js-example-basic-single" required="required">
                                <option value="">Seleccione</option><?php
                                $pdo = Database::connect();
                                $sql = " SELECT id, almacen FROM almacenes";
                                foreach ($pdo->query($sql) as $row) {?>
                                  <option value="<?=$row["id"]?>"<?php //if($row["id"]==6) echo "selected"?>><?=$row["almacen"]?></option><?php
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-3">Forma de Pago: </div>
                            <div class="col-sm-9">
                              <select name="id_forma_pago" id="id_forma_pago" style="width: 100%;" class="js-example-basic-single" required="required">
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT `id`, `forma_pago` FROM `forma_pago` WHERE 1";
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  echo "<option value='".$fila['id']."'";
                                  echo ">".$fila['forma_pago']."</option>";
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div><?php
                        }else{?>
                          ¿Está seguro que desea pagar los pendientes?
                          <input type="hidden" name="id_almacen" value="<?=$_SESSION['user']['id_almacen']?>">
                          <!-- id_forma_pago = ! -> Efectivo -->
                          <input type="hidden" name="id_forma_pago" value="1">
                          <!-- tipo_caja = ! -> Chica -->
                          <input type="hidden" name="tipo_caja" value="Chica"><?php
                        }?>
                      </div>

                      <div class="modal-footer">
                        <!-- <a href="#" class="btn btn-primary">Pagar pendientes</a> -->
                        <button type="submit" class="btn btn-primary">Pagar pendientes</button>
                        <button data-dismiss="modal" class="btn btn-light">Volver</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              <!-- FIN MODAL CERRAR CAJA -->
              <!-- Zero Configuration  Ends-->
              <!-- Feature Unable /Disable Order Starts-->
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

    <script src="assets/js/select2/select2.full.min.js"></script>
    <script src="assets/js/select2/select2-custom.js"></script>
    <script src="vendor/bootstrap-select-1.13.14/dist/js/bootstrap-select.js"></script>
    <script src="vendor/bootstrap-select-1.13.14/js/i18n/defaults-es_ES.js"></script>
    <!-- Plugins JS start-->
    <script src="assets/js/datatable/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.buttons.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/jszip.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/buttons.colVis.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/pdfmake.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/vfs_fonts.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.autoFill.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.select.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/buttons.bootstrap4.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/buttons.html5.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/buttons.print.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.responsive.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/responsive.bootstrap4.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.keyTable.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.colReorder.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.fixedHeader.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.rowReorder.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.scroller.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/custom.js"></script>
    <script src="assets/js/chat-menu.js"></script>
    <script src="assets/js/tooltip-init.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="assets/js/script.js"></script>
    <script>

      function reloadPage(){
        let desde=$("#desde").val();
        let hasta=$("#hasta").val();
        let id_proveedor=$("#id_proveedor").val();
        let id_almacen=$("#id_almacen").val();
        window.location.href="listarPagosPendientes.php?d="+desde+"&h="+hasta+"&p="+id_proveedor+"&a="+id_almacen
      }

      function calcular_total_seleccionado(){
        let total_seleccionado=0;
        $(".pago_pendiente").each(function (index, element) {
          if(this.checked){
            total_seleccionado+=parseFloat($(this).parent().parent().find(".deuda").html());
          }
        });
        $("#total_pagos_seleccionados").html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total_seleccionado))
      }

      $(document).ready(function() {

        //$("#modalElijaCaja").modal("show")

        $("#total_pagos_pendientes").html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(<?=$total_deuda?>))

        var total=0;

        $("#desde").on("change",reloadPage)
        $("#hasta").on("change",reloadPage)
        $("#id_proveedor").on("change",reloadPage)
        $("#id_almacen").on("change",reloadPage)

        $(".pago_pendiente").on("click",function(){
          console.log(this);
          calcular_total_seleccionado()
        })

        $("#seleccionar_todo").on("click",function(){
          let seleccionar_todo=this
          $(".pago_pendiente").each(function (index, element) {
            console.log(this);
            if(seleccionar_todo.checked){
              console.log("seleccionar");
              this.checked=true;
            }else{
              console.log("deseleccionar");
              this.checked=false;
            }
            // element == this
          });
          calcular_total_seleccionado()
        })

        /* Formatting function for child row details */
        function format ( d ) {
          return `
            <b>Forma de pago: </b>${d[8]}<br>
            <b>Almacen: </b>${d[9]}<br>
            <b>Código: </b>${d[10]}<br>
            <b>Categoría: </b>${d[11]}<br>`;
        }

        $('#dataTables-example666').DataTable({
          stateSave: true,
          //dom: '<"#total_pagos_seleccionados.mr-2 d-inline"l>frtip',
          dom: 'lrtip',
          //responsive: true,
          paginate: false,
          scrollY: '100vh',
          scrollCollapse: true,
          "columnDefs": [{
            "targets": [0],
            "searchable": false,
            "orderable": false,
          }],
          language: {
            "decimal": "",
            "emptyTable": "No hay información",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ Registros",
            "infoEmpty": "Mostrando 0 to 0 of 0 Registros",
            "infoFiltered": "(Filtrado de _MAX_ total registros)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ Registros",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No hay resultados",
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "Siguiente",
                "previous": "Anterior"
            }
          },
          initComplete: function(){
            $("#total_pagos_seleccionados").html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(0));
            $("#dataTables-example666_wrapper").find(".dataTables_scrollHead table thead th:first-child").removeClass("sorting_asc");
            $("#dataTables-example666_wrapper").find(".dataTables_scrollBody table tbody tr").each(function(index, element){
              $(element).find("td:first-child").each(function(){
                $(this).on("click",function(e){
                  //e.preventDefault();
                  let tr=$(this).parent();
                  var row = $("#dataTables-example666").DataTable().row(tr);
                  if ( row.child.isShown() ) {
                    // Open this row
                    row.child( format(row.data()) ).show();
                    //row.child.show();
                    tr.addClass('shown');
                  }else {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                  }
                });
              });
              $(element).find("td").not(":first-child").each(function(){
                $(this).on("click",function(){
                  let tr=$(this).parent();
                  var row = $("#dataTables-example666").DataTable().row(tr);
                  if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                  }else {
                    // Open this row
                    row.child( format(row.data()) ).show();
                    //row.child.show();
                    tr.addClass('shown');
                  }
                });
              })
            })
          }

        });

        checkProveedor();
        /*$("#id_proveedor").on("change",function(){
          checkProveedor();
        })*/

        let id_proveedor=$("#id_proveedor");
        if(id_proveedor.val()>0){
          //checkProveedor();
          console.log(id_proveedor.val());
          id_almacen=id_proveedor.find("option[value='"+id_proveedor.val()+"']").data("idAlmacen");
          $("#modalElijaCaja").find("select[name='id_almacen']").val(id_almacen).trigger("change")
        }

      });

      function checkProveedor(){
        if ($("#id_proveedor").val()==0) {
          $("#marcarVentasRendidas").addClass("disabled")
        }else{
          $("#marcarVentasRendidas").removeClass("disabled")
        }
      }
      
      $('.customer-selector').on('click', function () {
        $('.toggle-checkboxes').prop('checked', false);
      });

      $('#pagado-masivo').on('click', function (e) {
        e.preventDefault();

        if ($("#id_proveedor").val()==0) {
          alert("Debe seleccionar un proveedor para continuar");
        } else {
          if ($('.customer-selector:checked').length < 1) {
            alert("Debe seleccionar una operación como mínimo");
          } else {
            var arr = [];
            $('.customer-selector:checked').each(function (i,o) { arr.push($(o).val()); });

            let modal=$("#modalElijaCaja")
            modal.modal("show")
            modal.find("form").attr("action","marcarVentasPagadas.php?id=" + arr.join(","))

            //window.location.href= window.location.href.replace("listarPagosPendientes.php", "marcarVentasPagadas.php?id=" + arr.join(",") );
          } 
        }
      });

      var toggle = true;
      $('.toggle-checkboxes').on('click', function (e) {
        e.preventDefault();
        $('.customer-selector').prop('checked', toggle);
        toggle = !toggle;
      })
		
		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
    <!-- Plugin used-->
  </body>
</html>