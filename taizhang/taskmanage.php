<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
//error_reporting(0);//关闭提示 
include_once "../mysql.php";
include("../constant.php");

$username = isset($_SESSION['userName']) ? $_SESSION['userName'] : "";

$time = date("Y-m-d H:i:s");
$type = isset($_POST['itemtype']) ? $_POST['itemtype'] : "";
$target = isset($_POST['target']) ? $_POST['target'] : "";
$is_turn = isset($_POST['is_turn']) ? $_POST['is_turn'] : "";
$mLink = new mysql;
$min_generaltaskid = get_min_generaltaskid($mLink);
$generaltaskid = isset($_REQUEST['generaltaskid']) ? $_REQUEST['generaltaskid'] : $min_generaltaskid ;
?>

<!doctype html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<!--CSS控制文件-->
<link rel="stylesheet" href="../css/style.css?v=1">
<link rel="stylesheet" href="../css/common.css" />
<link rel="stylesheet" href="../css/taizhang.css">
<script type="text/javascript" src="../js/jquery.min.js" ></script>
<script type="text/javascript" src="../js/ajaxfileupload.js"></script>
<script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<script src="../js/layer/layer.js"></script>
    
<script type="text/javascript" src="../js/jquery-ui/jquery-ui.min.js"></script>
<link href="../js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
$(function() {
	$(".resizable").resizable();
});
</script>

<style type="text/css">
input[type="text"]{width:100%;}
.layui-layer-shade .show-dept{top:15%;}

.table_title{
	background-image:url(../img/table_title22.gif);
}
input[name='onbacktime']{width:150px;}
textarea{width:100%;}

</style>

