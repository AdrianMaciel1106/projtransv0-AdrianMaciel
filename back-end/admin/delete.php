<?php
require_once '../db.php';
$pdo = getDB();
$id = (int)($_GET['id'] ?? 0); // ID de la pregunta
if ($id > 0) {
    // Eliminar respuestas asociadas
  $stmt = $pdo->prepare("DELETE FROM preguntes WHERE id=:id");
  $stmt->execute([':id' => $id]);
}
header('Location: list.php');
