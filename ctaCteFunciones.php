<?php

function obtenerIpcAcumulado($pdo, $mesHasta, $cantidadMeses) {
  $sql = "SELECT porcentaje FROM ipc_mensual WHERE periodo <= ? ORDER BY periodo DESC LIMIT ".intval($cantidadMeses);
  $q = $pdo->prepare($sql);
  $q->execute([$mesHasta]);
  $porcentajes = $q->fetchAll(PDO::FETCH_COLUMN);

  if (count($porcentajes) != $cantidadMeses) {
    return null;
  }

  $factor = 1;
  foreach ($porcentajes as $porcentaje) {
    $factor *= 1 + ($porcentaje / 100);
  }

  return ($factor - 1) * 100;
}

function calcularPrecioAjustado($precio, $porcentaje, $redondeo = 0) {
  $nuevoPrecio = $precio * (1 + ($porcentaje / 100));
  if ($redondeo > 0) {
    $nuevoPrecio = ceil($nuevoPrecio / $redondeo) * $redondeo;
  }
  return $nuevoPrecio;
}

function obtenerSaldoCliente($pdo, $idCliente, $moneda = 'USD') {
  $sql = "SELECT COALESCE(SUM(debe-haber),0) saldo FROM cliente_cta_cte_movimiento WHERE id_cliente = ? AND moneda = ?";
  $q = $pdo->prepare($sql);
  $q->execute([$idCliente, $moneda]);
  return (float)$q->fetchColumn();
}

function siguienteMesAjuste($mesActual, $frecuencia) {
  $fecha = new DateTime($mesActual);
  $fecha->modify('+'.intval($frecuencia).' months');
  return $fecha->format('Y-m-01');
}
