<?php
$id = $_POST['id'] ?? 0;
$answer = $_POST['answer'] ?? '';
//$dsn = 'mysql:host=localhost;dbname=quiz;charset=utf8'; // XAMPP/MAMP/VMの場合
$dsn = 'mysql:host=mysql;dbname=quiz;charset=utf8'; // Dockerの場合
//$dsn = 'sqlite:./quiz.db'; // SQLiteの場合
$user = 'root';
$password = 'password';
try {
  $db = new PDO($dsn, $user, $password);
  //$db = new PDO($dsn); //SQLiteの場合
  $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // 静的プレースホルダーを指定
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
      $result = "不正解です(正解：{$ans})";
    }
  } else { //ページが表示されたときはランダムに出題する
    $sql = $db->query("SELECT * FROM questions");
    $q = array();
    foreach ($sql as $row) {
      $tmp=array();
      $tmp['id'] = $row['id'];
      $tmp['question'] = $row['question'];
      $tmp['answer'] = $row['answer'];
      $q[] = $tmp;
    }
    $key = array_rand($q, 1);
    $id = $q[$key]['id'];
    $question = $q[$key]['question'];
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
  <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
    <input type="hidden" name="id" value="<?= $id ?>">
    <p>問題：<?= htmlspecialchars($question) ?></p>
    <p><label>答え：<input type="text" name="answer" value="<?= htmlspecialchars($answer) ?>" required></label></p>
    <button type="submit">解答</button>
  </form>
  <h2>結果表示</h2>
  <?php if (isset($result)) : ?>
    <p><?= $result ?></p>
  <?php endif; ?>
  <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="GET">
    <button type="submit">次の問題</button>
  </form>
  <p><span style="margin-right: 30px"><a href="index.php">トップ</a></span><span style="margin-right: 30px"><a href="edit.php">編集</a></span><a href="save.php">新規作成</a></p>
</body>
</html>