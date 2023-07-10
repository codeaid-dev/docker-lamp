<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>アンケート</title>
</head>
<body>
  <h1>アンケート</h1>
  <form action="save.php" method="POST">
    <table>
      <tr>
      <td><label>名前：</td><td><input type="text" name="name" required></label></td>
      </tr><tr>
      <td><label>メールアドレス：</td><td><input type="email" name="email" required></label></td>
      </tr><tr>
      <td><label>年齢：</td><td><input type="number" name="age" min="18" max="110" required></label></td>
      </tr>
    </table>
    <p>
    <fieldset style="width:500px;">
      <legend>興味のあるプログラム言語：</legend>
      <label><input type="checkbox" name="program[]" value="PHP">PHP</label>
      <label><input type="checkbox" name="program[]" value="JavaScript">JavaScript</label>
      <label><input type="checkbox" name="program[]" value="Python">Python</label>
      <label><input type="checkbox" name="program[]" value="Java">Java</label>
      <label><input type="checkbox" name="program[]" value="C/C++">C/C++</label>
      <label><input type="checkbox" name="program[]" value="C#">C#</label>
      <label><input type="checkbox" name="program[]" value="Ruby">Ruby</label>
    </fieldset>
    </p><p>
    <fieldset style="width:500px;">
      <legend>学習に使っているパソコン：</legend>
      <label><input type="radio" name="pc" value="desktop" checked>デスクトップPC</label>
      <label><input type="radio" name="pc" value="note">ノートPC</label>
    </fieldset>
    </p><p>
    <label for="maker">パソコンメーカー：</label>
    <select name="maker" id="maker">
      <option value="">選択してください。</option>
      <option value="lenovo">Lenovo</option>
      <option value="dell">DELL</option>
      <option value="hp">HP</option>
      <option value="apple">Apple</option>
      <option value="dynabook">Dynabook</option>
      <option value="nec">NEC</option>
      <option value="vaio">VAIO</option>
      <option value="asus">ASUS</option>
      <option value="self">自作</option>
      <option value="other">その他</option>
    </select>
    </p>
    <p><button type="submit">送信</button></p>
  </form>
</body>
</html>