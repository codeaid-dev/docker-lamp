<?php
//$dsn = 'mysql:host=localhost;dbname=todo;charset=utf8mb4'; // XAMPP/MAMP/VMの場合
$dsn = 'mysql:host=mysql;dbname=todo;charset=utf8mb4'; // Dockerの場合
//$dsn = 'sqlite:./todo.db'; // SQLiteの場合
$user = 'root';
$password = 'password';
try {
  $pdo = new PDO($dsn, $user, $password); //MySQL
  //$pdo = new PDO($dsn); //SQLite
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  $pdo->query("CREATE TABLE IF NOT EXISTS users (
    username VARCHAR(256) NOT NULL PRIMARY KEY,
    password VARCHAR(256) NOT NULL
  );");
  $pdo->query("CREATE TABLE IF NOT EXISTS tasks (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(256) NOT NULL,
    task VARCHAR(256) NOT NULL
  );");
} catch (PDOException $e) {
  die ('エラー：'.$e->getMessage());
}
