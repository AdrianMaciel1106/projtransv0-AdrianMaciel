<?php
header('Content-Type: application/json; charset=utf-8'); // Indiquem que retornem JSON
session_start();// Iniciem sessió per guardar respostes correctes
include 'db.php'; // Incloem connexió a la base de dades

$sql = "SELECT 
            preguntes.id AS pregunta_id,
            preguntes.text AS pregunta_text,
            preguntes.imatge AS pregunta_img,
            respostes.id AS respuesta_id,
            respostes.text AS respuesta_text,
            respostes.imatge AS respuesta_img,
            respostes.is_correct
        FROM preguntes
        LEFT JOIN respostes ON preguntes.id = respostes.pregunta_id
        ORDER BY preguntes.id, respostes.id";

$result = $conn->query($sql);

$pregunta_actual = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($pregunta_actual != $row['pregunta_id']) {
            if ($pregunta_actual != 0) {
                echo "<hr>"; // separador entre preguntas
            }
            $pregunta_actual = $row['pregunta_id'];
            echo "<h3>Pregunta: " . $row['pregunta_text'] . "</h3>";
            if (!empty($row['pregunta_img'])) {
                echo "<img src='" . $row['pregunta_img'] . "' alt='Imagen de la pregunta' style='max-width:200px;'><br>";
            }
            echo "<ul>";
        }
        echo "<li>" . $row['respuesta_text'];
        if ($row['is_correct']) {
            echo " ✅"; // marca la respuesta correcta
        }
        echo "</li>";
        // Cierra la lista si la siguiente pregunta es diferente
        if (!isset($row_next) || $row_next['pregunta_id'] != $pregunta_actual) {
            echo "</ul>";
        }
    }
} else {
    echo "No hay preguntas.";
}


$_SESSION['answers'] = []; // Inicializamos array de respuestas correctas
foreach ($sel as $p) {
    // Guardamos el índice de la respuesta correcta
    $_SESSION['answers'][$p['id']] =
        array_search(true, array_column($p['respostes'], 'correcta'));
}

// Quitamos el campo "correcta" antes de enviar al cliente

$public = array_map(function($p){
  $respostes = array_map(function($r){
    unset($r['correcta']);
    return $r;
  }, $p['respostes']);
  $correctaIndex = array_search(true, array_column($p['respostes'], 'correcta'));
  return [
    'id'            => $p['id'],
    'pregunta'      => $p['pregunta'],
    'respostes'     => $respostes,
    'correctaIndex' => $correctaIndex
  ];
}, $sel);

echo json_encode($public, JSON_UNESCAPED_UNICODE); // Enviem dades al client