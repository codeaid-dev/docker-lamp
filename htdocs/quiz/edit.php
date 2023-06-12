<?php
$id = $_POST['id'] ?? '';
$question = $_POST['question'] ?? '';
$answer = $_POST['answer'] ?? '';
//$dsn = 'mysql:host=localhost;dbname=quiz;charset=utf8'; // XAMPP/MAMP/VMの場合
$dsn = 'mysql:host=mysql;dbname=quiz;charset=utf8'; // Dockerの場合
//$dsn = 'sqlite:./quiz.db'; // SQLiteの場合
$user = 'root';
$password = 'password';
try {
  $db = new PDO($dsn, $user, $password);
  //$db = new PDO($dsn); //SQLiteの場合
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // 例外を出力する
  $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // 静的プレースホルダーを指定
  $sql = $db->query("SELECT * FROM questions");
  $list = array();
  foreach ($sql as $row) {
    $tmp=array();
    $tmp['id'] = $row['id'];
    $tmp['question'] = $row['question'];
    $tmp['answer'] = $row['answer'];
    $list[] = $tmp;
  }
  if ($_SERVER['REQUEST_METHOD'] == 'POST') { //編集・削除ボタンが押されたとき
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (empty($id)) {
      print "<p>番号は数字を入力してください。</p>";
    } else if (!in_array($id,array_column($list,'id'))) {
      print "<p>指定した番号はありません。</p>";
    } else if (isset($_POST['get'])) { //読込
      foreach ($list as $row) {
        if ($row['id'] == $id) {
          $question = $row['question'];
          $answer = $row['answer'];
        }
      }
    } else if (isset($_POST['edit'])) { //編集
      if (empty($_POST['question']) || empty($_POST['answer'])) {
        print "<p>問題か答えが空白です。</p>";
      } else {
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // 静的プレースホルダーを指定
        $stmt = $db->prepare("UPDATE questions SET question=:question, answer=:answer WHERE id='$id'");
        $stmt->bindParam(':question', $question, PDO::PARAM_STR);
        $stmt->bindParam(':answer', $answer, PDO::PARAM_STR);
        $stmt->execute();
        print "<p>番号{$id}の問題を修正しました。</p>";
      }
    } else if (isset($_POST['delete'])) { //削除
      $db->query("DELETE FROM questions WHERE id={$id}");
      print "<p>番号{$id}の問題を削除しました。</p>";
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
  <style>
    table {
        border-collapse: collapse;
        border: 2px solid rgb(200, 200, 200);
        letter-spacing: 1px;
        font-family: sans-serif;
        font-size: 0.8rem;
    }
    td,th {
        border: 1px solid rgb(190, 190, 190);
        padding: 5px 10px;
    }
    td {
        text-align: center;
    }
  </style>
</head>
<body>
  <h1>クイズ編集</h1>
  <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
    <p><label>番号：<input type="text" name="id" value="<?= $id ?>" required></label></p>
    <p><label>問題：<input type="text" name="question" value="<?= htmlspecialchars($question) ?>"></label></p>
    <p><label>答え：<input type="text" name="answer" value="<?= htmlspecialchars($answer) ?>"></label></p>
    <button type="submit" name="get">読込(番号)</button>
    <button type="submit" name="edit">修正</button>
    <button type="submit" name="delete">削除</button>
  </form>
  <hr>
  <p>クイズ一覧</p>
  <?php if (count($list)>0) { ?>
    <table>
      <thead>
        <tr>
          <th>番号</th>
          <th>問題</th>
          <th>答え</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($list as $q) {
          print '<tr><td>' . $q['id'] . '</td>';
          print '<td>' . $q['question'] . '</td>';
          print '<td>' . $q['answer'] . '</td></tr>';
        } ?>
      </tbody>
    </table>
 <?php } ?>
  <hr>
  <p><span style="margin-right: 30px"><a href="index.php">トップ</a></span><span style="margin-right: 30px"><a href="save.php">新規作成</a></span><a href="quiz.php">出題</a></p>
</body>
</html>