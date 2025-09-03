<?php 
session_start(); 
if(empty($_SESSION['user'])){
	header("Location: index.php");
	die("Redirecting to index.php"); 
}?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head_tables.php');?>
  </head>
  <?php //include('header.php');?>

  <div class="dt-ext table-responsive" style="height: 100%;">
    <table class="display" id="dataTables-example666" style="height: 100%;">
      <thead>
        <tr>
          <th style="text-align:center">ID</th>
          <th style="text-align:center">ID Venta</th>
          <th style="text-align:center">Proveedora</th>
          <th style="text-align:center">Modalidad proveedora</th>
          <th style="text-align:center">Producto</th>
          <th style="text-align:center">Modalidad venta</th>
          <th style="text-align:center">Deuda proveedora</th>
        </tr>
      </thead>
      <tbody>
        <?php
        include_once("database.php");
        $pdo = Database::connect();
        $sql = "SELECT vd.id,vd.id_venta,pr.apellido,pr.nombre,p.codigo,p.descripcion,(SELECT modalidad FROM modalidades m WHERE vd.id_modalidad=m.id) AS modalidad_venta,vd.id_producto,vd.deuda_proveedor,p.id_proveedor,(SELECT modalidad FROM modalidades m WHERE pr.id_modalidad=m.id) AS modalidad_proveedor FROM ventas_detalle vd INNER JOIN productos p ON vd.id_producto=p.id INNER JOIN proveedores pr ON p.id_proveedor=pr.id";
        //echo $sql;
        $total_deuda=0;
        foreach ($pdo->query($sql) as $row) {
          if($row["modalidad_venta"]!=$row["modalidad_proveedor"]){
            //var_dump($row);
            echo '<tr>';
            echo '<td>';
            echo $row["id"];
            echo '</td>';
            echo '<td>'. $row["id_venta"] .'</td>';
            echo '<td>'. $row["nombre"] . ' ' . $row["apellido"] . '</td>';
            echo '<td>'. $row["modalidad_proveedor"] . '</td>';
            echo '<td>('.$row["codigo"].') '.$row["descripcion"].'</td>';
            echo '<td>'. $row["modalidad_venta"] . '</td>';
            echo '<td style="text-align:right">'. number_format($row["deuda_proveedor"],2) . '</td>';
            //echo '<td style="text-align:right">'. number_format($row["deuda_total"],2) . '</td>';
            echo '</tr>';
          }
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