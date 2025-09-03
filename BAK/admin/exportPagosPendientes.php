<?php
session_start(); 
header('Content-Disposition: attachment; filename="pagos_pendientes.xls"');
include 'database.php';
?>
<!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Work+Sans:100,200,300,400,500,600,700,800,900" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
</head>
<body>

			<div class="row">
				<div class="table-responsive">
				<a href="#" id="aExportar" onclick="$('#example2').tableExport({type:'excel',escape:'false'});"></a>
				<table border="1" id="example2" name="formularios" style="visibility:hidden;">
					<thead>
		                <tr>		  
						  <th>Operacion</th>
						  <th>ID</th>
						  <th>ID Detalle</th>
              <th>ID Proveedor</th>
						  <th>Proveedor</th>
						  <th>Almacen</th>
						  <th>Fecha/Hora</th>
						  <th>Código</th>
						  <th>Categoría</th>
						  <th>Descripción</th>
						  <th>Cantidad</th>
						  <th>Precio</th>
						  <th>Subtotal</th>
						  <th>Deuda</th>
		                </tr>
		              </thead>
		             <tbody>
		              <?php     
						$pdo = Database::connect();
						$sql = " SELECT v.id as id_venta, vd.id as id_venta_detalle, a.almacen, v.fecha_hora, p.codigo, c.categoria, p.descripcion, vd.`cantidad`, vd.`precio`, vd.`subtotal`, m.`modalidad`, vd.`pagado`, vd.`deuda_proveedor`,  pr.nombre, pr.apellido, p.id_proveedor FROM `ventas_detalle` vd inner join ventas v on v.id = vd.id_venta inner join almacenes a on a.id = v.id_almacen inner join productos p on p.id = vd.id_producto inner join categorias c on c.id = p.id_categoria inner join modalidades m on m.id = vd.id_modalidad inner join proveedores pr on pr.id = p.id_proveedor WHERE v.anulada = 0 and m.id = 40 and vd.`pagado` = 0";
						if ($_SESSION['user']['id_perfil'] == 2) {
							$sql .= " and a.id = ".$_SESSION['user']['id_almacen']; 
						}
							foreach ($pdo->query($sql) as $row) {
								echo '<tr>';
								echo '<td>Venta</td>';
								echo '<td>'. $row['id_venta'] . '</td>';
								echo '<td>'. $row['id_venta_detalle'] . '</td>';
                echo '<td>'. $row['id_proveedor'] . '</td>';
								echo '<td>'. $row['nombre'] . ' ' . $row['apellido'] . '</td>';
								echo '<td>'. $row['almacen'] . '</td>';
								echo '<td>'. $row['fecha_hora'] . '</td>';
								echo '<td>'. $row['codigo'] . '</td>';
								echo '<td>'. $row['categoria'] . '</td>';
								echo '<td>'. $row['descripcion'] . '</td>';
								echo '<td>'. $row['cantidad'] . '</td>';
								echo '<td>'. $row['precio'] . '</td>';
								echo '<td>'. $row['subtotal'] . '</td>';
								echo '<td>'. $row['deuda_proveedor'] . '</td>';
								echo '</tr>';
							}
						$sql2 = " SELECT cj.id as id_canje, cd.id as id_canje_detalle, a.almacen, cj.fecha_hora, p.codigo, c.categoria, p.descripcion, cd.`cantidad`, cd.`precio`, cd.`subtotal`, m.`modalidad`, cd.`pagado`, cd.`deuda_proveedor`, pr.nombre, pr.apellido, p.id_proveedor FROM `canjes_detalle` cd inner join canjes cj on cj.id = cd.id_canje inner join almacenes a on a.id = cj.id_almacen inner join productos p on p.id = cd.id_producto inner join categorias c on c.id = p.id_categoria inner join modalidades m on m.id = cd.id_modalidad inner join proveedores pr on pr.id = p.id_proveedor WHERE cj.anulado = 0 and m.id = 40 and cd.`pagado` = 0";
						if ($_SESSION['user']['id_perfil'] == 2) {
							$sql .= " and a.id = ".$_SESSION['user']['id_almacen']; 
						}
							foreach ($pdo->query($sql2) as $row) {
								echo '<tr>';
								echo '<td>Canje</td>';
								echo '<td>'. $row['id_canje'] . '</td>';
								echo '<td>'. $row['id_canje_detalle'] . '</td>';
                echo '<td>'. $row['id_proveedor'] . '</td>';
								echo '<td>'. $row['nombre'] . ' ' . $row['apellido'] . '</td>';
								echo '<td>'. $row['almacen'] . '</td>';
								echo '<td>'. $row['fecha_hora'] . '</td>';
								echo '<td>'. $row['codigo'] . '</td>';
								echo '<td>'. $row['categoria'] . '</td>';
								echo '<td>'. $row['descripcion'] . '</td>';
								echo '<td>'. $row['cantidad'] . '</td>';
								echo '<td>'. $row['precio'] . '</td>';
								echo '<td>'. $row['subtotal'] . '</td>';
								echo '<td>'. $row['deuda_proveedor'] . '</td>';
								echo '</tr>';
							}
					   Database::disconnect();
					  ?>
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