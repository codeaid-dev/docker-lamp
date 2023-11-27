<?php
//$dsn = 'mysql:host=localhost;dbname=survey;charset=utf8mb4'; // XAMPP/MAMP/VMの場合
$dsn = 'mysql:host=mysql;dbname=survey;charset=utf8mb4'; // Dockerの場合
//$dsn = 'sqlite:./survey.db'; // SQLiteの場合
$user = 'root';
$password = 'password';
try {
  $pdo = new PDO($dsn, $user, $password); //MySQL
  //$pdo = new PDO($dsn); //SQLite
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  $pdo->query("CREATE TABLE IF NOT EXISTS answers (
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    name VARCHAR(256) NOT NULL,
    email VARCHAR(256) NOT NULL PRIMARY KEY,
    age VARCHAR(256) NOT NULL,
    program VARCHAR(256),
    pc VARCHAR(256) NOT NULL,
    maker VARCHAR(256) NOT NULL,
    comments VARCHAR(256)
  );");
  $pdo->query("CREATE TABLE IF NOT EXISTS siteadmin (
    username VARCHAR(256) NOT NULL PRIMARY KEY,
    password VARCHAR(256) NOT NULL
  );");
} catch (PDOException $e) {
  die ('エラー：'.$e->getMessage());
}
