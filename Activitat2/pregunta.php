<?php
session_start();
if (!isset($_SESSION['preguntas'])) {
    header('Location: index.php');
    exit;
}

$index = intval($_SESSION['index']);
$total = count($_SESSION['preguntas']);
if ($index >= $total) {
    header('Location: final.php');
    exit;
}
$pregunta = $_SESSION['preguntas'][$index];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Pregunta <?= $index+1 ?> / <?= $total ?></title>
</head>
<body>
    <h2>Pregunta <?= $index+1 ?> de <?= $total ?></h2>
    <p><?= htmlspecialchars($pregunta['question']) ?></p>

    <form method="post" action="resultat.php">
        <?php foreach ($pregunta['answers'] as $id => $txt): ?>
            <label>
                <input type="radio" name="respuesta" value="<?= $id ?>" required>
                <?= htmlspecialchars($txt) ?>
            </label><br>
        <?php endforeach; ?>
        <button type="submit">Enviar</button>
    </form>

    <p>Puntuació actual: <?= intval($_SESSION['puntuacion']) ?> / <?= $total ?></p>
</body>
</html>
