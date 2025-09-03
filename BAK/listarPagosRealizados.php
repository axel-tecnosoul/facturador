<?php 
session_start(); 
if(empty($_SESSION['user'])){
	header("Location: index.php");
	die("Redirecting to index.php"); 
}

//$desde=date("Y-m-d");
//$desde="";
/*$filtroDesde="";
if(isset($_GET["d"]) and $_GET["d"]!=""){
  $desde=$_GET["d"];
  //$filtroDesde=" AND DATE(vd.fecha_hora_pago)>='".$desde."'";
  $filtroDesde=" AND DATE(fecha_hora_pago)>='".$desde."'";
}*/
$desde=date("Y-m-d",strtotime(date("Y-m-d")." -1 month"));

//$filtroHasta="";
if(isset($_GET["d"]) and $_GET["d"]!=""){
  $desde=$_GET["d"];
}
//$filtroHasta=" AND DATE(vd.fecha_hora_pago)<='".$desde."'";
$filtroDesde=" AND DATE(fecha_hora_pago)>='".$desde."'";

$hasta=date("Y-m-d");
//$hasta=date("Y-m-t",strtotime(date("Y-m-d")." -1 month"));
//$filtroHasta="";
if(isset($_GET["h"]) and $_GET["h"]!=""){
  $hasta=$_GET["h"];
}
//$filtroHasta=" AND DATE(vd.fecha_hora_pago)<='".$hasta."'";
$filtroHasta=" AND DATE(fecha_hora_pago)<='".$hasta."'";

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
                      <li class="breadcrumb-item">Pagos Realizados</li>
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
                    <h5>Pagos Realizados
                      &nbsp;<a href="exportPagosRealizados.php"><img src="img/xls.png" width="24" height="25" border="0" alt="Exportar Pagos Realizados" title="Exportar Pagos Realizados"></a>
                      <!-- <div id="total_pagos_realizados" class="mr-2 d-inline"></div> -->
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
                              $sql = "SELECT pr.id,CONCAT(pr.apellido,' ',pr.nombre) AS proveedor FROM ventas_detalle vd INNER JOIN ventas v ON vd.id_venta=v.id INNER JOIN productos p ON vd.id_producto=p.id INNER JOIN proveedores pr ON p.id_proveedor=pr.id WHERE v.anulada=0 AND vd.pagado=1 $whereAlmacen GROUP BY pr.id";
                              //echo $sql;
                              foreach ($pdo->query($sql) as $row) {
                                $selected="";
                                if($row["id"]==$id_proveedor){
                                  $selected="selected";
                                }?>
                                <option value="<?=$row["id"]?>" <?=$selected?>><?="(".$row["id"].") ".$row["proveedor"]?></option><?php
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
                                //echo $sql;
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
                          <td rowspan="2" style="vertical-align: middle;" class="text-right border-0 p-1">Total pagado: </td>
                          <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1" id="total_pagos_realizados"></td>
                        </tr>
                        <tr>
                          <td class="text-right border-0 p-1">Hasta: </td>
                          <td class="border-0 p-1"><input type="date" name="hasta" id="hasta" value="<?=$hasta?>" class="form-control form-control-sm filtraTabla"></td>
                        </tr>
                      </table>
                    </div>
                    <div class="dt-ext table-responsive">
                      <table class="display" id="dataTables-example666">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Fecha/Hora</th>
                            <th>Descripción</th>
                            <th>Pagado</th>
                            <th>Caja</th>
                            <th>Forma de Pago</th>
                            <th>Almacen</th>
                            <th>Opciones</th>
                            <th class="d-none">Precio</th>
                            <th class="d-none">Subtotal</th>
                            <th class="d-none">Cantidad</th>
                            <th class="d-none">Código</th>
                            <th class="d-none">Categoría</th>
                          </tr>
                        </thead>
                        <tbody><?php
                          $pdo = Database::connect();
                          $sql = " SELECT v.id as id_venta,vd.id AS id_detalle_venta, a.almacen, p.codigo, c.categoria, p.descripcion, vd.cantidad, vd.precio, vd.subtotal, m.modalidad, vd.pagado, pr.nombre, pr.apellido, vd.id_forma_pago, fp.forma_pago, vd.id_venta,vd.deuda_proveedor,date_format(vd.fecha_hora_pago,'%d/%m/%Y %H:%i') AS fecha_hora_pago,caja_egreso,forma_pago FROM ventas_detalle vd INNER JOIN ventas v ON vd.id_venta=v.id inner join productos p on p.id = vd.id_producto inner join categorias c on c.id = p.id_categoria inner join modalidades m on m.id = vd.id_modalidad inner join proveedores pr on pr.id = p.id_proveedor LEFT join almacenes a on a.id = vd.id_almacen LEFT join forma_pago fp on fp.id = vd.id_forma_pago WHERE v.anulada = 0 and vd.id_modalidad = 40 and vd.pagado = 1 AND v.id_venta_cbte_relacionado IS NULL $filtroDesde $filtroHasta $filtroProveedor $filtroAlmacen";
                          if ($_SESSION['user']['id_perfil'] == 2) {
                            $sql .= " and a.id = ".$_SESSION['user']['id_almacen']; 
                          }
                          //echo $sql;
                          $total_deuda=0;
                          foreach ($pdo->query($sql) as $row) {
                            $deuda = $row["deuda_proveedor"];
                            $total_deuda+=$deuda;?>
                            <tr>
                              <td><?="V#".$row["id_venta"]?></td>
                              <td><?=$row["fecha_hora_pago"]?>hs</td>
                              <td><?=$row["descripcion"]?></td>
                              <td> $<?=number_format($deuda,2)?><label class="d-none deuda"><?=$deuda?></label></td>
                              <td><?=$row["caja_egreso"]?></td>
                              <td><?=$row["forma_pago"]?></td>
                              <td><?=$row["almacen"]?></td>
                              <td>
                                <a href="modificarPagoRealizado.php?id=v/<?=$row["id_detalle_venta"]?>">
                                  <img src="img/icon_modificar.png" width="24" height="25" border="0" alt="Modificar Pago" title="Modificar Pago">
                                </a>
                                <a href="verVenta.php?id=<?=$row["id_venta"]?>">
                                  <img src="img/eye.png" width="24" height="15" border="0" alt="Ver Venta" title="Ver Venta">
                                </a>
                              </td>
                              <td class="d-none">$<?=number_format($row["precio"],2)?></td>
                              <td class="d-none">$<?=number_format($row["subtotal"],2)?></td>
                              <td class="d-none"><?=$row["cantidad"]?></td>
                              <td class="d-none"><?=$row["codigo"]?></td>
                              <td class="d-none"><?=$row["categoria"]?></td>
                            </tr><?php
                          }
                          $sql2 ="SELECT cj.id AS id_canje, cd.id AS id_detalle_canje, a.almacen, p.codigo, c.categoria, p.descripcion, cd.cantidad, cd.precio, cd.subtotal, m.modalidad, cd.pagado, pr.nombre, pr.apellido, cd.id_forma_pago, fp.forma_pago, cd.id_canje,cd.deuda_proveedor,date_format(cd.fecha_hora_pago,'%d/%m/%Y %H:%i') AS fecha_hora_pago,caja_egreso,forma_pago FROM canjes_detalle cd INNER JOIN canjes cj ON cd.id_canje=cj.id inner join productos p on p.id = cd.id_producto inner join categorias c on c.id = p.id_categoria inner join modalidades m on m.id = cd.id_modalidad inner join proveedores pr on pr.id = p.id_proveedor LEFT join almacenes a on a.id = cd.id_almacen LEFT join forma_pago fp on fp.id = cd.id_forma_pago WHERE cj.anulado = 0 and cd.id_modalidad = 40 and cd.pagado = 1 $filtroDesde $filtroHasta $filtroProveedor $filtroAlmacen";
                          if ($_SESSION['user']['id_perfil'] == 2) {
                            $sql2 .= " and a.id = ".$_SESSION['user']['id_almacen']; 
                          }
                          foreach ($pdo->query($sql2) as $row) {
                            $deuda = $row["deuda_proveedor"];
                            $total_deuda+=$deuda;?>
                            <tr>
                              <td><?="C#".$row["id_canje"]?></td>
                              <td><?=$row["fecha_hora_pago"]?>hs</td>
                              <td><?=$row["descripcion"]?></td>
                              <td> $<?=number_format($deuda,2)?><label class="d-none deuda"><?=$deuda?></label></td>
                              <td><?=$row["caja_egreso"]?></td>
                              <td><?=$row["forma_pago"]?></td>
                              <td><?=$row["almacen"]?></td>
                              <td>
                                <a href="modificarPagoRealizado.php?id=c/<?=$row["id_detalle_canje"]?>">
                                  <img src="img/icon_modificar.png" width="24" height="25" border="0" alt="Modificar Pago" title="Modificar Pago">
                                </a>
                                <a href="verCanje.php?id=<?=$row["id_canje"]?>">
                                  <img src="img/eye.png" width="24" height="15" border="0" alt="Ver Canje" title="Ver Canje">
                                </a>
                              </td>
                              <td class="d-none">$<?=number_format($row["precio"],2)?></td>
                              <td class="d-none">$<?=number_format($row["subtotal"],2)?></td>
                              <td class="d-none"><?=$row["cantidad"]?></td>
                              <td class="d-none"><?=$row["codigo"]?></td>
                              <td class="d-none"><?=$row["categoria"]?></td>
                            </tr><?php
                          }
                          Database::disconnect();?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
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
        window.location.href="listarPagosRealizados.php?d="+desde+"&h="+hasta+"&p="+id_proveedor+"&a="+id_almacen
      }

      $(document).ready(function() {

        //$("#modalElijaCaja").modal("show")

        $("#total_pagos_realizados").html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(<?=$total_deuda?>))

        var total=0;

        $("#desde").on("change",reloadPage)
        $("#hasta").on("change",reloadPage)
        $("#id_proveedor").on("change",reloadPage)
        $("#id_almacen").on("change",reloadPage)

        /* Formatting function for child row details */
        function format ( d ) {
          console.log(d)
          return `
            <b>Precio: </b>${d[8]}<br>
            <b>Subtotal: </b>${d[9]}<br>
            <b>Cantidad: </b>${d[10]}<br>
            <b>Código: </b>${d[11]}<br>
            <b>Categoría: </b>${d[12]}<br>`;
        }

        $('#dataTables-example666').DataTable({
          stateSave: true,
          //dom: '<"#total_pagos_seleccionados.mr-2 d-inline"l>frtip',
          dom: 'lrtip',
          //responsive: true,
          paginate: false,
          scrollY: '100vh',
          scrollCollapse: true,
          /*"columnDefs": [{
            "targets": [0],
            "searchable": false,
            "orderable": false,
          }],*/
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
            //$("#dataTables-example666_wrapper").find(".dataTables_scrollHead table thead th:first-child").removeClass("sorting_asc");
            //$("#total_pagos_seleccionados").html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(0));
            $("#dataTables-example666_wrapper").find(".dataTables_scrollBody table tbody tr").each(function(index, element){
              $(element).find("td:first-child").each(function(){
                $(this).on("click",function(e){
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
              });
              $(element).find("td").not(":first-child").each(function(){
                $(this).on("click",function(){
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
              })
            })
          }

        });
      });
		
		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
    <!-- Plugin used-->
  </body>
</html>