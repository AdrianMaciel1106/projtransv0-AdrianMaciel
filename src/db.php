<?php
$host = 'localhost';
$user = 'a22adrmacfir_admin';
$pass = 'b}/4$kPH+5;9Ztd%';
$dbname = 'a22adrmacfir_proj0';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>