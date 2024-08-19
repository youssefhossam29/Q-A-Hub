<?php



   function connection(){ 
      $dbHost = 'localhost';
      $dbUser = 'root';
      $dbPass = '';
      $dbName = 'php_blog';
      $con = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
      return $con;
   }  
   
   
   
?>