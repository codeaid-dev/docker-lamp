<?php
declare(strict_types=1);
/**
 * SQLiteデータベースに接続する
 */
function getConnection(): PDO
{
  $dbDir = dirname(__DIR__) . '/db';
  // DB保存ディレクトリがなければ作成
  if (!is_dir($dbDir)) {
    mkdir($dbDir, 0775, true);
  }
  $dbPath = $dbDir . '/quiz.db';
  $pdo = new PDO('sqlite:' . $dbPath);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  // 外部キー制約を有効化
  $pdo->exec('PRAGMA foreign_keys = ON');
  initializeDatabase($pdo);
  return $pdo;
}
/**
 * データベースを初期化する
 */
function initializeDatabase(PDO $pdo): void
{
  // categoriesテーブル
  $pdo->exec("
      CREATE TABLE IF NOT EXISTS categories (
          id INTEGER PRIMARY KEY AUTOINCREMENT,
          name TEXT NOT NULL UNIQUE
      )
  ");
  // questionsテーブル
  $pdo->exec("
      CREATE TABLE IF NOT EXISTS questions (
          id INTEGER PRIMARY KEY AUTOINCREMENT,
          category_id INTEGER NOT NULL,
          question TEXT NOT NULL,
          choice1 TEXT NOT NULL,
          choice2 TEXT NOT NULL,
          choice3 TEXT NOT NULL,
          choice4 TEXT NOT NULL,
          correct_answer INTEGER NOT NULL,
          explanation TEXT NOT NULL,
          created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,

          FOREIGN KEY (category_id)
              REFERENCES categories(id),

          CHECK (correct_answer BETWEEN 1 AND 4)
      )
  ");
  // wrong_answersテーブル
  $pdo->exec("
      CREATE TABLE IF NOT EXISTS wrong_answers (
          id INTEGER PRIMARY KEY AUTOINCREMENT,
          question_id INTEGER NOT NULL UNIQUE,
          wrong_count INTEGER NOT NULL DEFAULT 1,
          last_wrong_at TEXT,
          is_reviewed INTEGER NOT NULL DEFAULT 0,

          FOREIGN KEY (question_id)
              REFERENCES questions(id)
              ON DELETE CASCADE
      )
  ");
  // 初期カテゴリー登録
  $categories = [
      'Java',
      'SQL',
      'Servlet/JSP'
  ];
  $stmt = $pdo->prepare("
      INSERT OR IGNORE INTO categories (name)
      VALUES (:name)
  ");
  foreach ($categories as $name) {
      $stmt->execute([':name' => $name]);
  }
  // カテゴリーID取得
  $categoryStmt = $pdo->query("
      SELECT id, name FROM categories
  ");
  $categoryMap = [];
  foreach ($categoryStmt->fetchAll() as $category) {
      $categoryMap[$category['name']] = $category['id'];
  }
  // 初期問題登録
  $questions = [
    [
      'category' => 'Java',
      'question' => 'Javaのコンパイル後のファイルの拡張子は？',
      'choices' => [
        '.java',
        '.class',
        '.jar',
        '.exe'
      ],
      'correct' => 2,
      'explanation' => 'Javaのソースファイル（.java）をコンパイルすると、バイトコードを含む.classファイルが生成されます。'
    ],
    [
      'category' => 'SQL',
      'question' => 'データを検索するときに使用するSQL命令は？',
      'choices' => [
        'INSERT',
        'UPDATE',
        'SELECT',
        'DELETE'
      ],
      'correct' => 3,
      'explanation' => 'SELECTはテーブルからデータを検索・取得するためのSQL命令です。'
    ],
    [
      'category' => 'Servlet/JSP',
      'question' => 'HttpServletでGETリクエストを処理するメソッドは？',
      'choices' => [
        'doPost()',
        'doGet()',
        'service()',
        'init()'
      ],
      'correct' => 2,
      'explanation' => 'HttpServletでは、GETリクエストをdoGet()メソッドで処理します。'
    ]
  ];
  $insertStmt = $pdo->prepare("
      INSERT INTO questions (
          category_id,
          question,
          choice1,
          choice2,
          choice3,
          choice4,
          correct_answer,
          explanation
      )
      SELECT
          :category_id,
          :question,
          :choice1,
          :choice2,
          :choice3,
          :choice4,
          :correct_answer,
          :explanation
      WHERE NOT EXISTS (
          SELECT 1
          FROM questions
          WHERE category_id = :check_category_id
            AND question = :check_question
      )
  ");
  foreach ($questions as $q) {
      $categoryId = $categoryMap[$q['category']];

      $insertStmt->execute([
          ':category_id' => $categoryId,
          ':question' => $q['question'],
          ':choice1' => $q['choices'][0],
          ':choice2' => $q['choices'][1],
          ':choice3' => $q['choices'][2],
          ':choice4' => $q['choices'][3],
          ':correct_answer' => $q['correct'],
          ':explanation' => $q['explanation'],
          ':check_category_id' => $categoryId,
          ':check_question' => $q['question']
      ]);
  }
}
