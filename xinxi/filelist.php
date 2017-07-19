<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
//error_reporting(0);//关闭提示

$deptId = isset($_SESSION['userDeptID']) ? $_SESSION['userDeptID'] : 0;
$infoTitle_S = isset($_POST["infoTitle_S"]) ? trim($_POST["infoTitle_S"]) : "";

include_once "../mysql.php";
?>

<!doctype html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<title>督查文件管理</title>
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/dept.css" />
<script type="text/javascript" src="../js/taizhang.js"></script>
<script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<style type="text/css">
	.infotitle{width: 350px;}
</style>
</head>

<body>

<div class="right-main">
	<div id="top_div">
	<form action="filelist.php" method="POST">
		<!--定义查询条件路入框的范围ID-->
		<div id="search" class="search">
		    <table border="0" cellpadding="0" cellspacing="1" class="tab">
			    <tbody>
			    	<tr>
				    	<td height="25" colspan="4" class="tab-title" align="center">督查文件管理</td>
				    </tr>
				    <tr>
				    	<td class="tab-td-title" style="width:120px;">标题</td>
					    <td class="tab-td-content">
					    	<input type="text" style="width: 98%;" name="infoTitle_S" value="<?php echo $infoTitle_S; ?>">
					    </td>
					    <td class="tab-td-title" style="width:230px;">
						    <input type="submit" value="查 询" style="cursor:pointer" class="button1">
							<input type="button" value="添加" class="button1" style="cursor:pointer" onclick="openNewWindow('../taizhang/handle.php?name=../xinxi/fileadd.php', 1, 0)">
							<!--<input type="button" value="短信提醒" class="button1" onclick="javascript:hch.open_sms();">-->
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
	        	<td class="tab-title">标题</td>
	            <td class="tab-title">创建时间 </td> 
	            <td class="tab-title">操作 </td>      
	        </tr>
	          
			<?php 
				$res=get_fileList($infoTitle_S);
				if(!empty($res)){
					foreach ($res as $info){
			?>
					
		    <tr class="hang alternate_line1" style="cursor: pointer;">
		
			  	<td style="text-align:center;"><?php echo $info['infoTitle']?></td>
			  	<td style="text-align:center;"><?php echo $info['addTime']?></td>
			  	<td style="text-align:center;">
			  		<a href="javascript:void(0);" onclick="openNewWindow('../taizhang/handle.php?name=../xinxi/fileadd.php?infoId=<?php echo $info['infoId']?>', 0, 1)">修改</a> | 
			  		<!--<a href="file_insert_update_delete.php?infoId=<?php echo $info['infoId']?>&flag=del">删除</a>-->
			  		<a onclick="hch.delByInfoId(<?php echo $info['infoId']?>);" href="javascript:void(0);">删除</a>
			  	</td>
		  	</tr>
	
			<?php          
					}
				}else{
					echo '<tr class="hang alternate_line1" style="cursor: pointer;"><td colspan="3" style="text-align:center;">没有符合条件的纪录</td></tr>';
				}
			?>
	    </tbody>
	</table>
	
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
		//open_sms:function(){
		//	layer.open({
		//		type:2,
		//		title:'短信提醒',
		//		skin: 'layui-layer-rim', //加上边框
		//		area: ['80%', '80%'], //宽高
		//		content: "../sendsms.php"
		//	});
		//},
        showStyle: function () {//间隔行显示样式
            $.each("table.tab tr.hang", function (i) {
                if (i % 2 > 0) {
                    $("table.tab tr.hang").eq(i).addClass("alternate_line2");
                }
            });
        },
        delByInfoId:function(infoId){
        	if(confirm('确定删除改记录？')){
        		location.href="file_insert_update_delete.php?infoId="+infoId+"&flag=del";
        	}
        }
    }
    $(function () {
        hch.inInt();
    });
</script>

<?php
//绑定督查通报列表
function get_fileList($infoTitle){
	$mLink=new mysql;
	$where=" where infoType = 4";
	if($infoTitle != ""){
		$where.=" and infoTitle like '%".$infoTitle."%'";
	}
	$where .= " order by infoId desc";
	$res=$mLink->getAll("select infoId,infoTitle,addTime from information ".$where);
	$mLink->closelink();
	return $res;
}

?>