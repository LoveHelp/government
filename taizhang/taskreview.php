<?php
include_once 'taskrecvmanager.php';

$sep = '%';
$generaltask = empty($_REQUEST['itemgeneraltask']) ? "": $_REQUEST['itemgeneraltask'];
$taskstate = empty($_REQUEST['taskstate_a']) ? 0 : $_REQUEST['taskstate_a'];
$type = empty($_REQUEST['tasktypes']) ? 0 : $_REQUEST['tasktypes'];
$target = empty($_REQUEST["target"]) ? $sep.$sep : $sep.$_REQUEST['target'].$sep;
//首页跳转参数接收（保存值用于查询功能）
$route = empty($_REQUEST['route']) ? 2 : $_REQUEST['route']; //首页跳转过来审核按钮不显示
//获取所有的任务
$data = gettaskofpage($generaltask, $taskstate, $type, $target, 0, 15);
$total_count = 0;
if(!empty($data))
	$total_count = sizeof($data);
$pregeneraltask = 0;//保存列表中最后一个总体任务的id
if($total_count > 0)
	$pregeneraltask = $data[$total_count-1]['gtaskid'];

$username = isset($_SESSION['userName']) ? $_SESSION['userName'] : "";
$time = date("Y-m-d H:i:s");
$date = date("Y-m-d");
$generaltaskList = get_all_generaltask();
//是督查室主任，则可以修改审核记录
$bShow = 0;
if($userRoleId == $superRoleId)
	$bShow = 1;
?>

<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>审核进度</title>
	<script type="text/javascript" src="../js/jquery.min.js" ></script>
	<script type="text/javascript" src="../js/layer/layer.js" ></script>
	<script type="text/javascript" src="../js/ajaxfileupload.js" ></script>
	<link rel="stylesheet" href="../js/layer/skin/layer.css" />
	<link rel="stylesheet" href="../css/common.css" />
	<link rel="stylesheet" href="../css/progress.css" />
	<script type="text/javascript" src="../js/jquery-ui/jquery-ui.min.js"></script>
	<link href="../js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css">
	<style type="text/css">
		#feedbacklist td,
		#fbtable td,
		#reviewList td{
			padding: 5px 5px;
			line-height: 140%;
			background-color: #FFFFEF;
			text-align: center;
		}
		.progress{
			background:#f1f1f1 !important;
			width:100%;
			height:18px;
		}
		.progress > span{
			height:15px !important;
			min-width:3px;
		}
		div.link{
			line-height:40px;
			padding-right:5px;
		}
	</style>
