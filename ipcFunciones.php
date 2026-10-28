<?php

function ipcFactorCompuesto($porcentajes) {
  $factor = 1;
  foreach ($porcentajes as $porcentaje) {
    $factor *= 1 + ((float)$porcentaje / 100);
  }
  return $factor;
}

function ipcPorcentajeCompuesto($porcentajes) {
  return (ipcFactorCompuesto($porcentajes) - 1) * 100;
}

function redondearPrecioSuperior($valor, $multiplo) {
  $multiplo = (float)$multiplo;
  if ($multiplo <= 0) {
    return (float)$valor;
  }
  return ceil($valor / $multiplo) * $multiplo;
}

function redondearPrecioInferior($valor, $multiplo) {
  $multiplo = (float)$multiplo;
  if ($multiplo <= 0) {
    return (float)$valor;
  }
  return floor($valor / $multiplo) * $multiplo;
}

function calcularPrecioIpc($precioBase, $porcentajes, $redondeo = 0, $tipoRedondeo = 'superior') {
  $precio = (float)$precioBase * ipcFactorCompuesto($porcentajes);
  if ($tipoRedondeo === 'inferior') {
    return redondearPrecioInferior($precio, $redondeo);
  }
  return redondearPrecioSuperior($precio, $redondeo);
}

function obtenerIpcPeriodo($ipcPorPeriodo, $periodoHasta, $cantidadMeses) {
  ksort($ipcPorPeriodo);
  $periodos = array_keys($ipcPorPeriodo);
  $posicion = array_search($periodoHasta, $periodos, true);
  if ($posicion === false) {
    return null;
  }
  $inicio = $posicion - intval($cantidadMeses) + 1;
  if ($inicio < 0) {
    return null;
  }
  $resultado = [];
  for ($i = $inicio; $i <= $posicion; $i++) {
    $resultado[$periodos[$i]] = (float)$ipcPorPeriodo[$periodos[$i]];
  }
  return $resultado;
}

function calcularPropuestaServicio($servicio, $ipcPorPeriodo, $periodo) {
  $frecuencia = intval($servicio['frecuencia_meses']);
  $periodos = obtenerIpcPeriodo($ipcPorPeriodo, $periodo, $frecuencia);
  if ($periodos === null) {
    return ['estado' => 'ipc_incompleto'];
  }
  $precioNuevo = calcularPrecioIpc(
    $servicio['precio_base'],
    array_values($periodos),
    $servicio['redondeo'],
    !empty($servicio['tipo_redondeo']) ? $servicio['tipo_redondeo'] : 'superior'
  );
  return [
    'estado' => 'ok',
    'periodos' => $periodos,
    'ipc_acumulado' => ipcPorcentajeCompuesto(array_values($periodos)),
    'precio_base' => (float)$servicio['precio_base'],
    'precio_nuevo' => $precioNuevo
  ];
}
