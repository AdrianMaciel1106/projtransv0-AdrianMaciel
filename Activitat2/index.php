<?php
session_start();

$jsonFile = __DIR__ . '/Quiz.json';

if (!file_exists($jsonFile)) {
    echo "<h2>Error:</h2><p>No se encontró el fichero Quiz.json en el directorio del proyecto.</p>";
    echo "<p><a href=\"https://github.com/googlearchive/android-Quiz/blob/master/Application/src/main/assets/Quiz.json\" target=\"_blank\">Descargar JSON original</a></p>";
    exit;
}

$raw = file_get_contents($jsonFile);
$all = json_decode($raw, true);
if (!is_array($all) || count($all) < 10) {
    echo "Fichero JSON inválido o con menos de 10 preguntas.";
    exit;
}

$keys = array_rand($all, 10);
if (!is_array($keys)) $keys = [$keys];

$seleccionadas = [];
foreach ($keys as $k) {
    $q = $all[$k];
    if (!isset($q['question']) || !isset($q['answers']) || !isset($q['correctIndex'])) continue;

    $answers = $q['answers'];
    $correct = intval($q['correctIndex']);
    $idxs = range(0, count($answers)-1);
    shuffle($idxs);
    $newAnswers = [];
    $newCorrect = 0;
    foreach ($idxs as $newPos => $oldPos) {
        $newAnswers[] = $answers[$oldPos];
        if ($oldPos === $correct) $newCorrect = $newPos;
    }

    $seleccionadas[] = [
        'question' => $q['question'],
        'answers' => $newAnswers,
        'correctIndex' => $newCorrect
    ];
}

$_SESSION['preguntas'] = $seleccionadas;
$_SESSION['index'] = 0;
$_SESSION['puntuacion'] = 0;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Quiz - Inicio</title>
    <style>body{font-family:Arial,sans-serif;padding:20px}</style>
</head>
<body>
    <h1>Juego de preguntas</h1>
    <p>Se han seleccionado 10 preguntas al azar. ¿Listo?</p>
    <form action="pregunta.php" method="get">
        <button type="submit">Comenzar</button>
    </form>
</body>
</html>
