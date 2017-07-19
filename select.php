<?php
include_once "mysql.php";
header("Content-type:text/html;charset=utf-8");
$mLink=new mysql;
$res=$mLink->getAll("select * from user");
echo json_encode($res);