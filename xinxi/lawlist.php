<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
//error_reporting(0);//关闭提示
include_once "information.php";
include_once "../constant.php";

$deptId = isset($_SESSION['userDeptID']) ? $_SESSION['userDeptID'] : 0;
$startTime = date("Y-m-d");
$addTime = date("Y-m-d");
$infoSort = 0;
$lawType_S = isset($_POST["lawType_S"]) ? trim($_POST["lawType_S"]) : 0;
$infoTitle_S = isset($_POST["infoTitle_S"]) ? trim($_POST["infoTitle_S"]) : "";
$startTime_start = isset($_POST["startTime_start"]) ? $_POST["startTime_start"] : "";
$startTime_end = isset($_POST["startTime_end"]) ? $_POST["startTime_end"] : "";
$addTime_start = isset($_POST["addTime_start"]) ? $_POST["addTime_start"] : "";
$addTime_end = isset($_POST["addTime_end"]) ? $_POST["addTime_end"] : "";

include_once "../mysql.php";
?>

<!doctype html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<title>法律法规管理</title>
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
		<form action="lawlist.php" method="POST">
			<!--定义查询条件路入框的范围ID-->
			<div id="search" class="search">
				<table border="0" cellpadding="0" cellspacing="1" class="tab">
					<tbody>
						<tr>
							<td height="25" colspan="4" class="tab-title" align="center">法律法规管理</td>
						</tr>
						<tr>
							<td class="tab-td-title">类别
							</td>
							<td class="tab-td-content">
								<select name="lawType_S" id="lawType_S">
									<option value="0"></option>
									<?php 
										for ($i=1; $i<=count($lawTypeArray); $i++) {
											if($i==$lawType_S){
												echo '<option value="' . $i . '" selected="selected">' . $lawTypeArray[$i] . '</option>';
											}else{
												echo '<option value="' . $i . '">' . $lawTypeArray[$i] . '</option>';
											}
										}
									?>
								</select>	
							</td>
							<td class="tab-td-title">通报时间</td>
							<td class="tab-td-content">
								<input type="text" name="startTime_start" readonly="readonly" onclick="WdatePicker();" value="<?php echo $startTime_start; ?>">
								至
								<input type="text" name="startTime_end" readonly="readonly" onclick="WdatePicker();" value="<?php echo $startTime_end; ?>">
							</td>
						</tr>
						<tr>
							<td class="tab-td-title">标题
							</td>
							<td class="tab-td-content">
								<input type="text" style="width:98%;" name="infoTitle_S" value="<?php echo $infoTitle_S; ?>">
							</td>
							<td class="tab-td-title">录入时间</td>
							<td class="tab-td-content">
								<input type="text" name="addTime_start" readonly="readonly" onclick="WdatePicker();" value="<?php echo $addTime_start; ?>">
								至
								<input type="text" name="addTime_end" readonly="readonly" onclick="WdatePicker();" value="<?php echo $addTime_end; ?>">
							</td>
						</tr>
						<tr>
							<td class="tab-td-title" colspan="4" style="text-align: center;">
								<input type="submit" value="查 询" style="cursor:pointer" class="button1">
								<input type="button" value="添加" class="button1" style="cursor:pointer" onclick="openNewWindow('../taizhang/handle.php?name=../xinxi/lawadd.php', 1, 0)">
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
					<td class="tab-title">类别 </td>
					<td class="tab-title">标题</td>
					<td class="tab-title">通报时间 </td> 
					<td class="tab-title">发布时间 </td> 
					<td class="tab-title">操作 </td>      
				</tr>
				  
				<?php 
					$res=get_lawList($lawType_S,$infoTitle_S,$startTime_start,$startTime_end,$addTime_start,$addTime_end);
					if(!empty($res)){
						foreach ($res as $info){
				?>
						
				<tr class="hang alternate_line1" style="cursor: pointer;">
			
					<td style="text-align:center;">
						<?php echo $lawTypeArray[$info['lawType']];?>
					</td>
					<td style="text-align:center;"><?php echo $info['infoTitle']?></td>
					<td style="text-align:center;"><?php echo $info['startTime']?></td>
					<td style="text-align:center;"><?php echo $info['addTime']?></td>
					<td style="text-align:center;">
						<a href="javascript:void(0);" onclick="openNewWindow('../taizhang/handle.php?name=../xinxi/lawadd.php?infoId=<?php echo $info['infoId']?>', 0, 1)">修改</a> | 
						<a onclick="hch.delByInfoId(<?php echo $info['infoId']?>);" href="javascript:void(0);">删除</a>
					</td>
				</tr>
		
				<?php          
						}
					}else{
						echo '<tr class="hang alternate_line1" style="cursor: pointer;"><td colspan="5" style="text-align:center;">没有符合条件的纪录</td></tr>';
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
        showStyle: function () {//间隔行显示样式
            $.each("table.tab tr.hang", function (i) {
                if (i % 2 > 0) {
                    $("table.tab tr.hang").eq(i).addClass("alternate_line2");
                }
            });
        },
        delByInfoId:function(infoId){
        	if(confirm('确定删除改记录？')){
        		location.href="notice_insert_update_delete.php?infoId="+infoId+"&flag=del";
        	}
        },
        check:function(){
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
            
	        var infoId=$("input[name='hd_infoId']").val();
	        //console.log(infoId);
	        var deptId=$("input[name='hd_deptId']").val();
	        var lawType = $("select[name='lawType']").val();
	        var param={
	        	'infoId': infoId,
                'infoTitle': infoTitle,
                'infoSort': infoSort,
                'infoContent':infoContent,
                'startTime':startTime,
                'deptId':deptId,
                'lawType':lawType
	        }
	        //console.log(param);
	        if(infoId=='0'){//添加
	        	$.post("info_ajax.php?do=law_insert",param, function (res) {
      				//console.log(res);
      				if(res!="0"){
      					//layer.msg("添加成功！");
      					alert("添加成功");
            			location.reload();
      				}else{
      					layer.msg("添加失败！");
      				}
	                
	            });
	        }else{//修改
	        	$.post("info_ajax.php?do=law_update",param, function (res) {
	        		//console.log(res);
	                if(res=="1"){
      					//layer.msg("修改成功！");
      					alert("修改成功");
            			location.reload();
      				}else{
      					layer.msg("修改失败！");
      				}
	            });
	        }
        }
    }
    $(function () {
        hch.inInt();
    });
</script>

<?php
//绑定法律法规列表
function get_lawList($lawType,$infoTitle,$startTime_start,$startTime_end,$addTime_start,$addTime_end){
	$mLink=new mysql;
	$where=" where infoType = 5";
	if($lawType != 0){
		$where .= " and lawType=" . $lawType;
	}
	if($infoTitle != ""){
		$where.=" and infoTitle like '%".$infoTitle."%'";
	}
	if($startTime_start != ""){
		$where.=" and startTime >= '".$startTime_start."'";
	}
	if($startTime_end != ""){
		$where.=" and startTime <= '".$startTime_end."'";
	}
	if($addTime_start != ""){
		$where.=" and addTime >= '".$addTime_start."'";
	}
	if($addTime_end != ""){
		$where.=" and addTime <= '".$addTime_end."'";
	}
	$where .= " order by infoId desc";
	$res=$mLink->getAll("select infoId,infoTitle,addTime,startTime,lawType from information ".$where);
	$mLink->closelink();
	return $res;
}

?>