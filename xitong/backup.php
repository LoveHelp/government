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
 //配置信息
 $cfg_dbhost = '192.168.1.120';
 $cfg_dbname = 'government';
 $cfg_dbuser = 'admin';
 $cfg_dbpwd = 'CrZQV7Q48aDuca4F';
 $cfg_db_language = 'utf8';
 $to_file_name = "government.sql";
 // END 配置
 //链接数据库
 $link = mysql_connect($cfg_dbhost,$cfg_dbuser,$cfg_dbpwd);
 mysql_select_db($cfg_dbname);
 //选择编码
 mysql_query("set names ".$cfg_db_language);
 //数据库中有哪些表
 $tables = mysql_list_tables($cfg_dbname);
 //将这些表记录到一个数组
 $tabList = array();
 while($row = mysql_fetch_row($tables)){
  $tabList[] = $row[0];
 }

 echo "运行中，请耐心等待...<br/>";
 $info = "-- ----------------------------\r\n";
 $info .= "-- 日期：".date("Y-m-d H:i:s",time())."\r\n";
 $info .= "-- 备份数据库所有表结构和数据,以便重新导入数据库.\r\n";
 $info .= "-- ----------------------------\r\n\r\n";
 file_put_contents($to_file_name,$info,FILE_APPEND);
 //将每个表的表结构导出到文件
 foreach($tabList as $val){
  $sql = "show create table ".$val;
  $res = mysql_query($sql,$link);
  $row = mysql_fetch_array($res);
  $info = "-- ----------------------------\r\n";
  $info .= "-- Table structure for `".$val."`\r\n";
  $info .= "-- ----------------------------\r\n";
  $info .= "DROP TABLE IF EXISTS `".$val."`;\r\n";
  $sqlStr = $info.$row[1].";\r\n\r\n";
  //追加到文件
  file_put_contents($to_file_name,$sqlStr,FILE_APPEND);
  //释放资源
  mysql_free_result($res);
 }
 //将每个表的数据导出到文件
 foreach($tabList as $val){
  $sql = "select * from ".$val;
  $res = mysql_query($sql,$link);
  //如果表中没有数据，则继续下一张表
  if(mysql_num_rows($res)<1) continue;
  //
  $info = "-- ----------------------------\r\n";
  $info .= "-- Records for `".$val."`\r\n";
  $info .= "-- ----------------------------\r\n";
  file_put_contents($to_file_name,$info,FILE_APPEND);
  //读取数据
  while($row = mysql_fetch_row($res)){
   $sqlStr = "INSERT INTO `".$val."` VALUES (";
   foreach($row as $zd){
    $sqlStr .= "'".$zd."', ";
   }
   //去掉最后一个逗号和空格
   $sqlStr = substr($sqlStr,0,strlen($sqlStr)-2);
   $sqlStr .= ");\r\n";
   file_put_contents($to_file_name,$sqlStr,FILE_APPEND);
  }
  //释放资源
  mysql_free_result($res);
  file_put_contents($to_file_name,"\r\n",FILE_APPEND);
 }

 echo "OK!";

?>