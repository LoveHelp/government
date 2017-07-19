<?php
include_once "xitong/smsmanager.php";
include_once 'constant.php';

$title = "";
$txtTel = "";
$telCount = 0;

$mt = empty($_REQUEST['mt'])?0:trim($_REQUEST['mt']);
$tid = empty($_REQUEST['tid'])?0:trim($_REQUEST['tid']);
$data = array('title'=>'', 'tels'=>array());
if(!empty($mt)){
	if($mt == 'taskreview')
		$data = getcontactsbytaskid($tid);
	else if($mt == 'taskmanager')
		$data = getcontactsbytaskid($tid);
	else if($mt == 'tasktarget'){
		$data = getcontactsbytargetid($tid);
		$title = "关于项目“" . $data['title'] . "”，请尽快提交工作安排。";
		unset($data['title']);
	}else if($mt == 'taskreviewall'){
		$data = getcontactsbydepts($tid);
		$title = "请尽快反馈工作完成情况报告";
	}else if($mt == 'taskmanagerall'){
		$data = getcontactsbydepts($tid);
		$title = "请尽快接收已分配工作";
	}
		
	if(!empty($data['title']))
		$title = "关于项目“" . $data['title'] . "”，请尽快提交工作完成情况报告。";
	if(!empty($data)){
		$i = 0;
		$preDept="";
		foreach($data['tels'] as $row){
			//默认每个部门只选一个联系人
			if($preDept != $row['deptname']){
				if($i > 0){
					$txtTel .= ";";
				}
				$txtTel .= $row['name'] . "[" . $row['deptname'] . "](" . $row['tel'] . ")";
				$i++;
				$preDept = $row['deptname'];
			}
		}
		if(!empty($txtTel))
			$txtTel .= ";";
	}
	$telCount = $i;
}
	
?>
<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>短信群发</title>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/layer/layer.js" ></script>
	<link rel="stylesheet" href="css/common.css" />
	<style type="text/css">
		span#spInfo{
			font-size:12px;
			color:lightgray;
		}
		span#spTelCount{
			color:darkgray;
		}
	</style>
