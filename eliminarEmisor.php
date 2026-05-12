<?php
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';

if (!empty($_POST)) {
  $id = $_POST['id'];
  $action = $_POST['action'];
  
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  try {
    if ($action == 'desactivar') {
      // Verificar si el emisor tiene cobros asociados
      $sqlCheck = "SELECT COUNT(*) as count FROM cobros WHERE id_emisor = ?";
      $qCheck = $pdo->prepare($sqlCheck);
      $qCheck->execute(array($id));
      $result = $qCheck->fetch(PDO::FETCH_ASSOC);
      
      if ($result['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'No se puede desactivar el emisor porque tiene cobros asociados.']);
        Database::disconnect();
        exit;
      }
      
      $sql = "UPDATE emisores SET activo = 0 WHERE id = ?";
    } else {
      $sql = "UPDATE emisores SET activo = 1 WHERE id = ?";
    }
    
    $q = $pdo->prepare($sql);
    $q->execute(array($id));
    
    echo json_encode(['success' => true]);
    
  } catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
  }
  
  Database::disconnect();
}
?>