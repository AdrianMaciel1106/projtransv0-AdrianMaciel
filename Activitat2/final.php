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
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>Puntuació final</title>
</head>
<body>
    <h2>Has encertat <?= $aciertos ?> de <?= $total ?> preguntes.</h2>
    <form action="index.php" method="get">
        <button type="submit">Tornar a començar</button>
    </form>
</body>
</html>
