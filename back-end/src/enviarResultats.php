<?php
header('Content-Type: application/json');
session_start();

// Recibir datos JSON
$input = json_decode(file_get_contents('php://input'), true);
$respostes = $input['respostes'] ?? []; // Array de respuestas del usuario

$correctesMap = $_SESSION['respostes'] ?? []; // Mapa de respuestas correctas

$correctes = 0;
$contestades = 0; // Contador de respuestas correctas
// Contar respuestas correctas
foreach ($respostes as $r) {
 $id = isset($r['pregunta_id']) ? (int)$r['pregunta_id'] : null;
  $resposta = $r['resposta_id'] ?? null;

  if ($resposta !== null) {
    $contestades++;
    if (array_key_exists($id, $correctesMap) && (int)$correctesMap[$id] === $resposta) {
      $correctes++;
    }
  }
}
// Devolver resultados
echo json_encode([
  'total' => count($input['respostes']),
  'correctes' => $correctes,
  'correctesMap' => $correctesMap
]);
?>
