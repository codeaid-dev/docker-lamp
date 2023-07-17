<?php
session_start();
require_once '../config.php';

if(!isset($_SESSION['username'])){
  header('Location: login.php');
  exit;
}

$stmt = $pdo->query("SELECT * FROM posts ORDER BY updatetime DESC");
$posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ブログアプリ</title>
</head>
<body>
  <h1>管理画面</h1>
  <p><a href="add.php"><button>新規追加</button></a></p>
  <h2>記事一覧</h2>
  <?php if (!empty($posts)): ?>
    <ul>
      <?php foreach ($posts as $post): ?>
        <li>
          <?php $newtime = new DateTime($post['newtime']);
          $updatetime = new DateTime($post['updatetime']); ?>
          <p><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?><br>
          <span style="font-size:10px; margin-right:10px;">作成日：<?php echo $newtime->format('Y/m/d'); ?></span>
          <span style="font-size:10px;">更新日：<?php echo $updatetime->format('Y/m/d'); ?></span><br>
          <a href="edit.php?action=edit&id=<?php echo $post['id']; ?>">編集</a></p>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>記事がありません。</p>
  <?php endif; ?>
  <p><a href="logout.php">ログアウト</a></p>
</body>
</html>
