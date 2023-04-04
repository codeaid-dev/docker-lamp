<?php
session_start();
if (isset($_SESSION['username'])) {
  unset($_SESSION['username']);
  print <<<HTML
  <!DOCTYPE html>
  <html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDoアプリ</title>
  </head>
  <body>
    <p>ログアウトしました。</p>
    <p><a href="login.php">ログイン</a>
    <a href="signup.php" style="margin-left: 20px;">ユーザー登録</a></p>
  </body>
  </html>
  HTML;
  exit();
}
header('Location: login.php');
?>
