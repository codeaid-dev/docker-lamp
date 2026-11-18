<?php
declare(strict_types=1);
/**
 * HTMLエスケープ
 */
function h(mixed $value): string
{
  return htmlspecialchars(
    (string)$value,
    ENT_QUOTES,
    'UTF-8'
  );
}
/**
 * リダイレクト
 */
function redirect(string $url): never
{
  header('Location: ' . $url);
  exit;
}
/**
 * カテゴリー一覧取得
 */
function getCategories(PDO $pdo): array
{
  $stmt = $pdo->query("
      SELECT id, name
      FROM categories
      ORDER BY id
  ");
  return $stmt->fetchAll();
}
/**
 * 問題一覧取得
 */
function getQuestions(PDO $pdo): array
{
  $stmt = $pdo->query("
      SELECT
          q.*,
          c.name AS category_name
      FROM questions q
      INNER JOIN categories c
          ON q.category_id = c.id
      ORDER BY q.id DESC
  ");
  return $stmt->fetchAll();
}
/**
 * 問題1件取得
 */
function getQuestion(PDO $pdo, int $id): ?array
{
  $stmt = $pdo->prepare("
      SELECT *
      FROM questions
      WHERE id = :id
  ");
  $stmt->execute([':id' => $id]);
  $question = $stmt->fetch();
  return $question ?: null;
}
/**
 * 問題の入力値検証
 *
 * エラーがなければ空配列を返す
 */
function validateQuestion(array $data, PDO $pdo): array
{
  $errors = [];
  $categoryId = filter_var(
    $data['category_id'] ?? null,
    FILTER_VALIDATE_INT
  );
  $question = trim($data['question'] ?? '');

  $choices = [
    trim($data['choice1'] ?? ''),
    trim($data['choice2'] ?? ''),
    trim($data['choice3'] ?? ''),
    trim($data['choice4'] ?? '')
  ];
  $correct = filter_var(
    $data['correct_answer'] ?? null,
    FILTER_VALIDATE_INT
  );
  $explanation = trim($data['explanation'] ?? '');
  // カテゴリー確認
  if ($categoryId === false || $categoryId === null) {
    $errors[] = 'カテゴリーを選択してください。';
  } else {
    $stmt = $pdo->prepare("
      SELECT COUNT(*)
      FROM categories
      WHERE id = :id
    ");
    $stmt->execute([':id' => $categoryId]);
    if ((int)$stmt->fetchColumn() === 0) {
      $errors[] = '選択したカテゴリーが存在しません。';
    }
  }
  // 問題文
  if ($question === '') {
    $errors[] = '問題文を入力してください。';
  } elseif (mb_strlen($question) > 500) {
    $errors[] = '問題文は500文字以内で入力してください。';
  }
  // 選択肢
  foreach ($choices as $index => $choice) {
    if ($choice === '') {
      $errors[] = '選択肢' . ($index + 1) . 'を入力してください。';
    } elseif (mb_strlen($choice) > 200) {
      $errors[] = '選択肢は200文字以内で入力してください。';
    }
  }
  // 正解番号
  if ($correct === false || $correct === null ||
    $correct < 1 || $correct > 4) {
    $errors[] = '正解を選択してください。';
  }
  // 解説
  if ($explanation === '') {
    $errors[] = '解説を入力してください。';
  } elseif (mb_strlen($explanation) > 2000) {
    $errors[] = '解説は2000文字以内で入力してください。';
  }
  return $errors;
}
/**
 * 問題の登録
 */
function insertQuestion(PDO $pdo, array $data): void
{
  $stmt = $pdo->prepare("
    INSERT INTO questions (
      category_id,
      question,
      choice1,
      choice2,
      choice3,
      choice4,
      correct_answer,
      explanation
    ) VALUES (
      :category_id,
      :question,
      :choice1,
      :choice2,
      :choice3,
      :choice4,
      :correct_answer,
      :explanation
    )
  ");
  $stmt->execute([
    ':category_id' => (int)$data['category_id'],
    ':question' => trim($data['question']),
    ':choice1' => trim($data['choice1']),
    ':choice2' => trim($data['choice2']),
    ':choice3' => trim($data['choice3']),
    ':choice4' => trim($data['choice4']),
    ':correct_answer' => (int)$data['correct_answer'],
    ':explanation' => trim($data['explanation'])
  ]);
}
/**
 * 問題の更新
 */
function updateQuestion(PDO $pdo, int $id, array $data): void {
  $stmt = $pdo->prepare("
    UPDATE questions
    SET
      category_id = :category_id,
      question = :question,
      choice1 = :choice1,
      choice2 = :choice2,
      choice3 = :choice3,
      choice4 = :choice4,
      correct_answer = :correct_answer,
      explanation = :explanation
    WHERE id = :id
  ");
  $stmt->execute([
    ':category_id' => (int)$data['category_id'],
    ':question' => trim($data['question']),
    ':choice1' => trim($data['choice1']),
    ':choice2' => trim($data['choice2']),
    ':choice3' => trim($data['choice3']),
    ':choice4' => trim($data['choice4']),
    ':correct_answer' => (int)$data['correct_answer'],
    ':explanation' => trim($data['explanation']),
    ':id' => $id
  ]);
}
/**
 * 問題の削除
 */
function deleteQuestion(PDO $pdo, int $id): void
{
  $stmt = $pdo->prepare("
    DELETE FROM questions
    WHERE id = :id
  ");
  $stmt->execute([':id' => $id]);
}
/**
 * 問題登録・編集フォーム用の初期値
 */
function emptyQuestion(): array
{
  return [
    'category_id' => '',
    'question' => '',
    'choice1' => '',
    'choice2' => '',
    'choice3' => '',
    'choice4' => '',
    'correct_answer' => '',
    'explanation' => ''
  ];
}
