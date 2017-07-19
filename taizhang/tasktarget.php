<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
include("target.php");
$username = isset($_SESSION['userName']) ? $_SESSION['userName'] : "";
$itemtype = isset($_POST['itemtype']) ? $_POST['itemtype'] : "";
$itemgeneraltask = isset($_POST['itemgeneraltask']) ? $_POST['itemgeneraltask'] : "";
$itemtarget = isset($_POST['itemtarget']) ? $_POST['itemtarget'] : "";
$is_turn = isset($_POST['is_turn']) ? $_POST['is_turn'] : 1;
$is_task = isset($_POST['is_task']) ? $_POST['is_task'] : 0;
$resArr = json_decode(get_all_generaltask_and_target($itemtype, $itemgeneraltask, $itemtarget, $is_turn, $is_task), true);
$generaltaskList = json_decode(get_all_generaltask(), true);
?>
<!doctype html>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<!--CSS控制文件-->
<link rel="stylesheet" href="../css/style.css?v=1">
<link rel="stylesheet" href="../css/common.css" />
<link rel="stylesheet" href="../css/taizhang.css">
<!--常用的javascript文件-->
<!--<script src="../js/jquery-1.8.2.min.js"></script>-->
<script type="text/javascript" src="../js/jquery.min.js" ></script>
<script type="text/javascript" src="../js/layer/layer.js" ></script>
<script type="text/javascript" src="../js/taizhang.js"></script>
<script type="text/javascript" src="../js/ajaxfileupload.js"></script>
<script type='text/javascript' src="../js/calendar/calendar.js" ></script>
<script type='text/javascript' src='../js/calendar/WdatePicker.js'></script>
<script>
var lh = {
	open_target : function(){
		this.index = layer.open({
			type: 1,
			title: '添加工作目标',
			area: ['auto', 'auto'],
			skin: 'layui-layer-rim', //加上边框
			fixed: false,
			maxmin: false,
			//offset: '150px',
			content: $("#add_target")
		});
	},
	do_submit : function(){
		var target = $("#target").val();
		var generaltaskid = $("#generaltask").val();
		var tasktype = $("#tasktype").val();
		var fromdate = $("#fromdate").val();
		var handledate = $("#handledate").val();
		var modperson = '<?php echo $username; ?>';
		if(fromdate == ""){
			layer.msg("启动时间不能为空！");
		}else if(handledate == ""){
			layer.msg("完成时限不能为空！");
		}else if(target == ""){
			layer.msg("工作目标不能为空！");
		}else{
			$.ajax({
				type : 'post',
				url : 'target.php?do=add_target',
				data : {generaltaskid:generaltaskid, type:tasktype, target:target, fromdate:fromdate, handledate:handledate, modperson:modperson},
				dataType : 'text',
				success:function(result){
					if(result == "fail"){
						layer.msg("添加工作目标失败！");
						$("#target").focus();
					}else if(result == "exist"){
						layer.msg("工作目标已存在！");
					}else{
						lh.close();
						window.location.reload();
					}
				}
			});
		}
	},
	close : function(){
		layer.close(this.index);
	}
}
var hch = {
	inInt: function () {
		if (typeof String.prototype.endsWith != 'function') { 
			String.prototype.endsWith = function(suffix) {  
				return this.indexOf(suffix, this.length - suffix.length) !== -1; 
            };
        }
    },
	open_sms:function(targetid){
		layer.open({
			type:2,
			title:'短信提醒',
			skin: 'layui-layer-rim', //加上边框
			area: ['80%', '80%'], //宽高
			content: "../sendsms.php?mt=tasktarget&tid="+targetid
		});
	},
	open_generaltask : function(){
		this.index = layer.open({
			type: 1,
			title: '添加总体任务',
			skin: 'layui-layer-rim', //加上边框
			area: ['400px', '178px'],
			fixed: false,
			maxmin: false,
			//offset: '150px',
			content: $("#add_generaltask")
		});
		$(".layui-layer-content").css("background-color", "#ECF6FB");
	},
	open_delete : function(){
		this.index_del = layer.open({
			type: 1,
			title: '删除总体任务',
			skin: 'layui-layer-rim', //加上边框
			area: ['60%', 'auto'],
			fixed: false,
			maxmin: false,
			//offset: '150px',
			content: $("#del_generaltask")
		});
		$(".layui-layer-content").css("background-color", "#ECF6FB");
	},
	del_generaltask : function(id){
		layer.confirm('确定要删除该条总体任务吗？', {
			btn: ['确定','取消'] //按钮
		}, function(){
			$.ajax({
				type : 'post',
				url : 'target.php?do=del_generaltask',
				data : {id:id},
				dataType : 'text',
				success:function(result){
					alert(result);
					var res = parseInt(result);
					if(res == 2){
						layer.msg("该条总体任务正在使用，删除失败！");
					}else if(res == 1){
						$("#delgeneraltask_"+id).remove();
						layer.msg("删除成功！");
					}else{
						layer.msg("未知错误，删除失败！");
					}
				}
			});
		});
	},
	do_submit : function(){
		var content = $("#content").val();
		if(content == ""){
			layer.msg("总体任务不能为空！");
		}else{
			$.ajax({
				type : 'post',
				url : 'target.php?do=add_generaltask',
				data : {name:content},
				dataType : 'text',
				success:function(result){
					if(result == "fail"){
						layer.msg("总体任务已存在！");
						$("#content").focus();
					}else{
						hch.close();
						window.location.reload();
					}
				}
			});
		}
	},
	close : function(){
		layer.close(this.index);
	},
	open_adopt:function(targetid){
        this.index_adopt = layer.open({
			type: 2,
            title: "台账意见采纳",
			shadeClose: true,
			maxmin: true, //开启最大化最小化按钮
            skin: 'layui-layer-rim', //加上边框
            area: ['80%', '98%'], //宽高
            offset: '1%',
			scrollbar: false,
            content: "taskadopt.php?targetid=" + targetid
		});
	},
	open_dept:function(targetid){
        this.index_dept = layer.open({
			type: 2,
            title: "转办",
            skin: 'layui-layer-rim', //加上边框
            area: ['80%', '95%'], //宽高
            content: "turn.php?targetid="+targetid
		});
            
	   this.bind_dept(targetid);//绑定部门
	},
	open_input:function(){
        this.index_input = layer.open({
			type: 1,
            title: $('#input_task').attr("title"),
            skin: 'layui-layer-rim', //加上边框
            area: ['600px', '300px'], //宽高
			offset: "120px",
            content: $("#input_task")
         });
		$(".layui-layer-rim").css("top", "120px");
		$(".layui-layer-rim").css("background-color", "#DEEFFF");
	},
	input_submit:function(){
        var type = $("#input_type").val();
		$.ajaxFileUpload({
			 	url:'importexcel3.php',
        		type: 'post', 
			 	secureuri:false,
			 	fileElementId:'file',
			 	dataType: 'text',
			 	data:{type:type},
			 	success: function (res){
					hch.close2();
					layer.msg(res);
					setTimeout('refresh()', 3000);
				}
		});
	},
	close2: function () {
		layer.close(this.index_input);
    },
	close3: function () {
		layer.close(this.index_del);
    }
}
function do_edit(id, type){
	var title = "";
	if(type == 1){
		title = "#generaltask_"+id;
	}else{
		title = "#target_"+id;
	}
	var value = $(title).html();
	$(title).removeAttr("onclick");
	var height = $(title).height();
	var html = '<textarea id="textarea_' + id +'" class="text_area_edit" style="width:100%;height:' + height + 'px" type="text" onblur="do_leave(' + id + ',' + type + ')"></textarea>';
	$(title).html(html);
	$("#textarea_"+id).focus().val(value);
}

