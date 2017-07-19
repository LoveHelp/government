<!Doctype html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<link rel="stylesheet" type="text/css" href="../css/xtstyle.css">
<script src="../js/jquery.min.js"></script>
<script src="../js/layer/layer.js"></script>
<title>菜单管理</title>
</head>
<?php 
/*
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}*/
echo "数据导入中，请耐心等待...<br/>";
 //配置信息
 $cfg_dbhost = 'localhost';
 $cfg_dbname = 'government';
 $cfg_dbuser = 'root';
 $cfg_dbpwd = '';
 $cfg_db_language = 'utf8';
 $from_file_name = "government.sql";
  //链接数据库
 $link = mysql_connect($cfg_dbhost,$cfg_dbuser,$cfg_dbpwd);
 mysql_select_db($cfg_dbname);
 //选择编码
 mysql_query("set names ".$cfg_db_language);
 $_sql = file_get_contents($from_file_name); 
$_arr = explode(';', $_sql);
//执行sql语句
foreach ($_arr as $_value) {
    mysql_query($_value.';',$link);
}
mysql_close();
echo "导入完毕!"
?>