<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:index.php');
	exit;
}

include "shouye.php";
$roleid = isset($_SESSION['userRoleID']) ? $_SESSION['userRoleID'] : "";
$deptid = $_SESSION['userDeptID'];
$userid = $_SESSION['userID'];

$menuList = json_decode(get_menu_list($roleid), true);
$total_task = get_task_by_type("");
//加载进度提醒数据
include_once "reminddata.php";
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>南阳市政务督查管理系统</title>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script src="js/highcharts.js"></script>
<script type="text/javascript" src="js/layer/layer.js" ></script>
<link rel="stylesheet" href="css/shouye.css" type="text/css">
<link rel="stylesheet" type="text/css" href="css/progress.css">
<script src="js/jquery.slides.min.js"></script>
<script type="text/javascript" src="js/fudong.js" ></script>
<script type="text/javascript">
$(function(){
	$(window).resize();
});
window.onresize = function(){
	$(".pic img").width($(".pic").width());
}
var hch = {
    clear:function(){
		$("#sdate").attr("value", "");
		$("#edate").attr("value", "");
    },
	open_online:function(uid){
		$(".online").css("display", "none");
		this.index = layer.open({
			type:2,
			title:'在线交流',
			closeBtn: 0, //不显示关闭按钮
			area: ['300px', '300px'], //宽高
			shade: [0],
			//skin: 'layui-layer-rim', //加上边框
			offset: 'rb', //右下角弹出
			content: "xinxi/newmsg.php?uid=" + uid,
			success: function() {
				hch.auto_height();
			}
		});
	},
	auto_height:function(){
		layer.iframeAuto(this.index);
	}
}
function change_dc(obj){
	var id = $(obj).attr("id");
	$(".dc").removeClass("current");
	$("#"+id).addClass("current");
	get_detail(id);
}
function get_detail(id){
	$.ajax({
		type:'post',
		url:"shouye.php?do=dc",	
		data:{type:id, deptid:'<?php echo $deptid; ?>'},
		success:function(result){
			var html = result;
			$("#dc_info").html(html);
		}
	}); 
}
$(function(){ 
	get_detail(3);
	//focus
  $(".focus").slides({
        container: 'pic',
        paginationClass: 'navi',
        generateNextPrev: true,
        next: "next",
        prev: "prev",
        preload: true,
		preloadImage: 'img/loading.gif',
        play: 5000,
        pause: 2500
    });
  $(".focus").hover(function() {
        $(".focus .prev, .focus .next").fadeIn();
    },
    function() {
        $(".focus .prev, .focus .next").fadeOut();
    });

	$("div.MenuBg1").click(function(){
		var menuClass = $(this).attr("class");
		if(menuClass == "MenuBg1"){
			$(this).attr("class","MenuBg1Down");
		}else{
			$(this).attr("class","MenuBg1");
		}

		var sub = "#"+ $(this).attr("id") + "Sub";
		var display = $(sub).css("display");

		if(display == "none"){
			$(sub).css("display", "block");
		}else{
			$(sub).css("display", "none");
		}
	});
	$("div.MenuBg1Down").click(function(){
		var menuClass = $(this).attr("class");
		if(menuClass == "MenuBg1"){
			$(this).attr("class","MenuBg1Down");
		}else{
			$(this).attr("class","MenuBg1");
		}

		var sub = "#"+ $(this).attr("id") + "Sub";
		var display = $(sub).css("display");

		if(display == "none"){
			$(sub).css("display", "block");
		}else{
			$(sub).css("display", "none");
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
			backgroundColor: '#f1f1f1',
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
		xAxis: {
            categories: ['未完成', '完成']
        },
		title :{
			text:null
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
							window.location.href = "taizhang/taskreview.php?taskstate_a=1";
						}else if(e.point.name == "完成"){
							window.location.href = "taizhang/taskreview.php?taskstate_a=2";
						}else{
							window.location.href = "taizhang/taskreview.php?taskstate_a=0";
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
	<?php } ?>
});
	function query(spanbtn){
		var id=spanbtn.id;
		switch(id){
			case "1":			
			window.open("taizhang/inputtitle.php");
			break;
			case "2":		
			window.open("taizhang/taskrecv.php");
			break;
			case "3":
			window.open("taizhang/taskfeedback.php");
			break;	
		}
	}
</script>
</head>
<body class="bc">
	<div class="online" onclick="hch.open_online();" style="display: block;">在线交流</div>
	<!-- 最新动态 -->
	<div class="ceng1">
		<div class="dongtai">
			<p>最新动态</p>
			<div class="focus fl">
				<div class="pic">
					<?php
					$imageArray = json_decode(getimgsfromdir(), true);
					if(is_array($imageArray) && count($imageArray) > 0){
						foreach($imageArray as $image){
							//$image = '/ueditor/php/upload/image/' . $image;
							echo '<div>'
								. '<img src="' . $image . '" alt="">'
								. '<span></span>'
								. '</div>';
						}
					}else{
						echo '<div style="position: absolute; top: 0px; z-index: 0; display: none;">'
							. '<img src="img/shouye/1.jpg" alt=""> '
							. '<span>南阳市第五届人民代表大会第五次会议闭幕</span>'
							. '</div>'
							. '<div style="position: absolute; top: 0px; z-index: 0; display: none;">'
							. '<img src="img/shouye/2.jpg" alt="">'
							. '<span>在南阳市五届人大五次会议闭幕会上的讲话</span>'
							. '</div>'
							. '<div style="position: absolute; top: 0px; z-index: 0; display: none;">'
							. '<img src="img/shouye/3.jpg" alt="">'
							. '<span>政协南阳市五届四次会议闭幕</span>'
							. '</div>'
							. '<div style="position: absolute; top: 0px; z-index: 5; display: block;">'
							. '<img src="img/shouye/4.jpg" alt="">'
							. '<span>在政协南阳市五届四次会议闭幕大会上的讲话</span>'
							. '</div>'
							. '<div style="position: absolute; top: 0px; z-index: 0; display: none;">'
							. '<img src="img/shouye/5.jpg" alt="">'
							. '<span>南阳市第五届人民代表大会第五次会议开幕</span>'
							. '</div>';
					}
					?>
				</div>
			</div>
		</div>
		<div class="ducha">
			<div>
				<li class="dc sx current" id="3" onmouseover="change_dc(this);" onclick="change_dc(this);">督查动态</li>
				<li class="dc sx" id="1" onmouseover="change_dc(this);" onclick="change_dc(this);">督查通知</li>
				<li class="dc " id="4" onmouseover="change_dc(this);" onclick="change_dc(this);">督查文件</li>
				<li class="dc sx" id="2" onmouseover="change_dc(this);" onclick="change_dc(this);">督查通报</li>				
			</div>
			<div style="clear:both;"></div>
			<div class="info" id="dc_info"></div>
		</div>
	</div>
	<div style="clear:both;"></div>
	<!-- 督查工作 -->
	<div class="ceng2">
		<li class="main_img"><img src="img/shouye/dcgz.png" /></li>
		<li class="sub_img"><a href="taizhang/taskreview.php?route=1&tasktypes=1" target="mainFrame"><img src="img/shouye/icon1.png" /><p>重点工作</p></a></li>
		<li class="sub_img"><a href="taizhang/taskreview.php?route=1&tasktypes=2" target="mainFrame"><img src="img/shouye/icon2.png" /><p>重大项目</p></a></li>
		<li class="sub_img"><a href="taizhang/taskreview.php?route=1&tasktypes=3" target="mainFrame"><img src="img/shouye/icon3.png" /><p>市长台账</p></a></li>
		<li class="sub_img"><a href="taizhang/taskreview.php?route=1&tasktypes=4" target="mainFrame"><img src="img/shouye/icon9.png" /><p>领导批示</p></a></li>
		<li class="sub_img"><a href="taizhang/taskreview.php?route=1&tasktypes=5" target="mainFrame"><img src="img/shouye/icon4.png" /><p>会议纪要</p></a></li>
		<li class="sub_img"><a href="taizhang/taskreview.php?route=1&tasktypes=6" target="mainFrame"><img src="img/shouye/icon5.png" /><p>建议提案</p></a></li>
		<li class="sub_img"><a href="taizhang/taskreview.php?route=1&tasktypes=7" target="mainFrame"><img src="img/shouye/icon6.png" /><p>舆情监控</p></a></li>
		<li class="sub_img"><a href="taizhang/taskreview.php?route=1&tasktypes=8" target="mainFrame"><img src="img/shouye/icon7.png" /><p>民生工程</p></a></li>
		<li class="sub_img"><a href="taizhang/taskreview.php?route=1&tasktypes=9" target="mainFrame"><img src="img/shouye/icon8.png" /><p>中央项目</p></a></li>
	</div>
	<!-- 完成情况统计 -->
	<div class="ceng3">
		<li class="li_01">
			<div id="chartdiv" align="center"></div>
		</li>
		<li class="li_02">
				<ul class="tongji">
				<li>
				<p style="color: #000000">填报提醒:</p>
				<div class="progress">				
     			 <span  id="1" onclick="query(this);" style="width:<?=empty($alltarget) ? "10%" : ($waittarget*100/$alltarget); ?>%;"><span><?=$waittarget?>/<?=$alltarget?></span></span>
				</div>
				</li>
				<li>
					<p style="color: #000000">接收提醒:</p>				
				<div class="progress">
     			<span  id="2" onclick="query(this);" style="width:<?=empty($recvall) ? "10%" : ($waitrecv*100/$recvall);?>%;"><span><?=$waitrecv?>/<?=$recvall?></span></span>
				</div>	
				</li>
				<li>
				<p style="color: #000000">反馈提醒:</p>			
				<div class="progress">
     			 <span  id="3" onclick="query(this);"  class="green"  style="width:<?=empty($recvall) ? "10%" : ($yellow*100/$recvall);?>%;"><span><?=$yellow?>/<?=$recvall?></span></span>
				</div>
				</li>
				<li>
				<p style="color: #000000">到期提醒:</p>
				<div class="progress">
     			<span  id="3" onclick="query(this);" class="blue" style="width:<?=empty($recvall) ? "10%" : ($orange*100/$recvall);?>%;"><span><?=$orange?>/<?=$recvall?></span></span>
				</div>
				</li>				
				<li>
				<p style="color: #000000">超期提醒:</p>
				<div class="progress">
     			<span  id="3" onclick="query(this);" class="orange" style="width:<?=empty($recvall) ? "10%" : ($orange*100/$recvall);?>%;"><span><?=$orange?>/<?=$recvall?></span></span>
				</div>
				</li>
				<li>
				<p style="color: #000000">督办提醒:</p>
				<div class="progress">
     			<span  id="3" onclick="query(this);" class="red" style="width:<?=empty($recvall) ? "10%" : ($red*100/$recvall);?>%;"><span><?=$red?>/<?=$recvall?></span></span>
				</div>
				</li>
			</ul>
		</li>
		<li class="li_03">
			<a href="xinxi/online.php"><img src="img/shouye/zx.png"></a>
			<div class="content">
				<?php
					$messages = get_messages_by_id($userid);
					if(is_array($messages) && count($messages)){
						foreach($messages as $m){
							/*echo '<li class="li_02"><span style="float:right;display:inline;">' . $m['time'] . '</span>'
								. '<a href="xinxi/online.php?id=' . $m['id'] . '">' . "&nbsp;&nbsp;" . $m['uname'] . "&nbsp;&nbsp;" . $m['content'] . '</a>'
								. '</li>';*/
							echo '<p><a href="xinxi/online.php?id=' . $m['id'] . '"><span>' . $m['uname'] . "：" . $m['content'] . '</span></a></p>';
						}
					}else{
						echo '<div style="height:100%;width:100%;text-align:center;">暂无信息</div>';
					}
				?>
			</div>
		</li>
	</div>
<div id="ad1" style="z-index: 10000;width: 450px;height: 203px; text-align: right; background-position: right center; position: absolute; left: 800.767px; top: 488.766px;">
<!--<img style="position: absolute;top: 0;left: 0;" src="img/fudong.png" border="0">-->
<img style="position: absolute;top: 0;left: 0;" src="img/shouye/xinxi.png" border="0">
<a href="javascript:void(0);" onfocus="this.blur()" style="z-index:99999;color:#FFF;position: absolute;top: 13px;right: 10px;text-decoration:none;" onclick="document.getElementById('ad1').style.display='none';">×关闭</a>
<div style="position: absolute;text-align:left;top:65px;left:40px;width:380px;height:150px;z-index: 10;">

	<?php
	$href=array(1=>'xinxi/noticedetail.php?id=',2=>'xinxi/notificationdetail.php?id=',3=>'xinxi/infodetail.php?id=',4=>'xinxi/infodetail.php?id=');
	$float=floatdiv();
	if(!empty($float))
	{
		foreach($float as $f){
			echo '<a title="'.$f['infoTitle'].'" href="'.$href[$f['infoType']].$f['infoId'].'&type='.$f['infoType'].'" target="_blank" style="text-decoration:none;"><p style="line-height:100%;color:#000;background:url(img/shouye/listico.png) 3px no-repeat;text-indent:15px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;border-bottom:1px dotted #cdcdcd;line-height:35px;margin:0;padding:0;">'.$f['infoTitle'].'</p></a>';
		}
	}		
	?>
</div>		
</a><br>

</div>
<script type="text/javascript">new AdMove("ad1").Run();</script>  
</body>
</html>