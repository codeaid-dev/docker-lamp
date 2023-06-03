<?php
//$host = 'localhost'; //XAMPP,MAMP,VM
$host = 'mysql'; //Docker
$dbname = 'todo';
$dbuser = 'root';
$dbpass = 'password';

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
try {
  $pdo = new PDO($dsn, $dbuser, $dbpass); //MySQL
  //$pdo = new PDO('sqlite:./todo.db'); //SQLite
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
