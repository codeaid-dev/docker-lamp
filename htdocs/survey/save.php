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
  // validate_form()がエラーを返したらエラーを表示する
  list($errors, $input) = validate_form();
  if ($errors) {
    include 'index.php';
  } else {
    // サブミットされたデータが有効なら処理する
    //$host = 'localhost'; //XAMPP,MAMP,VM
    $host = 'mysql'; //Docker
    $dbname = 'survey';
    $user = 'root';
    $password = 'password';
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    try {
      $pdo = new PDO($dsn, $user, $password);
      //$pdo = new PDO($dsn); //SQLiteの場合
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // 静的プレースホルダーを指定
      $stmt = $pdo->prepare("INSERT INTO answers (name,email,age,program,pc,maker,comments) VALUES (?,?,?,?,?,?,?)");
      $stmt->bindParam(1, $input['name'], PDO::PARAM_STR);
      $stmt->bindParam(2, $input['email'], PDO::PARAM_STR);
      $stmt->bindParam(3, $input['age'], PDO::PARAM_STR);
      $program = implode("|",$input['program']);
      $stmt->bindParam(4, $program, PDO::PARAM_STR);
      $stmt->bindParam(5, $input['pc'], PDO::PARAM_STR);
      $stmt->bindParam(6, $GLOBALS['makers'][$input['maker']], PDO::PARAM_STR);
      $stmt->bindParam(7, $input['comments'], PDO::PARAM_STR);
      $stmt->execute();
      print '<h2>ご回答ありがとうございました。</h2>';
      $display=<<<_SURVEY_
      名前： {$input['name']}
      メールアドレス：{$input['email']}
      年齢：{$input['age']}
      興味のあるプログラミング言語：{$program}
      学習に使われるパソコン：{$input['pc']}
      パソコンメーカー：{$GLOBALS['makers'][$input['maker']]}
      コメント：\n
      _SURVEY_;
      if (strlen(trim($input['comments']))) {
        $display .= $input['comments'];
      }
      // HTMLエスケープし、改行(\n)を<br>タグに変える
      print nl2br(htmlspecialchars($display, ENT_HTML5));
      print '<p><a href="index.php">トップ</a></p>';
  } catch (PDOException $e) {
      die ('エラー：'.$e->getMessage());
    }
  }
} else {
  include 'index.php';
}

function existUser($email) {
  //$host = 'localhost'; //XAMPP,MAMP,VM
  $host = 'mysql'; //Docker
  $dbname = 'survey';
  $dbuser = 'root';
  $dbpass = 'password';
  $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
  try {
    $pdo = new PDO($dsn, $dbuser, $dbpass); //MySQL
    //$pdo = new PDO('sqlite:./survey.db'); //SQLite
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $stmt = $pdo->prepare("SELECT * FROM answers WHERE email=?");
    $stmt->bindParam(1, $email, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->fetch()) {
      return true;
    }
    return false;
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
    $input['email'] = $_POST['email']??'';
  } else {
    if (existUser($input['email'])) {
      $errors[] = 'すでにこのメールアドレスで回答済みです。';
    }
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