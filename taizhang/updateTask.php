<?php
include_once "../mysql.php";
header("Content-type:text/html;charset=utf-8");
$id = $_POST['id'];
$name = $_POST['name'];
$value = $_POST['value'];
$modifier = $_POST['modifier'];
$modtime = $_POST['modtime'];

$mLink = new mysql;
if(isset($_POST['type'])){
	$sql = "update progress set " . $name . "= '" . $value . "', modifier = '" . $modifier . "', modtime = '". $modtime . "' where id=" . $id;
}else{
	$sql = "update task set " . $name . "= '" . $value . "', modifier = '" . $modifier . "', modtime = '". $modtime . "' where id=" . $id;
}
$res = $mLink->update($sql);

if($res){
	echo $value;
}


