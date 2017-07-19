<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}

$pics = empty($_POST['pics']) ? 1 : trim($_POST['pics']);
$pics = str_replace('\"', '"', $pics);
$data = json_decode($pics, true);

?>
<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>南阳市政务督查管理系统</title>
	<script type="text/javascript" src="../js/jquery.min.js"></script>
	<style type="text/css">
		ul{
			margin: 5px auto;
			padding: 0px 0px;
			list-style: none;
			text-align:center;
			width:100%;
			overflow:hidden;
		}
		ul li{
			width:100%;
			text-align: center;
		}
		ul li img{
			border:10px solid rgba(100, 100, 100, 200);
		}
		ul li img:hover{
			border:10px solid #86bcea;
		}
		ul li p{
			line-height: 22px;
			margin: 0px;
			padding: 0px;
			font-size: 14px;
			text-align:center;
		}
		body.main{
			background-color: rgba(50, 50, 50, 200);
			overflow:hidden;
			margin:0px 0px;
			padding:0px 0px;
			color:#fff;
		}
		div.result{
			width: 100%;
			height: 100%;
			text-align: center;
		}
		.note{
			line-height: 140%;
			max-height:40px;
			text-align:left;
			overflow:hidden;
		}		
	</style>
</head>
<body class="main">
	<div class="content">
		<ul>			
		<?php
			foreach($data as $row){
		?>
			<li>
				<img src="<?=$row['url']?>" title="<?=$row['note']?>">
				<p class="note">&emsp;
				<?php 
					if(mb_strlen($row['note'],'utf-8') > 72) {
						echo mb_substr($row['note'],0,72,'utf-8') . "...";
					}else{
						echo $row['note'];
					}
				?>
				</p>
				<p ><?=$row['dname']." ".$row['date']?></p>
			</li>
		<?php
			}
		?>
		</ul>
	</div>
</body>
</html>
<script type="text/javascript">
window.onload = function(){
	$(window).resize();
}

window.onresize = function(){
	var contentObj = $("div.content");
	
	var nTotalHeight = $(window).height();
	var nTotalWidth = contentObj.width();
	var nHeight = nTotalHeight-10;
	
	var img_obj = $("div.content>ul>li>img");
	var p_obj = img_obj.next().next();
	
	var pHeight = p_obj.height();
	img_obj.height(nHeight-pHeight - 40);
	$("p.note").width(img_obj.width());
	var left_width = (nTotalWidth - img_obj.width())/2;
	$("p.note").css("margin-left", left_width);
}
</script>