<?php 

    // These variables define the connection information for your MySQL database 
	  $host = "localhost"; 
    $username = "miroperito";//c1971287_db
    $password = "C9EpKlN8MTILc4Y";//23zeduDAza
    $dbname = "miroperito";//c1971287_db
	
    $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); 
    try { $db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options); } 
    catch(PDOException $ex){ die("Failed to connect to the database: " . $ex->getMessage());} 
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
    header('Content-Type: text/html; charset=utf-8');
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    session_start(); 

    $smtpHost = "hosting3.tecnosoul.com.ar";
    $smtpSecure = "";
    $smtpPort = 25;
?>