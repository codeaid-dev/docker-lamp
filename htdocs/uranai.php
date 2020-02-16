<?php
  $uranai = rand(1, 100);
  if ($uranai > 0 && $uranai <= 10) {
    echo "今日は最高！<br>";
  } else if ($uranai > 10 && $uranai <= 40) {
    echo "今日はそこそこです<br>";
  } else if ($uranai > 40 && $uranai <= 80) {
    echo "今日はまぁまぁ<br>";
  } else {
    echo "今日は最悪です・・<br>";
  }
?>
