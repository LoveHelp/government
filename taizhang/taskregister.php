<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
include_once "../mysql.php";
include_once "../constant.php";
$itemtype = isset($_POST['itemtype']) ? $_POST['itemtype'] : 0;
$itemtarget = isset($_POST['itemtarget']) ? $_POST['itemtarget'] : "";
$username = isset($_SESSION['userName']) ? $_SESSION['userName'] : "";

$time = date("Y-m-d H:i:s");
$where = "";
if($itemtype != 0){
	$where .= " and type = " . $itemtype;
}
if($itemtarget != ""){
	$where .= " and target like '%" . $itemtarget ."%'";
}
$where .= " order by id desc";
$i = 0;
$mLink = new mysql;
$min_generaltaskid = get_min_generaltaskid($mLink);
$generaltaskid = isset($_REQUEST['generaltaskid']) ? $_REQUEST['generaltaskid'] : $min_generaltaskid ;
?>
<!doctype html>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<!--CSS控制文件-->
<link rel="stylesheet" href="../css/common.css" />
<link rel="stylesheet" href="../css/taizhang.css">
<!--常用的javascript文件-->
<!--<script src="../js/jquery-1.8.2.min.js"></script>-->
<script type="text/javascript" src="../js/jquery.min.js" ></script>
<script type="text/javascript" src="../js/layer/layer.js" ></script>
<script type="text/javascript" src="../js/taizhang.js"></script>
<script src="../js/ajaxfileupload.js"></script>
<script type="text/javascript" src="../js/jquery-ui/jquery-ui.min.js"></script>
<link href="../js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css">
<style>
.table_title{
	background-image:url(../img/table_title22.gif);
}
input[type="text"]{
	width:100%;
}
textarea{
	width:100%;
	height:100%;
}
input{width:100%;}
.copy{
	margin:5px 0px 0px 0px;
}
</style>
<script>
$(function() {
    $(".resizable").resizable();
});
var copy_task_id;
var hch = {
	onexporttip:function(){
		tipLayer = layer.open({
			type: 1,
		    title: '',
		    closeBtn: 0,
		    skin: 'layui-layer-rim', //加上边框
		    area: ['400px', '50px'], //宽高
		    content: $("#layertip")
		});
		$(".layui-layer-rim").css("top", "150px");
		$(".layui-layer-rim").css("background-color", "#DEEFFF");
	},
	open_type:function(){
        this.index_type = layer.open({
			type: 1,
            title: $('#select_type').attr("title"),
            skin: 'layui-layer-rim', //加上边框
            area: ['400px', '300px'], //宽高
			//offset: "120px",
            content: $("#select_type")
         });
		$(".layui-layer-rim").css("top", "120px");
		$(".layui-layer-rim").css("background-color", "#DEEFFF");
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
	open_attach:function(id){
		this.index_attach = layer.open({
			type: 1,
            title: $('#input_attach').attr("title"),
            skin: 'layui-layer-rim', //加上边框
            area: ['500px', '200px'], //宽高
            content: $("#input_attach")
         });
		$("#input_taskid").val(id);
		$("#attachfile").val("");
		$(".layui-layer-rim").css("background-color", "#DEEFFF");
	},
	copy_task:function(id){
		copy_task_id = id;
		if(confirm('复制成功，确定要粘贴成新台账吗?')){
			$.ajax({
				type:'post',
				url:"taizhang.php?do=paste",	
				data:{taskid:copy_task_id, username:'<?php echo $username; ?>'},
				success:function(result){
					if(result != ""){
						$("#task_"+id).before(result);
						//alert("粘贴台账成功！");
					}else{
						alert("粘贴台账失败！");
					}
				}
			});  
			return true;
		}
		return false;;
	},
	input_submit:function(){
        var type = $("#input_type").val();
		$.ajaxFileUpload({
			 	url:'importexcel.php',
        		type: 'post', 
			 	secureuri:false,
			 	fileElementId:'file',
			 	dataType: 'text',
			 	data:{type:type},
			 	success: function (res){
					hch.close2();
					alert(res);
					window.location.reload();
				}
		});
	},
	attach_submit:function(){
        var taskid = $("#input_taskid").val();
		$.ajaxFileUpload({
			 	url:'taizhang.php?do=attach',
        		type: 'post', 
			 	secureuri:false,
			 	fileElementId:'attachfile',
			 	dataType: 'text',
			 	data:{taskid:taskid},
			 	success: function (res){
					hch.close3();
					window.location.reload();
				}
		});
	},
	output_task : function(){
		var type = $("#output_type").val();
		hch.close();
		hch.onexporttip();
		$.ajax({
			type:'get',
			url:"exportexcel.php",	
			data:{type:type},
			success:function(result){
				layer.close(tipLayer);
				window.location.href = result;
			}
		});    
	},
    close: function () {
		layer.close(this.index_type);
    },
	close2: function () {
		layer.close(this.index_input);
    },
	close3: function () {
		layer.close(this.index_attach);
    }
}
function do_edit(obj, content,type){
	$("#"+obj).removeAttr("onclick");
	var name = $("#"+obj).attr('name');
	var id = obj.substring(name.length);
	var height = $("#"+obj).height();
	var html = '<textarea class="text_area_edit" style="height:' + height + 'px" type="text" onblur="do_leave(\''+obj+'\',' + id + ',\'' + name + '\','+type+')"></textarea>';
	$("#"+obj).html(html);
	$("textarea").focus().val(content.replace(/<br>/g,'\r\n'));
}
function do_edit2(obj){
	$(obj).removeClass("text_area_item");
	$(obj).removeAttr("readonly");
	$(obj).removeAttr("onclick");
	$(obj).addClass("text_area_edit");
}
function do_leave2(obj,id, name){
	var value = $(obj).val().replace(/[\r\n]/g,"<br>");
	$(obj).removeClass("text_area_edit");
	$(obj).addClass("text_area_item");
	$(obj).attr("onclick","do_edit2(this);");
	$.ajax({
		type:'post',
		url:"updateTask.php",	
		data:{id:id, name:name, value:value, type:2, modifier:'<?php echo $username; ?>', modtime:'<?php echo $time; ?>'},
		success:function(result){	
		}
	});
}
function do_leave(obj,id, name, type){
	var value = $("#"+obj+" textarea").val().replace(/[\r\n]/g,"<br>");
	if(type == "2"){
		$("#"+name+id).html('<div class="resizable">'+value+'</div>');
		$("#"+name+id).attr("onclick","do_edit('"+obj+"','"+value+"',2);");
		$.ajax({
			type:'post',
			url:"updateTask.php",	
			data:{id:id, name:name, value:value, type:type, modifier:'<?php echo $username; ?>', modtime:'<?php echo $time; ?>'},
			success:function(result){
				
			}
		});
	}else{
		$("#"+obj).html('<div class="resizable">'+value+'</div>');
		$("#"+obj).attr("onclick","do_edit('"+obj+"','"+value+"',1);");
		$.ajax({
			type:'post',
			url:"updateTask.php",	
			data:{id:id, name:name, value:value, modifier:'<?php echo $username; ?>', modtime:'<?php echo $time; ?>'},
			success:function(result){
				
			}
		});
	}
}
function change_activity(obj){
	var id = $(obj).val();
	window.location.href = "taskregister.php?generaltaskid=" + id;
}
</script>
</head>
<body class="main">
	<input id="hiddendeptid" type="hidden" value="<?=$_SESSION['userDeptID']?>" />
	<div id="search">
		<form name="actionform" method="post" action="taskregister.php">
			<table border="0" cellpadding="4" cellspacing="1" class="table01">
				<tr>
					<td colspan="4" class="table_title">登记台账</td>
				</tr>
				<tr>
					<td class="td_title">台账类型</td>
					<td width="130" class="td_content" style="width:330px;"> 
						<select name="itemtype" id="itemtype" class="select" style="width:120px;">
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
						<select name="generaltaskid" id="generaltaskid" class="select" onchange="change_activity(this)" style="width:320px;">
						<?php
							$generaltaskArr = get_generaltask_list($mLink);
							foreach($generaltaskArr as $g){
								if($g['id'] == $generaltaskid){
									$generaltask = $g['name'];
									echo '<option value="' . $g['id'] . '" selected="selected">' . $g['name'] . '</option>';
								}else{
									echo '<option value="' . $g['id'] . '">' . $g['name'] . '</option>';
								}
							}
						?>
							<option value="" <?php if($generaltaskid == "") echo "selected"; ?>>全部</option>
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
					<td colspan="6" class="td_title">
						<input type="submit" value="查 询" style="cursor:hand" class="button1">
						<input name="button4" type="button" class="button1" style="cursor:hand" onclick="queryReport_reset('register');return false;" value="重 置">
						<input type="button" class="button1" name="ww" value="添加" onclick="openNewWindow('handle.php?name=edit.php',1,0)" style="cursor:pointer;" />
						<input type="button" class="button1" name="drww" value="导入" onclick="hch.open_input();" style="cursor:pointer" />
						<input type="button" class="button1" name="drww" value="导出" onclick="hch.open_type();" />
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div style="height: 10px;"></div>
	<div id="result">
		<table align="center" cellpadding="6" cellspacing="1" class="table01" width="100%">	
			<thead>
				<tr>
					<th rowspan="2" width="4%" height="100%" class="table_title">序<br />号</th>
					<th rowspan="2" width="15%" height="100%" class="table_title">工作目标</th>
					<th rowspan="2" width="20%" height="100%" class="table_title">支撑项目</th>
					<th colspan="2" width="37%" height="100%" class="table_title">完成标准</th>
					<th colspan="2" width="12%" height="100%" class="table_title">时间节点</th>
					<th rowspan="2" width="10%" height="100%" class="table_title">操作</th>
				</tr>
				<tr>
					<th width="6%" height="100%" class="table_title">年度投资<br />（万元）</th>
					<th width="31%" height="100%" class="table_title">工作标准</th>
					<th width="6%" height="100%" class="table_title">启动时间</th>
					<th width="6%" height="100%" class="table_title">完成时间</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if($generaltaskid != ""){
				$taskArr = get_tasklist($mLink, $generaltaskid, $where);
				if(is_array($taskArr) && count($taskArr) > 0){
					echo '<tr><td width="100%" height="100%" colspan="9" class="table_title">' . $generaltask . '</td></tr>';
					foreach($taskArr as $value){
						$progressList = get_progressList($mLink, $value["id"]);
						if(!is_array($progressList) || count($progressList) == 0){
							insert_into_progress($mLink, $value["id"]);
							$progressList = get_progressList($mLink, $value["id"]);
						}
						$size = sizeof($progressList);
						$i++;
						$k = 0;
						$size2 = $size * 2;
						$size3 = $size * 2 + 2;
						$sub = "2";
						$main = "1";
						//附件列表
						$attachList = get_attachList($mLink, $value["id"]);
						$attach_html = "";
						if(is_array($attachList) && count($attachList) > 0){
							foreach($attachList as $attach){
								$attach_html .= '<p style="text-align:left;"><a style="color:blue;" href="' . $attach['attachUrl'] . '">' . $attach['attachName'] . '</a></p>';
							}
						}
						echo '<tr class="alternate_line1" style="line-height:100%;" id="task_' . $value['id'] . '">'
							. '<td rowspan="' . $size . '" align="center" onclick="openNewWindow(\'handle.php?name=edit.php&id=' . $value['id'] . '\', 0, 1)" style="cursor:pointer">' . $i . '</td>'
							. '<td rowspan="' . $size . '" style="text-align:left;" name="target" onclick="do_edit(\'target' . $value['id'] . '\',\'' . $value['target'] . '\',1)" id="target' . $value['id'] . '"><div class="resizable">' . $value['target'] . '</div></td>'
							. '<td rowspan="' . $size . '" style="text-align:left;" onclick="do_edit(\'title' . $value['id'] . '\',\'' . $value['title'] . '\',1)" name="title" id="title' . $value['id'] . '"><div class="resizable">' . $value['title'] . '</div></td>' 
							. '<td rowspan="' . $size . '" align="center" onclick="do_edit(\'investment' . $value['id'] . '\',\'' . $value['investment'] . '\',1)" name="investment" id="investment' . $value['id'] . '"><div class="resizable">'. $value['investment'] . '</div></td>'
							. '<td style="text-align:left;" onclick="do_edit(\'stage' . $progressList[0]["id"] . '\',\'' . $progressList[0]["stage"] . '\',2)" name="stage" id="stage' . $progressList[0]["id"] . '"><div class="resizable">'. $progressList[0]["stage"] . '</div></td>'
							. '<td align="center"><input onclick="do_edit2(this)" class="text_area_item" type="text" name="startdate" value="'. $progressList[0]["startdate"] . '" onblur="do_leave2(this,' . $progressList[0]["id"] . ',\'startdate\')" /></td>'
							. '<td align="center"><input onclick="do_edit2(this)" class="text_area_item" type="text" name="enddate" value="'. $progressList[0]["enddate"] . '" onblur="do_leave2(this,' . $progressList[0]["id"] . ',\'enddate\')" /></td>' 
							. '<td rowspan="' . $size . '" id="attach_' . $value['id'] . '">' . $attach_html . '<input value="上传附件" style="cursor:hand" class="button1" type="button" onclick="hch.open_attach(' . $value['id'] . ')"><p class="copy"><input value="复制" style="cursor:hand" class="button1" type="button" onclick="hch.copy_task(' . $value['id'] . ')"></p></td>'
							. '</tr>';
						if($size > 1){
							for($c=1; $c<$size; $c++){
								echo '<tr class="alternate_line1" style="line-height:100%;">'
									. '<td style="text-align:left;" onclick="do_edit(\'stage' . $progressList[$c]["id"] . '\',\'' . $progressList[$c]["stage"] . '\',2)" name="stage" id="stage' . $progressList[$c]["id"] . '"><div class="resizable">'. $progressList[$c]["stage"] . '</div></td>'
									. '<td align="center"><input onclick="do_edit2(this)" class="text_area_item" type="text" name="startdate" value="'. $progressList[$c]["startdate"] . '" onblur="do_leave2(this,' . $progressList[$c]["id"] . ',\'startdate\')" /></td>'
									. '<td align="center"><input onclick="do_edit2(this)" class="text_area_item" type="text" name="enddate" value="'. $progressList[$c]["enddate"] . '" onblur="do_leave2(this,' . $progressList[$c]["id"] . ',\'enddate\')" /></td>' . '</tr>';
							}
						}
					}
				}else{
					echo '<tr class="alternate_line1"><td colspan="10" align="center"><font size="2">没有符合条件的纪录</font></td></tr>';
				}
			}else{
				$generaltaskArr = get_generaltask_list($mLink);
				
				foreach($generaltaskArr as $g){
					$generaltaskid2 = $g['id'];
					$taskArr = get_tasklist($mLink, $generaltaskid2, $where);
					
					if(is_array($taskArr) && count($taskArr) > 0){
						echo '<tr><td width="100%" height="100%" colspan="9" class="table_title">' . $g['name']. '</td></tr>';
						foreach($taskArr as $value){
							$progressList = get_progressList($mLink, $value["id"]);
							if(!is_array($progressList) || count($progressList) == 0){
								insert_into_progress($mLink, $value["id"]);
								$progressList = get_progressList($mLink, $value["id"]);
							}
							//附件列表
							$attachList = get_attachList($mLink, $value["id"]);
							$attach_html = "";
							if(is_array($attachList) && count($attachList) > 0){
								foreach($attachList as $attach){
									$attach_html .= '<p style="text-align:left;"><a style="color:blue;" href="' . $attach['attachUrl'] . '">' . $attach['attachName'] . '</a></p>';
								}
							}
							$size = sizeof($progressList);
							$i++;
							$k = 0;
							$size2 = $size * 2;
							$size3 = $size * 2 + 2;
							$sub = "2";
							$main = "1";
							echo '<tr class="alternate_line1" style="line-height:100%;">'
									. '<td rowspan="' . $size . '" align="center" onclick="openNewWindow(\'handle.php?name=edit.php&id=' . $value['id'] . '\', 0, 1)" style="cursor:pointer">' . $i . '</td>'
									. '<td rowspan="' . $size . '" style="text-align:left;" ><div class="resizable">' . $value['target'] . '</div></td>'
									. '<td rowspan="' . $size . '" style="text-align:left;" onclick="do_edit(\'title' . $value['id'] . '\',\'' . $value['title'] . '\',1)" name="title" id="title' . $value['id'] . '"><div class="resizable">' . $value['title'] . '</div></td>' 
									. '<td rowspan="' . $size . '" align="center" onclick="do_edit(\'investment' . $value['id'] . '\',\'' . $value['investment'] . '\',1)" name="investment" id="investment' . $value['id'] . '"><div class="resizable">'. $value['investment'] . '</div></td>'
								. '<td style="text-align:left;" onclick="do_edit(\'stage' . $progressList[0]["id"] . '\',\'' . $progressList[0]["stage"] . '\',2)" name="stage" id="stage' . $progressList[0]["id"] . '"><div class="resizable">'. $progressList[0]["stage"] . '</div></td>'
								. '<td align="center"><input onclick="do_edit2(this)" class="text_area_item" type="text" name="startdate" value="'. $progressList[0]["startdate"] . '" onblur="do_leave2(this,' . $progressList[0]["id"] . ',\'startdate\')" /></td>'
								. '<td align="center"><input onclick="do_edit2(this)" class="text_area_item" type="text" name="enddate" value="'. $progressList[0]["enddate"] . '" onblur="do_leave2(this,' . $progressList[0]["id"] . ',\'enddate\')" /></td>' 
								. '<td rowspan="' . $size . '" id="attach_' . $value['id'] . '">' . $attach_html . '<input value="上传附件" style="cursor:hand" class="button1" type="button" onclick="hch.open_attach(' . $value['id'] . ')"><p class="copy"><input value="复制" style="cursor:hand" class="button1" type="button" onclick="hch.copy_task(' . $value['id'] . ')"></p></td>'
								. '</tr>';
							if($size > 1){
								for($c=1; $c<$size; $c++){
									echo '<tr class="alternate_line1" style="line-height:100%;">'
										. '<td style="text-align:left;" onclick="do_edit(\'stage' . $progressList[$c]["id"] . '\',\'' . $progressList[$c]["stage"] . '\',2)" name="stage" id="stage' . $progressList[$c]["id"] . '"><div class="resizable">'. $progressList[$c]["stage"] . '</div></td>'
										. '<td align="center"><input onclick="do_edit2(this)" class="text_area_item" type="text" name="startdate" value="'. $progressList[$c]["startdate"] . '" onblur="do_leave2(this,' . $progressList[$c]["id"] . ',\'startdate\')" /></td>'
										. '<td align="center"><input onclick="do_edit2(this)" class="text_area_item" type="text" name="enddate" value="'. $progressList[$c]["enddate"] . '" onblur="do_leave2(this,' . $progressList[$c]["id"] . ',\'enddate\')" /></td>' . '</tr>';
								}
							}
						}
					}	
				}
				if($i == 0){
					echo '<tr class="alternate_line1"><td colspan="9" align="center"><font size="2">没有符合条件的纪录</font></td></tr>';
				}
			}
			?>
			</tbody>
		</table>
	</div>
	<!-- 导出 -->
	<div class="show-dept" id="select_type" title="选择台账类型" style="display:none;">
		<div style="padding: 10px 10px;height:auto;">
			<br>
			<div style="height: 40px;line-height: 40px; text-align:center;">
				台账类型：
				<select name="output_type" id="output_type" class="select">
				<?php
					foreach($task_type as $key=>$value){
						echo '<option value="' . $key . '">' . $value . '</option>';
					}
				?>
				</select>	  
			</div>
			<br><br>
			<div style="clear: both;line-height: 35px;text-align: center;padding-top: 20px;">
				<input type="submit" value="导 出" style="cursor:pointer" class="button1" onclick="hch.output_task();">&nbsp;
				<input type="button" value="关 闭" style="cursor:pointer" class="button1" onclick="hch.close();"> 
			</div>
		</div>
	</div>
	<!-- 导入 -->
	<div class="show-dept" id="input_task" title="选择台账类型" style="display:none;">
		<form action="importexcel.php" method="post" enctype="multipart/form-data">
			<div style="padding: 10px 10px;height:auto;">
				<br>
				<div style="height:auto;line-height: 40px; text-align:center;">
					台账类型：
					<select name="input_type" id="input_type" class="select">
					<?php
						foreach($task_type as $k=>$v){
							echo '<option value="' . $k . '">' . $v . '</option>';
						}
					?>
					</select>	
					<br>
					<p>选择文件:<input style="width:70%;" type="file" value="导入文件" name="file" id="file" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>
					</p>
				</div>
				<br><br>
				<div style="clear: both;line-height: 35px;text-align: center;padding-top: 20px;">
					<input type="button" value="导 入" style="cursor:pointer" class="button1" onclick="hch.input_submit();">&nbsp;
					<input type="button" value="关 闭" style="cursor:pointer" class="button1" onclick="hch.close2();"> 
				</div>
			</div>
		</form>
	</div>
	<div class="show-dept" id="input_attach" title="上传附件" style="display:none;">
		<input type="hidden" name="input_taskid" id="input_taskid" value="" />
		<div style="padding: 10px 10px;height:auto;">
			<div style="height:auto;line-height: 40px; text-align:center;">
				<p style="text-align:center;">选择文件:<input style="width:70%;" type="file" value="" name="attachfile" id="attachfile" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>
				</p>
			</div>
			<div style="clear: both;line-height: 35px;text-align: center;padding-top: 20px;">
				<input type="button" value="上 传" style="cursor:pointer" class="button1" onclick="hch.attach_submit();">&nbsp;
				<input type="button" value="关 闭" style="cursor:pointer" class="button1" onclick="hch.close3();"> 
			</div>
		</div>
	</div>
	<div class="show-dept" id="layertip" style="display:none;">
		<div style="padding: 18px 10px 10px 10px;height:auto; text-align: center;">
			<small style="color: blue;"><b>正在导出，请稍事休息，谢谢。。。</b></small>
		</div>
	</div>
</body>
</html>

<?php
//获得总体任务
function get_generaltask_list($mLink){
	$sql = "select id, name from generaltask order by id";
	$res = $mLink->getAll($sql);
	return  $res;
}

//根据总体任务获取任务列表
function get_tasklist($mLink, $generaltaskid, $where){
	$sql = "select id,target,title,investment,postilleader,status from task where status > 0 and generaltaskid = " . $generaltaskid . $where;
	$res = $mLink->getAll($sql);
	return $res;
}

function get_all_task($mLink, $where){
	$sql = "select id from task where status > 0" . $where;
	$res = $mLink->getAll($sql);
	return $res;
}
	
//根据taskid查询工作标准
function get_progressList($mLink, $taskid){
	$res = $mLink->getAll("select id,stage,startdate,enddate from progress where status > 0 and taskid = " . $taskid);
	return $res;
}

//根据taskid查询附件列表
function get_attachList($mLink, $taskid){
	$res = $mLink->getAll("select attachName, attachUrl from attachment where taskid = " . $taskid);
	return $res;
}

function insert_into_progress($mLink,$taskid){
	$sql = "insert into progress (taskid) value (" . $taskid . ")";
	$res = $mLink->insert($sql);
	if($res){
		return $res;
	}
}

function get_min_generaltaskid($mLink){
	$sql = "select min(id) as id from generaltask";
	$res = $mLink->getRow($sql);
	if($res){
		return $res['id'];
	}
}

$mLink->closelink();
?>