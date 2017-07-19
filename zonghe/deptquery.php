<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}

include_once '../constant.php';
include_once '../mysql.php';

$title='单位列表';
$preAreaCode = key($areaCode); //从当前内部指针位置返回元素键名
if(empty($_REQUEST['deptids'])){
	$deptids="";
	$lname="";
	$sql = "select deptid, deptname, areaCode from dept order by areaCode, deptid;";
}
else{ //领导页面跳转过来
	$deptids = empty($_REQUEST['deptids']) ? 0 : trim($_REQUEST['deptids']);
	$lname = empty($_REQUEST['lname']) ? trim('') : trim($_REQUEST['lname']);
	$title="南阳市人民政府【".$lname."】同志工作分工";
	$sql = "select deptid, deptname, areaCode from dept where deptid in (". $deptids .") order by areaCode, deptid;";
}
$pdo = new mysql;
$data = $pdo->getAll($sql);


?>
<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>按单位查询</title>
	<script type="text/javascript" src="../js/jquery.min.js"></script>
	<!-- <link rel="stylesheet" href="../css/default.css" /> -->
	<link rel="stylesheet" href="../css/common.css" />
</head>
<body class="main">
	<div class="title"><?=$title?></div>
	<div style="height: 10px;"></div>
	<div class="menu">
		<ul>
			<?php
				$i=0;
				$count = count($areaCode);
				foreach ($areaCode as $key => $value) {
					$i++;
					$class = '';
					if($key == $preAreaCode)
						$class = 'current';
					if($i == $count)
						$class = 'lastli';
					?><li class="<?=$class?>" onmousemove="javascript:showlist(<?=$key?>, this);"><?=$value?></li><?php
				}
			  ?>
		</ul>
		<input type="hidden" name="acode" value='<?=$preAreaCode?>'>
		<input type="hidden" name="lname" value="<?=$lname?>">
		<input type="hidden" name="deptids" value="<?=$deptids?>">
	</div>
	<div style="height: 10px; clear: both;"></div>
	<?php 
		$count = count($data);
		$defaultAreaCode = -1;
		for($i=0; $i<$count; $i++){
			if($defaultAreaCode != $data[$i]['areaCode']){
				$divclass="current";
				if($defaultAreaCode != -1)
					$divclass="";
					?></ul></div>
					<?php
				$defaultAreaCode = $data[$i]['areaCode'];
				?><div class="list <?=$divclass?>" id="list<?=$defaultAreaCode?>"><ul>
				<?php	
			}
			$class='';
			if($i%6 == 5)
				$class = 'lastli';
			?><li class="<?=$class?>" onclick="redirect(<?=$data[$i]['deptid']?>)"><?=$data[$i]['deptname']?></li>
			<?php
		}
		if($count > 0)
			?></ul></div>
			<?php
	?>
</body>
</html>
<script type="text/javascript">
function showlist (id, obj) {
	//修改样式
	$("div.menu>ul>li").removeClass('current');
	$(obj).addClass('current');
	//切换部门
	var preid = $("input[name='acode']").val();
	$("input[name='acode']").val(id); //保存当前的areacode

	var preList = '#list'+preid;
	if($(preList).hasClass('current')){
		$(preList).removeClass('current');
		$(preList).fadeOut();	
	}
	
	var list = '#list'+id;
	if(!$(list).hasClass('current')){
		$(list).addClass('current');
		$(list).fadeIn('2000');
	}
}

function redirect(deptid){
	var lname = $("input[name='lname']").val();
	var deptids = $("input[name='deptids']").val();
	var param = "?did=" + deptid;
	if(lname.length > 0){
		param += "&deptids=" + deptids;
		param += "&lname=" + lname;
	}
	window.location = "taskquerybydept.php" + param;
}
</script>