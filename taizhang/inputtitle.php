<?php 
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
$firstdate=date('Y-m-d', strtotime(date('Y-m-01', strtotime(date('Y-m-d'))) . ' +1 month'));
$lastdate=date('Y-m-d', strtotime(date('Y-m-01', strtotime(date('Y-m-d'))) . ' +4 month -1 day'));

$where = '';
$itemtype = "";
$itemgeneraltask = "";
$itemtarget = "";
if(isset($_POST["itemtype"]) && $_POST["itemtype"] != ""){
	$itemtype = trim($_POST["itemtype"]);
	$where=' AND a.type =' . $itemtype;
}

if(isset($_POST["itemgeneraltask"]) && $_POST["itemgeneraltask"] != ""){
	$itemgeneraltask = trim($_POST["itemgeneraltask"]);
	$where=' AND a.generaltaskid =' . $itemgeneraltask;
}

if(isset($_POST["itemtarget"]) && $_POST["itemtarget"] != ""){
	$itemtarget = trim($_POST["itemtarget"]);
	$where=' AND a.target LIKE "%' . $itemtarget . '%"';
}

$deptid = $_SESSION['userDeptID'];	
include("target.php");;
$generaltaskList = json_decode(get_all_generaltask(), true);
?>
<!Doctype html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<!--CSS控制文件-->
<link rel="stylesheet" href="../css/style.css?v=1">
<link rel="stylesheet" href="../css/common.css" />
<link rel="stylesheet" href="../css/taizhang.css">
<script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<script src="../js/jquery.min.js"></script>
<script src="../js/layer/layer.js"></script>
<style>
tr{height:32px;line-height:32px;line-height:120%;}
#content{margin-top:10px;}
input{border:none;}
</style>
<title>台账填报</title>
</head>
<body class="main">
	<div id="search">
		<form action="inputtitle.php" method="post" name="queryform">
		<table width="100%"  cellpadding="4" cellspacing="1" class="table01">
			<tr>
				<td colspan="4" class="table_title" >台账填报</td>
			</tr>
			<tr>
				<td class="td_title">台账类型</td>
					<td class="td_content" style="width:330px;"> 
						<select name="itemtype" id="itemtype" class="select">
						<option value=""></option>
						<?php
							foreach($task_type as $k=>$t){
								if($k == $itemtype){
									echo '<option value="' . $k . '" selected="selected">' . $t . '</option>';
								}else{
									echo '<option value="' . $k . '">' . $t . '</option>';
								}
							}
						?>
						</select>
					</td>
					<td class="td_title">总体任务</td>
					<td class="td_content" style="width:330px;"> 
						<select name="itemgeneraltask" id="itemgeneraltask" class="select" style="width:auto;">
						<option value=""></option>
						<?php
							if(is_array($generaltaskList) && count($generaltaskList) > 0){
								foreach($generaltaskList as $v){
									if($v['id'] == $itemgeneraltask){
										echo '<option value="' . $v['id'] . '" selected="selected">' . $v['name'] . '</option>';
									}else{
										echo '<option value="' . $v['id'] . '">' . $v['name'] . '</option>';
									}
								}
							}
						?></select>
					</td>
			</tr>
			<tr>
				<td class="td_title">工作目标</td>
				<td class="td_content" colspan="3" style="width:330px;">
					<input id="itemtarget" name="itemtarget" type="text" class="input" style="width:317px" value="<?php echo $itemtarget; ?>"/>
				</td>
			</tr>
			<tr>
				<td style="text-align:center;" colspan="4">
					<input type="submit" class="button1" value="查询" /> 
				</td>
			</tr>
		</table>
		</form>
	</div>
	<div id="result">
		<table id="content" align="center" cellpadding="5" cellspacing="1" class="table01" width="100%">
			<thead>
				<tr class="table_title">
					<th width="4%" class="table_title">序号</th>
					<th width="12%" class="table_title">工作目标</th>
					<th width="5%" class="table_title">启动时间</th>
					<th width="5%" class="table_title">完成时间</th>              
					<th width="18%" class="table_title">责任单位</th>
					<th width="7%" class="table_title">添加支撑项目</th> 
					<th width="39%" class="table_title">支撑项目</th>  
					<th width="5%" class="table_title">年度投资</th> 
					<th width="5%" class="table_title">填报工作标准</th>          
			   </tr>
			</thead>
            <tbody>
				<?php querytargetlist($deptid, $where);?>
			</tbody>
		</table>
	</div>          
	<div class="show" id="tree"  title="添加支撑项目" style="display:none;">
		<div style="background-color:#DEEFFF;height:auto;">
			<table style="width: 100%" border="0" cellpadding="0" cellspacing="1" class="table01">
				<tbody>
					<tr>
						<td class="td_title">台账类型</td>
						<td colspan="3" class="td_content">
							<input style="width:98%;" disabled="true"  type="text" name="mytype" id="mytype" value="">
						</td>
					</tr>
					<tr>     
						<td class="td_title">工作目标</td>
						<td colspan="3" class="td_content">
							<input type="hidden" name="targetid" id="targetid" value="">
							<input style="width:98%;" type="text" name="target" id="target" value="" disabled>
						</td>
					</tr>
					<tr>
						<td align="center" class="td_title">支撑项目</td>
						<td colspan="3" class="td_content">
							<textarea style="width:98%;height: 160px;" name="title" id="title"></textarea>    
						</td>
					</tr>
					<tr>
						<td class="td_title">年度投资</td>
						<td colspan="3" class="td_content">
							<input type="text" style="width:98%;" name="investment" id="investment" value="">
						</td>
					</tr>
					<tr>  
						<td colspan="4" class='td_title'><!--定义好摆放按钮的TD的ID -->
							<input type="submit" value="保存修改" style="cursor:pointer" class="button1" onclick="hch.save();">
							<input type="button" value="关闭窗口" style="cursor:pointer" class="button1" onclick="hch.close();">     
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>        
	<div class="show" id="prtree" title="填报工作标准" style="display:none;">
		<div style="background-color:#FFF;">
			<table name="progresstb" border="0" cellpadding="0" cellspacing="1" class="table01">
				<tr>
					<th width="90px" class="table_title">责任单位</th>
					<th class="table_title">工作标准</th>
					<th width="90px" class="table_title">启动时间</th>
					<th width="90px" class="table_title">完成时限</th>
				</tr>
			</table>
			<div style="width: 100%;height:auto;max-height: 300px; overflow-y: auto;margin-bottom:20px;">  	  	
				<table name="progresstb" style="width: 100%;" border="0" cellpadding="0" cellspacing="1" class="table01">
					<tbody id="progresstb">
					</tbody>
				</table>
			</div>			
			<table border="0" cellpadding="0" cellspacing="1" class="table01">
				<tbody>
					<tr>
						<td align="center" class="td_title" colspan="4">填报工作标准</td>
					</tr>
					<tr>
						<td align="center" class="td_title">工作标准</td>
						<td colspan="3" class="td_content">
							<textarea style="width:98%;" cols="6" rows="5" name="stage" id="stage"></textarea>    
						</td>
					</tr>
					<tr>
						<td class="td_title">启动时间</td>
						<td colspan="1" class="td_content">
							<input type="text" name="startdate" id="startdate" onclick="WdatePicker()" value="<?=$firstdate?>" readonly="readonly" >
						</td>
						<td class="td_title">完成时间</td>
						<td colspan="1" class="td_content">
							<input type="text" name="enddate" id="enddate" onclick="WdatePicker()" value="<?=$lastdate?>" readonly="readonly" >
						</td>
					</tr>
					<tr>  
						<td colspan="4" class="td_title"><!--定义好摆放按钮的TD的ID -->
							<input type="submit" value="添加进度" style="cursor:pointer" class="button1" onclick="pro.save();">
							<input type="button" value="关闭窗口" style="cursor:pointer" class="button1" onclick="pro.close();">     
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>      
</body>
</html>
<script type="text/javascript"> 
var type,targetid,target,title,investment,taskid;

