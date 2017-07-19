<?php
include_once '../mysql.php';
session_start();
$userid = isset($_SESSION['userID']) ? $_SESSION['userID'] : "";
$time = date("Y-m-d H:i:s");

$sdate = isset($_POST['sdate']) ?  $_POST['sdate'] : "";
$edate = isset($_POST['edate']) ? $_POST['edate'] : "";
$where = " where (fromuser = " . $userid . " or touser = " . $userid . " or touser = 0)";
if($sdate != ""){
	$where .= " and DATE_FORMAT(time,'%Y-%m-%d') >= '" . $sdate . "'";
}
if($edate != ""){
	$where .= " and DATE_FORMAT(time,'%Y-%m-%d') <= '" . $edate . "'";
}
$id = isset($_GET['id']) ? $_GET['id'] : "";
$data = get_all_message($where);
function get_all_message($where){
	$mLink = new mysql;
	$sql = "select b.UNAME as fromuser, b.uid, c.UNAME as touser, a.id, a.content, a.time from message a left join user b on a.fromuser = b.uid left join user c on a.touser = c.uid" . $where . " order by a.time desc";
	$res = $mLink->getAll($sql);
	return $res;
}
?>
<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<title>在线交流管理</title>
<script type="text/javascript" src="../js/jquery.min.js" ></script>
<script type="text/javascript" src="../js/layer/layer.js" ></script>
<link rel="stylesheet" type="text/css" href="../css/common.css" />
<script type='text/javascript' src="../js/calendar/calendar.js" ></script>
<script type='text/javascript' src='../js/calendar/WdatePicker.js'></script>
<style>
.alternate_line2{background:#FFF;}
td{text-align:center;}
.orange{color:#FF6100;}
.online{position:fixed;right:0;bottom:0;width:20px;padding:20px 10px;background:#F7B824;font-size:16px;font-weight:600;text-align:center;z-index:99999999;cursor:pointer;}
</style>
</head>
<body class="main">
<div class="online" onclick="hch.open_online();">在线交流</div>
	<div id="search">
        <form action="online.php" method="post" style="width: 100%;">
            <table border="0" cellpadding="4" cellspacing="1" class="table01">
                <tr>
                    <td colspan="7" class="table_title" >
                        在线交流管理
                    </td>
                </tr>
                <tr>
                    <td width="120px" class="td_title">
                        发送时间
                    </td>
                    <td width="350px">
						<input type="text" name="sdate" id="sdate" maxlength="" size="15" value="<?php echo $sdate; ?>" onfocus="WdatePicker()" readonly="readonly" class="input" />
						至
						<input type="text" name="edate" id="edate" maxlength="" size="15" value="<?php echo $edate; ?>" onfocus="WdatePicker()" readonly="readonly" class="input" />
                    </td>
                    <td style="text-align:left;">
                        <input type="submit" value="查询" class="button1">
						<input type="button" value="重置" class="button1" onclick="hch.clear()">
                    </td>
                </tr>                   
            </table>
        </form>
    </div>
    <div id="result" style="margin-top:10px;">
        <!--定义查询返回结果框的范围ID-->
        <table border="0" cellpadding="4" cellspacing="1" class="table01">
            <thead>
                <tr class="table_title">
                    <th width="5%" class="table_title"> 序号</th>
					<th width="12%" class="table_title">发送人</th>
                    <th width="35%" class="table_title">内容</th>
                    <th width="12%" class="table_title">接收人</th>
                    <th width="12%" class="table_title">发送时间</th>
					<th width="12%" class="table_title">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(is_array($data) && count($data) > 0){
					$count = 0;
					foreach($data as $v){
						if($count%2 == 0){
							if($id == $v['id']){
								echo '<tr class="alternate_line1 orange">';
							}else{
								echo '<tr class="alternate_line1">';
							}
						}else{
							if($id == $v['id']){
								echo '<tr class="alternate_line2 orange">';
							}else{
								echo '<tr class="alternate_line2">';
							}
						}
						$count++;
						if($v['touser'] == ""){
							$receiver = "全体成员";
						}else{
							$receiver = $v['touser'];
						}
						echo '<td>' . $count . '</td>'
							. '<td>' . $v['fromuser'] . '</td>'
							. '<td>' . $v['content'] . '</td>'
							. '<td>' . $receiver . '</td>'
							. '<td>' . $v['time'] . '</td>'
							. '<td><input type="button" value="回复" onclick="hch.open_online(' . $v['uid'] . ')" style="cursor:hand" class="button1" /></td>'
							. '</tr>';
					}
				}else{
					echo '<tr class="alternate_line1">'
						. '<td colspan="6" style="line-height: 35px; text-align: center;">'
						. '<font size="2">没有符合条件的记录</font>'
						. '</td></tr>';
				}
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<script type="text/javascript">
var hch = {
    clear:function(){
		$("#sdate").attr("value", "");
		$("#edate").attr("value", "");
    },
	open_online:function(uid){
		$(".online").css("display", "none");
		this.index = layer.open({
			type:2,
			title:'在线交流',
			closeBtn: 0, //不显示关闭按钮
			area: ['300px', '300px'], //宽高
			shade: [0],
			//skin: 'layui-layer-rim', //加上边框
			offset: 'rb', //右下角弹出
			content: "newmsg.php?uid=" + uid,
			success: function() {
				hch.auto_height();
			}
		});
	},
	auto_height:function(){
		layer.iframeAuto(this.index);
	}
}
</script>