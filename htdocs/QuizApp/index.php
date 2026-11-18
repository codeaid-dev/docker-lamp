<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/lib/functions.php';
$pdo = getConnection();
$page = $_GET['page'] ?? 'list';
$errors = [];
$formData = emptyQuestion();
$editId = 0;
// フラッシュメッセージ
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
/* =====================================
   POST処理
===================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  /* ---------- 問題登録 ---------- */
  if ($action === 'create') {
    $formData = $_POST;
    $errors = validateQuestion($formData, $pdo);
    if (empty($errors)) {
      insertQuestion($pdo, $formData);
      $_SESSION['message'] = '問題を登録しました。';
      redirect('index.php?page=list');
    }
    $page = 'register';
  }
  /* ---------- 問題更新 ---------- */
  elseif ($action === 'update') {
    $editId = filter_var(
      $_POST['id'] ?? null,
      FILTER_VALIDATE_INT
    );
    $formData = $_POST;
    $errors = validateQuestion($formData, $pdo);
    if ($editId === false || $editId === null || $editId < 1) {
      $errors[] = '問題IDが不正です。';
    } elseif (getQuestion($pdo, $editId) === null) {
      $errors[] = '更新対象の問題が存在しません。';
    }
    if (empty($errors)) {
      updateQuestion($pdo, $editId, $formData);
      $_SESSION['message'] = '問題を更新しました。';
      redirect('index.php?page=list');
    }
    $page = 'edit';
  }
  /* ---------- 問題削除 ---------- */
  elseif ($action === 'delete') {
    $id = filter_var(
      $_POST['id'] ?? null,
      FILTER_VALIDATE_INT
    );
    if ($id === false || $id === null || $id < 1) {
      $_SESSION['message'] = '問題IDが不正です。';
    } elseif (getQuestion($pdo, $id) === null) {
      $_SESSION['message'] = '削除対象の問題が存在しません。';
    } else {
      deleteQuestion($pdo, $id);
      $_SESSION['message'] = '問題を削除しました。';
    }
    redirect('index.php?page=list');
  }
  /* ---------- クイズ開始 ---------- */
  elseif ($action === 'quiz_start') {
    $count = filter_var(
      $_POST['question_count'] ?? null,
      FILTER_VALIDATE_INT
    );
    if ($count === false || $count === null ||
      $count < 1 || $count > 50) {
      $errors[] = '出題数は1～50で指定してください。';
      $page = 'quiz_start';
    } else {
      // ランダムに問題を取得
      $stmt = $pdo->prepare("
        SELECT *
        FROM questions
      ");
      // SQLを実行
      $stmt->execute();
      // 問題をすべて取得
      $allQuestions = $stmt->fetchAll();
      // 問題数を取得
      $totalCount = count($allQuestions);
      // 出題数が登録問題数を超える場合はエラー
      if ($count > $totalCount) {
        $errors[] = "出題数は{$totalCount}問以下にしてください。";
        $page = 'quiz_start';
      } else {
        // PHP側で問題をランダムに並べ替える
        shuffle($allQuestions);
        // 指定された問題数だけ抽出
        $questions = array_slice($allQuestions, 0, $count);
        $_SESSION['quiz'] = [
          'questions' => $questions,
          'current' => 0,
          'correct_count' => 0,
          'answers' => []
        ];
        redirect('index.php?page=quiz_question');
      }
      if (empty($questions)) {
        $errors[] = '出題できる問題がありません。';
        $page = 'quiz_start';
      } else {
        $_SESSION['quiz'] = [
          'questions' => $questions,
          'current' => 0,
          'correct_count' => 0,
          'answers' => []
        ];
        redirect('index.php?page=quiz_question');
      }
    }
  }
  /* ---------- クイズ回答 ---------- */
  elseif ($action === 'answer') {
    if (!isset($_SESSION['quiz'])) {
      redirect('index.php?page=quiz_start');
    }
    $quiz = &$_SESSION['quiz'];
    $current = $quiz['current'];
    $question = $quiz['questions'][$current] ?? null;
    $answer = filter_var(
      $_POST['answer'] ?? null,
      FILTER_VALIDATE_INT
    );
    if ($question === null ||
        $answer === false ||
        $answer === null ||
        $answer < 1 ||
        $answer > 4) {
      $errors[] = '回答を選択してください。';
      $page = 'quiz_question';
    } else {
      // 回答済みかどうか確認
      if (isset($quiz['answers'][$current])) {
        redirect('index.php?page=result');
      }
      $correct = (int)$question['correct_answer'];
      $isCorrect = ($answer === $correct);
      if ($isCorrect) {
        $quiz['correct_count']++;
      }
      $quiz['answers'][$current] = [
        'answer' => $answer,
        'is_correct' => $isCorrect
      ];
      redirect('index.php?page=result');
    }
    unset($quiz);
  }
  /* ---------- 次の問題 ---------- */
  elseif ($action === 'next') {
    if (!isset($_SESSION['quiz'])) {
      redirect('index.php?page=quiz_start');
    }
    $quiz = &$_SESSION['quiz'];
    $current = $quiz['current'];
    if (!isset($quiz['answers'][$current])) {
      redirect('index.php?page=quiz_question');
    }
    $quiz['current']++;
    if ($quiz['current'] >= count($quiz['questions'])) {
      unset($quiz);
      redirect('index.php?page=complete');
    }
    unset($quiz);
    redirect('index.php?page=quiz_question');
  }
  /* ---------- クイズ終了 ---------- */
  elseif ($action === 'finish') {
    unset($_SESSION['quiz']);
    redirect('index.php?page=list');
  }
}
/* =====================================
   画面表示用データ取得
===================================== */
// カテゴリー一覧
$categories = getCategories($pdo);
// 問題一覧
if ($page === 'list') {
  $questions = getQuestions($pdo);
}
// 編集対象の取得
if ($page === 'edit' && $_SERVER['REQUEST_METHOD'] === 'GET') {
  $editId = filter_var(
    $_GET['id'] ?? null,
    FILTER_VALIDATE_INT
  );
  if ($editId === false || $editId === null || $editId < 1) {
    redirect('index.php?page=list');
  }
  $formData = getQuestion($pdo, $editId);
  if ($formData === null) {
    $_SESSION['message'] = '問題が存在しません。';
    redirect('index.php?page=list');
  }
}
// クイズ出題
if ($page === 'quiz_question') {
  if (!isset($_SESSION['quiz'])) {
    redirect('index.php?page=quiz_start');
  }
  $quiz = $_SESSION['quiz'];
  $current = $quiz['current'];
  $question = $quiz['questions'][$current] ?? null;
  if ($question === null) {
    redirect('index.php?page=complete');
  }
  if (isset($quiz['answers'][$current])) {
    redirect('index.php?page=result');
  }
}
// 回答結果
if ($page === 'result') {
  if (!isset($_SESSION['quiz'])) {
    redirect('index.php?page=quiz_start');
  }
  $quiz = $_SESSION['quiz'];
  $current = $quiz['current'];
  $question = $quiz['questions'][$current] ?? null;
  $answerData = $quiz['answers'][$current] ?? null;
  if ($question === null || $answerData === null) {
    redirect('index.php?page=quiz_question');
  }
}
// クイズ終了
if ($page === 'complete') {
  if (!isset($_SESSION['quiz'])) {
    redirect('index.php?page=list');
  }
  $quiz = $_SESSION['quiz'];
  $total = count($quiz['questions']);
  $correctCount = $quiz['correct_count'];
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>4択クイズ学習システム</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <h1>4択クイズ学習システム</h1>
  <nav>
    <a href="index.php?page=list">問題一覧</a>
    <a href="index.php?page=register">問題登録</a>
    <a href="index.php?page=quiz_start">クイズ開始</a>
  </nav>
</header>
<main>
<?php if ($message !== ''): ?>
  <p class="message"><?= h($message) ?></p>
<?php endif; ?>
<?php if (!empty($errors)): ?>
  <div class="errors">
    <ul>
      <?php foreach ($errors as $error): ?>
        <li><?= h($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
<?php if ($page === 'list'): ?>
  <!-- 問題一覧 -->
  <h2>問題一覧</h2>
  <p>
    <a class="button" href="index.php?page=register">
        新しい問題を登録
    </a>
  </p>
  <?php if (empty($questions)): ?>
    <p>登録されている問題はありません。</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>カテゴリー</th>
          <th>問題文</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($questions as $q): ?>
          <tr>
            <td><?= h($q['id']) ?></td>
            <td><?= h($q['category_name']) ?></td>
            <td><?= h($q['question']) ?></td>
            <td class="actions">
              <a href="index.php?page=edit&id=<?= h($q['id']) ?>">
                  編集
              </a>
              <form method="post"
                    onsubmit="return confirm('削除しますか？');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id"
                        value="<?= h($q['id']) ?>">
                <button type="submit" class="danger">
                    削除
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
<?php elseif ($page === 'register' || $page === 'edit'): ?>
  <!-- 問題登録・編集 -->
  <h2>
    <?= $page === 'register' ? '問題登録' : '問題編集' ?>
  </h2>
  <form method="post" class="question-form">
    <input type="hidden"
            name="action"
            value="<?= $page === 'register' ? 'create' : 'update' ?>">
    <?php if ($page === 'edit'): ?>
      <input type="hidden" name="id" value="<?= h($editId) ?>">
    <?php endif; ?>
    <label>カテゴリー</label>
    <select name="category_id" required>
      <option value="">選択してください</option>
      <?php foreach ($categories as $category): ?>
        <option value="<?= h($category['id']) ?>"
          <?= (string)($formData['category_id'] ?? '') ===
              (string)$category['id'] ? 'selected' : '' ?>>
          <?= h($category['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <label>問題文</label>
    <textarea name="question" rows="3" required><?= h(
        $formData['question'] ?? ''
    ) ?></textarea>
    <?php for ($i = 1; $i <= 4; $i++): ?>
      <label>選択肢<?= $i ?></label>
      <input type="text"
              name="choice<?= $i ?>"
              value="<?= h($formData['choice' . $i] ?? '') ?>"
              maxlength="200"
              required>
    <?php endfor; ?>
    <label>正解</label>
    <select name="correct_answer" required>
      <option value="">選択してください</option>
      <?php for ($i = 1; $i <= 4; $i++): ?>
        <option value="<?= $i ?>"
          <?= (string)($formData['correct_answer'] ?? '') ===
              (string)$i ? 'selected' : '' ?>>
          選択肢<?= $i ?>
        </option>
      <?php endfor; ?>
    </select>
    <label>解説</label>
    <textarea name="explanation"
              rows="5"
              maxlength="2000"
              required><?= h($formData['explanation'] ?? '') ?></textarea>
    <button type="submit">
      <?= $page === 'register' ? '登録する' : '更新する' ?>
    </button>
    <a href="index.php?page=list">戻る</a>
  </form>
<?php elseif ($page === 'quiz_start'): ?>
  <!-- クイズ開始 -->
  <h2>クイズ開始</h2>
  <p>出題する問題数を指定してください。</p>
  <form method="post" class="question-form">
    <input type="hidden" name="action" value="quiz_start">
    <label>出題数</label>
    <input type="number"
            name="question_count"
            min="1"
            max="50"
            value="5"
            required>
    <button type="submit">クイズを開始する</button>
  </form>
<?php elseif ($page === 'quiz_question'): ?>
  <!-- クイズ出題 -->
  <h2>クイズ</h2>
  <p>
    第<?= $current + 1 ?>問 /
    全<?= count($quiz['questions']) ?>問
  </p>
  <h3><?= h($question['question']) ?></h3>
  <form method="post" class="question-form">
    <input type="hidden" name="action" value="answer">
    <?php for ($i = 1; $i <= 4; $i++): ?>
      <label class="choice">
        <input type="radio"
                name="answer"
                value="<?= $i ?>"
                required>
        <?= h($question['choice' . $i]) ?>
      </label>
    <?php endfor; ?>
    <button type="submit">回答する</button>
  </form>
<?php elseif ($page === 'result'): ?>
  <!-- 回答結果 -->
  <h2>回答結果</h2>
  <h3><?= h($question['question']) ?></h3>
  <?php if ($answerData['is_correct']): ?>
    <p class="correct">正解です！</p>
  <?php else: ?>
    <p class="incorrect">不正解です。</p>
  <?php endif; ?>
  <p>
    あなたの回答：
    <?= h($question['choice' . $answerData['answer']]) ?>
  </p>
  <p>
    正解：
    <?= h($question['choice' . $question['correct_answer']]) ?>
  </p>
  <h3>解説</h3>
  <p><?= nl2br(h($question['explanation'])) ?></p>
  <p>
    現在の正解数：
    <?= $quiz['correct_count'] ?> /
    <?= count($quiz['questions']) ?>
  </p>
  <form method="post">
    <input type="hidden" name="action" value="next">
    <button type="submit">
      <?= $current + 1 < count($quiz['questions'])
        ? '次の問題へ'
        : '結果を見る' ?>
    </button>
  </form>
<?php elseif ($page === 'complete'): ?>
  <!-- クイズ終了 -->
  <h2>クイズ終了</h2>
  <p>お疲れさまでした。</p>
  <h3>結果</h3>
  <p>
    出題数：<?= $total ?> 問
  </p>
  <p>
    正解数：<?= $correctCount ?> 問
  </p>
  <p>
    正答率：
    <?= round($correctCount / $total * 100, 1) ?> %
  </p>
  <form method="post">
    <input type="hidden" name="action" value="finish">
    <button type="submit">終了する</button>
  </form>
  <p>
    <a href="index.php?page=quiz_start">もう一度クイズをする</a>
  </p>
<?php else: ?>
  <h2>ページが見つかりません。</h2>
  <a href="index.php?page=list">問題一覧へ</a>
<?php endif; ?>
</main>
<footer>
  <p>4択クイズ学習システム</p>
</footer>
</body>
</html>
