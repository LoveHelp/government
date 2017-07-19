<?php
$leaderid = isset($_GET['leaderId']) ? $_GET['leaderId'] : 1;
include_once '../mysql.php';
include_once '../taizhang/taskrecvmanager.php';

$sql = "select leadername lname, leaderphoto lphoto, deptids, leaderId, leaderpost, leaderwork, discription from leader where leaderId = " . $leaderid;
$mLink = new mysql;
$leaderdetail = $mLink->getRow($sql);
$deptids = $leaderdetail['deptids'];

$leader_list_sql = "select leadername lname, leaderphoto lphoto, deptids, leaderId from leader order by leaderSort asc";
$leaderList = $mLink->getAll($leader_list_sql);

//$data = getTaskByLeader($leaderdetail['deptids'], $sort);

$total_task = get_task_by_type($mLink, $deptids);
$taskArr = $total_task["arr"];
$total = $total_task["total"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Cache-Control" content="no-siteapp" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>按领导查询</title>
<link rel="stylesheet" type="text/css" href="../css/leader.css" />
  <!--<link rel="stylesheet" type="text/css" href="../css/common.css" />-->
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../js/highcharts.js"></script>
<script type="text/javascript" src="../js/layer/layer.js"></script>
<style type="text/css">
.button1 {
	width: 68px;
	height: 28px;
	text-align: center;
	color: #FFFFFF;
	font-weight: bold;
	background-image: url(../img/button2.jpg);
	background-repeat: no-repeat;
	background-position: left;
	border-left: 0px solid #2F3C4D;
	border-right: 0px solid #2F3C4D;
	border-top: 0px solid #2F3C4D;
	border-bottom: 0px solid #2F3C4D;
	padding-top: 2px;
}
div#result table{
	width:100%;
	background:#bebabb;
	text-align: left;
	/*font-family:"宋体";*/
	border:1px solid gray;
}
div#result table tr{
	border-bottom:1px solid gray;
	line-height:140%;
}
div#result table td{
	border-right:1px solid gray;
	line-height:140%;
	font-size:14px;
	background-color: #FFFFEF;
}
div#result  table th{
	font-size: 16px;
	border-top:none;
	background:#e3e3e3;
	border-right:1px solid gray;
}
.table_title{
	line-height:140%;
	text-align:center;
	font-weight:bold;
	background-image: url(../img/table_title22.gif);
}
.alternate_line1{
	background-color:#FFFFEF;
}
.alternate_line2{
	background-color: #ECF6FB;
}
.zfldr{
	float:left;
	width:25%;
	margin-top:20px;
}
.sjz{
	display:none;
}
div#result div.link{
	text-align:right;
	color:blue;
	cursor:pointer;
	line-height:30px;
}

div#result div.pic{
	float: right; 
	height: 30px; 
	width: 30px; 
	cursor: pointer;
}
#leader_content p a{color:blue;}
#chartdiv{width:28%;float:left;display:inline;text-align:center;}
</style>
</head>
<body style="margin:10px 10px;">
<div class="wrap"> 
  <!--头部-->
<div class="header mb10">
<div class="title">南阳市人民政府【<?php echo $leaderdetail['lname']; ?>】同志工作分工</div>

</div>
  <!--主体-->
  <div class="content">
    <!--侧栏-->
    <div class="side fl"> 
      <!--menu 开始-->
      <div class="menu mb10">
        <div class="bd">
      <!-- ---- 走进政府>>政府领导>>--【副市长导航】 -- 标签开始 ---- -->
<dl class="list-dl">
	<dt class="show">
		<a href="javascript:">市 长</a>
	</dt>
	<dd style="display:block;">
		<ul>
			<li <?php if($leaderid == $leaderList[0]['leaderId']) echo 'class="current"'; ?>>
			<?php
				echo '<a title="' . $leaderList[0]['lname'] . '" href="leaderdetail.php?leaderId=' . $leaderList[0]['leaderId'] . '">' . $leaderList[0]['lname'] . '</a>';
			?>
			</li>
		</ul>
	</dd>
	<dt class="current show">
		<a href="javascript:">副市长</a>
	</dt>
	<dd style="display:block;">
		<ul>
		<?php
		if(is_array($leaderList) && count($leaderList) > 0){
			$i = 1;
			for($i=1; $i < count($leaderList); $i++){
				if($leaderid == $leaderList[$i]['leaderId']){
					echo '<li class="current">';
				}else{
					echo '<li>';
				}
				echo '<a title="' . $leaderList[$i]['lname'] . '" href="leaderdetail.php?leaderId=' . $leaderList[$i]['leaderId'] . '">' . $leaderList[$i]['lname'] . '</a></li>';
			}
		}
		?>
		</ul>
	</dd>
