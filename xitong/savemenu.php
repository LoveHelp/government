<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
header("Content-type:text/html;charset=utf-8");
//添加或修改菜单	
include_once "../mysql.php";
changemenu();
function changemenu(){
$menuid=trim($_POST["menuid"]);
$menuname=trim($_POST["menuname"]);
$menuico=trim($_POST["menuico"]);
$menuurl=trim($_POST["menuurl"]);
$menuclass=trim($_POST["menuclass"]);
$mLink=new mysql;
$param=array($menuname,$menuico,$menuurl,$menuclass);
if($menuid>-1){
	$sqlstr="update menu set name=?,menuico=?,menuurl=?,menuclass=? where menuid=$menuid";
	//echo $sqlstr.$param."修改";
	$result=$mLink->update($sqlstr,$param);	
	if($result>0){echo 1;}else{echo 0;}
	}else{
		
		$sqlstr="insert into menu (name,menuico,menuurl,menuclass) values(?,?,?,?)";	
		$result=$mLink->insert($sqlstr,$param);
		if($result>0){echo 1;}else{echo 0;}
	}
}