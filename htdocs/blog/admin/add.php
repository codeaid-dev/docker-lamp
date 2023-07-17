<?php
require_once '../config.php';

try {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $article = $_POST['article'];
    $sql = 'INSERT INTO posts (newtime, updatetime, title, article) VALUES (NOW(), NOW(), ?, ?)';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $title, PDO::PARAM_STR);
    $stmt->bindValue(2, $article, PDO::PARAM_STR);
    $stmt->execute();
    header('Location: index.php');
    exit;
  }
} catch (PDOException $e) {
  die ('エラー：'.$e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>記事追加</title>
</head>
<body>
  <h1>記事追加</h1>
  <form method="POST">
    <label for="title">タイトル</label><br>
    <input type="text" id="title" name="title"><br><br>

    <label for="article">記事</label><br>
    <textarea id="article" name="article" rows="5" cols="40"></textarea>
    <p><button type="submit" name="add">追加</button></p>
  </form>
</body>
</html>
