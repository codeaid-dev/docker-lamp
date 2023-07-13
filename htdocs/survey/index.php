<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>アンケート</title>
</head>
<body>
  <h1>アンケート</h1>
  <table>
    <?php if (isset($errors) && $errors) { ?>
      <tr>
        <td>以下のエラーを修正してください：</td>
      </tr><tr>
        <td style="color:red;"><ul>
          <?php foreach ($errors as $error) { ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php } ?>
        </ul></td>
      </tr>
    <?php } ?>
  </table>
  <form action="save.php" method="POST">
    <table>
      <tr>
      <td><label>名前：</td><td><input type="text" name="name" value="<?= $input['name'] ?? '' ?>" required></label></td>
      </tr><tr>
      <td><label>メールアドレス：</td><td><input type="email" name="email" value="<?= $input['email'] ?? '' ?>" required></label></td>
      </tr><tr>
      <td><label>年齢：</td><td><input type="number" name="age" min="18" max="110" value="<?= $input['age'] ?? '' ?>" required></label></td>
      </tr>
    </table>
    <p>
    <fieldset style="width:500px;">
      <legend>興味のあるプログラム言語：</legend>
      <label><input type="checkbox" name="program[]" value="PHP" <?= isset($input['program'])&&in_array("PHP",$input['program'])?"checked":"" ?>>PHP</label>
      <label><input type="checkbox" name="program[]" value="JavaScript" <?= isset($input['program'])&&in_array("JavaScript",$input['program'])?"checked":"" ?>>JavaScript</label>
      <label><input type="checkbox" name="program[]" value="Python" <?= isset($input['program'])&&in_array("Python",$input['program'])?"checked":"" ?>>Python</label>
      <label><input type="checkbox" name="program[]" value="Java" <?= isset($input['program'])&&in_array("Java",$input['program'])?"checked":"" ?>>Java</label>
      <label><input type="checkbox" name="program[]" value="C/C++" <?= isset($input['program'])&&in_array("C/C++",$input['program'])?"checked":"" ?>>C/C++</label>
      <label><input type="checkbox" name="program[]" value="C#" <?= isset($input['program'])&&in_array("C#",$input['program'])?"checked":"" ?>>C#</label>
      <label><input type="checkbox" name="program[]" value="Ruby" <?= isset($input['program'])&&in_array("Ruby",$input['program'])?"checked":"" ?>>Ruby</label>
    </fieldset>
    </p><p>
    <fieldset style="width:500px;">
      <legend>学習に使っているパソコン：</legend>
      <?php if (isset($input['pc'])&&$input['pc']=="ノートPC") { ?>
        <label><input type="radio" name="pc" value="デスクトップPC">デスクトップPC</label>
        <label><input type="radio" name="pc" value="ノートPC" checked>ノートPC</label>
      <?php } else { ?>
        <label><input type="radio" name="pc" value="デスクトップPC" checked>デスクトップPC</label>
        <label><input type="radio" name="pc" value="ノートPC">ノートPC</label>
      <?php } ?>
    </fieldset>
    </p><p>
    <label for="maker">パソコンメーカー：</label>
    <select name="maker" id="maker">
      <option value="other">選択してください。</option>
      <option value="lenovo" <?= isset($input['maker'])&&$input['maker']=="lenove"?"selected":"" ?>>Lenovo</option>
      <option value="dell" <?= isset($input['maker'])&&$input['maker']=="dell"?"selected":"" ?>>DELL</option>
      <option value="hp" <?= isset($input['maker'])&&$input['maker']=="hp"?"selected":"" ?>>HP</option>
      <option value="apple" <?= isset($input['maker'])&&$input['maker']=="apple"?"selected":"" ?>>Apple</option>
      <option value="dynabook" <?= isset($input['maker'])&&$input['maker']=="dynabook"?"selected":"" ?>>Dynabook</option>
      <option value="nec" <?= isset($input['maker'])&&$input['maker']=="nec"?"selected":"" ?>>NEC</option>
      <option value="vaio" <?= isset($input['maker'])&&$input['maker']=="vaio"?"selected":"" ?>>VAIO</option>
      <option value="asus" <?= isset($input['maker'])&&$input['maker']=="asus"?"selected":"" ?>>ASUS</option>
      <option value="self" <?= isset($input['maker'])&&$input['maker']=="self"?"selected":"" ?>>自作</option>
      <option value="other" <?= isset($input['maker'])&&$input['maker']=="other"?"selected":"" ?>>その他</option>
    </select>
    </p><p>
      <label for="comments">コメント：</label><br>
      <textarea name="comments" id="comments" rows="5" cols="40"><?= $input['comments']??"" ?></textarea>
    </p>
    <p><button type="submit">送信</button></p>
  </form>
</body>
</html>