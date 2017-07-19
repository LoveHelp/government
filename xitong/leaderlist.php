<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
error_reporting(0);//关闭提示 
include_once "../mysql.php";
include_once "../constant.php";
header("Content-type:text/html;charset=utf-8");
$mLink=new mysql;

$where=" where 1=1";

if(!empty($_POST["leaderName_S"])){
	$where.=" and leaderName like '%".$_POST["leaderName_S"]."%'";
}
$where .= " order by leaderSort";

$res=$mLink->getAll("select * from leader".$where);
//echo json_encode($res);

$mLink->closelink();
?>

<!doctype html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<title>领导管理</title>
<link rel="stylesheet" href="../css/style.css?v=2" />
<link rel="stylesheet" href="../css/dept.css" />
</head>
<style>
.tab-td-title{width:120px;}
</style>
<body>

<div class="right-main">
	<div id="top_div">
		<form action="leaderlist.php" method="POST">
			<!--定义查询条件路入框的范围ID-->
			<table border="0" cellpadding="0" cellspacing="1" class="tab">
				<tr>
					<td height="25" colspan="3" class="tab-title" align="center">领导管理</td>
				</tr>
				<tr>
					<td class="tab-td-title">姓名</td>
					<td class="tab-td-content" style="width:200px;">
						<input type="text" name="leaderName_S" value="<?php if(!empty($_POST['leaderName_S'])){echo $_POST['leaderName_S'];}else{echo "";}?>" style="width:190px;">
					</td>
					<td style="background-color:#ECF6FB; padding-left:5px;">
						<input type="submit" value="查 询" style="cursor:pointer" class="button1">
						<input type="button" value="添加" class="button1" style="cursor:pointer" onclick="hch.open('0');">
						<input type="hidden" name="hd_leaderId" value="0" />
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div style="height:10px;"></div>  
	<div id="result" class="search"><!--定义查询返回结果框的范围ID-->
		<table border="0" cellpadding="6" cellspacing="1" class="tab" style="background-color:#bebabb;">
			<tr> 
				<td class="tab-title">姓名</td>
				<td class="tab-title">类型 </td>
				<td class="tab-title">分管部门 </td>
				<td class="tab-title">头像管理 </td>      
			</tr>  
			<?php 
				if(!empty($res)){
					foreach ($res as $info){
			?>		
			<tr class="hang alternate_line1" style="cursor: pointer;">
				<td style="text-align:center;" onclick="hch.open('<?php echo $info['leaderId']?>');"><?php echo $info['leaderName']?></td>
				<td style="text-align:center;" onclick="hch.open('<?php echo $info['leaderId']?>');">	
				<?php 
					$type=$info['leaderType'];
					echo $leaderType[$type];
				?>	
				</td>
				<td style="text-align:center;" onclick="hch.open_dept('<?php echo $info['leaderId']?>');">分管部门</td>
				<td style="text-align:center;" onclick="hch.openUploadTouxiang('<?php echo $info['leaderId']?>');" id="td<?php echo $info['leaderId']?>">
					<?php if(!empty($info['leaderPhoto'])){echo "修改头像";}else{echo "上传头像";}?>
				</td>
			</tr>
			<?php          
			  }}
			?>
		</table>
	</div>
	<div class="show" id="tree" title="添加/修改领导信息" style="display:none;">
		<div style="padding: 10px 10px;background-color:#DEEFFF;height:auto;">
			<table border="0" cellpadding="0" cellspacing="1" class="tab">
				<tr>
					<td class="tab-td-title">姓名</td>
					<td class="tab-td-content">
						<input type="text" name="leaderName" value="">
						<span id="Star">★</span>
					</td>
					<td class="tab-td-title">职位</td>
					<td class="tab-td-content">
						<input type="text" name="leaderpost" value="">
						<span id="Star">★</span>
					</td>
				</tr>
				<tr>
					<td class="tab-td-title">所属类型</td>
					<td class="tab-td-content">
						<select name="leaderType"> 
							<?php 
								if(!empty($leaderType)){
									for ($i=1; $i<=count($leaderType); $i++) {
										echo '<option value="'.$i.'">'.$leaderType[$i].'</option>';
									}
								}
							?>
						</select>	  
					</td>
					<td class="tab-td-title">序号</td>
					<td class="tab-td-content">
						<input type="text" name="leaderSort" value="0">
						<span id="Star"></span>
					</td>
				</tr>
				<tr>
					<td class="tab-td-title">主要分工</td>
					<td class="tab-td-content" colspan="3" style="height: 90px;padding-top:10px;">
						<textarea name="leaderwork" rows="5" style="width:98%;"></textarea>
					</td>
				</tr>
				<tr>
					<td class="tab-td-title">领导简介</td>
					<td class="tab-td-content" colspan="3" style="height: 90px;padding-top:10px;">
						<textarea name="discription" rows="5" style="width:98%;"></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="4" id="searchCon"><!--定义好摆放按钮的TD的ID -->
						<input type="submit" value="提 交" style="cursor:pointer" class="button1" onclick="hch.check();">&nbsp;
						<!--<input type="reset" value="重 置" style="cursor:pointer" class="button1" onclick="hch.rewrite();"> &nbsp;-->  
						<input type="button" value="关 闭" style="cursor:pointer" class="button1" onclick="hch.close();"> 
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="show-dept" id="tree_dept" title="分管部门" style="display:none;">
		<div style="padding: 10px 10px;background-color:#DEEFFF;height: 644px;">
			<div id="deptList"></div>
			<div style="clear: both;line-height: 35px;text-align: center;padding-top: 20px;">
				<input type="submit" value="提 交" style="cursor:pointer" class="button1" onclick="hch.chargeDept();">&nbsp;
				<input type="button" value="关 闭" style="cursor:pointer" class="button1" onclick="hch.close_dept();"> 
			</div>
		</div>
	</div>
	<div class="show-touxiang" id="tree_touxiang" title="上传头像" style="display:none;">
		<div style="padding: 10px 10px;background-color:#DEEFFF;height: 444px;">
			<div class="changeUserPic">
				<div class="preview" style="padding-left: 30px;padding-top: 45px;">  
					<div class="previewPic" style="width: 200px;position: relative;float: left;">  
						<img id="showimg" width="170px" height="200px"/>  
					</div>  
					<div class="choosePic" style="height: 200px;">  
						<p style="color: red;font-weight: bold;line-height: 40px;padding-top: 10px;">
							1、文件大小上传限制：不能大于2M<br />
							2、当前系统支持：.jpg,.gif,.jpeg,.png格式！
						</p>
						<p style="padding-top: 30px;line-height: 45px;">
							<input type="file" name="leaderPhoto" id="leaderPhoto" class="UploadImg" style="border: 0;"/><br />
							<input type="text" name="pic_url" disabled="disabled" style="display: none;width: 400px;" />
						</p>   
					</div>  
				</div>  
				<div class="clear"></div>  
				<div class="savePicture" style="height: 100px;line-height: 100px;text-align: right;padding-right: 100px;">  
					<input type="submit" value="提 交" style="cursor:pointer" class="button1" onclick="hch.aFileUpload();"/>&nbsp;
					<input type="button" value="关 闭" style="cursor:pointer" class="button1" onclick="hch.close_touxiang();"> 
				</div>  
			</div>  
		</div>
	</div>
