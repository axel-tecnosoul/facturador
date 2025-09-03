<?php 
include 'config.php';
include 'database.php';
$pdo = Database::connect();
$sql = " SELECT id FROM `productos` WHERE cb = '' ";
foreach ($pdo->query($sql) as $row) {
	$cb = microtime(true)*10000;
	$sql = "update `productos` set `cb` = ? where id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($cb,$row[0]));
	sleep(1);
}
Database::disconnect();
?>
                       