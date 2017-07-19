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
include_once "../constant.php";


$deptId = isset($_SESSION['userDeptID']) ? $_SESSION['userDeptID'] : 0;
$infoId = isset($_GET["id"]) ? $_GET["id"] : "";
$infoType = isset($_GET["type"]) ? $_GET["type"] : "";
$infoList=get_infoList($infoType,$infoId);
if(is_array($infoList)){
	$infoTitle=$infoList["infoTitle"];
	$addTime=$infoList["addTime"];
	$infoContent=$infoList["infoContent"];
	$attachList=get_attachList($infoId);
}

?>
<!doctype html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<title><?php echo $infoTypeArray[$infoType]?>详情</title>
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/dept.css" />
<style type="text/css">
body{margin: 0;padding: 0;overflow: auto;}
.container{margin-left: auto;margin-right: auto;position: relative;text-align: center;top: 50px;}
<?php if($infoType == 4) {?>
.container .dcontent{margin-left: auto;margin-right: auto;margin-top: 5px;padding-top: 10px;width: 1000px;top: 50px;height:auto;overflow: auto;}
<?php }else{ ?>
.container .dcontent{margin-left: auto;margin-right: auto;margin-top: 5px;padding-top: 10px;width: 1000px;top: 50px;height:auto;overflow: auto;border-top:2px solid red;}
<?php } ?>
span {color: #545454;}
.pattach{line-height: 30px;text-align: left;text-indent: 10px;}
.pattach a{font-size: 14px;color: #0066CC;}
.pattach a:hover{text-decoration: underline;color:#008200;}
img{width:100%;height:auto;}
</style>

</head>
<body>
<div class="container">
 <h2 style="line-height: 120px;color:#000;font-size:28px;"><?php echo $infoList["infoTitle"];?></h2>
 <?php 
 	if($infoType == "2"){
 		echo '<p style="font-size:16px;letter-spacing: 3px; color: red;line-height: 30px;">'.$infoList["infoCode"].'</p>';
 	}
 ?>
 
 <div class="dcontent">
  <p>
 	创建日期：<?php echo $infoList["addTime"];?>
 </p>
  <div style="text-align: left; padding-top: 30px;font-size:16px;line-height: 25px;">
  	<?php 
	$infoContent = str_replace('\"', '"', $infoList["infoContent"]);
	$infoContent = preg_replace('/[\n\r\t]/', '', $infoContent);
	echo $infoContent;
	/*if(is_array($attachList) && count($attachList) > 0){
		foreach($attachList as $a){
			if(substr($a["attachUrl"], -3) == "pdf"){
				echo '<embed style="width:100%;height:600px;" src="http://www.enedu.net' . $a["attachUrl"]  . '"></embed>';
			}
		}
	}*/
	?>
  </div>	 
  <div style="width: 500px;float: right;padding-top: 50px;">
  	<?php 
  		$showHtml="";
  		if(isset($attachList) && is_array($attachList)){
  			foreach($attachList as $row){
  				$showHtml .= '<p class="pattach"><a href="'.$row["attachUrl"].'">'.$row["attachName"].'</a></p>';
  			}
  		}
  		if($showHtml!="")
  		{
  			$showHtml='<p class="pattach" style="text-indent: 0;">附件：</p>'.$showHtml;
  			echo $showHtml;
  		}
  	?>
  </p>
</div>

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
    });
</script>