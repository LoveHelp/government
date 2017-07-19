<?php
include_once '../mysql.php';
$charttype = isset($_GET["charttype"]) ? $_GET["charttype"] : 1;
$mLink = new mysql;
$total_task = "select count(*) as count from task where status > 0";
$total_count = $mLink->getRow($total_task);

$leader_list_sql = "select leadername lname, leaderphoto lphoto, deptids, leaderId from leader order by leaderSort asc";
$leaderList = $mLink->getAll($leader_list_sql);
foreach($leaderList as $leader){
	$leader_complete = "select count(isover) as count, res.isover from (select t.id id, t.id taskid, IFNULL(r.isover,1) as isover from task t left join (select taskid, isover, remark from taskreview order by viewtime desc, isover desc) r on t.id=r.taskid join taskrecv rc on t.id=rc.taskid where t.status>=3 and rc.deptid in (" . $leader['deptids'] . ") group by t.id order by t.id) res group by isover";

	$data = $mLink->getAll($leader_complete);
	$total = 0;
	$uncomplete = 0;
	$complete = 0;
	foreach($data as $l){
		$total += $l["count"];
		if($l["isover"] == 1){
			$uncomplete = $l["count"];
		}else if($l["isover"] == 2){
			$complete += $l["count"];
		}else if($l["isover"] == 3){
			$complete += $l["count"];
		}
	}
	
	if($total == 0){
		$rate = 0;
	}else{
		$rate = ( $complete)/$total;
	}
	$rateArr[] = array(
		"leaderId"	=>		$leader["leaderId"],
		"leader"	=>		$leader["lname"],
		"total"		=>		$total,
		"uncomplete"=>		$uncomplete,
		"complete"	=>		$complete,
		"rate"		=>		round($rate*100,2));
	$rateArr2[] = array(
		"leaderId"	=>		$leader["leaderId"],
		"leader"	=>		$leader["lname"],
		"total"		=>		$total,
		"uncomplete"=>		$uncomplete,
		"complete"	=>		$complete,
		"rate"		=>		round($rate*100,2));
}
if($charttype == 2){
	$sort = array(  
	'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序  
    'field'     => 'rate',       //排序字段  
	); 
	$arrSort = array();
	foreach($rateArr AS $uniqid => $row){  
		foreach($row AS $key=>$value){  
			$arrSort[$key][$uniqid] = $value;  
		}  
	}  
	if($sort['direction']){  
		array_multisort($arrSort[$sort['field']], constant($sort['direction']), $rateArr);  
	} 
}
?>
<html>
<head><!--CSS控制文件-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="../css/common.css">
<!--常用的javascript文件-->
<link href="../js/calendar/skin/WdatePicker.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/calendar/WdatePicker.js"></script>
<script type="text/javascript" src="../js/jquery-1.8.2.min.js"></script>
<title>市政府领导分管工作完成情况统计</title>
<script src="../js/highcharts.js"></script>
<!--<script src="../js/highcharts/code/modules/exporting.js"></script>-->
<style>
.tab{
    border-collapse: collapse;
	width:100%;
	font-size:12px;
	min-width:none !important;
}
.tab td{
    border:1px solid #d7d7d7;
}
#chartdiv{
	min-height:400px;
	line-height:400px;
}
body.main{background:#ECF6FB;}
div#result td.table_title{
	background-image:none!important;
	background-color: #84cbf1;
}
</style>
<script>
function change_activity(){
	var charttype = $("#charttype").val();
	window.location.href = "leadertasksort.php?charttype=" + charttype;
}
$(function () {
	<?php 
		if(count($rateArr) == 0){
	?>
		$("#chartdiv").html("暂无数据！");
		$("#chartdiv2").html("暂无数据！");
	<?php
		}else{
	?>
	Highcharts.chart('chartdiv2', {
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
            text: "台账总数：<?php echo $total_count['count']?>"
        },
        tooltip: {
            //pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			pointFormat: '{series.name}: <b>{point.y}</b>'
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
                name: '<?php echo $rateArr2[1]["leader"]; ?>',
                y: <?php echo $rateArr[1]["total"]; ?>
            },{
                name: '<?php echo $rateArr2[2]["leader"]; ?>',
                y: <?php echo $rateArr[2]["total"]; ?>,
                sliced: true,
                selected: true
            },{
                name: '<?php echo $rateArr2[3]["leader"]; ?>',
                y: <?php echo $rateArr[3]["total"]; ?>
            },{
                name: '<?php echo $rateArr2[4]["leader"]; ?>',
                y: <?php echo $rateArr[4]["total"]; ?>
            },{
                name: '<?php echo $rateArr2[5]["leader"]; ?>',
                y: <?php echo $rateArr[5]["total"]; ?>
            },{
                name: '<?php echo $rateArr2[6]["leader"]; ?>',
                y: <?php echo $rateArr[6]["total"]; ?>
            },{
                name: '<?php echo $rateArr2[7]["leader"]; ?>',
                y: <?php echo $rateArr[7]["total"]; ?>
            }]
        }]
    });
	//颜色数组
	Highcharts.setOptions({
        colors: ['#50B432', '#058DC7', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
    });
    Highcharts.chart('chartdiv', {
		credits: {
            enabled: false//去掉版权信息
        },
        chart: {
            type: 'column'
        },
        title: {
            text: '领导工作完成情况统计'
        },
        subtitle: {
            text: '<?php if($charttype == 1) echo "按领导排名"; else echo "按完成率排名"?>'
        },
        xAxis: {
            categories: [
                '<?php echo $rateArr[0]["leader"]; ?>',
				'<?php echo $rateArr[1]["leader"]; ?>',
				'<?php echo $rateArr[2]["leader"]; ?>',
				'<?php echo $rateArr[3]["leader"]; ?>',
				'<?php echo $rateArr[4]["leader"]; ?>',
				'<?php echo $rateArr[5]["leader"]; ?>',
				'<?php echo $rateArr[6]["leader"]; ?>',
				'<?php echo $rateArr[7]["leader"]; ?>'
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: '完成率'
            }
        },
        tooltip: {
            pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.num}</b> ({point.y:.2f}%)<br/>',
            shared: true
        },
        plotOptions: {
            column: {
				pointWidth:30,
				dataLabels:{
					enabled:true, // dataLabels设为true
					//style:{"color": "contrast", "fontSize": "11px", "fontWeight": "bold", "textOutline": "1px 1px contrast" }
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    },
					format:'{point.y:.2f}%'
				},
				cursor: 'pointer',
				events: {  
					click: function (e) {
						 window.location.href=e.point.url; 
                    }  
				}  
            }
        },
        series: [
			{
            name: '已完成',
            data: [
				{num:<?php echo $rateArr[0]['complete']; ?>,y:<?php echo $rateArr[0]['rate'];?>,url:"leaderdetail.php?leaderId=<?php echo $rateArr[0]['leaderId'];?>"},
				{num:<?php echo $rateArr[1]['complete']; ?>,y:<?php echo $rateArr[1]['rate'];?>,url:"leaderdetail.php?leaderId=<?php echo $rateArr[1]['leaderId'];?>"},
				{num:<?php echo $rateArr[2]['complete']; ?>,y:<?php echo $rateArr[2]['rate'];?>,url:"leaderdetail.php?leaderId=<?php echo $rateArr[2]['leaderId'];?>"},
				{num:<?php echo $rateArr[3]['complete']; ?>,y:<?php echo $rateArr[3]['rate'];?>,url:"leaderdetail.php?leaderId=<?php echo $rateArr[3]['leaderId'];?>"},
				{num:<?php echo $rateArr[4]['complete']; ?>,y:<?php echo $rateArr[4]['rate'];?>,url:"leaderdetail.php?leaderId=<?php echo $rateArr[4]['leaderId'];?>"},
				{num:<?php echo $rateArr[5]['complete']; ?>,y:<?php echo $rateArr[5]['rate'];?>,url:"leaderdetail.php?leaderId=<?php echo $rateArr[5]['leaderId'];?>"},
				{num:<?php echo $rateArr[6]['complete']; ?>,y:<?php echo $rateArr[6]['rate'];?>,url:"leaderdetail.php?leaderId=<?php echo $rateArr[6]['leaderId'];?>"},
				{num:<?php echo $rateArr[7]['complete']; ?>,y:<?php echo $rateArr[7]['rate'];?>,url:"leaderdetail.php?leaderId=<?php echo $rateArr[7]['leaderId'];?>"}
			]
        },{
            name: '未完成',
            data: [
				{
					num:<?php echo $rateArr[0]['uncomplete']; ?>,
					y:<?php echo 100-$rateArr[0]['rate'];?>,
					url:"leaderdetail.php?leaderId=<?php echo $rateArr[0]['leaderId'];?>"
				},{
					num:<?php echo $rateArr[1]['uncomplete']; ?>,
					y:<?php echo 100-$rateArr[1]['rate'];?>,
					url:"leaderdetail.php?leaderId=<?php echo $rateArr[1]['leaderId'];?>"
				},{
					num:<?php echo $rateArr[2]['uncomplete']; ?>,
					y:<?php echo 100-$rateArr[2]['rate'];?>,
					url:"leaderdetail.php?leaderId=<?php echo $rateArr[2]['leaderId'];?>"
				},{
					num:<?php echo $rateArr[3]['uncomplete']; ?>,
					y:<?php echo 100-$rateArr[3]['rate'];?>,
					url:"leaderdetail.php?leaderId=<?php echo $rateArr[3]['leaderId'];?>"
				},{
					num:<?php echo $rateArr[4]['uncomplete']; ?>,
					y:<?php echo 100-$rateArr[4]['rate'];?>,
					url:"leaderdetail.php?leaderId=<?php echo $rateArr[4]['leaderId'];?>"
				},{
					num:<?php echo $rateArr[5]['uncomplete']; ?>,
					y:<?php echo 100-$rateArr[5]['rate'];?>,
					url:"leaderdetail.php?leaderId=<?php echo $rateArr[5]['leaderId'];?>"
				},{
					num:<?php echo $rateArr[6]['uncomplete']; ?>,
					y:<?php echo 100-$rateArr[6]['rate'];?>,
					url:"leaderdetail.php?leaderId=<?php echo $rateArr[6]['leaderId'];?>"
				},{
					num:<?php echo $rateArr[7]['uncomplete']; ?>,
					y:<?php echo 100-$rateArr[7]['rate'];?>,
					url:"leaderdetail.php?leaderId=<?php echo $rateArr[7]['leaderId'];?>"
				}
			]
        }]
    });
	<?php } ?>
	
});
</script>
</head>
<body class="main">
	<div id="search">
		<form name="queryForm" method="post" action="#">
			<table class="table01" width="100%" cellspacing="1" cellpadding="4" class="tab">
			<tbody>
				<tr>
					<td class="table_title">市政府领导分管工作完成情况统计</td>
				</tr>
			</tbody>
			</table>
		</form>
	</div>
	<div style="height:10px; width:100%;"></div>
	<div id="result">
		<form name="queryForm2" method="get" action="taizhang.php?do=cartogram" target="">
			<div style="width:100%;">
				<table cellpadding="0" cellspacing="0" border="0" align="center" class="tab" style="overflow-x:auto;">
					<tr>
						<td style="color:#FFFFFF; font-weight:bold; font-size: 12px; background-color:#84cbf1; text-align:left; height:28px;">
							&emsp;<img src="../img/xtb.png">&ensp;按任务数统计
						</td>
					</tr>
					<tr>
						<td width="100%" bgcolor="#fff">
							<div id="chartdiv2" align="center"  style="border-bottom:1px solid #ccc;"></div>
						</td>
					</tr>
				</table>
			<div>
			<div style="width:100%; height:25px; margin-top:5px; margin-bottom:4px; text-align:right;">
			<?php
				if($charttype == 1){
					echo '<input value="按完成率" onclick="window.location.href=\'leadertasksort.php?charttype=2\'" class="button1" type="button">';
				}else{
					echo '<input value="按领导" onclick="window.location.href=\'leadertasksort.php?charttype=1\'" class="button1" type="button">';
				}
			?>	
			</div>
			<div style="width:100%;">
				<table cellpadding="0" cellspacing="0" border="0" align="center" class="tab" style="overflow-x:auto;">
					<tr>
						<td style="color:#FFFFFF; font-weight:bold; font-size: 12px; background-color:#84cbf1; text-align:left; height:28px;">
							&emsp;<img src="../img/xtb.png">&ensp;按<?php if($charttype == 1) echo "领导"; else echo "完成率"?>排名
						</td>
					</tr>
					<tr>
						<td width="100%" bgcolor="#fff">
							<div id="chartdiv" align="center" style="width:100%; border-bottom:1px solid #ccc; height:560px;"></div>
						</td>
					</tr>
				</table>
			</div>
			<div style="height:5px; width:100%;"></div>
			<div style="width:100%;">
				<table cellspacing="1" cellpadding="6" align="center" class="tab">
					<tr>
						<td class="table_title" width="10%" height="100%">排名</td>
						<td class="table_title" width="25%" height="100%">领导</td>
						<td class="table_title" width="15%" height="100%">任务数</td>
						<td class="table_title" width="15%" height="100%">未完成</td>
						<td class="table_title" width="15%" height="100%">完成</td>
						<td class="table_title" width="20%" height="100%">完成率</td>
					</tr>
					<?php
					if(is_array($rateArr) && count($rateArr) > 0){
						$i = 0;
						foreach($rateArr as $r){
							if($i%2 == 0){
								echo '<tr class="alternate_line1">';
							}else{
								echo '<tr class="alternate_line2">';
							}
							$i++;

							echo '<td height="100%">' . $i . '</td>'
								. '<td height="100%">' . $r['leader'] . '</td>'
								. '<td height="100%">' . $r['total'] . '</td>'
								. '<td height="100%">' . $r['uncomplete'] . '</td>'
								. '<td height="100%">' . $r['complete'] . '</td>'
								. '<td height="100%">' . round($r['rate'],2) . '%</td></tr>';
						}
					}
					?>
				</table>
			</div>
		</form>
	</div>
	<div style="height:10px; width:100%;"></div>
</body>
</html>