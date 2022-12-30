<?php
if ('POST' == $_SERVER['REQUEST_METHOD']) {
  print "こんにちは、". $_POST['my_name'];
} else {
  print<<<_HTML_
  <form method="post" action="$_SERVER[PHP_SELF]">
  名前: <input type"text" name="my_name">
  <br>
  <button type="submit">表示</button>
  </form>
  _HTML_;
}
?>