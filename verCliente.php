<?php
require("config.php");
if(empty($_SESSION['user']['id_perfil'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}

require 'database.php';

$id = null;
if ( !empty($_GET['id'])) {
  $id = $_REQUEST['id'];
}

if ( null==$id ) {
  header("Location: listarProveedores.php");
}

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT p.id, p.dni, p.nombre, p.apellido, p.email, p.activo, date_format(fecha_alta,'%d/%m/%Y'), p.telefono, p.credito, a.almacen, m.modalidad FROM proveedores p left join almacenes a on a.id = id_almacen left join modalidades m on m.id = id_modalidad WHERE p.id = ? ";
$q = $pdo->prepare($sql);
$q->execute(array($id));
$data = $q->fetch(PDO::FETCH_ASSOC);
  
Database::disconnect();?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head_forms.php');?>
    <link rel="stylesheet" type="text/css" href="assets/css/select2.css">
    <link rel="stylesheet" type="text/css" href="assets/css/datatables.css">
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
                      <li class="breadcrumb-item">Ver Proveedor</li>
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
                    <h5>Ver Proveedor</h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="#">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">E-Mail</label>
								<div class="col-sm-9"><input name="email" type="email" maxlength="99" class="form-control" value="<?php echo $data['email']; ?>" readonly="readonly"></div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Teléfono</label>
								<div class="col-sm-9"><input name="telefono" type="text" maxlength="99" class="form-control" value="<?php echo $data['telefono']; ?>" readonly="readonly"></div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">DNI</label>
								<div class="col-sm-9"><input name="dni" type="text" maxlength="99" class="form-control" value="<?php echo $data['dni']; ?>" readonly="readonly"></div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Nombre</label>
								<div class="col-sm-9"><input name="nombre" type="text" maxlength="99" class="form-control" value="<?php echo $data['nombre']; ?>" readonly="readonly"></div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Apellido</label>
								<div class="col-sm-9"><input name="apellido" type="text" maxlength="99" class="form-control" value="<?php echo $data['apellido']; ?>" readonly="readonly"></div>
							</div>
              <div class="form-group row">
								<label class="col-sm-3 col-form-label">Almacen</label>
								<div class="col-sm-9"><input name="almacen" type="text" maxlength="99" class="form-control" value="<?php echo $data['almacen']; ?>" readonly="readonly"></div>
							</div>
              <div class="form-group row">
								<label class="col-sm-3 col-form-label">Modalidad</label>
								<div class="col-sm-9"><input name="modalidad" type="text" maxlength="99" class="form-control" value="<?php echo $data['modalidad']; ?>" readonly="readonly"></div>
							</div>
              <div class="form-group row">
								<label class="col-sm-3 col-form-label">Credito</label>
								<div class="col-sm-9"><input name="credito" type="text" maxlength="99" class="form-control" value="<?php echo $data['credito']; ?>" readonly="readonly"></div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Activo</label>
								<div class="col-sm-9">
                  <select name="activo" id="activo" class="js-example-basic-single col-sm-12" disabled="disabled">
                    <option value="">Seleccione...</option>
                    <option value="1" <?php if ($data['activo']==1) echo " selected ";?>>Si</option>
                    <option value="0" <?php if ($data['activo']==0) echo " selected ";?>>No</option>
                  </select>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-12 col-form-label"><b>Compras</b></label>
              </div>
							<div class="form-group row">
								<div class="col-sm-12">
									<div class="dt-ext table-responsive">
									  <table class="display" id="dataTables-example666">
										<thead>
										  <tr>
										  <th>#Compra</th>
										  <th>Fecha</th>
										  <th>Modalidad</th>
										  <th>Código</th>
										  <th>Categoría</th>
										  <th>Descripción</th>
										  <th>Precio</th>
										  <th>Cantidad</th>
										  </tr>
										</thead>
										<tbody><?php
											$pdo = Database::connect();
											$sql = " SELECT cd.id, c.id nro_oc, date_format(c.fecha,'%d/%m/%Y') fecha, m.modalidad, p.codigo, cat.categoria, p.descripcion, cd.precio, cd.cantidad FROM compras_detalle cd inner join compras c on c.id = cd.id_compra inner join modalidades m on m.id = c.id_modalidad inner join productos p on p.id = cd.id_producto inner join categorias cat on cat.id = p.id_categoria WHERE c.id_proveedor = ".$id;
											
											foreach ($pdo->query($sql) as $row) {
												echo '<tr>';
												echo '<td>#'. $row[1] . '</td>';
												echo '<td>'. $row[2] . '</td>';
												echo '<td>'. $row[3] . '</td>';
												echo '<td>'. $row[4] . '</td>';
												echo '<td>'. $row[5] . '</td>';
												echo '<td>'. $row[6] . '</td>';
												echo '<td>'. $row[7] . '</td>';
												echo '<td>'. $row[8] . '</td>';
												echo '</tr>';
										   }
										   Database::disconnect();?>
										</tbody>
									  </table>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-12 col-form-label"><b>Ventas</b></label>
              </div>
							<div class="form-group row">
								<div class="col-sm-12">
                  <div class="dt-ext table-responsive">
									  <table class="display" id="dataTables-example667">
                      <thead>
                        <tr>
                          <th>Venta</th>
                          <th>Fecha</th>
                          <th>Código</th>
                          <th>Descripción</th>
                          <th>Precio</th>
                          <th>Almacen</th>
                          <th>Pagado</th>
                          <th>Deuda</th>
                          <th>Forma de Venta</th>
                          <th>Modalidad</th>
                          <th>Fecha de pago</th>
                          <th>Caja egreso de dinero</th>
                          <th>Almacen egreso de dinero</th>
                          <th>Forma de pago a Proveedora</th>
                          <th>Cantidad</th>
                          <th>Categoría</th>
                        </tr>
                      </thead>
                      <tbody><?php
                        $pdo = Database::connect();
                        $sql = " SELECT v.id,date_format(v.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora,(SELECT forma_pago FROM forma_pago fp WHERE v.id_forma_pago=fp.id) AS forma_pago_venta,c.categoria,p.codigo,p.descripcion,vd.subtotal,vd.cantidad,vd.pagado,a.almacen,date_format(vd.fecha_hora_pago,'%d/%m/%Y %H:%i') AS fecha_hora_pago,caja_egreso,(SELECT almacen FROM almacenes a2 WHERE vd.id_almacen=a2.id) AS almacen_egreso_dinero,deuda_proveedor,(SELECT forma_pago FROM forma_pago fp WHERE vd.id_forma_pago=fp.id) AS forma_pago_proveedor,vd.id_modalidad,m.modalidad,vd.id AS id_detalle_venta FROM ventas v INNER JOIN ventas_detalle vd ON vd.id_venta=v.id INNER JOIN productos p ON vd.id_producto=p.id INNER JOIN proveedores pr ON p.id_proveedor=pr.id INNER JOIN categorias c ON p.id_categoria=c.id INNER JOIN almacenes a ON v.id_almacen=a.id INNER JOIN modalidades m ON vd.id_modalidad=m.id LEFT JOIN devoluciones_detalle de ON de.id_venta_detalle=vd.id WHERE v.anulada=0 AND v.id_venta_cbte_relacionado IS NULL AND de.id_devolucion IS NULL AND pr.id = ".$id;
                        //echo $sql;
                        foreach ($pdo->query($sql) as $row) {
                          echo '<tr>';
                          echo '<td>';
                          echo "<a href='verVenta.php?id=".$row["id"]."' target='_blank' class='badge badge-primary'><i class='fa fa-eye' aria-hidden='true'></i></a> ".$row["id"];
                          echo '</td>';
                          echo '<td>'.$row["fecha_hora"].'</td>';
                          echo '<td>'.$row["codigo"].'</td>';
                          echo '<td>'.$row["descripcion"].'</td>';
                          echo '<td>$'.number_format($row["subtotal"],2,',','.').'</td>';
                          echo '<td>'.$row["almacen"].'</td>';
                          echo '<td>';
                          if($row["pagado"]==1){
                            echo "Si";
                          }else{
                            echo "No";
                          }
                          echo '</td>';
                          echo '<td>$'.number_format($row["deuda_proveedor"],2,',','.').'</td>';
                          echo '<td>'.$row["forma_pago_venta"].'</td>';
                          echo '<td>';
                          if(!($row["id_modalidad"]==40 and $row["pagado"]==1)){

                          //}else{
                            if($_SESSION["user"]["id_perfil"]==1){
                              echo '<a href="modificarModalidadVenta.php?id='.$row["id_detalle_venta"].'&t=v"><img src="img/icon_modificar.png" width="24" height="25" border="0" alt="Modificar" title="Modificar"></a>';
                            }
                          }
                          echo $row["modalidad"];
                          echo '</td>';
                          echo '<td>'.$row["fecha_hora_pago"].'hs</td>';
                          echo '<td>'.$row["caja_egreso"].'</td>';
                          echo '<td>'.$row["almacen_egreso_dinero"].'</td>';
                          echo '<td>'.$row["forma_pago_proveedor"].'</td>';
                          echo '<td>'.$row["cantidad"].'</td>';
                          echo '<td>'.$row["categoria"].'</td>';
                          echo '</tr>';
                        }
                        Database::disconnect();?>
                      </tbody>
									  </table>
									</div>
								</div>
							</div>
              <div class="form-group row">
								<label class="col-sm-12 col-form-label"><b>Canjes</b></label>
              </div>
							<div class="form-group row">
								<div class="col-sm-12">
                  <div class="dt-ext table-responsive">
                    <table class="display" id="dataTables-example669">
                      <thead>
                        <tr>
                          <th>Canje</th>
                          <th>Fecha</th>
                          <th>Código</th>
                          <th>Descripción</th>
                          <th>Precio</th>
                          <th>Almacen</th>
                          <th>Pagado</th>
                          <th>Deuda</th>
                          <th>Forma de Venta</th>
                          <th>Modalidad</th>
                          <th>Fecha de pago</th>
                          <th>Caja egreso de dinero</th>
                          <th>Almacen egreso de dinero</th>
                          <th>Forma de pago a Proveedora</th>
                          <th>Cantidad</th>
                          <th>Categoría</th>
                        </tr>
                      </thead>
                      <tbody><?php
                        $pdo = Database::connect();
                        $sql = " SELECT c.id, date_format(c.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora, a.almacen, c.total,cd.cantidad,cd.subtotal,c2.categoria,p.codigo,p.descripcion,    cd.pagado,a.almacen,date_format(cd.fecha_hora_pago,'%d/%m/%Y %H:%i') AS fecha_hora_pago,caja_egreso,(SELECT almacen FROM almacenes a2 WHERE cd.id_almacen=a2.id) AS almacen_egreso_dinero,deuda_proveedor,(SELECT forma_pago FROM forma_pago fp WHERE cd.id_forma_pago=fp.id) AS forma_pago_proveedor,cd.id_modalidad,m.modalidad,cd.id AS id_detalle_venta FROM canjes c INNER JOIN canjes_detalle cd ON cd.id_canje=c.id INNER JOIN productos p ON cd.id_producto=p.id INNER JOIN proveedores pr ON p.id_proveedor=pr.id INNER JOIN categorias c2 ON p.id_categoria=c2.id INNER JOIN almacenes a on a.id = c.id_almacen INNER JOIN modalidades m ON cd.id_modalidad=m.id LEFT JOIN devoluciones_detalle de ON de.id_canje_detalle=cd.id WHERE anulado = 0 AND de.id_devolucion IS NULL AND p.id_proveedor = ".$id;
                        if ($_SESSION['user']['id_perfil'] == 2) {
                          $sql .= " and a.id = ".$_SESSION['user']['id_almacen']; 
                        }
                        foreach ($pdo->query($sql) as $row) {
                          echo '<tr>';
                          echo '<td>';
                          echo "<a href='verCanje.php?id=".$row["id"]."' target='_blank' class='badge badge-primary'><i class='fa fa-eye' aria-hidden='true'></i></a> ".$row["id"];
                          echo '</td>';
                          echo '<td>'.$row["fecha_hora"].'hs</td>';
                          echo '<td>'. $row["codigo"] . '</td>';
                          echo '<td>'. $row["descripcion"] . '</td>';
                          echo '<td>$'. number_format($row["subtotal"],2,',','.') . '</td>';
                          echo '<td>'.$row["almacen"].'</td>';

                          echo '<td>';
                          if($row["pagado"]==1){
                            echo "Si";
                          }else{
                            echo "No";
                          }
                          echo '</td>';
                          echo '<td>$'.number_format($row["deuda_proveedor"],2,',','.').'</td>';
                          //echo '<td>'.$row["forma_pago_venta"].'</td>';
                          echo '<td>Credito</td>';
                          echo '<td>';
                          if(!($row["id_modalidad"]==40 and $row["pagado"]==1)){

                          //}else{
                            
                            //AGREGAR POSIBILIDAD DE MODIFICAR MODALIDAD EN LOS CANJES

                            if($_SESSION["user"]["id_perfil"]==1){
                              echo '<a href="modificarModalidadVenta.php?id='.$row["id_detalle_venta"].'&t=c"><img src="img/icon_modificar.png" width="24" height="25" border="0" alt="Modificar" title="Modificar"></a>';
                            }
                          }
                          echo $row["modalidad"];
                          echo '</td>';
                          echo '<td>'.$row["fecha_hora_pago"].'hs</td>';
                          echo '<td>'.$row["caja_egreso"].'</td>';
                          echo '<td>'.$row["almacen_egreso_dinero"].'</td>';
                          echo '<td>'.$row["forma_pago_proveedor"].'</td>';
                          echo '<td>'.$row["cantidad"].'</td>';
                          echo '<td>'.$row["categoria"].'</td>';

                          echo '</tr>';
                        }
                        Database::disconnect();?>
                      </tbody>
                    </table>
									</div>
								</div>
							</div>
              <div class="form-group row">
								<label class="col-sm-12 col-form-label"><b>En stock</b></label>
              </div>
							<div class="form-group row">
								<div class="col-sm-12">
                  <div class="dt-ext table-responsive">
									  <table class="display" id="dataTables-example668">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Fecha</th>
                          <th>Código</th>
                          <th>Categoría</th>
                          <th>Descripción</th>
                          <th>Cantidad</th>
                          <th>Precio</th>
                          <th>Almacen</th>
                          <th>Modalidad</th>
                          <th>Activo</th>
                        </tr>
                      </thead>
                      <tbody><?php
                        $pdo = Database::connect();
                        //$sql = " SELECT v.id,date_format(v.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora,c.categoria,p.codigo,p.descripcion,vd.precio,vd.cantidad,vd.pagado FROM ventas v INNER JOIN ventas_detalle vd ON vd.id_venta=v.id INNER JOIN productos p ON vd.id_producto=p.id INNER JOIN proveedores pr ON p.id_proveedor=pr.id INNER JOIN categorias c ON p.id_categoria=c.id WHERE pr.id = ".$id;
                        $sql = " SELECT s.id, p.codigo, c.categoria, p.descripcion, a.almacen, s.cantidad, m.modalidad, p.precio,p.activo,date_format(p.fecha_hora_alta,'%d/%m/%Y %H:%i') AS fecha_hora FROM stock s inner join productos p on p.id = s.id_producto inner join almacenes a on a.id = s.id_almacen left join modalidades m on m.id = s.id_modalidad left join categorias c on c.id = p.id_categoria WHERE s.cantidad > 0 AND p.id_proveedor = ".$id;
                        if ($_SESSION['user']['id_perfil'] == 2) {
                          $sql .= " and a.id = ".$_SESSION['user']['id_almacen'];
                        }
                        foreach ($pdo->query($sql) as $row) {
                          echo '<tr>';
                          echo '<td>'.$row["id"].'</td>';
                          echo '<td>'.$row["fecha_hora"] .'</td>';
                          echo '<td>'.$row["codigo"] .'</td>';
                          echo '<td>'.$row["categoria"] .'</td>';
                          echo '<td>'.$row["descripcion"] .'</td>';
                          echo '<td>'.$row["cantidad"] .'</td>';
                          echo '<td>$'.number_format($row["precio"],2,',','.') .'</td>';
                          echo '<td>'.$row["almacen"] .'</td>';
                          echo '<td>'.$row["modalidad"] .'</td>';
                          echo '<td>';
                          if($row["activo"]==1){
                            echo "Si";
                          }else{
                            echo "No";
                          }
                          echo '</td>';
                          echo '</tr>';
                        }
                        Database::disconnect();?>
                      </tbody>
									  </table>
									</div>
								</div>
							</div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
						          <a href='listarProveedores.php' class="btn btn-light">Volver</a>
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
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="assets/js/script.js"></script>
    <!-- Plugin used-->
	  <script src="assets/js/select2/select2.full.min.js"></script>
    <script src="assets/js/select2/select2-custom.js"></script>
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

	<script>
		$(document).ready(function() {
      let basicDataTable={
				stateSave: true,
				responsive: true,
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
				}}
			}
			$('#dataTables-example666').DataTable(basicDataTable);
      $('#dataTables-example667').DataTable(basicDataTable);
      $('#dataTables-example668').DataTable(basicDataTable);
      $('#dataTables-example669').DataTable(basicDataTable);
		});
		
		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
  </body>
</html>