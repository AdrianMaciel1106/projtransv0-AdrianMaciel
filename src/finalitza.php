<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

$input = json_decode(file_get_contents('php://input'), true); // Leemos JSON recibido
if (!isset($input['answers']) || !is_array($input['answers'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Bad request']); // Respuesta de error
    exit;
}

$stored = isset($_SESSION['answers']) ? $_SESSION['answers'] : []; // Respuestas correctas almacenadas en sesión

$total = 0; // Total preguntas respondidas
$correctes = 0; // Total respuestas correctas

// Recorremos respuestas enviadas por el cliente
foreach ($input['answers'] as $ans) {
    $id = intval($ans['id']); // ID de la pregunta
    $chosen = intval($ans['chosen']); // Respuesta elegida por el usuario
    if (isset($stored[$id])) {
        $total++;
        // Comprobamos si la respuesta elegida es la correcta
        if ($chosen === intval($stored[$id])) {
            $correctes++;
        }
    }
}

echo json_encode(['total' => $total, 'correctes' => $correctes]);

