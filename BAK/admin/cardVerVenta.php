<?php
require_once 'database.php';
require_once 'funciones.php';

if (empty($id) or !empty($_GET['id'])) {
  $id = $_REQUEST['id'];
}

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT v.id, date_format(v.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora_formatted, v.fecha_hora, v.nombre_cliente, v.dni, v.direccion, v.email, v.telefono, a.almacen, v.total, d.descripcion, d.minimo_compra, d.minimo_cantidad_prendas, d.monto_fijo, d.porcentaje, v.total_con_descuento,v.modalidad_venta, fp.forma_pago,v.tipo_comprobante,v.estado,v.punto_venta,v.numero_comprobante,v.cae,date_format(v.fecha_vencimiento_cae,'%d/%m/%Y') AS fecha_vencimiento_cae,id_venta_cbte_relacionado,v.anulada,u.usuario, dev.id as devolucion_id FROM ventas v inner join almacenes a on a.id = v.id_almacen left join descuentos d on d.id = v.id_descuento_aplicado LEFT JOIN forma_pago fp ON v.id_forma_pago = fp.id INNER JOIN usuarios u ON v.id_usuario=u.id LEFT JOIN devoluciones dev ON dev.id_nueva_venta = v.id  WHERE v.id = ? ";
//echo $sql;
$q = $pdo->prepare($sql);
$q->execute(array($id));
$data = $q->fetch(PDO::FETCH_ASSOC);

$descuento=$data['descripcion'];
if($data['porcentaje']>0){
  $descuento.=" (".$data['porcentaje']."%)";
}
if($data['monto_fijo']>0){
  $descuento.=" ($".number_format($data['monto_fijo'],0,",",".").")";
}
if($data['minimo_cantidad_prendas']>0){
  $descuento.=" Cantidad prendas minimo: ".$data['minimo_cantidad_prendas'];
}
if($data['minimo_compra']>0){
  $descuento.=" Compra minima: ".number_format($data['minimo_compra'],0,",",".");
}
if($descuento=="" or empty($descuento)){
  $descuento="No se aplicaron descuentos";
}

$sql2 = "SELECT cj.id_venta, cj.credito_usado FROM canjes cj INNER JOIN ventas v ON v.id = cj.id_venta WHERE cj.id_venta = ? ";
//echo $sql;
$q2 = $pdo->prepare($sql2);
$q2->execute(array($id));
$data2 = $q2->fetch(PDO::FETCH_ASSOC);
$tiene_canjes = 0;
if(!empty($data2)){
  $tiene_canjes = 1;
}
Database::disconnect();

//var_dump($data);
//die;?>

<div class="card mb-0"><?php
  $style="";
  $texto="";
  $link_volver="listarVentas.php";
  if($data['anulada']==1){
    $style="background-color: rgb(255 0 0 / 50%);";
    $texto="Anulada";
    $link_volver="listarVentasAnuladas.php";
  }?>
  <div class="card-header" style="<?=$style?>">
    <h5>Ver Venta <?=$texto?><?php
      if($data['tipo_comprobante'] !== 'R'){ ?>
        <a href="factura.php?id=<?= $id;?>" target="_blank"><img src="img/print.png" width="30" height="25" border="0" alt="Imprimir comprobante" title="Imprimir comprobante"></a><?php
      }?>
    </h5>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col">
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Fecha Hora</label>
          <div class="col-sm-9"><?php echo $data['fecha_hora_formatted']; ?>hs</div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Usuario</label>
          <div class="col-sm-9"><?php echo $data['usuario']; ?></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Cliente</label>
          <div class="col-sm-9"><?php echo $data['nombre_cliente']; ?></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">DNI</label>
          <div class="col-sm-9"><?php echo $data['dni']; ?></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Dirección</label>
          <div class="col-sm-9"><?php echo $data['direccion']; ?></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">E-Mail</label>
          <div class="col-sm-9"><?php echo $data['email']; ?></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Teléfono</label>
          <div class="col-sm-9"><?php echo $data['telefono']; ?></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Almacen</label>
          <div class="col-sm-9"><?php echo $data['almacen']; ?></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Modalidad de venta</label>
          <div class="col-sm-9"><?php echo $data['modalidad_venta']; ?></div>
        </div>

        <div class="form-group row">
          <label class="col-sm-12 col-form-label">Productos</label>
        </div><?php

        $total_productos = $total_con_descuentos = 0;
        if($tiene_canjes == 0){?>
          <div class="form-group row">
            <div class="col-sm-12">
              <div class="dt-ext table-responsive">
                <table class="display" id="tableVentaProductos">
                  <thead>
                    <tr>
                      <th>Proveedor</th>
                      <th>Código</th>
                      <th>Categoría</th>
                      <th>Descripción</th>
                      <th>Precio</th>
                      <th>Cantidad</th>
                      <th>Subtotal</th>
                      <th>Modalidad</th>
                      <th>Pagado</th>
                      <th>ID Devolucion</th>
                    </tr>
                  </thead>
                  <tbody><?php
                    $pdo = Database::connect();
                    $sql = " SELECT p.codigo, c.categoria, p.descripcion, vd.precio, vd.cantidad, vd.subtotal, m.modalidad, vd.pagado, pr.apellido, pr.nombre, p.id_proveedor, vd.id as id_venta_detalle, dd.id_venta_detalle as devoluciones_venta_detalle, d.id as id_devolucion FROM ventas_detalle vd INNER JOIN ventas v ON v.id = vd.id_venta INNER JOIN productos p ON p.id = vd.id_producto INNER JOIN categorias c ON c.id = p.id_categoria INNER JOIN modalidades m ON m.id = vd.id_modalidad INNER JOIN proveedores pr ON p.id_proveedor=pr.id LEFT JOIN devoluciones_detalle dd ON dd.id_venta_detalle = vd.id LEFT JOIN devoluciones d ON d.id = dd.id_devolucion WHERE vd.id_venta = ".$data['id'];
                    //var_dump($sql);
                    
                    foreach ($pdo->query($sql) as $row) {
                      echo '<tr>';
                      echo '<td>('. $row["id_proveedor"] .') '. $row["nombre"] .' '. $row["apellido"] .' </td>';
                      echo '<td>'. $row['codigo'] . '</td>';
                      echo '<td>'. $row['categoria'] . '</td>';
                      echo '<td>'. $row['descripcion'] . '</td>';
                      echo '<td>$'. number_format($row['precio'],2) . '</td>';
                      $total_productos += $row['precio'];
                      echo '<td>'. $row['cantidad'] . '</td>';
                      echo '<td>$'. number_format($row['subtotal'],2) . '</td>';
                      $total_con_descuentos += $row['subtotal'];
                      echo '<td>'. $row['modalidad'] . '</td>';
                      if ($row['pagado'] == 1) {
                        echo '<td>Si</td>';	
                      } else {
                        echo '<td>No</td>';	
                      }
                      if($row['id_devolucion'] == NULL){
                        echo '<td></td>';
                      }else{
                        echo '<td>'. $row['id_devolucion'] . '</td>';
                      }
                      echo '</tr>';
                    }
                    Database::disconnect();?>
                  </tbody>
                </table>
              </div>
            </div>
          </div><?php
        }else{?>
          <div class="form-group row">
            <div class="col-sm-12">
              <div class="dt-ext table-responsive">
                <table class="display" id="tableVentaProductos">
                  <thead>
                    <tr>
                      <th>Proveedor</th>
                      <th>Código</th>
                      <th>Categoría</th>
                      <th>Descripción</th>
                      <th>Precio</th>
                      <th>Cantidad</th>
                      <th>Subtotal</th>
                      <th>Modalidad</th>
                      <th>Pagado</th>
                      <th>ID Devolucion</th>
                    </tr>
                  </thead>
                  <tbody><?php
                    $pdo = Database::connect();
                    $sql = " SELECT p.codigo, c.categoria, p.descripcion, cd.precio, cd.cantidad, cd.subtotal, m.modalidad, cd.pagado, pr.apellido, pr.nombre, p.id_proveedor, cd.id as id_canje_detalle, dd.id_venta_detalle as devoluciones_canje_detalle, d.id as id_devolucion FROM canjes_detalle cd INNER JOIN canjes cj ON cj.id = cd.id_canje INNER JOIN ventas v ON v.id = cj.id_venta INNER JOIN productos p ON p.id = cd.id_producto INNER JOIN categorias c ON c.id = p.id_categoria INNER JOIN modalidades m ON m.id = cd.id_modalidad INNER JOIN proveedores pr ON p.id_proveedor=pr.id LEFT JOIN devoluciones_detalle dd ON dd.id_venta_detalle = cd.id LEFT JOIN devoluciones d ON d.id = dd.id_devolucion WHERE cj.id_venta = ".$data['id'];
                    //var_dump($sql);
                    
                    foreach ($pdo->query($sql) as $row) {
                      $pagado="No";
                      if ($row['pagado'] == 1) {
                        $pagado="Si";
                      }
                      $id_devolucion="";
                      if ($row['id_devolucion'] != NULL) {
                        $id_devolucion=$row['id_devolucion'];
                      }
                      echo '<tr>';
                      echo '<td>('. $row["id_proveedor"] .') '. $row["nombre"] .' '. $row["apellido"] .' </td>';
                      echo '<td>'. $row['codigo'] . '</td>';
                      echo '<td>'. $row['categoria'] . '</td>';
                      echo '<td>'. $row['descripcion'] . '</td>';
                      echo '<td>$'. number_format($row['precio'],2) . '</td>';
                      $total_productos += $row['precio'];
                      echo '<td>'. $row['cantidad'] . '</td>';
                      echo '<td>$'. number_format($row['subtotal'],2) . '</td>';
                      $total_con_descuentos += $row['subtotal'];
                      echo '<td>'. $row['modalidad'] . '</td>';
                      echo '<td>'. $pagado . '</td>';
                      echo '<td>'. $id_devolucion . '</td>';
                      /*if($row['id_devolucion'] == NULL){
                        echo '<td></td>';
                      }else{
                        echo '<td>'. $row['id_devolucion'] . '</td>';
                      }*/
                      echo '</tr>';
                    }
                    Database::disconnect();?>
                  </tbody>
                </table>
              </div>
            </div>
          </div><?php
        }?>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Total productos vendidos</label>
          <div class="col-sm-9">$<?php echo number_format($total_productos,2); ?></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Forma de pago</label>
          <div class="col-sm-9"><?php echo $data['forma_pago']; ?></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Descuento</label>
          <div class="col-sm-9"><?php echo $descuento?></div>
        </div><?php
        if($tiene_canjes == 1){?>
          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Total con descuento</label>
            <div class="col-sm-9">$<?php echo number_format($total_con_descuentos,2)?></div>
          </div>
          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Credito usado</label>
            <div class="col-sm-9">$<?php echo number_format($data2['credito_usado'],2); ?></div>
          </div><?php
        }?>
        <!-- <div class="form-group row">
          <label class="col-sm-3 col-form-label">Total Productos Vendidos</label>
          <div class="col-sm-9">$<?php echo number_format($data['total'],2); ?></div>
        </div> -->
        <?php
        
        if($data['devolucion_id'] != NULL){?>
          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Total con descuento</label>
            <div class="col-sm-9">$<?php echo number_format($total_con_descuentos,2)?></div>
          </div>
          <div class="form-group row">
            <label class="col-sm-12 col-form-label">Devoluciones</label>
          </div>
          <div class="form-group row">
            <div class="col-sm-12">
              <div class="dt-ext table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Fecha</th>
                      <th>ID</th>
                      <th>Producto</th>
                      <!-- <th>Precio</th> -->
                      <th>Subtotal</th>
                      <th>Cantidad</th>
                      <th>Forma de Pago</th>
                      <th>Descuentos Aplicados</th>
                    </tr>
                  </thead>
                  <tbody><?php
                    $pdo = Database::connect();
                    $sql = "SELECT d.fecha_hora, p.codigo, p.descripcion, p.precio, vd.subtotal, vd.cantidad as cantidad_producto,v.id_descuento_aplicado, de.descripcion as descuento_aplicado, fp.forma_pago, d.total, vd.id_venta FROM devoluciones_detalle dd INNER JOIN devoluciones d ON d.id = dd.id_devolucion INNER JOIN ventas_detalle vd ON vd.id = dd.id_venta_detalle INNER JOIN productos p ON p.id = vd.id_producto INNER JOIN ventas v ON v.id = vd.id_venta LEFT JOIN descuentos de ON de.id = v.id_descuento_aplicado INNER JOIN forma_pago fp ON fp.id = v.id_forma_pago WHERE d.id = ". $data['devolucion_id'];
                    $q = $pdo->prepare($sql);
                    $q->execute();
                    //var_dump($pdo->errorInfo());
                    $devoluciones = $q->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($devoluciones as $data2) {?>
                      <tr>
                        <td><?=date("d/m/Y", strtotime($data2['fecha_hora'])); ?></td>
                        <td><a href="verVenta.php?id=<?=$data2['id_venta']?>" target="_blank"><img src="img/eye.png" width="24" height="15" border="0"  alt="Ver" title="Ver Venta"></a> V#<?=$data2['id_venta']?></td>
                        <td><?="(" . $data2["codigo"] . ") " . $data2["descripcion"]; ?></td>
                        <!-- <td>$<?=number_format($data2["precio"], 2, ",", ".");?></td> -->
                        <td>$<?=number_format($data2["subtotal"], 2, ",", ".");?></td>
                        <td><?=$data2["cantidad_producto"]; ?></td>
                        <td><?=$data2["forma_pago"]; ?></td>
                        <td><?php
                          $descuentos_aplicados = '';
                          if ($data2['id_descuento_aplicado'] == NULL || $data2['id_descuento_aplicado'] == 0) {
                            $descuentos_aplicados = 'Sin descuentos aplicados';
                          } else {
                            $descuentos_aplicados = $data2['descuento_aplicado'];
                          }
                          echo $descuentos_aplicados;?>
                        </td>
                      </tr><?php 
                    }
                    $sql = "SELECT d.fecha_hora, p.codigo, p.descripcion, p.precio, cd.subtotal, cd.cantidad as cantidad_producto,v.id_descuento_aplicado, de.descripcion as descuento_aplicado, d.total, cd.id_canje FROM devoluciones_detalle dd INNER JOIN devoluciones d ON d.id = dd.id_devolucion INNER JOIN canjes_detalle cd ON cd.id = dd.id_canje_detalle INNER JOIN productos p ON p.id = cd.id_producto INNER JOIN canjes v ON v.id = cd.id_canje LEFT JOIN descuentos de ON de.id = v.id_descuento_aplicado WHERE d.id = ". $data['devolucion_id'];
                    //echo $sql;
                    $q = $pdo->prepare($sql);
                    $q->execute();
                    $error=$q->errorInfo();
                    if(isset($error[2])) echo $error[2]."<br>";
                    //var_dump();
                    $devoluciones = $q->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($devoluciones as $data2) {?>
                      <tr>
                        <td><?=date("d/m/Y", strtotime($data2['fecha_hora'])); ?></td>
                        <td><a href="verCanje.php?id=<?=$data2['id_canje']?>" target="_blank"><img src="img/eye.png" width="24" height="15" border="0"  alt="Ver" title="Ver Canje"></a> C#<?=$data2['id_canje']?></td>
                        <td><?="(" . $data2["codigo"] . ") " . $data2["descripcion"]; ?></td>
                        <!-- <td>$<?=number_format($data2["precio"], 2, ",", ".");?></td> -->
                        <td>$<?=number_format($data2["subtotal"], 2, ",", ".");?></td>
                        <td><?=$data2["cantidad_producto"]; ?></td>
                        <td>Canje</td>
                        <td><?php
                          $descuentos_aplicados = '';
                          if ($data2['id_descuento_aplicado'] == NULL || $data2['id_descuento_aplicado'] == 0) {
                            $descuentos_aplicados = 'Sin descuentos aplicados';
                          } else {
                            $descuentos_aplicados = $data2['descuento_aplicado'];
                          }
                          echo $descuentos_aplicados;?>
                        </td>
                      </tr><?php 
                    }
                    Database::disconnect();?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Total Devolucion</label>
            <div class="col-sm-9">$<?php echo number_format($data2['total'],2); ?></div>
          </div><?php
        }?>
        
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Total pagado</label>
          <div class="col-sm-9">$<?php echo number_format($data['total_con_descuento'],2); ?></div>
        </div><?php
        if($data['tipo_comprobante']!="R"){
          $estado=get_estado_comprobante($data['estado']);
          $cbte=format_numero_comprobante($data['punto_venta'],$data['numero_comprobante']);
          $tipo_cbte=get_nombre_comprobante($data["tipo_comprobante"]);?>
          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Tipo de comprobante</label>
            <div class="col-sm-9"><?=$tipo_cbte?></div>
          </div>
          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Estado</label>
            <div class="col-sm-9"><?php
            $class="";
            if($data['estado']=="A"){
              $class="badge badge-success";
            }
            if($data['estado']=="R" or $data['estado']=="E"){
              $class="badge badge-danger";
            }?>
            <span class="<?=$class?>"><?=$estado?></span></div>
          </div>
          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Nro. de comprobante</label>
            <div class="col-sm-9"><?=$cbte?></div>
          </div>
          <div class="form-group row">
            <label class="col-sm-3 col-form-label">CAE</label>
            <div class="col-sm-9"><?=$data['cae']?></div>
          </div>
          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Fecha vencimiento CAE</label>
            <div class="col-sm-9"><?=$data['fecha_vencimiento_cae']?></div>
          </div><?php
          if(!is_null($data["id_venta_cbte_relacionado"])){?>
            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Comprobante relacionado</label>
              <div class="col-sm-9">
                <a href="verVenta.php?id=<?=$data["id_venta_cbte_relacionado"]?>" target="_blank" title="Ver Comprobante relacionado">
                  <img src="img/eye.png" width="24" height="15" border="0" alt="Ver Venta">
                  <?=$data['id_venta_cbte_relacionado']?>
                </a>
              </div>
            </div><?php
          }
        }?>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalGenerarNC" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Confirmación</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        </div>
        <div class="modal-body">¿Está seguro que desea generar un Nota de Crédito para esta factura?</div>
        <div class="modal-footer">
          <a href="generarNC.php?id=<?=$id?>" class="btn btn-primary" id="btnConfirmGenerarNC">Generar</a>
          <button data-dismiss="modal" class="btn btn-light">Volver</button>
        </div>
      </div>
    </div>
  </div>

  <div class="card-footer">
    <div class="col-sm-9 offset-sm-3"><?php
      if(isset($_POST["modal"])){?>
        <!-- si se muestra desde un modal ofrecemos la opcion de ir a la venta y de cerrar el modal -->
        <a href='verVenta.php?id=<?=$id?>' target="_blank" class='btn btn-primary'>Ir a la venta</a>
        <button type='button' class='btn btn-light' data-dismiss='modal'>Cerrar</button><?php
      }else{
        //si se muestra desde la ventas, se da la opcion de generar NC si es el caso o de volver al listado de ventas
        if($data["tipo_comprobante"]!="R"){
          if($data["estado"]=="A" and is_null($data["id_venta_cbte_relacionado"])){?>
            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalGenerarNC">Generar NC</button><?php
          }elseif($data["tipo_comprobante"]!="R" and $data["estado"]!="A"){?>
            <a href='informarFacturaAFIP.php?id=<?=$id?>' class='btn btn-danger'>Informar a AFIP</a><?php
          }
        }?>
        <a href='<?=$link_volver?>' class='btn btn-light'>Volver</a><?php
      }?>
    </div>
  </div>
</div>