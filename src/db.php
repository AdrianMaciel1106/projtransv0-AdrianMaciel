<?php
$host = 'localhost';
$user = 'usuario';
$pass = 'password';
$dbname = 'mi_bd';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>