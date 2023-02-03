<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (in_array('keyword',array_keys($_POST))) {
    $keyword = htmlspecialchars($_POST['keyword']);
  }
  if (in_array('delete',array_keys($_POST))) {
    $delete = htmlspecialchars($_POST['delete']);
  }
  if (!isset($keyword) && !isset($delete)) {
    header('Location: read.php');
    exit();
  }

  //$dsn = 'mysql:host=localhost;dbname=bookstore;charset=utf8'; // XAMPP/MAMP/VMの場合
  $dsn = 'mysql:host=mysql;dbname=bookstrage;charset=utf8'; // Dockerの場合
  //$dsn = 'sqlite:./bookstore.db'; // SQLiteの場合
  $user = 'root';
  $password = 'password';

  try {
    $db = new PDO($dsn, $user, $password);
    //$db = new PDO($dsn); //SQLiteの場合
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // prepareを使用する際のSQLインジェクション対策(必須)
    if (isset($delete)) {
      $stmt = $db->prepare("DELETE FROM books WHERE isbn=:isbn");
      $stmt->execute([':isbn'=>$delete]);
    } else {
      $stmt = $db->prepare("SELECT * FROM books WHERE isbn=:isbn OR name LIKE :name");
      $stmt->bindParam(':isbn', $keyword, PDO::PARAM_STR);
      $name = "%".$keyword."%";
      $stmt->bindParam(':name', $name, PDO::PARAM_STR);
      $stmt->execute();
    }
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
  <title>書籍データ庫</title>
</head>
<body>
  <h1>書籍データ庫</h1>
  <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
    <p><label>ISBNもしくは書籍名：<input type="text" name="keyword"></label></p>
    <p><button type="submit">表示</button></p>
  </form>
  <p><span style="margin-right: 30px"><a href="index.html">トップ</a></span><a href="write.php">保存ページ</a></p>
  <hr>
  <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($keyword)): ?>
    <?php while ($row = $stmt->fetch()): ?>
      <p>ISBN：<?= htmlspecialchars($row['isbn']) ?></p>
      <p>書籍名：<?= htmlspecialchars($row['name']) ?></p>
      <p>価格：<?= htmlspecialchars($row['price']) ?>円（税抜）</p>
      <p>ページ数：<?= htmlspecialchars($row['page']) ?></p>
      <p>発売日：<?= htmlspecialchars($row['date']) ?></p>
      <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" style="display:inline-block">
        <input type="hidden" name="delete" value=<?= htmlspecialchars($row['isbn']) ?>>
        <p><button type="submit">削除</button></p>
      </form>
      <form action="update.php" method="POST" style="display:inline-block">
        <input type="hidden" name="update" value=<?= htmlspecialchars($row['isbn']) ?>>
        <input type="hidden" name="name" value=<?= htmlspecialchars($row['name']) ?>>
        <input type="hidden" name="price" value=<?= htmlspecialchars($row['price']) ?>>
        <input type="hidden" name="page" value=<?= htmlspecialchars($row['page']) ?>>
        <input type="hidden" name="date" value=<?= htmlspecialchars($row['date']) ?>>
        <p><button type="submit">修正</button></p>
      </form>
      <p>-------------------------</p>
      <br>
    <?php endwhile; ?>
  <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($delete)): ?>
    <p><?= "ISBN: $delete を削除しました。" ?></p>
  <?php endif; ?>
</body>
</html>