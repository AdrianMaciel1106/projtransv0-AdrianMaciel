<?php
session_start();

$jsonFile = file_get_contents("/Quiz.json"); //Variable per agafar el contingut del fitxer Quiz.son

if (!file_exists($jsonFile)) { //Condicional per si no es troba el fitxer JSON. Retorna un missatge d'error
    echo "<h2>Error:</h2><p>Mo s'ha trobat el fitxer Quiz.json.</p>";
    echo "<p><a href=\"https://github.com/googlearchive/android-Quiz/blob/master/Application/src/main/assets/Quiz.json\" target=\"_blank\">Descargar JSON original</a></p>";
    exit;
}

$raw = file_get_contents($jsonFile);//Variable raw per agafar el contingut de la variable jsonFile.

$all = json_decode($raw, true); 

if (!is_array($all) || count($all) < 10) {
    echo "Fitxer JSON incorrecte o conté menys de 10 preguntes.";
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
    <title>Quiz - Inici</title>
</head>
<body>
    <h1>Joc de preguntes</h1>
    <p>S'han seleccionat 10 preguntes a l'atzar.</p>
    <form action="pregunta.php" method="get">
        <button type="submit">Comenzar</button>
    </form>
</body>
</html>
