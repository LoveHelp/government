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

$infoId = isset($_GET["infoId"]) ? $_GET["infoId"] : "";
$infoList=get_infoList(2,$infoId);
$infoTitle="";
	$recvDeptNames="";
	$signDeptNames="";
if(is_array($infoList)){
	$infoTitle=$infoList["infoTitle"];
	$infoCode=$infoList["infoCode"];
	$deptId=$infoList["deptId"];
	$deptName = get_deptName($deptId);
	$infoContent=$infoList["infoContent"];
	$addTime=$infoList["addTime"];
	$startTime=$infoList["startTime"];
	$recvDeptIds=$infoList["recvDeptIds"];
	if(strlen($recvDeptIds)>0){
		$deptNameArray = get_deptNameList($recvDeptIds);
		foreach($deptNameArray as $row){
			$recvDeptNames.=$row["deptName"].",";
		}
		if(strlen($recvDeptNames)>0){
			$recvDeptNames=substr($recvDeptNames,0,strlen($recvDeptNames)-1);
		}
	}
	$signDeptIds=$infoList["signDeptIds"];
	if(strlen($signDeptIds)>0){
		$deptNameArray_sign = get_deptNameList($signDeptIds);
		foreach($deptNameArray_sign as $row){
			$signDeptNames.=$row["deptName"].",";
		}
		if(strlen($signDeptNames)>0){
			$signDeptNames=substr($signDeptNames,0,strlen($signDeptNames)-1);
		}
	}
}else{
	$nowYear=date("Y");
	$infoCode="宛政督通字〔".$nowYear."〕号";
	$infoContent="内容必填";
	$deptId = isset($_SESSION['userDeptID']) ? $_SESSION['userDeptID'] : 0;
	$deptName = get_deptName($deptId);
	$addTime = date("Y-m-d");
	$startTime=date("Y-m-d");
	$recvDeptIds="";
	$deptListArray=get_deptList();
	if(is_array($deptListArray)){
		foreach($deptListArray as $row){
			$recvDeptIds.=$row["deptId"].",";
			$recvDeptNames.=$row["deptName"].",";
		}
		if(strlen($recvDeptIds)>0){
			$recvDeptIds=substr($recvDeptIds,0,strlen($recvDeptIds)-1);
		}
		if(strlen($recvDeptNames)>0){
			$recvDeptNames=substr($recvDeptNames,0,strlen($recvDeptNames)-1);
		}
	}
}
?>

<!doctype html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<title>发布督查通报</title>
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/dept.css" />
<link rel="stylesheet" href="../css/editor-min.css" type="text/css" />
<script type="text/javascript" src="../js/jquery.min.js"></script>
<style type="text/css">
	.infotitle{width: 800px;}
	.spattach{padding-top: 20px;padding-left: 20px;}
	.spattach a{display: block;line-height: 30px;}
	input[name="chkAll"]{
	    width: 20px;
	    height: 20px;
	    float: left;
	}
	input[name="chkAttach"]{
		width: 60px;
	}
	.show-dept .chkall {
		padding-left: 10px;
		padding-top: 10px;
	    line-height: 25px;
	    height: 25px;
	}
	.show-dept .chkall label {
	    height: 20px;
	    line-height: 20px;
	    float: left;
	    padding-left: 2px;
	}
</style>
</head>

<body>

