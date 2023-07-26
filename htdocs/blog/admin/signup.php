<?php
session_start();
require_once '../config.php';

if(isset($_SESSION['username'])){
  header('Location: index.php');
  exit;
}

try {
  $sql = "SELECT * FROM siteadmin";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $user = $stmt->fetch();
} catch (PDOException $e) {
  die ('エラー：'.$e->getMessage());
}
if($user){
  $exist = '管理者はすでに登録済みです。';
}

if(isset($_POST['signup'])){
  try {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if (preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[!-\/:-@[-`{-~])[!-~]{8,32}$/', $password) !== 1) {
        $error = 'パスワードは8~32文字で大小文字英字数字記号をそれぞれ1文字以上含める必要があります。';
    } else {
      $sql = "INSERT INTO siteadmin (username, password) VALUES (:username, :password)";
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
  <title>ブログアプリ</title>
</head>
<body>
  <?php if (isset($exist)) { ?>
    <p><?php echo $exist; ?></p>
  <?php } else { ?>
    <?php if (isset($error)) { ?>
      <p><?php echo $error; ?></p>
    <?php } ?>
    <h1>管理者登録</h1>
    <form method="post">
      <label>ユーザー名：<input type="text" name="username" required></label><br>
      <label>パスワード：<input type="password" name="password" required></label><br>
      <button type="submit" name="signup">登録</button>
    </form>
  <?php } ?>
  <p><a href="login.php">戻る</a></p>
</body>
</html>