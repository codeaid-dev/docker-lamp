<?php
session_start();
if (isset($_SESSION['username'])) {
  $username = htmlspecialchars($_SESSION['username']);
  unset($_SESSION['username']);
  print <<<HTML
  <!DOCTYPE html>
  <html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ブログアプリ</title>
  </head>
  <body>
    <p>$username - ログアウトしました。</p>
    <p><a href="login.php">ログイン</a></p>
  </body>
  </html>
  HTML;
  exit();
}
header('Location: login.php');
?>
