<?php
require_once 'database.php';
require_once 'funciones.php';

if (empty($id) or !empty($_GET['id'])) {
  $id = $_REQUEST['id'];
}

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//$sql = "SELECT mcg.monto,fp.forma_pago,u.usuario,msc.motivo,detalle,a.almacen,mcg.tipo_movimiento FROM movimientos_caja_grande mcg INNER JOIN almacenes a ON mcg.id_almacen=a.id INNER JOIN forma_pago fp ON mcg.id_forma_pago=fp.id INNER JOIN usuarios u ON mcg.id_usuario=u.id INNER JOIN motivos_salidas_caja msc ON mcg.id_motivo=msc.id WHERE mcg.id = ? ";
//$sql = "SELECT mc.fecha_hora,mc.monto,fp.forma_pago,u.usuario,msc.motivo,detalle,a.almacen,mc.tipo_movimiento,anulado FROM movimientos_caja mc INNER JOIN almacenes a ON mc.id_almacen_egreso=a.id INNER JOIN forma_pago fp ON mc.id_forma_pago=fp.id INNER JOIN usuarios u ON mc.id_usuario=u.id INNER JOIN motivos_salidas_caja msc ON mc.id_motivo=msc.id WHERE mc.id = ? ";
$sql = "SELECT mc.monto,fp.forma_pago,u.usuario,msc.motivo,detalle,(SELECT almacen FROM almacenes a WHERE mc.id_almacen_egreso=a.id) AS almacen_egreso,(SELECT almacen FROM almacenes a WHERE mc.id_almacen_corresponde=a.id) AS almacen_corresponde,fecha_hora,tipo_movimiento,mc.anulado FROM movimientos_caja mc INNER JOIN forma_pago fp ON mc.id_forma_pago=fp.id INNER JOIN usuarios u ON mc.id_usuario=u.id INNER JOIN motivos_salidas_caja msc ON mc.id_motivo=msc.id WHERE mc.id = ? ";
$q = $pdo->prepare($sql);
$q->execute(array($id));
$data = $q->fetch(PDO::FETCH_ASSOC);

Database::disconnect();?>

<div class="card mb-0">
  <div class="card-header">
    <h5>Ver movimiento de caja grande</h5>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col">
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Fecha</label>
          <div class="col-sm-9"><?=date("d-m-Y",strtotime($data["fecha_hora"]))?></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Hora</label>
          <div class="col-sm-9"><?=date("H:i",strtotime($data["fecha_hora"]))?></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Almacen egreso de Dinero</label>
          <div class="col-sm-9"><?=$data["almacen_egreso"]?></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Almacen correspondiente</label>
          <div class="col-sm-9"><?=$data["almacen_corresponde"]?></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Forma de pago</label>
          <div class="col-sm-9"><?=$data["forma_pago"]?></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Tipo de movimiento</label>
          <div class="col-sm-9"><?=$data["tipo_movimiento"]?></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Monto</label>
          <div class="col-sm-9">$<?=number_format($data["monto"],2,",",".")?></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Motivo</label>
          <div class="col-sm-9"><?=$data["motivo"]?></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Detalle</label>
          <div class="col-sm-9"><?=$data["detalle"]?></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Usuario</label>
          <div class="col-sm-9"><?=$data["usuario"]?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="card-footer">
    <div class="col-sm-9 offset-sm-3"><?php
      if(isset($_POST["modal"])){?>
        <!-- si se muestra desde un modal ofrecemos la opcion de ir a la venta y de cerrar el modal -->
        <a href='verMovimientoCajaGrande.php?id=<?=$id?>' target="_blank" class='btn btn-primary'>Ir al movimiento</a>
        <button type='button' class='btn btn-light' data-dismiss='modal'>Cerrar</button><?php
      }else{
        //si se muestra desde la ventas, se da la opcion de generar NC si es el caso o de volver al listado de ventas
        if($_SESSION["user"]["id_perfil"]=1 and $data["anulado"]==0){?>
          <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalAnularMovimiento">Anular</button>
          
          <div class="modal fade" id="modalAnularMovimiento" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Confirmación</h5>
                  <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">¿Está seguro que desea anular el movimiento?</div>
                <div class="modal-footer">
                  <a href="anularMovimientoCajaGrande.php?id=<?=$id?>" class="btn btn-primary">Anular</a>
                  <button data-dismiss="modal" class="btn btn-light">Volver</button>
                </div>
              </div>
            </div>
          </div><?php
        }?>
        
        <a href="listarCajaGrande.php" class="btn btn-light">Volver</a><?php
      }?>
    </div>
  </div>
</div>