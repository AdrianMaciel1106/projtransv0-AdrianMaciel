<?php
require_once '../db.php';
$pdo = getDB();

$preguntes = $pdo->query("SELECT id, text, imatge FROM preguntes ORDER BY id DESC")->fetchAll();

// Llistar
echo '<h1>Preguntes</h1><a href="new.php">Nova pregunta</a><ul>';
foreach ($preguntes as $p) {
  echo '<li>';
  echo htmlspecialchars($p['text']);
  echo ' — <a href="edit.php?id='.$p['id'].'">Editar</a>';
  echo ' — <a href="delete.php?id='.$p['id'].'" onclick="return confirm(\'Eliminar?\')">Eliminar</a>';

  // Respuestas
  $stmt = $pdo->prepare("SELECT id, text, imatge, es_correcta FROM respostes WHERE pregunta_id = :pid");
  $stmt->execute([':pid' => $p['id']]);
  $res = $stmt->fetchAll();

  // Mostrar respuestas
  echo '<ul>';
  foreach ($res as $r) {
    echo '<li>'.htmlspecialchars($r['text']).($r['es_correcta'] ? ' ✅' : '').'</li>';
  }
  echo '</ul></li>';
}
echo '</ul>';
