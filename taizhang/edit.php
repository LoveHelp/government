<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
include("taizhang.php");
$itemid = isset($_GET['id']) ? $_GET['id']:"";
$type = isset($_GET['id']) ? 'mod':'add';
$detail = json_decode(get_djtz_detail($itemid), true);
$progressList = json_decode(get_progress_list($itemid), true);
$i=0;
$res = json_decode(get_max_progress_id(), true);
$maxid = $res['count'];
$count = $maxid;
$delete = "";
$generaltaskList = json_decode(get_general_task(), true);
?>
<html>
<head>
<title>添加台账</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--CSS控制文件-->
<link rel="stylesheet" href="../css/default.css">
<link rel="stylesheet" href="../css/taizhang.css">
<!--常用的javascript文件-->
<script type='text/javascript' src="../js/calendar/calendar.js" ></script>
<script type='text/javascript' src="../js/checkValid.js" ></script>
<script type='text/javascript' src="../js/web.js" ></script>
<script type='text/javascript' src='../js/calendar/WdatePicker.js'></script>
<script type='text/javascript' src='../js/jquery-1.8.2.min.js'></script>
<style>
.td_title{width:120px;}
.input_item{width:80%;}
</style>
</head>
<body class="main"  style="overflow-x: hidden;overflow-y: auto">
<!--主处理界面的顶部样式-->
<table width="98.5%%" border="0" align="center" cellpadding="0" cellspacing="0" style="border-left:1px solid #FFFFFF;">
  </tr>
  <tr>
    <td width="3" bgcolor="#FFFFFF"><img name="shim" src="main.htm" width="3" height="1" alt=""></td>
    <td bgcolor="#FFFFFF" class="AllMain">
	<input type='hidden' name='dn' value='超级管理员部门'/>

