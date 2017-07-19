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
$state = empty($_POST['recvstate']) ? 0 : trim($_POST['recvstate']);
//获取所有的总体任务
$gtask = querygeneraltask();
//分页查询所有已下发任务
$data = queryDeptsTask($gtaskid, $target, $type, $deptid, $state);

?>

<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>任务接收</title>
	<script type="text/javascript" src="../js/jquery.min.js" ></script>
	<script type="text/javascript" src="../js/layer/layer.js" ></script>
	<!-- <link rel="stylesheet" href="../css/default.css" /> -->
	<link rel="stylesheet" href="../css/common.css" />
	</style>
</head>
<body class="main">
	<div id="search">
		<form action="taskrecv.php" method="post">
			<table border="0" cellpadding="4" cellspacing="1" class="table01">
				<tr>
					<td colspan="4" class="table_title">
						任务接收
					</td>
				</tr>
				<tr>
					<td class="td_title">
						台账类型
					</td>
					<td width="330px" class="td_content">
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
					<td style="width: 330px;" class="td_content">
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
						接收状态
					</td>
					<td class="td_content">
						<select name="recvstate" class="select" value="" style="width: 120px;">
							<option value="0">待接收</option>
							<option value="1">已接收</option>
							<option value="2">已退回</option>
						</select>
					</td>
					<td class="td_title">
						工作目标
					</td>
					<td style="width: 330px;" class="td_content">
						<input type="text" name="target" value="<?=str_replace('%','', $target)?>" class="htmlText" style="width: 317px;"/>
					</td>
				</tr>
				<tr>
					<td colspan="4" style="padding-left:10px !important;" class="td_title">
						<input type="submit" value="查询" class="button1" />
						<input type="button" value="导出" class="button1"  onclick="hch.open_type();" />
						<input type="hidden" id="h_page" value='1' />
						<input type="hidden" id="h_deptid" value='<?=$deptid?>' />
						<input type="hidden" id="h_gtaskid" value='<?=$gtaskid?>' />
						<input type="hidden" id="h_target" value="<?=$target?>"/>
						<input type="hidden" id="h_tasktype" value='<?=$type?>' />
						<input type="hidden" id="h_state" value="<?=$state?>" />
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
					<th rowspan="2" width="11%" class="table_title">工作目标</th>
					<th rowspan="2" width="11%" class="table_title">支撑项目</th>
					<th colspan="2" width="24%" class="table_title">工作标准</th>
					<th colspan="2" width="12%" class="table_title">时间节点</th>
					<th rowspan="2" width="6%" class="table_title">反馈方式</th>
					<th rowspan="2" width="17%" class="table_title">责任主体</th>
					<th rowspan="2" width="10%" class="table_title">附件列表</th>
					<th rowspan="2" width="6%" class="table_title">操作</th>
				</tr>
				<tr class="table_title">
					<th width="4%" height='100%' class="table_title">投资<br />（元）</th>
					<th width="20%" height='100%' class="table_title">工作标准</th>
					<th width="6%" height='100%' class="table_title">开始时间</th>
					<th width="6%" height='100%' class="table_title">结束时间</th>
				</tr>
			</thead>
			<tbody id="cons">
				<?php
				$total_count = sizeof($data);
				if($total_count == 0){
					?><tr>
					<td colspan="11" style="line-height: 35px;text-align:center;">
						<font size="2">没有符合条件的记录</font>
					</td>
				</tr><?php
				}else{
					$z = 0;//序号
					for($i=0; $i<$total_count; $i++){
						$z = $z + 1;
						$btnRecvTxt = '接收';
						$btnBackTxt = '退回';
						$btnDisabled='';
						$rows = $data[$i]['rowspan'];
						$state=$data[$i]['status'];
						if($data[$i]['status'] == 1){
							$btnRecvTxt = '已接收';
							$btnDisabled = 'disabled';
						}
						if($data[$i]['status'] == 2){
							$btnBackTxt = '已退回';
							$btnDisabled = 'disabled';
						}
							
						$backtype = $data[$i]['backtype'];
//						$backtype = empty($backtype) ? '季报' : $regbacktype[$backtype];
						$stages = $data[$i]['stage'];
						$sdates = $data[$i]['sdate'];
						$edates = $data[$i]['edate'];
						?><tr>
						<td rowspan="<?=$rows?>"><?=$z?></td>
						<td rowspan="<?=$rows?>"><?=$data[$i]['gtarget']?></td>
						<td rowspan="<?=$rows?>"><?=$data[$i]['title']?></td>
						<td rowspan="<?=$rows?>"><?=$data[$i]['investment']?></td>
						<td style="padding: 5px 5px;"><?=count($stages)>0? $stages[0]:''?></td>
						<td><?=count($sdates)>0? $sdates[0]:''?></td>
						<td><?=count($edates)>0? $edates[0]:''?></td>
						<td rowspan="<?=$rows?>"><?=$backtype?></td>
						<td rowspan="<?=$rows?>" style="text-align:left;">
							<?=$data[$i]['header_s']?>
							<div class="link" onclick="show_header(<?=$z?>);">
								查看全部
								<input type="hidden" id="h_header<?=$z?>" value="<?=$data[$i]['header_l']?>" >
							</div>
						</td>
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
						<td rowspan="<?=$rows?>">
							<input type="button" value="<?=$btnRecvTxt?>" onclick="javascript:onrecievetask(<?=$data[$i]['id']?>, <?=$data[$i]['taskid']?>);" class="button1" <?=$btnDisabled?> <?=$state==2 ? 'style="display:none;"' : ''?>>
							<div style="height: 5px;" <?=$state>0 ? 'style="display:none;"' : ''?>></div>
							<input type="button" value="<?=$btnBackTxt?>" title="<?=$data[$i]['remark']?>" onclick="javascript:hch.onbacktask(<?=$data[$i]['id']?>);" class="button1" <?=$btnDisabled?> <?=$state==1 ? 'style="display:none;"' : ''?>>
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
	<div class="show" id="tree" title="退回意见反馈" style="display:none; overflow: hidden;">
		<div style="padding: 10px 10px;background-color:#DEEFFF; height: 244px;">
			<table border="0" cellpadding="0" cellspacing="1" class="table01">
				<tbody>
					<tr>
						<td class="tab-td-title">退回意见</td>
						<td class="tab-td-content" style="height: 180px;">
							<textarea name="remark"></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="4" id="searchCon"><!--定义好摆放按钮的TD的ID -->
							<input type="submit" value="提 交" class="button1" onclick="hch.check();">
							<input type="button" value="关闭窗口" class="button1" onclick="hch.close();">     
							<input type="hidden" name="hd_rtid" value="" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	
	<div class="show-dept" id="select_type" title="选择台账类型" style="display:none;">
		<div style="padding: 10px 10px;height:auto;">
			<br>
			<div style="height: 40px;line-height: 40px; text-align:center;">
				台账类型：
				<select name="output_type" id="output_type" class="select">
				<?php
					foreach($task_type as $key=>$value){
						echo '<option value="' . $key . '">' . $value . '</option>';
					}
				?>
				</select>	  
			</div>
			<br><br>
			<div style="clear: both;line-height: 35px;text-align: center;padding-top: 20px;">
				<input type="submit" value="导 出" style="cursor:pointer" class="button1" onclick="hch.output_task();">&nbsp;
				<input type="button" value="关 闭" style="cursor:pointer" class="button1" onclick="hch.close2();"> 
			</div>
		</div>
	</div>
	<div class="show-dept" id="layertip" style="display:none;">
		<div style="padding: 18px 10px 10px 10px;height:auto; text-align: center;">
			<small style="color: blue;"><b>正在导出，请稍事休息，谢谢。。。</b></small>
		</div>
	</div>
</body>
</html>
<script type="text/javascript">
	function query_reset(){
		$("select[name='types']").val(0);
		$("input[name='target']").val('');
		refresh();
	}
	function onrecievetask(id, taskid){
		var param = {
			'id': id,
			'taskid': taskid
		}
		$.post("taskrecvmanager.php?do=recvTask", param, function (res) {
			if(res==1){
    			layer.msg("任务接收成功！");
    			layer.close(this.index);
    			setTimeout('refresh()', 800);
    		}else{
    			layer.msg("任务接收失败！");
    		}
		}, 'json');
	}
	var hch = {
		onbacktask: function (id) {
			this.index = layer.open({
				type: 1,
				title: $('#tree').attr("title"),
                skin: 'layui-layer-rim', //加上边框
                area: ['650px', '300px'], //宽高
                content: $("#tree")
           });
		   $("input[name='hd_rtid']").val(id);
		   $("textarea[name='remark']").focus();
        },
		open_type:function(){
        this.index_type = layer.open({
			type: 1,
            title: $('#select_type').attr("title"),
            skin: 'layui-layer-rim', //加上边框
            area: ['400px', '300px'], //宽高
			//offset: "120px",
            content: $("#select_type")
         });
		$(".layui-layer-rim").css("top", "120px");
		$(".layui-layer-rim").css("background-color", "#DEEFFF");
		},
		output_task : function(){
		var type = $("#output_type").val();
		hch.close();
		hch.onexporttip();
		$.ajax({
			type:'get',
			url:"exportexcel3.php",	
			data:{type:type},
			success:function(result){
				layer.close(tipLayer);
				window.location.href = result;
			}
		});    
		},
		
		onexporttip:function(){
		tipLayer = layer.open({
			type: 1,
		    title: '',
		    closeBtn: 0,
		    skin: 'layui-layer-rim', //加上边框
		    area: ['400px', '50px'], //宽高
		    content: $("#layertip")
		});
		$(".layui-layer-rim").css("top", "150px");
		$(".layui-layer-rim").css("background-color", "#DEEFFF");
		},
		
        close: function () {
        	layer.close(this.index);
        },
		
		close2: function () {
        	layer.close(this.index_type);
        },

        check:function(){
	        var remark = $("textarea[name='remark']").val().trim();
        	
        	if (!remark) {
        		layer.msg("任务退回原因不能为空！");
        		return false;
        	}
        	
        	var id=$("input[name='hd_rtid']").val();
	        var param={
	        	'id': id,
	        	'remark':remark,
	        };
	        $.post("taskrecvmanager.php?do=backTask",param, function (res){
	        	if(res==1){
        			layer.msg("任务退回成功！");
        			setTimeout('refresh()', 800);	
        		}else{
        			layer.msg("任务退回失败！");
        		}
	        }, 'json'); 
        }
	}
	function refresh(){
		document.forms[0].submit();
	}
	$(function(){
		//初始化接收状态
		$("select[name='recvstate']").val(<?=$state?>);
		
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
	           	var state = $("#h_state").val();
	           	
	           	if(!page)
	           		page = 0;
	           	var param={
	            	'gtaskid': gtaskid,
	            	'target': target,
	            	'tasktype': tasktype,
	            	'deptid' : deptid,
	            	'state': state,
	            	'page': page
	            }
	            $.post('taskrecvmanager.php?do=ajaxDeptsTask', param,function(res){
	            	var curpage = parseInt(page) + 1;
	            	$("#h_page").val(curpage);
	            	if(res==0){//查询数据为空
	            		var divObj = $("<div style='text-align:center; line-height:30px; height:30px;'></div>").appendTo($("#cons").parent().parent());
	            		$("<small></small>").text('数据已经全部加载完毕！').appendTo(divObj);
	            		$("#h_canscroll").val(0)
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
			$("<td></td>").append(data[i]['backtype']).attr('rowspan', rows).appendTo(trObj);
			var hidObj = $("<input type='hidden'>").attr('id', 'h_header'+index).val(data[i]['header_l']);
			var divlinkObj = $("<div class='link'></div>").attr('onclick', 'show_header('+index+');').text('查看全部');
			$(hidObj).appendTo(divlinkObj);
			$("<td style='text-align:left;'></td>").append(data[i]['header_s']).append(divlinkObj).attr('rowspan', rows).appendTo(trObj);
			$("<td style='text-align:left;'>" + attachStr + "</td>").attr('rowspan', rows).appendTo(trObj);
			//button
			var states = data[i]['status'];
			var btnTD = $("<td></td>").attr('rowspan', rows).appendTo(trObj);
			if(states == 0){
				$("<input type='button' class='button1' />").val('接收').attr('onclick', 'onrecievetask('+data[i]['id']+', '+data[i]['taskid']+');').appendTo(btnTD);
				$("<div style='height:5px;'></div>").appendTo(btnTD);
				$("<input type='button' class='button1' />").val('退回').attr('onclick', 'hch.onbacktask('+data[i]['id']+');').appendTo(btnTD);
			}else if(states == 1){
				$("<input type='button' class='button1' />").val('已接收').appendTo(btnTD);
			}else if(states == 2){
				$("<input type='button' class='button1' />").val('已退回').appendTo(btnTD);
			}
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
	function show_header(index){
		var header_a = $("#h_header"+index).val();
		//layer.msg(header_a);
		layer.open({
			title: '责任主体',
			skin: 'layui-layer-rim', //加上边框
			area: ['500px', 'auto'], //宽高
			content: header_a
		});
	}
</script>