</dl>
<!-- ---- 走进政府>>政府领导>>--【副市长导航】 -- 标签结束 ---- -->


        </div>
      </div>
      <!--menu 结束--> 
    </div>
    <div class="main fr">
      <div class="zfld">
		<div style="width:17%;float:left;text-align:center;max-width:147px;">
			<img src="<?php echo $leaderdetail['lphoto']; ?>" alt="<?php echo $leaderdetail['lname']; ?>" />
		</div>
        <div style="overflow:hidden;width:54%;float:left;"><p class="mt30"><span class="bule"><?php echo $leaderdetail['lname']; ?></span></p>
        <p><span><?php echo $leaderdetail['leaderpost']; ?></span></p>
        <p><span class="bule">主要分工</span></p>
        <p><span><?php echo $leaderdetail['leaderwork']; ?></span></p></div>
		<div id="chartdiv" align="center"></div>
      </div>
      <!--领导简介 开始-->
      <div class="box" id="leader_content">
        <h4 class="mb10"><b>领导简介</b><i id="triangle-bottomleft"></i></h4>
        <?php echo $leaderdetail['discription']; ?>
      </div>
      <!--领导简介 结束-->
      <!--领导动态 开始-->
      <div class="box">
        <h4 class="mb10"><a href=""><b>领导工作</b></a><i id="triangle-bottomleft"></i></h4>
		<p style="text-align:right;margin:0px 10px 10px 0px;"><input style="cursor:pointer;" value="督办提醒" class="button1" onclick="javascript:hch.open_sms();" type="button"></p>
		<div id="result">
		<!--定义查询返回结果框的范围ID-->
		<table cellpadding="4" cellspacing="1" border="0">
			<thead>
				<tr class="table_title">
					<th rowspan="2" width="5%" class="table_title"> 序号</th>
					<th rowspan="2" width="12%" class="table_title">工作目标</th>
					<th rowspan="2" width="15%" class="table_title">支撑项目</th>
					<th colspan="2" width="34%" class="table_title">工作标准</th>
					<th colspan="2" width="16%" class="table_title">时间节点</th>
					<th rowspan="2" width="10%" class="table_title">责任主体</th>
					<th rowspan="2" width="8%" class="table_title" onclick="change();" id="complete_status">完成情况&#8595;</th>
				</tr>
				<tr class="table_title">
					<th width="8%" class="table_title">投资（元）</th>
					<th width="26%" class="table_title">工作标准</th>
					<th width="8%" class="table_title">开始<br>时间</th>
					<th width="8%" class="table_title">结束<br>时间</th>
				</tr>
			</thead>
			<tbody>
				
			</tbody>
		</table>
	</div>
      </div>
<!-- ---- 走进政府>>政府领导>>--【原永胜领导讲话动态】 -- 标签结束 ---- -->


      <!--领导动态 结束-->
    </div>
  </div>
  <!--底部--> 
  <!-- ---------页脚   开始------------- -->
  <div class="footer">
<!-- ---- 全局>>--【相关链接】 -- 标签结束 ---- -->
<!--<div class="copyright clearfix">南阳市政务督查管理系统</div>-->
<!-- ---- 全局>>--【页尾】 -- 标签结束 ---- -->


  </div>
</div>
<script>
var hch = {
	open_sms:function(){
		layer.open({
			type:2,
			title:'督办提醒',
			skin: 'layui-layer-rim', //加上边框
			area: ['95%', '95%'], //宽高
			content: "../sendsms.php"
		});
	}
}
//Dom option
$(document).ready(function() {

  //侧栏伸缩
    $(".list-dl dt.show").click(function(){
      $(this).toggleClass("current").siblings("dt.show").removeClass("current");
      $(this).next().slideToggle(200).siblings("dd").hide();  
    });

	$("#leader_content p").after('<p style="text-align:right;" id="more"><a href="javascript:;" onclick="more_detail();">+点击展开</a></p>');
	
});
var sort = 1;