</div>
</body>
</html>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/layer/layer.js"></script>
    
    <script src="../js/ajaxfileupload.js"></script>
    
    <script type="text/javascript">
    var hch = {
        inInt: function () {
            this.showStyle();
            if (typeof String.prototype.endsWith != 'function') { 
            	String.prototype.endsWith = function(suffix) {  
            		return this.indexOf(suffix, this.length - suffix.length) !== -1; 
            	};
            }
        },
        showStyle: function () {//间隔行显示样式
            $.each("table.tab tr.hang", function (i) {
                if (i % 2 > 0) {
                    $("table.tab tr.hang").eq(i).addClass("alternate_line2");
                }
            });
        },
        open: function (leaderId) {

            this.index = layer.open({
                type: 1,
                title: $('#tree').attr("title"),
                skin: 'layui-layer-rim', //加上边框
                area: ['700px', 'auto'], //宽高
                content: $("#tree")
            });
	        
            $("input[name='hd_leaderId']").val(leaderId);
            if(leaderId!='0'){//修改，绑定初始值
            	$.post("leader_insert_update.php?do=leader_getRow",{'leaderId':leaderId}, function (res) {
	                //console.log(res);
	                //console.log(res.leaderId);
	                $("input[name='leaderName']").val(res.leaderName);
					$("input[name='leaderpost']").val(res.leaderpost);
	                $("select[name='leaderType']").val(res.leaderType);
	                $("input[name='leaderSort']").val(res.leaderSort);
					$("textarea[name='leaderwork']").val(res.leaderwork);
	                $("textarea[name='discription']").val(res.discription);
	            },'json');
            }else{
	                $("input[name='leaderName']").val("");
					$("input[name='leaderpost']").val("");
	                $("select[name='leaderType']").val("1");
	                $("input[name='leaderSort']").val("0");
					$("textarea[name='leaderwork']").val("");
	                $("textarea[name='discription']").val("");
            }

        },
        open_dept:function(leaderId){
        	this.index_dept = layer.open({
                type: 1,
                title: $('#tree_dept').attr("title"),
                skin: 'layui-layer-rim', //加上边框
                area: ['90%', '80%'], //宽高
                content: $("#tree_dept")
            });
            $("input[name='hd_leaderId']").val(leaderId);
            
	        this.bind_dept(leaderId);//绑定部门

        },
        openUploadTouxiang:function(leaderId){
        	this.index_touxiang = layer.open({
                type: 1,
                title: $('#tree_touxiang').attr("title"),
                skin: 'layui-layer-rim', //加上边框
                area: ['700px', '500px'], //宽高
                content: $("#tree_touxiang")
            });
            $("input[name='hd_leaderId']").val(leaderId);
            this.bind_leaderPhoto(leaderId);
        },
        bind_leaderPhoto:function(leaderId){
        	$.post("leader_insert_update.php?do=leader_getLeaderPhoto",{'leaderId':leaderId}, function (res) {
        		if(res.leaderPhoto){
        			$("input[name='pic_url']").val(res.leaderPhoto);
        			$("input[name='pic_url']").show();
        			$("#showimg").attr("src",res.leaderPhoto);
        		}else{
        			$("input[name='pic_url']").hide();
        			$("#showimg").attr("src","/government/img/logo.jpg");
        		}
	        },'json');
        },
//      bind_dept:function(){
//      	$.post("leader_insert_update.php?do=leader_getDeptList", function (res) {
//      		//console.log(res[0].deptId);
//      		var ss="";
//      		for(var i=0;i<res.length;i++){
//      			ss+="<p><input type=\"checkbox\" name=\"ckbDept\" value=\""+res[i].deptId+"\" id=\""+res[i].deptId+"\"/><label for=\""+res[i].deptId+"\">"+res[i].deptName+"</label></p>";
//      		}
//      		$("#deptList").html(ss);
//	        },'json');
//      },
		bind_dept:function(leaderId){
        	$.post("leader_insert_update.php?do=leader_getDeptList", function (res) {
        		//console.log(res);
        		$("#deptList").html(res);
	        	hch.get_checkedDept(leaderId);//获取选中部门
	        },'text');
        },
        get_checkedDept:function(leaderId){
        	$.post("leader_insert_update.php?do=leader_getDeptListByLeaderId",{'leaderId':leaderId}, function (res) {
          		//console.log(res);
          		var ckbDeptList = res.deptIds;
          		//console.log(ckbDeptList);
          		if(ckbDeptList){
          			var checkboxs = $("input[name='ckbDept']");//document.getElementsByName("ckbDept");
				    for (var i = 0; i < checkboxs.length; i++) {//获取选中状态
				    	var v=checkboxs[i].value;
				    	//console.log(v);
//				        if(ckbDeptList.indexOf(v)>-1){
//				        	checkboxs[i].checked = true;
//				        }
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
	        },'json');
        },
        close: function () {
            layer.close(this.index);
        },
        close_dept: function () {
            layer.close(this.index_dept);
        },
        close_touxiang: function () {
            layer.close(this.index_touxiang);
        },
        check:function(){
        	var leaderName = $("input[name='leaderName']").val();
			var leaderpost = $("input[name='leaderpost']").val();
        	var leaderType = $("select[name='leaderType']").val();
        	var leaderSort = $("input[name='leaderSort']").val();
			var leaderwork = $("textarea[name='leaderwork']").val();
        	var discription = $("textarea[name='discription']").val();
        	
        	//console.log(leaderPhoto);
        	
	        if (!leaderName) {
	            layer.msg("领导姓名不能为空！");
	            return false;
	        }

			if (!leaderpost) {
	            layer.msg("领导职位不能为空！");
	            return false;
	        }
        	
	        if (!leaderSort) {
	            layer.msg("排序不能为空！");
	            return false;
	        }
	        if(isNaN(leaderSort)){
	        	layer.msg("排序只能为数字！");
	            return false;
	        }
            
	        var leaderId=$("input[name='hd_leaderId']").val();
	        //console.log(leaderId);
	        var param={
	        	'leaderId': leaderId,
				'leaderpost': leaderpost,
                'leaderName': leaderName,
                'leaderType': leaderType,
                'leaderSort':leaderSort,
				'leaderwork':leaderwork,
                'discription':discription
	        }
	        //console.log(param);
	        if(leaderId=='0'){//添加
	        	$.post("leader_insert_update.php?do=leader_insert",param, function (res) {
      				//console.log(res);
      				if(res!="0"){
      					//layer.msg("添加成功！");
      					layer.msg("添加成功");
            			location.reload();
      				}else{
      					layer.msg("添加失败！");
      				}
	                
	            });
	        }else{//修改
	        	$.post("leader_insert_update.php?do=leader_update",param, function (res) {
	                if(res=="1"){
      					//layer.msg("修改成功！");
      					alert("修改成功");
            			location.reload();
      				}else{
      					layer.msg("修改失败！");
      				}
	            });
	        }
	        
        },
        chargeDept:function(){
        	var deptIds="";
        	$(':checkbox[name=ckbDept][checked]').each(function () {
                deptIds += $(this).val() + ",";
           });
            if (deptIds.length == 0) {
                layer.msg('您还没有选择');
                return false;
            }
            deptIds = deptIds.substr(0, deptIds.length - 1);
            var leaderId = $("input[name='hd_leaderId']").val();
            //console.log(deptIds);
        	$.post("leader_insert_update.php?do=leader_updateDept",{'deptIds':deptIds,'leaderId':leaderId}, function (res) {
      			//console.log(res);
      			if(res=="0"){
      				layer.msg("操作成功！");
            		hch.close_dept();
      			}else{
      				layer.msg("操作失败！");
      			} 
	        });
        },
        aFileUpload:function(){
        	var lId = $("input[name='hd_leaderId']").val();
        	var data={'leaderId': lId};
        	$.ajaxFileUpload
			({
			 	url:'leader_updateLeaderPhoto.php', //你处理上传文件的服务端
        		type: 'post', 
			 	secureuri:false,
			 	fileElementId:'leaderPhoto',
			 	dataType: 'json',
			 	data:data,
			 	success: function (res)
			 	{
		            //console.log(res.codeNum);
					var errCode = res.codeNum;  
		            var errMsg  = res.msg;  
		            //errCode为0、1、2、3、5、6均为上传不成功  
		            if(errCode==0||errCode==1||errCode==2||errCode==3||errCode==5||errCode==6){  
		                $('.choosePic span').html(errMsg);  
		                return;  
		            } else if(errCode == 4){ 
		            	layer.msg("头像上传成功！"); 
		            	//console.log(res.path);
	                	$("input[name='pic_url']").val(res.path);
	                	$("input[name='hd_pic_url']").val(res.path);
	                	$("#td"+lId).html("修改头像");
	                	console.log($("#td"+lId).html());
            			hch.close_touxiang();
		            }  
			 	},  
		        error:function(res, status, e){  
		            //console.log(res+"：");
		            //console.log(e);
		        }
			});
        }
    }
    $(function () {
        hch.inInt();
    });
    
    //上传头像预览  
	$('.choosePic').on('change', '.UploadImg', function(){  
	    var file = this.files[0];        
	    //判断类型是不是图片    
	    if(!/image\/\w+/.test(file.type)){       
	            $('.choosePic span').html('文件只能为图片类型');     
	            return false;     
	    }    
	  
	    //判断照片是否小于2M  
	    if(file.size > 2*1024*1024){       
	            $('.choosePic span').html('图片大小不能超过2M');     
	            return false;     
	    }   
	  
	    var reader = new FileReader();     
	    reader.readAsDataURL(file);     
	    reader.onload = function(e){     
	        $('.previewPic img').attr('src',this.result) //就是base64  
	    }   
	});  
//	$(document).on('click',':checkbox[name="selall4"]',function(){//   全选/全不选
//      var $this = $(this);
//      //console.log($this.attr('name'));
//      if ($this.val() == 'selallxq4') {
//          if ($this.attr('checked')) {
//              $('#deptList4 :checkbox[name="ckbDept"]').attr('checked', true);
//              } else {
//                  $('#deptList4 :checkbox[name="ckbDept"]').removeAttr('checked');
//              }
//          }
//  })
	$(document).on('click','.selall',function(item){//   全选/全不选
		var $this = $(this);
		var i=$(this).attr('id').replace('selall','');
	    if ($this.attr('checked')) {
	        $('#deptList'+i+' :checkbox[name="ckbDept"]').attr('checked', true);
	    } else {
	        $('#deptList'+i+' :checkbox[name="ckbDept"]').removeAttr('checked');
	    }
   });
</script>
