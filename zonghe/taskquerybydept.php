<?php
include_once '../taizhang/taskrecvmanager.php';

$sep = '%';
$taskstate = empty($_REQUEST['taskstate_a']) ? 0 : $_REQUEST['taskstate_a'];
$type = empty($_REQUEST['tasktypes']) ? 0 : $_REQUEST['tasktypes'];
$target = empty($_REQUEST["target"]) ? $sep.$sep : $sep.$_REQUEST['target'].$sep;
$did = empty($_REQUEST['did']) ? 0 : $_REQUEST['did'];
//判断是否从领导页面过来的
$deptids = empty($_REQUEST['deptids']) ? 0 : $_REQUEST['deptids'];
$lname = empty($_REQUEST['lname']) ? trim('') : $_REQUEST['lname'];
//获取所有的任务：分页查询
$generalTaskId = "";
$data = gettaskofpage($generalTaskId, $taskstate, $type, $target, 0, 15, $did);
$total_count = sizeof($data);
$pregeneraltask = 0;//保存列表中最后一个总体任务的id
if($total_count > 0)
	$pregeneraltask = $data[$total_count-1]['gtaskid'];

?>

<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>按单位查询</title>
	<script type="text/javascript" src="../js/jquery.min.js" ></script>
	<script type="text/javascript" src="../js/layer/layer.js" ></script>
	<!-- <link rel="stylesheet" href="../css/default.css" /> -->
	<link rel="stylesheet" href="../js/layer/skin/layer.css" />
	<link rel="stylesheet" href="../css/common.css" />
    <link rel="stylesheet" href="../css/progress.css" />
	<style type="text/css">
		#feedbacklist td,
		#fbtable td{
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
	</style>
