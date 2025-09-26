<?php
header('Content-Type: application/json');
require_once 'db.php';

$num = isset($_GET['num']) ? intval($_GET['num']) : 10;

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id, text FROM pregunta ORDER BY RAND() LIMIT :num");
    $stmt->bindValue(':num', $num, PDO::PARAM_INT);
    $stmt->execute();
    $preguntes = $stmt->fetchAll();
    $resultat = [];
    $stmt2 = $pdo->prepare("SELECT id, text FROM resposta WHERE pregunta_id = :pid");
    foreach ($preguntes as $pregunta) {
        $stmt2->execute([':pid' => $pregunta['id']]);
        $respostes = $stmt2->fetchAll();
        $resultat[] = [
            'id' => $pregunta['id'],
            'question' => $pregunta['text'],
            'answers' => $respostes
        ];
    }
    echo json_encode($resultat);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtenir preguntes']);
}
?>