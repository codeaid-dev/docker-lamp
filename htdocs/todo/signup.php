<?php
session_start();
require_once 'config.php';

if(isset($_SESSION['username'])){
  header('Location: index.php');
  exit;
}

if(isset($_POST['signup'])){
  try {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE username=:username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();
    if($user){
      $error = 'このユーザー名は登録できません。';
    } else if (preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[!-\/:-@[-`{-~])[!-~]{8,32}$/', $password) !== 1) {
        $error = 'パスワードは8~32文字で大小文字英字数字記号をそれぞれ1文字以上含める必要があります。';
    } else {
      $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(['username' => $username, 'password' => password_hash($password, PASSWORD_DEFAULT)]);
      $_SESSION['username'] = $username;
      header('Location: index.php');
      exit;
    }
  } catch (PDOException $e) {
    die ('エラー：'.$e->getMessage());
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
    <label>ユーザー名：<input type="text" name="username" required></label><br>
    <label>パスワード：<input type="password" name="password" required></label><br>
    <button type="submit" name="signup">登録</button>
  </form>
  <br>
  <a href="login.php">ログイン</a>
</body>
</html>