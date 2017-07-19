<?php
include_once "../mysql.php";
$from = $_POST['from'];
$to = $_POST['to'];
$content = $_POST['content'];
$time = $_POST['time'];

$mLink = new mysql;
$sql = "insert into message (fromuser, touser, content, time) values (" . $from . ", " . $to . ", '" . $content . "', '" . $time . "')";
$res = $mLink->insert($sql);
if($res){
	echo "success";
}