</head>
<body class="main" style="min-width: 600px; min-height: 500px;">
	<div class="title">短信群发</div>
	<div style="height:10px;"></div>
	<div class="content">
		<div class="left">
			<table border="0" class="tab">
				<tr>
					<td class="tab-td-title">收信人</td>
					<td class="tab-td-content" style="height:100px; padding: 5px 5px 5px 5px;">
						<textarea id="txtTel" style="overflow-y: auto;" placeholder="最多200个号码，号码间用分号（英文）分隔" title="最多200个号码，号码间用分号（英文）分隔"><?=$txtTel?></textarea>
					</td>
				</tr>
				<tr>
					<td class="tab-td-title">短信内容</td>
					<td class="tab-td-content" style="height: 100px; padding-top: 5px;">
						<textarea name="remark" style="overflow-y: auto;" placeholder="最多200个汉字" title="最多200个汉字"><?=$title?></textarea>
					</td>
				</tr>
				<tr>
					<td class="tab-td-title">短信模板</td>
					<td class="tab-td-content" style="line-height: 40px;">
						<select name="smstype" onchange="selectTmp(this);">
							<option value="0">选择模板</option>
							<option value="1">任务转办短信模板</option>
							<option value="2">任务反馈短信模板</option>
							<option value="3">督查通知短信模板</option>
							<option value="3">督查通知短信模板</option>
						</select>&emsp;&emsp;
						<span id="spInfo">已选中
						<span id="spTelCount"><?=$telCount ?></span>
						位联系人</span>
					</td>
				</tr>
				<tr>
					<td colspan=" 4 " id="searchCon">
						<input type="button" value="发送 " class="button2" onclick="hch.sendsms();">
					</td>
				</tr>
			</table>
		</div>
		<div class="right ">
			<div class="top">
				<ul style="margin: 0px; padding: 0px;">	
					<li class="list_top">
						<ul class="areacode">
							<li style="width: 8%;"><input type="checkbox" value="1" onchange="selContactByAreaCode(this)" /></li>
							<li style="width: 87%;" onclick="showLevels('1', this);">市政府办组成科室</li>
							<li id="icon1" class="icon" style="5%" onclick="hideList('1', this)">+</li>
						</ul>
						<ul id='depttype1' class="dept_list" style="display: none;">
							<li id="level11">
								<input type='checkbox' onchange='selContactByLevel(this);' value="11" />
								<span onclick="showContactByLevel('11', this)">办公室领导</span>
							</li>
							<li id="level12">
								<input type='checkbox' onchange='selContactByLevel(this);' value="12" />
								<span onclick="showContactByLevel('12', this)">科室负责人</span>
							</li>
							<li id="level13">
								<input type='checkbox' onchange='selContactByLevel(this);' value="13" />
								<span onclick="showContactByLevel('13', this)">科室成员</span>
							</li>
						</ul>
					</li>
					<li class="list_top">
						<ul class="areacode">
							<li style="width: 8%;"><input type="checkbox" value="2" onchange="selContactByAreaCode(this)" /></li>
							<li style="width: 87%;" onclick="showLevels('2', this);">垂直单位</li>
							<li id="icon2" class="icon" style="5%" onclick="hideList('2', this)">+</li>
						</ul>
						<ul id='depttype2' class="dept_list" style="display: none;">
							<li id="level21">
								<input type='checkbox' onchange='selContactByLevel(this);' value="21" />
								<span onclick="showContactByLevel('21', this)">主要领导</span>
							</li>
							<li id="level22">
								<input type='checkbox' onchange='selContactByLevel(this);' value="22" />
								<span onclick="showContactByLevel('22', this)">分管领导</span>
							</li>
							<li id="level23">
								<input type='checkbox' onchange='selContactByLevel(this);' value="23" />
								<span onclick="showContactByLevel('23', this)">办公室主任</span>
							</li>
							<li id="level24">
								<input type='checkbox' onchange='selContactByLevel(this);' value="24" />
								<span onclick="showContactByLevel('24', this)">具体负责人</span>
							</li>
						</ul>
					</li>
					<li class="list_top">
						<ul class="areacode">
							<li style="width: 8%;"><input type="checkbox" value="3" onchange="selContactByAreaCode(this)" /></li>
							<li style="width: 87%;" onclick="showLevels('3', this);">市直单位</li>
							<li id="icon3" class="icon" style="5%" onclick="hideList('3', this)">+</li>
						</ul>
						<ul id='depttype3' class="dept_list" style="display: none;">
							<li id="level31">
								<input type='checkbox' onchange='selContactByLevel(this);' value="31" />
								<span onclick="showContactByLevel('31', this)">主要领导</span>
							</li>
							<li id="level32">
								<input type='checkbox' onchange='selContactByLevel(this);' value="32" />
								<span onclick="showContactByLevel('32', this)">分管领导</span>
							</li>
							<li id="level33">
								<input type='checkbox' onchange='selContactByLevel(this);' value="33" />
								<span onclick="showContactByLevel('33', this)">办公室主任</span>
							</li>
							<li id="level34">
								<input type='checkbox' onchange='selContactByLevel(this);' value="34" />
								<span onclick="showContactByLevel('34', this)">具体负责人</span>
							</li>
						</ul>
					</li>
					<li class="list_top">
						<ul class="areacode">
							<li style="width: 8%;"><input type="checkbox" value="4" onchange="selContactByAreaCode(this)" /></li>
							<li style="width: 87%;" onclick="showLevels('4', this);">县区</li>
							<li id="icon4" class="icon" style="5%" onclick="hideList('4', this)">+</li>
						</ul>
						<ul id='depttype4' class="dept_list" style="display: none;">
							<li id="level41">
								<input type='checkbox' onchange='selContactByLevel(this);' value="41" />
								<span onclick="showContactByLevel('41', this)">县（区）长</span>
							</li>
							<li id="level42">
								<input type='checkbox' onchange='selContactByLevel(this);' value="42" />
								<span onclick="showContactByLevel('42', this)">分管领导</span>
							</li>
							<li id="level43">
								<input type='checkbox' onchange='selContactByLevel(this);' value="43" />
								<span onclick="showContactByLevel('43', this)">办公室主任</span>
							</li>
							<li id="level44">
								<input type='checkbox' onchange='selContactByLevel(this);' value="44" />
								<span onclick="showContactByLevel('44', this)">督查室主任</span>
							</li>
							<li id="level45">
								<input type='checkbox' onchange='selContactByLevel(this);' value="45" />
								<span onclick="showContactByLevel('45', this)">督查室人员</span>
							</li>
						</ul>
					</li>	
				</ul>
			</div>
		</div>
	</div>
	<div class="show-dept" id="layertip" style="display:none;">
  		<div style="padding: 18px 10px 10px 10px;height:auto; text-align: center;">
	  		<small id="msg" style="color: blue; font-weight: bold;">正在发送信息，请稍等。。。</small>
		</div>
	</div>
