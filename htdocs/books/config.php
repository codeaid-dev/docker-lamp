<?php
  //$dsn = 'mysql:host=localhost;dbname=books;charset=utf8mb4'; // XAMPP/MAMP/VMの場合
  $dsn = 'mysql:host=mysql;dbname=books;charset=utf8mb4'; // Dockerの場合
  //$dsn = 'sqlite:./books.db'; // SQLiteの場合
  $user = 'root';
  $password = 'password';
  try {
    $db = new PDO($dsn, $user, $password); //MySQL
    //$db = new PDO($dsn); //SQLite
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db->query("CREATE TABLE IF NOT EXISTS books (
      isbn VARCHAR(17) NOT NULL PRIMARY KEY,
      name VARCHAR(255) NOT NULL,
      price INT NOT NULL,
      page INT NOT NULL,
      date VARCHAR(255) NOT NULL
    )");
  } catch (PDOException $e) {
    die ('エラー：'.$e->getMessage());
  }
?>