<?php 
session_start(); 
if(empty($_SESSION['user'])){
	header("Location: index.php");
	die("Redirecting to index.php"); 
}

//$desde=date("Y-m-d");
$desde="";
$filtroDesde="";
if(isset($_GET["d"]) and $_GET["d"]!=""){
  $desde=$_GET["d"];
  $filtroDesde=" AND DATE(v.fecha_hora)>='".$desde."'";
}
//$hasta=date("Y-m-d");
$hasta=date("Y-m-t",strtotime(date("Y-m-d")." -1 month"));
$filtroHasta="";
if(isset($_GET["h"]) and $_GET["h"]!=""){
  $hasta=$_GET["h"];
}
$filtroHasta=" AND DATE(v.fecha_hora)<='".$hasta."'";
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
}?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head_tables.php');?>
  </head>
  <?php //include('header.php');?>

  <div class="dt-ext table-responsive">
    <table class="display" id="dataTables-example666">
      <thead>
        <tr>
          <th style="text-align:center">ID</th>
          <th style="text-align:center">Proveedora</th>
          <th style="text-align:center">Cantidad prendas</th>
          <th style="text-align:center">Venta total</th>
          <th style="text-align:center">Deuda total proveedora</th>
        </tr>
      </thead>
      <tbody><?php
        include_once("database.php");
        $pdo = Database::connect();
        $sql = "SELECT pr.id,pr.apellido,pr.nombre,COUNT(*) AS cant_prendas,SUM(vd.subtotal) AS venta_total,SUM(vd.deuda_proveedor) AS deuda_total FROM ventas_detalle vd INNER JOIN productos p ON vd.id_producto=p.id LEFT JOIN proveedores pr ON p.id_proveedor=pr.id WHERE pr.id_almacen IS NULL OR pr.id_modalidad IS NULL GROUP BY pr.id ORDER BY deuda_total DESC";
        //echo $sql;
        $total_deuda=0;
        foreach ($pdo->query($sql) as $row) {
          echo '<tr>';
          echo '<td>';
          echo $row["id"];
          echo '</td>';
          echo '<td>'. $row["nombre"] . ' ' . $row["apellido"] . '</td>';
          echo '<td style="text-align:right">'. $row["cant_prendas"] . '</td>';
          echo '<td style="text-align:right">'. number_format($row["venta_total"],2) . '</td>';
          echo '<td style="text-align:right">'. number_format($row["deuda_total"],2) . '</td>';
          echo '</tr>';
        }
        Database::disconnect();?>
      </tbody>
    </table>
  </div>
</html>

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
  $(document).ready(function() {

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

    });
});

</script>