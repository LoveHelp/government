<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
include("taizhang.php");
$type = isset($_POST['itemtype']) ? $_POST['itemtype'] : 0;
$time = date("Y-m-d H:i:s");
$username = isset($_SESSION['userName']) ? $_SESSION['userName'] : "";
$deptid = isset($_SESSION['userDeptID']) ? $_SESSION['userDeptID'] : "";
$i = 0;
$where = '';
$itemtype="";
$complete="";
if(isset($_POST['itemtype']) && $_POST['itemtype'] != ""){
	$itemtype = $_POST['itemtype'];
	$where .= ' and a.type = ' . $itemtype;	
}
if(isset($_POST['is_complete']) && $_POST['is_complete'] != ""){
	$complete = $_POST['is_complete'];
	$where .= ' and b.is_complete = ' . $complete;
}
$setTime1 = isset($_POST['setTime1']) ? $_POST['setTime1'] : "";
if($setTime1 != ""){
	$where .= ' and b.recvdate >= "' . $setTime1 . '"';
}
$setTime2 = isset($_POST['setTime2']) ? $_POST['setTime2'] : "";
if($setTime2 != ""){
	$where .= ' and b.recvdate <= "' . $setTime2 . '"';
}
$itemtarget = isset($_POST['itemtarget']) ? $_POST['itemtarget'] : "";
if($itemtarget != ""){
	$where .= ' and a.target like "%' . $itemtarget . '%"';
}
$generaltaskid = isset($_REQUEST['generaltaskid']) ? $_REQUEST['generaltaskid'] : "";
if($generaltaskid != ""){
	$where .= ' and a.generaltaskid =' . $generaltaskid;
}
$generaltaskList = json_decode(get_general_task(), true);
$total_count = get_task_apply_count($deptid, $where);
$total_page = ceil($total_count/10);
?>
<!DOCTYPE html>
<html>
<head>
<title>办结申请</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--CSS控制文件-->
<link rel="stylesheet" href="../css/common.css" />
<link rel="stylesheet" href="../css/taizhang.css">
<!--常用的javascript文件-->
<script src="../js/jquery.min.js"></script>
<script src="../js/layer/layer.js"></script>
<script type="text/javascript" src="../js/taizhang.js"></script>
<script type="text/javascript" src="../js/calendar/WdatePicker.js"></script>
<link href="../js/calendar/skin/WdatePicker.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/jquery-ui/jquery-ui.min.js"></script>
<link href="../js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css">
<style type="text/css">
input.input{width:177px;}
</style>
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
		url:"taizhang.php?do=task_apply",
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
		var url = 
        this.index_type = layer.open({
			type: 2,
            title: $('#apply_layer').attr("title"),
            skin: 'layui-layer-rim', //加上边框
            area: ['600px', 'auto'], //宽高
            content: ['layer.php?id='+taskId+"&type=apply", 'no'],
			success: function() {
				hch.auto_height();
			},
			end: function(){
			  window.location.reload();
			}
         });
		$(".layui-layer-rim").css("top", "120px");
	},
	auto_height : function(){
		layer.iframeAuto(this.index_type);
	}
}
</script>
</head>
<body class="main">
	<div id="search">
		<form name="actionform" method="post" action="taskapply.php">
			<table width="100%" cellpadding="4" cellspacing="1" class="table01">
				<tr>
				  <td height="25" colspan="4" class="table_title">办结申请</td>
				</tr>
				<tr>
					<td class="td_title">台账类型</td>
					<td class="td_content" style="width:330px;">
						<select name="itemtype" id="itemtype" class="select">
						<option value=""></option>
						<?php
							foreach($task_type as $key=>$value){
								if($itemtype == $key){
									echo '<option value="' . $key . '" selected>' . $value . '</option>';
								}else{
									echo '<option value="' . $key . '">' . $value . '</option>';
								}	
							}
						?>
						</select>
					</td>
					<td class="td_title">总体任务</td>
					<td class="td_content" style="width:330px;">
						<select name="generaltaskid" id="generaltaskid" class="select" style="width:330px;">
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
					<td height="20" class="td_title">是否办结</td>
					<td class="td_content">
						<select name="is_complete" size="1" id="is_complete" class="select">
							<option value="" selected></option>
							<?php
							foreach($complete_arr  as $key=>$v){
								if($complete == $key){
									echo '<option value="' . $key . '" selected>' . $v . '</option>';
								}else{
									echo '<option value="' . $key . '">' . $v . '</option>';
								}
								
							}
							?>
						</select>
					</td>
					<td height="20" class="td_title">接收时间</td>
					<td class="td_content">
						<input type="text" name="setTime1" id="setTime1" maxlength="" size="15" value="<?php echo $setTime1; ?>" onfocus="WdatePicker()" readonly="readonly" class="input" />
						至<input type="text" name="setTime2" id="setTime2" maxlength="" size="15" value="<?php echo $setTime2; ?>" onfocus="WdatePicker()" readonly="readonly" class="input" />
					</td>
				</tr>
				<tr>
					<td class="td_title">工作目标</td>
					<td class="td_content" colspan="3">
						<input type="text" id="itemtarget" name="itemtarget" value="<?php echo $itemtarget; ?>" class="input" style="width:317px;">
					</td>
				</tr>
				<tr>
				  <td colspan="4" class="td_title">
					<input type="submit" value="查 询" style="cursor:hand" class="button1">
					<input name="button4" type="button" class="button1" style="cursor:hand" onclick="queryReport_reset('apply');return false;" value="重 置">
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
					<th rowspan="2" width="3%" height="100%" class="table_title">序<br />号</th>
					<th rowspan="2" width="10%" height="100%" class="table_title">工作目标</th>
					<th rowspan="2" width="10%" height="100%" class="table_title">支撑项目</th>
					<th colspan="2" width="25%" height="100%" class="table_title">工作标准</th>
					<th colspan="2" width="12%" height="100%" class="table_title">时间节点</th>
					<th rowspan="2" width="8%" height="100%" class="table_title">反馈时间节点</th>
					<th rowspan="2" width="8%" height="100%" class="table_title">要求办结时间</th>
					<th rowspan="2" width="8%" height="100%" class="table_title">接收时间</th>
					<th rowspan="2" width="8%" height="100%" class="table_title">是否办结</th>
					<th rowspan="2" width="8%" height="100%" class="table_title">操作</th>
				</tr>
				<tr>
					<th width="4%" height="100%" class="table_title">投资<br />（元）</th>
					<th width="21%" height="100%" class="table_title">工作标准</th>
					<th width="6%" height="100%" class="table_title">启动时间</th>
					<th width="6%" height="100%" class="table_title">完成时间</th>
				</tr>				
			</tbody>
		</table>
	</div>
</body>
</html>
