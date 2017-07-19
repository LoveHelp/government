<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
//error_reporting(0);//关闭提示
include_once "../mysql.php";
include_once "information.php";
include_once "attachment.php";

$deptId = isset($_SESSION['userDeptID']) ? $_SESSION['userDeptID'] : 0;
$infoId = isset($_GET["id"]) ? $_GET["id"] : "";
$notificationList=get_infoList(2,$infoId);
if(is_array($notificationList)){
	$infoTitle=$notificationList["infoTitle"];
	$infoCode=$notificationList["infoCode"];
	$infoContent=$notificationList["infoContent"];
	$attachList=get_attachList($infoId);
}

?>
<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<title>督查通报详情</title>
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/dept.css" />
<script type='text/javascript' src="../js/jquery.min.js"/>
<script type="text/javascript" src="../js/layer/layer.js"></script>

<style type="text/css">
body{margin: 0 auto;padding: 0;overflow: auto;width:800px;text-align:center;}
.container{text-align: center;width:595px;margin:140px 102px 132px 102px; min-height:842px;height:auto;}
.container .dcontent{margin-left: auto;margin-right: auto;margin-top: 5px;padding-top: 53px;width: 595px;top: 50px;height:auto;overflow: auto;}
span {color: #545454;}
.pattach{line-height: 30px;text-align: left;text-indent: 50px;}
.pattach a{font-size: 14px;color: #0066cc;}
.pattach a:hover{text-decoration: underline;color:#0066CC;}
.dcontent p, .dcontent span, .dcontent a{font-size:16pt !important;font-weight:600;}
#infoContent p{line-height:180%;}
</style>
<script>
function preview(){ 
	window.onbeforeprint = function(){
		//$(".container").css("border", "none");
		var bdhtml = window.document.body.innerHTML; 
		var sprnstr = "<!--startprint-->"; 
		var eprnstr = "<!--endprint-->"; 
		var prnhtml = bdhtml.substr(bdhtml.indexOf(sprnstr)+17); 
		prnhtml = prnhtml.substring(0,prnhtml.indexOf(eprnstr)); 
		window.document.body.innerHTML = prnhtml;
	}
	if (!!window.ActiveXObject || "ActiveXObject" in window){
		wb.execwb(7,1);//ie弹出打印预览
	}else{
		window.print();
	}
}
window.onafterprint = function(){
	window.history.go(0);
}
</script>
</head>
<body>
<OBJECT classid="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2" height="0" id="wb" name="wb" width="3"></OBJECT>
<div>
	<h5 align="center" style="padding:25px;">
		<input type="button" id="qianshou" value="" class="button1" onclick="hch.qianshou();" style="cursor:hand;"/>&nbsp;&nbsp;
		<input type="button" class="button1" value="打 印" onclick="preview();">	
	</h5>
</div>
<div style="border:1px solid #525252;">
<!--startprint-->
<div class="container">
	
 <h1 style="color: red;margin-bottom: 50px;word-spacing:1pt;font-size: 56pt;font-family:方正小标宋简体"><img alt="" src="../img/notification.jpg"/></h1>
 
 <p style="line-height: 35px;height: 35px;font-size:16pt;font-family:仿宋_GB2312;font-weight:600;">
 	<?php echo $notificationList["infoCode"];?>
 </p>
 <p><img alt="" src="../img/line.jpg"/></p>
 <div class="dcontent">
 <h2 style="font-size:22pt;font-family:方正小标宋简体;line-height: 160%;width:82%;margin-left:9%;letter-spacing:1px;"><?php echo $notificationList["infoTitle"];?></h2>
 <?php if($notificationList['startTime'] != "") echo '<p style="font-size:16pt;height:30px;line-height:30px;word-spacing:1pt;">（' . date("Y年m月d日",strtotime($notificationList["startTime"])) . '）</p>'; ?>
  <div style="text-align: left;font-size:16pt;line-height: 25px;word-spacing:1pt;margin-top:25px;" id="infoContent">
  	<?php echo $notificationList["infoContent"];?>
  </div>	 
  <div style="width: 100%;padding-top: 50px;">
  	<p style="text-indent: 0;text-align: left;">附件:</p>
  	<div class="pattach"><p>
  	<?php 
		$count = 0;
  		if(isset($attachList) && is_array($attachList)){
  			foreach($attachList as $row){
				if($count == 0){
					echo '<a href="'.$row["attachUrl"].'">'.$row["attachName"].'</a></p>';
				}else{
					echo '<p class="pattach"><a href="'.$row["attachUrl"].'">'.$row["attachName"].'</a></p>';
				}
				$count++;
  			}
  		}
  	?>
	</div>
  </p>
</div>
</div>
<!--endprint-->
</div>
</body>
</html>
    <script type="text/javascript">
	var page = 1; 
    var infoId = '<?php echo $infoId;?>';
    var deptId = '<?php echo $deptId;?>';
    var param={
	    'infoId': infoId,
        'deptId': deptId
	};
    //console.log(param);
    var hch = {
        inInt: function () {
            $.post("info_ajax.php?do=notice_getQianshou",param, function (res) {
	        	//console.log(typeof(res));
	        	if(res=='1'){
	        		$("#qianshou").val("已签收");
	        	}else{
	        		$("#qianshou").val("签收");
	        	}
		    });
        },
        qianshou:function(){
        	$.post("info_ajax.php?do=notice_qianshou",param, function (res) {
        		//console.log(res);
        		if(res=='1'){
        			$("#qianshou").val("已签收");
        		}
	        });
        }
    }
    $(function () {
        hch.inInt();
    });
</script>