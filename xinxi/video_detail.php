<?php
$video = isset($_GET['video']) ? $_GET['video'] : ""; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<title>在线视频</title>
<link rel="stylesheet" type="text/css" href="../css/common.css" />
<style>
td{text-align:center;}
video{width:100%;height:60%;max-height:700px;z-index:999999999;}
.main{
	z-index:999999998;	
}
</style>
</head>
<body class="main">
<div style="height: 10px;"></div>
<div id="search">
	<table border="0" cellpadding="4" cellspacing="1" class="table01">
		<tr><td colspan="7" class="table_title">在线视频</td></tr>
		<tr>
			<td>
				<video src="../img/video/<?php echo $video; ?>" controls="controls" preload="preload" autoplay="autoplay">您的浏览器不支持此视频！</video>
			</td>
		</tr>                   
	</table>
</div>
</body>
</html>