</head>
<body class="main">
	<div id="search">
		<form action="taskreview.php" method="post" style="width: 100%;">
			<table border="0" cellpadding="4" cellspacing="1" class="table01">
				<tr>
					<td colspan="4" class="table_title">审核进度</td>
				</tr>
				<tr>
					<td height="28" class="td_title">台账类型</td>
					<td width="330px" class="td_content">
						<select name="tasktypes" id="tasktypes" class="select" value="" style="width:120px;">
							<option value="0"></option>
							<?php
							foreach($task_type as $key=>$v){
								if($key == $type){
									echo '<option value="' . $key . '" selected="selected">' . $v . '</option>';
								}else{
									echo '<option value="' . $key . '">' . $v . '</option>';
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
									if($v['id'] == $generaltask){
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
						<td class="td_title">总体评价</td>
						<td width="90px" class="td_content">
							<select name="taskstate_a" class="select" value="" style="width: 80px;">
								<option value="0"></option>
								<option value="1">未完成</option>
								<!--<option value="2">基本完成</option>-->
								<option value="2">完成</option>
							</select>
						</td>
						<td class="td_title">工作目标</td>
						<td class="td_content">
							<input type="text" name="target" value="<?=str_replace('%','', $target)?>" class="htmlText" style="width: 317px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="6" style="text-align: center; height:35px;">
							<input type="submit" value="查询" class="button1">
							<input type="button" class="button1" name="drww" value="导入" onclick="hch.open_input();" style="cursor:pointer" />
							<input type="button" value="导出" class="button1" onclick="javascript:hch.onexport();"> 
							<input type="button" value="短信提醒" class="button1" onclick="onsmstoall();">
							<input type="hidden" id="route" name="route" value="<?=$route?>">
							<input type="hidden" id="hdShowMod"  value="<?=$bShow?>" />
							<input type="hidden" id="hdsRole" value="<?=$superRoleId?>" />
							<input type="hidden" id="hduRole" value="<?=$userRoleId?>" />
							<input type="hidden" id="hdDate" value="<?=$date?>" />
							<input type="hidden" id="h_pregeneraltask" value='<?=$pregeneraltask?>' />
							<input type="hidden" id="h_page" value='1' />
							<input type="hidden" id="h_state" value='<?=$taskstate?>' />
							<input type="hidden" id="h_target" value="<?=$target?>"/>
							<input type="hidden" id="h_tasktype" value='<?=$type?>' />
							<input type="hidden" id="h_generaltask" value='<?=$generaltask?>' />
							<input type="hidden" id="h_canscroll" value='1' />
						</td>
					</tr>			
				</table>
			</form>	
		</div>
		<div style="line-height: 35px; text-align:right;">
			<form action="../xinxi/noticeadd.php" method="post" name="noticeadd_form">
				<input type="hidden" name="infoContent" value="" id="infoContent" />
				<input type="button" value="督查通知" class="button1" style="cursor:pointer;" onclick="javascript:hch.open_notice();">
			</form>
		</div>
		<div id="result">
			<!--定义查询返回结果框的范围ID-->
			<table border="0" cellpadding="4" cellspacing="1" class="table01" id="content_table">
				<thead>
					<tr class="table_title">
						<th rowspan="2" width="3%" class="table_title"> 序号</th>
						<th rowspan="2" width="11%" class="table_title">工作目标</th>
						<th rowspan="2" width="16%" class="table_title">支撑项目</th>
						<th colspan="2" width="23%" class="table_title">工作标准</th>
						<th colspan="2" width="10%" class="table_title">时间节点</th>
						<th colspan="2" width="18%" class="table_title">审核进度</th>
						<th rowspan="2" width="11%" class="table_title">责任主体</th>
						<th rowspan="2" width="8%" class="table_title">最新反馈</th>
					</tr>
					<tr class="table_title">
						<th width="5%" class="table_title">投资（元）</th>
						<th width="18%" class="table_title">工作标准</th>
						<th width="5%" class="table_title">开始<br>时间</th>
						<th width="5%" class="table_title">结束<br>时间</th>					
						<th width="5%" class="table_title">是否<br>完成</th>
						<th width="13%" class="table_title">完成情况</th>
					</tr>
				</thead>
				<tbody id="cons">
					<?php
					if($total_count == 0){
						?><tr>
						<td colspan="11" class="tip">
							<font size="2">没有符合条件的记录</font>
						</td>
					</tr><?php
				}else{
					$z = 0;//序号
					for($i=0; $i<$total_count; $i++){
						?><tr height="35px">
						<td colspan="11" class="table_title"><?=$data[$i]['gtask']?></td>
					</tr><?php
					for($j=0; $j<sizeof($data[$i]['proj']); $j++){
						$z = $z+1;
						$rows = $data[$i]['proj'][$j]['rowspan'];
						
						$process_id = $data[$i]['proj'][$j]['pro_id'];
						$stages = $data[$i]['proj'][$j]['stage'];
						$sdates = $data[$i]['proj'][$j]['sdate'];
						$edates = $data[$i]['proj'][$j]['edate'];
						
						$state = $data[$i]['proj'][$j]['status'];
						$state_text = "完成";
						if($state == 1)
							$state_text = "未完成";
							// else if($state == 3)
							// 	$state_text = "完成";
						
						?><tr>
						<td rowspan="<?=$rows?>"><div class="resizable"><?=$z?></div></td>
						<td rowspan="<?=$rows?>"><div class="resizable"><?=$data[$i]['proj'][$j]['gtarget']?></div></td>
						<td rowspan="<?=$rows?>" id="title<?=$data[$i]['proj'][$j]['id']?>" name="title" onclick="do_edit('title<?=$data[$i]['proj'][$j]['id']?>', '<?=$data[$i]['proj'][$j]['title']?>', 1);">
							<div class="resizable"><?=$data[$i]['proj'][$j]['title']?></div>
						</td>
						<td rowspan="<?=$rows?>" id="investment<?=$data[$i]['proj'][$j]['id']?>" name="investment" onclick="do_edit('investment<?=$data[$i]['proj'][$j]['id']?>', '<?=$data[$i]['proj'][$j]['investment']?>', 1);"><div class="resizable"><?=$data[$i]['proj'][$j]['investment']?></div></td>
						<td style="padding: 5px 5px;" id="stage<?=$process_id[0]?>" name="stage" onclick="do_edit('stage<?=$process_id[0]?>', '<?=$stages[0]?>', 2);">
							<div class="resizable"><?=count($stages)>0? $stages[0]:''?></div>
						</td>
						<td><div class="resizable"><?=count($sdates)>0? $sdates[0]:''?></div></td>
						<td><div class="resizable"><?=count($edates)>0? $edates[0]:''?></div></td>
						<td rowspan="<?=$rows?>"><div class="resizable"><?=$state_text ?></div></td>
						<td rowspan="<?=$rows?>" style='text-align:left;'>
							<div class="resizable">
								<?php if(!empty($data[$i]['proj'][$j]['complete_perc'])) { ?>
								<div class="progress">
									<?php }else { ?>
									<div class="progress" style="display:none;">
										<?php } ?>
										<span class="green" style="width:<?=$data[$i]['proj'][$j]['complete_perc'] ?>;"></span>
									</div>
									<div id="hisRvwDiv<?=$data[$i]['proj'][$j]['taskid']?>">
										<?=$data[$i]['proj'][$j]['hisReview'] ?>
									</div>
									<?php
									if($bShow && !empty($data[$i]['proj'][$j]['hisReview'])){
										?>
										<div class="link">
											<span onclick="rvw.showHisRvw(<?=$data[$i]['proj'][$j]['taskid']?>);">修改</span>
										</div>
										<?php
									}
									?>
								</div>
							</td>
							<!--<td rowspan="<?=$rows?>"><?=$data[$i]['proj'][$j]['header_s']?></td>-->
							<td rowspan="<?=$rows?>" style="text-align:left;">
								<div class="resizable">
									<div><?=$data[$i]['proj'][$j]['header_s']?></div>
									<div class="link">
										<span onclick="show_header(<?=$z?>);">查看全部</span>
										<input type="hidden" id="h_header<?=$z?>" value="<?=$data[$i]['proj'][$j]['header_l']?>" >
									</div>
								</div>
							</td>
							<td rowspan="<?=$rows?>" style='text-align:left;'>
								<div class="resizable">
									已反馈：<span class="link" onclick="show_feedback_yes(<?=$z?>)"><?=$data[$i]['proj'][$j]['feedback']['y_cnt']?></span><br>
									未反馈：<span class="link" onclick="show_feedback_no(<?=$z?>)"><?=$data[$i]['proj'][$j]['feedback']['n_cnt']?></span><br>
									待审核：<span class="link" ><?=$data[$i]['proj'][$j]['fb_no_rvw_cnt']?></span>
									<input type="hidden" id="h_feedback_yes<?=$z?>" value="<?=$data[$i]['proj'][$j]['feedback']['f_yes']?>" >
									<input type="hidden" id="h_feedback_no<?=$z?>" value="<?=$data[$i]['proj'][$j]['feedback']['f_no']?>" >
								</div>
								<div class="pic">
									<a href="picshow.php?taskid=<?=$data[$i]['proj'][$j]['taskid']?>" target="_blank">
										<img src="../img/f1.png" style="width:30px; height: 30px;">
									</a>
								</div>
								
								<?php 
								if($route == 2){
									?>
									<div style="height:5px; clear:both;"></div>
									<div style="text-align:center;">
										<input type="button" value="审核" onclick="javascript:hch.onreviewtask(<?=$data[$i]['proj'][$j]['taskid']?>, this);" class="button1">
										<input type="hidden" class="hdRoleId" value="<?=$data[$i]['proj'][$j]['roleid']?>" />
										<input type="hidden" class="hdBackTime" value="<?=$data[$i]['proj'][$j]['backendtime']?>" />
										<div style="height:2px"></div>
										<!--<input type="button" value="短信提醒" onclick="javascript:onsmsnotice(<?=$data[$i]['proj'][$j]['taskid']?>);" class="button1">-->
										<input type="button" value="短信提醒" onclick="hch.open_sms(<?=$data[$i]['proj'][$j]['taskid']?>);" class="button1">
									</div>
									<?php
								}?>
							</td>
						</tr>
						<?php
						for($k=1; $k<$rows; $k++){
							?><tr>
							<td style='padding: 5px 5px;' id="stage<?=$process_id[$k]?>" name="stage" onclick="do_edit('stage<?=$process_id[$k]?>', '<?=$stages[$k]?>', 2);">
								<div class="resizable"><?=$stages[$k]?></div>
							</td>
							<td><div class="resizable"><?=$sdates[$k]?></div></td>
							<td><div class="resizable"><?=$edates[$k]?></div></td>
						</tr>
						<?php
					}
				}
			}
		}?>
	</tbody>
</table>
</div>
<div class="show" id="fbdiv" title="最新反馈列表" style="display:none;">
	<div style="padding: 10px 10px;">
		<table border="0" cellpadding="0" cellspacing="1" class="table01">
			<thead>
				<th class="table_title" width="50px">序号</th>
				<th class="table_title" width="150px">部门</th>
				<th class="table_title" width="50px">工作<br>进度</th>
				<th class="table_title">工作反馈</th>
				<th class="table_title" width="150px">时间</th>
			</thead>
			<tbody id="fbtable">
			</tbody>
		</table>
	</div>
</div>
<div class="show" id="rvwDiv" title="历史审核列表" style="display:none;">
	<div style="padding: 10px 10px; min-width:680px;">
		<input type="hidden" id="hdTaskId" value="0" >
		<table border="0" cellpadding="0" cellspacing="1" class="table01">
			<thead>
				<th class="table_title" width="50px">序号</th>
				<th class="table_title" width="150px">审核员</th>
				<th class="table_title" width="80px">状态</th>
				<th class="table_title" width="50px">审核<br>进度</th>
				<th class="table_title">审核结论</th>
				<th class="table_title" width="100px">时间</th>
			</thead>
			<tbody id="reviewList">
			</tbody>
		</table>
	</div>
</div>
<div class="show" id="tree" title="审核进度" style="display:none; height: 100%;">
	<div style="padding: 10px 10px;background-color:#DEEFFF; min-width: 730px;">
		<div style="height: 164px;">
			<table border="0" cellpadding="0" cellspacing="1" class="table01">
				<tbody>
					<tr style="line-height: 40px; height: 40px;">
						<td class="tab-td-title">评价结果</td>
						<td class="tab-td-content">
							<select name="taskstate_b" class="select" value="" style="width: 100px;">
								<option value="1" selected="selected">未完成</option>
								<!--<option value="2">基本完成</option>-->
								<option value="2">完成</option>
							</select>
						</td>
						<td class="tab-td-title">总体进度</td>
						<td class="tab-td-content">
							<input type="text" name="progress_b" maxlength="3" onkeyup="this.value=this.value.replace(/\D/g,'')" style="width:60px;">
							<small style="color: gray;">总体进度（单位：%）只能是大于0小于或等于100的整数</small>
						</td>
					</tr>
					<tr>
						<td class="tab-td-title">审核结论</td>
						<td colspan="3" class="tab-td-content">
							<textarea name="remark_b" style="height: 80px;"></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="4" id="searchCon"><!--定义好摆放按钮的TD的ID -->
							<input type="button" value="提 交" class="button1" onclick="javascript:hch.check();">
							<input type="button" value="关闭窗口" class="button1" onclick="javascript:hch.close('indexLayer');">     
							<input type ="hidden" name="hd_rtid" value="" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div style="line-height:32px;">
			<small><b>审核意见</b></small>
			<select id="reviewType" class="select" style="width:80px;" onchange="refreshFeedback(this);">
				<option value="0"></option>
				<option value="1">通过</option>
				<option value="2">退回</option>
			</select>	 
		</div>
		<div style="border-bottom:0px; min-width: 600px; border: 1px solid #bebabb;" >
			<table id="tablehead" border="0" cellpadding="0" cellspacing="1" class="table01">
				<thead>
					<th class="table_title" width="40px">序号</th>
					<th class="table_title" width="120px">部门</th>
					<th class="table_title" width="120px">反馈人</th>
					<th class="table_title" width="50px">工作<br>进度</th>
					<th class="table_title" style="min-width:190px;">工作反馈</th>
					<th class="table_title" width="100px">时间</th>
					<th class="table_title" width="80px">操作</th>
				</thead>
				<tbody id="feedbacklist">
				</tbody>
			</table>
		</div>
			<!-- <div style="overflow-y: auto; overflow-x: auto;">
				<table id="feedbacklist" border="0" cellpadding="0" cellspacing="1" class="table01">
				</table>
			</div> -->
		</div>
	</div>
	<div class="show-dept" id="reviewDiv" title="驳回" style="display:none;">
		<div style="padding: 10px 10px;background-color:#DEEFFF; min-width: 400px;">
			<div style="height: 164px;">
				<table border="0" cellpadding="0" cellspacing="1" class="table01">
					<tbody>
						<tr>
							<td class="tab-td-title">驳回意见</td>
							<td class="tab-td-content">
								<textarea id="rvwmark" name="rvwmark" style="height: 100px;"></textarea>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="tab-td-title" style="line-height:35px;"><!--定义好摆放按钮的TD的ID -->
								<input type="button" value="驳 回" class="button1" onclick="javascript:hch.retFeedback();">
								<input type="button" value="关闭窗口" class="button1" onclick="layer.close(rvwLayer);"> 
								<input type="hidden" value="0" id="rvwFid" />
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="show-dept" id="select_type" title="选择台账类型" style="display:none;">
		<div style="padding: 10px 10px;height:auto;"><br>
			<div style="height: 40px;line-height: 40px; text-align:center;">
				台账类型：
				<select name="output_type" id="output_type" class="select">
					<?php
					foreach($task_type as $key=>$v){
						echo '<option value="' . $key . '">' . $v . '</option>';
					}
					?>
				</select>	  
			</div><br><br>
			<div style="clear: both;line-height: 35px;text-align:center;padding-top: 20px;">
				<input type="submit" value="导 出" style="cursor:pointer" class="button1" onclick="javascript:hch.exportFunc();">&nbsp;
				<input type="button" value="关 闭" style="cursor:pointer" class="button1" onclick="javascript:hch.close('exportLayer');"> 
			</div>
		</div>
	</div>
	<div class="show-dept" id="layertip" style="display:none;">
		<div style="padding: 18px 10px 10px 10px;height:auto; text-align:center;">
			<small style="color: blue;"><b>正在导出，请稍事休息，谢谢。。。</b></small>
		</div>
	</div>
	<div class="show-dept" id="layersmstip" style="display:none;">
		<div style="padding: 18px 10px 10px 10px;height:auto; text-align:center;">
			<small style="color: blue;"><b>正在查询有任务未反馈的单位列表，请稍等。。。</b></small>
		</div>
	</div>
	<div class="show-dept" id="input_task" title="选择台账类型" style="display:none;">
		<form action="importexcel2.php" method="post" enctype="multipart/form-data">
			<div style="padding: 10px 10px;height:auto;">
				<br>
				<div style="height:auto;line-height: 40px; text-align:center;">
					台账类型：<select name="input_type" id="input_type" class="select">
					<?php
					foreach($task_type as $key=>$value){
						echo '<option value="' . $key . '">' . $value . '</option>';
					}
					?>
				</select>	
				<br>
				选择文件：<input style="width:186px;border:none;" type="file" value="导入文件" name="file" id="file" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>
			</div>
			<br><br>
			<div style="clear: both;line-height: 35px;text-align: center;padding-top: 20px;">
				<input type="button" value="导 入" style="cursor:pointer" class="button1" onclick="hch.input_submit();">&nbsp;
				<input type="button" value="关 闭" style="cursor:pointer" class="button1" onclick="hch.close2();"> 
			</div>
		</div>
	</form>
</div>
</body>
</html>
<script type="text/javascript">
	var hch = {
		open_sms:function(taskid){
			layer.open({
				type:2,
				title:'短信提醒',
				skin: 'layui-layer-rim', //加上边框
				area: ['95%', '95%'], //宽高
				content: "../sendsms.php?mt=taskreview&tid="+taskid
			});
		},
		open_notice:function(){
			var html = '<table border="0" cellpadding="4" cellspacing="1" class="table01">'+$("#content_table tbody").html()+ '</table>';
			$("#infoContent").attr("value", html);
			document.forms['noticeadd_form'].submit();
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
		onsmstip:function(){
			smstipLayer = layer.open({
				type: 1,
				title: '',
				closeBtn: 0,
		        skin: 'layui-layer-rim', //加上边框
		        area: ['400px', '50px'], //宽高
		        content: $("#layersmstip")
		    });
			$(".layui-layer-rim").css("top", "150px");
			$(".layui-layer-rim").css("background-color", "#DEEFFF");
		},
		open_input:function(){
			this.index_input = layer.open({
				type: 1,
				title: $('#input_task').attr("title"),
				skin: 'layui-layer-rim', //加上边框
				area: ['600px', '300px'], //宽高
				offset: "120px",
				content: $("#input_task")
			});
			$(".layui-layer-rim").css("top", "120px");
			$(".layui-layer-rim").css("background-color", "#DEEFFF");
		},
		input_submit:function(){
			var type = $("#input_type").val();
			$.ajaxFileUpload({
				url:'importexcel2.php',
				type: 'post', 
				secureuri:false,
				fileElementId:'file',
				dataType: 'text',
				data:{type:type},
				success: function (res){
					hch.close2();
					alert(res);
					window.location.reload();
				}
			});
		},
		onexport:function(){
			exportLayer = layer.open({
				type: 1,
				title: $('#select_type').attr("title"),
	            skin: 'layui-layer-rim', //加上边框
	            area: ['400px', '300px'], //宽高
	            content: $("#select_type")
	        });
			$(".layui-layer-rim").css("top", "120px");
			$(".layui-layer-rim").css("background-color", "#DEEFFF");
		},
		exportFunc:function(){
			var type = $("#output_type").val();
			layer.close(exportLayer);

			hch.onexporttip();
			
			$.get('exportexcel2.php', {'type':type}, function(res){
				layer.close(tipLayer);
				window.location.href = res;
				// setTimeout("hch.close('tipLayer')", 2000);
			});   		
		},
		onreviewtask: function (id, obj) {
			var srole = $("#hdsRole").val();
			var urole = $("#hduRole").val();
			var curDate = $("#hdDate").val();
			var role = $(obj).next().val();
			var backtime = $(obj).next().next().val();
			//非督查室主任
			//if( urole!= srole){
				//督查室主任已审核
			//	if(role == srole)
					//按时报，则直接返回
			//		if(backtime=='0'){
			//			layer.msg("该任务已被督查室主任审核，您不能进行审核");
			//			return;
			//		}
					//当前台账工作区间内，普通台账管理员不可审核
			//		else if(curDate <= backtime){
			//			layer.msg("该任务已被督查室主任审核，您暂时不能进行审核");
			//			return;
			//		}
			//}
			indexLayer = layer.open({
				type: 1,
				title: $('#tree').attr("title"),
				// closeBtn: 0,
                skin: 'layui-layer-rim', //加上边框
                area: ['80%', '90%'], //宽高
                content: $("#tree")
            });
			$(".layui-layer-rim").css("background-color", "#DEEFFF");
		   $("input[name='hd_rtid']").val(id);//保存taskid
		   $("select[name='taskstate_b']").val(0);
		   $("input[name='progress_b']").val('');
		   $("textarea[name='remark_b']").val('');
		   loadFeedBack(id, 0);
		},
		close: function (index) {
			if(index == 'indexLayer'){
				layer.close(indexLayer);
	        	// $(document.body).css('overflow-y', 'auto');//动态显示主页面的滚动条
	        }else if(index == 'exportLayer'){
	        	layer.close(exportLayer);
	        }else if(index == 'tipLayer'){
	        	layer.close(tipLayer);
	        }        	
	    },
	    close2: function () {
	    	layer.close(this.index_input);
	    },
	    check:function(){
	    	var taskid = $("input[name='hd_rtid']").val();
	    	var state = $("select[name='taskstate_b']").val();
	    	var progress = $("input[name='progress_b']").val();
	    	var remark = $("textarea[name='remark_b']").val().trim();
	    	
	    	var num_pattern = /^(\d\d?|100)$/;
	    	if(!progress){
	    		layer.msg("总体进度不能为空！");
	    		return false;
	    	}
	    	if(!num_pattern.test(progress)){
	    		layer.msg("总体进度只能是大于0小于或等于100的整数！");
	    		return false;
	    	}
	    	if (!remark) {
	    		layer.msg("审核结论不能为空！");
	    		return false;
	    	}
	    	
	    	var param={
	    		'taskid': taskid,
	    		'state': state,
	    		'progress': progress,
	    		'remark': remark
	    	};
	    	$.post("taskrecvmanager.php?do=reviewTask",param, function (res){
	    		if(res==1){
	    			layer.msg("任务审核成功！");
	    			hch.close("indexLayer");
	    			if(state == 2)
	    				state = "完成";
	    			else
	    				state = "未完成";
	    			setTimeout('refresh(' + taskid + ',"' + state + '", ' + progress + ', "' + remark + '")', 800);
	    		}else if(res == 2){
	    			layer.msg("该任务已被督查室主任审核，您暂时不能进行审核");
					//setTimeout('refresh()', 1000);
				}else{
					layer.msg("任务审核失败！");
				}
			}, 'json');  
	    },
	    delete_feedback:function(id){
	    	var param={
	    		'id': id
	    	};
	    	$.post("taskrecvmanager.php?do=deleteFeedback",param, function (res){
	    		if(res==1){
	    			layer.msg("删除成功！");
	    			$("#feedback_"+id).remove();
	    		}else{
	    			layer.msg("删除失败！");
	    		}
	    	}, 'json');
	    },
	    accFeedback:function(id){
	    	$.post("taskrecvmanager.php?do=acceptFeedback",{'id':id}, function (res){
	    		if(res==1){
	    			layer.msg("反馈审核成功！");
	    			refreshFeedback(document.getElementById("reviewType"));
	    		}else{
	    			layer.msg("反馈审核失败！");
	    		}
	    	}, 'json');
	    },
	    openRvwDiv:function(id){
	    	rvwLayer = layer.open({
	    		type: 1,
	    		title: $('#reviewDiv').attr("title"),
				// closeBtn: 0,
                skin: 'layui-layer-rim', //加上边框
                area: ['400', '250'], //宽高
                content: $("#reviewDiv")
            });
	    	$("#rvwFid").val(id);
	    },
	    retFeedback:function(){
	    	var tid = $("#rvwFid").val();
	    	var mark = $("#rvwmark").val();
	    	if(!mark){
	    		layer.msg("请输入驳回意见！");
	    		return false;
	    	}
	    	var param = {
	    		'id': tid,
	    		'mark': mark
	    	};
	    	$.post("taskrecvmanager.php?do=returnFeedback", param, function (res){
	    		if(res==1){
	    			layer.msg("反馈驳回成功！");
	    			layer.close(rvwLayer);
	    			refreshFeedback(document.getElementById("reviewType"));
	    		}else{
	    			layer.msg("反馈驳回失败！");
	    		}
	    	}, 'json');
	    }
	}
	function refresh(tid, state, progress, remark){
		var obj = $("#hisRvwDiv"+tid);
		var stDate = getDate();
		var uname = "<?=$username?>";
		var bShow = <?=$bShow?>;
		var html = uname + "：" + progress + "%<br>";
		html += "&emsp;" + stDate + "<br>";
		html += "&emsp;" + remark;
		
		var preObj = $(obj).prev();
		$(preObj).css('display', 'block');
		$(preObj).children('span').css('width', progress+'%');
		$(obj).parents('td').prev().text(state);
		if(bShow == 1 && $(obj).html() == ""){
			var pObj = $('<div class="link"></div>');
			var spanObj = $('<span>修改</span>');
			$(spanObj).attr('onclick', 'rvw.showHisRvw('+tid+');');
			$(pObj).append(spanObj);
			$(obj).parent().append(pObj);							
		}
		$(obj).html(html);
		
		
	}
	$(function(){
		$(".resizable").resizable();
		var state = <?=$taskstate?>;
		$("select[name='taskstate_a']").val(state);
		
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
            	var state = $("#h_state").val();
            	var type = $("#h_tasktype").val();
            	var target = $("#h_target").val();
            	var generaltask = $("#h_generaltask").val();
            	
            	if(!page)
            		page = 0;
            	var param={
            		'state': state,
            		'type': type,
            		'target': target,
            		'generaltask': generaltask,
            		'page': page
            	}
            	$.post('taskrecvmanager.php?do=querynextpage', param,function(res){
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
	//分页添加数据
	function addData(data){
		//获取上一次加载列表中最后一个总体任务id
		var pregeneraltask = $("#h_pregeneraltask").val();
		var page = $("#h_page").val();
		var index = (parseInt(page)-1)*15;
		var bShow = $("#hdShowMod").val();
		//保存当前列表中最后一个总体任务id
		$("#h_pregeneraltask").val(data[data.length-1]['gtaskid']);
		var html = "";
		
		for(var i=0; i<data.length; i++){
			if(pregeneraltask != data[i]['gtaskid']){
				html += '<tr height="35px"><td colspan="12" class="table_title">' + data[i]['gtask'] + '</td></tr>';
				/*var generaltaskTR = $('<tr height="35px"></tr>').appendTo($("#cons"));
				$('<td colspan="12" class="table_title"></td>').text(data[i]['gtask']).appendTo(generaltaskTR);*/
			}        
			for(var j=0; j<data[i]['proj'].length; j++){
				index++;
				//var trObj = $("<tr></tr>").appendTo($("#cons"));
				var rows = data[i]['proj'][j]['rowspan'];
				var process_id = data[i]['proj'][j]['pro_id'];
				var stages = data[i]['proj'][j]['stage'];
				var sdates = data[i]['proj'][j]['sdate'];
				var edates = data[i]['proj'][j]['edate'];
				var state = data[i]['proj'][j]['status'];
				
				var state_txt = "完成";
				if(state == 1)
					state_txt = "未完成";
				html += '<tr><td rowspan="' + rows + '"><div class="resizable">' + index + '</div></td>'
				+ '<td rowspan="' + rows + '"><div class="resizable">' + data[i]['proj'][j]['gtarget'] + '</div></td>';
				
				var id="title"+data[i]['proj'][j]['id'];
				var content = data[i]['proj'][j]['title'];
				var func = "do_edit('"+id+"', '"+content+"', 1);";
				html += '<td rowspan="' + rows + '" id="'+id+'" name="title" onclick="'+func+'"><div class="resizable">' + data[i]['proj'][j]['title'] + '</div></td>';
				
				id="investment"+data[i]['proj'][j]['id'];
				content = data[i]['proj'][j]['investment'];
				func = "do_edit('"+id+"', '"+content+"', 1);";
				html += '<td rowspan="' + rows + '" id="'+id+'" name="investment" onclick="'+func+'"><div class="resizable">' + data[i]['proj'][j]['investment'] + '</div></td>';
				
				id="stage"+process_id[0];
				content = stages[0];
				func = "do_edit('"+id+"', '"+content+"', 2);";
				html += '<td style="padding: 5px 5px;" id="'+id+'" name="stage" onclick="'+func+'"><div class="resizable">' + stages[0] + '</div></td>'
				+ '<td><div class="resizable">' + sdates[0] + '</div></td>'
				+ '<td><div class="resizable">' + edates[0] + '</div></td>';

				html += '<td rowspan="' + rows + '"><div class="resizable">' + state_txt + '</div></td>'
				html += '<td rowspan="' + rows + '" style="text-align:left;"><div class="resizable">';
				if(data[i]['proj'][j]['complete_perc'] != "0"){
					html += '<div class="progress">';
				}else{
					html += '<div class="progress" style="display:none">';
				}
				html += '<span class="green" style="width:' + data[i]['proj'][j]['complete_perc'] +';"></span>';
				html += '</div>';
				html += '<div id="hisRvwDiv' + data[i]['proj'][j]['taskid'] + '">' + data[i]['proj'][j]['hisReview'] + '</div>';
				if(bShow == '1' && data[i]['proj'][j]['hisReview'].length>1)
				{
					html += '<div class="link">';
					html += '<span onclick="rvw.showHisRvw('+data[i]['proj'][j]['taskid']+');">修改</span>';
					html += '</div>';
				}
				html += '</div></td>';
				html += '<td rowspan="' + rows + '" style="text-align:left;"><div class="resizable">' + data[i]['proj'][j]['header_s'] + '</div>';
				html += '<div class="link" onclick="show_header('+ index + ');">';
				html += '<span style="cursor:pointer;" onclick="show_header('+ index + ');">查看全部</span><input type="hidden" id="h_header'+index+'" value="'+data[i]['proj'][j]['header_l']+'"></div></td>'
				+ '<td rowspan="' + rows + '" style="text-align:left;"><div class="resizable">已反馈：<span class="link" onclick="show_feedback_yes('+index+')">' + data[i]['proj'][j]['feedback']['y_cnt'] + '</span><br>未反馈：<span class="link" onclick="show_feedback_no('+index+')">'+data[i]['proj'][j]['feedback']['n_cnt']+'</span><input type="hidden" id="h_feedback_yes'+index+'" value="'+data[i]['proj'][j]['feedback']['f_yes']+'" ><br>';
				html += '待审核：<span class="link" >'+data[i]['proj'][j]['fb_no_rvw_cnt']+'</span>';
				html += '<input type="hidden" id="h_feedback_no'+index+'" value="'+data[i]['proj'][j]['feedback']['f_no']+'" ></div>' + '<div class="pic"><a target="_blank" href="picshow.php?taskid=' + data[i]['proj'][j]['taskid'] + '"><img src="../img/f1.png" style="width:30px; height: 30px;"></a></div>';
				//check button
				if($("#route").val() == 2){
					html += '<div style="height:5px; clear:both;"></div>';
					html += '<div style="text-align:center;">'
					html += '<input type="button" class="button1" value="审核" onclick="hch.onreviewtask(' + data[i]['proj'][j]['taskid'] + ', this)">';
					html += '<input type="hidden" class="hdRoleId" value="' + data[i]['proj'][j]['roleid'] + '" />';
					html += '<input type="hidden" class="hdBackTime" value="' + data[i]['proj'][j]['backendtime'] + '" />';
					html += '<div style="height:2px"></div>';
					//html += '<input type="button" class="button1" value="短信提醒" onclick="javascript:onsmsnotice(' + data[i]['proj'][j]['taskid'] + ')">';
					html += '<input type="button" class="button1" value="短信提醒" onclick="hch.open_sms('+ data[i]['proj'][j]['taskid'] +');">';
					html += '</div>';
				}
				html +='</td></tr>';
				//stages
				if(stages.length > 1){
					for(var k=1; k<stages.length; k++){
						id="stage"+process_id[k];
						content = stages[k];
						func = "do_edit('"+id+"', '"+content+"', 2);";
						html += '<tr>'
						+ '<td style="padding: 5px 5px;" id="'+id+'" name="stage" onclick="'+func+'"><div class="resizable">' + stages[k] + '</div></td>'
						+ '<td><div class="resizable">' + sdates[k] + '</div></td>'
						+ '<td><div class="resizable">' + edates[k] + '</div></td>'
						+ '</tr>';
					}
				}
				/*$("<td></td>").text(index).attr('rowspan', rows).appendTo(trObj);
				$("<td></td>").text(data[i]['proj'][j]['gtarget']).attr('rowspan', rows).appendTo(trObj);
				$("<td></td>").text(data[i]['proj'][j]['title']).attr('rowspan', rows).appendTo(trObj);
				$("<td></td>").text(data[i]['proj'][j]['investment']).attr('rowspan', rows).appendTo(trObj);
				if(stages.length > 0){
					$("<td style='padding: 5px 5px;'></td>").text(stages[0]).appendTo(trObj);
					$("<td></td>").text(sdates[0]).appendTo(trObj);
					$("<td></td>").text(edates[0]).appendTo(trObj);
				}else{
					$("<td style='padding: 5px 5px;'></td>").appendTo(trObj);
					$("<td></td>").appendTo(trObj);
					$("<td></td>").appendTo(trObj);
				}
				$("<td></td>").text(state_txt).attr('rowspan', rows).appendTo(trObj);
				$("<td style='text-align:left;'></td>").append(data[i]['proj'][j]['hisReview']).attr('rowspan', rows).appendTo(trObj);
				$("<td style='text-align:left;'></td>").append(data[i]['proj'][j]['headers']).attr('rowspan', rows).appendTo(trObj);
				//feedback
				var fbTD = $("<td style='text-align:left;'></td>").attr('rowspan', rows).appendTo(trObj);
				var divObj = $("<div style='float: right; height: 30px; width: 30px; cursor: pointer;'></div>").appendTo(fbTD);
				var aObj = $("<a target='_blank'></a>").attr('href', 'picshow.php?taskid='+data[i]['proj'][j]['taskid']).appendTo(divObj);
				$("<img src='../img/f1.png' style='width:30px; height: 30px;'>").appendTo(aObj);
				$(fbTD).append(data[i]['proj'][j]['data[i]['proj'][j]['feedback']']);
				//check button
				if($("#route").val() == 2){
					var btnTD = $("<td></td>").attr('rowspan', rows).appendTo(trObj);
					var btnObj = $("<input type='button' class='button1'>").val('审核').attr('onclick', 'hch.onreviewtask('+data[i]['proj'][j]['taskid']+");");
					btnObj.appendTo(btnTD);
				}
				//stages
				if(stages.length > 1){
					for(var k=1; k<stages.length; k++){
						var trObj2 = $("<tr></tr>").appendTo($("#cons"));
						$("<td style='padding: 5px 5px;'></td>").text(stages[k]).appendTo(trObj2);
						$("<td></td>").text(sdates[k]).appendTo(trObj2);
						$("<td></td>").text(edates[k]).appendTo(trObj2);
					}
				}*/
			}	
		}
		$("#content_table tr:last").after(html);
		$(".resizable").resizable();
	}
	function show_header(index){
		var header_a = $("#h_header"+index).val();
		layer.open({
			title: '责任主体',
			skin: 'layui-layer-rim', //加上边框
			area: ['500px', 'auto'], //宽高
			content: header_a
		});
	}
	function show_feedback_yes(index){
		var headers = $("#h_feedback_yes"+index).val();
		if(!headers){
			layer.msg("没有任何单位反馈！");
			return false;
		}
		
		$.post("taskrecvmanager.php?do=getfeedbackbyid",{'ids':headers}, function(res){
			$("#fbtable").empty();
			if(res.length == 0){
				var trObj = $("<tr></tr>");
				$("<td colspan='5'></td>").text('暂时没有任何反馈记录！').appendTo(trObj);
				trObj.appendTo($("#fbtable"));
		   		// $("#tablehead").width($("#feedbacklist").width());
		   	}
		   	for(var i=0; i<res.length; i++){
		   		var trObj = $("<tr></tr>");
		   		$("<td></td>").text((i+1)).appendTo(trObj);
		   		$("<td></td>").text(res[i]['dname']).appendTo(trObj);
		   		$("<td></td>").text(res[i]['progress']+'%').appendTo(trObj);
		   		var tdObj = $("<td style='text-align:left;'></td>").text(res[i]['remark']).appendTo(trObj);
		   		if(res[i]['reporturl'] != null){
		   			if($(tdObj).text())
		   				tdObj.append($("<br/>"));
		   			for(var j=0; j<res[i]['reporturl'].length; j++){
		   				if(j>0)
		   					tdObj.append('；');
		   				var aobj = $("<a target='_blank'></a>").text(res[i]['fname'][j]).attr('href', res[i]['reporturl'][j]);
		   				tdObj.append(aobj);			
		   			}	   				
		   		}
		   		
		   		$("<td></td>").text(res[i]['backtime']).appendTo(trObj);
		   		trObj.appendTo($("#fbtable"));
		   	}
		   },'json');
		layer.open({
			type: 1,
			title: '最新反馈列表',
			skin: 'layui-layer-rim', //加上边框
			area: ['800px', '600px'], //宽高
			content: $("#fbdiv")
		});
	}	
	function show_feedback_no(index){
		var headers = $("#h_feedback_no"+index).val();
		layer.open({
			title: '未反馈单位',
			skin: 'layui-layer-rim', //加上边框
			area: ['500px', 'auto'], //宽高
			content: headers
		});
	}
	function do_edit(obj, content, type){
		$("#"+obj).removeAttr("onclick");
		var name = $("#"+obj).attr('name');
		var id = obj.substring(name.length);
		var height = $("#"+obj).height();
		var html = '<textarea class="text_area_edit" style="height:' + height + 'px" type="text" onblur="do_leave(\''+obj+'\',' + id + ',\'' + name + '\','+type+')"></textarea>';
		$("#"+obj).html(html);
		$("textarea").focus().val(content);
	}
	function do_leave(obj, id, name, type){
		var value = $("#"+obj+" textarea").val();
		if(type == "2"){
			$("#"+name+id).html('<div class="resizable">'+value+'</div>');
			$("#"+name+id).attr("onclick","do_edit('"+obj+"','"+value+"',2);");
			$.ajax({
				type:'post',
				url:"updateTask.php",	
				data:{id:id, name:name, value:value, type:type, modifier:'<?php echo $username; ?>', modtime:'<?php echo $time; ?>'},
				success:function(result){
					
				}
			});
		}else{
			$("#"+obj).html('<div class="resizable">'+value+'</div>');
			$("#"+obj).attr("onclick","do_edit('"+obj+"','"+value+"',1);");
			$.ajax({
				type:'post',
				url:"updateTask.php",	
				data:{id:id, name:name, value:value, modifier:'<?php echo $username; ?>', modtime:'<?php echo $time; ?>'},
				success:function(result){
					
				}
			});
		}
	}
	function onsmsnotice(taskid){
		layer.confirm("是否发送短息？", {icon:3, title:"短信提示"}, 
	        function(index){//确定按钮回调函数
	        	$.post("../xitong/smsmanager.php?do=sendsmsbytaskid",{'taskid': taskid}, function(res){
	        		layer.close(index);
	        		layer.msg(res['msg']);
	        	}, "json");
	        	layer.close(index);
		       	// document.forms[0].submit();
	        },function(index){//取消回调函数
	        	// document.forms[0].submit();
	        }
	        );
	}
	function onsmstoall(){
		hch.onsmstip();
		var tasktype = $("#tasktypes").val();
		$.post("../xitong/smsmanager.php?do=getnofeedbackdept",{'tasktype':tasktype}, function(res){
			layer.close(smstipLayer);
			if(res['state'] != 1)
				layer.msg(res['msg']);
			else{
				layer.open({
					type:2,
					title:'短信提醒',
					skin: 'layui-layer-rim', //加上边框
					area: ['95%', '95%'], //宽高
					content: "../sendsms.php?mt=taskreviewall&tid="+res['dids']
				});
			}
		}, "json");
	}
	function refreshFeedback(obj){
		var taskid = $("input[name='hd_rtid']").val();
		var state = $(obj).val();
		loadFeedBack(taskid, state);
	}
	function loadFeedBack(tid, state){
		var param={
			'taskid': tid,
			'state': state
		};
		$.post("taskrecvmanager.php?do=getFeedbackByTaskid", param, function (res){
			$("#feedbacklist").empty();
			if(res.length == 0){
				var trObj = $("<tr></tr>");
				$("<td colspan='7' style='color:gray;font-size:12px;'></td>").text('暂时没有任何反馈记录！').appendTo(trObj);
				trObj.appendTo($("#feedbacklist"));
		   		// $("#tablehead").width($("#feedbacklist").width());
		   	}
		   	for(var i=0; i<res.length; i++){
		   		var trObj = $("<tr id='feedback_" + res[i]['id'] + "'></tr>");
		   		$("<td></td>").text((i+1)).appendTo(trObj);
		   		var isHead = res[i]['ishead'];
		   		var txtObj = $("<span style='color:blue; font-weight:bold;'><span>").text(res[i]['dname']);
		   		if(isHead == "1") 
		   			$("<td></td>").append(txtObj).appendTo(trObj);
		   		else
		   			$("<td></td>").text(res[i]['dname']).appendTo(trObj);
		   		$("<td></td>").text(res[i]['uname']).appendTo(trObj);
		   		$("<td></td>").text(res[i]['progress']+'%').appendTo(trObj);
		   		var tdObj = $("<td style='text-align:left;'></td>").text(res[i]['remark']).appendTo(trObj);
		   		if(res[i]['reporturl'] != null){
		   			if($(tdObj).text())
		   				tdObj.append($("<br/>"));
		   			for(var j=0; j<res[i]['reporturl'].length; j++){
		   				if(j>0)
		   					tdObj.append('；');
		   				var aobj = $("<a target='_blank'></a>").text(res[i]['fname'][j]).attr('href', res[i]['reporturl'][j]);
		   				tdObj.append(aobj);				
		   			}	   				
		   		}
		   		
		   		$("<td></td>").text(res[i]['backtime']).appendTo(trObj);
		   		var html = "";
		   		if(res[i]['rvwstate'] == 1){
		   			html += "<div style='line-height:28px;'><small>已通过</small></div>";
		   		}else if(res[i]['rvwstate'] == 2){
		   			html += "<div style='line-height:28px;'><small title='"+res[i]['rvwmark']+"'>已驳回</small></div>";
		   		}else{
		   			html += '<input type="button" value="通 过" class="button1" onclick="javascript:hch.accFeedback('+res[i]['id']+');">';
		   			html += '<div style="height:2px;"></div>';
		   			html += '<input type="button" value="退 回" class="button1" onclick="javascript:hch.openRvwDiv('+res[i]['id']+');">';
		   			html += '<div style="height:2px;"></div>';
		   		}
		   		html += '<input value="删 除" class="button1" onclick="javascript:hch.delete_feedback('+res[i]['id']+');" type="button">';
		   		$("<td></td>").html(html).appendTo(trObj);
		   		trObj.appendTo($("#feedbacklist"));
		   	}
		   	// $("#tablehead").width($("#feedbacklist").width());
		   }, 'json');
	}
	
	var rvw = {
		showHisRvw:function(tid){
			layer.open({
				type: 1,
				title: '历史审核列表',
				skin: 'layui-layer-rim', //加上边框
				area: ['800px', '600px'], //宽高
				content: $("#rvwDiv")
			});
			$(".layui-layer-rim").css("top", "150px");
			$(".layui-layer-rim").css("background-color", "#DEEFFF");
			
			$("#hdTaskId").val(tid);
			rvw.loadHisRvw(tid);
		},
		loadHisRvw:function(tid){
			var param={'tid': tid}
			$.post("taskrecvmanager.php?do=getHistoryReview", param, function (res){
				$("#reviewList").empty();
				if(res.length == 0){
					var trObj = $("<tr></tr>");
					$("<td colspan='7' style='color:gray;font-size:12px;'></td>").text('暂时没有任何反馈记录！').appendTo(trObj);
					trObj.appendTo($("#reviewList"));
					
				}
				var state_txt = "未完成";
				for(var i=0; i<res.length; i++){
					state_txt = "未完成";
					var trObj = $("<tr id='rvw_" + res[i]['id'] + "'></tr>");
					//序号
					$("<td></td>").text((i+1)).appendTo(trObj);
					//审核员
					$("<td></td>").append(res[i]['uname']).appendTo(trObj);
					//状态
					if(Number(res[i]['state']) == 2 || Number(res[i]['state'])==3)
						state_txt = "完成";
					$("<td></td>").attr('id', 'st'+res[i]['id']).attr('onclick', 'rvw.edit(this, 1);').append(state_txt).appendTo(trObj);
					//进度
					$("<td></td>").attr('id', 'pg'+res[i]['id']).attr('onclick', 'rvw.edit(this, 2);').text(res[i]['progress']+'%').appendTo(trObj);
					//结论
					$("<td></td>").attr('id', 'rm'+res[i]['id']).attr('onclick', 'rvw.edit(this, 3);').text(res[i]['remark']).appendTo(trObj);
					//时间
					$("<td></td>").text(res[i]['viewtime']).appendTo(trObj);
					trObj.appendTo($("#reviewList"));
				}
			}, "json");
		},
		edit:function(obj, type){
			$(obj).removeAttr('onclick');
			var width = $(obj).width();
			var objVal = $(obj).text();
			var hdObj = $("<input type='hidden' id='hdPreVal'>");
			
			var newObj;
			$(obj).text("");
			if(type == 1){
				newObj = $("<select><select>");
				$(obj).append(newObj);
				$(newObj).attr('onblur', 'rvw.update(this, 1)');
				$(newObj).append($("<option value='1'>未完成</option>"));
				$(newObj).append($("<option value='2'>完成</option>"));
				if(objVal == "完成"){
					$(hdObj).val(2);
					$(newObj).val(2);
				}
				else{
					$(hdObj).val(1);
					$(newObj).val(1);
				}
			}else if(type==2){
				newObj = $('<input type="text" >');
				$(obj).append(newObj);
				$(newObj).attr('maxlength', 3);
				$(newObj).attr('onblur', 'rvw.update(this, 2)');
				$(newObj).attr('onkeyup', "this.value=this.value.replace(/\D/g,'')");
				$(newObj).val(objVal.slice(0, -1));
				$(hdObj).val(objVal.slice(0, -1));
			}else if(type == 3){
				newObj = $('<textarea></textarea>');
				$(obj).append(newObj);
				$(newObj).attr('onblur', 'rvw.update(this, 3)');
				$(newObj).val(objVal);
				$(hdObj).val(objVal);
			}
			$(newObj).css('width', width+"px");
			$(newObj).focus();
			$(obj).append(hdObj);
		},
		update:function(obj, type){
			var parObj = $(obj).parent();
			var uname = "<?=$username?>";
			var dtObj, userObj;
			var rvwId = $(parObj).attr('id').substring(2);
			var tid = $("#hdTaskId").val();
			//赋值，未变不提交
			var objVal = $(obj).val();
			var preVal = $(obj).next().val();
			if(type == 2){
				var num_pattern = /^(\d\d?|100)$/;
				if(!num_pattern.test(objVal)){
					layer.msg("总体进度输入错误<br>总体进度只能是大于0小于或等于100的整数！");
					$(obj).val(preVal);
					objVal = preVal;
				}
			}
			var param={
				'rvwId': rvwId,
				'taskid': tid,
				'type': type,
				'key': objVal
			}
			
			$(obj).removeAttr('onblur');
			var bRet = false;
			if(preVal != objVal){
				$.post("taskrecvmanager.php?do=modRviewById", param, function (res){
					//刷新最新审核列表
					if(type == 1){
						userObj = $(parObj).prev();
						dtObj = $(parObj).next().next().next();
						$(parObj).text($(obj).find("option:selected").text());
					}
					else if(type == 2){
						userObj = $(parObj).prev().prev();
						dtObj = $(parObj).next().next();
						$(parObj).text(objVal+"%");
					}
					else{
						userObj = $(parObj).prev().prev().prev();
						dtObj = $(parObj).next();
						$(parObj).text(objVal);
					}
					
					var progress, remark, state;
					if(type == 1){
						state = "未完成";
						if(objVal == 2)
							state = "完成";
						progress = $(parObj).next().text();
						progress = progress.slice(0, -1);
						if($(parObj).next().children('input').length > 0)
							progress = $(parObj).next().children('input').val();
						
						remark = $(parObj).next().next().text();
						if($(parObj).next().next().children('textarea').length > 0)
							remark = $(parObj).next().next().children('textarea').val();
					}
					if(type == 2){
						state = $(parObj).prev().text();
						if($(parObj).prev().children('select').length > 0)
							state = $(parObj).prev().children('select').find("option:selected").text();
						progress = objVal;
						remark = $(parObj).next().text();
						if($(parObj).next().children('textarea').length > 0)
							remark = $(parObj).next().children('textarea').val();
					}else if(type == 3){
						state = $(parObj).prev().prev().text();
						if($(parObj).prev().prev().children('select').length > 0)
							state = $(parObj).prev().prev().children('select').find("option:selected").text();
						progress = $(parObj).prev().text();
						progress = progress.slice(0, -1);
						if($(parObj).prev().children('input').length > 0)
							progress = $(parObj).prev().children('input').val();
						remark = objVal;
					}
					
					refresh(tid, state, progress,  remark);
					bRet = true;
					
					var stDate = getDate();
					$(dtObj).text(stDate);
					$(userObj).text(uname);
					
				}, "json");
			}
			if(!bRet){
				if(type == 1){
					if(preVal == 1)
						$(parObj).text("未完成");
					else if(preVal == 2)
						$(parObj).text("完成");
				}
				
				else if(type == 2)
					$(parObj).text(preVal+"%");
				else
					$(parObj).text(preVal);
				$(obj).next().remove();
				$(obj).remove();
			}
			$(obj).next().remove();
			$(obj).remove();
			$(parObj).attr("onclick", "rvw.edit(this, "+type+");");
			
		}
	}
	function getDate(){
		var myDate = new Date();
		var stMonth = myDate.getMonth()+1;
		var	stDay = myDate.getDate();
		var stDate = myDate.getFullYear()+"-";
		if(stMonth < 10)
			stMonth = "0"+stMonth;
		if(stDay < 10)
			stDay = "0" + stDay;
		stDate += stMonth + "-" + stDay;
		return stDate;
	}
</script>