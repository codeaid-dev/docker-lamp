<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="css/style.css" rel="stylesheet">
  <title>サンプル</title>
</head>
<body>
  <h1>サンプル</h1>
  <?php
    $now = new DateTime();
    print $now->format('Y年m月d日G時i分s秒');

    $dsn = 'mysql:host=mysql;dbname=sampledb;charset=utf8';
    $user = 'root';
    $password = 'password';

    try {
      //$db = new PDO($dsn, $user, $password);
      $db = new PDO("sqlite:./db/sample.db");
      print '<p>接続成功</p>';

      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $db->exec("CREATE TABLE IF NOT EXISTS users(
                id INTEGER PRIMARY KEY,
                name VARCHAR(20),
                score INTEGER)");
      print '<p>テーブル作成</p>';

      $db->exec("INSERT INTO users VALUES(1, 'Yamada', 85)");
      $db->exec("INSERT INTO users VALUES(2, 'Tanaka', 79)");
      $db->exec("INSERT INTO users VALUES(3, 'Suzuki', 63)");
      print '<p>データ挿入</p>';

      $q = $db->query("SELECT * FROM users WHERE score >= 70");
      print '<p>70点以上選択</p>';
      print "<p>";
      while ($row = $q->fetch()) {
        print $row["id"] . " " . $row["name"] . " " . $row["score"] . "<br>";
      }
      print "</p>";
  
      $db->exec("DROP TABLE users");
      print '<p>テーブル削除</p>';
  
    } catch (PDOException $e) {
      die ('エラー：'.$e->getMessage());
    }
  ?>
</body>
</html>