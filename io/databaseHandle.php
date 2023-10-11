<?php 
// FOR EVERY DATABASE TRANSACTION, ADD AN IMPORT LINE POINTING TO THIS
// REMEMBER TO $socket->close AFTER OPERATION 
$serverURL = "gotogro-mrm-db.mysql.database.azure.com";
$username = "mydemouser";
$password = "Vsp3dbwH";
$database = "mysql_schema";
$certificate = './cert/DigiCertGlobalRootCA.crt.pem';

$socket = mysqli_init(); 
mysqli_ssl_set($socket, NULL, NULL, $certificate, NULL, NULL);
$connectionState = mysqli_real_connect($socket, $serverURL, $username, $password, $database, 3306, MYSQLI_CLIENT_SSL);

if (!$connectionState){
  echo '<script>console.log("<debug> sqli connection failed"); </script>';
  die('Failed to connect to MySQL: '.mysqli_connect_error());
} else {
  echo '<script>console.log("<debug> sqli socket established"); </script>';
};
?>