<?php
$nums = array(60, 50, 90, 70, 80);
$max = 0;
for ($i = 0; $i < 5; $i++) {
    if ($nums[$i] > $max) {
        $max = $nums[$i];
    }
}
echo $max;
?>
