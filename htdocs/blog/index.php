<?php
require_once 'config.php';

$stmt = $pdo->query("SELECT id,title,created_at,updated_at FROM posts ORDER BY updated_at DESC");
$posts = $stmt->fetchAll();
?>
<?php if (isset($_GET['post'])) { ?>
  <?php
    $id = intval($_GET['post']);
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id=?");
    $stmt->bindParam(1, $id, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch();
  ?>
  <?php if ($post) { ?>
    <!DOCTYPE html>
    <html lang="ja">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>ブログアプリ</title>
    </head>
    <body>
      <h1><?= htmlspecialchars($post['title']) ?></h1>
      <p>
        作成日時：<?php echo htmlspecialchars($post['created_at']); ?><br>
        更新日時：<?php echo htmlspecialchars($post['updated_at']); ?>
      </p>
      <p><?php echo nl2br(htmlspecialchars($post['article'])); ?></p>
      <p><a href="index.php">トップ</a></p>
    </body>
    </html>
  <?php } else {
    http_response_code(404);
    include_once("404.html");
    exit;
  } ?>
<?php } else { ?>
  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8">
    <title>ブログアプリ</title>
  </head>
  <body>
    <h1>ブログアプリ</h1>
    <h2>記事一覧</h2>
    <?php if (!empty($posts)): ?>
      <ul>
        <?php foreach ($posts as $post): ?>
          <li>
            <?php $created_at = new DateTime($post['created_at']);
            $updated_at = new DateTime($post['updated_at']); ?>
            <p><a href="index.php?post=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></a><br>
            <span style="font-size:10px; margin-right:10px;">作成日：<?php echo $created_at->format('Y/m/d'); ?></span>
            <span style="font-size:10px;">更新日：<?php echo $updated_at->format('Y/m/d'); ?></span></p>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>記事がありません。</p>
    <?php endif; ?>
  </body>
  </html>
<?php } ?>