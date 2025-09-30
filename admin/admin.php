<?php
include 'db.php';

// Rebre l'acció
$action = $_GET['action'] ?? $_POST['action'] ?? 'read';

// Funció per retornar JSON
function response($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

switch($action) {

    // CREAR una pregunta
    case 'create':
        $pregunta_text = $_POST['pregunta_text'] ?? '';
        $pregunta_img  = $_POST['pregunta_img'] ?? '';
        $respostes     = $_POST['respostes'] ?? [];

        if ($pregunta_text) {
            // Inserir la pregunta
            $stmt = $conn->prepare("INSERT INTO preguntes (text, imatge) VALUES (?, ?)");
            $stmt->bind_param("ss", $pregunta_text, $pregunta_img);
            $stmt->execute();
            $pregunta_id = $stmt->insert_id;

            // Inserir les respostes
            foreach ($respostes as $r) {
                $stmt2 = $conn->prepare("INSERT INTO respostes (pregunta_id, text, is_correct) VALUES (?, ?, ?)");
                $stmt2->bind_param("isi", $pregunta_id, $r['text'], $r['is_correct']);
                $stmt2->execute();
            }

            response(['success' => true, 'pregunta_id' => $pregunta_id]);
        } else {
            response(['success' => false, 'msg' => 'Falta el text de la pregunta']);
        }
        break;

    // LLEGIR totes les preguntes
    case 'read':
        $preguntes = [];
        $sql = "SELECT 
                    preguntes.id AS pregunta_id,
                    preguntes.text AS pregunta_text,
                    preguntes.imatge AS pregunta_img,
                    respostes.id AS resposta_id,
                    respostes.text AS resposta_text,
                    respostes.imatge AS resposta_img,
                    respostes.is_correct
                FROM preguntes
                LEFT JOIN respostes ON preguntes.id = respostes.pregunta_id
                ORDER BY preguntes.id, respostes.id";

        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $pid = $row['pregunta_id'];
                if (!isset($preguntes[$pid])) {
                    $preguntes[$pid] = [
                        'id' => $pid,
                        'text' => $row['pregunta_text'],
                        'imatge' => $row['pregunta_img'],
                        'respostes' => []
                    ];
                }
                if ($row['resposta_id']) {
                    $preguntes[$pid]['respostes'][] = [
                        'id' => $row['resposta_id'],
                        'text' => $row['resposta_text'],
                        'imatge' => $row['resposta_img'],
                        'is_correct' => $row['is_correct']
                    ];
                }
            }
        }
        response(array_values($preguntes));
        break;

    // ACTUALITZAR pregunta i respostes
    case 'update':
        $pregunta_id   = $_POST['pregunta_id'] ?? 0;
        $pregunta_text = $_POST['pregunta_text'] ?? '';
        $pregunta_img  = $_POST['pregunta_img'] ?? '';
        $respostes     = $_POST['respostes'] ?? [];

        if ($pregunta_id && $pregunta_text) {
            // Actualitzar la pregunta
            $stmt = $conn->prepare("UPDATE preguntes SET text=?, imatge=? WHERE id=?");
            $stmt->bind_param("ssi", $pregunta_text, $pregunta_img, $pregunta_id);
            $stmt->execute();

            // Actualitzar o inserir les respostes
            foreach ($respostes as $r) {
                if (isset($r['id']) && $r['id'] > 0) {
                    $stmt2 = $conn->prepare("UPDATE respostes SET text=?, is_correct=? WHERE id=?");
                    $stmt2->bind_param("sii", $r['text'], $r['is_correct'], $r['id']);
                    $stmt2->execute();
                } else {
                    $stmt2 = $conn->prepare("INSERT INTO respostes (pregunta_id, text, is_correct) VALUES (?, ?, ?)");
                    $stmt2->bind_param("isi", $pregunta_id, $r['text'], $r['is_correct']);
                    $stmt2->execute();
                }
            }

            response(['success' => true]);
        } else {
            response(['success' => false, 'msg' => 'Falten dades']);
        }
        break;

    // ELIMINAR pregunta i les seves respostes
    case 'delete':
        $pregunta_id = $_POST['pregunta_id'] ?? 0;
        if ($pregunta_id) {
            // Primer eliminar les respostes
            $stmt = $conn->prepare("DELETE FROM respostes WHERE pregunta_id=?");
            $stmt->bind_param("i", $pregunta_id);
            $stmt->execute();

            // Després eliminar la pregunta
            $stmt2 = $conn->prepare("DELETE FROM preguntes WHERE id=?");
            $stmt2->bind_param("i", $pregunta_id);
            $stmt2->execute();

            response(['success' => true]);
        } else {
            response(['success' => false, 'msg' => 'Falta l\'ID de la pregunta']);
        }
        break;

    default:
        response(['success' => false, 'msg' => 'Acció no vàlida']);
}

$conn->close();
?>
