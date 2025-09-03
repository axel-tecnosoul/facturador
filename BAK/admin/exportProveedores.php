<?php
session_start(); 
header('Content-Disposition: attachment; filename="proveedores.xls"');
include 'database.php';?>
<!doctype html>
<html lang="en">
  <head>
    <?php //include('head_tables.php');?>
    <style>
      /*table th, table td{
        border: 1px solid black;
      }*/

      /*.table-bordered {
  border-collapse: collapse;
}

.table-bordered th, .table-bordered td {
  border: 1px solid #000000;
  padding: 8px;
}*/
    </style>
  </head>
<body>
  <div class="row">
    <div class="table-responsive">
      <a href="#" id="aExportar" onclick="$('#example2').tableExport({type:'excel',escape:'false'});"></a>
      <table border="1" id="example2" class="table-bordered" name="formularios" style="visibility:hidden;">
        <thead>
          <tr>
            <th>ID</th>
            <th>DNI</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>E-Mail</th>
            <th>Telefono</th>
            <th>Credito</th>
            <th>Ventas por pesos</th>
            <th>Ventas por canje</th>
            <th>En stock</th>
            <th>Fecha Alta</th>
            <th>Activo</th>
          </tr>
        </thead>
        <tbody><?php
          $pdo = Database::connect();
          $sql = " SELECT p.id, p.dni, p.nombre, p.apellido, p.email, p.activo, p.fecha_alta, p.telefono, p.credito, a.almacen, m.modalidad, (SELECT COUNT(vd.id) FROM ventas_detalle vd INNER JOIN ventas v ON vd.id_venta=v.id INNER JOIN productos p2 ON p2.id=vd.id_producto LEFT JOIN devoluciones_detalle de ON de.id_venta_detalle=vd.id WHERE v.anulada = 0 AND v.id_venta_cbte_relacionado IS NULL AND de.id_devolucion IS NULL AND p2.id_proveedor=p.id) AS ventasPesos, (SELECT COUNT(cd.id) FROM canjes_detalle cd INNER JOIN canjes c ON cd.id_canje=c.id INNER JOIN productos p2 ON p2.id=cd.id_producto LEFT JOIN devoluciones_detalle de ON de.id_canje_detalle=cd.id WHERE c.anulado = 0 AND de.id_devolucion IS NULL AND p2.id_proveedor=p.id) AS ventasCanjes,(SELECT COUNT(s.id) FROM stock s INNER JOIN productos p2 ON p2.id=s.id_producto WHERE p2.id_proveedor=p.id AND s.cantidad > 0) AS enStock FROM proveedores p LEFT JOIN almacenes a ON p.id_almacen=a.id LEFT JOIN modalidades m ON p.id_modalidad=m.id WHERE 1 ORDER BY id";
          foreach ($pdo->query($sql) as $row) {
            echo '<tr>';
            echo '<td>'. $row["id"] . '</td>';
            echo '<td>'. $row["dni"] . '</td>';
            echo '<td>'. $row["nombre"] . '</td>';
            echo '<td>'. $row["apellido"] . '</td>';
            echo '<td>'. $row["email"] . '</td>';
            echo '<td>'. $row["telefono"] . '</td>';
            echo '<td>'. number_format($row["credito"],2,",",".") . '</td>';
            echo '<td>'. $row["ventasPesos"] . '</td>';
            echo '<td>'. $row["ventasCanjes"] . '</td>';
            echo '<td>'. $row["enStock"] . '</td>';
            echo '<td>'. $row["fecha_alta"] . '</td>';
            if ($row["activo"] == 1) {
              echo '<td>Si</td>';
            } else {
              echo '<td>No</td>';
            }
            echo '</tr>';
          }
          Database::disconnect();?>
        </tbody>
      </table>
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
  <script src="assets/js/bootstrap/tableExport.js"></script>
  <script src="assets/js/bootstrap/jquery.base64.js"></script>
  <!-- Plugins JS Ends-->
  <!-- Plugins JS Ends-->
  <!-- Theme js-->
  <script src="assets/js/script.js"></script>
</body>
</html>
<script>document.getElementById("aExportar").click();</script>