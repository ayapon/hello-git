<?php
$target = 'data/data.dat';
$newtarget = 'backup/data'.date("YmdHi").'.dat';
if (!copy($target, $newtarget)) {
    echo "バックアップできませんでした。\n";
}
	echo "<meta http-equiv='refresh' content='0; url=./admin.php'>"."\n";

?>
