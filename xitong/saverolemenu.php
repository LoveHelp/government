<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
header("Content-type:text/html;charset=utf-8");
//添加或修改菜单	
include_once "../mysql.php";
changerole();
function changerole(){
$roleid=trim($_POST["roleid"]);
$role=trim($_POST["role"]);
$savemenuidlist=trim($_POST["savemenuidlist"]);
$action=trim($_POST["action"]);
$mLink=new mysql;+
$result=0;
if($action>0){$sqlstr="delete from rolemenu where roleid=$roleid";	$mLink->query($sqlstr);	}
	$arr = explode(',',$savemenuidlist);
	foreach($arr as $menuid)
	{
	$param=array($roleid,$role,$menuid);	
	$sqlstr="insert into rolemenu (roleid,role,menuid) values(?,?,?)";	
	$result=$mLink->insert($sqlstr,$param);
	}	
	if($result>0){echo 1;}else{echo 0;}
}