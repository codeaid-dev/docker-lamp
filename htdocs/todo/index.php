<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['username'])){
  header('Location: login.php');
  exit;
}

if(isset($_POST['add'])){
//  $task = filter_input(INPUT_POST, 'task', FILTER_SANITIZE_SPECIAL_CHARS);
  $task = $_POST['task'];
  $sql = "INSERT INTO tasks (username, task) VALUES (:username, :task)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['username' => $_SESSION['username'], 'task' => $task]);
  header('Location: index.php');
  exit;
}

if(isset($_GET['del'])){
  $id = $_GET['del'];
  $sql = "DELETE FROM tasks WHERE id=:id AND username=:username";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['id' => $id, 'username' => $_SESSION['username']]);
  header('Location: index.php');
  exit;
}

$sql = "SELECT * FROM tasks WHERE username=:username";
$stmt = $pdo->prepare($sql);
$stmt->execute(['username' => $_SESSION['username']]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ToDoアプリ</title>
<style>
  table {
    border-collapse: collapse;
    border: 2px solid rgb(200, 200, 200);
    letter-spacing: 1px;
    font-family: sans-serif;
    font-size: 0.8rem;
  }
  td,th {
    border: 1px solid rgb(190, 190, 190);
    padding: 5px 10px;
  }
  td {
    text-align: center;
  }
</style>
</head>
<body>
  <h1>ToDo - <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
  <form method="post">
    <textarea name="task" rows="4" cols="30" placeholder="タスク追加"></textarea><br>
    <button type="submit" name="add">追加</button>
  </form>
  <br>
  <table>
    <tr>
      <th>タスク</th>
      <th>処理</th>
    </tr>
    <?php foreach($tasks as $task): ?>
    <tr>
      <td><?php echo nl2br(htmlspecialchars($task['task'])); ?></td>
      <td><a href="index.php?del=<?php echo $task['id']; ?>">削除</a>
      <a href="edit.php?edit=<?php echo $task['id']; ?>">修正</a></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <br>
  <a href="logout.php">ログアウト</a>
  <a href="leave.php" style="margin-left: 20px;">ユーザー登録解除</a>
</body>
</html>
