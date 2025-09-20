<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['answers']) || !is_array($input['answers'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Bad request']);
    exit;
}

$stored = isset($_SESSION['answers']) ? $_SESSION['answers'] : [];

$total = 0;
$correctes = 0;

foreach ($input['answers'] as $ans) {
    $id = intval($ans['id']);
    $chosen = intval($ans['chosen']);
    if (isset($stored[$id])) {
        $total++;
        if ($chosen === intval($stored[$id])) {
            $correctes++;
        }
    }
}

echo json_encode(['total' => $total, 'correctes' => $correctes]);

