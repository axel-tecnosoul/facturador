<?php
require __DIR__.'/../ipcFunciones.php';
$data = json_decode(file_get_contents(__DIR__.'/fixtures/precios_ipc_planilla.json'), true);
$errores = array();
foreach ($data['casos_reales'] as $caso) {
  if (isset($caso['multiplicador'])) {
    $obtenido = $caso['precio_hora'] * $caso['multiplicador'];
  } else {
    $obtenido = calcularPrecioIpc($caso['precio_base'], $caso['ipc'], $caso['redondeo'], $caso['tipo_redondeo']);
  }
  if (abs($obtenido - $caso['esperado']) > 0.001) {
    $errores[] = $caso['nombre'];
  }
}
if (count($errores)) {
  echo 'FALLARON: '.implode(', ', $errores).PHP_EOL;
  exit(1);
}
echo 'OK: '.count($data['casos_reales']).' casos coinciden con la planilla.'.PHP_EOL;
