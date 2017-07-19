<?php
header("Content-type:text/html;charset=utf-8");
$itemtype = isset($_POST['itemtype']) ? $_POST['itemtype'] : "";

include_once "../mysql.php";
$mLink = new mysql;
$taskArr = get_all($mLink);
$deptArr = get_task_by_dept($mLink);
?>
<html>
<head><!--CSS控制文件-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="../css/common.css">
<!--常用的javascript文件-->
<link href="../js/calendar/skin/WdatePicker.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/calendar/WdatePicker.js"></script>
<script type="text/javascript" src="../js/jquery-1.8.2.min.js"></script>
<title>统计图表</title>
<script src="../js/highcharts.js"></script>
<!--<script src="../js/highcharts/code/modules/exporting.js"></script>-->
<style>
.tab{
    border-collapse: collapse;
	width:100%;
	font-size:12px;
}
.tab td{
    border:1px solid #d7d7d7;
}
#chartdiv,#chartdiv2{
	min-height:400px;
	line-height:400px;
	background:#fff;
}
input[type="text"]{
	width:150px;
	height:21px;
	border:1px solid #ccc;
	-webkit-box-sizing:content-box;
}
</style>
<script>
$(function () {
 var typeArr = new Array('','重点工作','重大事项','建议提案','领导批示件','舆情监控','会议纪要','民生工程','其他督查工作');
	<?php 
		if($taskArr["count"] == 0){
	?>
		$("#chartdiv").html("暂无数据！");
		$("#chartdiv2").html("暂无数据！");
	<?php
		}else{
	?>
	//颜色数组
	Highcharts.setOptions({
        colors: ['#50B432', '#058DC7', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
    });
    $('#chartdiv').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: '台账总数（<?php echo $taskArr["count"]; ?>）'
        },
        xAxis: {
            categories: ['重点工作','重大事项','建议提案','领导批示件','舆情监控','会议纪要','民生工程','其他督查工作'],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: '完成率'
            }
        },
        tooltip: {
            pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.y:.1f}%)<br/>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
				pointWidth:30,
				dataLabels:{
					enabled:true, // dataLabels设为true
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    },
					format:'{point.y:.2f}%'
				}
            }
        },
        series: [{
            name: '未完成',
            data: [
				{num:<?php echo $taskArr['uncomplete'][0]; ?>, y:<?php echo $taskArr['uncom_rate'][0]; ?>},
				{num:<?php echo $taskArr['uncomplete'][1]; ?>, y:<?php echo $taskArr['uncom_rate'][1]; ?>},
				{num:<?php echo $taskArr['uncomplete'][2]; ?>, y:<?php echo $taskArr['uncom_rate'][2]; ?>},
				{num:<?php echo $taskArr['uncomplete'][3]; ?>, y:<?php echo $taskArr['uncom_rate'][3]; ?>},
				{num:<?php echo $taskArr['uncomplete'][4]; ?>, y:<?php echo $taskArr['uncom_rate'][4]; ?>},
				{num:<?php echo $taskArr['uncomplete'][5]; ?>, y:<?php echo $taskArr['uncom_rate'][5]; ?>},
				{num:<?php echo $taskArr['uncomplete'][6]; ?>, y:<?php echo $taskArr['uncom_rate'][6]; ?>},
				{num:<?php echo $taskArr['uncomplete'][7]; ?>, y:<?php echo $taskArr['uncom_rate'][7]; ?>}
			]
        }, {
           name: '已完成',
            data: [
				{num:<?php echo $taskArr['complete'][0]; ?>, y:<?php echo $taskArr['com_rate'][0]; ?>},
				{num:<?php echo $taskArr['complete'][1]; ?>, y:<?php echo $taskArr['com_rate'][1]; ?>},
				{num:<?php echo $taskArr['complete'][2]; ?>, y:<?php echo $taskArr['com_rate'][2]; ?>},
				{num:<?php echo $taskArr['complete'][3]; ?>, y:<?php echo $taskArr['com_rate'][3]; ?>},
				{num:<?php echo $taskArr['complete'][4]; ?>, y:<?php echo $taskArr['com_rate'][4]; ?>},
				{num:<?php echo $taskArr['complete'][5]; ?>, y:<?php echo $taskArr['com_rate'][5]; ?>},
				{num:<?php echo $taskArr['complete'][6]; ?>, y:<?php echo $taskArr['com_rate'][6]; ?>},
				{num:<?php echo $taskArr['complete'][7]; ?>, y:<?php echo $taskArr['com_rate'][7]; ?>}
		   ]
        }]
    });
	Highcharts.chart('chartdiv2', {
		credits: {
            enabled: false//去掉版权信息
        },
        chart: {
            type: 'column'
        },
        title: {
            text: '各部门任务完成情况统计图'
        },
        xAxis: {
            categories: [<?php for($i=0; $i<10;$i++) {?>'<?php echo $deptArr[$i]["deptName"]; ?>',<?php } ?>],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: '完成率'
            }
        },
        tooltip: {
            //pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.y:.0f}%)<br/>',
			pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.y:.1f}%)<br/>',
            shared: true
        },
        plotOptions: {
            column: {
                //pointPadding: 0.2,
                //borderWidth: 0,
				pointWidth:30,
				dataLabels: {
                    align: 'top',
                    enabled: true,
					//format: '<b>{series.name}</b>: {point.y:.1f} %',
					format: '{point.y:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    },
                }
            }
        },
        series: [
		{
            name: '未完成',
            data: [
				{num:<?php echo $deptArr[0]['total']-$deptArr[0]['complete']; ?>, y:<?php echo (1-$deptArr[0]['rate'])*100; ?>},
				{num:<?php echo $deptArr[1]['total']-$deptArr[1]['complete']; ?>, y:<?php echo (1-$deptArr[1]['rate'])*100; ?>},
				{num:<?php echo $deptArr[2]['total']-$deptArr[2]['complete']; ?>, y:<?php echo (1-$deptArr[2]['rate'])*100; ?>},
				{num:<?php echo $deptArr[3]['total']-$deptArr[3]['complete']; ?>, y:<?php echo (1-$deptArr[3]['rate'])*100; ?>},
				{num:<?php echo $deptArr[4]['total']-$deptArr[4]['complete']; ?>, y:<?php echo (1-$deptArr[4]['rate'])*100; ?>},
				{num:<?php echo $deptArr[5]['total']-$deptArr[5]['complete']; ?>, y:<?php echo (1-$deptArr[5]['rate'])*100; ?>},
				{num:<?php echo $deptArr[6]['total']-$deptArr[6]['complete']; ?>, y:<?php echo (1-$deptArr[6]['rate'])*100; ?>},
				{num:<?php echo $deptArr[7]['total']-$deptArr[7]['complete']; ?>, y:<?php echo (1-$deptArr[7]['rate'])*100; ?>},
				{num:<?php echo $deptArr[8]['total']-$deptArr[8]['complete']; ?>, y:<?php echo (1-$deptArr[8]['rate'])*100; ?>},
				{num:<?php echo $deptArr[9]['total']-$deptArr[9]['complete']; ?>, y:<?php echo (1-$deptArr[9]['rate'])*100; ?>}
			]
		},{
			name: '已完成',
			data: [
				{num:<?php echo $deptArr[0]['complete']; ?>, y:<?php echo $deptArr[0]['rate']*100; ?>},
				{num:<?php echo $deptArr[1]['complete']; ?>, y:<?php echo $deptArr[1]['rate']*100; ?>},
				{num:<?php echo $deptArr[2]['complete']; ?>, y:<?php echo $deptArr[2]['rate']*100; ?>},
				{num:<?php echo $deptArr[3]['complete']; ?>, y:<?php echo $deptArr[3]['rate']*100; ?>},
				{num:<?php echo $deptArr[4]['complete']; ?>, y:<?php echo $deptArr[4]['rate']*100; ?>},
				{num:<?php echo $deptArr[5]['complete']; ?>, y:<?php echo $deptArr[5]['rate']*100; ?>},
				{num:<?php echo $deptArr[6]['complete']; ?>, y:<?php echo $deptArr[6]['rate']*100; ?>},
				{num:<?php echo $deptArr[7]['complete']; ?>, y:<?php echo $deptArr[7]['rate']*100; ?>},
				{num:<?php echo $deptArr[8]['complete']; ?>, y:<?php echo $deptArr[8]['rate']*100; ?>},
				{num:<?php echo $deptArr[9]['complete']; ?>, y:<?php echo $deptArr[9]['rate']*100; ?>}
			]

		}]
    });
	<?php } ?>
	
});
function change_activity(){
	window.location.href = "cartogram.php";
}
</script>
</head>
<body class="main">
	<div id="search">
		<form name="queryForm" method="post" action="column.php">
			<table class="table01" width="100%" cellspacing="1" cellpadding="4">
			<tbody>
				<tr>
					<td colspan="3" class="table_title">统计图表</td>
				</tr>
				<tr>
					<td class="td_title">图表类型</td>
					<td class="td_content" style="width:185px;"> 
						<select name="charttype" id="charttype" class="select" onchange="change_activity();">
							<option value="0">饼形图</option>
							<option value="1" selected>柱状图</option>
						</select>
					</td>
					<td class="td_button">
						<input value="查 询" class="button1" type="submit" />
						<!--<input name="button4" class="button1" style="cursor:pointer" onclick="queryReport_reset();return false;" value="重 置" type="button" /> -->
					</td>
					<!--<td class="td_title" width="120" height="28">基数（年）:</td>
					<td class="td_content" width="120">
						<input type="text" id="tjfxTime" name="tjfxTime" value="2016" onclick="WdatePicker({dateFmt:'yyyy'})" class="Wdate" readonly="true">
					</td>-->
				</tr>
			</tbody>
			</table>
		</form>
	</div>
	<div style="height:10px;"></div>
	<div id="result">
		<form name="queryForm2" method="get" action="taizhang.php?do=cartogram" target="">
			<table cellpadding="0" cellspacing="0" border="0" align="center" class="tab" style="overflow-x:auto;">
				<tr>
					<td style="color:#FFFFFF; font-weight:bold; font-size: 12px; background-color:#84cbf1; text-align:left; height:28px;">
						&emsp;<img src="../img/xtb.png">&ensp;台帐信息统计图
					</td>
				</tr>
				<tr>
					<td width="100%" bgcolor="#fff">
						<div id="chartdiv" align="center" style="width:100%;border-bottom:1px solid #ccc;padding-bottom:10px;"></div>
					</td>
				</tr>
			</table>
			<div style="height:5px;"></div>
			<table cellpadding="0" cellspacing="0" border="0" align="center" class="tab">
				<tr>
					<td style="color:#FFFFFF; font-weight:bold; font-size: 12px; background-color:#84cbf1; text-align:left; height:28px;">
						&emsp;<img src="../img/xtb.png">&ensp;各部门完成情况统计图（任务数前十名）
					</td>
				</tr>
				<tr>
					<td width="100%" bgcolor="#fff">
						<div id="chartdiv2" align="center" style="width:100%;border-bottom:1px solid #ccc;padding-bottom:10px;"></div>
					</td>
				</tr>
			</table>
		</form>
	</div>
