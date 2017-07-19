<?php
include("taizhang.php");
$itemid = isset($_GET['id'])?$_GET['id']:"";
$type = isset($_GET['id'])?'mod':'add';
$detail = json_decode(get_djtz_detail($itemid), true);
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--CSS控制文件-->
<link rel="stylesheet" href="../css/default.css">
<!--常用的javascript文件-->
<script type='text/javascript' src="../js/popcalendar.js" type="text/javascript"></script> 
<script type='text/javascript' src="../js/common.js" type="text/javascript"></script> 
<script type='text/javascript' src="../js/checkValid.js" ></script>
<script type='text/javascript' src="../js/calendar.js" ></script>
<script type='text/javascript' src="../js/tjCommon.js" ></script>
<script type='text/javascript' src="../js/tjCheckData.js" ></script>
<script type='text/javascript' src="../js/web.js" ></script>
<script type='text/javascript' src='../js/calendar/WdatePicker.js'></script>
<script type='text/javascript' src='../js/jquery-1.8.2.min.js'></script>
<script type='text/javascript' src="../js/layer/layer.js"></script>
</head>
<body class="main" onload="destdepartmentChosen(document.forms[0]);onLoadSelectItemType(document.forms[0]);">
<!--主处理界面的顶部样式-->
<table width="98.5%%" border="0" align="center" cellpadding="0" cellspacing="0" style="border-left:1px solid #FFFFFF;">
  </tr>
  <tr>
    <td width="3" bgcolor="#FFFFFF"><img name="shim" src="main.htm" width="3" height="1" alt=""></td>
    <td bgcolor="#FFFFFF" class=AllMain>
	<input type='hidden' name='dn' value='超级管理员部门'/>

<!--结束主处理界面的顶部样式-->
<form name="/lh/supervise/newzdsx/dataup/dj/EditForm" method="post" action="/lh/supervise/newzdsx/dataup/dj/Edit.TJ">
	<table width="100%"  cellpadding="4" cellspacing="1" class="table01">
    <tr>
      <td colspan="4" class="table_title">工作标准</td>
    </tr>
	 
	<input type="hidden" name="TDateupDj.itemid" value="2016112499">

     <tr>
	  
	  <td class="td_title">启动时间</td>
      <td class="td_content" colspan="3">
        <input type="text" name="fromdate_str" value="<?php if($type == 'mod') echo $detail['FROMDATE']; ?>" onfocus="WdatePicker()" readonly="readonly" id="fromdate_str" class="input">
		<span class="text3"><span style="color: #FF0000">*</span>
      </td>
	</tr>
    <tr>
	  <td class="td_title">完成时限</td>
       <td class="td_content" colspan="3">
        <input type="text" name="handledate_str" value="<?php if($type == 'mod') echo $detail['HANDLEDATE']; ?>" onfocus="WdatePicker()" readonly="readonly" id="handledate_str" class="input">
        <span class="text3"><span style="color: #FF0000">*</span>
      </td>
       
      </tr>
	<tr> 
		<td  class="td_title">工作标准</td>
		<td  class="td_content" colspan="3">
			<textarea name="spare3" cols="60" rows="5" id="spare3" class="textarea"><?php if($type == 'mod') echo $detail['SPARE3']; ?></textarea>
			<span class="text3"><span style="color: #FF0000">*</span>
		</td>
	</tr>
	<input type="hidden" name="createdate" value="<?php echo date("Y-m-d H:i:s"); ?>" id="createdate" class="input">
	 
    <tr>
		<td  colspan="5" class="td_button">
			<input type="button" value="<?php if($type == 'mod') echo '修 改'; else echo '提 交'; ?>" onclick="do_edit()" class="button1" style="cursor:pointer;"/>&nbsp;
		  
			<input type="button" value="关 闭" onclick="window.parent.close()" class="button1" style="cursor:pointer;"/>    
		</td>
    </tr>
  </table>
  <input type="hidden" name="method" value="">
   <br>
<!-- 去掉了附件上传 -->
  
<script language="javaScript">

  function do_edit(){

      if (maxStringLength(MM_findObj('fromcompany'), '来文单位', 100))
      if (checkDate(MM_findObj('fromdate_str'), '台账日期', '-')) 
      if (checkSelect(MM_findObj('spare3'),'','总体任务'))
      if (checkSelect(MM_findObj('itemtype'),'','台账类型'))
      if (maxStringLength(MM_findObj('itemtitle'), '支撑项目', 100))         
      if (maxStringLength(MM_findObj('itemcontent'), '工作目标', 1000))  
       /*if (maxLength(MM_findObj('postilsugesstion'), '批示意见', 1000))
      if (maxLength(MM_findObj('undertakesuggestion'), '承办室意见', 500))3
      if (maxLength(MM_findObj('officesuggestion'), '委（局）领导意见', 500))
      if (maxLength(MM_findObj('undertakeperson'), '年度投资', 10))*/
      if (maxLength(MM_findObj('handlesuggestion'), '时间节点', 500))
     /* if (maxLength(MM_findObj('postilperson'), '责任领导', 10))*/
      if (maxLength(MM_findObj('officeperson'), '协管领导', 10))
      if (maxLength(MM_findObj('handleperson'), '经办人', 10))
      if (checkDate(MM_findObj('handledate_str'), '完成时限', '-')){   
		  document.forms[0].method.value='do_save';
		  document.forms[0].submit();
      }
  }

  function do_submit(){
  
      if (maxStringLength(MM_findObj('fromcompany'), '来文单位', 100))
      if (checkDate(MM_findObj('fromdate_str'), '台账日期', '-')) 
      if (checkSelect(MM_findObj('department'),'','台账类型'))
      if (checkSelect(MM_findObj('itemtype'),'','项目'))
      if (checkSelect(MM_findObj('registetype'),'','登记类别'))
      if (maxStringLength(MM_findObj('itemtitle'), '支撑项目', 100))         
      if (maxStringLength(MM_findObj('itemcontent'), '工作类别', 1000))  
       /*if (maxLength(MM_findObj('postilsugesstion'), '批示意见', 1000))
      if (maxLength(MM_findObj('undertakesuggestion'), '承办室意见', 500))
      if (maxLength(MM_findObj('officesuggestion'), '委（局）领导意见', 500))*/
      if (maxLength(MM_findObj('undertakeperson'), '年度投资', 10))
      if (maxLength(MM_findObj('handlesuggestion'), '时间节点', 500))
      if (maxLength(MM_findObj('postilperson'), '责任领导', 10))
      if (maxLength(MM_findObj('officeperson'), '协管领导', 10))
      if (maxLength(MM_findObj('handleperson'), '经办人', 10))
      if (checkDate(MM_findObj('handledate_str'), '完成时限', '-'))
       if (confirm('你确定要提交到立项吗？')){        
      document.forms[0].method.value='do_submit';
      document.forms[0].submit();
      } 
  }
  
  function do_del(){
    if (confirm('你确定要删除吗？')){
      document.forms[0].method.value='del';
      document.forms[0].submit();
    }
  }
  
  function do_upload_file(){
    openNewWindow('/system/common/handle.jsp?pageName=/system/upLoad.TJ&BS=YES&type=重大事项监察局&bid=2016112499&bid2=999999999999999&bid4=任务登记',0,1)
  }

</script>  
  
</form>

</body>
</html>