<!--结束主处理界面的顶部样式-->
<form name="editform" method="post" action="addTask.php">
	<table width="100%"  cellpadding="8" cellspacing="1" class="table01">
    <tr>
      <td colspan="5" class="table_title">台账登记</td>
    </tr>
	<input type="hidden" name="id" value="<?php echo $itemid; ?>">
	<tr>
	  <td class="td_title" style="height:28px;">启动时间</td>
      <td class="td_content">
        <input type="text" name="fromdate_str" value="<?php if($type == 'mod' && $detail['fromdate'] != "") echo $detail['fromdate'];  else echo date("Y-m-d", strtotime(date("Y",time())."-1"."-1")); ?>" onfocus="WdatePicker()" readonly="readonly" id="fromdate_str" class="input">
		<span class="text3"><span style="color: #FF0000">*</span></span>
      </td>
	  <td class="td_title">完成时限</td>
		   <td class="td_content">
			<input type="text" name="handledate_str" value="<?php if($type == 'mod' && $detail['handledate'] != "") echo $detail['handledate']; else echo date("Y-m-d", strtotime(date("Y",time())."-12"."-31")); ?>" onfocus="WdatePicker()" readonly="readonly" id="handledate_str" class="input">
			<span class="text3"><span style="color: #FF0000">*</span></span>
		  </td>
	</tr>
	<tr> 
		<td  class="td_title">总体任务</td>
		<td  class="td_content" colspan="3">
			<!--<textarea name="generaltask" style="width:50%;" rows="2" id="generaltask" class="textarea" <?php if($type == 'mod') echo "readonly"; ?>><?php if($type == 'mod') echo $detail['generaltask']; ?></textarea>-->
			<select id="generaltaskid" name="generaltaskid">
			<?php
			foreach($generaltaskList as $g){
				if($detail['generaltaskid'] == $g['id']){
					echo '<option value="' . $g['id'] . '" selected>' . $g['name'] . '</option>';
				}else{
					echo '<option value="' . $g['id'] . '">' . $g['name'] . '</option>';
				}
			}
			?>
			</select>
			<span class="text3"><span style="color: #FF0000">*</span></span>
		</td>
    </tr>  
	<tr>
		<td class="td_title">工作目标</td>
		<td class="td_content">
			<textarea name="target" style="width:90%;" rows="2" id="target" class="textarea"><?php if($type == 'mod') echo $detail['target']; ?></textarea>
			<span class="text3"><span style="color: #FF0000">*</span></span>
		</td>
		<td  class="td_title">支撑项目</td>
      <td  class="td_content">
		<textarea name="title" style="width:90%;" rows="2" id="title" class="textarea"><?php if($type == 'mod') echo $detail['title']; ?></textarea>
      </td>  
	</tr>
	<tr>
		<td height="28" class="td_title">工作标准</td>
		<td class="td_content" colspan="3">
		<table align="center" cellpadding="6" cellspacing="1" class="table01" width="100%" id="itemList">	
		<tbody>
			<tr>
				<td width="50%" height="100%" class="table_title">工作标准</td>
				<td width="18%" height="100%" class="table_title">启动时间</td>
				<td width="18%" height="100%" class="table_title">完成时间</td>
				<td width="14%" height="100%" class="table_title">操作</td>
			</tr>
			<?php if(is_array($progressList)) {
			$p = 0;
				foreach($progressList as $progress){
					if($p == 0){
						$delete .= $progress["id"];
					}else{
						$delete .= ",".$progress["id"];
					}
					$p++;
					echo '<tr>'
							. '<input type="hidden" name="list[' . $progress['id'] . '][type]" value="1" />'
							. '<input type="hidden" name="list[' . $progress['id'] . '][id]" value="' . $progress['id'] . '" />'
							. '<td width="55%" height="100%" class="table_title_item">'
								. '<textarea name="list[' . $progress['id'] . '][stage]" style="width:90%;" rows="2" class="textarea" id="list_stage_' . $progress['id'] . '">' . $progress['stage'] . '</textarea>'
								. '<span class="text3"><span style="color: #FF0000">*</span></span>'
							. '</td>'
							. '<td width="15%" height="100%" class="table_title_item">'
								. '<input type="text" name="list[' . $progress['id'] . '][startdate]" value="' . $progress['startdate'] . '" onfocus="WdatePicker()" readonly="readonly" class="input input_item" id="list_startdate_' . $progress['id'] . '">'
								. '<span class="text3"><span style="color: #FF0000">*</span></span>'
							. '</td>'
							. '<td width="15%" height="100%" class="table_title_item">'
								. '<input type="text" name="list[' . $progress['id'] . '][enddate]" value="' . $progress['enddate'] . '" onfocus="WdatePicker()" readonly="readonly" class="input input_item" id="list_enddate_' . $progress['id'] . '">'
								. '<span class="text3"><span style="color: #FF0000">*</span></span>'
							. '</td>'
							. '<td width="15%" height="100%" class="table_title_item">'
								. '<input type="button" value="删 除" onclick="removeRow(this)" class="button1" style="cursor:pointer;"/>'
							. '</td>'
						. '</tr>';
				}
			 }else{ ?>
			<tr>
				<input type="hidden" name="list[0][type]" value="0" />
				<td width="55%" height="100%" class="table_title_item">
					<textarea name="list[0][stage]" style="width:90%;" rows="2" class="textarea" id="list_stage_0"></textarea>
					<span class="text3"><span style="color: #FF0000">*</span></span>
				</td>
				<td width="15%" height="100%" class="table_title_item">
					<input type="text" name="list[0][startdate]" value="" onfocus="WdatePicker()" readonly="readonly" class="input input_item" id="list_startdate_0">
					<span class="text3"><span style="color: #FF0000">*</span></span>
				</td>
				<td width="15%" height="100%" class="table_title_item">
					<input type="text" name="list[0][enddate]" value="" onfocus="WdatePicker()" readonly="readonly" class="input input_item" id="list_enddate_0">
					<span class="text3"><span style="color: #FF0000">*</span></span>
				</td>
				<td width="15%" height="100%" class="table_title_item">
					<input type="button" value="删 除" onclick="removeRow(this)" class="button1" style="cursor:pointer;"/>
				</td>
			</tr>
			<?php } ?>
		</tbody>
		</table>
		<br />
		<div align="center"><input type="button" value="添 加" onclick="addRow()" class="button1" style="cursor:pointer;"/></div>
	</td>
	
	</tr>

	<tr>
	  <td  class="td_title">台账类型</td>
      <td  class="td_content">
	  <select name="type" id="type" class="select">
		<option value="0" <?php if($type == 'add') echo 'selected="selected"'; ?> ></option>
		<?php
			$i = 0;
			foreach($task_type as $key=>$v){
				$i++;

				if($detail['type'] == $key){
					echo '<option value="' . $key . '" selected="selected" >' . $v . '</option>';
				}else{
					echo '<option value="' . $key . '" >' . $v . '</option>';
				}
				
			}
		?>
	</select>
	   
	   <span class="text3"><span style="color: #FF0000">*</span></span>
      </td>
	  <td class="td_title">年度投资</td>
      <td class="td_content">
        <input type="text" name="investment" value="<?php if($type == 'mod') echo $detail['investment']; ?>" id="investment" class="input">
      </td>
	  
       
      </tr>
	<tr>
		<td  class="td_title">来文单位</td>
		<td class="td_content">
			<input type="text" name="fromcompany" value="<?php if($type == 'mod') echo $detail['fromcompany']; ?>" id="fromcompany" class="input">
      </td>
	<td class="td_title">市领导</td>
		<td  class="td_content">
			<input type="text" name="postilleader" value="<?php if($type == 'mod') echo $detail['postilleader']; else echo $_SESSION['userName']; ?>" id="postilperson" class="input">
		</td>
      
	  
	</tr>
   <tr>
   <td height="28" class="td_title">经办人</td>
      <td class="td_content">
        <input type="text" name="handleperson" value="<?php if($type == 'mod') echo $detail['handleperson']; ?>" id="handleperson" class="input">
      </td>
      
	 <td  height="28" class="td_title">牵头领导</td>
      <td  class="td_content">
        <input type="text" name="headleader" value="<?php if($type == 'mod') echo $detail['headleader']; ?>" id="headleader" class="input">
      </td> 
	</tr>
	<input type="hidden" name="createtime" value="<?php echo date("Y-m-d H:i:s"); ?>" id="createtime" class="input" />
	<input type="hidden" name="creater" value="<?php echo $_SESSION['userName']; ?>" id="creater" class="input" />
	<input type="hidden" name="status" value="1" id="status" class="input" />
	 
    <tr>
		<td  colspan="4" class="td_button">
			<input type="button" value="<?php if($type == 'mod') echo '修 改'; else echo '提 交'; ?>" onclick="do_edit()" class="button1" style="cursor:pointer;"/>&nbsp;
			<input type="button" value="删 除" onclick="do_delete(<?php echo $itemid; ?>)" class="button1" style="cursor:pointer;"/>&nbsp;
			<input type="button" value="关 闭" onclick="window.parent.close()" class="button1" style="cursor:pointer;"/>    
		</td>
    </tr>
  </table>
  <input type="hidden" name="method" value="">
  <input type="hidden" value="<?php echo $delete; ?>" name="delete" />
   <br>
