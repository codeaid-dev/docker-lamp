<?php
  //$dsn = 'mysql:host=localhost;dbname=quiz;charset=utf8mb4'; // XAMPP/MAMP/VMの場合
  $dsn = 'mysql:host=mysql;dbname=quiz;charset=utf8mb4'; // Dockerの場合
  //$dsn = 'sqlite:./quiz.db'; // SQLiteの場合
  $user = 'root';
  $password = 'password';
  try {
    $db = new PDO($dsn, $user, $password); //MySQL
    //$db = new PDO($dsn); //SQLite
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // 例外を出力する
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // 静的プレースホルダーを指定
    //For SQLite using AUTOINCREMENT, for MySQL using AUTO_INCREMENT
    $db->query("CREATE TABLE IF NOT EXISTS questions (
      id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
      question VARCHAR(255) NOT NULL,
      answer VARCHAR(255) NOT NULL
    )");
  } catch (PDOException $e) {
    die ('エラー：'.$e->getMessage());
  }
?>