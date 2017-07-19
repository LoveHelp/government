<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
//error_reporting(0);//关闭提示
include_once "../constant.php";
include_once "information.php";
include_once "attachment.php";

$deptId = $_SESSION['userDeptID'];
$infoId = isset($_GET["infoId"]) ? $_GET["infoId"] : "";
$infoList=get_infoList(5,$infoId);
$infoTitle="";
$lawType="";
$infoSort=0;
if(is_array($infoList)){
	$infoTitle=$infoList["infoTitle"];
	$infoSort=$infoList["infoSort"];
	$lawType=$infoList["lawType"];
	$deptId=$infoList["deptId"];
	$deptName = get_deptName($deptId);
	$infoContent=$infoList["infoContent"];
	$addTime=$infoList["addTime"];
	$startTime=$infoList["startTime"];
}else{
	$addTime = date("Y-m-d");
	$startTime = date("Y-m-d");
}
?>

<!doctype html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<title>发布法律法规</title>
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/dept.css" />
<link rel="stylesheet" href="../css/editor-min.css" type="text/css" />
<script type="text/javascript" src="../js/jquery.min.js"></script>
<style type="text/css">
	.infotitle{width: 800px;}
	.spattach{padding-top: 20px;padding-left: 20px;}
	.spattach a{display: block;line-height: 30px;}
</style>
</head>

<body>

<div class="right-main">
	<form name="myForm" enctype="multipart/form-data" method="POST" action="law_insert_update.php">
		<input type="hidden" name="hd_infoId" value="<?php echo $infoId; ?>" />
		<div id="search" class="search">
		    <table border="0" cellpadding="0" cellspacing="1" class="tab">
			    <tbody>
			    	<tr>
				    	<td height="25" colspan="4" class="tab-title" align="center">发布法律法规</td>
				    </tr>
  					<tr>
				    	<td class="tab-td-title">标题
					    </td>
					    <td class="tab-td-content" colspan="3">
					    	<input class="infotitle" type="text" id="infoTitle" name="infoTitle" value="<?php echo $infoTitle; ?>">
        					<span id="Star">★</span>
					    </td>
				    </tr>
				    <tr>
				    	<td class="tab-td-title">类别
					    </td>
					    <td class="tab-td-content">
					    	<select name="lawType" id="lawType">
						        <?php 
									for ($i=1; $i<=count($lawTypeArray); $i++) {
										echo '<option value="' . $i . '">' . $lawTypeArray[$i] . '</option>';
									}
								?>
					        </select>	
					    </td>
					    <td class="tab-td-title">排序号</td>
					    <td class="tab-td-content">
						    <input type="text" name="infoSort" value="<?php echo $infoSort; ?>">   
						    
					    </td>
				    </tr>
				    <tr>
				    	<td class="tab-td-title">通知内容
					    </td>
					    <td class="tab-td-content" colspan="3" style="padding-bottom: 5px;padding-top: 5px;">
					    	<div id="bdeditor">
		                        <script type="text/javascript" charset="utf-8" src="../js/ueditor/ueditor.config.js"></script>
		                        <script type="text/javascript" charset="utf-8" src="../js/ueditor/ueditor.all.min.js"> </script>
		                        <script type="text/javascript" charset="utf-8" src="../js/ueditor/lang/zh-cn/zh-cn.js"></script>
		                        <script id="editor" name="infoContent" type="text/plain" style="width:95%;height:300px;">
									<?php if(isset($infoContent)){echo $infoContent;}else{echo "内容必填";}?>
								</script>
								<script type="text/javascript">  
								    //UE.getEditor('editor'); 
								    UE.getEditor("editor",{topOffset:0,autoFloatEnabled:false,autoHeightEnabled:false,autotypeset:{removeEmptyline:true},toolbars:[['fullscreen','source','undo','redo','bold','italic','underline','fontborder','strikethrough','removeformat','autotypeset','blockquote','pasteplain','forecolor','backcolor','insertorderedlist','insertunorderedlist','selectall','cleardoc','rowspacingtop','rowspacingbottom','lineheight','indent','justifyleft','justifycenter','justifyright','fontfamily','fontsize','justifyjustify','touppercase','tolowercase','simpleupload','emotion','insertvideo','map','date','time','spechars','preview','searchreplace'],['con','title','fork','guide','division','other','mystyle']],autoHeightEnabled:false,allowDivTransToP:false,autoFloatEnabled:true,enableAutoSave:false}); 
								</script>  
		              		</div>
					    </td>
				    </tr>
				    <tr>
				    	<td class="tab-td-title">施行日期
					    </td>
					    <td class="tab-td-content">
					    	<input type="text" name="startTime" readonly="readonly" onclick="WdatePicker()" value="<?php echo $startTime; ?>">
        					<span id="Star">★</span>
					    </td>
					    <td class="tab-td-title">录入日期</td>
					    <td class="tab-td-content">
						    <input type="text" name="addTime" readonly="readonly" value="<?php echo $addTime; ?>">
					    </td>
				    </tr>
				    <tr>
				    	<td class="tab-td-title">上传附件
					    </td>
					    <td class="tab-td-content" colspan="3">
					    	<span style="float: left; width: 350px;padding-bottom: 3px;">
					    		<input type="file" name="upfile" id="upfile">
					    		<input type="button" value="上 传" style="cursor:pointer" class="button1" onclick="hch.uploadFile();">
					    	</span>
					    	<table id="attachlist" border="0" cellpadding="0" cellspacing="1" class="tab" style="width: 50%;margin-top: 5px;margin-bottom: 5px;">
					    		<tbody>
					    			
					    		</tbody>
					    	</table>
					    	<input type="hidden" name="hd_attachUrls" />
					    	<input type="hidden" name="hd_attachNames" />
					    </td>
				    </tr>
				    <tr>
  <input type="hidden" name="hd_deptId" value="<?php echo $deptId; ?>" /> 
					    <td class="tab-td-content" colspan="4" style="text-align: center;">
						    <input type="button" value="发 布" style="cursor:pointer" class="button1" onclick="hch.check();">&nbsp;
						    <input type="button" value="关 闭" style="cursor:pointer" class="button1" onclick="window.parent.close();">
					    </td>
				    </tr>
			    </tbody>
		    </table>
	    </div>
    </form>
  