</body>
</html>
<script type="text/javascript">
function showLevels(key, obj){
	//关闭按类型选择的列表
	if($("#depttype"+key).css('display') == 'none'){
		//隐藏所有的部门列表
		$('.dept_list').css('display', 'none');
		$('li.icon').text("+").attr('title', '');
		//显示目标部门列表
		$("#depttype"+key).css('display', 'block');
		$("#icon"+key).text('-').attr('title', '折叠');
	}else{
		//隐藏所有的部门列表
		$('.dept_list').css('display', 'none');
		$('li.icon').text("+").attr('title', '');
	}
}

function showContactByLevel(key, obj){
	var url = 'xitong/smsmanager.php?do=getcontactsbylevel';
	var parentObj = $(obj).parent();
	var preObj = $(obj).prev();
	//数据没有加载时，加载数据
	//数据已经加载过，则直接显示数据
	if($(parentObj).children('ul.contact_list').length == 0){
		//加载指定部门的联系人列表
		$.post(url, {'level': key}, function(res){
			if(res == 0){
				var divObj = $("<div class='contact_list' style='height:25px;'></div>").text('没有查询到任何记录！');
				$(divObj).appendTo($(obj));
			}else{
				var len = res.length;
				ulObj = $("<ul class='contact_list'></ul>").attr('id', 'contact'+key);
				var telist = $("#txtTel").val();
				for(var i=0; i<len; i++){
					var liObj = $("<li></li>");
					var tel = res[i]['tel'];
					var cb = $("<input type='checkbox' onchange='selContact(this);'/>").val(tel);
					$(cb).appendTo($(liObj));
					if(telist.indexOf(tel) > 0)
						$(cb).prop('checked', true);
					$("<span></span>").text(res[i]['name']+'['+res[i]['deptname']+']').appendTo($(liObj));
					$(liObj).appendTo($(ulObj));	
				}
				$(parentObj).children('.contact_list').empty();
				$(ulObj).appendTo($(parentObj));
				//根据父节点是否选中，改变子节点的状态
				if($(preObj).prop('checked')){
					$("#contact"+key+">li>input[type='checkbox']").prop('checked', true);
					$("#contact"+key+">li>input[type='checkbox']").trigger('change');
				}
			}
		}, 'json');
	}else if(!preObj.prop('checked')){//未选中则，则交替显示
		if($("#contact"+key).css('display') == 'block')
			$("#contact"+key).css('display', 'none');
		else
			$("#contact"+key).css('display', 'block');
	}else if(preObj.prop('checked')){
		$("#contact"+key).css('display', 'block');
	}
}
function selContactByLevel(obj){
	var key = $(obj).val();
	if($("#contact"+key).length == 0){
		//加载列表
		showContactByLevel(key, $(obj).next("span"));
	}else{
		//列表存在的话，显示列表
		$("#contact"+key).css('display', 'block');
		//选中联系人
		$("#contact"+key+">li>input[type='checkbox']").prop('checked', $(obj).prop('checked'));
		$("#contact"+key+">li>input[type='checkbox']").trigger('change');
	}
}
//隐藏/显示列表
function hideList(key, obj){
	var txt = $(obj).text();
	if(txt == '-'){
		$("#depttype"+key).css('display', 'none');
		$(obj).text('+');
		$(obj).attr('title', '');
	}
}
//选择联系人
function selContact(obj){
	//var tels = $("#tel_list").val();
	var txtTel = $("#txtTel").val();
	var tel = $(obj).val();
	var target = $(obj).next().text() + "("+tel+");";
	if($(obj).prop('checked')){
		if(txtTel.indexOf(target) < 0){
			//tels += tel+",";
			txtTel += target;
		}
	}else{
		if(txtTel.indexOf(target) >= 0){
			//tels = tels.replace(tel+",", '');
			txtTel = txtTel.replace(target, "");
		}
	}
	//$("#tel_list").val(tels);
	$("#txtTel").val(txtTel);
}
//根据部门分类选择联系人
function selContactByAreaCode (obj) {
	//隐藏所有的部门列表
	$('.dept_list').css('display', 'none');
	$('li.icon').text("+").attr('title', '');
	//显示选择的列表
	var key = $(obj).val();
	$("#depttype"+key).css('display', 'block');
	$("#icon"+key).text('-').attr('title', '折叠');
	//改变联系人状态
	$("#depttype"+key+">li>input[type='checkbox']").prop('checked', $(obj).prop('checked'));
	$("#depttype"+key+">li>input[type='checkbox']").trigger('change');
}

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
	sendsms:function(){
		var url = 'xitong/smsmanager.php?do=sendsms';
		var content = $("textarea[name='remark']").val().trim();
		var txtTel = $("#txtTel").val();
		var tels = telTrim(txtTel);
		if(!content){
			layer.msg('请输入短信内容！');
			return false;
		}
		if(!tels){
			layer.msg('请选择短信接收人！');
			return false;
		}
		var res = check_phone(tels);
		if(res != 0){
			layer.msg("号码["+res+"]不合法，手机号必须是11位数字！");
			findError(res);
			return false;
		}
		var param = {
			'content':content,
			'tels':tels
		}
		// layer.msg("暂未开通账户，此功能稍后开通！");
		hch.onexporttip();
		$.post(url, param, function(res){
			$("#msg").text(res['msg']);
			setTimeout("back()", 800);			
		}, 'json');
	}
}
function telTrim(tels){
	tels = tels.trim();
	tels = tels.replace(/\D/g, ';');
	tels = tels.replace('；', ';');
	var arr = tels.split(';');
	var len = arr.length;
	tels="";
	for(var i=0; i<len; i++){
		if(!arr[i])	//空继续
			continue;
		if(tels.length > 0)
			tels += ',';
		tels += arr[i].trim();
	}
	
	return tels;
}

