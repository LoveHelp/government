<?php
// header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
include_once 'mysql.php';

header("Cache-Control: no-cache");
header("Pragma: no-cache");

session_start();
$errmsg = null;
$username = "";
$upasswd = "";
if(!isset($_SESSION['timestamp'])){
	$_SESSION['timestamp'] = md5(mktime());
}
else if (isset($_POST['timestamp']) 
	&& $_SESSION['timestamp'] == $_POST['timestamp']) {
	$_SESSION['timestamp'] = md5(mktime());

if(isset($_POST["username"]) && isset($_POST["upasswd"]))
{
	$username = $_POST["username"];
	$upasswd = $_POST["upasswd"];
	if(empty($username) && empty($upasswd)){
		$errmsg = "请输入用户名和密码";
	}else if(empty($username)){
		$errmsg = "请输入用户名";
	}else if(empty($upasswd)){
		$errmsg = "请输入密码";
	}
		//输入用户名与密码，进行登录check
	if(is_null($errmsg)){
		$sql = "select uid, ucode, uname, roleid, deptid from user where ucode = ? and upasswd = ? and status = 1;";
		$link = new mysql();
		$param = array($username, md5($upasswd));
		$res = $link->getRow($sql, $param);
		if(!$res){
			$errmsg = "用户名或密码输入错误，请重新输入";
		}else{
			$_SESSION['userID'] = $res['uid'];
			$_SESSION['userCode'] = $res['ucode'];
			$_SESSION['userName'] = $res['uname'];
			$_SESSION['userRoleID'] = $res['roleid'];
			$_SESSION['userDeptID'] = $res['deptid'];
			$deptsql="select deptName from dept where deptId=?";
			$depts=$link->getRow($deptsql,array($res['deptid']));
			$_SESSION['userDeptName']=$depts['deptName'];	
			$errmsg = "登录成功";
			header('Location:default.php');
			exit;
		}
	}
}
}

?>
<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>南阳市政务督查管理平台</title>
	<script type="text/javascript" src="js/jquery.min.js" ></script>
	<link rel="stylesheet" href="css/index.css" />
</head>
<style>
img{border:0;}
</style>
<body>
	<div class="top"></div>
	<div class="middle">
		<div class="logo"><img src="img/title.jpg" /></div>
		<div class="input">
			<form class="loginForm" action="index.php" method="POST">
				<table>
					<tr>
						<td colspan="2" class="errLabel">
							<label id="err">
								<?php echo $errmsg ?>
							</label>
							<input type="hidden" name="timestamp" value="<?=$_SESSION['timestamp'] ?>" >
						</td>
					</tr>
					<tr>
						<!--<td class="iconUser"></td>-->
						<td class="texttd"><img src="img/1.jpg">&nbsp;</td>
						<td class="tdinput">
							<input style="width: 400px" type="text" name="username" id="uname" value="<?=$username ?>" tabIndex="1">
						</td>
					</tr>
					<tr>
						<!--<td class="iconPwd"></td>-->
						<td class="texttd"><img src="img/2.jpg">&nbsp;</td>
						<td class="tdinput">
							<input type="password" name="upasswd" id="upwd" value="<?=$upasswd ?>" tabIndex="2" >
						</td>
					</tr>
					<tr>
						<td style="text-align:center;" colspan="2">
							<a style="margin-top:20px;" href="javascript:void(0);" class="dlan" onclick="javascript:dosubmit();"  tabIndex="3"><img src="img/login.jpg" /></a>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<!--<div class="bottom"></div>-->
</body>
</html>
<script type="text/javascript">
	$(function(){ 
		$(document).keydown(function(event){ 
			if(event.keyCode==13){ 
				dosubmit();
			} 
		});
	}); 
	function dosubmit(){
		document.forms[0].submit();
	}
</script>