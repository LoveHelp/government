<?php
	include_once 'sharedfilesmanager.php';
	
	//sharedfiles.php或者deptlist.php传过来的参数,区分用户角色
	//=0表示：用户既不是督察室人员也不是管理员
	//其他表示：用户是督察室人员或管理员
	//督察室及管理员，暂时不开发上传文件权限
	$did = 0;
	//督察室、管理员查看指定部门的文件
	if(!empty($_REQUEST['did'])){
		$did = $_REQUEST['did'];
		$deptid = $did;
	}
	//部门只能查看自己部门的文件，督察室及管理员除外
	$data = getfilesfromdir($deptid);
	$count = 0;
	if(!empty($data))
		$count = sizeof($data);
?>
<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>共享文件列表</title>
	<script type="text/javascript" src="js/jquery-1.8.2.min.js" ></script>
	<script type="text/javascript" src="js/uploadify/jquery.uploadify.min.js" ></script>
	<script type="text/javascript" src="js/layer/layer.js" ></script>
	<link rel="stylesheet" href="js/uploadify/uploadify.css" />
	<link rel="stylesheet" href="css/common.css" />
	<style type="text/css">
		div.showPub{
			visibility: hidden;
			position: absolute;
			overflow: hidden;
			border: 1px solid #CCC;
			background-color: darkgrey;
			border: 1px solid #333;
			padding: 5px;
			-moz-border-radius: 5px;
			-webkit-border-radius: 5px;
			border-radius: 5px;
		}
		div.showPub a.icon {
			background: url(img/glyphicons-halflings-white.png);
			background-repeat: no-repeat;
			background-position: -456px -145px;
			height: 14px;
			width: 14px;
			float: right;
			cursor: pointer;
			margin-top: 2px !important;
		}
		div.showPub a.icon:hover {
			background: url(img/glyphicons-halflings.png);
			background-repeat: no-repeat;
			background-position: -456px -145px;
		}
		a{
			cursor:pointer;
			text-decoration: none;	
		}
		table td{
			text-align: center;
		}
	</style>
</head>
<body class="main">
	<?php if(empty($did)){
		?><div class="top">
			<input type="button" id="uploadbtn" value="上传文件" class="button2" />
		</div>
		<div style="height: 5px;"></div>
		<?php
	}
	?>
	<div class="result">
		<!--定义查询返回结果框的范围ID-->
        <table border="0" cellpadding="4" cellspacing="1" class="table01">
            <thead>
                <tr class="table_title">
                    <td width="50px" class="table_title"> 序号</td>
                    <td class="table_title">文件名</td>
                    <td width="150px" class="table_title">上传时间</td>
                </tr>
            </thead>
            <tbody>
            	<?php
                if($count == 0){
                    ?><tr class="alternate_line1">
                    <td colspan="6" class="tip">
                        <font size="2">没有符合条件的记录</font>
                    </td>
                </tr><?php
                }else{
                    for($i=0; $i<$count; $i++){
                        $classname = 'alternate_line1';
                        if($i%2 == 0)
                            $classname = 'alternate_line2';            
                        ?><tr class="<?=$classname?>">
                            <td><?=$i+1?></td>
                            <td style="text-align: left;">
                            	<a href="<?=$data[$i]['furl']?>" target='_blank'>
                            		<?=$data[$i]['fname']?>
                            	</a>
                            </td>                       
                            <td><?=$data[$i]['uploadtime']?></td>
                        </tr><?php
                    }
                }?>
            </tbody>
        </table>
	</div>
	<div id="showPub" class="showPub">
		<a href="javascript:closepopup();" class="icon"></a>
	 	<div id="uploadDiv"></div>
	</div>
</body>
</html>
<script type="text/javascript">
$(function(){
	var index=0;
	$('#uploadDiv').uploadify({
		auto: true,
		buttonText: '选择文件',
		fileObjName: 'ufile',
		method: "POST",
		multi: true,
		swf:'js/uploadify/uploadify.swf',
		fileSizeLimit: 102400,
		showUploadedPercent: true, //是否实时显示上传的百分比，如20%
		showUploadedSize: true,
		removeCompleted: true, //上传完成自动删除
		removeTimeout: 5, //上传完成到删除的间隔时间
		uploader: 'sharedfilesmanager.php?do=uploadfiles',
		onUploadStart:function(file){
			index = index + 1;
			$('#uploadDiv').uploadify("settings", "formData", {'index': index, 'session':'<?=session_id();?>'});
		},
		onUploadError: function(file, errorCode, errorMsg, errorString){
			var txt = "the file "+ file.name + " uploads failed: " + errorString;
			layer.msg(txt, {icon: 5, time: 1000});
		},
		onQueueComplete:function(queueData){
			if(queueData.uploadsErrored == 0){
				layer.msg('文件全部上传成功！');
			}
			setTimeout('refresh()', 800);
		}
	});
	$("#uploadbtn").bind('click', function (){
		var bgDiv = document.createElement('div');
		var popUp = document.getElementById("showPub");
		popUp.style.top = "100px";
		var bwid = document.body.clientWidth;
		var vleft = (bwid - 420)/2;
		popUp.style.left = vleft + "px";
		popUp.style.width = "420px";
		popUp.style.minHeight = "50px";
		popUp.style.height = "auto";
		popUp.style.visibility = "visible";
		popUp.style.opacity = "0.9";
		popUp.style.filter = "Alpha(opacity=70)";
		popUp.style.zIndex = 101;
		//背景层加入页面
		var vHei = document.body.clientHeight;
		bgDiv.id = 'bgDiv';
		bgDiv.style.left = 0;
		bgDiv.style.top = 0;
		bgDiv.style.width="100%";
		bgDiv.style.height=vHei+"px";
		bgDiv.style.position="absolute"; 
		bgDiv.style.opacity = "0.6";
		bgDiv.style.zIndex = 100;
		bgDiv.style.display="block";
		document.body.appendChild(bgDiv);
	});
})

function refresh(){
	location.reload(true);
}

function closepopup(){
	var popUp = document.getElementById("showPub");
	popUp.style.visibility = "hidden";
	var bgdiv = document.getElementById('bgDiv');
	document.body.removeChild(bgdiv);
}
</script>