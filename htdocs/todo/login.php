<?php
session_start();
require_once 'config.php';

if(isset($_SESSION['username'])){
  header('Location: index.php');
  exit;
}

if(isset($_POST['login'])){
  $username = $_POST['username'];
  $password = $_POST['password'];
  $sql = "SELECT * FROM users WHERE username=:username";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['username' => $username]);
  $user = $stmt->fetch();
  if($user && password_verify($password, $user['password'])){
    $_SESSION['username'] = $username;
    header('Location: index.php');
    exit;
  } else {
    $error = 'ログイン失敗';
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
  <h1>ログイン</h1>
  <?php if (isset($error)) : ?>
    <p><? echo $error; ?></p>
  <?php endif; ?> 
  <form method="post">
    <label>ユーザー名：<input type="text" name="username"></label><br>
    <label>パスワード：<input type="password" name="password"></label><br>
    <button type="submit" name="login">送信</button>
  </form>
  <br>
  <a href="signup.php">ユーザー登録</a>
</body>
</html>