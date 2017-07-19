<?php
$itemtype = isset($_POST['itemtype']) ? $_POST['itemtype'] : "";
include("taizhang.php");
include_once "../mysql.php";
$mLink = new mysql;
$total_task = get_task_by_type($mLink, $itemtype);
$taskArr = get_all($mLink);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head><!--CSS控制文件-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>统计图表</title>
<!--<link rel="stylesheet" href="../css/default.css">-->
<link rel="stylesheet" href="../css/common.css">
<!--常用的javascript文件-->
<script type="text/javascript" src="../js/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="../js/highcharts.js"></script>
<!--<script src="../js/highcharts/code/modules/exporting.js"></script>-->
<style type="text/css">
.tab{
    border-collapse: collapse;
	width:100%;
	font-size:12px;
}
.tab td{
    border:1px solid #d7d7d7;
}
#chartdiv{
	min-height:400px;
	line-height:400px;
}
</style>
<script type="text/javascript">
$(function () {
 var typeArr = new Array('','重点工作','重大事项','建议提案','领导批示件','舆情监控','会议纪要','民生工程','其他督查工作');
	<?php 
		if($total_task['total'] == 0){
	?>
		$("#chartdiv").html("暂无数据！");
		$("#chartdiv2").html("暂无数据！");
		$("#chartdiv3").html("暂无数据！");
	<?php
		}else{
	?>
	//颜色数组
	Highcharts.setOptions({
        colors: ['#50B432', '#b91903', '#058DC7', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
    });
    Highcharts.chart('chartdiv', {
		credits: {
            enabled: false//去掉版权信息
        },
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
		xAxis: {
            categories: ['未完成', '完成']
        },
        title: {
            text: typeArr[<?php if($itemtype == "") echo "0"; else echo $itemtype; ?>] + '台账总数（' + '<?php echo $total_task["total"]; ?>）'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                },
				events: {
					click: function(e) {
						if(e.point.name == "未完成"){
							window.location.href = "taskreview.php?taskstate_a=1";
						}else if(e.point.name == "完成"){
							window.location.href = "taskreview.php?taskstate_a=2";
						}else{
							window.location.href = "taskreview.php?taskstate_a=0";
						}
					}
				}
            }
        },
        series: [{
            name: '台账总数',
            colorByPoint: true,
            data: [{
                name: '完成',
                y: <?php echo $total_task["complete"]; ?>
            },{
                name: '未完成',
                y: <?php echo $total_task["uncomplete"]; ?>,
                sliced: true,
                selected: true
            }]
        }]
    });
	Highcharts.chart('chartdiv2', {
		credits: {
            enabled: false//去掉版权信息
        },
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
			height: 300,
            type: 'pie'
        },
        title: {
			
            text: '未完成台账总数（' + '<?php echo $total_task["uncomplete"]; ?>）'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                },
				events: {
					click: function(e) {
						for(var i=0; i<typeArr.length; i++){
							if(e.point.name == typeArr[i]){
								window.location.href = "taskreview.php?taskstate_a=1&tasktypes="+i;
							}
						}
					}
				}
            }
        },
        series: [{
            name: '未完成',
            colorByPoint: true,
            data: [{
                name: typeArr[1],
                y: <?php echo $taskArr[0]["uncomplete"]; ?>
            },{
				name: typeArr[2],
				y: <?php echo $taskArr[1]["uncomplete"]; ?>
			},{
                name: typeArr[3],
                y: <?php echo $taskArr[2]["uncomplete"]; ?>,
                sliced: true,
                selected: true
            },{
				name: typeArr[4],
				y: <?php echo $taskArr[3]["uncomplete"]; ?>
			},{
				name: typeArr[5],
				y: <?php echo $taskArr[4]["uncomplete"]; ?>
			},{
				name: typeArr[6],
				y: <?php echo $taskArr[5]["uncomplete"]; ?>
			},{
				name: typeArr[7],
				y: <?php echo $taskArr[6]["uncomplete"]; ?>
			},{
				name: typeArr[8],
				y: <?php echo $taskArr[7]["uncomplete"]; ?>
			}]
        }]
    });
	Highcharts.chart('chartdiv3', {
		credits: {
            enabled: false//去掉版权信息
        },
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
			height: 300,
            type: 'pie'
        },
        title: {
            text: '完成台账总数（' + '<?php echo $total_task["complete"]; ?>）'
        },
        tooltip: {
            //pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                },
				events: {
					click: function(e) {
						for(var i=0; i<typeArr.length; i++){
							if(e.point.name == typeArr[i]){
								window.location.href = "taskreview.php?taskstate_a=3&tasktypes="+i;
							}
						}
					}
				}
            }
        },
        series: [{
            name: '完成',
            colorByPoint: true,
            data: [{
                name: typeArr[1],
                y: <?php echo $taskArr[0]["complete"]; ?>
            },{
				name: typeArr[2],
				y: <?php echo $taskArr[1]["complete"]; ?>
			},{
                name: typeArr[3],
                y: <?php echo $taskArr[2]["complete"]; ?>,
                sliced: true,
                selected: true
            },{
				name: typeArr[4],
				y: <?php echo $taskArr[3]["complete"]; ?>
			},{
				name: typeArr[5],
				y: <?php echo $taskArr[4]["complete"]; ?>
			},{
				name: typeArr[6],
				y: <?php echo $taskArr[5]["complete"]; ?>
			},{
				name: typeArr[7],
				y: <?php echo $taskArr[6]["complete"]; ?>
			},{
				name: typeArr[8],
				y: <?php echo $taskArr[7]["complete"]; ?>
			}]
        }]
    });
	<?php } ?>
	
});
function change_activity(){
	window.location.href = "column.php";
}
</script>
</head>
<body class="main">
	<div id="search">
		<form name="queryForm" method="post" action="cartogram.php">
			<table class="table01" width="100%" cellspacing="1" cellpadding="4">
				<tr>
					<td colspan="5" class="table_title">统计图表</td>
				</tr>
				<tr>
					<td class="td_title">图表类型</td>
					<td class="td_content" style="width:185px;"> 
						<select name="charttype" id="charttype" class="select" onchange="change_activity();">
							<option value="0" selected>饼形图</option>
							<option value="1">柱状图</option>
						</select>
					</td>
					<td class="td_title">台账类型</td>
					<td class="td_content" style="width:185px;"> 
						<select name="itemtype" id="itemtype" class="select">
							<option value=""></option>
							<?php
							
							foreach($task_type as $key=>$v){
								if($itemtype == $key){
									echo '<option value="' . $key . '" selected>' . $v . '</option>';
								}else{
									echo '<option value="' . $key . '">' . $v . '</option>';
								}
							}
							?>
						</select>
					</td>
					<td class="td_button">
						<input value="查 询" class="button1" type="submit" />
						<!--<input name="button4" class="button1" style="cursor:pointer" onclick="queryReport_reset();return false;" value="重 置" type="button" /> -->
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div style="height:10px;"></div>
	<div id="result2">
		<form name="queryForm2" method="get" action="taizhang.php?do=cartogram">
			<table cellpadding="0" cellspacing="0" border="0" align="center" class="tab">
				<tr>
					<td colspan="2" style="color:#FFFFFF; font-weight:bold; font-size: 12px; background-color:#84cbf1; text-align:left; height:28px;">
						&emsp;<img src="../img/xtb.png">&ensp;台帐信息统计图
					</td>
				</tr>
				<tr>
					<td rowspan="4" style="background-color:#ffffff;">
						<div id="chartdiv" align="center"></div>
					</td>
					<td width="51%" height="28" background="../img/btds.jpg" style="color:#db0000; font-weight:bold; font-size: 12px; text-align:left;">
						&emsp;<img src="../img/xtb.png">&ensp;未完成统计图
					</td>
				</tr>
				<tr>
					<td style="background-color:#ffffff;">
						<div id="chartdiv2" align="center"></div>
					</td>
				</tr>
				<tr>
					<td height="28" background="../img/btds.jpg" style="color:#db0000; font-weight:bold; font-size: 12px; text-align:left;">
						&emsp;<img src="../img/xtb.png">&ensp;完成统计图
					</td>
				</tr>
				<tr>
					<td style="background-color:#ffffff;">
						<div id="chartdiv3" align="center"></div>
					</td>
				</tr>
			</table>
		</form>
	</div>
