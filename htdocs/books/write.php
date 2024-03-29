<?php
require_once 'config.php';

$isbn = "";
$name = "";
$price = "";
$page = "";
$date = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  list($errors, $input) = validate_form();
  if ($errors) {
    print '<ul><li>';
    print implode('</li><li>', $errors);
    print '</li></ul>';
  } else {
    try {
      $res = $db->query("SELECT * FROM books WHERE isbn='$isbn'");
      if (count($res->fetchAll()) != 0) {
        print '<p>入力したISBNはすでに保存されています。</p>';
      } else {
        $stmt = $db->prepare("INSERT INTO books (isbn, name, price, page, date) VALUES (:isbn, :name, :price, :page, :date)");
        $stmt->bindParam(':isbn', $isbn, PDO::PARAM_STR);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_INT);
        $stmt->bindParam(':page', $page, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->execute();
        print '<p>保存できました。</p>';
      }
    } catch (PDOException $e) {
      die ('エラー：'.$e->getMessage());
    }
  }
}

function validate_form() {
  $input['isbn'] = filter_input(INPUT_POST, 'isbn', FILTER_VALIDATE_INT);
  $input['price'] = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_INT);
  $input['page'] = filter_input(INPUT_POST, 'page', FILTER_VALIDATE_INT);
  $errors = array();
  $GLOBALS['isbn'] = $_POST['isbn'];
  if (empty($input['isbn'])) {
    $errors[] = 'ISBNは数字で入力してください。';
  }
  $GLOBALS['name'] = $_POST['name'];
  $GLOBALS['price'] = $_POST['price'];
  if (empty($input['price'])) {
    $errors[] = '価格は数字を入力してください。';
  }
  $GLOBALS['page'] = $_POST['page'];
  if (empty($input['page'])) {
    $errors[] = 'ページ数は数字を入力してください。';
  }
  $GLOBALS['date'] = $_POST['date'];

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
    <p><label>ISBN：<input type="text" name="isbn" value="<?= $isbn ?>" required></label></p>
    <p><label>書籍名：<input type="text" name="name" value="<?= $name ?>" required></label></p>
    <p><label>価格：<input type="text" name="price" value="<?= $price ?>" required></label></p>
    <p><label>ページ数：<input type="text" name="page" value="<?= $page ?>" required></label></p>
    <p><label>発売日：<input type="date" name="date" value="<?= $date ?>" required></label></p>
    <button type="submit">保存</button>
  </form>
  <p><span style="margin-right: 30px"><a href="/books/">トップ</a></span><a href="read.php">閲覧ページ</a></p>
</body>
</html>