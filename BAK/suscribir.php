<?php 
    require("admin/config.php");
	require("admin/database.php");
    
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$sql = "SELECT `id` from suscripciones where email = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($_POST['email']));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	if (empty($data)) {
		$sql = "INSERT INTO `suscripciones`(`email`, `fecha_hora`) VALUES (?,now())";
		$q = $pdo->prepare($sql);
		$q->execute(array($_POST['email']));

		Database::disconnect();		
	}
	header("Location: index.php");
?>