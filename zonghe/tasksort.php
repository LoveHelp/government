<?php
$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 1;
$setTime1 = isset($_POST['setTime1']) ? $_POST['setTime1'] : "";
$setTime2 = isset($_POST['setTime2']) ? $_POST['setTime2'] : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
	<meta http-equiv="expires" content="0">
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>督查上报</title>
	<script type="text/javascript" src="../js/jquery.min.js"></script>
	<link rel="stylesheet" href="../css/default.css" />
	<link rel="stylesheet" href="../css/common.css" />
	<script type="text/javascript" src="../js/calendar/WdatePicker.js"></script>
	<link href="../js/calendar/skin/WdatePicker.css" rel="stylesheet" type="text/css">
	<style>
	.main{text-align:center;}
	div.menu ul li{margin-right:20px;}
	div.menu ul li.last_li{margin-right:20px;}
	table{width:70%;margin-bottom:100px;text-align:center;}
	body.main{background:#FFF;}
	A:link, A:visited, A:active{color:#000;}
	</style>
</head>
<body class="main">
	<div class="title">综合排名</div>
	<div style="height: 10px;"></div>
	<div class="menu">
		<ul>
			<a href="tasksort.php"><li class="last_li current">总排名</li></a>
			<a href="worksort.php"><li class="last_li">按工作排名</li></a>
			<a href="typesort.php"><li class="last_li">按工作类型排名</li></a>
			<a href="unitsort.php"><li class="last_li">按单位排名</li></a>
		</ul>
	</div>
	<div style="height: 30px; line-height:30px; text-align:right;width:70%;margin:0 auto;clear: both;color:red;">*牵头单位每项工作计1分,责任单位计0.5分</div>

	<div id="list1" class="list current">
		<form action="tasksort.php" method="post">
			<input type="hidden" name="sort" id="sort" value="<?php echo $sort; ?>" />
			<table style="margin-bottom:10px;" cellspacing="1" cellpadding="6" align="center">
				<tr>
					<td height="20" class="td_title">时间</td>
					<td class="td_content" style="text-align:left;">
						<input type="text" name="setTime1" id="setTime1" maxlength="" size="15" value="<?php echo $setTime1; ?>" onfocus="WdatePicker()" readonly="readonly" class="input" />
						至<input type="text" name="setTime2" id="setTime2" maxlength="" size="15" value="<?php echo $setTime2; ?>" onfocus="WdatePicker()" readonly="readonly" class="input" />
					</td>
					<td height="20" class="td_title"><input value="查 询" style="cursor:pointer" class="button1" type="submit" /></td>
				</tr>
			</table>
		</form>
		<table cellspacing="1" cellpadding="6" align="center" >
			<tr>
				<td class="table_title" width="10%" height="100%">排名</td>
				<td class="table_title" width="25%" height="100%">责任单位</td>
				<td class="table_title" width="15%" height="100%">任务数</td>
				<td class="table_title" width="15%" height="100%">未完成</td>
				<td class="table_title" width="15%" height="100%">完成</td>
				<td class="table_title" width="20%" height="100%" onclick="change_sort();">完成率<?php if($sort == 1) echo "&#8593;"; else echo "&#8595;"?></td>
			</tr>
			<?php
			include "pager.php";
			$page = 1;
			$html = get_task_sort($page, $sort, $setTime1, $setTime2);
			echo $html;
			?>
	</table></div>
</body>
</html>
<script type="text/javascript">
/*var page = 1;

$(window).scroll(function () {
	//滚动条距离顶部距离
	var scrollTop = $(this).scrollTop();
	var windowHeight = $(window).height();
	//内容总高度
	var documentHeight = $(document).height();
	if (scrollTop + windowHeight == documentHeight) {
		page++;
		load_task();
	}
});
function load_task(){
	$.ajax({
		url:"pager.php?do=tasksort&page="+page,	
		success:function(result){
			var html = result;
			$("table tr:last").after(html);
		}
	}); 
}*/
function change_sort(){
	<?php
		if($sort == 1){
			$sort = 0;
		}else{
			$sort = 1;
		}
	?>
	window.location.href = "tasksort.php?sort=<?php echo $sort; ?>";
}
</script>
