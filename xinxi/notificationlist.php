<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
//error_reporting(0);//关闭提示
include_once "information.php";

$deptId = isset($_SESSION['userDeptID']) ? $_SESSION['userDeptID'] : 0;
$deptName = get_deptName($deptId);
$infoCode="宛政督通字【".date('Y')."】号";
$startTime = date("Y-m-d");
$addTime = date("Y-m-d");
$infoTitle_S = isset($_POST["infoTitle_S"]) ? trim($_POST["infoTitle_S"]) : "";
$startTime_start = isset($_POST["startTime_start"]) ? $_POST["startTime_start"] : "";
$startTime_end = isset($_POST["startTime_end"]) ? $_POST["startTime_end"] : "";

include_once "../mysql.php";
?>

<!doctype html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<title>督查通报管理</title>
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/dept.css" />
<link rel="stylesheet" href="../css/editor-min.css" type="text/css" />
<script type="text/javascript" src="../js/taizhang.js"></script>
<script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<style type="text/css">
	.infotitle{width: 350px;}
</style>
</head>

<body>

<div class="right-main">
	<div id="top_div">
		<form action="notificationlist.php" method="POST">
			<!--定义查询条件路入框的范围ID-->
			<div id="search" class="search">
				<table border="0" cellpadding="0" cellspacing="1" class="tab">
					<tbody>
						<tr>
							<td height="25" colspan="4" class="tab-title" align="center">督查通报管理</td>
						</tr>
						<tr>
							<td class="tab-td-title" style="width:120px;">通报标题</td>
							<td class="tab-td-content">
								<input type="text" style="width:98%" name="infoTitle_S" value="<?php echo $infoTitle_S; ?>">
							</td>
							<td class="tab-td-title" style="width:120px;">通报时间</td>
							<td class="tab-td-content" style="width:350px;">
								<input type="text" name="startTime_start" readonly="readonly" onclick="WdatePicker();" value="<?php echo $startTime_start; ?>">
								至
								<input type="text" name="startTime_end" readonly="readonly" onclick="WdatePicker();" value="<?php echo $startTime_end; ?>">
							</td>
						</tr>
						<tr>
							<td class="tab-td-title" colspan="4" style="text-align: center;">
								<input type="submit" value="查 询" style="cursor:pointer" class="button1">
								<input type="button" value="添加" class="button1" style="cursor:pointer" onclick="openNewWindow('../taizhang/handle.php?name=../xinxi/notificationadd.php', 0, 1)">
								<input type="button" value="短信提醒" class="button1" onclick="javascript:hch.open_sms();">
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</form>
	</div>
	<div style="height:10px;"></div>
  <div id="result" class="search"><!--定义查询返回结果框的范围ID-->

	<table id="container" border="0" cellpadding="6" cellspacing="1" class="tab" style="background-color:#bebabb;">
	    <tbody>
	        <tr>
	        	<td class="tab-title">通报标题</td>
	            <td class="tab-title">通报编号 </td>
	            <td class="tab-title">通报时间 </td> 
	            <td class="tab-title">发布时间 </td> 
	            <td class="tab-title">操作 </td>      
	        </tr>
	          
			<?php 
				$res=get_notificationList($infoTitle_S,$startTime_start,$startTime_end);
				if(!empty($res)){
					foreach ($res as $info){
			?>
					
		    <tr class="hang alternate_line1" style="cursor: pointer;">
		
			  	<td style="text-align:center;">
			  		<a target="_blank" href="notificationdetail.php?id=<?php echo $info['infoId']?>"><?php echo $info['infoTitle']?></a>
			  	</td>
			  	<td style="text-align:center;"><?php echo $info['infoCode']?></td>
			  	<td style="text-align:center;"><?php echo $info['startTime']?></td>
			  	<td style="text-align:center;"><?php echo $info['addTime']?></td>
			  	<td style="text-align:center;">
			  		<a href="javascript:void(0);" onclick="openNewWindow('../taizhang/handle.php?name=../xinxi/notificationadd.php?infoId=<?php echo $info['infoId']?>', 0, 1)">修改</a> | 
			  		<a onclick="hch.delByInfoId(<?php echo $info['infoId']?>);" href="javascript:void(0);">删除</a>
			  	</td>
		  	</tr>
	
			<?php          
					}
				}else{
					echo '<tr class="hang alternate_line1" style="cursor: pointer;"><td colspan="5" style="text-align:center;">没有符合条件的纪录</td></tr>';
				}
			?>
	    </tbody>
	</table>
	
  </div>
  
</div>

  </div>

</body>
</html>

    <script type="text/javascript" src="../js/jquery.min.js"></script>
    <script type="text/javascript" src="../js/layer/layer.js"></script>
    <script type="text/javascript">
	var page = 1; 
    var hch = {
        inInt: function () {
            this.showStyle();
        },
		open_sms:function(){
			layer.open({
				type:2,
				title:'短信提醒',
				skin: 'layui-layer-rim', //加上边框
				area: ['80%', '80%'], //宽高
				content: "../sendsms.php"
			});
		},
        showStyle: function () {//间隔行显示样式
            $.each("table.tab tr.hang", function (i) {
                if (i % 2 > 0) {
                    $("table.tab tr.hang").eq(i).addClass("alternate_line2");
                }
            });
        },
        delByInfoId:function(infoId){
        	if(confirm('确定删除改记录？')){
        		location.href="notice_insert_update_delete.php?infoId="+infoId+"&flag=del";
        	}
        }
    }
    $(function () {
        hch.inInt();
    });
</script>

<?php
//绑定督查通报列表
function get_notificationList($infoTitle,$startTime_start,$startTime_end){
	$mLink=new mysql;
	$where=" where infoType = 2";
	if($infoTitle != ""){
		$where.=" and infoTitle like '%".$infoTitle."%'";
	}
	if($startTime_start != ""){
		$where.=" and startTime >= '".$startTime_start."'";
	}
	if($startTime_end != ""){
		$where.=" and startTime <= '".$startTime_end."'";
	}
	$where .= " order by infoId desc";
	$res=$mLink->getAll("select infoId,infoTitle,addTime,startTime,infoCode from information ".$where);
	$mLink->closelink();
	return $res;
}

?>