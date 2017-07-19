<?php
session_start();
include("taizhang.php");
$username = isset($_SESSION['userName']) ? $_SESSION['userName'] : "";
$id = isset($_GET['id']) ? $_GET['id'] : "";
$type = isset($_GET['type']) ? $_GET['type'] : "";
$time = date("Y-m-d H:i:s");
?>
<html>
<head>
<title><?php if($type == "apply") echo "办结申请"; else echo "任务办结"; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--CSS控制文件-->
<link rel="stylesheet" href="../css/default.css">
<link rel="stylesheet" href="../css/taizhang.css">
<!--常用的javascript文件-->
<script src="../js/jquery-1.8.2.min.js"></script>
<script src="../js/layer/layer.js"></script>
<script src="../js/ajaxfileupload.js"></script>
<style>textarea{width:100%;height:100%;}</style>
<script>
var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
$(function(){
	$("#index").attr("value", index);
	<?php if($type == "apply") {?>
		$("#apply_layer").show();
	<?php }else if($type == "complete") {?>
		$("#complete_layer").show();
	<?php } ?>
	function download(url){
		window.location.href = url;
	}
});
</script>

<body style="height:auto;overflow-y:auto;">
<div class="show-dept" id="apply_layer" title="提交办结申请" style="display:none;">
<script>
var ly = {
	do_submit : function(){
		var applicant = $("#applicant").val();
		var apply_content = $("#apply_content").val();
		if(applicant == ""){
			$("#errors").show();
			$(".error_tips").html("*&nbsp;请输入办结申请人！");
			$("#applicant").focus();
		}else if(apply_content == ""){
			$("#errors").show();
			$(".error_tips").html("*&nbsp;请输入办结申请内容！");
			$("#apply_content").focus();
		}else{
			//document.forms["apply_form"].submit();
			var applicant = $("#applicant").val();
			var apply_content = $("#apply_content").val();
			$.ajaxFileUpload({
			 	url:'taizhang.php?do=apply',
        		type: 'post', 
			 	secureuri:false,
			 	fileElementId:'file',
			 	dataType: 'text',
			 	data:{applicant:applicant, apply_time:'<?php echo $time; ?>', apply_content:apply_content, taskid:<?php echo $id; ?>},
			 	success: function (res){
					parent.layer.msg("提交成功！");
					ly.close();
					window.location.reload();
				}
			});
		}
	},
    close: function () {
		 parent.layer.close(index);
    }
}

</script>
<form action="#" method="post" name="apply_form" enctype="multipart/form-data">
	<!--<input type="hidden" name="apply_time" value="<?php echo $time; ?>" />
	<input type="hidden" name="taskid" value="<?php echo $id; ?>" />
	<input type="hidden" name="index" id="index" value="" />-->
	<?php
		$detail = json_decode(get_task_apply_detail($id), true);
		$is_complete = $detail['is_complete'];
	?>
	<table align="center" cellpadding="5" cellspacing="1" class="table01">
	<?php
	if($is_complete == 3){
		if($detail['backreason'] == ""){
			$backreason = "无";
		}else{
			$backreason = $detail['backreason'];
		}
		echo '<tr><td colspan="4" class="table_title">驳回信息</td></tr>'
			. '<tr><td class="td_title">驳回时间</td>'
			. '<td class="td_content" colspan="3">' . $detail['backtime'] . '</td></tr>'
			. '<tr><td class="td_title">驳回理由</td>'
			. '<td class="td_content" colspan="3">' . $backreason . '</td></tr>';
	}
	?>
		<tr>
			<td colspan="4" class="table_title">办结申请</td>
		</tr>
        <tr>
			<td class="td_title">办结申请人</td>
			<td class="td_content" colspan="3">
				<input type="text" name="applicant" id="applicant" size="20" value="<?php if($is_complete == 1) echo $username; else echo $detail['applicant']; ?>" <?php if($is_complete == 2 ||  $is_complete == 4) echo "disabled"; ?> />
				<span class="text3" style="color: #FF0000">* </span>
			</td>
		</tr>
		<tr>
			<td class="td_title">办结申请内容</td>
			<td class="td_content" colspan="3">
				<textarea name="apply_content" id="apply_content" cols="60" rows="4" <?php if($is_complete == 2 ||  $is_complete == 4) echo "disabled"; ?>><?php if($is_complete > 1) echo $detail['apply_content']; ?></textarea>
				<span class="text3"><span style="color: #FF0000">*</span> 
			</td>
		</tr>
		<tr>
			<td class="td_title">完成情况报告</td>
			<td class="td_content" colspan="3">
				<?php 
					if($is_complete == 1){
						echo '<input name="file" id="file" value="" style="cursor:pointer" type="file" />';
					}else if($is_complete == 2 || $is_complete == 4){
						if($detail['report_url'] == ""){
							echo '无';
						}else{
							$report_name = substr($detail['report_url'], 31);
							echo '<a href="' . $detail['report_url'] . '" style="color:blue;">' . $report_name . '</a>';
						}
					}else{
						if($detail['report_url'] == ""){
							echo '<input name="file" id="file" value="" style="cursor:pointer" type="file" />';
						}else{
							$report_name = substr($detail['report_url'], 31);
							echo '<a href="' . $detail['report_url'] . '" style="color:blue;">' . $report_name . '</a>';
							echo '<input name="file" id="file" value="' . $detail['report_url'] . '" style="cursor:pointer" type="file" />';
						}
					} 
				?>
			</td>
		</tr>
		<tr style="display:none;" id="errors">
			<td colspan="4" class="error_tips"></td>
		</tr>
  		<tr>
			<td colspan="4" class="td_button" style="height:80px;line-height:80px;padding-bottom:20px;">
				<?php
					if($is_complete == 1 || $is_complete == 3){
						echo '<input value="提 交" onclick="ly.do_submit()" class="button1" style="cursor:pointer" type="button">';
						echo '&nbsp;&nbsp;';
					}
				?>
      			<input value="关 闭" onclick="ly.close();" class="button1" style="pointer" type="button">
			</td>
		</tr>     
	</table>
