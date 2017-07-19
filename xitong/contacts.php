<?php

include_once '../constant.php';
include_once 'contactmanager.php';

$name = empty($_POST['name']) ? trim('') : trim($_POST['name']);
$dtype = empty($_POST['dtype']) ? 0 : trim($_POST['dtype']);
$dept = empty($_POST['depts']) ? 0 : trim($_POST['depts']);

$dnames = array();
if($dtype != 0){
	$names = queryDeptByAreacode($dtype);
	$dnames = json_decode($names, true);
} 

$data = getAllContacts($name, $dtype, $dept);

?>
<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>通讯录管理</title>
	<script type="text/javascript" src="../js/jquery.min.js" ></script>
	<script type="text/javascript" src="../js/layer/layer.js" ></script>
	<link rel="stylesheet" href="../css/common.css" />
	<style type="text/css">
		tr {
			line-height: 32px;
			height:32px;
		}
		select {
			height: 25px;
			width: 154px;
		}
		div#result table tbody tr{
			cursor: pointer;		
		}
		div#result table td{
			text-align: center;
		}
	</style>
</head>
<body class="main">
	<!--<html:hidden property="processFlag" />-->
	<div id="search">
		<input type="hidden" name="hd_uid" value="" />
		<form action="contacts.php" method="post">
			<table border="0" cellpadding="6" cellspacing="1" class="tab">
				<tr>
					<td colspan="5" class="table_title">
						通讯录管理
					</td>
				</tr>
				<tr>
					<td class="td_title">
						用户姓名
					</td>
					<td width="180px" class="td_content">
						<input type="text" name="name" class="htmlText" value="<?=$name?>" style="width:170px;"/>
					</td>
					<?php
						$colspan = 3;
						if($roleid < 3){
							$colspan = 1;
					?>
					<td width="120px" class="td_title">
						部门
					</td>
					<td width="320px" class="td_content">
						<select id="dtype" name="dtype" onchange="javascript:loadDeptByAreacode(this);" tabIndex="3">
							<option value="0" selected="selected"></option>
							<?php
							foreach ($areaCode as $key => $value) {
								$selected = '';
								if($dtype == $key)
									$selected = "selected";
								?><option value="<?=$key?>" <?=$selected?> ><?=$value?></option><?php
							}
							?>
						</select>
						<select id="depts" name="depts" tabIndex="4">
							<option value="0"></option>
							<?php
							foreach ($dnames as $row) {
								$selected = '';
								if($dept == $row['deptid'])
									$selected = "selected";
								?><option value="<?=$row['deptid']?>" <?=$selected?> ><?=$row['deptname']?></option><?php
							}
							?>
						</select>
					</td>
					<?php
					}
					?>
					<td colspan="<?=$colspan?>">
						<input type="submit" value="查&emsp;询" class="button1">
						<input type="button" value="添加" class="button1" onclick="hch.open(0,0,0,0,'','','');" />
						<input type="button" value="导出" class="button1" onclick="exporttel();" />
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div style="height:10px;"></div>
	<div id="result">
		<!--定义查询返回结果框的范围ID-->
		<table border="0" cellpadding="4" cellspacing="1" class="table01">
			<thead>
				<tr>
					<td class="table_title" style="width:40px;">序号</td>
					<td class="table_title" style="width:15%;" >
						姓名
					</td>
					
					<td class="table_title" style="width:20%;" >
						手机号
					</td>
					<td class="table_title" style="width:20%;">
						微信
					</td>
					<td class="table_title" style="width:15%;">
						职务
					</td>
					<td class="table_title">
						部门
					</td>
				</tr>
			</thead>
			<tbody>
				<?php
				$count = 0;
				if(!empty($data))
					$count = sizeof($data); 
				if($count == 0){
					?>
					<tr class="alternate_line1">
						<td colspan="6" class="tip">
							<font size="2">没有符合条件的纪录</font>
						</td>
					</tr><?php
				}else{
					for($i=0; $i<$count; $i++){
						$level = $data[$i]['level'];
						$areacode = $data[$i]['areacode'];
						if(empty($duty[$areacode][$level])){
							if($areacode == 1)
								$level = $areacode."3";
							else if($areacode == 4)
								$level = $areacode."5";
							else
								$level = $areacode."4";
						}
						$duty_txt = $duty[$areacode][$level];
						?>
						<tr onclick="hch.open(<?=$data[$i]['id']?>,<?=$areacode?>, <?=$level?>, <?=$data[$i]['deptid']?>, '<?=$data[$i]['name']?>', '<?=$data[$i]['tel']?>', '<?=$data[$i]['weixin']?>');">
							<td><?=$i+1?></td>
							<td><?=$data[$i]['name']?></td>
							<td><?=$data[$i]['tel']?></td>
							<td><?=$data[$i]['weixin']?></td>
							<td><?=$duty_txt?></td>
							<td><?=$data[$i]['deptname']?></td>
						</tr><?php
					}
				}	
				?>
			</tbody>
		</table>
	</div>

	<div class="show" id="tree" title="添加/修改通讯录信息" style="display:none;">
		<div style="padding: 10px 10px;background-color:#DEEFFF;height: 154px;">
			<table border="0" cellpadding="0" cellspacing="1" class="tab">
				<tbody>
					<tr>
						<td class="tab-td-title">用户姓名</td>
						<td class="tab-td-content">
							<input type="text" name="name2">
							<span id="Star">★</span>
						</td>
						<td class="tab-td-title">手机</td>
						<td class="tab-td-content">
							<input type="text" name="tel">
							<span id="Star">★</span>
						</td>
					</tr>
					<tr>
						<td class="tab-td-title">部门</td>
						<td class="tab-td-content" colspan="3">
							<select id="depttype" name="depttype" onchange="javascript:loadDeptByAreacode(this);">
								<?php
								foreach ($areaCode as $key => $value) {
									?><option value="<?=$key?>"><?=$value?></option><?php
								}
								?>
							</select>
							<select id="deptid" name="deptid" tabIndex="16"></select>
							<span id="Star">★</span>
						</td>
					</tr>
					<tr>
						<td class="tab-td-title">职务</td>
						<td class="tab-td-content">
							<select id="duty" name="duty">
							<!--<option value="1">一把手</option>
							<option value="2">办公室领导</option>
							<option value="3">办公室主任</option>
							<option value="4" selected="selected">科员</option>-->
							</select>
							<span id="Star">★</span>
						</td>
						<td class="tab-td-title">微信号</td>
						<td class="tab-td-content">
							<input type="text" name="weixin">
							<!-- <span id="Star">★</span> -->
						</td>
					</tr>
					<tr>
						<td colspan="4" id="searchCon"><!--定义好摆放按钮的TD的ID -->
							<input type="submit" value="提 交" style="cursor:pointer" class="button1" onclick="hch.check();"  tabIndex="20">
							<input type="button" value="关闭窗口" style="cursor:pointer" class="button1" onclick="hch.close();" tabIndex="21">     
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</body>
</html>
<script type="text/javascript">
	var hch = {
		open: function (id, acode, level, deptid, name, tel, weixin) {
			this.index = layer.open({
				type: 1,
				title: $('#tree').attr("title"),
            skin: 'layui-layer-rim', //加上边框
            area: ['600px', '210px'], //宽高
            content: $("#tree")
        });
		$("input[name='name2']").focus();
        id = $.trim(id);
        $("input[name='hd_uid']").val(id);
        $("input[name='name2']").val(name);
        $("input[name='tel']").val(tel);
        $("input[name='weixin']").val(weixin);
        //加载部门
        var areacode = acode;
        var roleid = <?=$roleid?>;
        //非管理员
        if(roleid>2 && roleid<6)
			deptid = <?=$deptid?>;
        if(deptid!=0){
        	$.post('contactmanager.php?do=queryDeptsBydeptid', {'deptid':deptid}, function(res){
        		if(res == 0)
					return;
				areacode = res['areaCode'];
				$("#depttype").val(areacode);
				$('#deptid').empty();
				if(res['depts'] != null){
					for(var i=0; i<res['depts'].length; i++){
						var optionObj = $("<option></option>").val(res['depts'][i]['deptid']).text(res['depts'][i]['deptname']);
						if(res['depts'][i]['deptid'] == deptid)
							$(optionObj).prop('selected', true);
						$(optionObj).appendTo($('#deptid'));
					}
				}
				//加载职务
        		loadDuty(areacode, level);
        		//非管理员，则不可操作
        		if(roleid>2 && roleid<6){
        			$("#depttype").prop('disabled', true);
        			$('#deptid').prop('disabled', true);
        		}
			},'json');
        }else{
			loadDeptByAreacode(document.getElementById("depttype"));
		}
    },
    close: function () {
    	layer.close(this.index);
    },
    check:function(){
    	var name = $("input[name='name2']").val();
        var tel = $("input[name='tel']").val();
        var weixin = $("input[name='weixin']").val();
        var duty = $("#duty").val();
    	
    	if (!name) {
    		layer.msg("姓名不能为空！");
    		return false;
    	}
    	var name_reg = /^[\u4E00-\u9FA5]{2,4}$/;
    	if(!name_reg.test(name)){
    		layer.msg('用户名只能是2到4个汉字！');
    		return false;
    	}
    	
    	if (!tel) {
    		layer.msg("手机号不能为空！");
    		return false;
    	}
    	var tel_reg = /^1[34578]\d{9}$/;
    	if(!tel_reg.test(tel)){
    		layer.msg("手机号码只能是11位有效号码！");
    		return false;
    	} 
    	
    	var id=$("input[name='hd_uid']").val();
		var deptid = $("#deptid").val();
    	var param={
    		'id': id,
    		'duty': duty,
			'deptid': deptid,
    		'name': name,
    		'tel': tel,
    		'weixin':weixin
    	};
        if(id=='0'){//添加
        	$.post("contactmanager.php?do=contactAdd",param, function (res) {
        		if(res==0){
        			layer.msg("添加失败！");
        		}else if(res==2){
        			layer.msg("信息已经存在，请重新输入！");
        			$("input[name='name']").focus();
        		}else if(res == 1){
        			layer.msg("添加成功！");
        			setTimeout('refresh()', 1000);
        		}
        	},'json');
        }else{//修改
        	$.post("contactmanager.php?do=contactModify",param, function (res) {
        		if(res==1){
        			layer.msg("修改成功！");
        			setTimeout('refresh()', 1000);
        		}else{
        			layer.msg("修改失败！");
        		}
        	},'json');
        }
    }
}
function exporttel(){
		$.ajax({
			type:'get',
			url:"exporttel.php",				
			success:function(result){				
				window.location.href = result;				
			}
		}); 
}
function refresh(){
	document.forms[0].submit();
}

function loadDeptByAreacode(obj){
	var selval = $(obj).val();
	var nobj = $(obj).next();
	$(nobj).empty();
	if(selval == 0)
		return;
	$.post("contactmanager.php?do=queryDeptByAreacode", {'areacode':selval}, function (res) {
		if(res.length>0){
			if($(nobj).prop('id')!='deptid')
				$(nobj).append('<option value="0" selected="selected"></option>');
		}
		for(var i=0; i<res.length; i++){
			var optionObj = $("<option></option>").val(res[i]['deptid']).text(res[i]['deptname']);
			optionObj.appendTo($(nobj));
		}
	},'json');
	
	loadDuty(selval, 0);
}
function loadDuty(key, dutyid){
	var duty_arr = <?=$json_duty?>;
	var duty = duty_arr[key];
	var obj = $("#duty");
	$(obj).empty();
	$.each(duty, function(key, txt) {
		var optionObj = $("<option></option>").val(key).text(txt);
		optionObj.appendTo($(obj));
	});
	if(dutyid != 0)
		$(obj).val(dutyid);
}
</script>