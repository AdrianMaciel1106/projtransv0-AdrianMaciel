<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!isset($data['answers']) || !is_array($data['answers'])) {
  http_response_code(400);
  echo json_encode(['error'=>'Bad request']);
  exit;
}

// $_SESSION['answers'] és mapping id -> correctIndex guardat a getPreguntes.php
$stored = isset($_SESSION['answers']) ? $_SESSION['answers'] : [];

$total = 0;
$correct = 0;

// El client enviava només índexs en ordre de les preguntes rebudes.
// Per aquest exemple suposarem que l'ordre de preguntes al servidor és la mateixa que al client
// i usarem els ids guardats a sessió en l'ordre que vam seleccionar prèviament.
// Una implementació simple: iterar sobre stored en l'ordre d'inserció
$ids = array_keys($stored);

for ($i = 0; $i < count($data['answers']) && $i < count($ids); $i++) {
  $chosen = intval($data['answers'][$i]);
  if ($chosen >= 0) {
    $total++;
    $id = $ids[$i];
    if ($chosen === intval($stored[$id])) $correct++;
  }
}

echo json_encode(['total'=>$total, 'correctes'=>$correct]);
