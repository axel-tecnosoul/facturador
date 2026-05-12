<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

require_once 'database.php';
require_once 'vendor/afip/Afip.php';
require_once 'config_facturacion_electronica.php';

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function normalizarCuit($value)
{
    return preg_replace('/\D+/', '', trim((string)$value));
}

$cuitAxel=20351290340;
$cuitGaby=27332168970;
$cuitFavio=20228728943;

$aInitializeAFIP['CUIT']=$cuitAxel;//CUIT de la empresa que factura (11 caracteres)

$afip = new Afip($aInitializeAFIP);

$serverStatus = null;
$serverStatusError = null;
try {
  $serverStatus = $afip->RegisterScopeThirteen->GetServerStatus();
} catch (Exception $e) {
    $serverStatusError = $e->getMessage();
}

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT id, nombre, razon_social, cuit, pide_factura FROM clientes ORDER BY id";
$q = $pdo->prepare($sql);
$q->execute();
$clientes = $q->fetchAll(PDO::FETCH_ASSOC);

$resultados = [];

foreach ($clientes as $cliente) {
    $cuitNormalizado = normalizarCuit($cliente['cuit'] ?? '');

    $estado = 'OK';
    $detalle = '';
    $razonSocialAfip = '';

    if ($cuitNormalizado === '' || strlen($cuitNormalizado) !== 11) {
        $estado = 'OMITIDO';
        $detalle = 'CUIT vacio o invalido';
    } else {
        try {
          $persona = $afip->RegisterScopeThirteen->GetTaxpayerDetails((int)$cuitNormalizado);

            if (!$persona) {
                $estado = 'SIN DATOS';
                $detalle = 'AFIP no devolvio persona';
            } else {
                if (!empty($persona->razonSocial)) {
                    $razonSocialAfip = (string)$persona->razonSocial;
                } else {
                    $nombre = trim((string)($persona->nombre ?? ''));
                    $apellido = trim((string)($persona->apellido ?? ''));
                    $razonSocialAfip = trim($apellido . ' ' . $nombre);
                }

                if ($razonSocialAfip === '') {
                    $estado = 'SIN DATOS';
                    $detalle = 'No vino razonSocial ni nombre/apellido';
                }
            }
        } catch (Exception $e) {
            $estado = 'ERROR';
            $detalle = $e->getMessage();
        }
    }

    $resultados[] = [
        'id' => $cliente['id'],
        'nombre' => $cliente['nombre'],
        'razon_social_actual' => $cliente['razon_social'],
        'cuit' => $cliente['cuit'],
        'cuit_normalizado' => $cuitNormalizado,
        'pide_factura' => !empty($cliente['pide_factura']) ? 'Si' : 'No',
        'razon_social_afip' => $razonSocialAfip,
        'estado' => $estado,
        'detalle' => $detalle
    ];
}

Database::disconnect();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Testing AFIP Clientes</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 16px; }
    table { border-collapse: collapse; width: 100%; font-size: 13px; }
    th, td { border: 1px solid #ccc; padding: 6px; text-align: left; vertical-align: top; }
    th { background: #f5f5f5; }
    .ok { color: #0f7a0f; font-weight: 600; }
    .error { color: #b71c1c; font-weight: 600; }
    .omitido { color: #7a5f00; font-weight: 600; }
  </style>
</head>
<body>
  <h2>Testing AFIP - Clientes</h2>
  <p>Clientes evaluados: <?php echo count($resultados); ?></p>

  <h3>Estado servicio Padron A13</h3>
  <?php if ($serverStatusError !== null) { ?>
    <p class="error">Error: <?php echo e($serverStatusError); ?></p>
  <?php } else { ?>
    <pre><?php echo e(print_r($serverStatus, true)); ?></pre>
  <?php } ?>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Razon social actual</th>
        <th>CUIT</th>
        <th>CUIT normalizado</th>
        <th>Pide factura</th>
        <th>Razon social AFIP</th>
        <th>Estado</th>
        <th>Detalle</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($resultados as $r) {
        $estadoClass = '';
        if ($r['estado'] === 'OK') {
            $estadoClass = 'ok';
        } elseif ($r['estado'] === 'ERROR') {
            $estadoClass = 'error';
        } elseif ($r['estado'] === 'OMITIDO') {
            $estadoClass = 'omitido';
        }
      ?>
        <tr>
          <td><?php echo e($r['id']); ?></td>
          <td><?php echo e($r['nombre']); ?></td>
          <td><?php echo e($r['razon_social_actual']); ?></td>
          <td><?php echo e($r['cuit']); ?></td>
          <td><?php echo e($r['cuit_normalizado']); ?></td>
          <td><?php echo e($r['pide_factura']); ?></td>
          <td><?php echo e($r['razon_social_afip']); ?></td>
          <td class="<?php echo e($estadoClass); ?>"><?php echo e($r['estado']); ?></td>
          <td><?php echo e($r['detalle']); ?></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</body>
</html>
