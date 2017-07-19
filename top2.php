<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:index.php');
	exit;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>无标题文档</title>
<link href="css/default2.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="js/jquery.min.js"></script>
<style>
.ys a{
    text-decoration:none;
	color:#FFFFFF;
}
.ys a:hover{
    text-decoration:none;
	color: #FFFF00;
}
.nowtime{
	float:left;
	color: #ccc;
	padding-left:38px
}
.a{
	color:#ccc;
}
.title_img{
	vertical-align:middle;
	float:left;
	width:auto;
	height:78px;
	margin-right:10px;
}
.title_img2{
	vertical-align:middle;
	float:left;
	width:auto;
	height:auto;
	margin:13px 5px;
	display:none;
}
.title_l{
	margin-left:16%;
	overflow:hidden;
	float:left;
	width:84%;
}
.welcome{
	width:100%;
	text-align:center;
	font-family:"微软雅黑";
	color:#FFF;
	font-size:20px;
	font-weight:bold;
}
#change_version{
	position:fixed;
	top:10px;
	right:10px;
	font-family:"微软雅黑";
}
@media screen and (min-width:1401px) and (max-width: 1600px) { 
	.welcome{
		font-size:18px;
	}
	.title_img{
		height:70px;
		width:auto;
	}
	.title_img2{
		height:48px;
		width:auto;
	}
}
@media screen and (min-width:1281px) and (max-width: 1400px) { 
	.welcome{
		font-size:16px;
	}
	.title_img{
		height:64px;
		width:auto;
	}
	.title_img2{
		height:40px;
		width:auto;
	}
	.ys img{
		height:30px;
	}
	.line{
		height:32px;
	}
}
@media screen and (max-width: 1280px) {
	.welcome{
		font-size:14px;
	}
	.title_img{
		height:52px;
		width:auto;
	}
	.title_img2{
		height:36px;
		width:auto;
	}
	.ys img{
		height:26px;
	}
	.line{
		height:30px;
	}
}
</style>

<script language="JavaScript" type="text/JavaScript">
var version = 1;
function changeVersion(){
	window.open('default2.php',"_top");
}
function shouye() {
  window.open('default.php',"_top");
}
function zhuxiao() {
	//window.location.href = "logout.php";
	window.open('logout.php',"_top");
}
$(function(){
    startRequest();
});
var i = 11;
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
</script>
</head>

<body class="top">
<input value="旧 版" style="cursor:hand" type="button" id="change_version" onclick="changeVersion();" />
<table width="100%" height="103" border="0" cellpadding="0" cellspacing="5" class="top" background="">
	<input type="hidden" name="changvalue" value="0">
    <tr style="height:125px;line-height:125px;">
		<td width="70%">
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
		</div>
		</td>
		<td width="14%" align="right" class="ys" valign="top" style="vertical-align:middle;">
			<strong><span class="welcome">您好!<?=$_SESSION['userDeptName']?><?=$_SESSION['userCode']?></span></strong>
		</td>
		<td width="1%" align="center"><img class="line" src="img/shouye/line.png"></td>
		<td width="6%" align="right" class="ys" valign="top" style="vertical-align:middle;">
			<strong><a href="javascript:void(0)" onClick="shouye()"><img class="sy" src="img/shouye/sy.png"></a></strong>
		</td>
		<td width="1%" align="center"><img class="line" src="img/shouye/line.png"></td>
		<td width="8%" align="left" class="ys" valign="top" style="vertical-align:middle;">
			<strong><a href="javascript:void(0)" onClick="zhuxiao()"><img class="logout" src="img/shouye/logout.png"></a></strong>
		</td>	 	
	 </tr>
</table>

</body>
</html>
