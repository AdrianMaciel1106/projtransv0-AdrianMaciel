<?php
$host = 'localhost';
$user = 'a22adrmacfir_admin';
$pass = 'Projecte0-AdrianMaciel';
$dbname = 'a22adrmacfir_proj0';

 // Connexió amb mysqli (per admin.php)
function getDB() {
    global $host, $dbname, $user, $pass;
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4"; // Data Source Name
    // Connexió amb PDO (per getPreguntes.php)
    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error de conexión a la base de datos']);
        exit;
    }
}