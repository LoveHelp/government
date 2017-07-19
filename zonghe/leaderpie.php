<?php
$leaderId = isset($_GET['leaderId']) ? $_GET['leaderId'] : "";
include_once "../mysql.php";
$mLink = new mysql;
$total_task = get_task_by_type($mLink, $leaderId);
$taskArr = $total_task["completeArr"];
$total = $total_task["total"];
$leadername = $total_task["leadername"];
?>
<html>
<head><!--CSS控制文件-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="../css/default.css">
<!--常用的javascript文件-->
<link href="../js/calendar/skin/WdatePicker.css" rel="stylesheet" type="text/css">
</head>
<body class="main">
<script type="text/javascript" src="../js/calendar/WdatePicker.js"></script>
<script type="text/javascript" src="../js/jquery-1.8.2.min.js"></script>
<title>统计图表</title>
<script src="../js/highcharts.js"></script>
<style>
.tab{
    border-collapse: collapse;
	width:98%;
	font-size:12px;
}
.tab td{
    border:1px solid #d7d7d7;
}
#chartdiv{
	min-height:400px;
	line-height:400px;
}
body.main{background:#ECF6FB;}
</style>
<script>
$(function () {
 var typeArr = new Array('','重点工作','重大事项','建议提案','领导批示件','舆情监控','会议纪要','民生工程','其他督查工作');
	<?php 
		if($total_task['total'] == 0){
	?>
		$("#chartdiv").html("暂无数据！");
	<?php
		}else{
	?>
	//颜色数组
	Highcharts.setOptions({
        colors: ['#b91903', '#058DC7', '#50B432',  '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
    });
   Highcharts.chart('chartdiv', {
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
            text: '完成台账总数（' + '<?php echo $total; ?>）'
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
                    },
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
                y: <?php echo $taskArr[0]["complete"]; ?>,
            },{
				name: typeArr[2],
				y: <?php echo $taskArr[1]["complete"]; ?>,
			},{
                name: typeArr[3],
                y: <?php echo $taskArr[2]["complete"]; ?>,
                sliced: true,
                selected: true
            },{
				name: typeArr[4],
				y: <?php echo $taskArr[3]["complete"]; ?>,
			},{
				name: typeArr[5],
				y: <?php echo $taskArr[4]["complete"]; ?>,
			},{
				name: typeArr[6],
				y: <?php echo $taskArr[5]["complete"]; ?>,
			},{
				name: typeArr[7],
				y: <?php echo $taskArr[6]["complete"]; ?>,
			},{
				name: typeArr[8],
				y: <?php echo $taskArr[7]["complete"]; ?>,
			}]
        }]
    });
	<?php } ?>
	
});
function change_activity(){
	window.location.href = "column.php";
}
</script>
<table width="98.5%" border="0" cellpadding="0" cellspacing="0" style="border-left:1px solid #FFFFFF;">
	<tbody>
	<tr>
		<td height="2" colspan="3"></td>
	</tr>
	<tr>
		<div>
			<form name="queryForm" method="post" action="cartogram.php">
				<table class="table01" width="100%" cellspacing="1" cellpadding="4">
				<tbody>
					<tr>
						<td colspan="2" class="table_title">南阳市人民政府【<?php echo $leadername; ?>】同志分管工作完成情况统计</td>
					</tr>
					<!--<tr>
						<td class="td_title" style="width:10%;height:28px;min-width:100px;">图表类型</td>
						<td class="td_content"> 
							<select name="charttype" id="charttype" class="select" onchange="change_activity();">
								<option value="1" selected>完成</option>
								<option value="2">未完成</option>
							</select>
						</td>
					</tr>-->
				</tbody>
				</table>
			</form>
		</div>
	<form name="queryForm2" method="get" action="taizhang.php?do=cartogram" target="">
	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tbody>
	<tr>
		<td height="5" colspan="3"><img src="../img/shim.gif" width="1" height="5"></td>
    </tr>
    <tr>
		<td>
		<table width="98%" cellpadding="0" cellspacing="0" border="0" align="center" class="tab">
	    <tbody>
		<tr>
			<td height="28" bgcolor="#bc1b00" style="color:#FFFFFF; font-weight:bold; font-size: 12px;">&nbsp;&nbsp;<img src="../img/xtb.png"> 台帐信息统计图</td>
        </tr>
		<tr>
			<td rowspan="4" style="background:#FFF">
				<div id="chartdiv" align="center" style="width:99%;"></div>
			</td>
        </tr>
		</tbody></table>  
     </td>
    </tr>    
  </tbody></table>
 
</form>
<!--主处理界面的底部样式-->
    </td>
  </tr>
</tbody></table>
<table width="98.5%" height="35" border="0" cellpadding="0" cellspacing="0">
  <tbody><tr>
    <td class="Copyright" align="center">南阳市政务督查管理系统</td>
  </tr>
</tbody></table>
<!--结束主处理界面的底部样式-->
</body>
</html>
<?php
//获得台账统计
function get_task_by_type($mLink, $leaderId){
	//台账类型
	$task_type = array('1'=>'重点工作','2'=>'重大事项','3'=>'建议提案','4'=>'领导批示件','5'=>'舆情监控','6'=>'会议纪要','7'=>'民生工程','8'=>'其他督查工作');
	//根据leaderId查询领导姓名和负责的部门
	$sql = "select leaderName, deptIds from leader where leaderId = " . $leaderId;
	$leaderInfo = $mLink->getRow($sql);
	$leadername = $leaderInfo["leaderName"];
	$deptIds = $leaderInfo["deptIds"];

	//查询该领导完成任务总数
	$sql = "select count(taskid) as total from (select d.taskid,d.isover, d.type from (select a.taskid, b.isover,c.type from taskrecv a join taskreview b on a.taskid = b.taskid join task c on b.taskid = c.id where a.deptid in (" . $deptIds . ") order by b.id) d group by d.taskid) e where e.isover = 2";
	$totalRes = $mLink->getRow($sql);
	$total = $totalRes["total"];
	//查询该领导完成任务数——根据台账类型分类
	foreach($task_type as $key=>$v){
		$type = $key;
		$sql = "select count(taskid) as complete from (select d.taskid,d.isover, d.type from (select a.taskid, b.isover,c.type from taskrecv a join taskreview b on a.taskid = b.taskid join task c on b.taskid = c.id where a.deptid in (" . $deptIds . ") order by b.id) d group by d.taskid) e where e.isover = 2 and e.type = " . $type;
		$completeRes = $mLink->getRow($sql);
		$complete = $completeRes["complete"];
		$completeArr[] = array(
			"type"		=>		$type,
			"complete"	=>		$complete);
	}

	$res = array(
		"leadername"	=>		$leadername,
		"total"			=>		$total,
		"completeArr"	=>		$completeArr);
	return $res;
}

$mLink->closelink();
?>