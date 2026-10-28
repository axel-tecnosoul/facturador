<?php
session_start();
if (empty($_SESSION['user'])) {
  header('Location: index.php');
  die();
}
require 'database.php';
require 'ipcFunciones.php';
$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$mensaje = '';
$error = '';

try {
  if (!empty($_POST['accion']) && $_POST['accion'] === 'guardar_ipc') {
    $periodo = $_POST['periodo'].'-01';
    $q = $pdo->prepare('INSERT INTO ipc_mensual (periodo,porcentaje) VALUES (?,?) ON DUPLICATE KEY UPDATE porcentaje=VALUES(porcentaje)');
    $q->execute([$periodo,(float)$_POST['porcentaje']]);
    $mensaje = 'IPC guardado.';
  }
  if (!empty($_POST['accion']) && $_POST['accion'] === 'confirmar_ajustes') {
    $periodo = $_POST['periodo'].'-01';
    $ids = !empty($_POST['servicios']) ? $_POST['servicios'] : [];
    $pdo->beginTransaction();
    foreach ($ids as $id) {
      $q = $pdo->prepare('SELECT * FROM cliente_servicio WHERE id_cliente_servicio=? AND activo=1');
      $q->execute([(int)$id]);
      $servicio = $q->fetch(PDO::FETCH_ASSOC);
      if (!$servicio || $servicio['mes_proximo_ajuste'] !== $periodo) continue;
      $q = $pdo->prepare('SELECT porcentaje FROM ipc_mensual WHERE periodo<=? ORDER BY periodo DESC LIMIT '.intval($servicio['frecuencia_ajuste_meses']));
      $q->execute([$periodo]);
      $porcentajes = array_reverse($q->fetchAll(PDO::FETCH_COLUMN));
      if (count($porcentajes) != intval($servicio['frecuencia_ajuste_meses'])) throw new Exception('Faltan IPC para '.$servicio['descripcion']);
      $nuevo = calcularPrecioIpc($servicio['precio_actual'],$porcentajes,$servicio['redondeo']);
      $ipcAcum = ipcPorcentajeCompuesto($porcentajes);
      $q = $pdo->prepare("INSERT INTO cliente_servicio_precio (id_cliente_servicio,vigencia_desde,precio_anterior,porcentaje_aplicado,precio_nuevo,origen,detalle) VALUES (?,?,?,?,?,'IPC',?)");
      $q->execute([$id,$periodo,$servicio['precio_actual'],$ipcAcum,$nuevo,'Ajuste confirmado desde Precios IPC']);
      $siguiente = (new DateTime($periodo))->modify('+'.intval($servicio['frecuencia_ajuste_meses']).' months')->format('Y-m-01');
      $q = $pdo->prepare('UPDATE cliente_servicio SET precio_actual=?,mes_proximo_ajuste=? WHERE id_cliente_servicio=?');
      $q->execute([$nuevo,$siguiente,$id]);
    }
    $pdo->commit();
    $mensaje = 'Ajustes aplicados y guardados en el historial.';
  }
} catch (Exception $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  $error = $e->getMessage();
}

