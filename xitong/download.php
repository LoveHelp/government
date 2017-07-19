<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:index.php');
	exit;
}
function getfiles($path){ 
$filelist=array();
foreach(scandir($path) as $afile)
	{
	if($afile=='.'||$afile=='..') continue; 
		if(is_dir($path.'/'.$afile)) 
		{ 
		getfiles($path.'/'.$afile); 
		} else { 
		$filelist[]=iconv('gbk','utf-8',$afile); 
		} 
	}
	return $filelist;
} 
?>
<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>软件下载</title>
	<script type="text/javascript" src="../js/jquery.min.js" ></script>
	<script type="text/javascript" src="../js/layer/layer.js" ></script>
	<link rel="stylesheet" href="../css/common.css" />
	<style type="text/css">
		tr {
			line-height: 32px;
			height:32px;
		}
		select {
			height: 25px;
			width: 154px;
		}
		div#result table tbody tr{
			cursor: pointer;		
		}
		div#result table td{
			text-align: center;
		}
	</style>
</head>
<body class="main">
	<!--<html:hidden property="processFlag" />-->
	<div id="search">
		<input type="hidden" name="hd_uid" value="" />
		<form action="contacts.php" method="post">
			<table border="0" cellpadding="6" cellspacing="1" class="tab">
				<tr>
					<td colspan="5" class="table_title">
						软件下载
					</td>
				</tr>
			</table>
		</form>
	</div>
				<ul width="100%">
				<?php
				$filelist=getfiles('download');
				foreach($filelist as $f)
				{
					echo '<li style="width:120px;float:left;align:center">','<a href="','download/'.$f,'"><img src="../img/down.jpg" />','<p style="width:80px;align:center">',$f,'</p></a></li>';
				}
				?>
				</ul>
</body>
</html>