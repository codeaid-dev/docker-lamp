<?php
  require_once 'config.php';

  $id = $_POST['id'] ?? 0;
  $answer = $_POST['answer'] ?? '';
  try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') { //解答ボタンによるPOST送信は正解を判定する
      $stmt = $db->prepare("SELECT * FROM questions WHERE id=:id");
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      while ($row = $stmt->fetch()) {
        $question = $row['question'];
        $ans = $row['answer'];
      }
      if ($ans == $answer) {
        $result = '正解です';
      } else {
        $result = "不正解です(正解：".htmlspecialchars($ans).")";
      }
    } else { //ページが表示されたときはランダムに出題する
      $sql = $db->query("SELECT * FROM questions");
      $questions = array();
      foreach ($sql as $row) {
        $tmp=array();
        $tmp['id'] = $row['id'];
        $tmp['question'] = $row['question'];
        $tmp['answer'] = $row['answer'];
        $questions[] = $tmp;
      }
      if (!empty($questions)) {
        $key = array_rand($questions, 1);
        $id = $questions[$key]['id'];
        $question = $questions[$key]['question'];
      }
    }
  } catch (PDOException $e) {
    die ('エラー：'.$e->getMessage());
  }  
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>クイズ</title>
</head>
<body>
  <h1>クイズ出題</h1>
  <?php if (!empty($questions) || !empty($question)) : ?>
    <form method="POST">
      <input type="hidden" name="id" value="<?= $id ?>">
      <p>問題：<?= htmlspecialchars($question) ?></p>
      <p><label>答え：<input type="text" name="answer" value="<?= htmlspecialchars($answer) ?>" required></label></p>
      <button type="submit">解答</button>
    </form>
    <h2>結果表示</h2>
    <?php if (isset($result)) : ?>
      <p><?= $result ?></p>
    <?php endif; ?>
    <form method="GET">
      <button type="submit">次の問題</button>
    </form>
  <?php else : ?>
    <p>問題がありません。</p>
  <?php endif; ?>
  <p><span style="margin-right: 30px"><a href="index.php">トップ</a></span><span style="margin-right: 30px"><a href="edit.php">編集</a></span><a href="save.php">新規作成</a></p>
</body>
</html>