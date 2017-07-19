<?php

include_once 'sharedfilesmanager.php';
//根据用户的角色区别督察室人员及管理员
//非督察室人员、非管理员直接跳转
$roleid = $_SESSION['userRoleID'];
//1 超级管理员；2 台账管理员；6督查室主任
if($roleid > 2 && $roleid != 6 ){
	header('location:sharedfilesofdept.php');
	exit;
}

$dcode = empty($_POST['dcode']) ? trim('') : trim($_POST['dcode']);
$fname = empty($_POST['fname']) ? trim(''): trim($_POST['fname']);
$fromtime = empty($_POST['fromtime']) ? '' : trim($_POST['fromtime']);
$totime = empty($_POST['totime']) ? '' : trim($_POST['totime']);
$page = empty($_POST['page']) ? 1 : trim($_POST['page']);
$data = getfilesfromdb(0);
$count = 0;
if(!empty($data)){
	$data = json_decode($data, true);
	$count = sizeof($data);
}

?>
<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>邮件系统</title>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/layer/layer.js"></script>
	<script type="text/javascript" src="js/calendar/WdatePicker.js"></script>
	<link rel="stylesheet" href="css/common.css" />
</head>
<body class="main">
	<div id="search">
		<form name="actionform" method="post" action="sharedfiles.php">
			<table width="100%" cellpadding="4" cellspacing="1" class="table01">
				<tr>
				  <td height="25" colspan="7" class="table_title">文件列表</td>
				</tr>
				<tr>
					<td class="td_title">部门简码</td>
					<td class="td_content" style="width:160px;">
						<input type="text" id="dcode" name="dcode" value="<?=$dcode?>" placeholder="督查室（dcs）" />
					</td>
					<td height="20" class="td_title">文件名称</td>
					<td class="td_content" style="width:160px;">
						<input type="text" id="fname" name="fname" value="<?=$fname?>" placeholder="文件名称关键字" />
					</td>
					<td height="20" class="td_title">上传时间</td>
					<td class="td_content" style="width:350px;">
						<input type="text" name="fromtime" id="fromtime" value="<?=$fromtime?>" onfocus="WdatePicker()" readonly="readonly" class="input" />
						至
						<input type="text" name="totime" id="totime" value="<?=$totime?>" onfocus="WdatePicker()" readonly="readonly" class="input" />
					</td>
					<td>
						<input type="submit" value="查&emsp;询" class="button1">
						<input type="button" value="部门列表" onclick="showdepts();" class="button1">
						<input type="hidden" id="h_page" name="page" value="<?=$page?>">
						<input type="hidden" id="h_canscroll" value='1' />
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div style="height:10px;"></div>
	<div id="result">
		<!--定义查询返回结果框的范围ID-->
		<table border="0" cellpadding="4" cellspacing="1" class="table01" id="content_table">
			<thead>
				<tr class="table_title">
					<th style="width:50px;" class="table_title">序号</th>
					<th class="table_title">文件</th>
					<th style="width:200px;"  class="table_title">部门</th>
					<th style="width:100px;"  class="table_title">上传者</th>
					<th style="width:150px;"  class="table_title">上传时间</th>
				</tr>
			</thead>
			<tbody id="cons">
				<?php
				if($count === 0)
				{
					?><tr class="alternate_line1">
						<td colspan="5" class="tip">
							<font size="2">没有符合条件的纪录</font>
						</td>
					</tr><?php
				}else{
					for($i=0; $i<$count; $i++){
						?>
						<tr>
							<td><?=$i+1?></td>
							<td style="text-align:left;"><a href="<?=$data[$i]['furl']?>" target="_blank"><?=$data[$i]['fname']?></a></td>
							<td><span title="<?=$data[$i]['deptcode']?>"><?=$data[$i]['deptname']?></span></td>
							<td><?=$data[$i]['uname']?></td>
							<td><?=$data[$i]['uploadtime']?></td>
						</tr><?php
					}
				}	
				?>
			</tbody>
		</table>
	</div>
	<div></div>
</body>
</html>
<script type="text/javascript">
$(function(){
	//滚动条距底部的距离  
    var BOTTOM_OFFSET = 0;
	$(window).scroll(function(){
		if($("#h_canscroll").val() != 1)
			return;
		var currWin = $(window);  
	    //当前窗口的高度  
	    var winHeight = currWin.height();  
	    //当前滚动条从上往下滚动的距离  
	    var scrollTop = currWin.scrollTop();  
	    //当前文档的高度  
	    var docHeight = $(document).height();  
	  
	    //当 滚动条距底部的距离 + 滚动条滚动的距离 >= 文档的高度 - 窗口的高度  
	    //换句话说：（滚动条滚动的距离 + 窗口的高度 = 文档的高度）  这个是基本的公式  
	    if ((BOTTOM_OFFSET + scrollTop) >= docHeight - winHeight) {
	      	$("#h_canscroll").val(2);//防止出现重复加载的情况
	       	var page = $("#h_page").val();
	       	var dcode = $("#dcode").val();
	       	var fname = $("#fname").val();
	       	var fromtime = $("#fromtime").val();
	       	var totime = $("#totime").val();
	       	
	       	if(!page)
	       		page = 0;
	       	var param={
	        	'dcode': dcode,
	          	'fname': fname,
	           	'fromtime' : fromtime,
	           	'totime': totime,
	           	'page': page
	        }
	        $.post('sharedfilesmanager.php?do=getfilesfromdb', param,function(res){
	           	var curpage = parseInt(page) + 1;
	           	$("#h_page").val(curpage);
	           	if(res==0){//查询数据为空
	           		var divObj = $("<div style='text-align:center; line-height:30px; height:30px;'></div>").appendTo($("#cons").parent().parent());
	           		$("<small></small>").text('数据已经全部加载完毕！').appendTo(divObj);
	           		$("#h_canscroll").val('0')
	           		return ;	
	           	}
	           	addData(res);//向页面中填充数据
	           	$("#h_canscroll").val(1);//数据加载完毕，回复状态
	        }, 'json');
	    }
    });
})
function addData(data){
	var page = $("#h_page").val();
	var index = (parseInt(page)-1)*20;
		
	for(var i=0; i<data.length; i++){
		index++;
		var trObj = $("<tr></tr>").appendTo($("#cons"));				
		$("<td></td>").text(index).appendTo(trObj);
		var aObj = $("<a target='_blank'></a>").attr('href', data[i]['furl']).text(data[i]['fname']);
		$("<td style='text-align:left;'></td>").append(aObj).appendTo(trObj);
		var spanObj = $("<span></span>").attr("title", data[i]['deptcode']).text(data[i]['deptname']);
		$("<td></td>").append(spanObj).appendTo(trObj);
		$("<td></td>").text(data[i]['uname']).appendTo(trObj);
		$("<td></td>").text(data[i]['uploadtime']).appendTo(trObj);
	}
}

function showdepts(){
	window.location = "deptlist.php";
}
</script>