<div class="right-main">
	<form name="myForm" enctype="multipart/form-data" method="POST">
		<input type="hidden" name="hd_infoId" value="<?php echo $infoId; ?>" />
		<div id="search" class="search">
		    <table border="0" cellpadding="0" cellspacing="1" class="tab">
			    <tbody>
			    	<tr>
				    	<td height="25" colspan="4" class="tab-title" align="center">发布督查通报</td>
				    </tr>
  					<tr>
				    	<td class="tab-td-title">通报标题
					    </td>
					    <td class="tab-td-content" colspan="3">
					    	<input class="infotitle" type="text" id="infoTitle" name="infoTitle" value="<?php echo $infoTitle; ?>">
        					<span id="Star">★</span>
					    </td>
				    </tr>
				    <tr>
				    	<td class="tab-td-title">通报编号
					    </td>
					    <td class="tab-td-content">
					    	<input type="text" class="infotitle" name="infoCode" value="<?php echo $infoCode; ?>">
        					<span id="Star">★</span>
					    </td>
					    <td class="tab-td-title">发布单位</td>
					    <td class="tab-td-content">
						    <input type="text" name="deptName" readonly="readonly" value="<?php echo $deptName; ?>">   
						    <input type="hidden" name="hd_deptId" value="<?php echo $deptId; ?>" /> 
					    </td>
				    </tr>
				    <tr>
				    	<td class="tab-td-title">接收单位
					    </td>
					    <td class="tab-td-content" colspan="3" style="padding-top:5px;">
					    	<textarea name="recvDeptNames" style="width: 95%;" rows="5" readonly="readonly" onclick="hch.open_dept();"><?php echo $recvDeptNames;?></textarea>
					    	<div><font style="color: red;font-size: 14px;">（默认发送给所有部门）</font></div>
					    	<input type="hidden" name="hd_recvDeptIds" value="<?php echo $recvDeptIds;?>"/>
					    </td>
				    </tr>
				    <?php 
				    	if($signDeptNames!=""){
					    	$res='<tr>'
							    	.'<td class="tab-td-title">已签收单位'
								    .'</td>'
								    .'<td class="tab-td-content" colspan="3" style="padding-top:5px;">'
								    	.'<textarea name="recvDeptNames" style="width: 90%;" rows="5" readonly="readonly">'.$signDeptNames.'</textarea>'
								    .'</td>'
							    .'</tr>';
							echo $res;
				    	}
				    ?>
				    <tr>
				    	<td class="tab-td-title">通报内容
					    </td>
					    <td class="tab-td-content" colspan="3" style="padding-bottom: 5px;padding-top: 5px;">
					    	<div id="bdeditor">
		                        <script type="text/javascript" charset="utf-8" src="../js/ueditor/ueditor.config.js"></script>
		                        <script type="text/javascript" charset="utf-8" src="../js/ueditor/ueditor.all.min.js"> </script>
		                        <script type="text/javascript" charset="utf-8" src="../js/ueditor/lang/zh-cn/zh-cn.js"></script>
		                        <script id="editor" name="infoContent" type="text/plain" style="width:95%;height:300px;">
									<?php echo $infoContent;?>
								</script>
								<script type="text/javascript">  
								    //UE.getEditor('editor'); 
								    UE.getEditor("editor",{topOffset:0,autoFloatEnabled:false,autoHeightEnabled:false,autotypeset:{removeEmptyline:true},toolbars:[['fullscreen','source','undo','redo','bold','italic','underline','fontborder','strikethrough','removeformat','autotypeset','blockquote','pasteplain','forecolor','backcolor','insertorderedlist','insertunorderedlist','selectall','cleardoc','rowspacingtop','rowspacingbottom','lineheight','indent','justifyleft','justifycenter','justifyright','fontfamily','fontsize','justifyjustify','touppercase','tolowercase','simpleupload','emotion','insertvideo','map','date','time','spechars','preview','searchreplace'],['con','title','fork','guide','division','other','mystyle']],autoHeightEnabled:false,allowDivTransToP:false,autoFloatEnabled:true,enableAutoSave:false}); 
								</script>  
		              		</div>
		              		<p style="color: red;">（注：不能直接从pdf中复制内容到此编辑器！可以将pdf中内容复制到txt中，再从txt中复制粘贴到此编辑器。）</p>
					    </td>
				    </tr>
				    <tr>
				    	<td class="tab-td-title">通报时间
					    </td>
					    <td class="tab-td-content">
					    	<input type="text" name="startTime" readonly="readonly" onclick="WdatePicker()" value="<?php echo $startTime; ?>">
        					<span id="Star">★</span>
					    </td>
					    <td class="tab-td-title">发布时间</td>
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

