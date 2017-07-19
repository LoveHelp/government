<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}

include("taizhang.php");
$itemtype = isset($_POST['itemtype']) ? $_POST['itemtype'] : "";
$itemcomment = isset($_POST['itemcomment']) ? $_POST['itemcomment'] : "";
$itemstatus = isset($_REQUEST['itemstatus']) ? $_REQUEST['itemstatus'] : "";
$itemtarget = isset($_POST['itemtarget']) ? $_POST['itemtarget'] : "";
$time = date("Y-m-d H:i:s");
$username = $_SESSION['userName'];
$deptid = isset($_SESSION['userDeptID']) ? $_SESSION['userDeptID'] : "";
$where = '';
//台账类型
if($itemtype != ""){
	$where .= " and a.type = " . $itemtype;
}

//总体评价
if($itemcomment != ""){
	$where .= " and b.isover = " . $itemcomment;
}

//办结状态
if($itemstatus != ""){
	if($itemstatus == 2){
		$where .= " and a.status = 5";
	}else{
		$where .= " and a.status < 5";
	}
}

//工作目标
if($itemtarget != ""){
	$where .= " and a.target like \"%" . $itemtarget . "%\"";
}

$generaltaskid = isset($_REQUEST['generaltaskid']) ? $_REQUEST['generaltaskid'] : "";
if($generaltaskid != ""){
	$where .= ' and a.generaltaskid =' . $generaltaskid;
}

