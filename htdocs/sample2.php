<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="css/style.css" rel="stylesheet">
  <title>サンプル2</title>
</head>
<body>
  <h1>サンプル2</h1>
  <?php
    $now = new DateTime();
    print $now->format('Y年m月d日G時i分s秒');

    $dsn = 'mysql:host=mysql;dbname=sampledb;charset=utf8';
    $user = 'root';
    $password = 'password';
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false];
    $data = [[1,'Yamada',85],[2,'Tanaka',79],[3,'Suzuki',63]];

    try {
      //$db = new PDO($dsn, $user, $password, $options); // MySQL
      //$db = new PDO("sqlite:./db/sample.db", options:$options); // SQLite
      $db = new PDO("sqlite:./db/sample.db"); // SQLite
      print '<p>接続成功</p>';

      $db->exec("CREATE TABLE IF NOT EXISTS users(
                id INTEGER PRIMARY KEY,
                name VARCHAR(20),
                score INTEGER)");
      print '<p>テーブル作成</p>';

      $stmt = $db->prepare("INSERT INTO users (id, name, score) VALUES(:id, :name, :score)");
      foreach ($data as $value) {
        $stmt->bindParam(':id', $value[0], PDO::PARAM_INT);
        $stmt->bindParam(':name', $value[1], PDO::PARAM_STR);
        $stmt->bindParam(':score', $value[2], PDO::PARAM_INT);
        $stmt->execute();
      }
      print '<p>データ挿入</p>';

      $stmt = $db->prepare("SELECT * FROM users WHERE score >= :score");
      $stmt->bindValue(':score',70,PDO::PARAM_INT);
      $stmt->execute();
      print '<p>70点以上選択</p>';
      print "<p>";
      while ($row = $stmt->fetch()) {
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