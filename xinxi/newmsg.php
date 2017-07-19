<?php
include_once "../mysql.php";
$mLink = new mysql;
session_start();
$userid = isset($_SESSION['userID']) ? $_SESSION['userID'] : "";
$uid = isset($_GET['uid']) ? $_GET['uid'] : "";
$time = date("Y-m-d H:i:s");
?>
<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>在线交流</title>
	<script type="text/javascript" src="../js/jquery.min.js"></script>
	<script type="text/javascript" src="../js/layer/layer.js" ></script>
	<link rel="stylesheet" type="text/css" href="../css/default.css" />
	<style>
	.main{background:#ECF6FB;height:auto;overflow:hidden;width:298px;}
	.td_title{width:25%;}
	.td_content{width:75%}
	.td_content #content{width:90%;outline:none;overflow-x:hidden;overflow-y:auto;}
	.button1{
		width: 68px;
		height: 24px;
		text-align: center;
		color: #FFFFFF;
		font-weight: bold;
		background-image: url(../img/button2.jpg);
		background-repeat: no-repeat;
		background-position: left;
		border-left: 0px solid #2F3C4D;
		border-right: 0px solid #2F3C4D;
		border-top: 0px solid #2F3C4D;
		border-bottom: 0px solid #2F3C4D;
		padding-top: 2px;
	}
	</style>
</head>
<body class="main">
	<!--添加工作目标-->
<div class="show-dept">
<form action="#" method="post">
	<table align="center" cellpadding="5" cellspacing="1" class="table01">
        <tr>
			<td class="td_title">接收人</td>
			<td class="td_content" colspan="3">
				<select name="receiver" id="receiver">
					<?php
					if($uid == ""){
						echo '<option value="0" selected>全体成员</option>';
					}else{
						echo '<option value="0">全体成员</option>';
					}
					$contacts = get_all_users($mLink);
					foreach($contacts as $v){
						if($uid == $v['uid']){
							echo '<option value="' . $v['uid'] . '" selected>' . $v['uname'] . '</option>';
						}else{
							echo '<option value="' . $v['uid'] . '">' . $v['uname'] . '</option>';
						}
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="td_title">内容</td>
			<td class="td_content" colspan="3">
				<textarea name="content" id="content" rows="4"></textarea>
				<span class="text3"><span style="color: #FF0000">*</span> 
				<a href="tencent://message/?uin=287910417&Site=在线QQ&Menu=yes">在线视频</a>  
			</td>
		</tr>
		<tr>
			<td colspan="4" class="td_button" style="height:80px;line-height:80px;">
				<input type="button" value="确 定" class="button1" onclick="hch.do_submit();">&nbsp;
				<input type="button" value="取 消" class="button1" onclick="hch.close_layer();"> 
			</td>
		</tr>
	</table>
	</form>
</div>
</body>
</html>
<script type="text/javascript">
var hch = {
	close_layer:function(){
		parent.$(".online").css("display", "block");
		var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
		parent.layer.close(index);
	},
	do_submit:function(){
		var receiver = $("#receiver").val();
		var content = $("#content").val();
		if(content == ""){
			layer.msg("发送内容不能为空！");
		}else{
			$.ajax({
				type: 'post',
				url: "addOnline.php",
				data: {from:'<?php echo $userid; ?>', to:receiver, content:content, time:'<?php echo $time; ?>'},
				success:function(result){
					if(result){
						layer.msg("发送成功！");
						window.parent.location.reload(); //刷新父页面
						hch.close_layer();
						
					}else{
						layer.msg("发送失败！");
					}
				}
			});
		}
	}
}
</script>
<?php
function get_all_users($mLink){
	$sql = "select uid, UNAME as uname from user where status = 1";
	$res = $mLink->getAll($sql);
	return $res;
}
$mLink->closelink();
?>