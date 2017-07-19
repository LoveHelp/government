<?php
include_once "../mysql.php";
header("Content-type:text/html;charset=utf-8");
$id = $_POST['id'];
$value = $_POST['value'];
$mode = $_POST['mode'];
$createtime = date('Y-m-d H:i:s ');

$mLink = new mysql;

if($mode == "title"){
	$sql = "update p_task set title = '" . $value . "', createtime = '". $createtime . "' where id=" . $id;
}else{
	$sql = "update p_progress set stage = '" . $value . "', createtime = '" . $createtime . "' where id = " . $id;
}

$res = $mLink->update($sql);

if($res){
	echo $value;
}


