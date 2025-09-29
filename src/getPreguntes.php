<?php
header('Content-Type: application/json'); // Respuesta en JSON
include 'db.php'; // Incluir conexión a la base de datos

// Número de preguntas a obtener (por defecto 10)
$num = isset($_GET['num']) ? intval($_GET['num']) : 10;

// Validar número
try {
    $pdo = getDB();

    // Obtener preguntas aleatorias
    $stmt = $pdo->prepare("SELECT id, text, imatge FROM preguntes ORDER BY RAND() LIMIT :num");
    $stmt->bindValue(':num', $num, PDO::PARAM_INT);
    $stmt->execute();
    $preguntes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Para cada pregunta, obtener sus respuestas
    $resultat = [];
    $stmt2 = $pdo->prepare("SELECT id, text, imatge FROM respostes WHERE pregunta_id = :pid");

    // Construir resultado
    foreach ($preguntes as $pregunta) {
        $stmt2->execute([':pid' => $pregunta['id']]);
        $respostesDB = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // Formatear respuestas
        $respostes = [];
        foreach ($respostesDB as $r) {
            $respostes[] = [
                'id' => (int)$r['id'],        // ID real de la respuesta
                'text' => $r['text'],
                'imatge' => $r['imatge'] ?? null
            ];
        }

        //  Añadir pregunta y sus respuestas al resultado
        $resultat[] = [
            'id' => (int)$pregunta['id'],
            'pregunta' => $pregunta['text'],
            'imatge' => $pregunta['imatge'] ?? null,
            'respostes' => $respostes
        ];
    }

    // Devolver preguntas y respuestas en JSON
    echo json_encode($resultat);

    // Manejo de errores
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtenir preguntes']);
}
