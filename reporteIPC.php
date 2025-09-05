<?php
include_once("config.php");
if(empty($_SESSION['user'])){
    header("Location: index.php");
    die("Redirecting to index.php");
}
require_once "database.php";

$hoy = date("Y-m-d");
$desde = isset($_GET['desde']) ? $_GET['desde'] : date("Y-m-01", strtotime($hoy." -1 month"));
$hasta = isset($_GET['hasta']) ? $_GET['hasta'] : $hoy;
$id_cliente = isset($_GET['id_cliente']) ? $_GET['id_cliente'] : '';

function calcularPrecioActual($pdo, $precio_base, $fecha_base, $fecha_fin){
    if(!$precio_base || !$fecha_base) return 0;
    $factor = 1;
    $inicio = new DateTime($fecha_base);
    $fin = new DateTime($fecha_fin);
    if($fin > $inicio){
        $inicio->modify('first day of next month');
        while($inicio <= $fin){
            $periodo = $inicio->format('Y-m-01');
            $stmt = $pdo->prepare("SELECT porcentaje FROM ipc_historial WHERE periodo = ?");
            $stmt->execute([$periodo]);
            $ipc = $stmt->fetch(PDO::FETCH_ASSOC);
            if($ipc){
                $factor *= (1 + $ipc['porcentaje']/100);
            }
            $inicio->modify('first day of next month');
        }
    }
    return round($precio_base * $factor, 2);
}
$pdo = Database::connect();

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT c.id, c.razon_social, c.precio_base, c.fecha_base, SUM(co.horas) AS horas, SUM(co.monto_pesos) AS monto
        FROM clientes c
        LEFT JOIN cobros co ON co.id_cliente = c.id AND co.fecha_factura BETWEEN ? AND ?";
$params = [$desde, $hasta];
if($id_cliente){
    $sql .= " WHERE c.id = ?";
    $params[] = $id_cliente;
}
$sql .= " GROUP BY c.id, c.razon_social, c.precio_base, c.fecha_base ORDER BY c.razon_social";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <?php include('head_forms.php');?>
  </head>
  <body class="light-only">
    <div class="page-wrapper">
      <?php include('header.php');?>
      <div class="page-body-wrapper">
        <?php include('menu.php');?>
        <div class="page-body">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-12">
                <div class="card">
                  <div class="card-header">
                    <h5>Reporte IPC</h5>
                  </div>
                  <div class="card-body">
                    <form class="form-inline" method="get" action="reporteIPC.php">
                      <label class="mr-2">Desde:</label>
                      <input type="date" name="desde" value="<?=$desde?>" class="form-control mr-3">
                      <label class="mr-2">Hasta:</label>
                      <input type="date" name="hasta" value="<?=$hasta?>" class="form-control mr-3">
                      <label class="mr-2">Cliente:</label>
                      <select name="id_cliente" class="form-control mr-3">
                        <option value="">Todos</option>
                        <?php
                        foreach($pdo->query("SELECT id, razon_social FROM clientes ORDER BY razon_social") as $c){
                            $sel = ($id_cliente == $c['id']) ? 'selected' : '';
                            echo "<option value='{$c['id']}' $sel>{$c['razon_social']}</option>";
                        }
                        ?>
                      </select>
                      <button type="submit" class="btn btn-primary">Filtrar</button>
                    </form>
                    <div class="table-responsive mt-4">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th>Cliente</th>
                            <th>Precio Base</th>
                            <th>Precio Actual</th>
                            <th>Horas</th>
                            <th>Monto</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $totalHoras = 0;
                          $totalMonto = 0;
                          foreach($rows as $row){
                              $precioActual = calcularPrecioActual($pdo, $row['precio_base'], $row['fecha_base'], $hasta);
                              $horas = $row['horas'] ? $row['horas'] : 0;
                              $monto = $row['monto'] ? $row['monto'] : 0;
                              $totalHoras += $horas;
                              $totalMonto += $monto;
                              echo '<tr>';
                              echo '<td>'.htmlspecialchars($row['razon_social']).'</td>';
                              echo '<td class="text-right">'.number_format($row['precio_base'],2,',','.').'</td>';
                              echo '<td class="text-right">'.number_format($precioActual,2,',','.').'</td>';
                              echo '<td class="text-right">'.number_format($horas,2,',','.').'</td>';
                              echo '<td class="text-right">'.number_format($monto,2,',','.').'</td>';
                              echo '</tr>';
                          }
                          ?>
                        </tbody>
                        <tfoot>
                          <tr>
                            <th colspan="3" class="text-right">Totales</th>
                            <th class="text-right"><?=number_format($totalHoras,2,',','.')?></th>
                            <th class="text-right"><?=number_format($totalMonto,2,',','.')?></th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php Database::disconnect(); include("footer.php"); ?>
        </div>
      </div>
    </div>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/bootstrap/popper.min.js"></script>
    <script src="assets/js/bootstrap/bootstrap.js"></script>
    <script src="assets/js/icons/feather-icon/feather.min.js"></script>
    <script src="assets/js/icons/feather-icon/feather-icon.js"></script>
    <script src="assets/js/sidebar-menu.js"></script>
    <script src="assets/js/config.js"></script>
  </body>
</html>
