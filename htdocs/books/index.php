<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>書籍データ庫</title>
</head>
<body>
  <h1>書籍データ庫</h1>
  <p>書籍の情報を格納します。</p>
  <ul>
    <li>ISBN</li>
    <li>書籍名</li>
    <li>価格</li>
    <li>ページ数</li>
    <li>発売日</li>
  </ul>
  <p><span style="margin-right: 30px"><a href="write.php">保存ページ</a></span>
  <a href="read.php">閲覧ページ</a></p>
</body>
</html>