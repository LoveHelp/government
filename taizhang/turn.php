<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
include("target.php");
$username = isset($_SESSION['userName']) ? $_SESSION['userName'] : "";
$targetid = isset($_GET['targetid']) ? $_GET['targetid'] : "";
if($targetid != ""){
	$result = get_regbacktype_by_targetid($targetid);
	$onbacktime = $result['onbacktime'];
	$itemregbacktype = $result['regbacktype'];
}else{
	$onbacktime = "";
	$itemregbacktype = 1;
}
?>
<!doctype html>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<!--CSS控制文件-->
<link rel="stylesheet" href="../css/style.css?v=1">
<link rel="stylesheet" href="../css/default.css">
<link rel="stylesheet" href="../css/taizhang.css">
<!--常用的javascript文件-->
<!--<script src="../js/jquery-1.8.2.min.js"></script>-->
<script type="text/javascript" src="../js/jquery.min.js" ></script>
<script type="text/javascript" src="../js/layer/layer.js" ></script>
<script type="text/javascript" src="../js/taizhang.js"></script>
<script type='text/javascript' src="../js/calendar/calendar.js" ></script>
<script type='text/javascript' src='../js/calendar/WdatePicker.js'></script>
<style>
.table_title{
	background-image:url(../img/table_title22.gif);
}
.alternate_line1 td{text-align:center;}
#add_generaltask p{padding:20px 0px;text-align:center;}
#add_target p{padding:20px 0px;text-align:center;}
/*.layui-layer-content{background:#ECF6FB;}*/
.button1{cursor:pointer;}
.large{background-image:url(../img/button1Large.gif);width:110px;}
.alternate_line1{line-height:100%;}
tr{height:30px;}
</style>
<script>
var index = parent.layer.getFrameIndex(window.name);
var hch = {
	inInt: function () {
		if (typeof String.prototype.endsWith != 'function') { 
			String.prototype.endsWith = function(suffix) {  
				return this.indexOf(suffix, this.length - suffix.length) !== -1; 
            };
        }
    },
	close_layer:function(){
		parent.layer.close(index);
	},
	open_msg:function(msg){
		layer.msg(msg, {offset: ['500px']});
    },
	add_dept:function(){
        	var deptIds="";
        	var deptHeadIds="";
        	$(':checkbox[name=ckbDept][checked]').each(function () {
                deptIds += $(this).val() + ",";
            });
            if (deptIds.length == 0) {
                hch.open_msg('您还没有选择责任单位！');
                return false;
            }
        	$(':checkbox[name=ckbDeptHead][checked]').each(function () {
                deptHeadIds += $(this).val() + ",";
            });
            if (deptHeadIds.length == 0) {
                hch.open_msg('您还没有选择牵头单位！');
                return false;
            }
            deptIds = deptIds.substr(0, deptIds.length - 1);
            deptHeadIds = deptHeadIds.substr(0, deptHeadIds.length - 1);

            var targetid = <?php echo $targetid; ?>;
            var onbacktime = $("#onbacktime").val();
            var regbacktype = $("#regbacktype").val();
						
			$.ajax({
				type: 'post',
				url: "target.php?do=update_dept",
				data: {deptIds:deptIds, deptHeadIds:deptHeadIds, targetid:targetid, onbacktime:onbacktime, regbacktype:regbacktype},
				success:function(result){
					if(result){
						parent.layer.msg("操作成功！");
						parent.window.location.reload(); //刷新父窗口
						hch.close_layer();
					}else{
						hch.open_msg('操作失败！');
					}
				}
			});
        },
		singleselect:function(i){
        	if(i==1 && $("#onbacktime").val()){//按期反馈时间
        		$("#regbacktype").val(3);
        	}else if(i==2 && $("#regbacktype").val() != "3"){//定期反馈上报类型
        		$("#onbacktime").attr("value", "");
        	}
        },
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
    //加载牵头单位预选
  	$(document).on('click','#deptList :checkbox',function(){
  		var ss='';
  		$('#deptList :checked').each(function(){
		  	var id=$(this).val();
		  	var name=$(this).next().text();
		  	if($(this).attr('name').substring(0,6)!="selall"){
		  		ss+='<p><input id="ck_hd_'+id+'" type="checkbox" name="ckbDeptHead" value="'+id+'" />	<label for="ck_hd_'+id+'">'+name+'</label></p>';
		  	}
	  	});
  		
  		//console.log(ss);
  		$('#head_list').html('牵头单位：<br/>'+ss);	
  	});
</script>
</head>
<body class="main" style="overflow-x: hidden;overflow-y: auto;background:#DEEFFF;">
<!--
	描述：转办弹出层
-->
<input type="hidden" name="hd_targetid" />
<div class="show-dept" id="tree_dept" title="转办">
	<div style="padding: 10px 10px;height: auto;">
		<div style="height: 40px;line-height: 40px;">
  			按期反馈时间：<input type="text" value="<?php echo $onbacktime; ?>" id="onbacktime" name="onbacktime" onclick="WdatePicker()" onchange="hch.singleselect(1);" readonly="readonly" />&nbsp;&nbsp;
  			定期反馈上报类型：
  			<select name="regbacktype" id="regbacktype" onchange="hch.singleselect(2);">
	        <?php 
				foreach($regbacktype as $key=>$value) {
					if($key == $itemregbacktype){
						echo '<option value="' . $key . '" selected>' . $value . '</option>';
					}else{
						echo '<option value="' . $key . '">' . $value . '</option>';
					}
				}
			?>
        </select>	  
  		</div>
  		<!--<div style="height: 20px;line-height: 20px;">选择责任单位：</div>-->
  		<div style="border:1px solid #e0dede;padding-left:5px;padding-bottom: 5px;" id="deptList">
		<?php
			foreach($areaCode as $key=>$value){
				echo '<div style="line-height:40px;font-weight:bold;overflow:hidden;" id="areaCode' . $key . '">'
					. '<span class="spselname">' . $areaCode[$key] . '</span>'
					. '<span class="spselall">'
					. '<input type="checkbox" class="selall" name="selall' . $key . '" id="selall' . $key . '" value="selallxq' . $key . '"/>'
					. '<label for="selall' . $key . '">全选</label></span>'
					. '<div style="padding-left:10px;font-weight:100;clear:both;" id="deptList' . $key . '">';
				$deptList = get_dept_list($targetid, $key);
				foreach($deptList as $dept){
					echo '<p id="p' . $dept["deptId"] . '">';
					if($dept['status'] == ""){
						echo '<input type="checkbox" name="ckbDept" value="' . $dept["deptId"] . '" id="' . $dept["deptId"] . '"/>';
					}else{
						echo '<input type="checkbox" name="ckbDept" checked value="' . $dept["deptId"] . '" id="' . $dept["deptId"] . '"/>';
					}
					echo '<label for="' . $dept["deptId"] . '">' . $dept["deptName"] . '</label></p>';
				}
				echo '</div></div>';
			}
		?>
		</div>
  		<div id="head_list" class="show-head">
  			<?php
			$headdept = get_head_dept_list($targetid);
			if(is_array($headdept) && count($headdept) > 0){
				echo "牵头单位：<br>";
				foreach($headdept as $h){
					if($h['ishead'] == 1){
						echo '<p><input id="ck_hd_' . $h['deptid'] . '" name="ckbDeptHead" value="' . $h['deptid'] . '" type="checkbox" checked><label for="ck_hd_' . $h['deptid'] . '">' . $h['deptName'] . '</label></p>';
					}else{
						echo '<p><input id="ck_hd_' . $h['deptid'] . '" name="ckbDeptHead" value="' . $h['deptid'] . '" type="checkbox"><label for="ck_hd_' . $h['deptid'] . '">' . $h['deptName'] . '</label></p>';
					}
				}
			}
			?>
  		</div>
  		<div style="clear: both;line-height: 35px;text-align: center;padding-top: 20px;">
  			<input type="submit" value="转 办" style="cursor:pointer" class="button1" onclick="hch.add_dept();">&nbsp;
			<input type="button" value="关 闭" style="cursor:pointer" class="button1" onclick="hch.close_layer();"> 
  		</div>
	</div>
</div>
</body>
</html>
