<?php
class Database 
{
	private static $dbHost = 'localhost' ;
	private static $dbName = 'miroperito';//c1971287_db
	private static $dbUsername = 'miroperito';//c1971287_db
	private static $dbUserPassword = 'C9EpKlN8MTILc4Y';//23zeduDAza

	private static $cont  = null;
	
	public function __construct() {
		exit('Init function is not allowed');
	}
	
	public static function connect()
	{
	   // One connection through whole application
       if ( null == self::$cont )
       {      
        try 
        {
          self::$cont =  new PDO( "mysql:host=".self::$dbHost.";"."dbname=".self::$dbName.";charset=utf8", self::$dbUsername, self::$dbUserPassword);  
        }
        catch(PDOException $e) 
        {
          die($e->getMessage());  
        }
       } 
       return self::$cont;
	}
	
	public static function disconnect()
	{
		self::$cont = null;
	}
}
?>