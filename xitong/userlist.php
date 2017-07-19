<?php
include_once '../constant.php';
include_once 'usermanager.php';

// 第一次进入不做查询脚本

if(isset($_POST['name']))
{
	$name = trim($_POST['name']);
	$department = trim($_POST['department']);
	$depttype = trim($_POST['depttype']);
}else{
	$name = '';
	$department = '';
	$depttype = 0;
}

$data = getAllUsers($name, $department, $depttype);

$dnames = array();
if($depttype != 0){
	$names = queryDeptByAreacode($depttype);
	$dnames = json_decode($names, true);
}
?>
<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>用户管理</title>
	<script type="text/javascript" src="../js/jquery.min.js" ></script>
	<script type="text/javascript" src="../js/layer/layer.js" ></script>
	<!--<link rel="stylesheet" href="../css/default.css" />-->
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
		div#result table th.table_title{
			width: 25%;
		}
		div#result table tbody tr{
			cursor: pointer;		
		}
		div#result table td{
			text-align: center;
			width: 25%;
		}
	</style>
</head>
<body class="main">
	<!--<html:hidden property="processFlag" />-->
	<div id="search">
		<input type="hidden" name="hd_uid" value="" />
		<form action="userlist.php" method="post">
			<table border="0" cellpadding="6" cellspacing="1" class="tab">
				<tr>
					<td colspan="5" class="table_title">用户管理</td>
				</tr>
				<tr>
					<td class="td_title">用户姓名</td>
					<td width="180px" class="td_content">
						<input type="text" name="name" id="name" value="<?=$name?>" style="width:170px;" />
					</td>
					<td class="td_title">部门</td>
					<td width="320px" class="td_content">
						<select id="depttype" name="depttype" onchange="javascript:loadDeptByAreacode('depttype','department');">
							<option value="0" selected="selected"></option>
							<?php
							foreach ($areaCode as $key => $value) {
								$selected = '';
								if($depttype == $key)
									$selected = "selected";
								?><option value="<?=$key?>" <?=$selected?> ><?=$value?></option><?php
							}
							?>
						</select>
						<select id="department" name="department" tabIndex="4">
							<option value="0"></option>
							<?php
							foreach ($dnames as $row) {
								$selected = '';
								if($department == $row['deptid'])
									$selected = "selected";
								?><option value="<?=$row['deptid']?>" <?=$selected?> ><?=$row['deptname']?></option><?php
							}
							?>
						</select>
					</td>
					<td>
						<input type="submit" value="查&emsp;询" class="button1" tabIndex="5" >
						<input type="button" value="清&emsp;空" class="button1" onclick="javascript:doclear();" tabIndex="6" >
						<input type="button" value="添加" class="button1" onclick="hch.open('0');" />
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div style="height: 10px;"></div>
	<div id="result">
		<!--定义查询返回结果框的范围ID-->
		<table border="0" cellpadding="4" cellspacing="1" class="table01">
			<thead>
				<tr>
					<td class="table_title" >
						用户账号
					</td>
					<td class="table_title" >
						姓名
					</td>
					<td class="table_title" >
						部门
					</td>
					<td class="table_title" style="padding-right: 20px;">
						角色
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
						<td colspan="4" class="tip">
							<font size="2">没有符合条件的纪录</font>
						</td>
					</tr><?php
				}else{
					for($i=0; $i<$count; $i++){
						$classname = 'alternate_line1';
						if($i%2 == 0)
							$classname = 'alternate_line2';
						?>
						<tr class="<?=$classname?>" onclick="hch.open('<?=$data[$i]['uid']?>');">
							<td><?=$data[$i]['ucode']?></td>
							<td><?=$data[$i]['uname']?></td>
							<td><?=$data[$i]['dname']?></td>
							<td><?=$data[$i]['rname']?></td>
						</tr><?php
					}
				}	
				?>
			</tbody>
		</table>
	</div>

	<div class="show" id="tree" title="添加/修改用户信息" style="display:none;">
		<div style="padding: 10px 10px;background-color:#DEEFFF;height: 474px;">
			<table border="0" cellpadding="0" cellspacing="1" class="tab">
				<tbody>
					<tr>
						<td class="tab-td-title">用户账号</td>
						<td class="tab-td-content">
							<input type="text" name="ucode" value="" tabIndex="7">
							<span id="Star">★</span>
						</td>
						<td class="tab-td-title">用户姓名</td>
						<td class="tab-td-content">
							<input type="text" name="u_name" value="" tabIndex="8" >
							<span id="Star">★</span>
						</td>
					</tr>
					<tr>
						<td class="tab-td-title">登录密码</td>
						<td class="tab-td-content">
							<input type="password" name="u_passwd" value="" tabIndex="9" placeholder="不修改密码，请置空">
							<span id="Star">★</span>
						</td>
						<td class="tab-td-title">角色</td>
						<td class="tab-td-content">
							<select id="roleid" name="roleid" tabIndex="10"></select>
							<span id="Star">★</span>
						</td>
					</tr>
					<tr>
						<td class="tab-td-title">身份证号</td>
						<td class="tab-td-content">
							<input type="text" name="caid" tabIndex="11">
							<!--<span id="Star">★</span>-->
						</td>
						<td class="tab-td-title">性别</td>
						<td class="tab-td-content">
							<select id="sex" name="sex" tabIndex="12">
								<option value="1" selected="selected">男</option>
								<option value="2" >女</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="tab-td-title">手机</td>
						<td class="tab-td-content">
							<input type="text" name="mobile" tabIndex="13">
							<!--<span id="Star">★</span>-->
						</td>
						<td class="tab-td-title">邮箱</td>
						<td class="tab-td-content">
							<input type="text" name="email" tabIndex="14">
						</td>
					</tr>
					<tr>
						<td class="tab-td-title">部门</td>
						<td class="tab-td-content" colspan="3">
							<select id="depts" name="depts" onchange="javascript:loadDeptByAreacode('depts', 'deptid');" tabIndex="15">
								<?php
								foreach ($areaCode as $key => $value) {
									$selected = '';
									// if($depttype == $key)
									// 	$selected = "selected";
									?><option value="<?=$key?>" <?=$selected?> ><?=$value?></option><?php
								}
								?>
							</select>
							<select id="deptid" name="deptid" tabIndex="16"></select>
							<span id="Star">★</span>
						</td>
					</tr>
					<tr>
						<td class="tab-td-title">状态</td>
						<td class="tab-td-content" colspan="3">
							<select name="status"  tabIndex="17"> 
								<option value="1" selected="selected">有效</option>
								<option value="0">无效</option>
							</select>	  
							<span id="Star">★</span>
						</td>
						<!--<td class="tab-td-title">是否允许进入监察系统</td>
						<td class="tab-td-content">
							<select name="supuser"  tabIndex="18" disabled="disabled"> 
								<option value="0">不允许</option>
								<option value="1">允许</option>
								<option value="2">不限制</option>
							</select>	  
							<span id="Star">★</span>
						</td>-->
					</tr>
					<tr>
						<td class="tab-td-title">备注
						</td>
						<td class="tab-td-content" colspan="3" style="height: 90px; padding-top: 5px;">
							<textarea name="remark" rows="5" cols="70" tabIndex="19"></textarea>
						</td>
					</tr>
					<tr>
						<td class="tab-td-title">添加/修改人</td>
						<td class="tab-td-content">
							<input type="text" name="modperson" disabled="disabled" value="">
						</td>
						<td class="tab-td-title">添加/修改时间</td>
						<td class="tab-td-content">
							<input type="text" name="modtime" disabled="disabled" value="">
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
		open: function (uid) {
			this.index = layer.open({
				type: 1,
				title: $('#tree').attr("title"),
            skin: 'layui-layer-rim', //加上边框
            area: ['700px', '530px'], //宽高
            content: $("#tree")
        });
		
        uid = $.trim(uid);
        $("input[name='hd_uid']").val(uid);
		//清空所有数据
		$("input[name='ucode']").focus();
        $("input[name='ucode']").attr('disabled', false).val('');
        $("input[name='u_name']").val('');
        $("input[name='u_passwd']").val('');
        $("select[name='roleid']").val(1);
        $("input[name='caid']").val('');
        $("select[name='sex']").val(1);
        $("input[name='mobile']").val('');
        $("input[name='email']").val('');
        $("select[name='depts']").val(1);
        $("select[name='deptid']").val('');
        $("select[name='status']").val(1);
        $("select[name='supuser']").val(0);
        $("textarea[name='remark']").val('');
        $("input[name='modperson']").val("<?=$_SESSION['userName'] ?>");
        $("input[name='modtime']").val("<?=date('Y-m-d H:i:s',time()) ?>");		
        if(uid!='0'){//修改，绑定初始值 	
        	$.post("usermanager.php?do=queryUserById",{'uid':uid}, function (res) {
                //console.log(res.deptCode);
				$("input[name='ucode']").attr("disabled", true).val(res.ucode);
                $("input[name='u_name']").val(res.uname);
                $("input[name='u_passwd']").val('');
                $("select[name='roleid']").val(res.roleid);
                $("input[name='caid']").val(res.caid);
                $("select[name='sex']").val(res.sex);
                $("input[name='mobile']").val(res.mobile);
                $("input[name='email']").val(res.email);
                // 加载用户的部门
            	loadDeptByDeptid(res.deptid);
                //$("select[name='deptid']").val(res.deptid);
                $("select[name='status']").val(res.status);
                $("select[name='supuser']").val(res.supuser);
                $("textarea[name='remark']").val(res.remark);
                $("input[name='modperson']").val(res.modperson);
                $("input[name='modtime']").val(res.modtime);
            },'json');
			$("input[name='u_name']").focus();
        }else{//添加用户
			$("input[name='u_passwd']").val('9517');
			//加载部门
    		loadDeptByAreacode('depts', 'deptid');
        }
    },
    close: function () {
    	layer.close(this.index);
    },
    check:function(){
    	var ucode = $.trim($("input[name='ucode']").val());
    	var uname = $.trim($("input[name='u_name']").val());
    	var upasswd = $.trim($("input[name='u_passwd']").val());
    	var roleid = $.trim($("select[name='roleid']").val());
    	var caid = $.trim($("input[name='caid']").val());
    	var sex = $.trim($("select[name='sex']").val());
    	var mobile = $.trim($("input[name='mobile']").val());
    	var email = $.trim($("input[name='email']").val());
    	var deptid = $.trim($("select[name='deptid']").val());
    	var status = $.trim($("select[name='status']").val());
//  	var supuser = $.trim($("select[name='supuser']").val());
		var supuser = 0;
    	var remark = $.trim($("textarea[name='remark']").val());
    	
    	if (!ucode) {
    		layer.msg("用户登录账号不能为空！");
    		return false;
    	}
    	
    	if (!uname) {
    		layer.msg("用户名称不能为空！");
    		return false;
    	}
    	
    	var uid=$("input[name='hd_uid']").val();
    	if(upasswd.length == 0 && uid=='0')
    		upasswd = '123';
    	var param={
    		'uid': uid,
    		'ucode': ucode,
    		'uname': uname,
    		'upasswd':upasswd,
    		'sex':sex,
    		'mobile':mobile,
    		'email':email,
    		'caid':caid,
    		'status':status,
    		'supuser':supuser,
    		'remark':remark,
    		'deptid':deptid,
    		'roleid':roleid
    	};
        if(uid=='0'){//添加
        	$.post("usermanager.php?do=userAdd",param, function (res) {
        		if(res==0){
        			layer.msg("添加失败！");
        		}else if(res==2){
        			layer.msg("该账号已经存在，请重新输入！");
        			$("input[name='ucode']").focus();
        		}else if(res == 1){
        			layer.msg("添加成功！");
        			setTimeout('refresh()', 1000);
        		}
        	},'json');
        }else{//修改
        	$.post("usermanager.php?do=userModify",param, function (res) {
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

function loadDeptByAreacode(pObj, cObj){
	var selval = $('#'+pObj).val();
	$('#'+cObj).find("option[value!='0']").remove();
	if(selval == 0)
		return;
	$.post("usermanager.php?do=queryDeptByAreacode", {'areaCode':selval}, function (res) {
		for(var i=0; i<res.length; i++){
			var optionObj = $("<option></option>").val(res[i]['deptid']).text(res[i]['deptname']);
			optionObj.appendTo($('#'+cObj));
		}
	},'json');
}

function loadDeptByDeptid(deptid){
	$.post("usermanager.php?do=queryDeptByDeptId", {'deptid':deptid}, function (res) {
		if(res == 0)
			return;
		var areacode = res['areaCode'];
		$("#depts").val(areacode);
		$('#deptid').find("option[value!='0']").remove();
		if(res['depts'] != null){
			for(var i=0; i<res['depts'].length; i++){
				var optionObj = $("<option></option>").val(res['depts'][i]['deptid']).text(res['depts'][i]['deptname']);
				if(res['depts'][i]['deptid'] == deptid)
					$(optionObj).prop('selected', true);
				$(optionObj).appendTo($('#deptid'));
			}
		}
	},'json');
}

function doclear(){
	$("#name").val('');
	$("#depttype").val(0);
	$("#department").val(0);

	refresh();
}

function refresh(){
	document.forms[0].submit();
}

$(function(){
	//加载角色         
    var roleObj = $("input[name='roleid']");
    if($("select[name='roleid'] option").length == 0){	//第一次加载所有的role
      	$.post("usermanager.php?do=queryRoles", function(res){
       		for(var i=0; i<res.length; i++){
       			var optionObj = $("<option></option>").val(res[i]['roleid']).text(res[i]['role']);
       			$("#roleid").append(optionObj);
       		}
       	},'json');     
    }
})
</script>