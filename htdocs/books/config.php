<?php
  //$dsn = 'mysql:host=localhost;dbname=books;charset=utf8mb4'; // XAMPP/MAMP/VMの場合
  $dsn = 'mysql:host=mysql;dbname=books;charset=utf8mb4'; // Dockerの場合
  //$dsn = 'sqlite:./books.db'; // SQLiteの場合
  $user = 'root';
  $password = 'password';
  try {
    $pdo = new PDO($dsn, $user, $password); //MySQL
    //$pdo = new PDO('sqlite:./books.db'); //SQLite
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->query("CREATE TABLE IF NOT EXISTS books (
      isbn VARCHAR(17) NOT NULL PRIMARY KEY,
      name VARCHAR(255) NOT NULL,
      price INT NOT NULL,
      page INT NOT NULL,
      date VARCHAR(255) NOT NULL
    );");
  } catch (PDOException $e) {
    die ('エラー：'.$e->getMessage());
  }
?>