<?php
	include 'database.php';
  $aProductos=[];
	if(!empty($_GET["almacen"])) {
			$pdo = Database::connect();
			$sql = " SELECT s.`id`, p.`codigo`, c.`categoria`, p.`descripcion`, p.`precio`, s.`cantidad`, p.`cb` FROM `stock` s inner join productos p on p.id = s.id_producto inner join categorias c on c.id = p.id_categoria WHERE s.`cantidad` > 0 and p.`activo` = 1 and s.`id_almacen` = ".$_GET["almacen"];
			
			foreach ($pdo->query($sql) as $row) {
        $aProductos[]=[
				  "cb"=>$row[6],
				  "codigo"=>$row[1],
				  "categoria"=>$row[2],
				  "descripcion"=>$row[3],
				  "precio"=>'$'. number_format($row[4],2),
				  "cantidad"=>$row[5],
          "id_producto"=>$row[0],
				  "input"=>'<input type="number" name="cantidad_'.$row[0].'" id="cantidad_'.$row[0].'" min="0" max="'.$row[5].'" value="0" />',
        ];
				/*echo '<tr>';
				echo '<td>'. $row[6] . '</td>';
				echo '<td>'. $row[1] . '</td>';
				echo '<td>'. $row[2] . '</td>';
				echo '<td>'. $row[3] . '</td>';
				echo '<td>$'. number_format($row[4],2) . '</td>';
				echo '<td>'. $row[5] . '</td>';
				echo '<td>';
				echo '<input type="number" name="cantidad_'.$row[0].'" id="cantidad_'.$row[0].'" min="0" max="'.$row[5].'" value="0" />';
				echo '</td>';
				echo '</tr>';*/
		   }
		   Database::disconnect();
	}
  echo json_encode($aProductos);