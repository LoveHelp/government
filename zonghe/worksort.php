<?php
include_once '../mysql.php';

$itemtarget = isset($_POST['itemtarget']) ? $_POST['itemtarget'] : "";
$generaltaskid = isset($_POST['generaltask']) ? $_POST['generaltask'] : 1;

$mLink = new mysql;
$generaltaskArr = get_generaltask($mLink);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
	<meta http-equiv="expires" content="0">
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>督查上报</title>
	<script type="text/javascript" src="../js/jquery.min.js"></script>
	<link rel="stylesheet" href="../css/default.css" />
	<link rel="stylesheet" href="../css/common.css" />
	<style>
	.main{text-align:center;}
	div.menu ul li{margin-right:20px;}
	div.menu ul li.last_li{margin-right:20px;}
	table{width:70%;margin-bottom:100px;text-align:center;}
	body.main{background:#FFF;}
	A:link, A:visited, A:active{color:#000;}
	</style>
</head>
<body class="main">
	<div style="height: 10px;"></div>
	<div class="title">督查上报</div>
	<div style="height: 10px;"></div>
	<div class="menu">
		<ul>
			<a href="tasksort.php"><li class="last_li">总排名</li></a>
			<a href="worksort.php"><li class="last_li current">按工作排名</li></a>
			<a href="typesort.php"><li class="last_li">按工作类型排名</li></a>
			<a href="unitsort.php"><li class="last_li">按单位排名</li></a>
		</ul>
	</div>
	<div style="height: 10px; clear: both;"></div>
	<div id="list" class="list current">
	<form action="worksort.php" method="post">
		<table style="margin-bottom:10px;" cellspacing="1" cellpadding="6" align="center">
			<tr>
				<td class="td_title" style="width:100px;" height="28">总体任务：</td>
				<td class="td_content" > 
					<select name="generaltask" id="generaltask" style="width:100%">
					<?php
					foreach($generaltaskArr as $g){
						if($generaltaskid == $g['id']){
							echo '<option selected value="' . $g['id'] . '">' . $g['name'] . '</option>';
						}else{
							echo '<option value="' . $g['id'] . '">' . $g['name'] . '</option>';
						}
					}
					?>
					</select>
				</td>
				<td class="td_title" style="width:100px;" height="28">工作目标：</td>
				<td class="td_content" width="auto"> 
					<input name="itemtarget" type="text" value="<?php echo $itemtarget; ?>" style="width:98%;"/>
				</td>
				<td class="td_button">
					<input value="查 询" style="cursor:hand" class="button1" type="submit">
				</td>
			</tr>
		</table>
	</form>
		<table cellspacing="1" cellpadding="6" align="center" >
			<tr>
				<td class="table_title" width="10%" height="100%">排名</td>
				<td class="table_title" width="25%" height="100%">工作目标</td>
				<td class="table_title" width="15%" height="100%">支撑项目</td>
				<td class="table_title" width="15%" height="100%">责任单位</td>
				<td class="table_title" width="15%" height="100%">反馈时间</td>
				<td class="table_title" width="20%" height="100%">完成率</td>
			</tr>
	<?php
	$taskArr = get_task_sort_by_generaltask($mLink, $generaltaskid, $itemtarget);
	$count = 0;
	if(is_array($taskArr) && count($taskArr) > 0){
	foreach($taskArr as $d){
		if($count%2 == 0){
			echo '<tr class="alternate_line1">';
		}else{
			echo '<tr class="alternate_line2">';
		}
		$count++;
		$taskid = $d['id'];
		$deptList = get_dept_list($mLink, $taskid);
		$size = count($deptList);
		if(is_array($deptList) && count($deptList) > 0){
			$progress = get_progress($mLink, $taskid, $deptList[0]['deptid']);
			echo '<td rowspan="' . $size . '" height="100%">' . $count . '</td>'
				. '<td rowspan="' . $size . '" height="100%">' . $d['target'] . '</td>'
				. '<td rowspan="' . $size . '" height="100%">' . $d['title'] . '</td>'
				. '<td height="100%">' . $deptList[0]['deptName'] . '</td>';
			if($progress == ""){
				echo '<td height="100%">未反馈</td>'
					. '<td height="100%">0%</td></tr>';
			}else{
				echo '<td height="100%">' . $progress['backtime'] . '</td>'
					. '<td height="100%">' . $progress['progress'] . '%</td></tr>';
			}
			
			for($r=1;$r<$size;$r++){
				$progress_r = get_progress($mLink, $taskid, $deptList[$r]['deptid']);
				if(($count-1)%2 == 0){
					echo '<tr class="alternate_line1">';
				}else{
					echo '<tr class="alternate_line2">';
				}
				echo '<td height="100%">' . $deptList[$r]['deptName'] . '</td>';
				if($progress_r == ""){
					echo '<td height="100%">未反馈</td>'
					. '<td height="100%">0%</td></tr>';
				}else{
					echo '<td height="100%">' . $progress_r['backtime'] . '</td>'
					. '<td height="100%">' . $progress_r['progress'] . '%</td></tr>';
				}
			}
		}else{
			echo '<td height="100%">' . $count . '</td>'
				. '<td height="100%">' . $d['target'] . '</td>'
				. '<td height="100%">' . $d['title'] . '</td>'
				. '<td height="100%"></td>'
				. '<td height="100%"></td>'
				. '<td height="100%"></td></tr>';
		}	
	}
	}else{
		echo '<tr class="alternate_line1"><td colspan="6">暂无数据</td></tr>';
	}
	?>
	</table></div>
</body>
</html>
<script type="text/javascript">
function showlist(i) {
	var url = "";
	if(i == 1){
		url = "tasksort.php";
	}else if(i == 2){
		url = "worksort.php";
	}else if(i == 3){
		url = "typesort.php"
	}else{
		url = "unitsort.php";
	}
	window.location.href = url;
}
</script>
<?php
function get_generaltask($mLink){
	$sql = "select id, name from generaltask";
	$res = $mLink->getAll($sql);
	return $res;
}
function get_task_sort_by_generaltask($mLink, $generaltaskid, $target){
	$where = "";
	if($target != ""){
		$where .= " and target like '%" . $target . "%'";
	}
	$sql = "select id, title, target from task where status >= 3 and generaltaskid = " . $generaltaskid . $where;
	$res = $mLink->getAll($sql);
	return $res;
}
function get_dept_list($mLink, $taskid){
	$sql = "select a.deptid, b.deptName from taskrecv a, dept b where a.deptid = b.deptId and a.taskid = " . $taskid;
	$res = $mLink->getAll($sql);
	return $res;
}
function get_progress($mLink, $taskid, $deptid){
	$sql = "select backtime, progress from taskfeedback where taskid = " . $taskid . " and deptid = " . $deptid . " order by backtime desc, progress desc limit 0,1";
	$res = $mLink->getRow($sql);
	return $res;
}
$mLink->closelink();
?>