function do_edit(obj){
	$("#"+obj).removeAttr("onclick");
	var height = $("#"+obj).height();
	var content = $("#"+obj).html().replace(/<br>/g,'\r\n');
	var html = '<textarea class="text_area_edit" style="height:' + height + 'px" type="text" onblur="do_leave(\'' + obj + '\')" id="textarea_' + obj + '"></textarea>';
	$("#"+obj).html(html);
	$("#textarea_"+obj).focus().val(content);
}

function do_leave(obj){
	var value = $("#textarea_"+obj).val().replace(/[\r\n]/g,"<br>");
	var name = $("#"+obj).attr('name');
	var id = obj.substring(name.length);

	$.ajax({
		type:'post',
		url:"updatePTask.php",	
		data:{id:id, value:value, mode:name},
		success:function(result){
			$("#"+obj).attr("onclick","do_edit('" + obj + "')");
			$("#"+obj).html(value);		
		}
	});
}
 
 function addtitle(mybtn){
 	//点击行内内容时,取出行内每格的内容  	
 	type=$(mybtn).parent("td").parent("tr").find("td").eq(0).text();
 	targetid=mybtn.id;
 	target=$(mybtn).parent("td").parent("tr").find("td").eq(2).text();   	
  	hch.open();  	  	
  }
  
  function getprogress(taskid)
  {
			mydata="do=3&taskid="+taskid;			
       		$.ajax({
   			type: "POST",
   			url: "savetask.php",
   			data: mydata,
   			success: function(msg){ 		
				if(msg != "0"){    			   						
					$("#progresstb").html("");
					$("#progresstb").append(msg);
				}else{   				
					$("#progresstb").html('<tr class="alternate_line1"><td colspan="4" align="center">暂无数据</td></tr>');
				}
   			}
			});	
  }
  
   function inputprogress(pbtn){
 	//点击行内内容时,取出行内每格的内容
 	//alert(myrow);
 	taskid=pbtn.id;
 	if(taskid.substring(0,1)!="t")
 	  	{	 
 	  		getprogress(taskid); 	     	
		}else{
		//当没有支撑项目时,写入与工作目标相同的支撑项目		
			targetid=taskid.substring(1);
			title=$(pbtn).parent("td").parent("tr").find("td").eq(2).text();
			investment="";
			mydata="do=1&targetid="+targetid+"&title="+title+"&investment="+investment;
       		$.ajax({
   			type: "POST",
   			url: "savetask.php",
   			data: mydata,
   			success: function(msg){ 		
   			if(msg>0){ 
   				taskid=msg;	   					   						
   				$("#progresstb").html('<tr class="alternate_line1"><td colspan="4" align="center">暂无数据</td></tr>');   			 
   				}
   			}
			});		
	}	
  	pro.open();  	  	
  }
  
	//弹出层
    var hch = {
        inInt: function () {
            //this.showStyle();
        },
        open: function () {
        	//opentype为1是修改,为0是添加
        		$("#mytype").val(type);
        		$("#targetid").val(targetid);
        		$("#target").val(target);        		
            	this.index = layer.open({
                type: 1,
                title: $('#tree').attr("title"),
                skin: 'layui-layer-rim', //加上边框
                area: ['450px', 'auto'], //宽高
                content: $("#tree")
            });

        },
        save: function () {
        	targetid=$("#targetid").val();
 			title=$("#title").val().trim();
 			investment=$("#investment").val().trim();
 			if (title !== null && title !== undefined && title !== '')
 			{        	     	
			var mydata="do=1&targetid="+targetid+"&title="+title+"&investment="+investment;
       		$.ajax({
   			type: "POST",
   			url: "savetask.php",
   			data: mydata,
   			success: function(msg){ 		
   			if(0<msg) {layer.msg("保存成功！");setTimeout("hch.close();query();",500);}else{layer.msg("保存失败,请检查！");}
   				}
			});
			}else{
				alert("支撑项目必须填写.");
			}
        },
        close: function () {
            layer.close(this.index);
        }
    }
    $(function () {
        hch.inInt();
    });
    
    //弹出工作标准填报层
    var pro = {
        inInt: function () {
            //this.showStyle();
        },
        open: function () {
        	//opentype为1是修改,为0是添加       		
			this.index_prtree = layer.open({
                type: 1,
                title: $('#prtree').attr("title"),
                skin: 'layui-layer-rim', //加上边框
                area: ['600px', 'auto'], //宽高
				offset:"10%",
                content: $("#prtree"),
            });
			$(".layui-layer-content").css("height", "auto");
        },
		auto_height : function(i){
			layer.iframeAuto(i);
		},
        save: function () {
        	stage=$("#stage").val().trim();
 			startdate=$("#startdate").val();
 			enddate=$("#enddate").val(); 			
 			if (taskid>0 && stage!="")
 			{        	     	
			var mydata="do=2&taskid="+taskid+"&stage="+stage+"&startdate="+startdate+"&enddate="+enddate;
       		$.ajax({
   			type: "POST",
   			url: "savetask.php",
   			data: mydata,
   			success: function(msg){  			 		
   			if(msg>0) {layer.msg("保存成功！");getprogress(taskid);}else{layer.msg("保存失败,请检查！");}
   				}
			});
			}else{
				alert("工作标准必须填写.");
			}
        },
        close: function () {
            layer.close(this.index_prtree);
        }
    }
    $(function () {
        pro.inInt();
    });
</script>