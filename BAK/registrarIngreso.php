<?php 
    require("config.php"); 
	require 'database.php';
    $submitted_username = ''; 
    if(!empty($_POST)){ 
        $query = "SELECT `id`, `usuario`, `clave`, `activo`, `id_perfil`, `id_almacen` FROM `usuarios` WHERE activo = 1 and usuario = :user"; 
        $query_params = array(':user' => $_POST['user']); 
        
        try{ 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
        $login_ok = false; 
        $row = $stmt->fetch(); 
        if($row){ 
            $check_pass = $_POST['pass']; 
            if($check_pass === $row['clave']){
                $login_ok = true;
            } 
        } 

        if($login_ok){ 
            unset($row['clave']); 
            $_SESSION['user'] = $row;  
			
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql2 = "SELECT `id` FROM `usuarios_asistencia` WHERE id_usuario = ? and date_format(fecha,'%d/%m/%Y') = date_format(now(),'%d/%m/%Y')";
			$q2 = $pdo->prepare($sql2);
			$q2->execute(array($_SESSION['user']['id']));
			$data = $q2->fetch(PDO::FETCH_ASSOC);
			if (empty($data)) {
				$sql = "INSERT INTO `usuarios_asistencia`(`id_usuario`, `fecha`, `registro_ingreso`, `ip`) VALUES (?,now(),now(),?)";
				$q = $pdo->prepare($sql);
				$q->execute(array($_SESSION['user']['id'],$_SERVER['REMOTE_ADDR']));
				
				header("Location: mensajeAsistencia.php?msg=ingresoExitoso");
			} else {
				header("Location: mensajeAsistencia.php?msg=ingresoExistente");
			}
        }
        else{ 
            header("Location: login.php"); 
            die("Redirecting to: login.php"); 
        } 
    } 
?>