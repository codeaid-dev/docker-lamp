<?php
//$host = 'localhost'; //XAMPP,MAMP,VM
$host = 'mysql'; //Docker
$dbname = 'blog';
$user = 'root';
$password = 'password';
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
try {
  $pdo = new PDO($dsn, $user, $password); //MySQL
  //$pdo = new PDO('sqlite:./survey.db'); //SQLite
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  $pdo->query("CREATE TABLE IF NOT EXISTS posts (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    title VARCHAR(256) NOT NULL,
    article VARCHAR(256) NOT NULL
  );");
  $pdo->query("CREATE TABLE IF NOT EXISTS siteadmin (
    username VARCHAR(256) NOT NULL PRIMARY KEY,
    password VARCHAR(256) NOT NULL
  );");
} catch (PDOException $e) {
  die ('エラー：'.$e->getMessage());
}
