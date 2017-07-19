<?php
include_once '../mysql.php';

session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}

$uid=trim($_SESSION['userID']);

if(empty($_POST['oldpasswd'])){
	$errorinfo1='';
	$errorinfo2='';
	$oldpasswd='';
	$newpasswd='';
	$repasswd='';
	$result=0;
}else{
	$result=0;
	$oldpasswd=$_POST['oldpasswd'];
	$newpasswd=$_POST['newpasswd'];
	$repasswd=$_POST['repasswd'];
	$errorinfo='';
	if($newpasswd != $repasswd){
		$errorinfo2 = "新密码两次输入不一致，请重新输入！";
		$errorinfo1 = '';
		$result=2;
	}else{
		$pdo = new mysql;
		$param=array();
		$param[':uid'] = $uid;
		$param[':upasswd'] = md5($oldpasswd);
		$sql = "select count(uid) from user where uid=:uid and upasswd=:upasswd;";
		$res = $pdo->getFirst($sql, $param);
		if($res == 0){
			$errorinfo1 = "旧密码输入错误，请重新输入！";
			$errorinfo2 = '';
			$result=3;
		}else{
			$sql = "update user set upasswd=:upasswd where uid=:uid;";
			$param[':upasswd'] = md5($newpasswd);
			$res = $pdo->update($sql, $param);
			if($res == 0){
				$errorinfo1 = "密码修改失败！";
				$errorinfo2 = '';
			}else if($res == 1){
				$errorinfo1 = "";
				$errorinfo2 = '';
				$result=1;
			}
		}		
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<title>修改密码</title>
	<script type="text/javascript" src="../js/jquery.min.js" ></script>
	<script type="text/javascript" src="../js/layer/layer.js" ></script>
	<link rel="stylesheet" href="../js/layer/skin/layer.css" />
	<link rel="stylesheet" href="../css/common.css" />
	<style type="text/css">
		td.left{width:50%; text-align: right; font-weight: bold; height:30px;}
	</style>
</head>
<body class="main">
	<div style="width:100%; height: 10px;"></div>
	<div id="search">
		<form action="passwdmod.php" method="post">
			<table border="0" cellpadding="6" cellspacing="1" class="tab">
				<tr>
					<td colspan="2" class="table_title">
						修改密码
					</td>
				</tr>
				<tr>
					<td class="left">
						旧密码
					</td>
					<td class="td_content">
						<input type="password" name="oldpasswd" value="<?=$oldpasswd?>" class="htmlText" />
						<small style="color: red;"><?=$errorinfo1?></small>
					</td>
				</tr>
				<tr>
					<td class="left">
						新密码
					</td>
					<td class="td_content">
						<input type="password" name="newpasswd" value="<?=$newpasswd?>" class="htmlText" />
						<small style="color: red;"><?=$errorinfo2?></small>
					</td>
				</tr>
				<tr>
					<td class="left">
						确认密码
					</td>
					<td class="td_content">
						<input type="password" name="repasswd" value="<?=$repasswd?>" class="htmlText"  />
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center; padding-left: 86px;line-height:30px;">
						<input type="submit" value="修&emsp;改" class="button1" tabIndex="5" >
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div class="show-dept" id="layertip" style="display:none;">
  		<div style="padding: 18px 10px 10px 10px;height:auto; text-align: center;">
	  		<small style="color: red;"><b>密码修改成功，请牢记新密码！</b></small>
		</div>
	</div>
</body>
</html>
<script type="text/javascript">
	$(function(){
		var res = <?=$result?>;
		if(res == 1){
			hch.showtip();
			setTimeout('refresh()', 1000);
		}else if(res == 2){
			$("input[name='newpasswd']").focus();
		}else if(res == 3){
			$("input[name='oldpasswd']").focus();
		}
	})
	var hch = {
		showtip:function(){
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
		}
	}
	function refresh(){
		window.location = "passwdmod.php";
	}
</script>