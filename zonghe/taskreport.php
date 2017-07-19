<?php 
include_once '../constant.php';
include_once '../mysql.php';

$preAreaCode = key($areaCode);
$sql = "select e.deptName, e.total, e.uncomplete,e.complete, if(e.total=0,0,e.complete/e.total) as rate from (select a.deptName, if(b.total is null,0,b.total) as total, if(c.uncomplete is null,0,c.uncomplete) as uncomplete, if(d.complete is null,0,d.complete) as complete from dept a left join (select t1.deptid, count(t1.deptid) as total from taskrecv t1 where 1=1 group by t1.deptid) b on a.deptId = b.deptid left join (select t2.deptid, count(t2.deptid) as uncomplete from taskrecv t2 where 1=1 and t2.is_complete < 4 group by t2.deptid) c on a.deptId = c.deptid left join (select t3.deptid, count(t3.deptid) as complete from taskrecv t3 where 1=1 and t3.is_complete = 4 group by t3.deptid) d on a.deptId = d.deptid where";
$pdo = new mysql;

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
	<meta http-equiv="expires" content="0">
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>部门查询</title>
	<script type="text/javascript" src="../js/jquery.min.js"></script>
	<link rel="stylesheet" href="../css/default.css" />
	<link rel="stylesheet" href="../css/common.css" />
	<style>
	.main{text-align:center;}
	div.menu ul li{margin-right:10px;}
	table{width:70%;margin-bottom:100px;}
	body.main{background:#FFF;}
	</style>
</head>
<body class="main">
	<div style="height: 10px;"></div>
	<div class="title">督查上报</div>
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
	</div>
	<div style="height: 10px; clear: both;"></div>
	<?php
	foreach ($areaCode as $k=>$v) {
		if($k == 1){
			echo '<div id="list' . $k . '" class="list current">';
		}else{
			echo '<div id="list' . $k . '" class="list">';
		}
		echo '<table cellspacing="1" cellpadding="6" align="center" >'
			. '<tbody><tr>'
			. '<td class="table_title" width="10%" height="100%">排名</td>'
			. '<td class="table_title" width="25%" height="100%">责任单位</td>'
			. '<td class="table_title" width="15%" height="100%">任务数</td>'
			. '<td class="table_title" width="15%" height="100%">未完成</td>'
			. '<td class="table_title" width="15%" height="100%">完成</td>'
			. '<td class="table_title" width="20%" height="100%">完成率</td>'
			. '</tr>';
		$num = 0;
		$data = array();
		$data = $pdo->getAll($sql . " a.areaCode = " . $k . ") e order by rate desc,total desc, complete desc, uncomplete asc");
		foreach($data as $key=>$d){
			if($num%2 == 0){
				echo '<tr class="alternate_line1">';
			}else{
				echo '<tr class="alternate_line2">';
			}
			$num++;
			echo '<td width="15%" height="100%">' . $num . '</td>'
				. '<td width="20%" height="100%">' . $d['deptName'] . '</td>'
				. '<td width="15%" height="100%">' . $d['total'] . '</td>'
				. '<td width="15%" height="100%">' . $d['uncomplete'] . '</td>'
				. '<td width="15%" height="100%">' . $d['complete'] . '</td>'
				. '<td width="20%" height="100%">' . round($d['rate']*100,2) . '%</td></tr>';
		}
		echo '</tbody></table></div>';
	}
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
	document.location = "taskquerybydept.php?did="+deptid;
}
</script>