</head>
<body class="main">
	<div id="search">
		<form action="taskquerybydept.php" method="post" style="width: 100%;">
			<table border="0" cellpadding="4" cellspacing="1" class="table01">
				<tr>
					<td colspan="7" class="table_title" style="text-align: right;">
						<span style="margin-right: 45%;">按单位查询</span>
						<img src="../img/back.png" onclick="javascript:back();" style="cursor:pointer;" />
					</td>
					<!--<td class="table_title" style="text-align:right;">-->
						<!-- <input type="button" style="background:url(../img/back.jpg) no-repeat center center; width:80px; height:35px; border:none;"> -->
						<!--<img src="../img/back.jpg" onclick="javascript:back();" style="cursor:pointer;" />-->
					<!--</td>-->
				</tr>
				<tr>
					<td width="80px" class="td_title">
						总体评价
					</td>
					<td width="90px">
						<select name="taskstate_a" class="select" value="" style="width: 80px;">
							<option value="0"></option>
							<option value="1">未完成</option>
							<!--<option value="2">基本完成</option>-->
							<option value="2">完成</option>
						</select>
					</td>
					<td width="80px" height="28" class="td_title">
						台账类型
					</td>
					<td width="130px">
						<select name="tasktypes" class="select" value="" style="width: 120px;">
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
					<td width="80px" class="td_title">
						工作目标
					</td>
					<td width="250px">
						<input type="text" name="target" value="<?=str_replace('%','', $target)?>" class="htmlText" style="width: 240px;"/>
					</td>
					<td>
						<input type="submit" value="查询" class="button1">
						<input type="hidden" id="did" name="did" value="<?=$did?>">
						<input type="hidden" name="lname" value="<?=$lname?>">
						<input type="hidden" name="deptids" value="<?=$deptids?>">
						<input type="hidden" id="h_pregeneraltask" value='<?=$pregeneraltask?>' />
						<input type="hidden" id="h_page" value='1' />
						<input type="hidden" id="h_state" value='<?=$taskstate?>' />
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
					<th rowspan="2" width="13%" class="table_title">支撑项目</th>
					<th colspan="2" width="23%" class="table_title">工作标准</th>
					<th colspan="2" width="10%" class="table_title">时间节点</th>
					<th colspan="2" width="19%" class="table_title">审核进度</th>
					<th rowspan="2" width="11%" class="table_title">责任主体</th>
					<th rowspan="2" width="8%" class="table_title">最新反馈</th>
				</tr>
				<tr class="table_title">
					<th width="5%" class="table_title">投资（元）</th>
					<th width="18%" class="table_title">工作标准</th>
					<th width="5%" class="table_title">开始<br>时间</th>
					<th width="5%" class="table_title">结束<br>时间</th>					
					<th width="5%" class="table_title">是否<br>完成</th>
					<th width="14%" class="table_title">完成情况</th>
				</tr>
			</thead>
			<tbody id="cons">
				<?php
				$total_count = sizeof($data);
				if($total_count == 0){
					?><tr class="alternate_line1">
					<td colspan="11" style="line-height: 35px; text-align: center;">
						<font size="2">没有符合条件的记录</font>
					</td>
				</tr><?php
				}else{
					$z=0;
					for($i=0; $i<$total_count; $i++){
					?><tr height="35px">
						<td colspan="12" class="table_title"><?=$data[$i]['gtask']?></td>
					</tr><?php
						for($j=0; $j<sizeof($data[$i]['proj']); $j++){
							$z = $z + 1;
							$rows = $data[$i]['proj'][$j]['rowspan'];

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
							<td rowspan="<?=$rows?>"><?=$z?></td>
							<td rowspan="<?=$rows?>"><?=$data[$i]['proj'][$j]['gtarget']?></td>
							<td rowspan="<?=$rows?>"><?=$data[$i]['proj'][$j]['title']?></td>
							<td rowspan="<?=$rows?>"><?=$data[$i]['proj'][$j]['investment']?></td>
							<td style="padding: 5px 5px;"><?=count($stages)>0? $stages[0]:''?></td>
							<td><?=count($sdates)>0? $sdates[0]:''?></td>
							<td><?=count($edates)>0? $edates[0]:''?></td>
							<td rowspan="<?=$rows?>"><?=$state_text ?></td>
							<td rowspan="<?=$rows?>" style="text-align: left;">
                                <?php if(!empty($data[$i]['proj'][$j]['complete_perc']))
                                {
                                    ?><div class="progress">
                                    <span class="green" style="width:<?=$data[$i]['proj'][$j]['complete_perc'] ?>;"></span>
                                    </div>
                                    <?php
                                }
                                ?>
                                <?=$data[$i]['proj'][$j]['hisReview'] ?>
                            </td>
							<td rowspan="<?=$rows?>" style="text-align:left;">
								<?=$data[$i]['proj'][$j]['header_s']?>
								<div class="link">
									<span onclick="show_header(<?=$z?>);">查看全部</span>
									<input type="hidden" id="h_header<?=$z?>" value="<?=$data[$i]['proj'][$j]['header_l']?>" >
								</div>
							</td>
							<td rowspan="<?=$rows?>" style="text-align: left;">
								<?php if(!empty($data[$i]['proj'][$j]['feedback'])){?>
								<div class='pic' >
									<a href="../taizhang/picshow.php?taskid=<?=$data[$i]['proj'][$j]['taskid']?>" target="_blank">
										<img src="../img/f1.png" style="width:30px; height: 30px;">
									</a>
								</div>
								<?php }?>
								已反馈：<span class="link" onclick="show_feedback_yes(<?=$z?>)"><?=$data[$i]['proj'][$j]['feedback']['y_cnt']?></span><br>
								未反馈：<span class="link" onclick="show_feedback_no(<?=$z?>)"><?=$data[$i]['proj'][$j]['feedback']['n_cnt']?></span>
								<input type="hidden" id="h_feedback_yes<?=$z?>" value="<?=$data[$i]['proj'][$j]['feedback']['f_yes']?>" >
								<input type="hidden" id="h_feedback_no<?=$z?>" value="<?=$data[$i]['proj'][$j]['feedback']['f_no']?>" >
							</td>
							</tr><?php
							for($k=1; $k<$rows; $k++){
								?><tr>
								<td style='padding: 5px 5px;'><?=$stages[$k]?></td>
								<td style="text-align: center;"><?=$sdates[$k]?></td>
								<td style="text-align: center;"><?=$edates[$k]?></td>
								</tr><?php
							}
						}
					}
				}?>
			</tbody>
		</table>
		<input type="hidden" id="canscroll" value='1' />
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
								<input type="text" name="progress" id="" style="width:60px; border:1px solid #ccc; height:23px;">
								<small style="color: gray;">总体进度（单位：%）只能是大于0小于或等于100的整数</small>
							</td>
						</tr>
						<tr>
							<td class="tab-td-title">审核结论</td>
							<td colspan="3" class="tab-td-content">
								<textarea name="remark" style="height: 80px;"></textarea>
							</td>
						</tr>
						<tr>
							<td colspan="4" id="searchCon"><!--定义好摆放按钮的TD的ID -->
								<input type="submit" value="提 交" class="button1" onclick="javascript:hch.check();">
								<input type="button" value="关闭窗口" class="button1" onclick="javascript:hch.close('indexLayer');">     
								<input type ="hidden" name="hd_rtid" value="" />
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div style="height: 10px;"></div>
			<div style="border-bottom:0px; min-width: 600px; border: 1px solid #bebabb;" >
				<table id="tablehead" border="0" cellpadding="0" cellspacing="1" class="table01">
					<thead>
						<th class="table_title" width="5%">序号</th>
						<th class="table_title" width="15%">部门</th>
						<th class="table_title" width="10%">反馈人</th>
						<th class="table_title" width="8%">工作<br>进度</th>
						<th class="table_title" width="50%">完成情况报告</th>
						<th class="table_title" width="12%">时间</th>
					</thead>
					<tbody id="feedbacklist">
					</tbody>
				</table>
			</div>
		</div>
	</div>
