<?php
session_start();
if (!isset($_SESSION['preguntas'])) {
    header('Location: index.php');
    exit;
}

$total = count($_SESSION['preguntas']);
$aciertos = intval($_SESSION['puntuacion']);

session_unset();
session_destroy();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Puntuación final</title>
    <style>body{font-family:Arial,sans-serif;padding:20px}</style>
</head>
<body>
    <h2>Has acertado <?= $aciertos ?> de <?= $total ?> preguntas.</h2>
    <form action="index.php" method="get">
        <button type="submit">Volver a empezar</button>
    </form>
</body>
</html>