function do_leave(id, type){
	var value = $("#textarea_"+id).val();
	var url = "";
	var title = "";
	if(type == 1){
		url = "target.php?do=update_generaltask";
		title = "#generaltask_"+id;
	}else{
		url = "target.php?do=update_target";
		title = "#target_"+id;
	}
	$(title).html(value);
	$(title).attr("onclick", "do_edit(" + id + "," + type + ")");
	$.ajax({
		type: 'post',
		url: url,
		data: {id:id, value:value},
		success:function(result){
		}
	});
}

function refresh()
{
	window.location.reload();
}
</script>
</head>
<body class="main">	
	<div id="search">
		<form name="actionform" method="post" action="tasktarget.php">
			<table border="0" cellpadding="4" cellspacing="1" class="table01">
				<tr>
					<td colspan="4" class="table_title">台账采集</td>
				</tr>
				<tr>
					<td class="td_title">台账类型</td>
					<td class="td_content" style="width:330px;"> 
						<select name="itemtype" id="itemtype" class="select">
						<option value=""></option>
						<?php
							foreach($task_type as $k=>$t){
								if($k == $itemtype){
									echo '<option value="' . $k . '" selected="selected">' . $t . '</option>';
								}else{
									echo '<option value="' . $k . '">' . $t . '</option>';
								}
							}
						?>
						</select>
					</td>
					<td class="td_title">总体任务</td>
					<td class="td_content" style="width:330px;"> 
						<select name="itemgeneraltask" id="itemgeneraltask" class="select" style="width:auto;">
						<option value=""></option>
						<?php
							if(is_array($generaltaskList) && count($generaltaskList) > 0){
								foreach($generaltaskList as $v){
									if($v['id'] == $itemgeneraltask){
										echo '<option value="' . $v['id'] . '" selected="selected">' . $v['name'] . '</option>';
									}else{
										echo '<option value="' . $v['id'] . '">' . $v['name'] . '</option>';
									}
								}
							}
						?></select>
					</td>
				</tr>
				<tr>
					<td class="td_title">是否转办</td>
					<td width="100" class="td_content"> 
						<select name="is_turn" id="is_turn" class="select" style="width:90px;">
							<option value="" <?php if($is_turn == "") echo "selected"; ?>></option>
							<option value="1" <?php if($is_turn == 1) echo "selected"; ?>>未转办</option>
							<option value="2" <?php if($is_turn == 2) echo "selected"; ?>>已转办</option>
						</select>
					</td>
					<td class="td_title" style="width:160px;">是否已转为正式台账</td>
					<td width="100" class="td_content"> 
						<select name="is_task" id="is_task" class="select" style="width:90px;">
							<option value="" <?php if($is_task == "") echo "selected"; ?>></option>
							<option value="0" <?php if($is_task == 0) echo "selected"; ?>>否</option>
							<option value="1" <?php if($is_task == 1) echo "selected"; ?>>是</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="td_title">工作目标</td>
					<td class="td_content" colspan="3">
						<input type="text" id="itemtarget" name="itemtarget" value="<?php echo $itemtarget; ?>" class="input" style="width:317px;">
					</td>
				</tr>
				<tr>
					<td style="text-align:center; height:35px;" colspan="9">
						<input type="submit" value="查 询" style="cursor:hand" class="button1">
						<input name="button4" type="button" class="button1" style="cursor:hand" onclick="queryReport_reset('target');return false;" value="重 置">
						<!--<input type="button" value="短信提醒" class="button1" onclick="javascript:hch.open_sms();">-->
						<input type="button" class="button1" name="drww" value="导入" onclick="hch.open_input();" />
					</td>
				</tr>			
			</table>
		</form>	
	</div>
	<div style="line-height: 35px; text-align:right;">
		<input type="button" class="button1 large" name="del_generaltask" value="删除总体任务" onclick="hch.open_delete()" />
		<input type="button" class="button1 large" name="add_generaltask" value="添加总体任务" onclick="hch.open_generaltask()" />
		<input type="button" class="button1 large" name="add_target" value="添加工作目标" onclick="lh.open_target()" />
	</div>
	<div id="result">
		<table align="center" cellpadding="4" cellspacing="1" class="table01" border="0">
			<thead>
				<tr style="height:32px;">
					<th width="6%" height="100%" class="table_title">序号</th>
					<th height="100%" class="table_title">工作目标</th>
					<th width="10%" height="100%" class="table_title">启动时间</th>
					<th width="10%" height="100%" class="table_title">完成时限</th>
					<th width="30%" height="100%" class="table_title">责任主体</th>
					<th width="100" height="100%" class="table_title">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$i = 0;
				if(is_array($resArr) && count($resArr) > 0){
					foreach($resArr as $r){
						echo '<td class="table_title" colspan="7" id="generaltask_' . $r['id'] . '" onclick="do_edit(' . $r['id'] . ',1)">' . $r['name'] . '</td>';
						$targetList = $r['targetList'];
						$count = 1;
						if(is_array($targetList) && count($targetList) > 0){
							foreach($targetList as $target){
								$i++;
								$response = '<p style="color:red;text-align:center;">未转办</p>';
								if($target['response'] != ""){
									$response = $target['response'];
								}
								echo '<tr>'
									. '<td >' . $i . '</td>'
									. '<td id="target_' . $target['id'] . '" onclick="do_edit(' . $target['id'] . ',2)">' . $target['target'] . '</td>'
									. '<td>' . $target['fromdate'] . '</td>'
									. '<td>' . $target['handledate'] . '</td>'
									. '<td style="text-align:left;">' . $response . '</td>'
									. '<td><input type="button" value="转 办" onclick="hch.open_dept(' . $target['id'] . ')" class="button1" />'
									. '<div style="height:2px;"></div>'
									. '<input type="button" value="采 纳" onclick="hch.open_adopt(' . $target['id'] . ')" class="button1" />'
									. '<div style="height:2px;"></div>'
									. '<input type="button" value="短信提醒" onclick="hch.open_sms('.$target['id'].')" class="button1" />'
									. '</td></tr>';
							}
						}else{
							echo '<tr><td colspan="7" class="tip"><font size="2">没有符合条件的纪录</font></td></tr>';
						}
					}
				}else{
					echo '<tr><td colspan="7"  class="tip"><font size="2">没有符合条件的纪录</font></td></tr>';
				}
				?>
			</tbody>
		</table>
	</div>
	<!-- 删除总体任务 -->
	<div class="show-dept" id="del_generaltask" title="删除总体任务" style="display:none;overflow:hidden;">
		<form action="target.php?do=del_generaltask" method="post">
			<table align="center" cellpadding="5" cellspacing="1" class="table01">
				<tr>
					<th class="td_title">序号</th>
					<th class="td_title">总体任务</th>
					<th class="td_title">操作</th>
				</tr>			
				<?php
				if(is_array($generaltaskList) && count($generaltaskList) > 0){
					$g_count = 0;
					foreach($generaltaskList as $generaltask){
						$g_count++;
						echo '<tr id="delgeneraltask_' . $generaltask['id'] . '">'
							. '<td class="td_content" align="center">' . $g_count . '</td>'
							. '<td class="td_content">' . $generaltask['name'] . '</td>'
							. '<td class="td_content" align="center"><input type="button" value="删 除" class="button1" onclick="hch.del_generaltask(' . $generaltask['id'] . ')">';
					}
				}else{
					echo '<tr><td colspan="4" class="td_content">暂无记录</td></tr>';
				}
				?>
			</table>
		</form>
	</div>
	<!-- 添加总体任务 -->
	<div class="show-dept" id="add_generaltask" title="添加总体任务" style="display:none;overflow:hidden;">
		<form action="importexcel.php" method="post">
			<div style="padding:8px;text-align:center;">
				<textarea placeholder="请输入总体任务！" id="content" style="width:370px; height:90px; border:1px solid gray; overflow-y:auto;"></textarea>
			</div>
			<div style="text-align:center;">
				<input type="button" value="确 定" style="cursor:pointer" class="button1" onclick="hch.do_submit();">
				<input type="button" value="取 消" style="cursor:pointer" class="button1" onclick="hch.close();"> 
			</div>
		</form>
	</div>
	<!--添加工作目标-->
	<div class="show-dept" id="add_target" title="添加工作目标" style="display:none;">
		<form action="target.php?do=add_target" method="post">
			<input type="hidden" name="modperson" value="<?php echo $username; ?>" />
			<input type="hidden" name="modtime" value="<?php echo date("Y-m-d H:i:s"); ?>" />
			<table align="center" cellpadding="5" cellspacing="1" class="table01">
				<tr>
					<td colspan="4" class="td_title">导入工作目标</td>
				</tr>
				<tr>
					<td colspan="4" class="td_title">添加工作目标</td>
				</tr>
				<tr>
					<td class="td_title">总体任务</td>
					<td class="td_content" colspan="3">
						<select name="generaltask" id="generaltask">
							<?php
							foreach($resArr as $v){
								echo '<option value="' . $v['id'] . '">' . $v['name'] . '</option>';
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="td_title">台账类型</td>
					<td class="td_content" colspan="3">
						<select name="tasktype" id="tasktype">
							<?php
							foreach($task_type as $key=>$value){
								echo '<option value="' . $key . '">' . $value . '</option>';
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="td_title">启动时间</td>
					<td class="td_content" colspan="3">
						<input type="text" name="fromdate" value="<?php echo date("Y-m-d", strtotime(date("Y",time())."-1"."-1")); ?>" onfocus="WdatePicker()" readonly="readonly" id="fromdate" class="input">
						<span class="text3"><span style="color: #FF0000">*</span>
					</td>
				</tr>
				<tr>
					<td class="td_title">完成时限</td>
					<td class="td_content" colspan="3">
						<input type="text" name="handledate" value="<?php echo date("Y-m-d", strtotime(date("Y",time())."-12"."-31")); ?>" onfocus="WdatePicker()" readonly="readonly" id="handledate" class="input">
						<span class="text3"><span style="color: #FF0000">*</span>
					</td>
				</tr>
				<tr>
					<td class="td_title">工作目标</td>
					<td class="td_content" colspan="3">
						<textarea name="target" id="target" cols="60" rows="4"></textarea>
						<span class="text3"><span style="color: #FF0000">*</span> 
					</td>
				</tr>
				<tr>
					<td colspan="4" class="td_title" style="line-height:40px; text-align:center;">
						<input type="button" value="确 定" class="button1" onclick="lh.do_submit();">&nbsp;
						<input type="button" value="取 消" class="button1" onclick="lh.close();"> 
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div class="show-dept" id="input_task" title="选择台账类型" style="display:none;">
		<form action="importexcel.php" method="post" enctype="multipart/form-data">
			<div style="padding: 10px 10px;height:auto;">
				<br>
				<div style="height:auto;line-height: 40px; text-align:center;">
					台账类型：<select name="input_type" id="input_type" class="select">
					<?php
						foreach($task_type as $key=>$value){
							echo '<option value="' . $key . '">' . $value . '</option>';
						}
					?>
					</select>	
					<br>
					选择文件：<input style="min-width:186px;border:none;" type="file" value="导入文件" name="file" id="file" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>
				</div>
				<br><br>
				<div style="clear: both;line-height: 35px;text-align: center;padding-top: 20px;">
					<input type="button" value="导 入" style="cursor:pointer" class="button1" onclick="hch.input_submit();">&nbsp;
					<input type="button" value="关 闭" style="cursor:pointer" class="button1" onclick="hch.close2();"> 
				</div>
			</div>
		</form>
	</div>
</body>
