<?php
require_once '../config.php';

try {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    if (isset($_POST['update'])) {
      $title = $_POST['title'];
      $article = $_POST['article'];
      $sql = 'UPDATE posts SET updatetime=NOW(), title=?, article=? WHERE id=?';
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(1, $title, PDO::PARAM_STR);
      $stmt->bindValue(2, $article, PDO::PARAM_STR);
      $stmt->bindValue(3, $id, PDO::PARAM_INT);
      $stmt->execute();
    } else if (isset($_POST['delete'])) {
      $sql = 'DELETE FROM posts WHERE id=?';
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(1, $id, PDO::PARAM_INT);
      $stmt->execute();
    }
    header('Location: index.php');
    exit;
  } else {
    $id = $_GET['id'];
    $sql = 'SELECT * FROM posts WHERE id=?';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $id, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
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
  <title>記事編集</title>
</head>
<body>
  <h1>記事編集</h1>
  <?php if (!empty($post)) { ?>
    <form method="POST">
      <label for="title">タイトル</label><br>
      <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>"><br><br>

      <label for="article">記事</label><br>
      <textarea id="article" name="article" rows="5" cols="40"><?php echo htmlspecialchars($post['article'], ENT_QUOTES, 'UTF-8'); ?></textarea>
      <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
      <p><button type="submit" name="update" style="margin-right:20px;">更新</button><button type="submit" name="delete">削除</button></p>
    </form>
  <?php } else { ?>
    <p>記事がありません。</p>
  <?php } ?>
</body>
</html>
