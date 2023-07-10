<?php
//$host = 'localhost'; //XAMPP,MAMP,VM
$host = 'mysql'; //Docker
$dbname = 'survey';
$dbuser = 'root';
$dbpass = 'password';

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
try {
  $pdo = new PDO($dsn, $dbuser, $dbpass); //MySQL
  //$pdo = new PDO('sqlite:./survey.db'); //SQLite
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  $pdo->query("CREATE TABLE IF NOT EXISTS answers (
    name VARCHAR(256) NOT NULL,
    email VARCHAR(256) NOT NULL PRIMARY KEY,
    age VARCHAR(256) NOT NULL,
    program VARCHAR(256) NOT NULL,
    pc VARCHAR(256) NOT NULL,
    maker VARCHAR(256) NOT NULL,
    comments VARCHAR(256) NOT NULL
  );");
} catch (PDOException $e) {
  die ('エラー：'.$e->getMessage());
}
