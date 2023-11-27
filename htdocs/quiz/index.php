<?php
  //$dsn = 'mysql:host=localhost;dbname=quiz;charset=utf8mb4'; // XAMPP/MAMP/VMの場合
  $dsn = 'mysql:host=mysql;dbname=quiz;charset=utf8mb4'; // Dockerの場合
  //$dsn = 'sqlite:./quiz.db'; // SQLiteの場合
  $user = 'root';
  $password = 'password';
  try {
    $db = new PDO($dsn, $user, $password); //MySQL
    //$db = new PDO($dsn); //SQLite
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // 例外を出力する
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // 静的プレースホルダーを指定
    $db->query("CREATE TABLE IF NOT EXISTS questions (
      id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
      question VARCHAR(255) NOT NULL,
      answer VARCHAR(255) NOT NULL
    ) DEFAULT CHARACTER SET=utf8;");
  } catch (PDOException $e) {
    die ('エラー：'.$e->getMessage());
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
  <h1>クイズ作成と出題</h1>
  <p>クイズを作成保存し、保存したクイズを出題します。</p>
  <p><span style="margin-right: 30px"><a href="save.php">新規作成</a></span>
  <span style="margin-right: 30px"><a href="edit.php">編集</a></span>
  <a href="quiz.php">出題</a></p>
</body>
</html>