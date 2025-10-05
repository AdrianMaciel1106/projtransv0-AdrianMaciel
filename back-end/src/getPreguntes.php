<?php
// back-end/src/getPreguntes.php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    $pdo = getDB();

    if ($method === 'GET') {
        // ========= OBTENER PREGUNTAS =========
        $num = isset($_GET['num']) ? max(1, min(100, (int)$_GET['num'])) : 10;

        // Preguntas aleatorias (RAND() vale si la tabla no es enorme)
        $stmt = $pdo->prepare("SELECT id, text, imatge FROM preguntes ORDER BY RAND() LIMIT :num");
        $stmt->bindValue(':num', $num, PDO::PARAM_INT);
        $stmt->execute();
        $preguntes = $stmt->fetchAll();

        // Respuestas por pregunta (sin is_correct)
        $stmtResp = $pdo->prepare("
            SELECT id, text, imatge
            FROM respostes
            WHERE pregunta_id = :pid
            ORDER BY id
        ");

        $resultat = [];
        foreach ($preguntes as $p) {
            $stmtResp->execute([':pid' => $p['id']]);
            $respsDB = $stmtResp->fetchAll();

            $respostes = [];
            foreach ($respsDB as $r) {
                $respostes[] = [
                    'id'     => (int)$r['id'],
                    'text'   => $r['text'],
                    'imatge' => $r['imatge'] ?? null,
                ];
            }

            $resultat[] = [
                'id'        => (int)$p['id'],
                'pregunta'  => $p['text'],
                'imatge'    => $p['imatge'] ?? null,
                'respostes' => $respostes,
            ];
        }

        echo json_encode($resultat, JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($method === 'POST') {
        // ========= CORREGIR RESPUESTAS =========
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['respostes']) || !is_array($data['respostes'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Formato de datos inválido'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Filtrar contestadas y construir pares (pid, rid)
        $answeredPairs = [];   // [['pid'=>x,'rid'=>y], ...]
        $answeredIds   = [];   // [rid1, rid2, ...]
        foreach ($data['respostes'] as $r) {
            if (isset($r['resposta_id']) && $r['resposta_id'] !== null) {
                $pid = (int)$r['pregunta_id'];
                $rid = (int)$r['resposta_id'];
                $answeredPairs[] = ['pid' => $pid, 'rid' => $rid];
                $answeredIds[] = $rid;
            }
        }

        $contestades = count($answeredPairs);
        $correctes = 0;

        if ($contestades > 0) {
            // Traemos para todos los rid: a qué pregunta pertenecen y si son correctos
            $placeholders = implode(',', array_fill(0, count($answeredIds), '?'));
            $sql = "SELECT id AS rid, pregunta_id AS pid, is_correct
                    FROM respostes
                    WHERE id IN ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($answeredIds);

            // Mapa rid => [pid, ok]
            $meta = [];
            while ($row = $stmt->fetch()) {
                $meta[(int)$row['rid']] = [
                    'pid' => (int)$row['pid'],
                    'ok'  => ((int)$row['is_correct'] === 1),
                ];
            }

            // Contar correctas: el rid debe pertenecer al pid Y ser correcta
            foreach ($answeredPairs as $a) {
                $rid = $a['rid'];
                $pid = $a['pid'];
                if (isset($meta[$rid]) && $meta[$rid]['pid'] === $pid && $meta[$rid]['ok']) {
                    $correctes++;
                }
            }
        }

        echo json_encode([
            'total'     => $contestades,   // solo preguntas contestadas
            'correctes' => $correctes
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error del servidor'], JSON_UNESCAPED_UNICODE);
}
