<?php
include_once 'taskrecvmanager.php';

$sep = '%';
//总体任务id
$gtaskid = empty($_POST['gtaskid']) ? -1 : trim($_POST['gtaskid']);
//工作目标
$target = empty($_POST["target"]) ? trim('') : $sep.trim($_POST['target']).$sep;
//台账类型
$type = empty($_POST['types']) ? -1 : trim($_POST['types']);
//接收状态
$state = 1;
//获取所有的总体任务
$gtask = querygeneraltask();
//分页查询所有已接收的任务
$data = queryDeptsRecvedTask2($gtaskid, $target, $type, $deptid);

?>

<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>任务反馈</title>
	<script type="text/javascript" src="../js/jquery.min.js" ></script>
	<script type="text/javascript" src="../js/layer/layer.js" ></script>
	<script type="text/javascript" src="../js/jquery.form.js"></script>
	<!-- <link rel="stylesheet" href="../css/default.css" /> -->
	<link rel="stylesheet" href="../css/common.css" />
	<style type="text/css">
		td{
			word-break:break-all;
		}
        #tbFeedBacks td{
            padding: 5px 5px;
            line-height: 140%;
            background-color: #FFFFEF;
            text-align: center;
        }
        .layui-layer-rim{
            background-color: #DEEFFF;
        }
        .layui-layer-title{
            color: darkgray;
            font-weight: bold;
        }
	</style>
