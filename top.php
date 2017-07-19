<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>无标题文档</title>
<link href="css/default.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
</style>

<script language="JavaScript" type="text/JavaScript">
function shouye() {
  window.open('default.php',"_top");
}
function zhuxiao() {
	//window.location.href = "logout.php";
	window.open('logout.php',"_top");
}
</script>
</head>

<body class="top">
<table width="100%" height="103" border="0" cellpadding="0" cellspacing="5" class="top" background="url(img/flash/top_manage.swf)">
	<input type="hidden" name="changvalue" value="0">
    <tr style="height:100px;line-height:100px;">
		<td width="86%"></td>
		<td width="6%" align="right" class="ys" valign="top" style="vertical-align:middle;">
			<strong><a href="javascript:void(0)" onClick="shouye()"><img src='img/shouye.png' border="0" /></a></strong>
		</td>
		<td width="8%" class="ys"  valign="top" style="vertical-align:middle;">
			<strong><a href="javascript:void(0)" onClick="zhuxiao()"><img src='img/zhuxiao.png' border="0"/></a></strong>
		</td>	 	
	 </tr>
</table>

</body>
</html>