</body>
</html>
<?php
//获得台账统计
function get_task_by_dept($mLink){
	//$sql = "(select a.deptId as deptid, a.deptName, count(b.deptid) as task_count from dept a join taskrecv b on a.deptId = b.deptid GROUP BY b.deptid)  UNION (select deptid, deptName, 0 as task_count from (select a2.deptId as deptid, a2.deptName, b2.deptid as deptsort from dept a2 left join taskrecv b2 on a2.deptId = b2.deptid order by a2.deptId) res where ISNULL(res.deptsort) )  order by deptid asc";
	$sql = "select d.deptId, d.deptName, table1.total as total,if(ISNULL(table2.complete),0,table2.complete) as complete, complete/total as rate from dept d join (select a.deptId as deptid, a.deptName, b.taskid as taskid, count(b.deptid) as total from dept a join taskrecv b on a.deptId = b.deptid join task on b.taskid = task.id GROUP BY b.deptid order by total desc limit 0,10) table1 on d.deptId = table1.deptid left join (select count(deptId) as complete, deptId,taskid from (select * from (select a.deptId, a.deptName, b.taskid, c.isover from dept a join taskrecv b on a.deptId = b.deptid join task on b.taskid = task.id join taskreview c on b.taskid = c.taskid order by deptId asc,b.taskid asc, c.isover desc) res1 group by deptId, taskid) res2 where isover = 2 group by deptId) table2 on d.deptid = table2.deptid";//取前十名
	$dept_res = $mLink->getAll($sql);
	
	return $dept_res;
}

