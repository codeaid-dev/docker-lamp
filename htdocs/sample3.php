<?php
$name = "";
$hobby = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = htmlspecialchars($_POST['name']);
  $hobby = htmlspecialchars($_POST['hobby']);
  if ($name == "" || $hobby == "") {
    print '<p>空白のフィールドがあります。</p>';
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>サンプル3</title>
</head>
<body>
  <h1>サンプル3</h1>
  <?php if ($name != "" && $hobby != ""): ?>
    <p>
      <?= $name ?>さんの趣味は、<br>
      <?= $hobby ?><br>
      です。
    </p>
    <a href="">戻る</a>
  <?php else: ?>
    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
      <p><label>名前:<input type="text" name="name"></label></p>
      <p><label>趣味:<input type="text" name="hobby"></label></p>
      <button type="submit">表示</button>
    </form>
  <?php endif; ?>
</body>
</html>