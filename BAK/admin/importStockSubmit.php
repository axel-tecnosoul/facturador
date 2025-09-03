<?php 
	session_start(); 
	if(empty($_SESSION['user'])) {
		header("Location: index.php");
		die("Redirecting to index.php"); 
	}
    require("config.php");
	require 'database.php';
	
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	if ( !empty($_POST)) {
		if ($_FILES['csv']['size'] > 0) {
			$file = $_FILES['csv']['tmp_name']; 
			$handle = fopen($file,"r"); 
			$data = fgetcsv($handle,10000,",","'");
			do {
				if ($data[0]) {
					$sql2 = " SELECT id from productos where codigo = ? ";
					$q2 = $pdo->prepare($sql2);
					$q2->execute(array($data[0]));
					$data2 = $q2->fetch(PDO::FETCH_ASSOC);
					
					if (!empty($data2)) {
						$sql = "INSERT INTO `stock`(`id_producto`, `id_almacen`, `cantidad`, `id_modalidad`) VALUES (?,?,?,?) ";
						$q = $pdo->prepare($sql);
						$q->execute(array($data2['id'],$data[1],$data[2],$data[3]));
					}
					
				}
			} while ($data = fgetcsv($handle,10000,",","'")); 
		}
					
		header("Location: listarStock.php");	
	}
	
	Database::disconnect();
?>