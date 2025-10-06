<?php
// Cabeceras para JSON y CORS
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Responder a OPTIONS y salir (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Incluir la base de datos correctamente
include __DIR__ . '/../src/db.php'; // Ajuste relativo desde admin.php

// Obtener la acción
$action = $_GET['action'] ?? $_POST['action'] ?? 'read';

// Función de respuesta JSON
function response($data) {
    echo json_encode($data);
    exit;
}

switch($action) {

    // Crear pregunta
    case 'create':
        $pregunta_text = $_POST['pregunta_text'] ?? '';
        $pregunta_img  = $_POST['pregunta_img'] ?? '';
        $respostes     = $_POST['respostes'] ?? [];

        if (!$pregunta_text) {
            response(['success' => false, 'msg' => 'Falta el text de la pregunta']);
        }

        $stmt = $conn->prepare("INSERT INTO preguntes (text, imatge) VALUES (?, ?)");
        $stmt->bind_param("ss", $pregunta_text, $pregunta_img);
        $stmt->execute();
        $pregunta_id = $stmt->insert_id;

        foreach ($respostes as $r) {
            $stmt2 = $conn->prepare("INSERT INTO respostes (pregunta_id, text, is_correct) VALUES (?, ?, ?)");
            $stmt2->bind_param("isi", $pregunta_id, $r['text'], $r['is_correct']);
            $stmt2->execute();
        }

        response(['success' => true, 'pregunta_id' => $pregunta_id]);
        break;

    // Leer preguntas
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

        if ($result && $result->num_rows > 0) {
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

    // Actualizar pregunta
    case 'update':
        $pregunta_id   = $_POST['pregunta_id'] ?? 0;
        $pregunta_text = $_POST['pregunta_text'] ?? '';
        $pregunta_img  = $_POST['pregunta_img'] ?? '';
        $respostes     = $_POST['respostes'] ?? [];

        if (!$pregunta_id || !$pregunta_text) {
            response(['success' => false, 'msg' => 'Falten dades']);
        }

        $stmt = $conn->prepare("UPDATE preguntes SET text=?, imatge=? WHERE id=?");
        $stmt->bind_param("ssi", $pregunta_text, $pregunta_img, $pregunta_id);
        $stmt->execute();

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
        break;

    // Eliminar pregunta
    case 'delete':
        $pregunta_id = $_POST['pregunta_id'] ?? 0;
        if (!$pregunta_id) {
            response(['success' => false, 'msg' => 'Falta l\'ID de la pregunta']);
        }

        $stmt = $conn->prepare("DELETE FROM respostes WHERE pregunta_id=?");
        $stmt->bind_param("i", $pregunta_id);
        $stmt->execute();

        $stmt2 = $conn->prepare("DELETE FROM preguntes WHERE id=?");
        $stmt2->bind_param("i", $pregunta_id);
        $stmt2->execute();

        response(['success' => true]);
        break;

    default:
        response(['success' => false, 'msg' => 'Acció no vàlida']);
}

$conn->close();
?>
