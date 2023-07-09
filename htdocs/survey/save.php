<?php
$pc_makers = array('lenovo' => 'Lenovo',
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
    show_form($errors);
  } else {
    // サブミットされたデータが有効なら処理する
    process_form($input);
  }

  //$dsn = 'mysql:host=localhost;dbname=bookstore;charset=utf8'; // XAMPP/MAMP/VMの場合
  $dsn = 'mysql:host=mysql;dbname=bookstrage;charset=utf8'; // Dockerの場合
  //$dsn = 'sqlite:./bookstore.db'; // SQLiteの場合
  $user = 'root';
  $password = 'password';

  try {
    $db = new PDO($dsn, $user, $password);
    //$db = new PDO($dsn); //SQLiteの場合
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // 静的プレースホルダーを指定
    if (isset($delete)) {
      $stmt = $db->prepare("DELETE FROM books WHERE isbn=:isbn");
      $stmt->execute([':isbn'=>$delete]);
    } else {
      $stmt = $db->prepare("SELECT * FROM books WHERE isbn=:isbn OR name LIKE :name");
      $stmt->bindParam(':isbn', $keyword, PDO::PARAM_STR);
      $name = "%".$keyword."%";
      $stmt->bindParam(':name', $name, PDO::PARAM_STR);
      $stmt->execute();
    }
  } catch (PDOException $e) {
    die ('エラー：'.$e->getMessage());
  }
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
  }

  // 18才以上の年齢が入力されているか確認
  $input['age'] = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT,
                              array('options' => array('min_range'=>18, 'max_range'=>110)));
  if (is_null($input['age']) || ($input['age'] === false)) {
    $errors[] = '18才以上110以下の年齢を入力してください。';
  }

  // プログラミング言語が選択されているか確認
  if (isset($_POST['program']) && is_array($_POST['program'])) {
    $input['program'] = implode("|", $_POST['program']);
  } else {
    $input['program'] = "";
  }

  // 学習に使われているパソコンが選択されているか確認
  if (isset($_POST['program']) && is_array($_POST['program'])) {
    if ($_POST['pc'] == 'desktop') {
      $input['pc'] = "デスクトップPC";
    } else {
      $input['pc'] = "ノートPC";
    }
  } else {
    $input['pc'] = "";
  }

  // パソコンメーカーが選択されているか確認
  if (!array_key_exists($_POST['pc_maker'], $GLOBALS['pc_makers'])) {
    $errors[] = 'パソコンメーカーを選択してください。';
  } else {
    $input['pc_maker'] = $GLOBALS['pc_makers'][$_POST['pc_maker']];
  }

  return array($errors, $input);
}

?>