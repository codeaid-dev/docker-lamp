<?php
session_start();
require_once '../config.php';

if(!isset($_SESSION['username'])){
  header('Location: login.php');
  exit;
}

$sql = "SELECT * FROM answers";
$stmt = $pdo->query($sql);
$answers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['download']) && !empty($answers)) {
  $filepath = 'survey.csv';
  $fp = fopen($filepath, 'w');
  $csvstr = array('回答日時','名前','メールアドレス','年齢','興味のあるプログラミング言語','学習に使っているパソコン','パソコンメーカー','コメント');
  fputcsv($fp, $csvstr);
  foreach ($answers as $answer) {
    $csvstr = array($answer['uptime'],$answer['name'],$answer['email'],$answer['age'],$answer['program'],$answer['pc'],$answer['maker']);
    if (strpos($answer['comments'], '\n')) {
      $csvstr[] = "\"".$answer['comments']."\"";
    } else {
      $csvstr[] = $answer['comments'];
    }
    fputcsv($fp, $answer);
  }
  fclose($fp);

  //HTTPヘッダを設定し出力する
  header('Content-Type: application/octet-stream');
  header('Content-Length: '.filesize($filepath));
  header('Content-Disposition: attachment; filename=download.csv');
  readfile($filepath);
  exit;
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>アンケート</title>
</head>
<body>
  <h2>回答一覧</h2>
  <table border="1">
    <tr>
      <th>回答日時</th>
      <th>名前</th>
      <th>メールアドレス</th>
      <th>年齢</th>
      <th>興味のあるプログラム言語</th>
      <th>学習に使っているパソコン</th>
      <th>パソコンメーカー</th>
      <th>コメント</th>
    </tr>
    <?php foreach($answers as $answer): ?>
    <tr>
      <td><?= htmlspecialchars($answer['uptime']) ?></td>
      <td><?= htmlspecialchars($answer['name']) ?></td>
      <td><?= htmlspecialchars($answer['email']) ?></td>
      <td><?= htmlspecialchars($answer['age']) ?></td>
      <td><?= htmlspecialchars($answer['program']) ?></td>
      <td><?= htmlspecialchars($answer['pc']) ?></td>
      <td><?= htmlspecialchars($answer['maker']) ?></td>
      <td><?= nl2br(htmlspecialchars($answer['comments'])) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <form method="POST">
    <p><button type="submit" name="download">ダウンロード</button></p>
  </form>
  <p><a href="logout.php">ログアウト</a></p>
</body>
</html>