<?php
// back-end/src/db.php
declare(strict_types=1);

function getDB(): PDO {
    // Mueve estas credenciales a variables de entorno en producción
    $host   = 'localhost';
    $dbname = 'a22adrmacfir_proj0';
    $user   = 'a22adrmacfir_admin';
    $pass   = 'Projecte0-AdrianMaciel';
    $port   = 3306;

    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

    return new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}
