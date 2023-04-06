<?php
session_start();
require_once 'config.php';

if(isset($_SESSION['username'])){
  header('Location: index.php');
  exit;
}

if(isset($_POST['signup'])){
  $username = $_POST['username'];
  $password = $_POST['password'];
  $sql = "SELECT * FROM users WHERE username=:username";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['username' => $username]);
  $user = $stmt->fetch();
  if($user){
    $error = 'このユーザー名は登録できません。';
  } else {
    $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username, 'password' => password_hash($password, PASSWORD_DEFAULT)]);
    $_SESSION['username'] = $username;
    header('Location: index.php');
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ToDoアプリ</title>
</head>
<body>
  <h1>ユーザー登録</h1>
  <?php if (isset($error)) : ?>
    <p><?php echo $error; ?></p>
  <?php endif; ?>
  <form method="post">
    <label>ユーザー名：<input type="text" name="username" value="<?= $username ?? '' ?>"></label><br>
    <label>パスワード：<input type="password" name="password" value="<?= $password ?? '' ?>"></label><br>
    <button type="submit" name="signup">登録</button>
  </form>
  <br>
  <a href="index.php">トップページ</a>
  <a href="login.php" style="margin-left: 20px;">ログイン</a>
</body>
</html>