</body>
</html>
<?php
//获得台账统计
function get_task_by_type($mLink, $itemtype){
	$where = "";
	if($itemtype != ""){
		$where .= " and type = " . $itemtype;
	}
	$sql = "select count(*) as count from task where status > 0" . $where;
	$total_res = $mLink->getRow($sql);
	$total = $total_res['count'];//总任务数

	$sql = "SELECT count(*) as count FROM (SELECT id,isover FROM (SELECT task.id, taskreview.isover FROM task LEFT JOIN taskreview ON task.id=taskreview.taskid WHERE 1=1" . $where ." ORDER BY isover DESC) AS res1 GROUP BY id) AS res2 WHERE isover = 2";//2:完成
	$complete_res = $mLink->getRow($sql);
	$complete = $complete_res['count'];//总任务完成
	
	$uncomplete = $total - $complete;//未完成
	$res = array(
		"total"			=>		$total,
		"uncomplete"	=>		$uncomplete,
		"complete"		=>		$complete);
	return $res;
}

function get_all($mLink){
	for($i = 1; $i <= 8; $i++){
		$sql = "select count(*) as count from task where status > 0 and type = " . $i;
		$total_res = $mLink->getRow($sql);
		$total = $total_res['count'];//总任务数
		$sql = "SELECT count(*) as count FROM (SELECT id,isover FROM (SELECT task.id, taskreview.isover FROM task LEFT JOIN taskreview ON task.id=taskreview.taskid WHERE type = " . $i . " ORDER BY isover DESC) AS res1 GROUP BY id) AS res2 WHERE isover = 2";//2:完成
		$com_res = $mLink->getRow($sql);
		$complete = $com_res['count'];
		$uncomplete = $total - $complete;
		$res[] = array(
			"total"			=>		$total,
			"uncomplete"	=>		$uncomplete,
			"complete"		=>		$complete);
	}
	return $res;
}

$mLink->closelink();
?>