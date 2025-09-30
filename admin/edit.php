<?php
require_once '../db.php';
$pdo = getDB(); // PDO connection
$id = (int)($_GET['id'] ?? 0); // ID de la pregunta
if ($id <= 0) { echo 'ID invàlid'; exit; }
// Editar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $text = trim($_POST['text'] ?? '');
  $imatge = trim($_POST['imatge'] ?? '');
  $correctaRid = (int)($_POST['correctaRid'] ?? 0);

  // Validar
  $pdo->beginTransaction();
  try {
    $pdo->prepare("UPDATE preguntes SET text=:t, imatge=:i WHERE id=:id")->execute([':t'=>$text, ':i'=>$imatge?:null, ':id'=>$id]);

    // Actualizar respuestas (simplificado: solo correcta)
    $pdo->prepare("UPDATE respostes SET es_correcta = CASE WHEN id=:rid THEN 1 ELSE 0 END WHERE pregunta_id=:pid")
        ->execute([':rid' => $correctaRid, ':pid' => $id]);

        // Commit y redirigir
    $pdo->commit();
    header('Location: list.php'); exit;
  } catch (Throwable $e) {
    $pdo->rollBack();
    echo 'Error editant: '.$e->getMessage();
  }
}

// Carga
$preg = $pdo->prepare("SELECT id, text, imatge FROM preguntes WHERE id=:id");
$preg->execute([':id'=>$id]);
$p = $preg->fetch();

// Respuestas
$res = $pdo->prepare("SELECT id, text, imatge, es_correcta FROM respostes WHERE pregunta_id=:pid");
$res->execute([':pid'=>$id]);
$answers = $res->fetchAll();
?>
<h1>Editar pregunta</h1>
<form method="post">
  <label>Text: <input name="text" value="<?= htmlspecialchars($p['text']) ?>" required></label><br>
  <label>Imatge: <input name="imatge" value="<?= htmlspecialchars($p['imatge'] ?? '') ?>"></label><br>

  <h3>Respostes</h3>
  <?php foreach ($answers as $a): ?>
    <div>
      <span>ID <?= $a['id'] ?>:</span>
      <input value="<?= htmlspecialchars($a['text']) ?>" disabled>
      <label>
        Correcta
        <input type="radio" name="correctaRid" value="<?= $a['id'] ?>" <?= $a['es_correcta'] ? 'checked' : '' ?>>
      </label>
    </div>
  <?php endforeach; ?>

  <button type="submit">Guardar</button>
</form>
