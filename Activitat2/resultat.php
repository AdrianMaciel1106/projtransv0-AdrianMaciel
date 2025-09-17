<?php
session_start();
if (!isset($_SESSION['preguntas'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['respuesta'])) {
    header('Location: pregunta.php');
    exit;
}

$index = intval($_SESSION['index']);
$pregunta = $_SESSION['preguntas'][$index];
$respuestaUsuario = intval($_POST['respuesta']);
$correcta = intval($pregunta['correctIndex']);
$acierto = ($respuestaUsuario === $correcta);

if ($acierto) {
    $_SESSION['puntuacion']++;
}

$_SESSION['index'] = $index + 1;
$total = count($_SESSION['preguntas']);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Resultado</title>
    <style>body{font-family:Arial,sans-serif;padding:20px}</style>
</head>
<body>
    <?php if ($acierto): ?>
        <h2>¡Correcto!</h2>
    <?php else: ?>
        <h2>Incorrecto</h2>
        <p>Respuesta correcta: <?= htmlspecialchars($pregunta['answers'][$correcta]) ?></p>
    <?php endif; ?>

    <p>Puntuación actual: <?= intval($_SESSION['puntuacion']) ?> / <?= $total ?></p>

    <?php if ($_SESSION['index'] < $total): ?>
        <form action="pregunta.php" method="get">
            <button type="submit">Siguiente</button>
        </form>
    <?php else: ?>
        <form action="final.php" method="get">
            <button type="submit">Ver puntuación final</button>
        </form>
    <?php endif; ?>
</body>
</html>
