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
$noticeList=get_infoList(1,$infoId);
if(is_array($noticeList)){
	$infoTitle=$noticeList["infoTitle"];
	$infoCode=$noticeList["infoCode"];
	$infoContent=$noticeList["infoContent"];
	$attachList=get_attachList($infoId);
}
$count=get_infoCount($infoId,$deptId);

?>
<!doctype html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<title>督查通知详情</title>
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/dept.css" />
<style type="text/css">
body{margin: 0 auto;padding: 0;overflow: auto;width:800px;text-align:center;}
.container{text-align: center;width:595px;margin:140px 102px 132px 102px; min-height:842px;height:auto;}
.container .dcontent{margin-left: auto;margin-right: auto;margin-top: 5px;padding-top: 53px;width: 595px;top: 50px;height:auto;overflow: auto;}
span {color: #545454;}
.pattach{line-height: 35px;text-align: left;text-indent: 50px;}
.pattach a{font-size: 14px;color: #0066cc;}
.pattach a:hover{text-decoration: underline;color:#0066CC;}
.dcontent p, .dcontent a{font-size:16pt !important;font-weight:600;}
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
<h5 align="center" style="margin: 25px;">
	<?php if($count>0){?>
		<input type="button" id="qianshou" value="" class="button1" onclick="hch.qianshou();" style="cursor:hand;"/>&nbsp;&nbsp;
	<?php }?>
	<input type="button" class="button1" value="打 印"  onclick="preview()">	
	<!--<input id="printpreview" onclick="printpreview()" type="button" class="button1" value="打印预览" />-->
</h5>

<div style="border:1px solid #525252;">
<!--startprint-->
<div class="container">	
 <h1 style="padding-top:30px;color: red;margin-bottom: 50px;font-size: 56pt;font-family:方正小标宋简体"><img alt="" src="../img/notice.jpg"/></h1>
 <p style="line-height: 35px;height: 35px;font-size:16pt;font-weight:500;font-family:仿宋_GB2312;font-weight:600;">
 	<?php echo $noticeList["infoCode"];?>
 </p>
 <p><img alt="" src="../img/line.jpg"/></p>
 <div class="dcontent">
 <h2 style="font-size:22pt;font-family:方正小标宋简体;line-height: 160%;width:80%;margin-left:10%;word-spacing:1pt"><?php echo $noticeList["infoTitle"];?></h2>
 <?php if($noticeList['startTime'] != "") echo '<p style="font-size:16pt;height:30px;line-height:30px;word-spacing:1pt">（' . date("Y年m月d日",strtotime($noticeList["startTime"])) . '）</p>'; ?>
  <div style="text-align: left;font-size:16pt;line-height: 25px;margin-top:25px;word-spacing:1pt" id="infoContent">
  	<?php echo $noticeList["infoContent"];?>
  </div>	 
  <div style="width: 100%;float: left;padding-top: 50px;">
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

<script type='text/javascript' src="../js/jquery.min.js"/>
<script type="text/javascript" src="../js/layer/layer.js"></script>
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
		/*if ((navigator.userAgent.indexOf('MSIE') >= 0) && (navigator.userAgent.indexOf('Opera') < 0)){
			//alert('你是使用IE');
		}else if (navigator.userAgent.indexOf('Firefox') >= 0){
			$("#printpreview").css("display", "none");
		}else if (navigator.userAgent.indexOf('Opera') >= 0){
			$("#printpreview").css("display", "none");
		}else{
			//alert('你是使用其他的浏览器浏览网页！');
		}*/
    });
</script>