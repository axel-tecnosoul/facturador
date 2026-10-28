<?php
session_start();
if (empty($_SESSION['user'])) {
  header("Location: index.php");
  die("Redirecting to index.php");
}
include("database.php");
include("ctaCteFunciones.php");

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$mensaje = '';
$error = '';

if (!empty($_POST['accion'])) {
  try {
    if ($_POST['accion'] == 'guardar_ipc') {
      $periodo = $_POST['periodo'].'-01';
      $sql = "INSERT INTO ipc_mensual (periodo, porcentaje) VALUES (?, ?) ON DUPLICATE KEY UPDATE porcentaje = VALUES(porcentaje)";
      $q = $pdo->prepare($sql);
      $q->execute([$periodo, $_POST['porcentaje']]);
      $mensaje = 'IPC guardado correctamente.';
    }

    if ($_POST['accion'] == 'guardar_movimiento') {
      $sql = "INSERT INTO cliente_cta_cte_movimiento (id_cliente, fecha, observaciones, moneda, debe, haber, pesos_recibidos, cotizacion_usd) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
      $q = $pdo->prepare($sql);
      $q->execute([
        $_POST['id_cliente'], $_POST['fecha'], trim($_POST['observaciones']), $_POST['moneda'],
        $_POST['debe'] ?: 0, $_POST['haber'] ?: 0, $_POST['pesos_recibidos'] ?: null, $_POST['cotizacion_usd'] ?: null
      ]);
      $mensaje = 'Movimiento guardado correctamente.';
    }
  } catch (Exception $e) {
    $error = $e->getMessage();
  }
}

