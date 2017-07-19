<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:index.php');
	exit;
}
/* 日期XXXX年XX月XX日 星期X */
$weekarray=array("日","一","二","三","四","五","六");
$time = date("Y年n月j日", time()) . "星期" . $weekarray[date("w")];

include_once "shouye.php";
$roleid = isset($_SESSION['userRoleID']) ? $_SESSION['userRoleID'] : "";

$menuList = json_decode(get_menu_list($roleid), true);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<title>南阳市政务督查管理系统</title>
<script type="text/javascript" src="js/jquery.min.js"></script>
<link rel="stylesheet" href="css/shouye.css" type="text/css">
<style>
#change_version{
	position:fixed;
	top:10px;
	right:10px;
}
</style>
<script type="text/javascript">
function changeVersion(){
	window.open('default.php',"_top");
}
function changeFrameHeight(){
    var ifm = $("#mainFrame");
	
	var _width = document.body.clientWidth;
	//$(".bottom").height();

	if(_width <= 1600 && _width > 1280){
		var _height = window.innerHeight - 105;
		ifm.height(_height + "px");
		if(screen == false){
			ifm.width(_width*0.94 - 224 + "px");
		}else{
			ifm.width(_width*0.94 - 10 + "px");
		}
	}else if(_width <= 1280){
		var _height = window.innerHeight - 95;
		ifm.height(_height + "px");
		if(screen == false){
			ifm.width(_width*0.94 - 174 + "px");
		}else{
			ifm.width(_width*0.94 - 10 + "px");
		}
	}else if(_width > 1600){
		var _height = window.innerHeight - 125;
		ifm.height(_height + "px");
		if(screen == false){
			ifm.width(_width*0.94 - 224 + "px");
		}else{
			ifm.width(_width*0.94 - 10 + "px");
		}
	}

	$("#left").height(_height);
}
window.onresize=function(){  
     changeFrameHeight();  
}
$(function(){
	changeFrameHeight();
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
	
	show_img();
	
});
function find()
{
	t=$("#find").val();
	window.open("zonghe/taskquery.php?target="+t);
}
	var i = 11;
function show_img(){
    startRequest();
}
function startRequest(){
	$(".title_img2").css("display", "none");
	iCount = setInterval(function(){
        if(!i){
			clearInterval(iCount);
			i = 11;
			setTimeout("startRequest()", 2000);
		}else{
			var count = 11 - i + 1;
			var id = "d_" + count;
			$("#" + id).css("display", "block");
			i--;
		}
    },250);
}
var screen=false,leftWitdh,rightWidth;
function shiftwindow(){
	if(screen==false){
		leftWitdh = $("#left").width();
		rightWidth = $("#mainFrame").width();
		$("#left").width(0);
		$("#mainFrame").width(leftWitdh + rightWidth);
		ShiftButton.src='img/BtnShiftMenu22.gif';
		screen=true;
	}else if(screen==true){
		$("#left").width(leftWitdh);
		$("#mainFrame").width(rightWidth);
		ShiftButton.src='img/BtnShiftMenu11.gif';
		screen=false;
	}
}
</script>
</head>
<body class="frame">
<input value="新 版" style="cursor:hand" type="button" id="change_version" onclick="changeVersion();" />
<!-- top -->
<div class="title" id="title">
	<div class="title_l">
		<img class="title_img" src="img/shouye/1-0.png" />
		<img class="title_img2" id="d_1" src="img/shouye/1-1.png" />
		<img class="title_img2" id="d_2" src="img/shouye/1-2.png" />
		<img class="title_img2" id="d_3" src="img/shouye/1-3.png" />
		<img class="title_img2" id="d_4" src="img/shouye/1-4.png" />
		<img class="title_img2" id="d_5" src="img/shouye/1-5.png" />
		<img class="title_img2" id="d_6" src="img/shouye/1-6.png" />
		<img class="title_img2" id="d_7" src="img/shouye/1-7.png" />
		<img class="title_img2" id="d_8" src="img/shouye/1-8.png" />
		<img class="title_img2" id="d_9" src="img/shouye/1-9.png" />
		<img class="title_img2" id="d_10" src="img/shouye/1-10.png" />
		<img class="title_img2" id="d_11" src="img/shouye/1-11.png" />
	</div>
	<div class="right">
		<li style="font-family:微软雅黑;"><span style="width:100%;text-align:center">您好!<?=$_SESSION['userDeptName']?><?=$_SESSION['userCode']?></span>
		<!--全站搜索 <input type="text" id="find"/>  <input type="button" onclick="find()" value="搜索" style="cursor:pointer"/>-->
		</li>
		<li><img class="line" src="img/shouye/line.png" /></li>
		<li><a href="main2.php" target="mainFrame"><img class="sy" src="img/shouye/sy.png" /></a></li>
		<li><img class="line" src="img/shouye/line.png" /></li>
		<li><a href="logout.php"><img class="logout" src="img/shouye/logout.png" /></a></li>
	</div>
</div>
<div class="bottom">
	<!-- left -->
	<div class="left" id="left">
		<!-- 显示你的收藏夹内容代码开始 -->
		<div class="favMenu" id="aMenu" style="margin: 0px;"> 
		<?php
			if(is_array($menuList) && count($menuList) > 0){
				$i = 0;
				foreach($menuList as $menu){
					$i++;
					if($i == 1){
						$class = "MenuBg1Down";
						$display = "display:block;";
					}else{
						$class = "MenuBg1";
						$display = "display:none;";
					}
					
					if(is_array($menu['sub']) && count($menu['sub']) > 0){
						// 主菜单
						echo '<div class="' . $class . '" id="Menu' . $i . '"><img src="' . $menu['icon'] . '" /></div>'
							. '<div class="sub" id="Menu' . $i . 'Sub" style="' . $display .'">';
						foreach($menu['sub'] as $sub){
							// 子菜单 
							echo '<div class="subItem"><a href="' . $sub['menuurl'] . '"  target="mainFrame">' . $sub['name'] . '</a></div>';
						}
						echo '</div>';
					}
					
				}
			}
		?>
		</div><!-- for favMenu-->
	<!-- 代码结束 -->
	</div>
	<!--middle-->
	<div class="middle">
		<table width="8" border="0" cellpadding="0" cellspacing="0" height="100%">
			<tbody>
			<tr>
				<td>
					<img src="img/BtnShiftMenu11.gif" name="Image34" id="ShiftButton" width="9" height="73" border="0" style="cursor:pointer" onclick="shiftwindow()">
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	<!-- main -->
	<div class="main">
		<iframe src="main2.php" frameborder="no" border="0" name="mainFrame" id="mainFrame" width="100%" height="100%" onload="changeFrameHeight();"/>
	</div>
</div>
</body>
</html>