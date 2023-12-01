<?php
require_once 'config.php';

if(!isset($_SESSION['username'])){
  header('Location: login.php');
  exit;
}

if(isset($_POST['edit'])){
  $task = $_POST['task'];
  $id = $_POST['id'];
  $sql = "UPDATE tasks SET task=:task WHERE username=:username AND id=:id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['task' => $task, 'username' => $_SESSION['username'], 'id' => $id]);
  header('Location: index.php');
  exit;
}

if(isset($_GET['edit'])){
  $id = $_GET['edit'];
  $sql = "SELECT * FROM tasks WHERE id=:id AND username=:username";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['id' => $id, 'username' => $_SESSION['username']]);
  $task = $stmt->fetchAll()[0];
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
  <h1>ToDo - <?php echo $_SESSION['username'] ?></h1>
  <form method="post">
    <input type="hidden" name="id" value="<?php echo $id ?? ''; ?>">
    <textarea name="task" rows="4" cols="30"><?php echo $task['task'] ?? '' ?></textarea><br>
    <button type="submit" name="edit">修正</button>
  </form>
  <br>
  <a href="logout.php">ログアウト</a>
  <a href="leave.php" style="margin-left: 20px;">ユーザー登録解除</a>
</body>
</html>