</head>
<body class="main">
	<div id="search">
		<form name="actionform" method="post" action="taskmanage.php">
			<table width="100%" cellpadding="4" cellspacing="1" class="table01">
				<tr>
				  <td height="25" colspan="4" class="table_title">台账转办</td>
				</tr>
				<tr>
					<td class="td_title">台账类型</td>
					<td class="td_content" style="width:330px;"> 
						<select name="itemtype" id="itemtype" class="select">
						<option value=""></option>
						<?php
							foreach($task_type as $k=>$t){
								if($k == $type){
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
						<select name="generaltaskid" id="generaltaskid" class="select" style="width:320px;" onchange="hch.change_activity(this)">
						<?php
							$generaltaskArr = get_generalTask($mLink);
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
					<td class="td_title">是否转办</td>
					<td width="100" class="td_content">
						<select name="is_turn" id="is_turn" style="width:90px;">
							<option value="" <?php if($is_turn == "") echo "selected"; ?>></option>
							<option value="1" <?php if($is_turn == 1) echo "selected"; ?>>未转办</option>
							<option value="2" <?php if($is_turn == 2) echo "selected"; ?>>已转办</option>
						</select>
					</td>
					<td class="td_title">工作目标</td>
					<td class="td_content">
						<input type="text" name="target" value="<?php echo $target; ?>" class="input" style="width:317px;">
					</td>
				</tr>
				<tr>
					<td colspan="4" class="td_title">
						<input type="button" value="短信提醒" style="cursor:hand" class="button1" onclick="onsmstoall();">
						<input type="submit" value="查询" style="cursor:hand" class="button1">       
						<input type="button" name="excel" value="导出" onclick="hch.open_type();" class="button1">
						<input type="button" name="excel" value="导入责任主体" onclick="hch.open_input();" class="button1 large">
						<input type="hidden" name="excel" value="" id="excelType">
						<input type="hidden" name="hd_taskId" />
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div style="height:10px;"></div>
	<div id="result">
		<table id="container" align="center" cellpadding="6" cellspacing="1" class="table01" width="100%">	
			<thead>
				<tr>
					<th rowspan="2" width="4%" height="100%" class="table_title">序<br />号</th>
					<th rowspan="2" width="11%" height="100%" class="table_title">工作目标</th>
					<th rowspan="2" width="15%" height="100%" class="table_title">支撑项目</th>
					<th colspan="2" width="20%" height="100%" class="table_title">工作标准</th>
					<th colspan="2" width="12%" height="100%" class="table_title">时间节点</th>
					<th rowspan="2" width="28%" height="100%" class="table_title">责任主体</th>
					<th rowspan="2" width="10%" height="100%" class="table_title">退回意见</th>
				</tr>
				<tr>
					<th width="6%" height="100%" class="table_title">年度投资<br />（元）</th>
					<th width="14%" height="100%" class="table_title">工作标准</th>
					<th width="6%" height="100%" class="table_title">启动时间</th>
					<th width="6%" height="100%" class="table_title">完成时间</th>
				</tr>
			<thead>
			<tbody>
			<?php
			if($generaltaskid == ""){
				$i = 0;
				$generalTaskArr=get_generalTask($mLink);
				foreach($generalTaskArr as $gtask){
					$generaltaskid = $gtask['id'];
					$res = get_taskList($mLink, $type, $target, $generaltaskid, $is_turn);
					if(is_array($res)){
						echo '<tr><td width="100%" height="35px" colspan="10" class="table_title">' . $gtask['name']. '</td></tr>';
						foreach($res as $value){
							$progressList = get_progressList($mLink,$value["id"]);
							$size = sizeof($progressList);
							$size = sizeof($progressList);
							$i++;
							$k = 0;
							$size2 = $size * 2;
							$size3 = $size * 2 + 2;
							$sub = "2";
							$main = "1";
							//责任主体：牵头单位+责任单位
							$deptNames_array=get_deptListByTaskid($mLink,$value["id"]);//责任单位
							$zrztNames = "<font color='red'>未转办</font>";
							$remark="";
							if(is_array($deptNames_array) && count($deptNames_array)>0){
								$deptHeadNames="<strong>牵头单位：</strong><br />";
								$deptNames = "<br /><strong>责任单位：</strong><br />";
								foreach($deptNames_array as $row){
									if(intval($row["ishead"])==1){//牵头单位
										$deptHeadNames.=$row["deptName"]."(".$taskrecv_status[$row["status"]].")";
									}else{//责任单位
										$deptNames.=$row["deptName"]."(".$taskrecv_status[$row["status"]].")<br/>";
									}
									if(intval($row["status"])==2){//退回状态
										$remark=$row["deptName"]."(".$row["remark"].")<br/>";
									}
								}
								$zrztNames=$deptHeadNames.$deptNames;
							}
							if(empty($progressList[0])){
								$progressList[0]["stage"]="";
								$progressList[0]["id"]="";
								$progressList[0]["startdate"]="";
								$progressList[0]["enddate"]="";
							}
							echo '<tr class="alternate_line1" style="cursor:pointer;line-height:30px;height:30px;">'
							. '<td rowspan="' . $size . '" align="center"><div class="resizable">' . $i . '</div></td>'
							. '<td rowspan="' . $size . '" align="center"><div class="resizable">' . $value['target'] . '</div></td>'
							. '<td rowspan="' . $size . '" align="center"><div class="resizable">' . $value['title'] . '</div></td>'
							. '<td rowspan="' . $size . '" align="center"><div class="resizable">'. $value['investment'] . '</div></td>'
							. '<td align="center" onclick="hch.do_edit(\'stage' . $progressList[0]["id"] . '\',\'' . $progressList[0]["stage"] . '\',2)" name="stage" id="stage' . $progressList[0]["id"] . '"><div class="resizable">'. $progressList[0]["stage"] . '</div></td>'
							. '<td align="center"><input onclick="hch.do_edit2(this)" class="text_area_item" type="text" name="startdate" value="'. $progressList[0]["startdate"] . '" onblur="hch.do_leave2(this,' . $progressList[0]["id"] . ',\'startdate\')" /></td>'
							. '<td align="center"><input onclick="hch.do_edit2(this)" class="text_area_item" type="text" name="enddate" value="'. $progressList[0]["enddate"] . '" onblur="hch.do_leave2(this,' . $progressList[0]["id"] . ',\'enddate\')" /></td>'
							. '<td rowspan="' . $size . '" align="center"><div id="div'.$value["id"].'" class="resizable">' . $zrztNames . '</div></td>'
							. '<td rowspan="' . $size . '" align="center"><div class="resizable">' . $remark . '<input type="button" value="转办" onclick="hch.open_dept(' . $value['id'] . ')" style="cursor:hand" class="button1"><div style="height:2px;"></div><input type="button" value="短信提醒" onclick="hch.open_sms('.$value['id'].');" class="button1" /></div></td>'
							. '</tr>';
							if($size > 1){
								for($j=1; $j<$size; $j++){
									echo '<tr class="alternate_line1" style="line-height:100%;">'
										. '<td align="center" onclick="hch.do_edit(\'stage' . $progressList[$j]["id"] . '\',\'' . $progressList[$j]["stage"] . '\',2)" name="stage" id="stage' . $progressList[$j]["id"] . '"><div class="resizable">'. $progressList[$j]["stage"] . '</div></td>'
										. '<td align="center"><input onclick="hch.do_edit2(this)" class="text_area_item" type="text" name="startdate" value="'. $progressList[$j]["startdate"] . '" onblur="hch.do_leave2(this,' . $progressList[$j]["id"] . ',\'startdate\')" /></td>'
										. '<td align="center"><input onclick="hch.do_edit2(this)" class="text_area_item" type="text" name="enddate" value="'. $progressList[$j]["enddate"] . '" onblur="hch.do_leave2(this,' . $progressList[$j]["id"] . ',\'enddate\')" /></td>'
										. '</tr>';
								}
							}
						}
					}
				}
				if($i == 0){
					echo '<tr class="alternate_line1" style="height:32px;line-height:32px;"><td colspan="10" align="center" ><font size="2">没有符合条件的纪录</font></td></tr>';
				}
			}else{
				$i = 0;
				$res = get_taskList($mLink, $type, $target, $generaltaskid, $is_turn);
				if(is_array($res) && count($res) > 0){
					echo '<tr><td width="100%" height="35px" colspan="10" class="table_title">' . $generaltask . '</td></tr>';
					foreach($res as $value){
						$progressList = get_progressList($mLink,$value["id"]);
						$size = sizeof($progressList);
						$size = sizeof($progressList);
						$i++;
						$k = 0;
						$size2 = $size * 2;
						$size3 = $size * 2 + 2;
						$sub = "2";
						$main = "1";
						//责任主体：牵头单位+责任单位
						$deptNames_array=get_deptListByTaskid($mLink,$value["id"]);//责任单位
						$zrztNames = "<font color='red'>未转办</font>";
						$remark="";
						if(is_array($deptNames_array) && count($deptNames_array)>0){
							$deptHeadNames="<strong>牵头单位：</strong>";
							$deptNames = "<br /><strong>责任单位：</strong>";
							$i1 = 0;
							$i2 = 0;
							foreach($deptNames_array as $row){
								if(intval($row["ishead"])==1){//牵头单位
									if($i1 == 0){
										$deptHeadNames .= $row["deptName"] . "(" . $taskrecv_status[$row["status"]] . ")";
									}else{
										$deptHeadNames .= "," . $row["deptName"] . "(" . $taskrecv_status[$row["status"]] . ")";
									}
									$i1++;
								}else{//责任单位
									if($i2 == 0){
										$deptNames .= $row["deptName"] . "(" . $taskrecv_status[$row["status"]] . ")";
									}else{
										$deptNames .= "," . $row["deptName"]."(" . $taskrecv_status[$row["status"]] . ")";
									}
									$i2++;
								}
								if(intval($row["status"])==2){//退回状态
									$remark=$row["deptName"]."(".$row["remark"].")<br/>";
								}								
							}
							$zrztNames=$deptHeadNames.$deptNames;
						}
						if(empty($progressList[0])){
							$progressList[0]["stage"]="";
							$progressList[0]["id"]="";
							$progressList[0]["startdate"]="";
							$progressList[0]["enddate"]="";
						}
						echo '<tr class="alternate_line1" style="cursor:pointer;line-height:30px;height:30px;">'
							. '<td rowspan="' . $size . '" align="center"><div class="resizable">' . $i . '</div></td>'
							. '<td rowspan="' . $size . '" align="center"><div class="resizable">' . $value['target'] . '</div></td>'
							. '<td rowspan="' . $size . '" align="center"><div class="resizable">' . $value['title'] . '</div></td>'
							. '<td rowspan="' . $size . '" align="center"><div class="resizable">'. $value['investment'] . '</div></td>'
							. '<td align="center" onclick="hch.do_edit(\'stage' . $progressList[0]["id"] . '\',\'' . $progressList[0]["stage"] . '\',2)" name="stage" id="stage' . $progressList[0]["id"] . '"><div class="resizable">'. $progressList[0]["stage"] . '</div></td>'
							. '<td align="center"><input onclick="hch.do_edit2(this)" class="text_area_item" type="text" name="startdate" value="'. $progressList[0]["startdate"] . '" onblur="hch.do_leave2(this,' . $progressList[0]["id"] . ',\'startdate\')" /></td>'
							. '<td align="center"><input onclick="hch.do_edit2(this)" class="text_area_item" type="text" name="enddate" value="'. $progressList[0]["enddate"] . '" onblur="hch.do_leave2(this,' . $progressList[0]["id"] . ',\'enddate\')" /></td>'
							. '<td rowspan="' . $size . '" style="text-align:left;"><div id="div'.$value["id"].'" class="resizable">' . $zrztNames . '</div></td>'
							. '<td rowspan="' . $size . '" align="center"><div class="resizable">' . $remark . '<input type="button" value="转办" onclick="hch.open_dept(' . $value['id'] . ')" style="cursor:hand" class="button1"><div style="height:2px;"></div><input type="button" value="短信提醒" onclick="hch.open_sms('.$value['id'].');" class="button1" /></div></td>'
							. '</tr>';
						if($size > 1){
							for($j=1; $j<$size; $j++){
								echo '<tr class="alternate_line1" style="line-height:100%;">'
									. '<td align="center" onclick="hch.do_edit(\'stage' . $progressList[$j]["id"] . '\',\'' . $progressList[$j]["stage"] . '\',2)" name="stage" id="stage' . $progressList[$j]["id"] . '"><div class="resizable">'. $progressList[$j]["stage"] . '</div></td>'
									. '<td align="center"><input onclick="hch.do_edit2(this)" class="text_area_item" type="text" name="startdate" value="'. $progressList[$j]["startdate"] . '" onblur="hch.do_leave2(this,' . $progressList[$j]["id"] . ',\'startdate\')" /></td>'
									. '<td align="center"><input onclick="hch.do_edit2(this)" class="text_area_item" type="text" name="enddate" value="'. $progressList[$j]["enddate"] . '" onblur="hch.do_leave2(this,' . $progressList[$j]["id"] . ',\'enddate\')" /></td>'
									. '</tr>';
							}
						}
					}
				}else{
					echo '<tr class="alternate_line1" style="height:32px;line-height:32px;"><td colspan="10" align="center" ><font size="2">没有符合条件的纪录</font></td></tr>';
				}
			}
			?>
			</tbody>
		</table>
	</div>
	<div id="loadmore" style="cursor: pointer;height: 35px;line-height: 35px;text-align: center;display: none;">加载更多</div>
	<!--描述：转办弹出层-->
	<div class="show-dept" id="tree_dept" title="转办" style="display:none; top:20%;">
		<div style="padding: 10px 10px;background-color:#DEEFFF;min-height: 747px;height: auto;">
			<div style="height: 40px;line-height: 40px;">
				按期反馈时间：<input type="text" value="<?=empty($onbacktime) ? "" : $onbacktime ?>" name="onbacktime" onclick="WdatePicker()" onfocus="hch.singleselect(1);" readonly="readonly" />&nbsp;&nbsp;
				定期反馈上报类型：
				<select name="regbacktype" id="regbacktype" onclick="hch.singleselect(2);">
				<?php 
					for ($i=1; $i<=count($regbacktype); $i++) {
						echo '<option value="' . $i . '">' . $regbacktype[$i] . '</option>';
					}
				?>
				</select>	  
			</div>
			<div style="height: 20px;line-height: 20px;">选择责任单位：</div>
			<div style="border:1px solid #e0dede;padding-left:5px;padding-bottom: 5px;" id="deptList"></div>
			<div id="head_list" class="show-head"></div>
			<div style="clear: both;line-height: 35px;text-align: center;padding-top: 20px;">
				<input type="submit" value="转 办" style="cursor:pointer" class="button1" onclick="hch.addDept();">
				<input type="button" value="关 闭" style="cursor:pointer" class="button1" onclick="hch.close_dept();"> 
			</div>
		</div>
	</div>
	<!--导出-->
	<div class="show-dept" id="select_type" title="选择台账类型" style="display:none;">
		<div style="padding: 10px 10px;height:auto;">
			<br>
			<div style="height: 40px;line-height: 40px; text-align:center;">
				台账类型：
				<select name="output_type" id="output_type" class="select">
				<?php
					foreach($task_type as $k=>$t){
						echo '<option value="' . $k . '">' . $t . '</option>';
					}
				?>
				</select>	  
			</div>
			<br><br>
			<div style="clear: both;line-height: 35px;text-align: center;padding-top: 20px;">
				<input type="submit" value="导 出" style="cursor:pointer" class="button1" onclick="hch.output_task();">
				<input type="button" value="关 闭" style="cursor:pointer" class="button1" onclick="hch.close_type();"> 
			</div>
		</div>
	</div>
	<div class="show-dept" id="layertip" style="display:none;">
  		<div style="padding: 18px 10px 10px 10px;height:auto; text-align: center;">
	  		<small style="color: blue;"><b>正在导出，请稍事休息，谢谢。。。</b></small>
		</div>
	</div>
	<div class="show-dept" id="layersmstip" style="display:none;">
  		<div style="padding: 18px 10px 10px 10px;height:auto; text-align:center;">
	  		<small style="color: blue;"><b>正在查询有任务未接收的单位列表，请稍等。。。</b></small>
		</div>
	</div>
	<!-- 导入 -->
	<div class="show-dept" id="input_task" title="导入责任主体" style="display:none;">
		<form action="importexcel.php" method="post" enctype="multipart/form-data">
			<div style="padding: 10px 10px;height:auto;">
				<br>
				<div style="height:auto;line-height: 30px; text-align:center;">
					<span style="display:inline-block; width:70px;">台账类型：</span>
					<select name="input_type" id="input_type" class="select">
					<?php
						foreach($task_type as $key=>$value){
							echo '<option value="' . $key . '">' . $value . '</option>';
						}
					?>
					</select>
				</div>
				<div style="height:auto;line-height: 30px; text-align:center;">
					<span style="display:inline-block; width:70px;">选择文件：</span>
					<input style="min-width:180px;border:none;" type="file" value="导入文件" name="file" id="file" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>
				</div>
				<div style="height:10px;"></div>
				<div style="height:auto;line-height: 30px; text-align:center;">
					<span style="display:inline-block; width:70px;">反馈类型：</span>
					<select name="regbacktype" id="stBackType" onchange="selBackType(this, '#inBacktime', 1);">
					<?php 
						for ($i=1; $i<=count($regbacktype); $i++) {
							echo '<option value="' . $i . '">' . $regbacktype[$i] . '</option>';
						}
					?>
					</select>
				</div>
				<div style="height:auto;line-height: 30px; text-align:center;">
					<span style="display:inline-block; width:70px;"></span>
					<input type="text" id="inBacktime" style="width:176px;" onclick="WdatePicker()" onfocus="selBackType(this, '#stBackType', 2);" readonly="readonly" />
				</div>
				<br>
				<div style="clear: both;line-height: 35px;text-align: center;padding-top: 20px;">
					<input type="button" value="导 入" style="cursor:pointer" class="button1" onclick="hch.input_submit();">&nbsp;
					<input type="button" value="关 闭" style="cursor:pointer" class="button1" onclick="hch.close2();"> 
				</div>
			</div>
		</form>
	</div>
</body>
</html>
<script type="text/javascript">
    var hch = {
        inInt: function () {
            if (typeof String.prototype.endsWith != 'function') { 
            	String.prototype.endsWith = function(suffix) {  
            		return this.indexOf(suffix, this.length - suffix.length) !== -1; 
            	};
            }
        },
		change_activity : function(obj){
			var id = $(obj).val();
			window.location.href = "taskmanage.php?generaltaskid=" + id;
		},
		open_sms:function($taskid){
			layer.open({
				type:2,
				title:'短信提醒',
				skin: 'layui-layer-rim', //加上边框
				area: ['80%', '80%'], //宽高
				content: "../sendsms.php?mt=taskmanager&tid="+$taskid
			});	
		},
        open_dept:function(taskId){
        	this.index_dept = layer.open({
                type: 1,
                title: $('#tree_dept').attr("title"),
                skin: 'layui-layer-rim', //加上边框
                area: ['90%', '85%'], //宽高
                offset: '10%',
                content: $("#tree_dept")
            });
            $("input[name='hd_taskId']").val(taskId);
            
	        this.bind_dept(taskId);//绑定部门
        },
        open_msg:function(msg){
        	layer.msg(msg, {offset: ['500px']});
        },
		bind_dept:function(taskId){
        	$.post("../xitong/leader_insert_update.php?do=leader_getDeptList", function (res) {
        		//console.log(res);
        		$("#deptList").html(res);
	        	hch.get_checkedDept(taskId);//获取选中部门
	        },'text');
        },
        get_checkedDept:function(taskId){
        	$.post("task_manage.php?do=task_getDeptListByTaskId",{'taskId':taskId}, function (res) {
          		//console.log("dd:"+res);
          		var arry = res.split(';');
          		var ckbDeptList = arry[0];
          		$("select[name='regbacktype']").val(arry[2]);
          		if($("select[name='regbacktype']").val()=="3"){
          			$("input[name='onbacktime']").val(arry[1]);
          		}else{
          			$("input[name='onbacktime']").val("");
          		}
          		//console.log(ckbDeptList);
          		if(ckbDeptList){//责任领导选中状态
          			var checkboxs = $("input[name='ckbDept']");//document.getElementsByName("ckbDept");
				    for (var i = 0; i < checkboxs.length; i++) {//获取选中状态
				    	var v=checkboxs[i].value;
						if(ckbDeptList.indexOf(v+",")==0){//字符串以‘v,’开头
							checkboxs[i].checked = true;
						}else if(ckbDeptList.indexOf(v)==0 && ckbDeptList.length==v.length){//字符串=‘v’
							checkboxs[i].checked = true;
						}else if(ckbDeptList.endsWith(","+v)){//字符串以‘,v’结尾
							checkboxs[i].checked = true;
						}else if(ckbDeptList.indexOf(","+v+",")>0){
							checkboxs[i].checked = true;
						}
				    }
          		}

  				hch.add_head_list();
          		var ckbHeadDeptList = arry[3];
          		//console.log(ckbHeadDeptList);
          		if(ckbHeadDeptList){//牵头领导选中状态
          			var checkboxs_Head = $("input[name='ckbDeptHead']");
          			for (var i = 0; i < checkboxs_Head.length; i++) {//获取选中状态
				    	var v=checkboxs_Head[i].value;
						if(ckbHeadDeptList.indexOf(v+",")==0){//字符串以‘v,’开头
							checkboxs_Head[i].checked = true;
						}else if(ckbHeadDeptList.indexOf(v)==0 && ckbHeadDeptList.length==v.length){//字符串=‘v’
							checkboxs_Head[i].checked = true;
						}else if(ckbHeadDeptList.endsWith(","+v)){//字符串以‘,v’结尾
							checkboxs_Head[i].checked = true;
						}else if(ckbHeadDeptList.indexOf(","+v+",")>0){
							checkboxs_Head[i].checked = true;
						}
				    }
          		}
	        });
        },
        add_head_list:function(){
       		var ss='';
  			$('#deptList :checked').each(function(){
  				var id=$(this).val();
  				var name=$(this).next().text();
  				ss+='<p><input id="ck_hd_'+id+'" type="checkbox" name="ckbDeptHead" value="'+id+'" />	<label for="ck_hd_'+id+'">'+name+'</label></p>';
  				
  			});
  			//console.log(ss);
  			$('#head_list').html('牵头单位：<br/>'+ss);	
        },
        addDept:function(){
            var taskId = $("input[name='hd_taskId']").val();
            var onbacktime=$("input[name='onbacktime']").val();
            var regbacktype=$("select[name='regbacktype']").val();
            //2017-02-14 新增
            if(regbacktype=="3" && !onbacktime){
            	layer.msg('请填写按期反馈时间！');
                return false;
            }
        	var deptIds="";
        	var deptHeadIds="";
        	$(':checkbox[name=ckbDept][checked]').each(function () {
                deptIds += $(this).val() + ",";
            });
            if (deptIds.length == 0) {
                hch.open_msg('您还没有选择责任单位！');
                return false;
            }
        	$(':checkbox[name=ckbDeptHead][checked]').each(function () {
                deptHeadIds += $(this).val() + ",";
            });
            if (deptHeadIds.length == 0) {
                hch.open_msg('您还没有选择牵头单位！');
                return false;
            }
            deptIds = deptIds.substr(0, deptIds.length - 1);
            deptHeadIds = deptHeadIds.substr(0, deptHeadIds.length - 1);
            //console.log(deptHeadIds);
            
//      	var pattern = /^([1-9]\d*|0)$/;// /^[1-9]\d*$/;
//      	if(!onbacktime){
//      		onbacktime=0;
//      	}
//	 		if(!pattern.test(onbacktime)){
//	 			layer.msg('必须输入数字！');
//              return false;
//	 		}
            
            var param={
            	'deptIds':deptIds,
            	'deptHeadIds':deptHeadIds,
            	'taskId':taskId,
            	'onbacktime':onbacktime,
            	'regbacktype':regbacktype
            }
        	$.post("task_manage.php?do=task_updateDeptBytaskId",param, function (res) {
      			//console.log(res);
      			if(res!=""){
      				hch.open_msg('操作成功！');
	          		hch.close_dept();
      				$("#div"+taskId).html(res);
      			}else{
              		hch.open_msg('操作失败！');
    			} 
//    			if(res){
//              	hch.open_msg('操作成功！');
//          		hch.close_dept();
//          		location.reload();
//    			}else{
//              	hch.open_msg('操作失败！');
//    			} 
	        },'text');
        },
        close_dept: function () {
            layer.close(this.index_dept);
        },
        close_type: function () {
            layer.close(this.index_type);
        },
        singleselect:function(i){
        	if(i==1 && $("input[name='onbacktime']").val()){//按期反馈时间
        		$("select[name='regbacktype']").val(3);
        	}else if(i==2 && $("select[name='regbacktype']").val()!="3"){//定期反馈上报类型
        		$("input[name='onbacktime']").val("");
        	}
        },
        loadmore:function(){ //点击div加载更多
		    var pageSize=10;
			var where = '';
 			//console.log(where);
			totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop()); 
			if($(document).height() <= totalheight){ 
			    var param={
				    'page': page,
				    'pageSize':pageSize,
				    'where':where
				};
				//console.log(param);
				$.post("../pager_scroll.php?do=pager_taskmanage", param, function(res) {  
				    //console.log(res);
				    if (res) {  
						$("#container").append(res);  
				        page++;  
				    } else {
                		hch.open_msg('别滚动了，已经到底了。。。');
					    $("#loadmore").html("没有可以加载的了");
				        return false;
				    }
				},'text'); 	
				
			} 
       	},
		do_edit:function(obj, content,type){
			$("#"+obj).removeAttr("onclick");
			var name = $("#"+obj).attr('name');
			var id = obj.substring(name.length);
			var height = $("#"+obj).height();
			var html = '<textarea class="text_area_edit" style="height:' + height + 'px" type="text" onblur="hch.do_leave(\''+obj+'\',' + id + ',\'' + name + '\','+type+')"></textarea>';
			$("#"+obj).html(html);
			$("textarea").focus().val(content);
		},
		do_edit2:function(obj){
			$(obj).removeClass("text_area_item");
			$(obj).removeAttr("readonly");
			$(obj).removeAttr("onclick");
			$(obj).addClass("text_area_edit");
		},
		do_leave2:function(obj,id, name){
			var value = $(obj).val();
			$(obj).removeClass("text_area_edit");
			$(obj).addClass("text_area_item");
			$(obj).attr("onclick","hch.do_edit2(this);");
			$.ajax({
				type:'post',
				url:"updateTask.php",	
				data:{id:id, name:name, value:value, type:2, modifier:'<?php echo $username; ?>', modtime:'<?php echo $time; ?>'},
				success:function(result){	
				}
			});
		},
		do_leave:function(obj, id, name, type){
			var value = $("#"+obj+" textarea").val();
			//alert(value);
			if(type == "2"){
				$("#"+name+id).html('<div class="resizable">'+value+'</div>');
				$("#"+name+id).attr("onclick","hch.do_edit('"+obj+"','"+value+"',2);");
				$.ajax({
					type:'post',
					url:"updateTask.php",	
					data:{id:id, name:name, value:value, type:type, modifier:'<?php echo $username; ?>', modtime:'<?php echo $time; ?>'},
					success:function(result){
						
					}
				});
			}else{
				$("#"+obj).html('<div class="resizable">'+value+'</div>');
				$("#"+obj).attr("onclick","hch.do_edit('"+obj+"','"+value+"',1);");
				$.ajax({
					type:'post',
					url:"updateTask.php",	
					data:{id:id, name:name, value:value, modifier:'<?php echo $username; ?>', modtime:'<?php echo $time; ?>'},
					success:function(result){
						
					}
				});
			}
		},
       	do_height:function(obj){
       		$(obj).addClass("autoheight");
       	},
		open_type:function(){
			this.index_type = layer.open({
				type: 1,
				title: $('#select_type').attr("title"),
				skin: 'layui-layer-rim', //加上边框
				area: ['400px', '300px'], //宽高
				offset: "120px",
				content: $("#select_type")
			 });
			$(".layui-layer-rim").css("background-color", "#DEEFFF");
		},
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
		onsmstip:function(){
			smstipLayer = layer.open({
				type: 1,
		        title: '',
		        closeBtn: 0,
		        skin: 'layui-layer-rim', //加上边框
		        area: ['400px', '50px'], //宽高
		        content: $("#layersmstip")
			});
			$(".layui-layer-rim").css("top", "150px");
			$(".layui-layer-rim").css("background-color", "#DEEFFF");
		},
		output_task : function(){
			var type = $("#output_type").val();
			hch.close_type();
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
		open_input:function(){
			$("#input_type").val("1");
			$("#file").val("");
			$("#stBackType").val("1");
			$("#inBacktime").val("");
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
			var tkType = $("#input_type").val();
			var regbacktype = $("#stBackType").val();
			var backtime = $("#inBacktime").val();
			var file = $("#file").val();
			if(!file){
				layer.msg("请选择上传文件");
				return false;
			}
			if(regbacktype == "3" && !backtime){
				layer.msg("请选择台账反馈类型");
				return false;
			}
			$.ajaxFileUpload({
					url:'importexcel4.php',
					type: 'post', 
					secureuri:false,
					fileElementId:'file',
					dataType: 'text',
					data:{'tkType': tkType, 'regbacktype': regbacktype, 'backtime':backtime},
					success: function (res){
						layer.msg(res);
						setTimeout('refresh()', 1000);
					}
			});
		},
		close2: function () {
			layer.close(this.index_input);
		}
    }
    
    $(function () {
        hch.inInt();
    });
    //全选/全不选    	
	$(document).on('click','.selall',function(item){
		var $this = $(this);
		var i=$(this).attr('id').replace('selall','');
	    if ($this.attr('checked')) {
	        $('#deptList'+i+' :checkbox[name="ckbDept"]').attr('checked', true);
	    } else {
	        $('#deptList'+i+' :checkbox[name="ckbDept"]').removeAttr('checked');
	    }
    });    
    //加载牵头单位预选
  	$(document).on('click','#deptList :checkbox',function(){
  		var ss='';
  		$('#deptList :checked').each(function(){
		  	var id=$(this).val();
		  	var name=$(this).next().text();
		  	if($(this).attr('name').substring(0,6)!="selall"){
		  		ss+='<p><input id="ck_hd_'+id+'" type="checkbox" name="ckbDeptHead" value="'+id+'" />	<label for="ck_hd_'+id+'">'+name+'</label></p>';
		  	}
	  	});
  		
  		//console.log(ss);
  		$('#head_list').html('牵头单位：<br/>'+ss);	
  	});
	function refresh()
	{
		window.location.reload();
	}

	function onsmstoall(){
		hch.onsmstip();
		$.post("../xitong/smsmanager.php?do=getnorecvdept",{}, function(res){
			layer.close(smstipLayer);
			if(res['state'] != 1)
				layer.msg(res['msg']);
			else{
				layer.open({
					type:2,
					title:'短信提醒',
					skin: 'layui-layer-rim', //加上边框
					area: ['95%', '95%'], //宽高
					content: "../sendsms.php?mt=taskmanagerall&tid="+res['dids']
				});
			}
		}, "json");
	}
	
	function selBackType(obj, param, flag){
		var value = $(obj).val();
		if(flag == 1){
			if(value != 3)
				$(param).val("");
		}else{
			if(value)
				$(param).val("3");
		}
	}

</script>
  
<!--
	描述：滚动鼠标加载更多

<script type="text/javascript">
    var pageSize=5;
	var where = '<?php echo $where;?>';
    $(window).scroll(function (){ 
		totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop()); 
		if($(document).height() <= totalheight){ 
		    var param={
			    'page': page,
			    'pageSize':pageSize,
			    'where':where
			};
			//console.log(param);
			$.post("../pager_scroll.php?do=pager_taskmanage", param, function(res) {  
			    //console.log(res);
			    if (res) {  
					$("#container").append(res);  
			        page++;  
			    } else {
			        layer.msg("别滚动了，已经到底了。。。");
				    $("#loadmore").html("没有可以加载的了");
			        return false;
			    }
			},'text'); 	
			
		} 
	});
</script>
-->
<?php
//获得总体任务
function get_generalTask($mLink){
	$res = $mLink->getAll("select id,name from generaltask order by id");
	return  $res;
}

//根据总体任务获取任务列表
function get_taskList($mLink,$type,$target,$generaltaskid,$is_turn){
	$where = " where status > 0";
	if($type != ""){
		$where .= " and type =" . $type;
	}
	if($target != ""){
		$where .= " and target like '%" . $target ."%'";
	}
	if($generaltaskid != ""){
		$where .= " and generaltaskid = " . $generaltaskid;
	}
	if($is_turn == 1){
		$where .= " and status < 3";
	}else if($is_turn == 2){
		$where .= " and status >= 3";
	}
	$where .= " order by status";
	$sql = "select id,target,title,investment,postilleader,status from task " . $where;// . " limit 0,10";
	//echo $sql;
	$res = $mLink->getAll($sql);
	return $res;
}
	
//根据taskid查询工作标准
function get_progressList($mLink,$taskid){
	$res = $mLink->getAll("select id,stage,startdate,enddate from progress where status > 0 and taskid = " . $taskid);
	return $res;
}

//根据taskid查询taskrecv中责任单位列表
function get_deptListByTaskid($mLink,$taskid){
	$deptNames="";
	$res = $mLink->getAll("select deptName, t.status, t.remark, ishead from taskrecv t left join dept d on t.deptid = d.deptId where taskid = " . $taskid);
	return $res;
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