<?php
header("Content-type:text/html;charset=utf-8");
include_once 'data/backup.php';

$res = getBackupList();
$data = json_decode($res, true);
$count = 0;
if(!empty($data))
    $count = sizeof($data);

?>
<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <title>短信管理</title>
    <script type="text/javascript" src="../js/jquery.min.js" ></script>
    <script type="text/javascript" src="../js/layer/layer.js" ></script>
	<script type="text/javascript" src="../js/md5.min.js" ></script>
    <link rel="stylesheet" type="text/css" href="../css/common.css" />
	<style>
	a{text-decoration: none;}
	.miniTab{background-color:transparent !important; text-align:left;}
	</style>
</head>
<body class="main">
	<div id="search">
        <form action="sms.php" method="post" style="width: 100%;">
            <table border="0" cellpadding="4" cellspacing="1" class="table01">
                <tr>
                    <td class="table_title">系统归档</td>
                </tr>                 
            </table>
        </form>
    </div>
    <div style="width:100%; line-height: 40px;">
        <input type="button" value="数据库恢复" class="button2" onclick="recovery();">
        <input type="button" value="数据备份" class="button2" onclick="backup();">
        <input type="button" value="数据年度归档" class="button2" onclick="guidang();">
    </div>
    <div id="result">
        <!--定义查询返回结果框的范围ID-->
        <table border="0" cellpadding="4" cellspacing="1" class="table01">
            <thead>
                <tr class="table_title">
                	<td width="50px" class="table_title"> 序号</td>
                    <td class="table_title">文件名</td>
                    <td width="150px" class="table_title">备份时间</td>
                </tr>
            </thead>
            <tbody>
				<?php
				if($count == 0){
                ?><tr>
                    <td colspan="5" class="tip">
                        <font size="2">没有符合条件的记录</font>
                    </td>
                </tr><?php
				}else{
					for($i=0;$i<$count;$i++){
						$name = "{$data[$i]['key']}.zip";
						$url = "/government/backup/{$name}";
						?><tr>
						<td><?=$i+1?></td>
						<td style="text-align:left;"><a href="<?=$url?>"><?=$name?></a></td>
						<td><?=$data[$i]['val']?></td>
						</tr><?php
					}
				}?>
            </tbody>
        </table>
    </div>
    <div id="recoveryForm" style="display:none;">
  		<div style="padding: 18px 10px 10px 10px;height:auto; text-align:center;">
	  		<div style="line-height: 35px;">
				<table class="miniTab">
					<tr>
						<td style="text-align:right;">选择备份：</td>
						<td><select id="backup_sel" style="width:150px;"></select></td>
					</tr>
					<tr>
						<td style="text-align:right;">输入口令：</td>
						<td><input id="rvkey" type="password" style="width:146px;" />
						<span style="color:red;">*</span></td>
					</tr>
					<tr>
						<td></td>
						<td><input type="button" value="开始恢复" class="button1" onclick="hch.start_recovery();" /></td>
					</tr>
				</table>
	  		</div>
		</div>
	</div>
    <div id="recoverytip" style="display:none;">
  		<div style="padding: 18px 10px 10px 10px;height:auto; text-align:center;">
	  		<small style="color: Blue;">正在恢复数据，请稍事休息，谢谢。。。<b id="tip"></b></small>
		</div>
	</div>
    <div id="backuptip" style="display:none;">
  		<div style="padding: 18px 10px 10px 10px;height:auto; text-align:center;">
	  		<small style="color: Blue;">正在备份数据，请稍事休息，谢谢。。。<b id="tip"></b></small>
		</div>
	</div>
	<div id="guidangForm" style="display:none;">
  		<div style="padding: 18px 10px 10px 10px;height:auto; text-align:center;">
	  		<div style="line-height: 35px;">
				输入口令：<input id="gdkey" type="password" style="width:146px;" />
				<span style="color:red;">*</span>
	  			<input type="button" value="开始归档" class="button1" onclick="hch.start_guidang();" />
	  		</div>
		</div>
	</div>
	<div id="guidangtip" style="display:none;">
  		<div style="padding: 18px 10px 10px 10px;height:auto; text-align:center;">
	  		<small style="color: Blue;">正在进行数据年度归档，请稍事休息，谢谢。。。<b id="tip"></b></small>
		</div>
	</div>
