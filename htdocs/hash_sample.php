<?php
  $expire = 30;
  ini_set('session.gc_divisor', 1);
  ini_set('session.gc_maxlifetime', $expire);
  session_set_cookie_params($expire);
  session_start();

  function to_hash($password) {
    $str = '1234567890abcdefghijklmnopqrstuvwxyz';
    $salt = substr(str_shuffle($str), 0,16);
    $hash = hash('sha256',$password.$salt);
    return $salt.$hash;
  }

  function verify_password($password, $hash) {
    $salt = substr($hash, 0, 16);
    $digest = substr($hash, 16);
    $verify = hash('sha256',$password.$salt);
    return $verify == $digest;
  }

  if (isset($_POST['signup'])) {
    if (file_exists("hash_sample.json")) {
      $json = file_get_contents("hash_sample.json");
      $json = mb_convert_encoding($json, "UTF-8");
      $signupinfo = json_decode($json, true);
      //$signupinfo[$_POST['username']] = to_hash($_POST['password']);
      $signupinfo[$_POST['username']] = password_hash($_POST['password'],PASSWORD_DEFAULT);
    } else {
      //$signupinfo = array($_POST['username'] => to_hash($_POST['password']));
      $signupinfo = array($_POST['username'] => password_hash($_POST['password'],PASSWORD_DEFAULT));
    }
    $signupinfo = json_encode($signupinfo);
    file_put_contents("hash_sample.json",$signupinfo);
  }

  if (isset($_POST["logout"])) {
    $_SESSION = array();
    session_destroy();
    header('Location: hash_sample.php');
    exit();
  }

  if (isset($_POST['login'])) {
    if (file_exists("hash_sample.json")) {
      $json = file_get_contents("hash_sample.json");
      $json = mb_convert_encoding($json, "UTF-8");
      $signupinfo = json_decode($json, true);
      foreach ($signupinfo as $key => $value) {
        if ($key == $_POST['username']) {
          $username = $_POST['username'];
          //if (verify_password($_POST['password'], $value)) {
          if (password_verify($_POST['password'],$value)) {
            $password = $value;
          }
        }
      }
    }
    if (isset($username) && !isset($password)) {
      $error = 'パスワードが違います。';
    } else if (!isset($username) && !isset($password)) {
      $username = $_POST['username'];
      $error = 'ユーザーが登録されていません。';
    } else {
      $_SESSION['username'] = $_POST['username'];
    }
  }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>パスワード保存とログイン</title>
</head>
<body>
  <h1>パスワード保存とログイン</h1>
  <?php if (isset($username)) { ?>
    <?php if (isset($error)) { ?>
      <p><?php echo htmlspecialchars($username); ?> - <?php echo $error; ?></p>
      <?php $error = "" ?>
      <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">ログインへ</a>
    <?php } else { ?>
      <p><?php echo  htmlspecialchars($username); ?> - ログイン中です。</p>
      <form method="POST">
        <button type="submit" name="logout">ログアウト</button>
      </form>
    <?php } ?>
  <?php }  else { ?>
    <h3>ログイン</h3>
    <form method="POST">
      <label>ユーザー名：<input type="text" name="username" required></label><br>
      <label>パスワード：<input type="password" name="password" required></label><br>
      <button type="submit" name="login">ログイン</button>
    </form>
  <?php } ?>
<p>----------</p>
<h3>ユーザー登録</h3>
<form method="POST">
  <label>ユーザー名：<input type="text" name="username" required></label><br>
  <label>パスワード：<input type="password" name="password" required></label><br>
  <button type="submit" name="signup">登録</button>
</form>
</body>
</html>