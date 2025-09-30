<?php
require_once '../db.php';
$pdo = getDB();

// Crear
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $text = trim($_POST['text'] ?? ''); // texto de la pregunta
  $imatge = trim($_POST['imatge'] ?? ''); // URL de la imagen
  $respostes = $_POST['respostes'] ?? []; // array de textos
  $correcta = (int)($_POST['correcta'] ?? -1); // índice de la correcta

  // Validar
  if ($text && count($respostes) >= 2 && $correcta >= 0 && $correcta < count($respostes)) {
    $pdo->beginTransaction();
    try {
      $stmt = $pdo->prepare("INSERT INTO preguntes (text, imatge) VALUES (:t, :i)");
      $stmt->execute([':t' => $text, ':i' => $imatge ?: null]);
      $pid = (int)$pdo->lastInsertId();

      // Respuestas
      foreach ($respostes as $i => $rtxt) {
        $stmtR = $pdo->prepare("INSERT INTO respostes (pregunta_id, text, imatge, es_correcta) VALUES (:pid, :t, NULL, :c)");
        $stmtR->execute([':pid' => $pid, ':t' => trim($rtxt), ':c' => ($i === $correcta ? 1 : 0)]);
      }

      // Commit y redirigir
      $pdo->commit();
      header('Location: list.php'); exit;
    } catch (Throwable $e) {
      $pdo->rollBack();
      echo 'Error creant: '.$e->getMessage();
    }
  }
}
?>
// Formulari
<form method="post">
  <label>Text de la pregunta: <input name="text" required></label><br>
  <label>Imatge (URL opcional): <input name="imatge"></label><br>
  <h3>Respostes</h3>
  <div>
    <input name="respostes[]" placeholder="Resposta 1" required>
    <input type="radio" name="correcta" value="0" required> Correcta
  </div>
  <div>
    <input name="respostes[]" placeholder="Resposta 2" required>
    <input type="radio" name="correcta" value="1"> Correcta
  </div>
  <div>
    <input name="respostes[]" placeholder="Resposta 3">
    <input type="radio" name="correcta" value="2"> Correcta
  </div>
  <div>
    <input name="respostes[]" placeholder="Resposta 4">
    <input type="radio" name="correcta" value="3"> Correcta
  </div>
  <button type="submit">Crear</button>
</form>