</body>
</html>
<script type="text/javascript">
function recovery(){
	layer.confirm("确实要进行数据恢复吗？<br>恢复开始时，切勿操作数据，以免造成数据错误！", 
		{icon:3, title:'数据恢复'},
		function(index){
			hch.open_recover();
			layer.close(index);
		}
	);
}
function backup(){
	layer.confirm("确实要开始备份数据库吗？<br>备份开始时，切勿操作数据，以免造成数据丢失！", 
		{icon:3, title:'数据备份'},
		function(index){
			hch.open_backup();
			layer.close(index);
		}
	);
}
function guidang(){
	layer.confirm("确实要进行年度归档吗？<br>一般在每年年终时，归档一次即可！<br>归档开始时，切勿操作数据，以免造成数据丢失！", 
		{icon:3, title:'数据年度归档'},
		function(index){
			hch.open_guidang();
			layer.close(index);
		}
	);
}
var hch = {
	 open_recover:function(){
        rvLayer = layer.open({
            type: 1,
		    title: '数据恢复',
		    skin: 'layui-layer-rim', //加上边框
		    area: ['400px', '180px'], //宽高
		    content: $("#recoveryForm")
        });
        $(".layui-layer-rim").css("background-color", "#DEEFFF");
        var url = "data/backup.php?do=getBackupList";
        $.post(url, function (res) {
        	if(res != 0){
        		var obj = $("#backup_sel");
        		for(var i=0; i<res.length; i++){
        			var opt_obj = $("<option></option>");
                    $(opt_obj).text(res[i]['val']);
                    $(opt_obj).val(res[i]['key']);
                    $(opt_obj).appendTo($(obj));
        		}
        	}
        }, 'json');
    },
    start_recovery:function(){
    	var backup_sel = $("#backup_sel").val();
    	if(!backup_sel){
    		layer.msg("请选择用来恢复数据的备份记录！");
    		return;
    	}
		var rvkey = md5($("#rvkey").val());
    	tipLayer = layer.open({
            type: 1,
		    title: '',
		    closeBtn: 0,
		    skin: 'layui-layer-rim', //加上边框
		    area: ['400px', '50px'], //宽高
		    content: $("#recoverytip")
        });
        $(".layui-layer-rim").css("background-color", "#DEEFFF");
        var url = "data/backup.php?do=dataRecovery";
        var param = {'param': backup_sel, 'rvkey': rvkey}
        $.post(url, param, function (res) {
        	if(res == 1){
        		layer.msg("数据恢复成功！");
				setTimeout('refresh()', 800);
        	}else if(res == -1){
				layer.msg("数据恢复口令输入错误！");
				$("#rvkey").focus();
			}else{
        		layer.msg("数据恢复失败！");
        	}
			layer.close(tipLayer);        	
        }, 'json');
    },
    open_backup:function(){
        tipLayer = layer.open({
            type: 1,
		    title: '',
		    closeBtn: 0,
		    skin: 'layui-layer-rim', //加上边框
		    area: ['400px', '50px'], //宽高
		    content: $("#backuptip")
        });
        $(".layui-layer-rim").css("background-color", "#DEEFFF");
        var url = "data/backup.php?do=dataBackup";
        $.post(url, function (res) {
        	if(res == 1){
        		layer.msg("数据备份成功！");
        	}else{
        		layer.msg("数据备份失败！");
        	}
        	layer.close(tipLayer);
        	setTimeout('refresh()', 800);
        }, 'json');
    },
    open_guidang:function(){
        gdLayer = layer.open({
            type: 1,
		    title: '数据年度归档',
		    skin: 'layui-layer-rim', //加上边框
		    area: ['400px', '120px'], //宽高
		    content: $("#guidangForm")
        });
        $(".layui-layer-rim").css("background-color", "#DEEFFF");
    },
	start_guidang:function(){
		var gdkey = md5($("#gdkey").val());
        tipLayer = layer.open({
            type: 1,
		    title: '',
		    closeBtn: 0,
		    skin: 'layui-layer-rim', //加上边框
		    area: ['400px', '50px'], //宽高
		    content: $("#guidangtip")
        });
        $(".layui-layer-rim").css("background-color", "#DEEFFF");
        var url = "data/backup.php?do=dataGuidang";
        $.post(url, {'gdkey': gdkey}, function (res) {
        	if(res == 1){
        		layer.msg("数据年度归档成功！");
				setTimeout('refresh()', 800);
        	}else if(res == -1){
        		layer.msg("数据年度归档口令输入错误！");
				$("#gdkey").focus();
        	}else{
				layer.msg("数据年度归档失败！");
			}
        	layer.close(tipLayer);
        }, 'json');
    }   
}

function refresh()
{
	window.location.reload();
}
</script>
