<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
include_once "../mysql.php";
//查询总任务数
$link=new mysql;
$sql='select count(id) as count from task';
$res=$link->getRow($sql);
$all=$res['count'];
//查询已下发任务和未下发任务
$sql='select count(id) as count from task where status>2';
$res=$link->getRow($sql);
$zhuanban = $res['count'];
if($all>=$zhuanban) $weizhuan=$all-$zhuanban;
//查询已下发任务和未下发任务
$sql='select count(id) as count from task where status>3';
$res=$link->getRow($sql);
$jieshou = $res['count'];
if($zhuanban>=$jieshou) $weishou=$zhuanban-$jieshou;
//反馈进度查询
$ruqi=date('Y-m').'-25';

$sql='SELECT DISTINCT taskid FROM taskfeedback WHERE backtime<"'.$ruqi.'"';
$res=$link->getAll($sql);
$ruqifan = count($res);
$sql='SELECT DISTINCT taskid FROM taskfeedback WHERE backtime="'.$ruqi.'"';
$res=$link->getAll($sql);
$anqifan=count($res);

//办结申请和已办结
$sql='SELECT DISTINCT taskid FROM taskrecv WHERE is_complete=2';
$res=$link->getAll($sql);
$banjie=count($res);
//拖期反馈
$sql='SELECT DISTINCT taskid FROM taskfeedback WHERE backtime>"'.$ruqi.'"';
$res=$link->getAll($sql);
$tuoqifan=count($res);
?>
<!Doctype html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<link rel="stylesheet" type="text/css" href="../css/xtstyle.css">
<link rel="stylesheet" type="text/css" href="../css/progress.css">
<script src="../js/jquery.min.js"></script>
<script src="../js/layer/layer.js"></script>
<title>进度提醒</title>
</head>
<body class="main">
	<table width="100%"  cellpadding="4" cellspacing="1" class="table01">
    <tr>
      <td colspan="4" class="table_title" >进度提醒</td>
	 </tr>
	</table>
<div>
<p>总任务数</p>
<div style="width:100%" class="progress">
      <span  id="1" onclick="query(this);" class="blue" style="width:<?php echo($all*100/$all);?>%;"><span><?=$all?></span></span>
</div>
<p>已转办</p>
<div style="width:100%"  class="progress">
      <span  id="2" onclick="query(this);" class="green" style="width:<?php echo($zhuanban*100/$all);?>%;"><span><?=$zhuanban?></span></span>
</div>
<p>未转办</p>
<div style="width:100%"  class="progress">
      <span  id="2" onclick="query(this);" class="orange" style="width:<?php echo($weizhuan*100/$all);?>%;"><span><?=$weizhuan?></span></span>
</div>
<p>已接收</p>
<div style="width:100%"  class="progress">
      <span  id="2" onclick="query(this);" class="green" style="width:<?php echo($weishou*100/$all);?>%;"><span><?=$weishou?></span></span>
</div>
<p>已反馈</p>
<div style="width:100%"  class="progress">
      <span  id="3" onclick="query(this);" class="green" style="width:<?php echo($ruqifan*100/$all);?>%;"><span><?=$ruqifan?></span></span>
</div>
<p>未反馈</p>
<div style="width:100%"  class="progress">
      <span  id="3" onclick="query(this);" class="red" style="width:<?php echo(($all-$ruqifan)*100/$all);?>%;"><span><?=($all-$ruqifan)?></span></span>
</div>
<p>已办结</p>
<div style="width:100%"  class="progress">
      <span  id="4" onclick="query(this);" class="green" style="width:<?php echo($banjie*100/$all);?>%;"><span><?=$banjie?></span></span>
</div>
<p>未办结</p>
<div style="width:100%"  class="progress">
      <span  id="4" onclick="query(this);" class="orange" style="width:<?php echo(($all-$banjie)*100/$all);?>%;"><span><?=($all-$banjie)?></span></span>
</div>
<br>
<HR style="border:3 double #987cb9" width="100%" color=#987cb9 SIZE=3>
<p style="padding-bottom:100px;"><small>蓝色——代表总任务数<br />绿色——代表已完成数<br />黄色——代表需要提醒数<br />红色——代表超期警告。</p></small>
</div>
<script type="text/javascript">
	function query(spanbtn){
		var id=spanbtn.id;
		switch(id){
			case "1":			
			self.location.href="../taizhang/taskregister.php";
			break;
			case "2":		
			self.location.href="../taizhang/taskmanage.php";
			break;
			case "3":
			self.location.href="../taizhang/taskreview.php";
			break;
			case "4":
			self.location.href="../taizhang/taskcomplete.php?itemstatus=1";
			break;		
		}
	}
</script>
</body>