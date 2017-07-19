<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:index.php');
	exit;
}
include_once "mysql.php";
include_once "constant.php";
include_once "reminddata.php";

$deptid = $_SESSION['userDeptID'];
$userid = $_SESSION['userID'];

function get_info_by_type($type,$deptid){
	$mLink = new mysql;
	$sql = "select * from information where infoType = " .$type;
	if($type==1){
		$sql.=" and recvDeptIds REGEXP '^".$deptid."$|^".$deptid.",|,".$deptid.",|,".$deptid."$'";
	}
	
	//$sql .= " order by infoId desc limit 0,5";
	$sql .= " order by infoId desc";
	$res = $mLink->getAll($sql);
	if($res){
		return json_encode($res);
	}
}

function get_messages_by_id($userid){
	$mLink = new mysql;
	$sql = "select a.id, a.content, a.time, b.UNAME as uname from message a left join user b on a.fromuser = b.uid where a.fromuser = " . $userid . " or a.touser = " . $userid . " or a.touser = 0 order by time";
	$res = $mLink->getAll($sql);
	if($res){
		return $res;
	}
}

function floatdiv(){
	$mLink = new mysql;
	$sql = "select infoId,infoType,infoTitle,addTime from information order by infoId desc limit 3";
	$res = $mLink->getAll($sql);
	if($res){
		return $res;
	}
}

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<style type="text/css">
body{background-image:url(img/LeftBg2.gif);background-repeat:repeat-x;margin: 0px;padding: 0px;overflow: hidden;}
a{font-family: "微软雅黑",serif;text-decoration: none;color: rgb(0, 0, 0); font-size: 16px; width: 100%;}
a:link {color: #222222}
a:visited {color: #222222}
a:hover {color: #bb0d00}
a:active {color: #222222}
.li_01{margin-left: 10px;margin-right:10px;margin-top:25px;padding: 2px 0px 1px 10px;list-style:none; background: url('img/text_list_icon.jpg') no-repeat scroll 0px center;float:left;width:100px;}
.li_02{margin-left: -30px;margin-right:10px;padding: 5px 2px 5px 10px;list-style:none; background: url('img/text_list_icon.jpg') no-repeat scroll 0px center;border-bottom:1px dotted #909090;
		display:block;white-space:nowrap; overflow:hidden; text-overflow:ellipsis;}
.li_02 a{color: rgb(0, 0, 0); font-size: 14px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 100%; margin-top: 5px; margin-bottom: 5px;}
.li_02 a:hover{color: #bb0d00;}
.li_03{margin-left: -30px;margin-right:10px;padding: 2px 2px 1px 10px;list-style:none; background: url('img/text_list_icon.jpg') no-repeat scroll 0px center;}
.panel-header-div{width:50%;}
.portal-p-div{width:100%; height: 200px;}
.portal-p-div2{width:100%; height: 300px;}
.online{position:absolute;right:0;bottom:235px;width:48px;padding:32px 8px;background:url("img/msg.png");font-size:16px;font-weight:48;text-align:center;z-index:99999999;cursor:pointer;}
.more{position:absolute;right:30px;top:5px;color:#FFF;}
.more a{color:#FFF;font-size:14px;}
.task_type{overflow:hidden;}
.list-dl ul{margin:0;padding:0;margin-top:26px;}
.list-dl ul li{width:200px;height:36px;list-style:none;float:left;text-align:left;}
.list-dl ul li a{
	width: 150px;
	color: #333;
	display: block;
	height: 34px;
	background:url(img/list-dl-dot.png) 125px -5px no-repeat;
	padding-left: 10px;
	margin: 0 auto;
}
.list-dl ul li.current a,.list-dl ul li a:hover {
	color: #307ee8;
	background: url(img/list-dl-dot.png) 125px -39px no-repeat;
}
</style>
<script type="text/javascript" src="js/jquery.min.js"></script>
<link id="easyuiTheme" rel="stylesheet" href="css/easyui.css" type="text/css">
<link rel="stylesheet" href="css/portal.css" type="text/css" charset="utf-8">
<script type="text/javascript" src="js/fusioncharts/FusionCharts.js"></script>
<script type="text/javascript" src="js/layer/layer.js" ></script>
<script type="text/javascript" src="js/fudong.js" ></script>
<link rel="stylesheet" href="css/progress.css" type="text/css" charset="utf-8">
</head>
<body style="overflow-x: hidden;overflow-y: auto">
<div class="online" onclick="con.open_online();"></div>
<script>
var con = {
	open_online:function(){
		$(".online").css("display", "none");
		this.index = layer.open({
			type:2,
			title:'在线交流',
			closeBtn: 0, //不显示关闭按钮
			area: ['300px', '250px'], //宽高
			shade: [0],
			//skin: 'layui-layer-rim', //加上边框
			offset: 'rb', //右下角弹出
			content: "xinxi/newmsg.php",
			success: function() {
				con.auto_height();
			}
		});
	},
	auto_height:function(){
		layer.iframeAuto(this.index);
	}
}
function change(obj, id){
	if($(obj).hasClass("panel-tool-expand")){
		$(obj).removeClass("panel-tool-expand");
		$("#"+id).css("display", "block");
	}else{
		$(obj).addClass("panel-tool-expand");
		$("#"+id).css("display", "none");
	}
}
</script>
<div id="portal" style="width:100%;margin:0;padding:0;background:#FFF;" class="portal portal-noborder">

<table border="0" cellspacing="0" cellpadding="0" style="width:100%;height:auto;">
<tbody>
	<tr>
		<td class="portal-column-td panel-header-div">
			<div class="portal-column-left portal-column">
				<div class="panel portal-panel">
					<div class="panel-header" style="border:none;">
						<div class="panel-title">督查台帐</div>
						<!--<div class="more"><a href="javascript:void(0)">更多...&nbsp;&nbsp;</a></div>-->
						<div class="panel-tool"><a class="panel-tool-collapse" onclick="change(this, 'p1');" href="javascript:void(0)"></a></div>
					</div>
					<div id="p1" title="" class="panel-body portal-p portal-p-div">
						<div class="task_type">
						<!--<?php
						foreach($task_type as $key=>$value){
							echo '<li class="li_01">' 
								. '<a href="taizhang/taskreview.php?route=1&tasktypes=' . $key . '" target="_blank">' . $value . '</a>'
								. '</li>';
						}
						?>-->
						<dd class="list-dl" style="margin-left:0px;">
							<ul>
								<?php
								foreach($task_type as $key=>$value){
									echo '<li><a href="taizhang/taskreview.php?route=1&tasktypes=' . $key . '" target="_blank">' . $value . '</a></li>';
								}
								?>
							</ul>
						</dd>
						</div>
						<p style="text-align:right;padding-right:50px;margin-top:40px;"><a href="taizhang/cartogram.php"><img src="img/xtb.png">&nbsp;查看工作完成情况</a></p>
					</div>
				</div>
				<div class="panel portal-panel">
					<div class="panel-header" style="border:none;">
						<div class="panel-title">督查通知</div>
					<div class="panel-tool"><a class="panel-tool-collapse" onclick="change(this, 'p2');" href="javascript:void(0)"></a></div>
				</div>
				<div id="p2" title="" class="panel-body portal-p portal-p-div">
					<ul>
					<?php
					$notice_type = 1;
					$noticeList = json_decode(get_info_by_type($notice_type,$deptid), true);
					if(is_array($noticeList) && count($noticeList) > 0){
						foreach($noticeList as $key=>$n){
							echo '<li class="li_02"><span style="float:right;display:inline;">' . $n['addTime'] . '</span>'
								. '<a href="xinxi/noticedetail.php?id=' . $n['infoId'] . '" target="_blank">' . $n['infoCode'] . " " . $n['infoTitle'] . '</a>'
								. '</li>';
						}
					}else{
						echo '<li class="li_03"><font size="2.5">暂无信息</font></li>';
					}
					?>
        			</ul>
				</div>
			</div>
			<div class="panel portal-panel">
				<div class="panel-header" style="border:none;">
					<div class="panel-title">督查通报</div>
					<div class="panel-tool"><a class="panel-tool-collapse" onclick="change(this, 'p3');" href="javascript:void(0)"></a></div>
				</div>
				<div id="p3" title="" class="panel-body portal-p portal-p-div">
					<ul>
						<?php
							$tongbao = 2;
							$tongbaoList = json_decode(get_info_by_type($tongbao,$deptid), true);
							if(is_array($tongbaoList) && count($tongbaoList) > 0){
								foreach($tongbaoList as $key=>$t){
									echo '<li class="li_02"><span style="float:right;display:inline;">' . $t['addTime'] . '</span>'
										. '<a href="xinxi/notificationdetail.php?id=' . $t['infoId'] . '&type=2" target="_blank">' . $t['infoCode'] . " " . $t['infoTitle'] . '</a>'
										. '</li>';
								}
							}else{
								echo '<li class="li_03"><font size="2.5">暂无信息</font></li>';
							}
						?>
					</ul>
				</div>
			</div>
		</div>
	</td>
	<td class="portal-column-td panel-header-div">
		<div class="portal-column-right portal-column">
			<div class="panel portal-panel">
				<div class="panel-header" style="border:none;">
					<div class="panel-title">督查动态</div>
					<div class="panel-tool"><a class="panel-tool-collapse" onclick="change(this, 'p4');" href="javascript:void(0)"></a></div>
				</div>
				<div id="p4" title="" class="panel-body portal-p portal-p-div">
					<ul>
						<?php
							$dongtai = 3;
							$dongtaiList = json_decode(get_info_by_type($dongtai,$deptid), true);
							if(is_array($dongtaiList) && count($dongtaiList) > 0){
								foreach($dongtaiList as $key=>$d){
									echo '<li class="li_02"><span style="float:right;display:inline;">' . $d['addTime'] . '</span>'
										. '<a href="xinxi/infodetail.php?id=' . $d['infoId'] . '&type=3" target="_blank">' . $d['infoCode'] . " " . $d['infoTitle'] . '</a>'
										. '</li>';
								}
							}else{
								echo '<li class="li_03"><font size="2.5">暂无信息</font></li>';
							}
						?>
					</ul>
				</div>
			</div>
			<div class="panel portal-panel">
				<div class="panel-header" style="border:none;">
					<div class="panel-title">督查文件</div>
					<div class="panel-tool"><a class="panel-tool-collapse" onclick="change(this, 'p5');" href="javascript:void(0)"></a>
				</div>
			</div>
			<div id="p5" title=""  class="panel-body portal-p portal-p-div">
				<ul>
				<?php
					$files = 4;
					$filesList = json_decode(get_info_by_type($files,$deptid), true);
					if(is_array($filesList) && count($filesList) > 0){
						foreach($filesList as $key=>$f){
							echo '<li class="li_02"><span style="float:right;display:inline;">' . $f['addTime'] . '</span>'
								. '<a href="xinxi/infodetail.php?id=' . $f['infoId'] . '&type=4" target="_blank">' . $f['infoCode'] . " " . $f['infoTitle'] . '</a>'
								. '</li>';
						}
					}else{
						echo '<li class="li_03"><font size="2.5">暂无信息</font></li>';
					}
					?>
				</ul>
			</div>
		</div>
		<div class="panel portal-panel">
			<div class="panel-header" style="border:none;">
				<div class="panel-title" style="">在线交流</div>
				<div class="panel-tool"><a class="panel-tool-collapse" onclick="change(this, 'p6');" href="javascript:void(0)"></a></div>
			</div>
			<div id="p6" title="" class="panel-body portal-p portal-p-div">
				<ul>
					<?php
					$messages = get_messages_by_id($userid);
					if(is_array($messages) && count($messages)){
						foreach($messages as $m){
							echo '<li class="li_02"><span style="float:right;display:inline;">' . $m['time'] . '</span>'
								. '<a href="xinxi/online.php?id=' . $m['id'] . '">' . "&nbsp;&nbsp;" . $m['uname'] . "&nbsp;&nbsp;" . $m['content'] . '</a>'
								. '</li>';
						}
					}else{
						echo '<li class="li_03"><font size="2.5">暂无信息</font></li>';
					}
					?>
				</ul>
			</div>
		</div>
	</div>
</td>		
</tbody>
</table>

</div>
<div id="ad1" style="z-index: 10000;width: 450px;height: 203px; text-align: right; background-position: right center; position: absolute; left: 800.767px; top: 488.766px;">
<img style="position: absolute;top: 0;left: 0;" src="img/fudong.png" border="0">

<div style="position: absolute;text-align:left;top:45px;left:65px;width:330px;height:120px;z-index: 10;">
	<?php
	$href=array(1=>'xinxi/noticedetail.php?id=',2=>'xinxi/notificationdetail.php?id=',3=>'xinxi/infodetail.php?id=',4=>'xinxi/infodetail.php?id=');
	$float=floatdiv();
	foreach($float as $f){
		echo '<a href="'.$href[$f['infoType']].$f['infoId'].'" target="_blank"><p style="line-height:100%;margin:5px 0 0 0;padding:0">'.$f['infoTitle'].'</p></a>';
	}
	?>
</div>		
</a><br>
<a href="javascript:void(0);" onfocus="this.blur()" onclick="document.getElementById('ad1').style.display='none';">×关闭</a>
</div>
<script type="text/javascript">new AdMove("ad1").Run();</script>  
</body>
</html>