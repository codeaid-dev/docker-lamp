<?php
$expire = 60;
ini_set('session.gc_divisor', 1);
ini_set('session.gc_maxlifetime', $expire);
session_set_cookie_params($expire);
session_start();

//$dsn = 'mysql:host=localhost;dbname=blog;charset=utf8mb4'; // XAMPP/MAMP/VMの場合
$dsn = 'mysql:host=mysql;dbname=blog;charset=utf8mb4'; // Dockerの場合
//$dsn = 'sqlite:'.$_SERVER['DOCUMENT_ROOT'].'/blog/blog.db'; // SQLiteの場合(DBファイルへの絶対パス)
$user = 'root';
$password = 'password';
try {
  $pdo = new PDO($dsn, $user, $password); //MySQL
  //$pdo = new PDO($dsn); //SQLite
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  //MySQL
  $pdo->query("CREATE TABLE IF NOT EXISTS posts (
    id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    title VARCHAR(256) NOT NULL,
    article VARCHAR(256) NOT NULL
  );");
  //SQLite
  /*$pdo->query("CREATE TABLE IF NOT EXISTS posts (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    created_at DATETIME DEFAULT (DATETIME('now','localtime')),
    updated_at DATETIME DEFAULT (DATETIME('now','localtime')),
    title VARCHAR(256) NOT NULL,
    article VARCHAR(256) NOT NULL
  );");
  $pdo->query("CREATE TRIGGER IF NOT EXISTS trigger_updated_at AFTER UPDATE ON posts
    BEGIN
    UPDATE posts SET updated_at = DATETIME('now', 'localtime') WHERE rowid == NEW.rowid;
    END
  ");*/

  $pdo->query("CREATE TABLE IF NOT EXISTS siteadmin (
    username VARCHAR(256) NOT NULL PRIMARY KEY,
    password VARCHAR(256) NOT NULL
  );");
} catch (PDOException $e) {
  die ('エラー：'.$e->getMessage());
}
