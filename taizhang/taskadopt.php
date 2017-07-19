<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
include("target.php");
$username = isset($_SESSION['userName']) ? $_SESSION['userName'] : "";
$time = date("Y-m-d H:i:s");;
$mLink = new mysql;
$targetid = isset($_GET['targetid']) ? $_GET['targetid'] : "";
$target = json_decode(get_target_by_id($mLink, $targetid), true);
?>
<html>
<head>
<title>台账意见采纳</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--CSS控制文件-->
<link rel="stylesheet" href="../css/default.css">
<link rel="stylesheet" href="../css/taizhang.css">
<!--常用的javascript文件-->
<script src="../js/jquery-1.8.2.min.js"></script>
<script src="../js/layer/layer.js"></script>
<style>
.table_title{background-image:url(../img/table_title22.gif);}
textarea{width:100%;height:100%;}
.alternate_line1 td{text-align:center}
.alternate_line2 td{text-align:center}
body{background:#ECF6FB;}
</style>
<script>
var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
var target_count = 0;
var hch = {
	adopt_progress : function(stage, startdate, enddate){
		var id = $("input[type='radio']:checked").val();
		var current_stage = $("#progress_" + id + "0_stage").html();
		var rowspan = parseInt($("#radio_" + id).attr("rowspan"));
		if($("#task_" + id + "_title").html() == "" && $("#task_" + id + "_investment").html() == "" && current_stage == "" ){
			target_count++;	
		}
		if(current_stage == ""){
			$("#progress_" + id + "0_stage").html(stage);
			$("#progress_" + id + "0_startdate").html(startdate);
			$("#progress_" + id + "0_enddate").html(enddate);
		}else{
			var next = rowspan + 1;
			var before = rowspan - 1;
			$("#radio_" + id).attr("rowspan", next);
			$("#task_" + id + "_target").attr("rowspan", next);
			$("#task_" + id + "_title").attr("rowspan", next);
			$("#task_" + id + "_investment").attr("rowspan", next);
			var html = '<tr class="alternate_line1" id="progress_' + id + rowspan + '">'
					+ '<td id="progress_' + id + rowspan + '_stage">' + stage + '</td>'
					+ '<td id="progress_' + id + rowspan + '_startdate">' + startdate + '</td>'
					+ '<td id="progress_' + id + rowspan + '_enddate">' + enddate + '</td>'
					+ '<td><input type="button" value="删除" class="button1" onclick="hch.target_delete(' + id + ',' + rowspan + ');"></td></tr>';
			if(rowspan == 1){
				$("#" + id).after(html);
			}else{
				$("#progress_" + id + before).after(html);
			}
		}
		//alert(target_count);
	},
	adopt_title : function(title, investment){
		var id = $("input[type='radio']:checked").val();
		var current_title = $("#task_" + id + "_title").html();
		var current_investment = $("#task_"+id+"_investment").html();
		var target = "<?php echo $target['target']; ?>";
		if(current_title == "" && current_investment == ""){
			$("#task_" + id + "_title").html(title);
			$("#task_" + id + "_investment").html(investment);
			if($("#progress_00_stage").html() == ""){
				target_count++;
			}
		}else{
			target_count++;
			$("input[type='radio']:checked").removeAttr("checked");
			var i = parseInt(id) + 1;
			var html = '<tr class="alternate_line1" id="' + i + '">'
					+ '<td rowspan="1" id="radio_' + i + '"><input type="radio" name="task_radio" value="' + i + '" checked="checked" /></td>'
					+ '<td rowspan="1" id="task_' + i + '_target">' + target + '</td>'
					+ '<td rowspan="1" id="task_' + i + '_title">' + title + '</td>'
					+ '<td rowspan="1" id="task_' + i + '_investment">' + investment + '</td>'
					+ '<td id="progress_' + i + '0_stage"></td>'
					+ '<td id="progress_' + i + '0_startdate"></td>'
					+ '<td id="progress_' + i + '0_enddate"></td>'
					+ '<td><input type="button" value="删除" class="button1" onclick="hch.target_delete(' + i + ',0);"></td></tr>';
			var rowspan = parseInt($("#radio_" + id).attr("rowspan"));
			if(rowspan == 1){
				$("#" + id).after(html);
			}else{
				var before = rowspan - 1;
				$("#progress_" + id + before).after(html);	
			}
		}
		//alert(target_count);
	},
	target_delete : function(t, p){
		var progress = $("#progress_" + t + p + "_stage").html();
		var id = $("input[type='radio']:checked").val();
		var rowspan = parseInt($("#radio_" + t).attr("rowspan"));
		if(p == 0){
			if(progress != ""){
				$("#progress_" + t + p + "_stage").html("");
				$("#progress_" + t + p + "_startdate").html("");
				$("#progress_" + t + p + "_enddate").html("");
			}else if(rowspan == 1){
				if(t == 0){
					if($("#task_" + t + "_title").html() != "" || $("#task_" + t + "_investment").html() != ""){
						$("#task_" + t + "_title").html("");
						$("#task_" + t + "_investment").html("");
						target_count--;
					}
				}else{
					target_count--;
					$("#" + t).remove();
					if(id == t){
						var pre = t - 1;
						$("#radio_" + pre + " input").attr("checked", "checked");
					 }
				}
			}
		}else{
			$("#progress_" + t + p).remove();
			var rowspan = parseInt($("#radio_" + t).attr("rowspan"));
			if(rowspan > 1){
				var pre = rowspan - 1;
				$("#radio_" + t).attr("rowspan", pre);
				$("#task_" + t + "_target").attr("rowspan", pre);
				$("#task_" + t + "_title").attr("rowspan", pre);
				$("#task_" + t + "_investment").attr("rowspan", pre);
			}
		}
		//alert(target_count);
	},
	do_sumit : function(){
		//alert(target_count);
		var task_0_title = $("#task_0_title").html();
		var task_0_investment = $("#task_0_investment").html();
		var progress_00_stage = $("#progress_00_stage").html();
		if(task_0_title == "" && task_0_investment == "" && progress_00_stage == ""){
			parent.layer.msg("未采纳任何意见！");
			parent.layer.close(index);
		}else{
			var task = new Array();
			for(var i = 0; i < target_count; i++){
				var progress = new Array();
				var rowspan = parseInt($("#radio_" + i).attr("rowspan"));
				for(var k = 0; k < rowspan; k++){
					var stage = $("#progress_" + i + k + "_stage").html();
					var startdate = $("#progress_" + i + k + "_startdate").html();
					var enddate = $("#progress_" + i + k + "_enddate").html();
					var progress_sub = {'stage':stage, 'startdate':startdate, 'enddate':enddate};
					progress.push(progress_sub);
				}
				var target = {'title':$("#task_" + i + "_title").html(), 'investment':$("#task_" + i + "_investment").html(),'progress':progress};
				task.push(target);
			}
			task = JSON.stringify(task);
			$.ajax({
				type: 'post',
				//dataType: 'json',
				url: 'target.php?do=add_task',
				//async:false,
				//contentType:"application/json",
				data: {task:task, targetid:<?php echo $targetid; ?>, username:'<?php echo $username; ?>', time:'<?php echo $time; ?>'},
				success:function(result){
					if(result){
						parent.layer.msg(result);
						parent.layer.close(index);
					}
				}
			});
		}
	},
	close : function(){
		parent.layer.close(index);
	}
}
</script>
<body style="overflow-x:hidden;overflow-y:auto;">
<form action="target.php?do=add_task" method="post" name="add_task" style="margin:0;">
	<table align="center" cellpadding="5" cellspacing="1" class="table01" id="add_task_table">
		<tr>
			<td colspan="8" class="table_title">登记台账</td>
		</tr>
		<tr>
			<td rowspan="2" width="4%" height="100%" class="table_title">选择</td>
			<td rowspan="2" width="15%" height="100%" class="table_title">工作目标</td>
			<td rowspan="2" width="15%" height="100%" class="table_title">支撑项目</td>
			<td colspan="2" width="40%" height="100%" class="table_title">工作标准</td>
			<td colspan="2" width="16%" height="100%" class="table_title">时间节点</td>
			<td rowspan="2" width="10%" height="100%" class="table_title">操作</td>
		</tr>
		<tr>
			<td width="8%" height="100%" class="table_title">年度投资<br />（元）</td>
			<td width="32%" height="100%" class="table_title">工作标准</td>
			<td width="8%" height="100%" class="table_title">启动时间</td>
			<td width="8%" height="100%" class="table_title">完成时间</td>
		</tr>
		<tr class="alternate_line1" id="0">
			<td id="radio_0" rowspan="1"><input type="radio" name="task_radio" value="0" checked="checked" /></td>
			<td name="task[0][target]" rowspan="1" id="task_0_target"><?php echo $target['target']; ?></td>
			<td name="task[0][title]" rowspan="1" id="task_0_title"></td>
			<td name="task[0][investment]" rowspan="1" id="task_0_investment"></td>
			<td name="progress_0[0][stage]" id="progress_00_stage"></td>
			<td name="progress_0[0][startdate]" id="progress_00_startdate"></td>
			<td name="progress_0[0][enddate]" id="progress_00_enddate"></td>
			<td><input type="button" value="删除" class="button1" onclick="hch.target_delete(0,0);"></td>
		</tr>
	</table>
<div style="padding:20px;text-align:center;">
				<input type="button" value="确 定" class="button1" onclick="hch.do_sumit();" />&nbsp;
				<input type="button" value="取 消" class="button1" onclick="hch.close();" /> 
</div>
	</form>
	<table align="center" cellpadding="5" cellspacing="1" class="table01">
		<tr>
			<td colspan="8" class="table_title">台账采纳</td>
		</tr>
		<tr>
			<td width="10%" class="table_title">责任主体</td>
			<td width="20%" class="table_title">支撑项目</td>
			<td width="10%" class="table_title">年度投资</td>
			<td width="10%" class="table_title">操作</td>
			<td width="20%" class="table_title">工作标准</td>
			<td width="10%" class="table_title">开始时间</td>
			<td width="10%" class="table_title">完成时间</td>
			<td width="10%" class="table_title">操作</td>
		</tr>
		<?php
		$titleList = json_decode(get_p_task_list($mLink, $targetid), true);
		$i = 0;
		if(is_array($titleList) && count($titleList) > 0){
			foreach($titleList as $t){
				$taskid = $t['id'];
				$progressList = json_decode(get_p_progress_list($mLink, $taskid), true);
				$count = count($progressList);
				if($i%2 == 0){
					echo '<tr class="alternate_line1">';
				}else{
					echo '<tr class="alternate_line2">';
				}
				echo  '<td rowspan="' . $count . '">' . $t['deptName'] . '</td>'
					. '<td rowspan="' . $count . '">' . $t['title'] . '</td>'
					. '<td rowspan="' . $count . '">' . $t['investment'] . '</td>'
					. '<td rowspan="' . $count . '"><input type="button" value="采纳" class="button1" onclick="hch.adopt_title(\'' . $t['title'] . '\', \'' . $t['investment'] . '\');"></td>';
				if(is_array($progressList) && count($progressList) > 0){
					echo '<td>' . $progressList[0]['stage'] . '</td>'
						. '<td>' . $progressList[0]['startdate'] . '</td>'
						. '<td>' . $progressList[0]['enddate'] . '</td>'
						. '<td><input type="button" value="采纳" class="button1" onclick="hch.adopt_progress(\'' . $progressList[0]['stage'] . '\', \'' . $progressList[0]['startdate'] . '\', \'' . $progressList[0]['enddate'] . '\');"></td></tr>';
					for($c = 1; $c < $count; $c++){
						if($i%2 == 0){
							echo '<tr class="alternate_line1">';
						}else{
							echo '<tr class="alternate_line2">';
						}
						echo '<td>' . $progressList[$c]['stage'] . '</td>'
							. '<td>' . $progressList[$c]['startdate'] . '</td>'
							. '<td>' . $progressList[$c]['enddate'] . '</td>'
							. '<td><input type="button" value="采纳" class="button1" onclick="hch.adopt_progress(\'' . $progressList[$c]['stage'] . '\', \'' . $progressList[$c]['startdate'] . '\', \'' . $progressList[$c]['enddate'] . '\');"></td></tr>';
					}
				}else{
					echo '<td></td>'
						. '<td></td>'
						. '<td></td>'
						. '<td></td></tr>';
				}
				$i++;
			}
		}else{
			echo '<tr class="alternate_line1"><td colspan="8"><font size="2">没有符合条件的纪录</font></td></tr>';
		}
		?>
	</table>
</body>
</html>
<?php 
$mLink->closelink();
?>