<?php
require_once '../config.php';
if (!isset($_SESSION['username'])) {
  header('Location: login.php');
  exit();
} else {
  $user = $_SESSION['username'];
  $sql = "DELETE FROM siteadmin WHERE username=:username";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['username' => $user]);
  unset($_SESSION['username']);
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>アンケート</title>
</head>
<body>
  <p><?php echo htmlspecialchars($user); ?>のユーザー登録を解除しました。</p>
  <p><a href="signup.php">ユーザー登録</a></p>
</body>
</html>
