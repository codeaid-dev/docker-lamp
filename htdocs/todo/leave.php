<?php
require_once 'config.php';
if (!isset($_SESSION['username'])) {
  header('Location: login.php');
  exit();
} else {
  $user = $_SESSION['username'];
  $sql = "DELETE FROM users WHERE username=:username";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['username' => $user]);
  unset($_SESSION['username']);
  $sql = "DELETE FROM tasks WHERE username=:username";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['username' => $user]);
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
  <p><?php echo htmlspecialchars($user); ?>のユーザー登録を解除しました。</p>
  <p><a href="login.php">ログイン</a>
  <a href="signup.php" style="margin-left: 20px;">ユーザー登録</a></p>
</body>
</html>
