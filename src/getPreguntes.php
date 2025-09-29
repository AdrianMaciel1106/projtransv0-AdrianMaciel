<?php
header('Content-Type: application/json');
include 'db.php';

$num = isset($_GET['num']) ? intval($_GET['num']) : 10;

try {
    $pdo = getDB();

    // Preguntes
    $stmt = $pdo->prepare("SELECT id, text, imatge FROM preguntes ORDER BY RAND() LIMIT :num"); // Consulta
    $stmt->bindValue(':num', $num, PDO::PARAM_INT); // Enllaçar paràmetre
    $stmt->execute(); // Executem la consulta
    $preguntes = $stmt->fetchAll(PDO::FETCH_ASSOC); // Preguntes obtingudes

    $resultat = [];

    // Respostes per cada pregunta
    $stmt2 = $pdo->prepare("SELECT id, text, imatge, is_correct FROM respostes WHERE pregunta_id = :pid");
    
    // Recorrem les preguntes obtingudes
    foreach ($preguntes as $pregunta) {
        $stmt2->execute([':pid' => $pregunta['id']]); // Obtenim respostes
        $respostesDB = $stmt2->fetchAll(PDO::FETCH_ASSOC); // Respostes de la pregunta

        $respostes = []; // Respostes formatades per la pregunta
        $correctaIndex = null; // Índex de la resposta correcta

        // Recorrem les respostes per formatar-les
        foreach ($respostesDB as $idx => $r) {
            $respostes[] = [
                'text' => $r['text'],
                'imatge' => $r['imatge'] ?? null
            ];
            if ((int)$r['is_correct'] === 1) {
                $correctaIndex = $idx;
            }
        }

        $resultat[] = [
            'id' => $pregunta['id'],
            'pregunta' => $pregunta['text'],   // clave que espera JS
            'imatge' => $pregunta['imatge'],   
            'respostes' => $respostes,         // clave que espera JS
            'correctaIndex' => $correctaIndex  // clave que espera JS
        ];
    }

    echo json_encode($resultat); // Retornem JSON
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al obtenir preguntes',
        'detall' => $e->getMessage()
    ]);
}
