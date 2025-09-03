<?php
include 'database.php';
$aProductos = [];
if (!empty($_GET["almacen"])) {
  $pdo = Database::connect();
  $sql = " SELECT s.id, p.codigo, c.categoria, p.descripcion, s.cantidad, p.cb FROM stock s inner join productos p on p.id = s.id_producto inner join almacenes a on a.id = s.id_almacen inner join categorias c on c.id = p.id_categoria WHERE s.cantidad > 0 and p.activo = 1 and s.id_almacen = " . $_GET["almacen"];
  //var_dump($sql);
  
  foreach ($pdo->query($sql) as $row) {
    $aProductos[] = [
      "cb" => $row[5],
      "codigo" => $row[1],
      "categoria" => $row[2],
      "descripcion" => $row[3],
      //"precio"=>'$'. number_format($row[4],2),
      "cantidad" => $row[4],
      "id_producto" => $row[0],
      "input" => '<input type="number" name="cantidad_' . $row[0] . '" id="cantidad_' . $row[0] . '" min="0" max="' . $row[5] . '" value="0" />',
    ];
    /*echo '<tr>';
				echo '<td>'. $row[5] . '</td>';
				echo '<td>'. $row[1] . '</td>';
				echo '<td>'. $row[2] . '</td>';
				echo '<td>'. $row[3] . '</td>';
				echo '<td>'. $row[4] . '</td>';
				echo '<td>';
				echo '<input type="number" name="traspasar_'.$row[0].'" id="traspasar_'.$row[0].'" value="0" min="0" max="'.$row[4].'" />';
				echo '</td>';
				echo '</tr>';*/
  }
  Database::disconnect();
}
echo json_encode($aProductos);
