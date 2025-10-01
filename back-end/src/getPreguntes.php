<?php
header('Content-Type: application/json');
include './db.php';

// Determinar el método de la petición
$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = getDB();

    if ($method === 'GET') {
        // ========== OBTENER PREGUNTAS ==========
        $num = isset($_GET['num']) ? intval($_GET['num']) : 10;

        // Obtener preguntas aleatorias
        $stmt = $pdo->prepare("SELECT id, text, imatge FROM preguntes ORDER BY RAND() LIMIT :num");
        $stmt->bindValue(':num', $num, PDO::PARAM_INT);
        $stmt->execute();
        $preguntes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Para cada pregunta, obtener sus respuestas
        $resultat = [];
        $stmt2 = $pdo->prepare("SELECT id, text, imatge FROM respostes WHERE pregunta_id = :pid");

        foreach ($preguntes as $pregunta) {
            $stmt2->execute([':pid' => $pregunta['id']]);
            $respostesDB = $stmt2->fetchAll(PDO::FETCH_ASSOC);

            $respostes = [];
            foreach ($respostesDB as $r) {
                $respostes[] = [
                    'id' => (int)$r['id'],
                    'text' => $r['text'],
                    'imatge' => $r['imatge'] ?? null
                ];
            }

            $resultat[] = [
                'id' => (int)$pregunta['id'],
                'pregunta' => $pregunta['text'],
                'imatge' => $pregunta['imatge'] ?? null,
                'respostes' => $respostes
            ];
        }

        echo json_encode($resultat);

    } elseif ($method === 'POST') {
        // ========== EVALUAR RESPUESTAS ==========
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (!isset($data['respostes']) || !is_array($data['respostes'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Formato de datos inválido']);
            exit;
        }

        $respostes = $data['respostes'];
        $correctes = 0;
        $total = count($respostes);

        // Preparar consulta para verificar respuestas correctas
        $stmt = $pdo->prepare("SELECT correcta FROM respostes WHERE id = :resposta_id");

        foreach ($respostes as $resposta) {
            if (!isset($resposta['resposta_id']) || $resposta['resposta_id'] === null) {
                continue;
            }

            $stmt->execute([':resposta_id' => $resposta['resposta_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar si la respuesta es correcta
            if ($result && isset($result['correcta']) && (int)$result['correcta'] === 1) {
                $correctes++;
            }
        }

        // Devolver resultado
        echo json_encode([
            'total' => $total,
            'correctes' => $correctes
        ]);

    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
}