</div>

</body>
</html>

<script type="text/javascript" src="../js/layer/layer.js"></script>
<script src="../js/ajaxfileupload.js"></script>
<script type="text/javascript">
	var attachUrls="";
    var hch = {
        inInt: function () {
            this.showStyle();
            this.selAll();
            
	        this.del(0);
        },
        showStyle: function () {//间隔行显示样式
            $.each("table.tab tr.hang", function (i) {
                if (i % 2 > 0) {
                    $("table.tab tr.hang").eq(i).addClass("alternate_line2");
                }
            });
        },
        check:function(){
        	
        	//labAttach
        	var attachUrls = "";//$("label[name='labAttach']").html();
        	var attachNames="";
        	$('input[name=hd_attach]').each(function () {
                attachUrls += $(this).val() + ",";
            });
        	if(attachUrls){
        		attachUrls=attachUrls.substring(0,attachUrls.length-1);
        	}
        	$('label[name=labAttach]').each(function () {
                attachNames += $(this).html() + ",";
            });
        	if(attachNames){
        		attachNames=attachNames.substring(0,attachNames.length-1);
        	}
        	//console.log(attachUrls);
        	$("input[name='hd_attachUrls']").val(attachUrls);
        	$("input[name='hd_attachNames']").val(attachNames);
        	
        	var infoTitle = $("input[name='infoTitle']").val();
        	var infoSort = $("input[name='infoSort']").val();
        	var infoContent = $("textarea[name='infoContent']").val();
        	var startTime = $("input[name='startTime']").val();
        	if (!infoTitle) {
	            layer.msg("标题不能为空！");
	            $("input[name='infoTitle']").focus();
	            return false;
	        }
	        if (!startTime) {
	            layer.msg("施行日期不能为空！");
	            $("input[name='startTime']").focus();
	            return false;
	        }
	        if (!infoSort) {
	            layer.msg("排序不能为空！");
	            $("input[name='infoSort']").focus();
	            return false;
	        }
	        if(isNaN(infoSort)){
	        	layer.msg("排序只能为数字！");
	            $("input[name='infoSort']").focus();
	            return false;
	        }
	        
			document.forms[0].submit();
        },
        uploadFile:function(){
        	var lId = $("input[name='hd_infoId']").val();
        	var data={'infoId': lId};
        	$.ajaxFileUpload
			({
			 	url:'attachment_ajax.php',
        		type: 'post', 
			 	secureuri:false,
			 	fileElementId:'upfile',
			 	dataType: 'json',
			 	data:data,
			 	success: function (res)
			 	{
		            //console.log(res[0].attachUrl);
		            var ss="";
		            if(lId!=""){
		            	    ss="	<tr>"
								+"    	<td height=\"25\" colspan=\"3\" class=\"tab-title\" align=\"center\">已经上传附件清单</td>"
								+"    </tr>";
		        		for(var i=0;i<res.length;i++){
		        			ss+="    <tr>"
									+"	    <td class=\"tab-td-content\">"
									+res[i].attachName
									+"	    </td>"
									+"	    <td class=\"tab-td-content\" width=\"100\" style=\"text-align: center;\">"
									+"	    	<a href=\"javascript:void(0);\" onclick=\"hch.del('"+res[i].attachId+"');\">删除</a>"
									+"	    </td>"
									+"    </tr>";
		        		}
	        			$("#attachlist").html(ss);
		            }else if(!jQuery.isEmptyObject(res)){//添加
		            	ss+="    <tr>"
									+"	    <td class=\"tab-td-content\">"
									+"<label name=\"labAttach\">"+res.attachName+"</label>"
									+"<input type=\"hidden\" name=\"hd_attach\" value=\""+res.attachUrl+"\"/>"
									+"	    </td>"
									+"	    <td class=\"tab-td-content\" width=\"100\" style=\"text-align: center;\">"
									+"	    	<a href=\"javascript:void(0);\" onclick=\"hch.delRow(this);\">删除</a>"
									+"	    </td>"
									+"    </tr>";
	        			$("#attachlist").append(ss);
		            }
	        		
				},  
		        error:function(res, status, e){  
		            //console.log(res+"：");
		            //console.log(e);
		        }
			});
        },
        delRow:function(obj){
        	$(obj).parent().parent().remove();
        },
        del:function(attachId){
        	var lId = $("input[name='hd_infoId']").val();
        	$.post("attachment_ajax.php?do=attach_delete",{'infoId':lId,'attachId':attachId}, function (res) {
        		//console.log(res);
        		var ss="	<tr>"
								+"    	<td height=\"25\" colspan=\"3\" class=\"tab-title\" align=\"center\">已经上传附件清单</td>"
								+"    </tr>";
	        		for(var i=0;i<res.length;i++){
	        			ss+="    <tr>"
								+"	    <td class=\"tab-td-content\">"
								+res[i].attachName
								+"	    </td>"
								+"	    <td class=\"tab-td-content\" width=\"100\" style=\"text-align: center;\">"
								+"	    	<a href=\"javascript:void(0);\" onclick=\"hch.del('"+res[i].attachId+"');\">删除</a>"
								+"	    </td>"
								+"    </tr>";
	        		}
	        		$("#attachlist").html(ss);
	        },'json');
        }
    }
    $(function () {
        hch.inInt();
    });
</script>
