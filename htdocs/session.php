<?php
  $expire = 30;
  ini_set('session.gc_divisor', 1);
  ini_set('session.gc_maxlifetime', $expire);
  session_set_cookie_params($expire);
  session_start();

  if (isset($_POST["logout"])) {
    $_SESSION = array();
    session_destroy();
    header('Location: session.php');
    exit();
  }
  if (isset($_POST['login'])) {
    if ($_POST['password']!='1234') {
      print('<p>パスワードが違います</p>');
    } else {
      $_SESSION['username'] = $_POST['username'];
    }
  }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sessionサンプル</title>
</head>
<body>
  <?php if (isset($_SESSION['username'])) { ?>
    <p><?php echo htmlspecialchars($_SESSION['username']); ?> - ログイン中です。</p>
    <form method="post">
      <button type="submit" name="logout">ログアウト</button>
    </form>
  <?php } else { ?>
    <h2>ログイン</h2>
    <form method="post">
      <label>ユーザー名：<input type="text" name="username" required></label><br>
      <label>パスワード：<input type="password" name="password" required></label><br>
      <button type="submit" name="login">ログイン</button>
    </form>
  <?php } ?>
</body>
</html>