</body>
</html>
<script type="text/javascript">
	var hch = {
		onreviewtask: function (id) {
			indexLayer = layer.open({
				type: 1,
				title: $('#tree').attr("title"),
                skin: 'layui-layer-rim', //加上边框
                area: ['80%', '90%'], //宽高
                content: $("#tree")
            });
			$(".layui-layer-rim").css("background-color", "#DEEFFF");
//			$(".layui-layer-content").css("overflow-x", "auto");
		    $("input[name='hd_rtid']").val(id);//保存taskid
		   // $(document.body).css('overflow-y', 'hidden');//隐藏主页面的滚动条
		    $.post("../taizhang/taskrecvmanager.php?do=getFeedbackByTaskid",{'taskid': id}, function (res){
		   		$("#feedbacklist").empty();
		   		if(res.length == 0){
		   			var trObj = $("<tr></tr>");
		   			$("<td colspan='6'></td>").text('暂时没有任何反馈记录！').appendTo(trObj);
		   			trObj.appendTo($("#feedbacklist"));
		   			// $("#tablehead").width($("#feedbacklist").width());
		   		}
		   		for(var i=0; i<res.length; i++){
		   			var trObj = $("<tr></tr>");
		   			$("<td></td>").text((i+1)).appendTo(trObj);
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
		   			trObj.appendTo($("#feedbacklist"));
		   		}
		   		// $("#tablehead").width($("#feedbacklist").width());
		   	}, 'json');
		   
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
	    check:function(){
	    	var taskid = $("input[name='hd_rtid']").val();
	    	var state = $("select[name='taskstate_b']").val();
	    	var progress = $("input[name='progress']").val();
	    	var remark = $("textarea[name='remark']").val().trim();

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
	    	$.post("../taizhang/taskrecvmanager.php?do=reviewTask",param, function (res){
	    		if(res==1){
	    			layer.msg("任务审核成功！");
	    			setTimeout('refresh()', 800);
	    		}else{
	    			layer.msg("任务审核失败！");
	    		}
	    	}, 'json');  
	    }
	}
	function refresh(){
		document.forms[0].submit();
	}
	function back(){
		var lname = $("input[name='lname']").val();
		var deptids = $("input[name='deptids']").val();
		var param="";
		if(deptids!=0 && lname.length>0){
			param += "?deptids=" + deptids;
			param += "&lname=" + lname;
		}
		window.location = 'deptquery.php' + param;
	}
	$(function(){
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
            	var did = $("#did").val();
            	
            	if(!page)
            		page = 0;
            	var param={
            		'state': state,
            		'type': type,
            		'target': target,
            		'page': page,
            		'did' : did
            	}
            	$.post('../taizhang/taskrecvmanager.php?do=querynextpage', param,function(res){
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
		//获取上一次加载列表中最后一个总体任务id
		var pregeneraltask = $("#h_pregeneraltask").val();
		var page = $("#h_page").val();
		var index = (parseInt(page)-1)*15;
		//保存当前列表中最后一个总体任务id
		$("#h_pregeneraltask").val(data[data.length-1]['gtaskid']);
		for(var i=0; i<data.length; i++){
			if(pregeneraltask != data[i]['gtaskid']){
				var generaltaskTR = $('<tr height="35px"></tr>').appendTo($("#cons"));
				$('<td colspan="12" class="table_title"></td>').text(data[i]['gtask']).appendTo(generaltaskTR);
			}        
			for(var j=0; j<data[i]['proj'].length; j++){
				index++;
				var trObj = $("<tr></tr>").appendTo($("#cons"));
				var rows = data[i]['proj'][j]['rowspan'];
				var stages = data[i]['proj'][j]['stage'];
				var sdates = data[i]['proj'][j]['sdate'];
				var edates = data[i]['proj'][j]['edate'];
				var state = data[i]['proj'][j]['status'];
				var state_txt = "完成";
				if(state == 1)
					state_txt = "未完成";
					
				$("<td></td>").text(index).attr('rowspan', rows).appendTo(trObj);
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
				var hidObj = $("<input type='hidden'>").attr('id', 'h_header'+index).val(data[i]['proj'][j]['header_l']);
				var divlinkObj = $("<div class='link'></div>");
				var spanObj = $("<span></span>").attr('onclick', 'show_header('+index+');').text('查看全部');
				$(spanObj).appendTo(divlinkObj);
				$(hidObj).appendTo(divlinkObj);
				$("<td style='text-align:left;'></td>").append(data[i]['proj'][j]['header_s']).append(divlinkObj).attr('rowspan', rows).appendTo(trObj);
				//feedback
				var fbTD = $("<td style='text-align:left;'></td>").attr('rowspan', rows).appendTo(trObj);
				var divObj = $("<div class='pic'></div>").appendTo(fbTD);
				var aObj = $("<a target='_blank'></a>").attr('href', '../taizhang/picshow.php?taskid='+data[i]['proj'][j]['taskid']).appendTo(divObj);
				$("<img src='../img/f1.png' style='width:30px; height: 30px;'>").appendTo(aObj);
				var html = '已反馈：<span class="link" onclick="show_feedback_yes('+index+')">'+data[i]['proj'][j]['feedback']['y_cnt']+'</span><br>'
						 + '未反馈：<span class="link" onclick="show_feedback_no('+index+')">'+data[i]['proj'][j]['feedback']['n_cnt']+'</span>'
						 + '<input type="hidden" id="h_feedback_yes'+index+'" value="'+data[i]['proj'][j]['feedback']['f_yes']+'" >'
						 + '<input type="hidden" id="h_feedback_no'+index+'" value="'+data[i]['proj'][j]['feedback']['f_no']+'" >';
				$(fbTD).append(html);
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
		
		$.post("../taizhang/taskrecvmanager.php?do=getfeedbackbyid",{'ids':headers}, function(res){
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
</script>