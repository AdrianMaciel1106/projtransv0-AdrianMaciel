<?php
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
// Validar y calcular resultados...
echo json_encode([
  'total' => count($input['respostes']),
  'correctes' => $correctes
]);
?>
