<?php
    require("config.php");
    if(empty($_SESSION['user']))
    {
        header("Location: index.php");
        die("Redirecting to index.php"); 
    }
	
	require 'database.php';

	$id = null;
	if ( !empty($_GET['id'])) {
		$id = $_REQUEST['id'];
	}
	
	if ( null==$id ) {
		header("Location: listarVentas.php");
	}
	
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
	$sql = "UPDATE movimientos_caja set anulado = 1 where id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($id));
	
	Database::disconnect();
		
	header("Location: listarCajaGrande.php");
	
?>