function get_all($mLink){
	$sql = "select count(*) as count from task where status > 0";
	$count_res = $mLink->getRow($sql);
	$count = $count_res['count'];//总任务数

	for($i = 1; $i <= 8; $i++){
		$sql = "select count(*) as count from task where status > 0 and type = " . $i;
		$total_res = $mLink->getRow($sql);
		$total[] = $total_res['count'];//某个台账类型总任务数
		$sql = "SELECT count(*) as count FROM (SELECT id,isover FROM (SELECT task.id, taskreview.isover FROM task LEFT JOIN taskreview ON task.id=taskreview.taskid WHERE type = " . $i . " ORDER BY isover DESC) AS res1 GROUP BY id) AS res2 WHERE isover = 2";//3:完成
		$com_res = $mLink->getRow($sql);
		$complete[] = $com_res['count'];
		$uncomplete[] = $total_res['count'] - $com_res['count'];
		if($total_res['count'] > 0){
			$rate = round($com_res['count']/$total_res['count'], 2);
			$com_rate[] = $rate*100;
			$uncom_rate[] = (1-$rate)*100;
		}else{
			$com_rate[] = 0;
			$uncom_rate[] = 0;
		}
	}
	$res = array(
		"count"			=>		$count,
		"total"			=>		$total,
		"uncomplete"	=>		$uncomplete,
		"uncom_rate"	=>		$uncom_rate,
		"complete"		=>		$complete,
		"com_rate"		=>		$com_rate);
	return $res;
}

$mLink->closelink();
?>