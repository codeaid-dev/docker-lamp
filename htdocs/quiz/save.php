<?php
  require_once 'config.php';

  $question = $_POST['question'] ?? '';
  $answer = $_POST['answer'] ?? '';
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
      $stmt = $db->prepare("INSERT INTO questions (question, answer) VALUES (:question, :answer)");
      $stmt->bindParam(':question', $question, PDO::PARAM_STR);
      $stmt->bindParam(':answer', $answer, PDO::PARAM_STR);
      $stmt->execute();
      print '<p>保存できました。</p>';
    } catch (PDOException $e) {
      die ('エラー：'.$e->getMessage());
    }  
  }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>クイズ</title>
</head>
<body>
  <h1>クイズ新規作成</h1>
  <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
    <p><label>問題：<input type="text" name="question" value="<?= htmlspecialchars($question) ?>" required></label></p>
    <p><label>答え：<input type="text" name="answer" value="<?= htmlspecialchars($answer) ?>" required></label></p>
    <button type="submit">保存</button>
  </form>
  <p><span style="margin-right: 30px"><a href="index.php">トップ</a></span><span style="margin-right: 30px"><a href="edit.php">編集</a></span><a href="quiz.php">出題</a></p>
</body>
</html>