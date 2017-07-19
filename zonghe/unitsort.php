<?php
include_once '../constant.php';
include_once '../mysql.php';

$itemareaCode = isset($_REQUEST['itemareaCode']) ? $_REQUEST['itemareaCode'] : 1;
$mLink = new mysql;
$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 1;
$setTime1 = isset($_REQUEST['setTime1']) ? $_REQUEST['setTime1'] : "";
$setTime2 = isset($_REQUEST['setTime2']) ? $_REQUEST['setTime2'] : "";
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
	<script type="text/javascript" src="../js/calendar/WdatePicker.js"></script>
	<link href="../js/calendar/skin/WdatePicker.css" rel="stylesheet" type="text/css">
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
	<input type="hidden" name="preAreaCode" value='<?=$preAreaCode?>'>
	<div class="menu">
		<ul>
			<a href="tasksort.php"><li class="last_li">总排名</li></a>
			<a href="worksort.php"><li class="last_li">按工作排名</li></a>
			<a href="typesort.php"><li class="last_li">按工作类型排名</li></a>
			<a href="unitsort.php"><li class="last_li current">按单位排名</li></a>
		</ul>
	</div>
	<div style="height: 30px; line-height:30px; text-align:right;width:70%;margin:0 auto;clear: both;color:red;">*牵头单位每项工作计1分,责任单位计0.5分</div>
	<div id="list4" class="list current">
	<form action="unitsort.php" method="post">
		<input type="hidden" name="sort" id="sort" value="<?php echo $sort; ?>" />
		<table style="margin-bottom:10px;" cellspacing="1" cellpadding="6" align="center">
			<tr>
				<td class="td_title" style="width:100px;" height="28">选择单位：</td>
				<td class="td_content" width="auto" align="left"> 
					<select name="itemareaCode" id="itemareaCode" class="select" onchange="change_activity(this, <?php echo $sort; ?>)">
						<?php
						foreach($areaCode as $key=>$value){
							if($itemareaCode == $key){
								echo '<option value="' . $key . '" selected>' . $value . '</option>';
							}else{
								echo '<option value="' . $key . '">' . $value . '</option>';
							}
						}
						?>
					</select>
				</td>
				<td height="20" class="td_title">时间</td>
				<td class="td_content" style="text-align:left;">
					<input type="text" name="setTime1" id="setTime1" maxlength="" size="15" value="<?php echo $setTime1; ?>" onfocus="WdatePicker()" readonly="readonly" class="input" />
					至<input type="text" name="setTime2" id="setTime2" maxlength="" size="15" value="<?php echo $setTime2; ?>" onfocus="WdatePicker()" readonly="readonly" class="input" />
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
				<td class="table_title" width="25%" height="100%">责任单位</td>
				<td class="table_title" width="15%" height="100%">任务数</td>
				<td class="table_title" width="15%" height="100%">未完成</td>
				<td class="table_title" width="15%" height="100%">完成</td>
				<td class="table_title" width="20%" height="100%" onclick="change_sort();">完成率<?php if($sort == 1) echo "&#8593;"; else echo "&#8595;"?></td>
			</tr>
			<?php
			$dataArr4 = get_task_sort_by_unit($mLink, $itemareaCode, $sort, $setTime1, $setTime2);
			if(is_array($dataArr4) && count($dataArr4) > 0){
				$s = 0;
				foreach($dataArr4 as $d){
					if($s%2 == 0){
						echo '<tr class="alternate_line1">';
					}else{
						echo '<tr class="alternate_line2">';
					}
					$s++;
					$uncomplete = $d['total_count'] - $d['complete_count'];
					echo '<td height="100%">' . $s . '</td>'
						. '<td height="100%">' . $d['deptName'] . '</td>'
						. '<td height="100%">' . $d['total_count'] . '</td>'
						. '<td height="100%">' . $uncomplete . '</td>'
						. '<td height="100%">' . $d['complete_count'] . '</td>'
						. '<td height="100%">' . round($d['rate']*100,2) . '%</td></tr>';
				}
			}else{
				echo '<tr class="alternate_line1"><td colspan="6" align="center"><font size="2">没有符合条件的纪录</font></td></tr>';
			}
			?>
		</table>
	</div>
</body>
</html>
<script type="text/javascript">
function change_activity(obj, sort){
	window.location.href = "unitsort.php?itemareaCode=" + $(obj).val() + "&sort=" + sort + "&setTime1=<?php echo $setTime1; ?>&setTime2=<?php echo $setTime2; ?>";
}
function change_sort(){
	<?php
		if($sort == 1){
			$sort = 0;
		}else{
			$sort = 1;
		}
	?>
	window.location.href = "unitsort.php?itemareaCode=<?php echo $itemareaCode; ?>&sort=<?php echo $sort; ?>&setTime1=<?php echo $setTime1; ?>&setTime2=<?php echo $setTime2; ?>";
}
</script>
<?php
function get_task_sort_by_unit($mLink, $areaCode, $sort, $time1, $time2){
	if($sort == 1){
		$s = " desc";
	}else{
		$s = " asc";
	}
	$where = "";
	if($time1 != ""){
		$where .= " and recvdate >= '" . $time1 . "'";  
	}
	if($time2 != ""){
		$where .= " and recvdate <= '" . $time2 . "'";
	}
	$sql = "select d.deptId, d.deptName, IFNULL(table1.total_count,0) as total_count,IFNULL(table1.total_point,0) as total_point,IFNULL(table2.complete_count,0) as complete_count,IFNULL(table2.complete_point,0) as complete_point, IFNULL(complete_point/total_point,0) as rate from dept d left join (select s1.deptid, count(taskid) as total_count,sum(point) as total_point from (select a.deptId as deptid, a.deptName, b.taskid as taskid,IF(b.ishead=1,2,IF(b.ishead=0,1,0)) as point  from dept a join taskrecv b on a.deptId = b.deptid" . $where . ")s1 GROUP BY s1.deptid) table1 on d.deptId = table1.deptid left join (select count(deptId) as complete_count, deptId,sum(point) as complete_point from (select * from (select a.deptId, a.deptName, b.taskid, c.isover,IF(b.ishead=1,2,IF(b.ishead=0,1,0)) as point from dept a join taskrecv b on a.deptId = b.deptid" . $where ." join taskreview c on b.taskid = c.taskid order by deptId asc,b.taskid asc, c.isover desc) res1 group by deptId, taskid) res2 where isover = 2 group by deptId) table2 on d.deptid = table2.deptid where d.areaCode = " . $areaCode . " order by rate" . $s . ",total_count" . $s . ",complete_count" . $s . ",deptId" . $s;
	$res = $mLink->getAll($sql);
	return $res;
}
?>
