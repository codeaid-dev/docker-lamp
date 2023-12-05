<?php
  if (isset($_COOKIE['count'])) {
    $count = (int)$_COOKIE['count'];
    $count += 1;
  } else {
    $count = 1;
  }
  if (isset($_POST['clear'])) {
    setcookie('count', $count, time());
    header('Location: cookie.php');
    exit();
  } else {
    setcookie('count', $count, time()+20);
  }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cookieサンプル</title>
</head>
<body>
  <h2>アクセス数：<?= $count ?>回</h2>
  <form method="POST">
    <button type="submit" name="clear">カウントクリア</button>
  </form>
</body>
</html>