<!-- 去掉了附件上传 -->
  
<script language="javaScript">
var count = <?php echo $maxid; ?>;
var n = <?php echo sizeof($progressList); ?>;
//动态添加tr
function addRow(){	
	count++;
	n++;
	var tr = $("#itemList tbody tr:last");
	var html = '<tr><input type="hidden" name="list['+count+'][type]" value="0" /><td width="55%" height="100%" class="table_title_item">'
				+ '<textarea name="list['+count+'][stage]" style="width:90%;" rows="2" class="textarea" id="list_stage_'+count+'"></textarea>'
				+ '<span class="text3"><span style="color: #FF0000">*</span></span></td>'
				+ '<td width="15%" height="100%" class="table_title_item">'
				+ '<input type="text" name="list['+count+'][startdate]" value="" onfocus="WdatePicker()" readonly="readonly" class="input input_item" id="list_startdate_'+count+'">'
				+ '<span class="text3"><span style="color: #FF0000">*</span></span></td>'
				+ '<td width="15%" height="100%" class="table_title_item">'
				+ '<input type="text" name="list['+count+'][enddate]" value="" onfocus="WdatePicker()" readonly="readonly" class="input input_item" id="list_enddate_'+count+'">'
				+ '<span class="text3"><span style="color: #FF0000">*</span></span></td>'
				+ '<td width="15%" height="100%" class="table_title_item">'
				+ '<input type="button" value="删 除" onclick="removeRow(this)" class="button1" style="cursor:pointer;"/></td></tr>';
    tr.after(html);
}

function removeRow(obj){
	var tr = $(obj).parent().parent();
	if(n > 1){
		tr.remove();
		count--;
		n--;
	}
}

function removeRow(obj, id){
	var tr = $(obj).parent().parent();
	if(n > 1){
		tr.remove();
		count--;
		n--;
	}
}

function do_edit(){
	//if (maxStringLength(MM_findObj('fromcompany'), '来文单位', 100))
    if (checkDate(MM_findObj('fromdate_str'), '启动时间', '-'))
	if (checkSelect(MM_findObj('type'),'0','台账类型'))
	if (checkDate(MM_findObj('handledate_str'), '完成时限', '-'))
	if (maxLength(MM_findObj('investment'), '年度投资', 100))
	if (maxStringLength(MM_findObj('target'), '工作目标', 1000)){
		document.forms[0].submit();
	}
	//if (maxStringLength(MM_findObj('handleperson'), '经办人', 10))
	/*for(var i=<?php echo $maxid; ?>; i< <?php echo $count; ?>; i++)
		if (maxStringLength(document.getElementById('list_stage_'+i), '工作标准', 1000))
		if (maxStringLength(document.getElementById('list_startdate_'+i), '启动时间', 1000))
		if (maxStringLength(document.getElementById('list_enddate_'+i), '完成时间', 1000))*/
	
    
    //if (maxStringLength(MM_findObj('headleader'), '牵头单位', 10)){   
		//document.forms[0].method.value='do_save';
	//	document.forms[0].submit();
    //}
  }

function do_delete(id){
	$.ajax({
		url:'taizhang.php?do=delete',
		type:'post',
		data:{id:id},
		success:function(result){
			if(result){
				alert("删除成功！");
				window.parent.close();
			}else{
				alert("删除失败！");
			}
		}
	});
}
</script>  
  
</form>

</body>
</html>