<div class="show-dept" id="tree_dept" title="选择接收部门" style="display:none;">
  	<div style="padding: 10px 10px;background-color:#DEEFFF;height: 694px;">
  		<div class="chkall">
  			<input type="checkbox" name="chkAll" id="chkAllDept" /><label for="chkAllDept">全选</label>
  		</div>
  		<div id="deptList"></div>
  		<div style="clear: both;line-height: 35px;text-align: center;padding-top: 20px;">
  			<input type="submit" value="确 定" style="cursor:pointer" class="button1" onclick="hch.check_dept();">&nbsp;
        <input type="button" value="关 闭" style="cursor:pointer" class="button1" onclick="hch.close_dept();"> 
  		</div>
  	</div>
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
            
	        this.bind_dept();//绑定部门
	        this.del(0);
        },
        showStyle: function () {//间隔行显示样式
            $.each("table.tab tr.hang", function (i) {
                if (i % 2 > 0) {
                    $("table.tab tr.hang").eq(i).addClass("alternate_line2");
                }
            });
        },
        open_dept:function(){
        	this.index_dept = layer.open({
                type: 1,
                title: $('#tree_dept').attr("title"),
                skin: 'layui-layer-rim', //加上边框
                area: ['80%', '80%'], //宽高
                content: $("#tree_dept")
           });
	       hch.get_checkedDept();//获取选中部门
        },
		bind_dept:function(){
        	$.post("../xitong/leader_insert_update.php?do=leader_getDeptList", function (res) {
        		//console.log(res);
        		$("#deptList").html(res);
	        },'text');
        },
        get_checkedDept:function(){
        	var infoId = $("input[name='hd_infoId']").val();
        	if(!infoId){//添加，默认全选
        		$(':checkbox[name="ckbDept"]').attr('checked', true);
        		$(':checkbox[name="chkAll"]').attr('checked', true);
        	}else{//修改，从数据库中获取选中
        		var ckbDeptList = $("input[name='hd_recvDeptIds']").val();
	          	if(ckbDeptList){
	          		var checkboxs = $("input[name='ckbDept']");//document.getElementsByName("ckbDept");
					   for (var i = 0; i < checkboxs.length; i++) {//获取选中状态
					    var v=checkboxs[i].value;
					    //console.log(v);
						if(ckbDeptList.indexOf(v+",")==0){//字符串以‘v,’开头
							checkboxs[i].checked = true;
						}else if(ckbDeptList.indexOf(v)==0 && ckbDeptList.length==v.length){//字符串=‘v’
							checkboxs[i].checked = true;
						}else if(ckbDeptList.endsWith(","+v)){//字符串以‘,v’结尾
							checkboxs[i].checked = true;
						}else if(ckbDeptList.indexOf(","+v+",")>0){
							checkboxs[i].checked = true;
						}
					}
	          	}
        	}
        },
        close_dept: function () {
            layer.close(this.index_dept);
        },
        selAll: function () {
            $(':checkbox[name="chkAll"]').click(function () {//   全选/全不选
                var $this = $(this);
                if ($this.attr('checked')) {
                    $(':checkbox[name="ckbDept"]').attr('checked', true);
                } else {
                    $(':checkbox[name="ckbDept"]').removeAttr('checked');
                }
            });
        },
        check_dept:function(){
        	var deptIds="";
        	var deptNames="";
        	$(':checkbox[name=ckbDept][checked]').each(function () {
                deptIds += $(this).val() + ",";
                deptNames += $(this).next().html() + ",";
            });
            if (deptIds.length == 0) {
                layer.msg('您还没有选择');
                return false;
            }
            deptIds = deptIds.substr(0, deptIds.length - 1);
            deptNames = deptNames.substr(0, deptNames.length - 1);
            $("input[name='hd_recvDeptIds']").val(deptIds);
            $("textarea[name='recvDeptNames']").val(deptNames);
            //console.log(deptIds);
            //console.log(deptNames);
            this.close_dept();
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
        	var infoCode = $("input[name='infoCode']").val();
        	var startTime = $("input[name='startTime']").val();
        	if (!infoTitle) {
	            layer.msg("标题不能为空！");
	            $("input[name='infoTitle']").focus();
	            return false;
	        }
	        if (!infoCode) {
	            layer.msg("通知编号不能为空！");
	            $("input[name='infoCode']").focus();
	            return false;
	        }
	        if (!startTime) {
	            layer.msg("通报日期不能为空！");
	            $("input[name='startTime']").focus();
	            return false;
	        }
	        
        	var infoContent = UE.getEditor('editor').getContent();
        	//console.log(infoContent);
        	var addTime = $("input[name='addTime']").val();
        	var hd_deptId = $("input[name='hd_deptId']").val();
        	var hd_recvDeptIds = $("input[name='hd_recvDeptIds']").val();
        	var hd_infoId = $("input[name='hd_infoId']").val();
        	var hd_attachUrls = $("input[name='hd_attachUrls']").val();
        	var hd_attachNames = $("input[name='hd_attachNames']").val();
	        var param={
	        	'infoTitle': infoTitle,
                'infoContent': infoContent,
                'addTime': addTime,
                'infoCode':infoCode,
                'startTime':startTime,
                'hd_deptId':hd_deptId,
                'hd_recvDeptIds':hd_recvDeptIds,
                'hd_infoId':hd_infoId,
                'hd_attachUrls':hd_attachUrls,
                'hd_attachNames':hd_attachNames
	        }
	        
			layer.confirm("是否发送短息？", {icon:3, title:"短信提示"}, 
	        	function(index){//确定按钮回调函数
					var sms_param={};
		        	var deptids = $("input[name='hd_recvDeptIds']").val();
		        	if($("input[name='hd_recvDeptIds']").length > 0 && $.trim(deptids).length > 0){
		        		sms_param = {
		        			'deptids': deptids,
		        			'smstype': 1,
		        			'title' : infoCode+" "+infoTitle
		        		};
		        		$.post('../xitong/smsmanager.php?do=sendtodepts', sms_param, function(res){
		        			//处理返回结果
		        		}, 'json');
	        		}
		        	//document.forms[0].submit();
		        	hch.doSubmit(param);
	        	},function(index){//取消回调函数
		        	//document.forms[0].submit();
		        	hch.doSubmit(param);
	        	}
	        );	
        },
        doSubmit:function(param){
        	$.post("notification_insert_update.php",param, function (res) {
      			//console.log("JSON.stringify："+JSON.stringify(res));
      			//console.log(typeof(res));
      			if(res){
      				if(res=="1"){
      					layer.msg('发布成功！');
      					setTimeout("window.parent.close();",1000);
      				}else if(res=="3"){
      					layer.msg('修改成功！');
      					setTimeout("window.parent.close();",1000);
      				}
      			}else{
      				layer.msg("操作失败！");
      			}
	        },'text');
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
	//全选/全不选    	
	$(document).on('click','.selall',function(item){
		var $this = $(this);
		var i=$(this).attr('id').replace('selall','');
	    if ($this.attr('checked')) {
	        $('#deptList'+i+' :checkbox[name="ckbDept"]').attr('checked', true);
	    } else {
	        $('#deptList'+i+' :checkbox[name="ckbDept"]').removeAttr('checked');
	    }
    });    
</script>
