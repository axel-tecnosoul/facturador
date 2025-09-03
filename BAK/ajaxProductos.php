<?php
	include 'database.php';
  $aProductos=[];
	if(!empty($_GET["proveedor"])) {
			$pdo = Database::connect();
			$sql = " SELECT p.id, p.codigo, c.categoria, p.descripcion, p.precio, p.cb FROM productos p inner join categorias c on c.id = p.id_categoria WHERE p.activo = 1 and p.id_proveedor = ".$_GET["proveedor"];
			
			foreach ($pdo->query($sql) as $row) {
        $aProductos[]=[
				  "cb"=>$row[5],
				  "codigo"=>$row[1],
				  "categoria"=>$row[2],
				  "descripcion"=>$row[3],
				  "precio"=>'$'. number_format($row[4],2),
				  //"cantidad"=>$row[5],
          "id_producto"=>$row[0],
				  "input"=>'<input type="number" name="cantidad_'.$row[0].'" id="cantidad_'.$row[0].'" min="0" max="'.$row[5].'" value="0" />',
        ];
				/*echo '<tr>';
				echo '<td>'. $row[5] . '</td>';
				echo '<td>'. $row[1] . '</td>';
				echo '<td>'. $row[2] . '</td>';
				echo '<td>'. $row[3] . '</td>';
				echo '<td>$'. number_format($row[4],2) . '</td>';
				echo '<td>';
				echo '<input type="number" name="cantidad_'.$row[0].'" id="cantidad_'.$row[0].'" value="0" />';
				echo '</td>';
				echo '</tr>';*/
		   }
		   Database::disconnect();
	}
  echo json_encode($aProductos);