$generaltaskList = json_decode(get_general_task(), true);
$total_count = get_task_complete_count($deptid, $where);
$total_page = ceil($total_count/10);
?>
<!DOCTYPE html>
<html>
<head>
<title>任务办结</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--CSS控制文件-->
<link rel="stylesheet" href="../css/common.css" />
<link rel="stylesheet" href="../css/taizhang.css">
<!--常用的javascript文件-->
<script type="text/javascript" src="../js/jquery.min.js" ></script>
<script src="../js/layer/layer.js"></script>
<script type="text/javascript" src="../js/taizhang.js"></script>
<script type="text/javascript" src="../js/jquery-ui/jquery-ui.min.js"></script>
<link href="../js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css">
<script>
$(function() {
    $(".resizable").resizable();
});
var page = 1;
var total = <?php echo $total_page; ?>;
load_task();
$(window).scroll(function () {
	//滚动条距离顶部距离
	var scrollTop = $(this).scrollTop();
	var windowHeight = $(window).height();
	//内容总高度
	var documentHeight = $(document).height();
	if (scrollTop + windowHeight == documentHeight) {
		if(page < total){
			page++;
			load_task();
		}
	}
});
function load_task(){
	var deptid = <?php echo $deptid; ?>;
	var where = '<?php echo $where; ?>';
	$.ajax({
		type:"post",
		url:"taizhang.php?do=task_complete",
		data:{deptid:deptid,page:page,where:where},
		success:function(result){
			var html = result;
			$("#result tr:last").after(html);
			/*if(page == total){
				layer.msg("加载完成...");
			}*/
		}
	}); 
}
var hch = {
	open_apply:function(taskId){
        this.index_type = layer.open({
			type: 2,
            title: $('#complete_layer').attr("title"),
            skin: 'layui-layer-rim', //加上边框
            area: ['800px', '90%'], //宽高
            content: ['layer.php?id='+taskId+"&type=complete", 'yes'],
			/*success: function() {
				hch.auto_height();
			},*/
			end: function(){
			  window.location.reload();
			}
         });
		
		$(".layui-layer-rim").css("background", "#ECF6FB");
	},
	open_sms:function(){
		layer.open({
			type:2,
			title:'短信提醒',
			skin: 'layui-layer-rim', //加上边框
			area: ['80%', '80%'], //宽高
			offset:'10%',
			content: "../sendsms.php"
		});
	},
	auto_height : function(){
		layer.iframeAuto(this.index_type);
	}
}
</script>
</head>
<body class="main">
	<div id="search">
		<form name="queryform" method="post" action="taskcomplete.php" id="queryform">
			<table width="100%" cellpadding="4" cellspacing="1" class="table01">
				<tr>
					<td height="25" colspan="4" class="table_title">任务办结</td>
				</tr>
				<tr>
					<td width="15%" class="td_title">台账类型</td>
					<td width="35%" class="td_content">
						<select name="itemtype" id="itemtype" class="select" style="width:120px;">
							<option value=""></option>
							<?php
								foreach($task_type as $key=>$value){
									if($key == $itemtype){
										echo '<option value="' . $key . '" selected>' . $value . '</option>';
									}else{
										echo '<option value="' . $key . '">' . $value . '</option>';
									}
									
								}
							?>
						</select>
					</td>
					<td width="15%" class="td_title">总体任务</td>
					<td class="td_content">
						<select name="generaltaskid" id="generaltaskid" class="select" style="width:320px;">
						<option value=""></option>
						<?php
							if(is_array($generaltaskList) && count($generaltaskList) > 0){
								foreach($generaltaskList as $g){
									if($generaltaskid == $g['id']){
										echo '<option value="' . $g['id'] . '" selected>' . $g['name'] . '</option>';
									}else{
										echo '<option value="' . $g['id'] . '">' . $g['name'] . '</option>';
									}
								}
							}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td width="15%" class="td_title">总体评价</td>
					<td width="30%" class="td_content">
						<select name="itemcomment" id="itemcomment" class="select" style="width:80px;">
							<option value="" selected></option>
							<?php
							foreach($comment_type as $key=>$v){
								if($key == $itemcomment){
									echo '<option value="' . $key . '" selected>' . $v . '</option>';
								}else{
									echo '<option value="' . $key . '">' . $v . '</option>';
								}
							}
							?>
						</select>
					</td>
					<td width="15%" class="td_title">办结状态</td>
					<td width="35%" class="td_content">
						<select name="itemstatus" id="itemstatus" class="select" style="width:80px;">
							<option value=""></option>
							<option value="1" <?php if($itemstatus == 1) echo 'selected="selected"'; ?>>未办结</option>
							<option value="2" <?php if($itemstatus == 2) echo 'selected="selected"'; ?>>已办结</option>
						</select>
					</td>
				</tr>	
				<tr>
					<td width="15%" class="td_title">工作目标</td>
					<td width="35%" class="td_content" colspan="3">
						<input type="text" name="itemtarget" id="itemtarget" value="<?php echo $itemtarget; ?>" class="input" style="width:317px;">
					</td>
				</tr>
				<tr>    
				  <td colspan="4" class="td_title">
					<input type="submit" value="查 询" style="cursor:hand" class="button1">&nbsp;    
					<input name="button4" type="button" class="button1" style="cursor:hand" onclick="queryReport_reset('complete');return false;" value="重 置">
					<input type="button" value="短信提醒" class="button1" onclick="javascript:hch.open_sms();">
				   </td>
				</tr>
			</table>
		</form>
	</div>
	<div style="height:10px;"></div>
	<div id="result">
		<table align="center" cellpadding="6" cellspacing="1" class="table01" width="100%">	
			<tbody>
				<tr>
					<td rowspan="2" width="3%" height="100%" class="table_title">序<br />号</td>
					<td rowspan="2" width="10%" height="100%" class="table_title">工作目标</td>
					<td rowspan="2" width="10%" height="100%" class="table_title">支撑项目</td>
					<td colspan="2" width="22%" height="100%" class="table_title">工作标准</td>
					<td colspan="2" width="10%" height="100%" class="table_title">时间节点</td>
					<td rowspan="2" width="17%" height="100%" class="table_title">责任主体</td>
					<td rowspan="2" width="10%" height="100%" class="table_title">最新进展</td>
					<td rowspan="2" width="6%" height="100%" class="table_title">总体评价</td>
					<td rowspan="2" width="6%" height="100%" class="table_title">是否办结</td>
					<td rowspan="2" width="6%" height="100%" class="table_title">操作</td>
				</tr>
				<tr>
					<td width="4%" height="100%" class="table_title">投资<br />（元）</td>
					<td width="18%" height="100%" class="table_title">工作标准</td>
					<td width="5%" height="100%" class="table_title">启动<br />时间</td>
					<td width="5%" height="100%" class="table_title">完成<br />时间</td>
				</tr>
			</tbody>
		</table>
	</div>
</body>
</html>
