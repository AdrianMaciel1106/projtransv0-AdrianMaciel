<?php
header('Content-Type: application/json');
include 'db.php';

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
$respostesUsuari = $input['respostes'] ?? [];

if (!is_array($respostesUsuari)) {
    http_response_code(400);
    echo json_encode(['error'=>'Formato incorrecto']);
    exit;
}

try {
    $pdo = getDB();
    $valides = [];

    // Normalizar y filtrar entradas válidas
    foreach ($respostesUsuari as $r) {
        if (isset($r['pregunta_id'], $r['resposta_id']) && is_numeric($r['pregunta_id']) && is_numeric($r['resposta_id'])) {
            $pid = (int)$r['pregunta_id'];
            $rid = (int)$r['resposta_id'];
            $valides[] = ['pid'=>$pid,'rid'=>$rid];
        }
    }

    // Si no hay respuestas válidas, devolver 0/0
    if (count($valides) === 0) {
        echo json_encode(['total'=>0,'correctes'=>0]);
        exit;
    }

    // Mantener última respuesta por pregunta si hubo duplicados
    $porPregunta = [];
    foreach ($valides as $v) { $porPregunta[$v['pid']] = $v['rid']; }

    $stmt = $pdo->prepare("SELECT is_correct FROM respostes WHERE id = :rid AND pregunta_id = :pid LIMIT 1");
    $correctes = 0;
    foreach ($porPregunta as $pid => $rid) {
        $stmt->execute([':rid'=>$rid, ':pid'=>$pid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            // asegurar int y solo 1 cuenta como correcta
            if (intval($row['is_correct']) === 1) $correctes++;
        }
        // si no devuelve fila, no contamos nada (respuesta inválida)
    }

    $total = count($porPregunta);
    echo json_encode(['total'=>$total,'correctes'=>$correctes]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error'=>'Error al finalitzar el joc']);
}