function check_phone(tels){
	var arr = tels.split(',');
	var len = arr.length;
	var reg = /(^1[3|4|5|6|7|8|9][0-9]{9}$)/;
	for(var i=0; i<len; i++)
	{
		if(!arr[i])//空继续
			continue;
		if(!reg.test(arr[i]))
			break;
	}
	if(i>=len)
		return 0;
	return arr[i];
}

function findError(txt){
	var txtTel = $("#txtTel").val();
	txtTel = txtTel.replace('）', ')');
	txtTel = txtTel.replace('（', '(');
	txtTel = txtTel.replace('；', ';');
	$("#txtTel").val(txtTel);
	var tar1 = txt + ")";
	var tar2 = txt + ";";
	var tar3 = ";" + txt;
	var index=-1;
	if((index=txtTel.indexOf(tar1))> -1){
		selError(index, txt.length);
	}else if((index=txtTel.indexOf(tar2))> -1){
		selError(index, txt.length);
	}else if((index=txtTel.indexOf(tar3))> -1){
		selError(index+1, txt.length);
	}	
}

function selError(start, length){
	var end = start + length;
	var obj = document.getElementById("txtTel");
	var userAgent = navigator.userAgent;
	//if(userAgent.indexOf("MSIE") > -1){//IE浏览器  
	if(obj.createTextRange){
		var range = obj.createTextRange();                
		range.moveEnd("character",end);  
		range.moveStart("character", start);  
		range.select();  
	}else{//非IE浏览器  
		obj.setSelectionRange(start, end);
		obj.focus();		
	}  
}

function back(){
	layer.closeAll();
	parent.location.reload();
}

function selectTmp(obj){
	var myDate = new Date(); 
	var year = myDate.getFullYear();
	var day = myDate.getDate();
	var val = $(obj).val();
	var msg = '〔'+year+'〕'+day+'号 关于xxxxxxxxxxxx已发布，请注意查收';
	if(val == 1){
		$("textarea[name='remark']").val("任务xxxxxxxxxxxx已经转办，请注意查收");
	}else if(val == 2){
		$("textarea[name='remark']").val("请尽快提交关于工作xxxxxxxxxxxx的完成情况报告");
	}else if(val == 3){
		$("textarea[name='remark']").val(msg);
	}else if(val == 4){
		$("textarea[name='remark']").val(msg);
	}
}
</script>