</head>
<body class="main">
	<div id="search">
		<form id="searchform" action="taskfeedback.php" method="post" style="width: 100%;">
			<table border="0" cellpadding="4" cellspacing="1" class="table01">
				<tr>
					<td colspan="4" class="table_title">
						任务反馈
					</td>
				</tr>
				<tr>
					<td height="28" class="td_title">
						台账类型
					</td>
					<td width="320px" class="td_content">
						<select name="types" class="select" value="" style="width: 120px;">
							<option value="0"></option>
							<?php
							foreach($task_type as $key => $val){
								if($type == $key){
								?><option value="<?=$key?>" selected="selected"><?=$val?></option><?php
								}else{
								?><option value="<?=$key?>"><?=$val?></option><?php
								}
							}
							?>
						</select>
					</td>
					<td class="td_title">
						总体任务
					</td>
					<td width="330px" class="td_content">
						<select name="gtaskid" class="select" value="" style="width: 320px;">
							<option value="0"></option>
							<?php
							foreach($gtask as $row){
								if($gtaskid == $row['id']){
								?><option value="<?=$row['id']?>" selected="selected"><?=$row['name']?></option><?php
								}else{
								?><option value="<?=$row['id']?>"><?=$row['name']?></option><?php
								}
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="td_title">
						工作目标
					</td>
					<td width="330px" colspan="3" class="td_content">
						<input type="text" name="target" value="<?=str_replace('%','', $target)?>" class="htmlText" style="width: 317px;"/>
					</td>
				</tr>
				<tr>
					<td colspan="4" class="td_title">
						<input id="btnSearch" type="submit" value="查询" class="button1">    
						<input type="hidden" id="h_page" value='1' />
						<input type="hidden" id="h_deptid" value='<?=$deptid?>' />
						<input type="hidden" id="h_gtaskid" value='<?=$gtaskid?>' />
						<input type="hidden" id="h_target" value="<?=$target?>"/>
						<input type="hidden" id="h_tasktype" value='<?=$type?>' />
						<input type="hidden" id="h_canscroll" value='1' />
					</td>
				</tr>					
			</table>
		</form>
	</div>
	<div style="height: 10px;"></div>
	<div id="result">
		<!--定义查询返回结果框的范围ID-->
		<table border="0" cellpadding="4" cellspacing="1" class="table01">
			<thead>
				<tr class="table_title">
					<th rowspan="2" width="3%" class="table_title"> 序号</th>
					<th rowspan="2" width="13%" class="table_title">工作目标</th>
					<th rowspan="2" width="17%" class="table_title">支撑项目</th>
					<th colspan="2" width="21%" class="table_title">工作标准</th>
					<th colspan="2" width="12%" class="table_title">时间节点</th>
					<th rowspan="2" width="12%" class="table_title">附件列表</th>
					<th rowspan="2" width="5%" class="table_title">反馈类型</th>
					<th rowspan="2" width="10%" class="table_title">完成情况</th>
					<th rowspan="2" width="6%" class="table_title">操作</th>
				</tr>
				<tr class="table_title">
					<th width="4%" height='100%' class="table_title">投资（元）</th>
					<th width="17%" height='100%' class="table_title">工作标准</th>
					<th width="6%" height='100%' class="table_title">开始<br>时间</th>
					<th width="6%" height='100%' class="table_title">结束<br>时间</th>
				</tr>
			</thead>
			<tbody id="cons">
				<?php
				$total_count = sizeof($data);
				if($total_count == 0){
					?><tr>
					<td colspan="12" style="line-height: 35px;">
						<font size="2">没有符合条件的记录</font>
					</td>
				</tr><?php
				}else{
					$z=0;//序号
					for($i=0; $i<$total_count; $i++){
						$z = $z + 1;
						$rows = $data[$i]['rowspan'];
							
						$stages = $data[$i]['stage'];
						$sdates = $data[$i]['sdate'];
						$edates = $data[$i]['edate'];

						$backtype = $data[$i]['backtype'];
						?><tr>
						<td rowspan="<?=$rows?>"><?=$z?></td>
						<td rowspan="<?=$rows?>"><?=$data[$i]['gtarget']?></td>
						<td rowspan="<?=$rows?>"><?=$data[$i]['title']?></td>
						<td rowspan="<?=$rows?>"><?=$data[$i]['investment']?></td>
						<td style="padding: 5px 5px;"><?=count($stages)>0? $stages[0]:''?></td>
						<td><?=count($sdates)>0? $sdates[0]:''?></td>
						<td><?=count($edates)>0? $edates[0]:''?></td>
						<!--附件列表-->
						<td rowspan="<?=$rows?>">
						<?php
							$attachList = $data[$i]['attachList'];
							if(is_array($attachList) && count($attachList) > 0){
								foreach($attachList as $attach){
									echo '<p style="text-align:left;"><a style="color:blue;" href="' . $attach['attachUrl'] . '">' . $attach['attachName'] . '</a></p>';
								}
							}
						?>
						</td>
						<td rowspan="<?=$rows?>"><?=$backtype?></td>
						<td rowspan="<?=$rows?>"><?=$data[$i]['historyReview']?></td>
						<td rowspan="<?=$rows?>">
							<input type="button" value="反馈进度" onclick="javascript:hch.onfeedbacktask(<?=$data[$i]['taskid']?>);" class="button1">
                            <div style="height: 2px;"></div>
                            <input type="button" value="查看反馈" onclick="queryFeedBack(<?=$data[$i]['taskid']?>);" class="button1">
						</td>
						</tr><?php
						for($k=1; $k<$rows; $k++){
							?><tr>
							<td style='padding: 5px 5px;'><?=$stages[$k]?></td>
							<td><?=$sdates[$k]?></td>
							<td><?=$edates[$k]?></td>
							</tr><?php
						}
					}
				}?>
			</tbody>
		</table>
	</div>
	<div class="show" id="tree" title="任务反馈" style="display:none;">
		<div style="padding: 10px 10px;background-color:#DEEFFF; height: 264px;">
			<form id="feedbackform" action="" method="post" enctype="multipart/form-data">
				<table border="0" cellpadding="0" cellspacing="1" class="table01">
					<tbody>
						<tr style="line-height: 40px; height: 40px;">
							<td class="tab-td-title">工作进度</td>
							<td class="tab-td-content">
								<input type="text" name="progress" id="" style="width:60px; border:1px solid #ccc; height:23px;">
								<small style="color: gray;">工作进度（单位：%）只能是大于0小于或等于100的整数</small>
							</td>
						</tr>
						<tr>
							<td class="tab-td-title">完成情况</td>
							<td class="tab-td-content" style="height: 100px;">
								<textarea name="remark" style="height: 100px;"></textarea>
							</td>
						</tr>
						<tr style="line-height: 40px; height: 40px;">
							<td rowspan="2" class="tab-td-title">完成情况报告</td>
							<td class="tab-td-content">
								<input type="file" id="file1" name="file1[]" multiple="multiple">
								<small style="color:red; font-weight: normal;">上传word、excel文件</small>
							</td>
						</tr>
						<tr style="line-height: 40px; height: 40px;">
							<td class="tab-td-content">
								<input type="file" id="file2" name="file2[]" multiple="multiple">
								<small style="color:red; font-weight: normal;">上传pdf、图片（最小分辨率800*600）</small>
							</td>
						</tr>
						<tr>
							<td colspan="4" id="searchCon"><!--定义好摆放按钮的TD的ID -->
								<input type="button" id="btnFeedback" value="提 交" class="button1" onclick="hch.check();">
								<input type="button" value="关闭窗口" class="button1" onclick="hch.close();">     
								<input type="hidden" name="htaskid" value="" />
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>
    <div class="show-dept" id="divFeedBacks" title="历史反馈" style="display:none;">
        <div style="padding: 10px 10px; min-width: 650px;">
            <table border="0" cellpadding="0" cellspacing="1" class="table01">
                <thead>
                    <th class="table_title" width="40px">序号</th>
                    <th class="table_title" width="150px">部门</th>
                    <th class="table_title" width="40px">工作<br>进度</th>
                    <th class="table_title" style="min-width:240px;">工作反馈</th>
                    <th class="table_title" width="100px">时间</th>
                    <th class="table_title" width="80px">状态</th>
                </thead>
                <tbody id="tbFeedBacks">
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<script type="text/javascript">
	var hch = {
		onfeedbacktask: function (taskid) {
			feedback = layer.open({
				type: 1,
				title: $('#tree').attr("title"),
                skin: 'layui-layer-rim', //加上边框
                area: ['650px', '320px'], //宽高
                content: $("#tree")
           });
		   $("input[name='htaskid']").val(taskid);
		   $("input[name='progress']").val("").focus();
		   $("textarea[name=remark]").val("");
		   $("#file1").val("");
		   $("#file2").val("");
        },
        close: function () {
        	layer.close(feedback);
        	// refresh();
        },
        check:function(){
        	var progress = $("input[name='progress']").val().trim();
	        var remark = $("textarea[name='remark']").val().trim();
	        // var file = $("input[name='file']").val().trim();
	        var file1 = $("#file1").val();
	        var file2 = $("#file2").val();
	        var taskid = $("input[name='htaskid']").val();
	        //var num_pattern = /^[1-9][0-9]?{1, 2}|100$/;
	        var num_pattern = /^([1-9][0-9]?|100)$/;
        	if(!progress){
        		layer.msg("工作进度不能为空！");
        		return false;
        	}
        	if(!num_pattern.test(progress) || progress==0){
        		layer.msg("工作进度只能是大于0小于或等于100的整数！");
        		return false;
        	}

        	if(!remark){
        		layer.msg("完成情况不能为空！");
        		return false;
        	}

        	if (!file1 && !file2) {
        		layer.msg("必须上传完成情况报告！");
        		return false;
        	}
			
			$("#btnFeedback").attr('disabled', true);
			var formData = new FormData($("#feedbackform")[0]);  
			$.ajax({  
				url:'taskrecvmanager.php?do=feedbacktask',
				type: 'POST',  
				data: formData,  
				async: false,  
				cache: false,  
				contentType: false,  
				processData: false,  
				success: function (data) {  
					layer.close(layer.index);
					if(data==1){
						layer.msg("任务反馈成功！");
						//setTimeout('refresh()', 1000);
					}else if(data==2){
						layer.msg("任务反馈失败：获取台账信息失败！");
					}else{
						layer.msg("任务反馈失败！");
					}
					$("#btnFeedback").attr('disabled', false);
				},  
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					$("#btnFeedback").attr('disabled', false);
					var txt = "上传文件失败，状态：" + XMLHttpRequest.readyState;
					txt += "，错误信息：" + textStatus;
					layer.msg(txt);
				}  
			});  
        }
	}
	function refresh(){
		//$("#searchform").submit();
	}

	$(function(){		
		//滚动条距底部的距离  
    	var BOTTOM_OFFSET = 0;
		$(window).scroll(function(){
			if($("#h_canscroll").val() != 1)
				return;
			var currWin = $(window);  
	        //当前窗口的高度  
	        var winHeight = currWin.height();  
	        //当前滚动条从上往下滚动的距离  
	        var scrollTop = currWin.scrollTop();  
	        //当前文档的高度  
	        var docHeight = $(document).height();  
	  
	        //当 滚动条距底部的距离 + 滚动条滚动的距离 >= 文档的高度 - 窗口的高度  
	        //换句话说：（滚动条滚动的距离 + 窗口的高度 = 文档的高度）  这个是基本的公式  
	        if ((BOTTOM_OFFSET + scrollTop) >= docHeight - winHeight) {
	        	$("#h_canscroll").val(2);//防止出现重复加载的情况
	          	var page = $("#h_page").val();
	           	var gtaskid = $("#h_gtaskid").val();
	           	var target = $("#h_target").val();
	           	var tasktype = $("#h_tasktype").val();
	           	var deptid = $("#h_deptid").val();
	           	
	           	if(!page)
	           		page = 0;
	           	var param={
	            	'gtaskid': gtaskid,
	            	'target': target,
	            	'tasktype': tasktype,
	            	'deptid' : deptid,
	            	'page': page
	            }
	            $.post('taskrecvmanager.php?do=ajaxDeptsRecvedTask2', param,function(res){
	            	var curpage = parseInt(page) + 1;
	            	$("#h_page").val(curpage);
	            	if(res==0){//查询数据为空
	            		var divObj = $("<div style='text-align:center; line-height:30px; height:30px;'></div>").appendTo($("#cons").parent().parent());
	            		$("<small></small>").text('数据已经全部加载完毕！').appendTo(divObj);
	            		$("#h_canscroll").val('0')
	            		return ;	
	            	}
	            	addData(res);//向页面中填充数据
	            	$("#h_canscroll").val(1);//数据加载完毕，回复状态
	            }, 'json');
	        }
        });  
	})
	
	function addData(data){
		var page = $("#h_page").val();
		var index = (parseInt(page)-1)*15;
		
		for(var i=0; i<data.length; i++){
			index++;
			var trObj = $("<tr></tr>").appendTo($("#cons"));
			var rows = data[i]['rowspan'];
			var stages = data[i]['stage'];
			var sdates = data[i]['sdate'];
			var edates = data[i]['edate'];
			var attachList = data[i]['attachList'];

			var attachStr = "";
			for(var j=0; j<attachList.length; j++){
				attachStr += '<p style="text-align:left;"><a style="color:blue;" href="' + attachList[j]['attachUrl'] + '">' + attachList[j]['attachName'] + '</a></p>';
			}
				
			$("<td></td>").text(index).attr('rowspan', rows).appendTo(trObj);
			$("<td></td>").text(data[i]['gtarget']).attr('rowspan', rows).appendTo(trObj);
			$("<td></td>").text(data[i]['title']).attr('rowspan', rows).appendTo(trObj);
			$("<td></td>").text(data[i]['investment']).attr('rowspan', rows).appendTo(trObj);
			if(stages.length > 0){
				$("<td style='padding: 5px 5px;'></td>").text(stages[0]).appendTo(trObj);
				$("<td></td>").text(sdates[0]).appendTo(trObj);
				$("<td></td>").text(edates[0]).appendTo(trObj);
			}else{
				$("<td style='padding: 5px 5px;'></td>").appendTo(trObj);
				$("<td></td>").appendTo(trObj);
				$("<td></td>").appendTo(trObj);
			}
			$("<td style='text-align: left;'>" + attachStr + "</td>").attr('rowspan', rows).appendTo(trObj);
			$("<td></td>").text(data[i]['backtype']).attr('rowspan', rows).appendTo(trObj);
			$("<td></td>").html(data[i]['historyReview']).attr('rowspan', rows).appendTo(trObj);
			//button
			var btnTD = $("<td></td>").attr('rowspan', rows).appendTo(trObj);
			$("<input type='button' class='button1' />").val('反馈进度').attr('onclick', 'hch.onfeedbacktask('+data[i]['taskid']+');').appendTo(btnTD);
            $("<div style='height: 2px;'></div>").appendTo(btnTD);
            $("<input type='button' class='button1' />").val('查看反馈').attr('onclick', 'queryFeedBack('+data[i]['taskid']+');').appendTo(btnTD);
			//stages
			if(stages.length > 1){
				for(var k=1; k<stages.length; k++){
					var trObj2 = $("<tr></tr>").appendTo($("#cons"));
					$("<td style='padding: 5px 5px;'></td>").text(stages[k]).appendTo(trObj2);
					$("<td></td>").text(sdates[k]).appendTo(trObj2);
					$("<td></td>").text(edates[k]).appendTo(trObj2);
				}
			}	
		}
	}

	function queryFeedBack(taskid){
        lyFeedBacks = layer.open({
            type: 1,
            title: $('#divFeedBacks').attr("title"),
            skin: 'layui-layer-rim', //加上边框
            area: ['90%', '90%'], //宽高
            content: $("#divFeedBacks")
        });

        var tbFeedBacksOjb = $("#tbFeedBacks");
        tbFeedBacksOjb.empty();

        $.post("taskrecvmanager.php?do=getFeedbackByTaskid", {'taskid': taskid}, function (data){
            if(data.length == 0){
                var trObj = $("<tr></tr>");
                $("<td colspan='7' style='color:gray;font-size:12px;'></td>").text('暂时没有任何反馈记录！').appendTo(trObj);
                trObj.appendTo(tbFeedBacksOjb);
            }
            for(var i=0; i<data.length; i++) {
                var trObj = $("<tr></tr>");
                $("<td></td>").text((i + 1)).appendTo(trObj);
                var isHead = data[i]['ishead'];
                var txtObj = $("<span style='color:blue; font-weight:bold;'><span>").text(data[i]['dname']);
                if (isHead == "1")
                    $("<td></td>").append(txtObj).appendTo(trObj);
                else
                    $("<td></td>").text(data[i]['dname']).appendTo(trObj);
                $("<td></td>").text(data[i]['progress'] + '%').appendTo(trObj);
                var tdObj = $("<td style='text-align:left;'></td>").text(data[i]['remark']).appendTo(trObj);
                if (data[i]['reporturl'] != null) {
                    if ($(tdObj).text())
                        tdObj.append($("<br/>"));
                    for (var j = 0; j < data[i]['reporturl'].length; j++) {
                        if (j > 0)
                            tdObj.append('；');
                        var aobj = $("<a target='_blank'></a>").text(data[i]['fname'][j]).attr('href', data[i]['reporturl'][j]);
                        tdObj.append(aobj);
                    }
                }
                $("<td></td>").text(data[i]['backtime']).appendTo(trObj);
                var html = "";
                if (data[i]['rvwstate'] == 1) {
                    html += "<div style='line-height:28px;'><small>已通过</small></div>";
                } else if (data[i]['rvwstate'] == 2) {
                    html += "<div style='line-height:28px;'><small title='" + data[i]['rvwmark'] + "'>已驳回</small></div>";
                } else{
                    html += "<div style='line-height:28px; color:darkblue;'><small>待审核</small></div>";
                }
                $("<td></td>").html(html).appendTo(trObj);
                trObj.appendTo(tbFeedBacksOjb);
            }
        }, "json");
    }
</script>