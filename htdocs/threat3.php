<?php
  $dsn = 'mysql:host=mysql;dbname=sampledb;charset=utf8';
  $user = 'root';
  $password = 'password';
  try {
    $db = new PDO($dsn, $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db->exec("CREATE TABLE IF NOT EXISTS users(
              id INTEGER PRIMARY KEY AUTO_INCREMENT,
              name VARCHAR(256))");

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $name = $_POST['name'];
      if (isset($_POST['save'])) {
        $stmt = $db->prepare("INSERT INTO users (name) VALUES(:name)");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
      } else if (isset($_POST['show'])) {
        $id = $_POST['num'];
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
      }
    }
  } catch (PDOException $e) {
    die ('エラー：'.$e->getMessage());
  }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>脅威動作例</title>
</head>
<body>
  <h1>脅威動作例</h1>
  <?php if (isset($rows)): ?>
    <?php foreach ($rows as $row): ?>
    <p>
      「<?= htmlspecialchars($row['name']) ?>」さん、こんにちは！
    </p>
    <?php endforeach; ?>
    <a href="">戻る</a>
  <?php else: ?>
    <form method="POST">
      <p><label>名前:<input type="text" name="name"></label></p>
      <p><button type="submit" name="save">保存</button></p>
      <p>-----</p>
      <p><label>番号:<input type="text" name="num"></label></p>
      <p><button type="submit" name="show">表示</button></p>
    </form>
  <?php endif; ?>
</body>
</html>