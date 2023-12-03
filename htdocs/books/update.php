<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['name'];
  $price = $_POST['price'];
  $page = $_POST['page'];
  $date = $_POST['date'];
  if (in_array('update',array_keys($_POST))) {
    $update = $_POST['update'];
  } else {
    list($errors, $input) = validate_form();
    if ($errors) {
      print '<ul><li>';
      print implode('</li><li>', $errors);
      print '</li></ul>';
      $update = $_POST['isbn'];
    } else {
      $isbn = $_POST['isbn'];
      $name = $_POST['name'];
      $price = $input['price'];
      $page = $input['page'];
      $date = $_POST['date'];
      try {
        $stmt = $db->prepare("UPDATE books SET name=:name, price=:price, page=:page, date=:date WHERE isbn='$isbn'");
  //      $stmt->bindParam(':isbn', $isbn, PDO::PARAM_STR);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_INT);
        $stmt->bindParam(':page', $page, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->execute();
      } catch (PDOException $e) {
        die ('エラー：'.$e->getMessage());
      }
    }
  }
}

function validate_form() {
  $input['price'] = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_INT);
  $input['page'] = filter_input(INPUT_POST, 'page', FILTER_VALIDATE_INT);
  $errors = array();
//  if (empty($_POST['name'])) {
//    $errors[] = '書籍名を入力してください。';
//  }
  if (empty($input['price'])) {
    $errors[] = '価格は数字を入力してください。';
  }
  if (empty($input['page'])) {
    $errors[] = 'ページ数は数字を入力してください。';
  }
//  if (empty($_POST['date'])) {
//    $errors[] = '発売日を入力してください。';
//  }

  return array($errors, $input);
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>書籍データ庫</title>
</head>
<body>
  <h1>書籍データ庫</h1>
  <form method="POST">
    <?php if (isset($update)): ?>
      <p>ISBN：<?= htmlspecialchars($update) ?></p>
      <p><input type="hidden" name="isbn" value=<?= htmlspecialchars($update) ?>></label></p>
    <?php else: ?>
      <p>ISBN：<?= htmlspecialchars($isbn) ?>（修正できました）</p>
      <p><input type="hidden" name="isbn" value=<?= htmlspecialchars($isbn) ?>></label></p>
    <?php endif; ?>
    <p><label>書籍名：<input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required></label></p>
    <p><label>価格：<input type="text" name="price" value="<?= htmlspecialchars($price) ?>" required></label></p>
    <p><label>ページ数：<input type="text" name="page" value="<?= htmlspecialchars($page) ?>" required></label></p>
    <p><label>発売日：<input type="date" name="date" value="<?= htmlspecialchars($date) ?>" required></label></p>
    <button type="submit">修正</button>
  </form>
  <p><span style="margin-right: 30px"><a href="/books/">トップ</a></span><a href="read.php">閲覧ページ</a></p>
</body>
</html>