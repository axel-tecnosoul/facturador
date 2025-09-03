<?php 
    require("config.php"); 
    unset($_SESSION['proveedor']);
    header("Location: loginProveedores.php"); 
    die("Redirecting to: loginProveedores.php");
?> 