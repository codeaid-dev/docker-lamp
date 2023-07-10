<?php
$makers = array('lenovo' => 'Lenovo',
'dell' => 'DELL',
'hp' => 'HP',
'apple' => 'Apple',
'dynabook' => 'Dynabook',
'nec' => 'NEC',
'vaio' => 'VAIO',
'asus' => 'ASUS',
'acer' => 'Acer',
'self' => '自作PC',
'other' => 'その他');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // validate_form()がエラーを返したら、エラーをshow_form()に渡す
  list($errors, $input) = validate_form();
  if ($errors) {
    include 'index.php';
  } else {
    // サブミットされたデータが有効なら処理する
    //$dsn = 'mysql:host=localhost;dbname=bookstore;charset=utf8'; // XAMPP/MAMP/VMの場合
    $dsn = 'mysql:host=mysql;dbname=bookstrage;charset=utf8'; // Dockerの場合
    //$dsn = 'sqlite:./bookstore.db'; // SQLiteの場合
    $user = 'root';
    $password = 'password';

    try {
      $db = new PDO($dsn, $user, $password);
      //$db = new PDO($dsn); //SQLiteの場合
      $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // 静的プレースホルダーを指定
      $stmt = $db->prepare("INSERT INTO answers (name,email,age,program,pc,maker,comments) VALUES (?,?,?,?,?,?,?)");
      $stmt->bindParam(1, $input['name'], PDO::PARAM_STR);
      $stmt->bindParam(2, $input['email'], PDO::PARAM_STR);
      $stmt->bindParam(3, $input['age'], PDO::PARAM_STR);
      $stmt->bindParam(4, implode("|",$input['program']), PDO::PARAM_STR);
      $stmt->bindParam(5, $input['pc'], PDO::PARAM_STR);
      $stmt->bindParam(6, $GLOBALS['makers'][$input['maker']], PDO::PARAM_STR);
      $stmt->bindParam(7, $input['comments'], PDO::PARAM_STR);
      $stmt->execute();
    } catch (PDOException $e) {
      die ('エラー：'.$e->getMessage());
    }
  }
} else {
  include 'index.php';
}

function validate_form() {
  $input = array();
  $errors = array();

  // 名前が入力されているか確認
  $input['name'] = trim($_POST['name'] ?? '');
  if (!strlen($input['name'])) {
    $errors[] = '名前を入力してください。';
  }

  // 正しいメールアドレスかどうか確認
  $input['email'] = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
  if (!$input['email']) {
    $errors[] = '正しいメールアドレスを入力してください。';
    $input['email'] = $_POST['email']??'';
  }

  // 18才以上の年齢が入力されているか確認
  $input['age'] = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT,
                              array('options' => array('min_range'=>18, 'max_range'=>110)));
  if (is_null($input['age']) || ($input['age'] === false)) {
    $errors[] = '18才以上110以下の年齢を入力してください。';
  }

  // プログラミング言語が選択されているか確認
  if (isset($_POST['program']) && is_array($_POST['program'])) {
    //$input['program'] = implode("|", $_POST['program']);
    $input['program'] = $_POST['program'];
  } else {
    $input['program'] = array();
  }

  // 学習に使われているパソコンが選択されているか確認
  $input['pc'] = $_POST['pc']??'デスクトップPC';

  // パソコンメーカーが選択されているか確認
  $input['maker'] = $_POST['maker']??'';

  // コメントを確認
  $input['comments'] = trim($_POST['comments'] ?? '');

  return array($errors, $input);
}

?>