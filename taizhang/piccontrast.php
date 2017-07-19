<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}

$pics = empty($_POST['pics']) ? 1 : trim($_POST['pics']);
$pics = str_replace('\"', '"', $pics);
$data = json_decode($pics, true);

$len = sizeof($data);
if($len == 1){
	$style="width:100%";
}else if($len == 2){
	$style="width: 50%;";
}else if($len >= 3){
	$style="width: 33%;";
}
?>
<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>工作完成情况图片对比</title>
	<script type="text/javascript" src="../js/jquery.min.js"></script>
	<script type="text/javascript" src="../js/layer/layer.js" ></script>
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
			<?=$style?>;
			float: left;
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
			/*color:#6c6c6c;*/
			max-height:40px;
			text-align:left;
			overflow:hidden;
		}
	</style>
</head>
<body class="main">
	<div class="result">
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
				<p class="time"><?=$row['date']?></p>
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
	//var height = $(window).height();
	var pic_count = <?=$len?>;
	var ulObj = $("div.result>ul");
	var imgObj = $("div.result>ul>li>img");
	var pnoteObj = $("div.result>ul>li>p.note");
	var ptimeObj = $("div.result>ul>li>p.time");
	var resultObj = $("div.result");
	
	var nTotalHeight = $(window).height();
	var nTotalWidth = resultObj.width();
	resultObj.height(nTotalHeight);
	//var nHeight = nTotalHeight - 55;
	var nHeight = nTotalHeight;
	var halfHeight = parseInt(nHeight/2)-5;
	var pHeight = ptimeObj.height();
	ulObj.height(nHeight);
	
	if(pic_count > 3){
		imgObj.height(halfHeight-pHeight-40 - 20);
		imgObj.width(nTotalWidth/3-100);
		pnoteObj.width(imgObj.width());
		var left_width = (nTotalWidth*0.99/3 - imgObj.width())/2;
		pnoteObj.css("margin-left", left_width);		
	}
	else if(pic_count == 3){
		imgObj.width(nTotalWidth/3-100);
		imgObj.height(imgObj.width());
		pnoteObj.width(imgObj.width());
		var left_width = (nTotalWidth*0.99/3 - imgObj.width())/2;
		pnoteObj.css("margin-left", left_width);	
		var top_ = (ulObj.height() - imgObj.height() - pHeight - 40)/2; 
		ulObj.css('padding-top', top_);
	}
	else if(pic_count == 2){
		imgObj.width(nTotalWidth/2-100);
		imgObj.height(imgObj.width()-100);
		pnoteObj.width(imgObj.width());
		var left_width = (nTotalWidth/2 - imgObj.width())/2;
		pnoteObj.css("margin-left", left_width);
		var top_ = (ulObj.height() - imgObj.height() - pHeight - 40)/2; 
		ulObj.css('padding-top', top_);
	}
	else if(pic_count == 1){
		imgObj.height(nHeight-pHeight-40-10);
		pnoteObj.width(imgObj.width());
		var left_width = (nTotalWidth - imgObj.width())/2;
		pnoteObj.css("margin-left", left_width);
	}
}
</script>