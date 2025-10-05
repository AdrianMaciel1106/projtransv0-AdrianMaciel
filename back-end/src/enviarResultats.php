<?php
header('Content-Type: application/json');
session_start();

// Recibir datos JSON
$input = json_decode(file_get_contents('php://input'), true);
$respostes = $input['respostes'] ?? []; // Array de respuestas del usuario

$correctesMap = $_SESSION['answers'] ?? []; // Mapa de respuestas correctas

$correctes; // Contador de respuestas correctas
// Contar respuestas correctas
foreach ($respostes as $r) {
  $id = $r['pregunta_id'] ?? null;
  $resposta = $r['resposta_id'] ?? null;
  if (isset($correctesMap[$id]) && $resposta == $correctesMap[$id]) {
    printf($id);
  }
}

// Devolver resultados
echo json_encode([
  'total' => count($input['respostes']),
  'correctes' => $correctes
]);
?>
