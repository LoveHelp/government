<?php
include_once 'smsmanager.php';

$sdate = empty($_POST['sdate'])?'':$_POST['sdate'];
$edate = empty($_POST['edate'])?'':$_POST['edate'];

$data = getallsms($sdate, $edate);
$count = 0;
if(!empty($data))
    $count = count($data);
?>
<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <title>短信管理</title>
    <script type="text/javascript" src="../js/jquery.min.js" ></script>
    <script type="text/javascript" src="../js/layer/layer.js" ></script>
    <script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
    <link rel="stylesheet" type="text/css" href="../css/common.css" />
	<style>
	i{color:blue;float:right;padding-right:10px;cursor:pointer;}
	div.link small{
		cursor:pointer;
	}
	</style>
</head>
<body class="main">
	<div id="search">
        <form action="sms.php" method="post" style="width: 100%;">
            <table border="0" cellpadding="4" cellspacing="1" class="table01">
                <tr>
                    <td colspan="7" class="table_title">
                        短信管理
                    </td>
                </tr>
                <tr>
                    <td class="td_title">
                        发送时间
                    </td>
                    <td width="350px" class="td_content">
                        <input type="text" name="sdate" value="<?=$sdate?>" onclick="WdatePicker();">
                        至
                        <input type="text" name="edate" value="<?=$edate?>" onclick="WdatePicker();">
                    </td>
                    <td>
                        <input type="submit" value="查询" class="button1">
						<input type="button" value="短信群发" class="button1" onclick="hch.open_sms();" />
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
                <tr class="table_title">
                    <td width="3%" class="table_title"> 序号</td>
                    <td width="32%" class="table_title">短信内容</td>
                    <td width="40%" class="table_title">短信接收者</td>
                    <td width="10%" class="table_title">发送状态</td>
                    <td width="15%" class="table_title">发送时间</td>
                </tr>
            </thead>
            <tbody>
                <?php
                if($count == 0){
                    ?><tr class="alternate_line1">
                    <td colspan="5" style="line-height: 35px; text-align: center;">
                        <font size="2">没有符合条件的记录</font>
                    </td>
                </tr><?php
                }else{
                    for($i=0; $i<$count; $i++){
                        $classname = 'alternate_line1';
                        if($i%2 == 0)
                            $classname = 'alternate_line2';

                        $state_text = "成功";
                        $state = $data[$i]['state'];
                        $errtitle = $data[$i]['err'];
                        if($state == 0){
                            $state_text = "失败";  
                        }
                        $z=$i+1;
                        ?><tr class="<?=$classname?>" >
                            <td style="text-align: center; width:3%; "><?=$i+1?></td>
                            <td width="32%"><?=$data[$i]['contents']?></td>
                            <td style="text-align:left;" width="40%" id="content_<?=$i?>">
								<?=substr($data[$i]['tels'], 0, 184)?>
								<i onclick="hch.open_content(<?=$i?>)">...&nbsp;&nbsp;+点击展开</i>
							</td>                            
                            <td width="10%" style="text-align: center;">
                            	<div><span title="<?=$errtitle?>"><?=$state_text?></span><div>
								<div class="link" style="line-height:18px;">
									<small onclick="show_detail(<?=$z?>);">查看详情</small>
									<input type="hidden" id="h_detail<?=$z?>" value="<?=$data[$i]['tels']?>" >
								</div>
							</td>
                            <td width="15%"><?=$data[$i]['sendtime']?></td>
                        </tr><?php
                    }
                }?>
            </tbody>
        </table>
    </div>
</body>
</html>
<script type="text/javascript">
var dataList = eval(<?php echo json_encode($data); ?>);
var hch = {
    open_sms:function(){
        layer.open({
            type:2,
            title:'短信提醒',
            skin: 'layui-layer-rim', //加上边框
            area: ['80%', '80%'], //宽高
            content: "../sendsms.php",
            end: function(){
                document.forms[0].submit();
            }
        });
    },
	open_content:function(i){
		var html = dataList[i]['tels'] + '<i onclick="hch.close_content(' + i + ')">...&nbsp;&nbsp;-点击折叠</i>';
		$("#content_"+i).html(html);
	},
	close_content:function(i){
		var html = dataList[i]['tels'].substr(0, 140) + '<i onclick="hch.open_content(' + i + ')">...&nbsp;&nbsp;+点击展开</i>';
		$("#content_"+i).html(html);
	}
}
function show_detail(index){
	var details = $("#h_detail"+index).val();
	var html = "<b>发送成功：</b><br>";
	var arr = details.split(";");
	var tmp="";
	html += "<table class='tab' border=0 style='background-color:#fff !important; color:gray;'><tr>";
	for(var i=0; i<arr.length; i++)
	{
		tmp = $.trim(arr[i]);
		
		if(i>=3 && i%3==0)
			html += "</tr><tr>";
		html += "<td>" + tmp + "</td>";
	}
	html +="</tr></table>";
	html += "<br><b>发送失败：</b><br>";
	layer.open({
		title: '短信发送详情',
		skin: 'layui-layer-rim', //加上边框
		area: ['800px', '500px'], //宽高
		content: html
	});
}
</script>