</form>
</div>
<div class="show-dept" id="complete_layer" title="提交办结申请" style="display:none;">
<script>
var lh = {
	do_complete : function(){
		var completeleader = $("#completeleader").val();
		var completetime = $("#completetime").val();
		var taskid = $("#taskid").val();
		$.ajax({
			type:'post',
			url:"taizhang.php?do=complete",	
			data:{taskid:taskid,completeleader:completeleader,completetime:completetime,index:index},
			dataType:"text",
			success:function(result){
				if(result){
					parent.layer.close(index);
					window.location.reload();
				}
				//parent.layer.close(index);
			}
		}); 
		//document.forms["complete_form"].submit();
		
	},
    close: function () {
		 parent.layer.close(index);
    }
}

</script>
<form action="" method="post" name="complete_form" >
	<input type="hidden" name="completeleader" id="completeleader" value="<?php echo $username; ?>" />
	<input type="hidden" name="completetime" id="completetime" value="<?php echo $time; ?>" />
	<input type="hidden" name="taskid" id="taskid" value="<?php echo $id; ?>" />
<script>
	function apply_back(id){
		var value = $("#reason_"+id).val();
		$("#reason_"+id).attr("disabled","disabled");
		var backleader = $("#completeleader").val();
		var backtime = $("#completetime").val();
		if(value == ""){
			alert("驳回理由必填！");
		}else{
			$.ajax({
				type:'post',
				url:"taizhang.php?do=apply_back",	
				data:{id:id,backleader:backleader,backtime:backtime,backreason:value},
				success:function(result){
					$("#task_" + id).html("已驳回");
				}
			});  
		}
	}  
</script>
	<table align="center" cellpadding="5" cellspacing="1" class="table01" width="100%">
	<tbody>
		<tr>
			<td colspan="8" class="table_title">办结申请列表</td>
		</tr>
		<tr>
			<td width="15%" height="100%" class="table_title">责任主体</td>
			<td width="30%" height="100%" class="table_title">办结申请内容</td>
			<td width="20%" height="100%" class="table_title">完成情况报告</td>	
			<td width="20%" height="100%" class="table_title">驳回理由</td>
			<td width="15%" height="100%" class="table_title">办结申请</td>
		</tr>
		<?php
		$taskrecvArr = json_decode(get_all_task_apply($id), true);
		$task_detail = json_decode(get_djtz_detail($id), true);
		$complete_status = $task_detail['status'];
		if(is_array($taskrecvArr) && count($taskrecvArr) > 0){
		foreach($taskrecvArr as $task){
			/*if($task['regbacktype'] == 1){
				$backtime = $task['onbacktime'];
			}else{
				$backtime = $back_type[$task['regbacktype']];
			}*/
			if($task['apply_content'] == ""){
				$report_name = "";
			}else if($task['report_url'] == ""){
				$report_name = "无";
			}else{
				$report_name = substr($task['report_url'], 31);
			}
			
			echo '<tr>';
			if($task['ishead'] == 1){
				echo '<td align="center" height="100%" class="td_content"><font color="red">' . $task['deptName'] . '</font></td>';
			}else{
				echo '<td align="center" height="100%" class="td_content">' . $task['deptName'] . '</td>';
			}
			echo '<td align="center" height="100%" class="td_content">' . $task['apply_content'] . '</td>'
				. '<td align="center" height="100%" class="td_content"><a style="color:blue;" href="' . $task['report_url'] . '">' . $report_name . '</td>';
			
			if($task['is_complete'] == 1 || $task['apply_content'] == ""){
				echo '<td align="center" height="100%" class="td_content"></td>';
				$is_apply = '无';
			}else if($task['is_complete'] == 2){
				echo '<td align="center" height="100%" class="td_content"><textarea id="reason_' . $task['id'] . '"></textarea></td>';
				$is_apply = '<input type="button" value="驳 回" onclick="apply_back(' . $task['id'] . ');" class="button1" style="cursor:pointer" /> ';
			}else if($task['is_complete'] == 3){
				echo '<td align="center" height="100%" class="td_content">' . $task['backreason'] . '</td>';
				$is_apply = "已驳回";
			}else{
				echo '<td align="center" height="100%" class="td_content">' . $task['backreason'] . '</td>';
				$is_apply = "已办结";
			}
			echo '<td align="center" height="100%" class="td_content" id="task_' . $task['id'] . '">' . $is_apply . '</td></tr>';
		}
		}
		?>
	</tbody>
	</table>
	<div style="text-align:center; line-height:32px;">
		<div style="text-align:left;color:red;">&nbsp;*红色字体为牵头单位</div>
		<?php
			if($complete_status < 5){
				echo '<input type="button" value="办 结" onclick="lh.do_complete()" class="button1" style="cursor:pointer" />&nbsp;';
			}	
		?>     
		<input type="button" value="关 闭" onclick="lh.close()" class="button1" style="cursor:pointer" />  
	</div>
</form>
</div>
</body>
</html>