<?php
session_start(); 
header('Content-Disposition: attachment; filename="productos_vendidos.xls"');
include 'database.php';?>
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
                <th>Fecha/Hora</th>
                <th>Código</th>
                <th>Descripción</th>
                <th>Categoría</th>
                <th>Proveedor</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Almacen</th>
                <th>Pagado</th>
                <th>Caja</th>
                <th>Forma de Pago</th>
              </tr>
            </thead>
            <tbody><?php
              $pdo = Database::connect();
              //$sql = " SELECT v.id as id_venta,vd.id AS id_detalle_venta, v.total_con_descuento, vd.pagado, a.almacen, p.codigo, c.categoria, p.descripcion, vd.cantidad, vd.precio, vd.subtotal, m.modalidad, vd.pagado, pr.nombre, pr.apellido, vd.id_forma_pago, fp.forma_pago, vd.id_venta,vd.deuda_proveedor,date_format(v.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora,caja_egreso,forma_pago FROM ventas_detalle vd INNER JOIN ventas v ON vd.id_venta=v.id inner join productos p on p.id = vd.id_producto inner join categorias c on c.id = p.id_categoria inner join modalidades m on m.id = vd.id_modalidad inner join proveedores pr on pr.id = p.id_proveedor LEFT join almacenes a on a.id = v.id_almacen LEFT join forma_pago fp on fp.id = vd.id_forma_pago WHERE v.anulada = 0 AND v.id_venta_cbte_relacionado IS NULL";
              $sql="SELECT v.id as id_venta,vd.id AS id_detalle_venta, v.total_con_descuento, vd.pagado, a.almacen, p.codigo, c.categoria, p.descripcion, vd.cantidad, vd.precio, vd.subtotal, m.modalidad, vd.pagado, pr.nombre, pr.apellido, vd.id_forma_pago, fp.forma_pago, vd.id_venta,vd.deuda_proveedor,date_format(v.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora_formatted, fecha_hora, caja_egreso, forma_pago FROM ventas_detalle vd INNER JOIN ventas v ON vd.id_venta=v.id inner join productos p on p.id = vd.id_producto inner join categorias c on c.id = p.id_categoria inner join modalidades m on m.id = vd.id_modalidad inner join proveedores pr on pr.id = p.id_proveedor LEFT join almacenes a on a.id = v.id_almacen LEFT join forma_pago fp on fp.id = vd.id_forma_pago LEFT JOIN devoluciones_detalle de ON de.id_venta_detalle=vd.id WHERE v.anulada = 0 AND v.id_venta_cbte_relacionado IS NULL AND de.id_devolucion IS NULL ";
              if ($_SESSION['user']['id_perfil'] == 2) {
                $sql .= " and a.id = ".$_SESSION['user']['id_almacen']; 
              }
              $aProductosVendidos=[];
							foreach ($pdo->query($sql) as $row) {
                $pagado="No";
                if($row["pagado"] == 1){
									$pagado="Si";
								}
                $aProductosVendidos[]=[
                  "operacion" => "Venta",
                  "id" => $row['id_venta'],
                  "fecha_hora" => $row['fecha_hora'],
                  "codigo" => $row['codigo'],
                  "descripcion" => $row['descripcion'],
                  "categoria" => $row['categoria'],
                  "proveedor" => $row["nombre"]." ".$row["apellido"],
                  "subtotal" => $row['subtotal'],
                  "cantidad" => $row['cantidad'],
                  "almacen" => $row['almacen'],
                  "pagado" => $pagado,
                  "caja_egreso" => $row['caja_egreso'],
                  "forma_pago" => $row['forma_pago'],
                ];

								/*echo '<tr>';
								echo '<td>Venta</td>';
								echo '<td>'. $row['id_venta'] . '</td>';
								echo '<td>'. $row['fecha_hora_formatted'] . '</td>';
                echo '<td>'. $row['codigo'] . '</td>';
								echo '<td>'. $row['descripcion'] . '</td>';
                echo '<td>'. $row['categoria'] . '</td>';
                echo '<td>'. $row["nombre"]." ".$row["apellido"] . '</td>';
								echo '<td>'. number_format($row['subtotal'],2,",",".") . '</td>';
                echo '<td>'. $row['cantidad'] . '</td>';
                echo '<td>'. $row['almacen'] . '</td>';
								echo '<td>'. $pagado . '</td>';
								echo '<td>'. $row['caja_egreso'] . '</td>';
								echo '<td>'. $row['forma_pago'] . '</td>';
								echo '</tr>';*/
							}
              //$sql2 = "SELECT cj.id AS id_canje, cd.id AS id_detalle_canje, cj.total_con_descuento, cd.pagado, a.almacen, p.codigo, c.categoria, p.descripcion, cd.cantidad, cd.precio, cd.subtotal, m.modalidad, cd.pagado, pr.nombre, pr.apellido, cd.id_forma_pago, fp.forma_pago, cd.id_canje,cd.deuda_proveedor,date_format(cj.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora,caja_egreso,forma_pago FROM canjes_detalle cd INNER JOIN canjes cj ON cd.id_canje=cj.id inner join productos p on p.id = cd.id_producto inner join categorias c on c.id = p.id_categoria inner join modalidades m on m.id = cd.id_modalidad inner join proveedores pr on pr.id = p.id_proveedor LEFT join almacenes a on a.id = cj.id_almacen LEFT join forma_pago fp on fp.id = cd.id_forma_pago WHERE cj.anulado = 0";
              $sql2="SELECT cj.id AS id_canje, cd.id AS id_detalle_canje, cj.total_con_descuento, cd.pagado, a.almacen, p.codigo, c.categoria, p.descripcion, cd.cantidad, cd.precio, cd.subtotal, m.modalidad, cd.pagado, pr.nombre, pr.apellido, cd.id_forma_pago, fp.forma_pago, cd.id_canje,cd.deuda_proveedor,date_format(cj.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora_formatted, fecha_hora,caja_egreso,forma_pago FROM canjes_detalle cd INNER JOIN canjes cj ON cd.id_canje=cj.id inner join productos p on p.id = cd.id_producto inner join categorias c on c.id = p.id_categoria inner join modalidades m on m.id = cd.id_modalidad inner join proveedores pr on pr.id = p.id_proveedor LEFT join almacenes a on a.id = cj.id_almacen LEFT join forma_pago fp on fp.id = cd.id_forma_pago LEFT JOIN devoluciones_detalle de ON de.id_canje_detalle=cd.id WHERE cj.anulado = 0 AND de.id_devolucion IS NULL";
              if ($_SESSION['user']['id_perfil'] == 2) {
                $sql .= " and a.id = ".$_SESSION['user']['id_almacen']; 
              }
							foreach ($pdo->query($sql2) as $row) {
                $pagado="No";
                if($row["pagado"] == 1){
									$pagado="Si";
								}

                $aProductosVendidos[]=[
                  "operacion" => "Canje",
                  "id" => $row['id_canje'],
                  "fecha_hora" => $row['fecha_hora'],
                  "codigo" => $row['codigo'],
                  "descripcion" => $row['descripcion'],
                  "categoria" => $row['categoria'],
                  "proveedor" => $row["nombre"]." ".$row["apellido"],
                  "subtotal" => $row['subtotal'],
                  "cantidad" => $row['cantidad'],
                  "almacen" => $row['almacen'],
                  "pagado" => $pagado,
                  "caja_egreso" => $row['caja_egreso'],
                  "forma_pago" => $row['forma_pago'],
                ];

                /*echo '<tr>';
								echo '<td>Canje</td>';
								echo '<td>'. $row['id_canje'] . '</td>';
								echo '<td>'. $row['fecha_hora_formatted'] . '</td>';
                echo '<td>'. $row['codigo'] . '</td>';
								echo '<td>'. $row['descripcion'] . '</td>';
                echo '<td>'. $row['categoria'] . '</td>';
                echo '<td>'. $row["nombre"]." ".$row["apellido"] . '</td>';
								echo '<td>'. number_format($row['subtotal'],2,",",".") . '</td>';
                echo '<td>'. $row['cantidad'] . '</td>';
                echo '<td>'. $row['almacen'] . '</td>';
                echo '<td>'. $pagado . '</td>';
								echo '<td>'. $row['caja_egreso'] . '</td>';
								echo '<td>'. $row['forma_pago'] . '</td>';
								echo '</tr>';*/

								/*echo '<tr>';
								echo '<td>Canje</td>';
								echo '<td>'. $row['id_canje'] . '</td>';
								echo '<td>'. $row['fecha_hora'] . '</td>';
								echo '<td>'. $row['descripcion'] . '</td>';
								echo '<td>'. number_format($row['deuda_proveedor'],2,",",".") . '</td>';
								echo '<td>'. $row['caja_egreso'] . '</td>';
								echo '<td>'. $row['forma_pago'] . '</td>';
								echo '<td>'. $row['almacen'] . '</td>';
								if($row["pagado"] == 1){
									echo '<td>SI</td>';
								}else{
									echo '<td>NO</td>';
								}
								echo '</tr>';*/
							}
					    Database::disconnect();

              //var_dump($aProductosVendidos);
              // Función de comparación personalizada
              function compararFechas($a, $b) {
                $fechaHoraA = strtotime($a['fecha_hora']);
                $fechaHoraB = strtotime($b['fecha_hora']);
                return $fechaHoraA - $fechaHoraB;
              }

              // Ordenar el array usando la función de comparación personalizada
              usort($aProductosVendidos, 'compararFechas');
              
              foreach ($aProductosVendidos as $key => $row) {
                echo '<tr>';
								echo '<td>'. $row['operacion'] . '</td>';
								echo '<td>'. $row['id'] . '</td>';
								echo '<td>'. date("d-m-Y H:i",strtotime($row['fecha_hora'])) . '</td>';
                echo '<td>'. $row['codigo'] . '</td>';
								echo '<td>'. $row['descripcion'] . '</td>';
                echo '<td>'. $row['categoria'] . '</td>';
                echo '<td>'. $row["proveedor"] . '</td>';
								echo '<td>'. number_format($row['subtotal'],2,",",".") . '</td>';
                echo '<td>'. $row['cantidad'] . '</td>';
                echo '<td>'. $row['almacen'] . '</td>';
                echo '<td>'. $row['pagado'] . '</td>';
								echo '<td>'. $row['caja_egreso'] . '</td>';
								echo '<td>'. $row['forma_pago'] . '</td>';
								echo '</tr>';
              }?>
				    </tbody>
					</table>
				</div>
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