$periodo = !empty($_GET['periodo']) ? $_GET['periodo'] : date('Y-m');
$periodoSql = $periodo.'-01';
$ipc = $pdo->query('SELECT periodo,porcentaje FROM ipc_mensual ORDER BY periodo')->fetchAll(PDO::FETCH_ASSOC);
$q = $pdo->prepare("SELECT cs.*,COALESCE(NULLIF(c.nombre,''),c.razon_social) cliente FROM cliente_servicio cs INNER JOIN clientes c ON c.id=cs.id_cliente WHERE cs.activo=1 ORDER BY cliente,cs.orden,cs.descripcion");
$q->execute();
$servicios = $q->fetchAll(PDO::FETCH_ASSOC);
$propuestas = [];
foreach ($servicios as $s) {
  if ($s['mes_proximo_ajuste'] !== $periodoSql || empty($s['frecuencia_ajuste_meses'])) continue;
  $q = $pdo->prepare('SELECT periodo,porcentaje FROM ipc_mensual WHERE periodo<=? ORDER BY periodo DESC LIMIT '.intval($s['frecuencia_ajuste_meses']));
  $q->execute([$periodoSql]);
  $filas = array_reverse($q->fetchAll(PDO::FETCH_ASSOC));
  if (count($filas) != intval($s['frecuencia_ajuste_meses'])) {
    $propuestas[] = ['servicio'=>$s,'error'=>'Faltan IPC del período'];
    continue;
  }
  $porcentajes = array_column($filas,'porcentaje');
  $propuestas[] = ['servicio'=>$s,'ipc'=>ipcPorcentajeCompuesto($porcentajes),'nuevo'=>calcularPrecioIpc($s['precio_actual'],$porcentajes,$s['redondeo'])];
}
?>
<!DOCTYPE html><html lang="es"><head><?php include 'head_tables.php'; ?></head><body class="light-only"><div class="page-wrapper">
<?php include 'header.php'; ?><div class="page-body-wrapper"><?php include 'menu.php'; ?><div class="page-body"><div class="container-fluid"><div class="page-header"><h3><?php include 'title.php'; ?></h3></div>
<?php if($mensaje){?><div class="alert alert-success"><?=htmlspecialchars($mensaje)?></div><?php } if($error){?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php } ?>
<div class="row"><div class="col-xl-9"><div class="card"><div class="card-header"><h5>Precios según IPC</h5></div><div class="card-body">
<form method="get" class="form-inline mb-3"><label class="mr-2">Mes a revisar</label><input type="month" name="periodo" value="<?=htmlspecialchars($periodo)?>" class="form-control mr-2"><button class="btn btn-light">Ver</button></form>
<form method="post"><input type="hidden" name="accion" value="confirmar_ajustes"><input type="hidden" name="periodo" value="<?=htmlspecialchars($periodo)?>"><div class="table-responsive"><table class="table table-bordered table-sm"><thead><tr><th></th><th>Cliente</th><th>Servicio</th><th>Frecuencia</th><th>Precio actual</th><th>IPC acumulado</th><th>Redondeo</th><th>Precio propuesto</th></tr></thead><tbody>
<?php foreach($propuestas as $p){$s=$p['servicio'];?><tr><td><?php if(empty($p['error'])){?><input type="checkbox" name="servicios[]" value="<?=$s['id_cliente_servicio']?>" checked><?php }?></td><td><?=htmlspecialchars($s['cliente'])?></td><td><?=htmlspecialchars($s['descripcion'])?></td><td><?=$s['frecuencia_ajuste_meses']?> mes/es</td><td class="text-right">$ <?=number_format($s['precio_actual'],0,',','.')?></td><td class="text-right"><?=isset($p['ipc'])?number_format($p['ipc'],2,',','.').' %':htmlspecialchars($p['error'])?></td><td class="text-right">$ <?=number_format($s['redondeo'],0,',','.')?></td><td class="text-right font-weight-bold"><?=isset($p['nuevo'])?'$ '.number_format($p['nuevo'],0,',','.'):'-'?></td></tr><?php } ?>
<?php if(!$propuestas){?><tr><td colspan="8" class="text-center">No hay servicios a actualizar en este mes.</td></tr><?php }?></tbody></table></div><?php if($propuestas){?><button class="btn btn-primary" type="submit">Confirmar seleccionados</button><?php }?></form>
</div></div></div><div class="col-xl-3"><div class="card"><div class="card-header"><h5>IPC mensual</h5></div><div class="card-body"><form method="post"><input type="hidden" name="accion" value="guardar_ipc"><div class="form-group"><label>Mes</label><input type="month" name="periodo" class="form-control" required></div><div class="form-group"><label>IPC %</label><input type="number" step="0.01" name="porcentaje" class="form-control" required></div><button class="btn btn-primary btn-sm">Guardar</button></form><hr><table class="table table-sm"><tbody><?php foreach(array_reverse($ipc) as $i){?><tr><td><?=date('m/Y',strtotime($i['periodo']))?></td><td class="text-right"><?=number_format($i['porcentaje'],2,',','.')?>%</td></tr><?php }?></tbody></table></div></div></div></div>
</div></div><?php include 'footer.php'; ?></div></div><script src="assets/js/jquery-3.2.1.min.js"></script><script src="assets/js/bootstrap/popper.min.js"></script><script src="assets/js/bootstrap/bootstrap.js"></script><script src="assets/js/icons/feather-icon/feather.min.js"></script><script src="assets/js/icons/feather-icon/feather-icon.js"></script><script src="assets/js/sidebar-menu.js"></script><script src="assets/js/config.js"></script><script src="assets/js/script.js"></script></body></html>
