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
    <title>Resultat</title>
</head>
<body>
    <?php if ($acierto): ?>
        <h2>Correcte!</h2>
    <?php else: ?>
        <h2>Incorrecte</h2>
        <p>Resposta correcta: <?= htmlspecialchars($pregunta['answers'][$correcta]) ?></p>
    <?php endif; ?>

    <p>Puntuació actual: <?= intval($_SESSION['puntuacion']) ?> / <?= $total ?></p>

    <?php if ($_SESSION['index'] < $total): ?>
        <form action="pregunta.php" method="get">
            <button type="submit">Següent</button>
        </form>
    <?php else: ?>
        <form action="final.php" method="get">
            <button type="submit">Veure puntuació final</button>
        </form>
    <?php endif; ?>
</body>
</html>