function change(){
	var index = layer.load(1, {
	  shade: [0.1,'#fff'] //0.1透明度的白色背景
	});
	if(sort == 1){
		sort = 0;
	}else{
		sort = 1;
	}
	$.ajax({
		type:'post',
		url:"../taizhang/taskrecvmanager.php?do=leaderdetail",	
		data:{deptid:'<?php echo $deptids; ?>', sort:sort},
		success:function(result){
			var html = result;
			$("#result tbody").html(html);
			layer.close(index);
			if(sort == 1){
				$("#complete_status").html("完成情况&#8595;");
			}else{
				$("#complete_status").html("完成情况&#8593;");
			}
		}
	}); 
}
function more_detail(){
	$("#more").html('<a href="javascript:;" onclick="less();">-点击折叠</a>');
	$(".sjz").css("display", "block");
}
function less(){
	$("#more").html('<a href="javascript:;" onclick="more_detail();">+点击展开</a>');
	$(".sjz").css("display", "none");
}
$(function () {
	var index = layer.load(1, {
	  shade: [0.1,'#fff'] //0.1透明度的白色背景
	});
	$.ajax({
		type:'post',
		url:"../taizhang/taskrecvmanager.php?do=leaderdetail",	
		data:{deptid:'<?php echo $deptids; ?>', sort:sort},
		success:function(result){
			var html = result;
			$("#result tbody").html(html);
			layer.close(index);
		}
	}); 
	<?php 
		if($total_task['total'] == 0){
	?>
		$("#chartdiv").html("暂无数据！");
	<?php
		}else{
	?>
	//颜色数组
	Highcharts.setOptions({
        colors: ['#50B432', '#058DC7', '#50B432',  '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
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
            text: '台账总数（' + '<?php echo $total; ?>）'
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
                    //format: '<b>{point.name}</b>: {point.percentage:.1f} %',
					format: '<b>{point.name}</b>: {point.y}',
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
            name: '任务数',
            colorByPoint: true,
            data: [{
                name: '<?php echo $taskArr[0]["name"]; ?>',
                y: <?php echo $taskArr[0]["count"]; ?>,
            },{
				name: '<?php echo $taskArr[1]["name"]; ?>',
                y: <?php echo $taskArr[1]["count"]; ?>,
			},{
                name: '<?php echo $taskArr[2]["name"]; ?>',
                y: <?php echo $taskArr[2]["count"]; ?>,
                sliced: true,
                selected: true
            },{
				name: '<?php echo $taskArr[3]["name"]; ?>',
                y: <?php echo $taskArr[3]["count"]; ?>,
			},{
				name: '<?php echo $taskArr[4]["name"]; ?>',
                y: <?php echo $taskArr[4]["count"]; ?>,
			},{
				name: '<?php echo $taskArr[5]["name"]; ?>',
                y: <?php echo $taskArr[5]["count"]; ?>,
			},{
				name: '<?php echo $taskArr[6]["name"]; ?>',
                y: <?php echo $taskArr[6]["count"]; ?>,
			},{
				name: '<?php echo $taskArr[7]["name"]; ?>',
                y: <?php echo $taskArr[7]["count"]; ?>,
			}]
        }]
    });
	<?php } ?>
	
});
function show_header(index){
	var header_a = $("#h_header"+index).val();
	layer.open({
		title: '责任主体',
		skin: 'layui-layer-rim', //加上边框
		area: ['500px', 'auto'], //宽高
		content: header_a
	});
}
</script>
</body>
</html>
<?php
//获得台账统计
function get_task_by_type($mLink, $deptIds){
	//台账类型
	$task_type = array('1'=>'重点工作','2'=>'重大项目','3'=>'市长台账','4'=>'领导批示','5'=>'会议纪要','6'=>'建议提案','7'=>'舆情监控','8'=>'民生工程','9'=>'中央项目');

	//查询该领导任务总数
	$sql = "select count(c.taskid) as count,type from (select a.taskid, b.type from taskrecv a join task b on a.taskid = b.id where a.deptid in (" . $deptIds . ") GROUP BY taskid) c group by type";
	$res = $mLink->getAll($sql);
	$total = 0;
	//查询该领导完成任务数——根据台账类型分类
	$arr = array();
	foreach($task_type as $key=>$v){
		$type = $key;
		$count = 0;
		foreach($res as $r){
			if($r["type"] == $type){
				$count = $r["count"];
				$total += $r["count"];
			}
		}
		$arr[] = array(
			"type"		=>		$type,
			"name"		=>		$v,
			"count"		=>		$count);
	}
	$result = array(
		"total"		=>		$total,
		"arr"		=>		$arr);
	return $result;
}

$mLink->closelink();
?>