$clientes = $pdo->query("SELECT id, nombre, razon_social FROM clientes WHERE activo = 1 ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$ipc = $pdo->query("SELECT * FROM ipc_mensual ORDER BY periodo DESC")->fetchAll(PDO::FETCH_ASSOC);
$idCliente = !empty($_GET['id_cliente']) ? intval($_GET['id_cliente']) : (!empty($clientes) ? $clientes[0]['id'] : 0);

$movimientos = [];
if ($idCliente) {
  $sql = "SELECT * FROM cliente_cta_cte_movimiento WHERE id_cliente = ? ORDER BY fecha, id_movimiento";
  $q = $pdo->prepare($sql);
  $q->execute([$idCliente]);
  $movimientos = $q->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <?php include('head_tables.php'); ?>
  <style>
    .planilla th { white-space: nowrap; background: #f1f3f5; }
    .planilla td { white-space: nowrap; }
    .saldo-positivo { font-weight: bold; }
    .ipc-tabla { max-height: 420px; overflow-y: auto; }
  </style>
</head>
<body class="light-only">
<div class="page-wrapper">
  <?php include('header.php'); ?>
  <div class="page-body-wrapper">
    <?php include('menu.php'); ?>
    <div class="page-body">
      <div class="container-fluid">
        <div class="page-header">
          <div class="row"><div class="col-12"><div class="page-header-left">
            <h3><?php include("title.php"); ?></h3>
            <ol class="breadcrumb"><li class="breadcrumb-item">Cuenta corriente</li></ol>
          </div></div></div>
        </div>
      </div>
      <div class="container-fluid">
        <?php if ($mensaje) { ?><div class="alert alert-success"><?=htmlspecialchars($mensaje)?></div><?php } ?>
        <?php if ($error) { ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php } ?>
        <div class="row">
          <div class="col-xl-9">
            <div class="card">
              <div class="card-header"><h5>Cta. Cte. Clientes</h5></div>
              <div class="card-body">
                <form method="get" class="form-inline mb-3">
                  <label class="mr-2">Cliente</label>
                  <select name="id_cliente" class="form-control mr-2" onchange="this.form.submit()">
                    <?php foreach ($clientes as $cliente) { ?>
                      <option value="<?=$cliente['id']?>" <?=$cliente['id']==$idCliente?'selected':''?>><?=htmlspecialchars($cliente['nombre'] ?: $cliente['razon_social'])?></option>
                    <?php } ?>
                  </select>
                </form>
                <div class="table-responsive">
                  <table class="table table-bordered table-sm planilla">
                    <thead><tr><th>Fecha</th><th>Observaciones</th><th>$$ recibidos</th><th>Cotización USD</th><th>Debe USD</th><th>Haber USD</th><th>Saldo USD</th></tr></thead>
                    <tbody>
                    <?php $saldo=0; foreach ($movimientos as $mov) { if ($mov['moneda']=='USD') $saldo += $mov['debe']-$mov['haber']; ?>
                      <tr>
                        <td><?=date('d/m/Y',strtotime($mov['fecha']))?></td>
                        <td><?=htmlspecialchars($mov['observaciones'])?></td>
                        <td class="text-right"><?=$mov['pesos_recibidos']!==null?'$ '.number_format($mov['pesos_recibidos'],2,',','.'):'-'?></td>
                        <td class="text-right"><?=$mov['cotizacion_usd']!==null?number_format($mov['cotizacion_usd'],2,',','.'):'-'?></td>
                        <td class="text-right"><?=$mov['debe']>0?number_format($mov['debe'],2,',','.'):'-'?></td>
                        <td class="text-right"><?=$mov['haber']>0?number_format($mov['haber'],2,',','.'):'-'?></td>
                        <td class="text-right saldo-positivo"><?=number_format($saldo,2,',','.')?></td>
                      </tr>
                    <?php } ?>
                    </tbody>
                  </table>
                </div>
                <hr>
                <form method="post">
                  <input type="hidden" name="accion" value="guardar_movimiento">
                  <input type="hidden" name="id_cliente" value="<?=$idCliente?>">
                  <div class="form-row">
                    <div class="form-group col-md-2"><label>Fecha</label><input type="date" name="fecha" class="form-control" required value="<?=date('Y-m-d')?>"></div>
                    <div class="form-group col-md-4"><label>Observaciones</label><input type="text" name="observaciones" class="form-control" required></div>
                    <div class="form-group col-md-2"><label>Moneda</label><select name="moneda" class="form-control"><option>USD</option><option>ARS</option></select></div>
                    <div class="form-group col-md-2"><label>Debe</label><input type="number" step="0.01" name="debe" class="form-control"></div>
                    <div class="form-group col-md-2"><label>Haber</label><input type="number" step="0.01" name="haber" class="form-control"></div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-3"><label>$$ recibidos</label><input type="number" step="0.01" name="pesos_recibidos" class="form-control"></div>
                    <div class="form-group col-md-3"><label>Cotización USD blue</label><input type="number" step="0.01" name="cotizacion_usd" class="form-control"></div>
                    <div class="form-group col-md-3 align-self-end"><button class="btn btn-primary">Agregar movimiento</button></div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="col-xl-3">
            <div class="card">
              <div class="card-header"><h5>IPC mensual</h5></div>
              <div class="card-body">
                <form method="post" class="mb-3">
                  <input type="hidden" name="accion" value="guardar_ipc">
                  <div class="form-group"><label>Mes</label><input type="month" name="periodo" class="form-control" required></div>
                  <div class="form-group"><label>IPC %</label><input type="number" name="porcentaje" step="0.01" class="form-control" required></div>
                  <button class="btn btn-primary btn-sm">Guardar IPC</button>
                </form>
                <div class="ipc-tabla">
                  <table class="table table-sm table-bordered"><thead><tr><th>Mes</th><th>IPC</th></tr></thead><tbody>
                  <?php foreach ($ipc as $fila) { ?><tr><td><?=date('m/Y',strtotime($fila['periodo']))?></td><td class="text-right"><?=number_format($fila['porcentaje'],2,',','.')?>%</td></tr><?php } ?>
                  </tbody></table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php include("footer.php"); ?>
  </div>
</div>
<script src="assets/js/jquery-3.2.1.min.js"></script>
<script src="assets/js/bootstrap/popper.min.js"></script>
<script src="assets/js/bootstrap/bootstrap.js"></script>
<script src="assets/js/icons/feather-icon/feather.min.js"></script>
<script src="assets/js/icons/feather-icon/feather-icon.js"></script>
<script src="assets/js/sidebar-menu.js"></script>
<script src="assets/js/config.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
