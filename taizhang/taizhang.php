<?php
include_once('../mysql.php');
header("Content-type:text/html;charset=utf-8");
//办结状态
define('COMPLETE_ARR',"return array('1'=>'未办结','2'=>'办结申请中','3'=>'办结申请被驳回', '4'=>'已办结');");
$complete_arr = eval(COMPLETE_ARR);
//台账类型
define('TASK_TYPE',"return array('1'=>'重点工作','2'=>'重大项目','3'=>'市长台账','4'=>'领导批示','5'=>'会议纪要','6'=>'建议提案','7'=>'舆情监控','8'=>'民生工程','9'=>'中央项目');");
$task_type = eval(TASK_TYPE);
//反馈类型
define('BACK_TYPE',"return array('1'=>'不用报','2'=>'月报','3'=>'季报','4'=>'双月','5'=>'周报','6'=>'定期报');");
$back_type = eval(BACK_TYPE);
//评价类型
define('COMMENT_TYPE',"return array('1'=>'未完成','2'=>'完成');");
$comment_type = eval(COMMENT_TYPE);
//台账状态
define('TASK_STATUS',"return array('0'=>'删除','1'=>'登记','2'=>'立项','3'=>'转办','4'=>'接收', '5'=>'已办结');");
$task_status = eval(TASK_STATUS);

//文件上传的父目录
define('BASEDIR_REPORT', '/government/upload/report/');

mainfunc();

function mainfunc(){
	if(isset($_GET['do'])){
		$do = trim($_GET['do']);//pager_taskmanage
		//$where = trim($_POST['where']);
		switch($do){
			case "apply"://办结申请
				session_start();
				$deptid = $_SESSION['userDeptID'];
				$userid = $_SESSION['userID'];
				$applicant = trim($_POST['applicant']);
				$apply_time = trim($_POST['apply_time']);
				$apply_content = trim($_POST['apply_content']);
				$id = trim($_POST['taskid']);
				//$index = trim($_POST['index']);
				$report_url = "";
				if (isset($_FILES["file"]) && $_FILES["file"]["size"] > 0){
					if ($_FILES["file"]["error"] > 0){
						echo "Error: " . $_FILES["file"]["error"] . "<br />";
					}else{
						$relativeDir = basedir_report.$deptid.'/';//相对于网站根目录的路径
						$uploadDir = $_SERVER['DOCUMENT_ROOT'].$relativeDir;//文件上传的目录
						if(!file_exists($uploadDir)){
							mkdir($uploadDir, 0777, true);
						}
						$filename = date("YmdHis"). sprintf('%03d%03d', $userid, 0) . $_FILES["file"]["name"];
						$gbkname = iconv("UTF-8", "gb2312", $filename);//中文目录需要转码
						if (file_exists($uploadDir . $gbkname)){
							echo $_FILES["file"]["name"] . " already exists. ";
						}else{
							move_uploaded_file($_FILES["file"]["tmp_name"], $uploadDir . $gbkname);
							$report_url = $relativeDir . $filename;
						}
					}
				}
				$result = add_apply($id, $applicant, $apply_time, $apply_content, $report_url);
				if($result){
					echo $result; 
				}
				break;
			case "apply_back"://办结申请被驳回
				$id = $_POST['id'];
				$backleader = $_POST['backleader'];
				$backtime = $_POST['backtime'];
				$backreason = $_POST['backreason'];
				$result = add_apply_back_status($id, $backleader, $backtime, $backreason);
				if($result){
					return true;
				}
				break;
			case "complete"://任务办结
				$taskid = $_POST['taskid'];
				$completeleader = $_POST['completeleader'];
				$completetime = $_POST['completetime'];
				$index = $_POST['index'];
				$result = update_task_apply_complete($taskid, $completeleader, $completetime);
				echo $result;
				break;
			case "cartogram"://统计图表
				$type = $_POST['type'];
				$tjfxTime = $_POST['tjfxTime'];
				$itemtype = isset($_POST['itemtype']) ? $_POST['itemtype'] : "";
				if($type == 'total'){
					$result = get_task_cartogram($tjfxTime, $itemtype);
				}else{
					$result = get_uncomplete_task($type, $tjfxTime);
				}
				echo json_encode($result);
				break;
			case "remind"://首页提醒
				$user = $_POST['deptid'];
				$result = get_remind_count($user);
				echo $result;
				break;
			/*case "page"://分页
				$page = isset($_POST['page']) ? $_POST['page'] : 0;//序号
				$g = isset($_POST['g']) ? $_POST['g'] : 0;//总体任务
				$order = isset($_POST['order']) ? $_POST['order'] : 0;//开始序号
				$type = isset($_POST['type']) ? $_POST['type'] : 0;//台账类型
				$target = isset($_POST['target']) ? $_POST['target'] : "";//台账类型
				$where = "";
				if($type != 0){
					$where .= " and type = " . $type; 
				}
				if($target != ""){
					$where .= " and target like '%" . $target . "%'";
				}
				$result = get_task_register_list($g, $page, $order, $where);
				echo json_encode($result);
				break;*/
			case "task_apply"://办结申请分页
				$deptid = $_POST['deptid'];
				$page = $_POST['page'];
				$where = $_POST['where'];
				$result = get_task_apply_by_page($page, $deptid, $where);
				echo $result;
				break;
			case "task_complete"://办结完结分页
				$deptid = $_POST['deptid'];
				$page = $_POST['page'];
				$where = $_POST['where'];
				$result = get_task_complete_by_page($page, $deptid, $where);
				echo $result;
				break;
			case "delete"://删除台账
				$id = isset($_POST['id']) ? $_POST['id'] : "";
				$result = delete_task_by_id($id);
				echo $result;
				break;
			case "attach"://上传附件
				session_start();
				$deptid = $_SESSION['userDeptID'];
				$userid = $_SESSION['userID'];
				$taskid = isset($_POST['taskid']) ? $_POST['taskid'] : "";
				$report_url = "";
				if (isset($_FILES["attachfile"]) && $_FILES["attachfile"]["size"] > 0){
					if ($_FILES["attachfile"]["error"] > 0){
						echo "Error: " . $_FILES["attachfile"]["error"] . "<br />";
					}else{
						$relativeDir = BASEDIR_REPORT . $deptid . '/';//相对于网站根目录的路径
						$uploadDir = $_SERVER['DOCUMENT_ROOT'] . $relativeDir;//文件上传的目录
						if(!file_exists($uploadDir)){
							mkdir($uploadDir, 0777, true);
						}
						$filename = date("YmdHis"). $_FILES["attachfile"]["name"];
						$gbkname = iconv("UTF-8", "gb2312", $filename);//中文目录需要转码
						if (file_exists($uploadDir . $gbkname)){
							echo $_FILES["attachfile"]["name"] . " already exists. ";
						}else{
							move_uploaded_file($_FILES["attachfile"]["tmp_name"], $uploadDir . $gbkname);
							$report_url = $relativeDir . $filename;
						}
					}
				}
				$result = upload_attach_file($taskid, $_FILES["attachfile"]["name"], $report_url);
				echo $result;
				break;
			case "paste"://复制台账
				session_start();
				$id = $_POST['taskid'];
				$username = $_POST['username'];
				$result = paste_task($id, $username);
				echo $result;
				break;
			default:
				break;
		}
	}
}

//复制台账
function paste_task($id, $userName){
	$html = "";//返回结果
	$time = date('Y-m-d H:i:s ');

	$mLink = new mysql;
	$sql = "select generaltaskid, type, target, title, fromdate, handledate, investment from task where id = " . $id;
	$res = $mLink->getRow($sql);
	if($res){
		$title = $res['title'] . "复制";
		if($res['fromdate'] != ""){
			$sql = "insert into task (generaltaskid, type, target, title, fromdate, handledate, investment, creater, createtime) values (" . $res['generaltaskid'] . "," . $res['type'] . ",'" . $res['target'] . "','" . $res['title'] . "','" . $res['fromdate'] . "','" . $res['handledate'] . "','" . $res['investment'] . "','" . $userName . "','" . $time . "')";
		}else{
			$sql = "insert into task (generaltaskid, type, target, title, investment, creater, createtime) values (" . $res['generaltaskid'] . "," . $res['type'] . ",'" . $res['target'] . "','" . $title . "','" . $res['investment'] . "','" . $userName . "','" . $time . "')";
		}
		
		$new_task_id = $mLink->insert($sql);

		if($new_task_id){
			$sql = "select stage, startdate, enddate from progress where taskid = " . $id;
			$progressList = $mLink->getAll($sql);

			if(is_array($progressList) && count($progressList) > 0){
				$sql = "insert into progress (taskid, stage, startdate, enddate, creater, createtime) values (" . $new_task_id . ",'" . $progressList[0]['stage'] . "','" . $progressList[0]['startdate'] . "','" . $progressList[0]['enddate'] . "','" . $userName . "','" . $time . "')";
				$progress_id = $mLink->insert($sql);

				$size = sizeof($progressList);
				if($progress_id){
					$html .= '<tr class="alternate_line1" style="line-height:100%;" id="task_' . $new_task_id . '">'
						. '<td rowspan="' . $size . '" align="center" onclick="openNewWindow(\'handle.php?name=edit.php&id=' . $new_task_id . '\', 0, 1)" style="cursor:pointer;color:red;">New</td>'
						. '<td rowspan="' . $size . '" style="text-align:left;" name="target" onclick="do_edit(\'target' . $new_task_id . '\',\'' . $res['target'] . '\',1)" id="target' . $new_task_id . '"><div class="resizable">' . $res['target'] . '</div></td>'
						. '<td rowspan="' . $size . '" style="text-align:left;" onclick="do_edit(\'title' . $new_task_id . '\',\'' . $res['title'] . '\',1)" name="title" id="title' . $new_task_id . '"><div class="resizable">' . $res['title'] . '复制</div></td>' 
						. '<td rowspan="' . $size . '" align="center" onclick="do_edit(\'investment' . $new_task_id . '\',\'' . $res['investment'] . '\',1)" name="investment" id="investment' . $new_task_id . '"><div class="resizable">'. $res['investment'] . '</div></td>'
						. '<td style="text-align:left;" onclick="do_edit(\'stage' . $progress_id . '\',\'' . $progressList[0]["stage"] . '\',2)" name="stage" id="stage' . $progress_id . '"><div class="resizable">'. $progressList[0]["stage"] . '</div></td>'
						. '<td align="center"><input onclick="do_edit2(this)" class="text_area_item" type="text" name="startdate" value="'. $progressList[0]["startdate"] . '" onblur="do_leave2(this,' . $progress_id . ',\'startdate\')" /></td>'
						. '<td align="center"><input onclick="do_edit2(this)" class="text_area_item" type="text" name="enddate" value="'. $progressList[0]["enddate"] . '" onblur="do_leave2(this,' . $progress_id . ',\'enddate\')" /></td>' 
						. '<td rowspan="' . $size . '" id="attach_' . $new_task_id . '"><input value="上传附件" style="cursor:hand" class="button1" type="button" onclick="hch.open_attach(' . $new_task_id . ')"><p class="copy"><input value="复制" style="cursor:hand" class="button1" type="button" onclick="hch.copy_task(' . $new_task_id . ')"></p></td>'
						. '</tr>';
				}else{
					$sql = "delete from task where taskid = " . $new_task_id;
					$mLink->update($sql);
					return "";
				}
				
				if($size > 1){
					for($c=1; $c<$size; $c++){
						$sql = "insert into progress (taskid, stage, startdate, enddate, creater, createtime) values (" . $new_task_id . ",'" . $progressList[$c]['stage'] . "','" . $progressList[$c]['startdate'] . "','" . $progressList[$c]['enddate'] . "','" . $userName . "','" . $time . "')";
						$progress_id = $mLink->insert($sql);

						if($progress_id){
							$html .= '<tr class="alternate_line1" style="line-height:100%;">'
							. '<td style="text-align:left;" onclick="do_edit(\'stage' . $progress_id . '\',\'' . $progressList[$c]["stage"] . '\',2)" name="stage" id="stage' . $progress_id . '"><div class="resizable">'. $progressList[$c]["stage"] . '</div></td>'
							. '<td align="center"><input onclick="do_edit2(this)" class="text_area_item" type="text" name="startdate" value="'. $progressList[$c]["startdate"] . '" onblur="do_leave2(this,' . $progress_id . ',\'startdate\')" /></td>'
							. '<td align="center"><input onclick="do_edit2(this)" class="text_area_item" type="text" name="enddate" value="'. $progressList[$c]["enddate"] . '" onblur="do_leave2(this,' . $progress_id . ',\'enddate\')" /></td>' . '</tr>';
						}else{
							$sql = "delete from task where id = " . $new_task_id;
							$mLink->update($sql);
							$sql = "delte from progress where taskid = " . $new_task_id;
							$mLink->update($sql);
							return "";
						}
					}
				}
			}else{
				$sql = "insert into progress (taskid, creater, createtime) values (" . $new_task_id . ",'" . $userName . "','" . $time . "')";
				$new_progress_id = $mLink->insert($sql);

				if($new_progress_id){
					$html .= '<tr class="alternate_line1" style="line-height:100%;" id="task_' . $new_task_id . '">'
						. '<td align="center" onclick="openNewWindow(\'handle.php?name=edit.php&id=' . $new_task_id . '\', 0, 1)" style="cursor:pointer">' . $i . '</td>'
						. '<td style="text-align:left;" name="target" onclick="do_edit(\'target' . $new_task_id . '\',\'' . $value['target'] . '\',1)" id="target' . $new_task_id . '"><div class="resizable">' . $value['target'] . '</div></td>'
						. '<td style="text-align:left;" onclick="do_edit(\'title' . $new_task_id . '\',\'' . $value['title'] . '\',1)" name="title" id="title' . $new_task_id . '"><div class="resizable">' . $value['title'] . '</div></td>' 
						. '<td align="center" onclick="do_edit(\'investment' . $new_task_id . '\',\'' . $value['investment'] . '\',1)" name="investment" id="investment' . $new_task_id . '"><div class="resizable">'. $value['investment'] . '</div></td>'
						. '<td style="text-align:left;" onclick="do_edit(\'stage' . $new_progress_id . '\',\'\',2)" name="stage" id="stage' . $new_progress_id . '"><div class="resizable"></div></td>'
						. '<td align="center"><input onclick="do_edit2(this)" class="text_area_item" type="text" name="startdate" value="" onblur="do_leave2(this,' . $new_progress_id . ',\'startdate\')" /></td>'
						. '<td align="center"><input onclick="do_edit2(this)" class="text_area_item" type="text" name="enddate" value="" onblur="do_leave2(this,' . $new_progress_id . ',\'enddate\')" /></td>' 
						. '<td id="attach_' . $new_task_id . '"><input value="上传附件" style="cursor:hand" class="button1" type="button" onclick="hch.open_attach(' . $new_task_id . ')"><p class="copy"><input value="复制" style="cursor:hand" class="button1" type="button" onclick="hch.copy_task(' . $new_task_id . ')"></p><p class="copy"><input value="粘贴" style="cursor:hand" class="button1" type="button" onclick="hch.paste_task(' . $new_task_id . ')"></p></td>'
						. '</tr>';
				}else{
					$sql = "delete from task where id = " . $new_task_id;
					$mLink->update($sql);
					$sql = "delte from progress where taskid = " . $new_task_id;
					$mLink->update($sql);
					return "";
				}
			}
		}else{
			return "";
		}
	}

	return $html;
}

//上传附件
function upload_attach_file($taskid, $filename, $report_url){
	$mLink = new mysql;
	$sql = "insert into attachment (taskid, attachName, attachUrl) values (" . $taskid . ", '" . $filename . "', '" . $report_url . "')";
	$res = $mLink->update($sql);
	if($res){
		return true;
	}
}

function get_task_apply_count($deptid, $where){
	$mLink = new mysql;
	$sql = "select a.id as taskid, b.id as id, a.type as type, a.target as target, a.title as title, a.handledate as handledate, a.investment as investment, a.onbacktime as onbacktime, a.regbacktype as regbacktype, b.recvdate as recvdate, b.is_complete as is_complete from task a left join taskrecv b on a.id = b.taskid where a.status > 0 and b.status = 1 and b.deptid = " . $deptid . $where . " order by a.id";
	$taskArr = $mLink->getAll($sql);
	return count($taskArr);
}
function get_task_complete_count($deptid, $where){
	$mLink = new mysql;
	$sql = "select a.*, b.remark, b.isover from task a left join (select * from taskreview c, (select max(viewtime) as latesttime from taskreview) d where c.viewtime = d.latesttime) b on a.id = b.taskid where a.status > 1" . $where . " order by a.id";
	$taskArr = $mLink->getAll($sql);
	return count($taskArr);
}
function get_task_apply_by_page($page, $deptid, $where){
	$mLink = new mysql;
	$count = 0;
	$html = "";

	$num = ($page-1)*10;
	$sql = "select a.id as taskid, b.id as id, a.type as type, a.target as target, a.title as title, a.handledate as handledate, a.investment as investment, a.onbacktime as onbacktime, a.regbacktype as regbacktype, b.recvdate as recvdate, b.is_complete as is_complete from task a left join taskrecv b on a.id = b.taskid where a.status > 0 and b.status = 1 and b.deptid = " . $deptid . $where . " order by a.id limit " . $num . ",10";

	$taskArr = $mLink->getAll($sql);
	$task = array();
	if(is_array($taskArr) && count($taskArr) > 0){
		foreach($taskArr as $t){
			$num++;
			$html .= '<tr class="alternate_line1" style="line-height:100%;">';
			$sql2 = "select * from progress where taskid = " . $t['taskid'] . " order by id";
			$progressArr = $mLink->getAll($sql2);
			$size = count($progressArr);
			$is_complete = '';
			if($t['is_complete'] == 1){
				$is_complete = '<font color="red">未办结</font>';
			}else if($t['is_complete'] == 2){
				$is_complete = '办结申请中';
			}else if($t['is_complete'] == 3){
				$is_complete = '办结申请被驳回';
			}else{
				$is_complete = '已办结';
			}
			$back = '';
			if($t['regbacktype'] == 3){
				$back = $taskdetail['onbacktime'];
			}else if($t['regbacktype'] == 1){
				$back = "季报";
			}else if($t['regbacktype'] == 2){
				$back = "月报";
			}else if($t['regbacktype'] == 4){
				$back = "双月";
			}else if($t['regbacktype'] == 5){
				$back = "周报";
			}
			if($size == 0){
				$html .= '<td align="center"><div class="resizable">' . $num . '</div></td>'
						. '<td align="center"><div class="resizable">' . $t['target'] . '</div></td>'
						. '<td align="center"><div class="resizable">' . $t['title'] . '</div></td>'
						. '<td align="center"><div class="resizable">'. $t['investment'] . '</div></td>'
						. '<td align="center"><div class="resizable"></div></td>'
						. '<td align="center"><div class="resizable"></div></td>'
						. '<td align="center"><div class="resizable"></div></td>'
						. '<td align="center"><div class="resizable">' . $back . '</div></td>'
						. '<td align="center"><div class="resizable">' . $t['handledate'] . '</div></td>'
						. '<td align="center"><div class="resizable">' . $t['recvdate'] . '</div></td>'
						. '<td align="center"><div class="resizable">' . $is_complete . '</div></td>'
						. '<td align="center"><input type="button" value="申请" onclick="hch.open_apply(' . $t['id'] . ')" style="cursor:hand" class="button1" /></td></tr>';;
			}else{
				$html .= '<td rowspan="' . $size . '" align="center" width="4%" ><div class="resizable">' . $num . '</div></td>'
						. '<td rowspan="' . $size . '" align="center" ><div class="resizable">' . $t['target'] . '</div></td>'
						. '<td rowspan="' . $size . '" align="center"><div class="resizable">' . $t['title'] . '</div></td>'
						.  '<td rowspan="' . $size . '" align="center"><div class="resizable">'. $t['investment'] . '</div></td>'
						. '<td align="center"><div class="resizable">'. $progressArr[0]["stage"] . '</div></td>'
						. '<td align="center"><div class="resizable">'. $progressArr[0]["startdate"] . '</div></td>'
						. '<td align="center"><div class="resizable">'. $progressArr[0]["enddate"] . '</div></td>'
						. '<td rowspan="' . $size . '" align="center" ><div class="resizable">' . $back . '</div></td>'
						. '<td rowspan="' . $size . '" align="center" ><div class="resizable">' . $t['handledate'] . '</div></td>'
						. '<td rowspan="' . $size . '" align="center" ><div class="resizable">' . $t['recvdate'] . '</div></td>'
						. '<td rowspan="' . $size . '" align="center" ><div class="resizable">' . $is_complete . '</div></td>'
						. '<td rowspan="' . $size . '" align="center"><input type="button" value="申请" onclick="hch.open_apply(' . $t['id'] . ')" style="cursor:hand" class="button1" /></td></tr>';
				for($k=1;$k<$size;$k++){
					$html .= '<tr class="alternate_line1" style="line-height:100%;">'
							. '<td align="center"><div class="resizable">' . $progressArr[$k]["stage"] . '</div></td>'
							. '<td align="center"><div class="resizable">' . $progressArr[$k]["startdate"] . '</div></td>'
							. '<td align="center"><div class="resizable">'. $progressArr[$k]["enddate"] . '</div></td>' . '</tr>';
				}

			}
		}
	}else{
		$html .= '<tr class="alternate_line1" style="line-height:100%;"><td colspan="20" align="center"><font size="2">没有符合条件的纪录</font></td></tr>';
	}
			
	return $html;
}

function get_task_complete_by_page($page, $deptid, $where){
	$mLink = new mysql;
	$count = ($page-1)*10;
	$html = "";

	$sql = "select a.*, b.remark, b.isover from task a left join (select * from taskreview c, (select max(viewtime) as latesttime from taskreview) d where c.viewtime = d.latesttime) b on a.id = b.taskid where a.status > 1" . $where . " order by a.id limit " . $count . ",10";
	$taskArr = $mLink->getAll($sql);
	if(is_array($taskArr) && count($taskArr) > 0){
		foreach($taskArr as $t){
			$count++;
			$sql2 = "select * from progress where taskid = " . $t['id'] . " order by id";
			$progressArr = $mLink->getAll($sql2);
			
			if($t['isover'] == 1){
				$is_over = "未完成";
			}else if($t['isover'] == 2){
				$is_over = "完成";
			}else{
				$is_over = "";
			}
			$dept = '';
			$main = '<b>牵头单位：</b>';
			$sub = '<br><b>责任单位：</b>';
			$sql3 = "select a.ishead as ishead, b.deptName as deptName from taskrecv a, dept b where a.taskid = " . $t['id'] . " and a.deptid = b.deptId";
			$deptdArr = $mLink->getAll($sql3);
			$count1 = 0;
			$count2 = 0;
			if(is_array($deptdArr) && count($deptdArr) > 0){
				foreach($deptdArr as $d){
					if($d['ishead'] == 1){
						if($count1 == 0){
							$main .= $d['deptName'];
						}else{
							$main .= ',' . $d['deptName'];
						}
						$count1++;
					}else{
						if($count2 == 0){
							$sub .= $d['deptName'];
						}else{
							$sub .= ',' . $d['deptName'];
						}
						$count2++;
					}
				}
			}
			$dept = $main . $sub;
			if($t['status'] < 5){
				$is_complete = '<font color="red">未办结</font>';
			}else{
				$is_complete = "已办结";
			}
			if(is_array($progressArr) && count($progressArr) > 0){
				$size = count($progressArr);
				$html .= '<tr class="alternate_line1" style="line-height:100%;">'
						. '<td rowspan="' . $size . '" align="center"><div class="resizable">' . $count . '</div></td>'
						. '<td rowspan="' . $size . '" align="center" ><div class="resizable">' . $t['target'] . '</div></td>'
						. '<td rowspan="' . $size . '" align="center"><div class="resizable">' . $t['title'] . '</div></td>'
						. '<td rowspan="' . $size . '" align="center"><div class="resizable">'. $t['investment'] . '</div></td>'
						. '<td align="center"><div class="resizable">'. $progressArr[0]["stage"] . '</div></td>'
						. '<td align="center"><div class="resizable">'. $progressArr[0]["startdate"] . '</div></td>'
						. '<td align="center"><div class="resizable">'. $progressArr[0]["enddate"] . '</div></td>'
						. '<td rowspan="' . $size . '" align="left" ><div class="resizable">' . $dept . '</div></td>'
						. '<td rowspan="' . $size . '" align="center" ><div class="resizable">' . $t['remark'] . '</div></td>'
						. '<td rowspan="' . $size . '" align="center" ><div class="resizable">' . $is_over . '</div></td>'
						. '<td rowspan="' . $size . '" align="center" ><div class="resizable">' . $is_complete . '</div></td>'
						. '<td rowspan="' . $size . '" align="center"><input type="button" value="办结" onclick="hch.open_apply(' . $t['id'] . ')" style="cursor:hand" class="button1" /></td>'. '</tr>';
				if($size > 1){
					for($k=1;$k<$size;$k++){
						$html .= '<tr class="alternate_line1" style="line-height:100%;">'
								. '<td align="center"><div class="resizable">' . $progressArr[$k]["stage"] . '</div></td>'
								. '<td align="center"><div class="resizable">' . $progressArr[$k]["startdate"] . '</div></td>'
								. '<td align="center"><div class="resizable">'. $progressArr[$k]["enddate"] . '</div></td>' . '</tr>';
					}
				}
			}else{
				$html .= '<tr class="alternate_line1" style="line-height:100%;">'
						. '<td align="center"><div class="resizable">' . $count . '</div></td>'
						. '<td align="center"><div class="resizable">' . $t['target'] . '</div></td>'
						. '<td align="center"><div class="resizable">' . $t['title'] . '</div></td>'
						. '<td align="center"><div class="resizable">'. $t['investment'] . '</div></td>'
						. '<td align="center"><div class="resizable"></div></td>'
						. '<td align="center"><div class="resizable"></div></td>'
						. '<td align="center"><div class="resizable"></div></td>'
						. '<td align="center" <div class="resizable">' . $dept . '</div></td>'
						. '<td align="center"><div class="resizable">' . $t['remark'] . '</div></td>'
						. '<td align="center"><div class="resizable">' . $is_over . '</div></td>'
						. '<td align="center"><div class="resizable">' . $is_complete . '</div></td>'
						. '<td align="center"><input type="button" value="办结" onclick="hch.open_apply(' . $t['id'] . ')" style="cursor:hand" class="button1" /></td>'. '</tr>';
			}		
		}
	}else{
		$html .= '<tr class="alternate_line1" style="line-height:100%;"><td colspan="12" align="center"><font size="2">没有符合条件的纪录</font></td></tr>';
	}
	return $html;
}

//进度提醒
function get_remind_count($deptid){
	$mLink = new mysql;
	$sql = "select a.weidu, b.jieshou, c.tuihui from (select count(*) as weidu from taskrecv where status = 0 and deptid = " . $deptid . ") a, (select count(*) as jieshou from taskrecv where status = 1 and deptid = " . $deptid .") b, (select count(*) as tuihui from taskrecv where status = 2 and deptid = " . $deptid . ") c";
	$res = $mLink->getRow($sql);
	$arr = array(
		"weidu"		=>	$res["weidu"],
		"jieshou"	=>	$res["jieshou"],
		"tuihui"	=>	$res["tuihui"]);	
	return json_encode($arr);
}

//获得台账统计
function get_task_cartogram($time, $itemtype){
	$mLink = new mysql;
	$where = "";
	if($itemtype != ""){
		$where .= " and type = " . $itemtype;
	}
	//$sql = "select a.total, b.uncomplete, c.complete from (select count(*) as total from task where status > 0 and DATE_FORMAT(handledate, '%Y') = '" . $time . "'" . $where . ") a, (select count(*) as uncomplete from task where status > 0 and status < 5 and DATE_FORMAT(handledate, '%Y') = '" . $time . "'" . $where . ") b, (select count(*) as complete from task where status = 5 and DATE_FORMAT(handledate, '%Y') = '" . $time . "'" . $where . ") c";
	$sql = "select a.total, b.uncomplete, c.complete from (select count(*) as total from task where status > 0" . $where . ") a, (select count(*) as uncomplete from task where status > 0 and status < 5" . $where . ") b, (select count(*) as complete from task where status = 5" . $where . ") c";
	$res = $mLink->getRow($sql);
	if($res){
		$res = array(
				"total"			=>		$res['total'],
				"uncomplete"	=>		$res['uncomplete'],
				"complete"		=>		$res['complete']);
		return $res;
	}
}

//获得未完成台账统计
function get_uncomplete_task($type, $time){
	$mLink = new mysql;
	$where = "";
	if($type == "uncomplete"){
		$where .= " and status > 0 and status < 5";
	}
	if($type == "complete"){
		$where .= " and status = 5";
	}
	//$sql = "select count(*) as count, type as tasktype from task where DATE_FORMAT(handledate, '%Y') = '" .$time . "'" . $where . " group by type order by id";
	$sql = "select count(*) as count, type as tasktype from task where 1=1" . $where . " group by type order by id";
	$res = $mLink->getAll($sql);
	$arr = array();
	if($res){
		foreach($res as $v){
			$arr[] = array(
				"count"			=>		$v['count'],
				"tasktype"		=>		$v['tasktype']
			);
		}
	}
	return $arr;
}
//添加办结申请
function add_apply($id, $applicant, $time, $content, $url){
	$mLink = new mysql;
	$sql = "update taskrecv set applicant = '" . $applicant . "', apply_time = '" . $time . "', apply_content = '" . $content . "', report_url = '" . $url . "', is_complete = 2 where id = " . $id;
	$res = $mLink->update($sql);
	if($res){
		return true;
	}
}

//获得办结申请详情
function get_task_apply_detail($id){
	$mLink = new mysql;
	$sql = "select * from taskrecv where id = " . $id;
	$res = $mLink->getRow($sql);
	if($res){
		return json_encode($res);
	}
}

//获得全部办结申请
function get_all_task_apply($taskid){
	$mLink = new mysql;
	$sql = "select a.*, b.deptName as deptName, c.regbacktype as regbacktype, c.onbacktime as onbacktime from taskrecv a left join dept b on a.deptid = b.deptId left join task c on c.status > 0 and c.id = a.taskid where a.taskid = " . $taskid . " order by a.id asc";
	$res = $mLink->getAll($sql);
	if($res){
		return json_encode($res);
	}
}
//更新办结申请结果——任务办结
function update_task_apply_complete($taskid, $completeleader, $completetime){
	$mLink = new mysql;
	$sql1 = "update task set status = 5, completeleader = '" . $completeleader . "', completetime= '" . $completetime . "' where id = " .$taskid;
	$res = $mLink->update($sql1);

	$sql2 = "update taskrecv set is_complete = 4 where taskid = " . $taskid;
	$res2 = $mLink->update($sql2);
	if($res){
		return true;
	}
}

//更新办结申请结果——驳回
function add_apply_back_status($id, $leader, $time, $reason){
	$mLink = new mysql;
	$sql = "update taskrecv set is_complete = 3, backleader = '" . $leader . "', backtime = '" . $time . "', backreason = '" . $reason . "' where id = " . $id;
	$res = $mLink->update($sql);
	if($res){
		return true;
	}
}
 
//获得登记台账列表
function get_djtz($type, $target, $generaltaskid) {
	$mLink = new mysql;
	$where = "";
	if($type != 0){
		$where .= " and type=" . $type;
	}
	if($target != ""){
		$where .= " and target like '%" . $target ."%'";
	}
	if($generaltaskid != ""){
		$where .= " and generaltaskid = " . $generaltaskid . "";
	}
	$sql = "select * from task where status > 0" . $where . " order by id";
	$res = $mLink->getAll($sql);
	return  json_encode($res);
}

//查询登记台账详情
function get_djtz_detail($itemid){
	$mLink = new mysql;
	$res = $mLink->getRow("select * from task where id = " . $itemid);
	return  json_encode($res);
}

//查询工作标准
function get_progress_list($id){
	$mLink = new mysql;
	$res = $mLink->getAll("select * from progress where status > 0 and taskid = " . $id . " order by id asc");
	return  json_encode($res);
}

//查询工作标准id最大值
function get_max_progress_id(){
	$mLink = new mysql;
	$res = $mLink->getRow("select max(id) as count from progress");
	return json_encode($res);
}

//查询督查通知详情
function get_ggl_detail($id){
	$mLink = new mysql;
	$res = $mLink->getRow("select * from ggl where id = " . $id);
	return  json_encode($res);
}

//查询工作责任制状态
function get_work_state(){
	$mLink = new mysql;
	$res = $mLink->getAll("select * from codemap where type = '工作责任制状态'");
	return  json_encode($res);
}

//获得总体任务
function get_general_task(){
	$mLink = new mysql;
	$res = $mLink->getAll("select id, name from generaltask order by id");
	return  json_encode($res);
}

//获得办结申请列表
function get_task_apply_list($deptid, $where){
	$mLink = new mysql;
	$generalTaskList = json_decode(get_generaltask_list(), true);
	$count = 0;
	$res = array();
	if(is_array($generalTaskList) && count($generalTaskList) > 0){
		foreach($generalTaskList as $g){
			$sql2 = "select a.id as taskid, b.id as id, a.type as type, a.target as target, a.title as title, a.handledate as handledate, a.investment as investment, a.onbacktime as onbacktime, a.regbacktype as regbacktype, b.recvdate as recvdate, b.is_complete as is_complete from task a left join taskrecv b on a.id = b.taskid where a.status > 0 and b.status = 1 and b.deptid = " . $deptid . " and a.generaltaskid = " . $g['id'] . $where . " order by a.id";

			$taskArr = $mLink->getAll($sql2);
			$task = array();
			foreach($taskArr as $t){
				$sql3 = "select * from progress where taskid = " . $t['taskid'] . " order by id";
				$progressArr = $mLink->getAll($sql3);
				$progress = array();
				foreach($progressArr as $p){
					$progress[] = array(
							"stage"		=>		$p['stage'],
							"startdate"	=>		$p['startdate'],
							"enddate"	=>		$p['enddate']);
				}
				$task[] = array(
					"taskdetail"		=>		$t,
					"progressList"		=>		$progress);
			}
			$count += sizeof($task);
			$res[] = array(
					"generaltask"		=>		$g['name'],
					"task"				=>		$task);
		}
	}
	$res_arr = array(
			"count"		=>		$count,
			"res"		=>		$res);
	return json_encode($res_arr);
}

//获得任务办结列表
function get_task_complete_list($where){
	$mLink = new mysql;
	$generalTaskList = json_decode(get_generaltask_list(), true);
	$count = 0;
	$res = array();
	if(is_array($generalTaskList) && count($generalTaskList) >  0){
		foreach($generalTaskList as $g){
			$sql2 = "select a.*, b.remark, b.isover from task a left join (select * from taskreview c, (select max(viewtime) as latesttime from taskreview) d where c.viewtime = d.latesttime) b on a.id = b.taskid where a.status > 1 and a.generaltaskid = " . $g['id'] . $where . " order by a.id";
			$taskArr = $mLink->getAll($sql2);
			$task = array();
			foreach($taskArr as $t){
				$sql3 = "select * from progress where taskid = " . $t['id'] . " order by id";
				$progressArr = $mLink->getAll($sql3);
				$progress = array();
				foreach($progressArr as $p){
					$progress[] = array(
							"stage"		=>		$p['stage'],
							"startdate"	=>		$p['startdate'],
							"enddate"	=>		$p['enddate']);
				}
				$sql4 = "select a.ishead as ishead, b.deptName as deptName from taskrecv a, dept b where a.taskid = " . $t['id'] . " and a.deptid = b.deptId";
				$deptdArr = $mLink->getAll($sql4);
				$dept = array();
				foreach($deptdArr as $d){
					$dept[] = array(
						"ishead"		=>		$d['ishead'],
						"deptName"		=>		$d['deptName']);
				}
				$task[] = array(
					"taskdetail"		=>		$t,
					"progressList"		=>		$progress,
					"deptList"			=>		$dept);
			}
			$count += sizeof($task);
			$res[] = array(
					"generaltask"		=>		$g['name'],
					"task"				=>		$task);
		}
	}
	$res_arr = array(
			"count"		=>		$count,
			"res"		=>		$res);
	return json_encode($res_arr);
}

function get_generaltask_num(){
	$mLink = new mysql;
	$sql1 = "select DISTINCT(generaltask) as generaltask from task order by id";
	$generalTaskList = $mLink->getAll($sql1);
	foreach($generalTaskList as $g){
		$sql2 = "select count(*) as count from task where status > 0 and generaltask = '" . $g['generaltask'] . "' order by id";
		$task_count = $mLink->getRow($sql2);
		$res[] = array(
			"generaltask"		=>		$g['generaltask'],
			"task_count"		=>		$task_count['count']);
	}
	return json_encode($res);
}
//get_task_register_list(2 ,30);
//get_task_register_list(0 ,9,9);
function get_task_by_generaltaskid($generaltaskid, $start, $num, $where){
	$mLink = new mysql;
	$sql = "select * from task where status > 0 and generaltaskid = " . $generaltaskid . $where ." order by id LIMIT " . $start . "," .$num;
	
	$taskArr = $mLink->getAll($sql);
	return $taskArr;
}
//get_task_register_list(1,3,6,"");
//获得登记台账列表
function get_task_register_list($g, $page, $order, $where){
	
	$start = $order;//start from
	$num = 5;//total:5
	$mLink = new mysql;
	$generalTaskList = json_decode(get_generaltask_list(), true);//generaltask list
	$html = "";
	$sub = "2";//progress
	$main = "1";//task
	$i = $page*5;
	$total = 0;
	$g_count = $g;//generaltask num
	if($page == 0){
		$html .= '<tr><td width="100%" height="100%" colspan="9" class="table_title">' . $generalTaskList[$g_count]['name'] . '</td></tr>';//输出总体任务
	}
	$task_count = 0;//查到多少条 五条结束
	while(($g_count < count($generalTaskList)) && ($task_count < 5)){
		if($num == 0){
			$page++;
			break;
		}
			
		//echo $generalTaskList[$g_count]['id']."——".$start."——".$num."——".$where;
		$taskArr = get_task_by_generaltaskid($generalTaskList[$g_count]['id'], $start, $num, $where);//查询task条数
		//echo count($taskArr);
		//var_dump($taskArr);
		if(!is_array($taskArr) || count($taskArr) == 0){//结果为空
			if($start == 0 || $num < 5){
				$html .= '<tr class="alternate_line1"><td colspan="9" align="center"><font size="2">没有符合条件的纪录</font></td></tr>';//该条总体任务下没有数据
			}
			$g_count++;//查询下一条总体任务
			//$taskid = array();
			$start = 0;//开始序号设为0
			if($g_count < count($generalTaskList)){
				$html .= '<tr><td width="100%" height="100%" colspan="9" class="table_title">' . $generalTaskList[$g_count]['name'] . '</td></tr>';//输出总体任务
				continue;
			}	
		}else{
			

			/*if($num == 5){
				$page++;
				$start += count($taskArr);//下次查寻从第几条开始
				$num = 5;
			}*/
			
			
			$task_count += count($taskArr);//统计显示了多少行，五条结束
			
			//var_dump($taskArr);
			foreach($taskArr as $t){
				//$html = "";
				$taskid[] = $t['id'];
				$i++;
				//echo "<br>i:".$i;
				$sql3 = "select * from progress where taskid = " . $t['id'] . " order by id";
				$progressArr = $mLink->getAll($sql3);
				//echo count($progressArr);
				if(!is_array($progressArr) || count($progressArr) == 0){
					insert_into_progress($t['id']);
					$progressArr = $mLink->getAll($sql3);
				}
				
				$size = sizeof($progressArr);
				$html .= '<tr class="alternate_line1" style="line-height:100%;">'
						. '<td rowspan="' . $size . '" align="center" onclick="openNewWindow(\'handle.php?name=edit.php&id=' . $t['id'] . '\', 0, 1)">' . $i . '</td>'
						. '<td rowspan="' . $size . '" align="center" ><div class="resizable">' . $t['target'] . '</div></td>'
						. '<td rowspan="' . $size . '" align="center" onclick="do_edit(this,\'' . $t['title'] . '\',1)" name="title" id="title' . $t['id'] . '"><div class="resizable">' . $t['title'] . '</div></td>' 
						. '<td rowspan="' . $size . '" align="center" onclick="do_edit(this,\'' . $t['investment'] . '\',1)" name="investment" id="investment' . $t['id'] . '"><div class="resizable">'. $t['investment'] . '</div></td>'
					. '<td align="center" onclick="do_edit(this,\'' . $progressArr[0]["stage"] . '\',2)" name="stage" id="stage' . $progressArr[0]["id"] . '"><div class="resizable">'. $progressArr[0]["stage"] . '</div></td>'
					. '<td align="center"><input onclick="do_edit2(this)" class="text_area_item" type="text" name="startdate" value="'. $progressArr[0]["startdate"] . '" onblur="do_leave2(this,' . $progressArr[0]["id"] . ',\'startdate\')" /></td>'
					. '<td align="center"><input onclick="do_edit2(this)" class="text_area_item" type="text" name="enddate" value="'. $progressArr[0]["enddate"] . '" onblur="do_leave2(this,' . $progressArr[0]["id"] . ',\'enddate\')" /></td>' . '</tr>';
				if($size > 1){
					for($c=1; $c<$size; $c++){
						$html .= '<tr class="alternate_line1" style="line-height:100%;">'
							. '<td align="center" onclick="do_edit(this,\'' . $progressArr[$c]["stage"] . '\',2)" name="stage" id="stage' . $progressArr[$c]["id"] . '"><div class="resizable">'. $progressArr[$c]["stage"] . '</div></td>'
							. '<td align="center"><input onclick="do_edit2(this)" class="text_area_item" type="text" name="startdate" value="'. $progressArr[$c]["startdate"] . '" onblur="do_leave2(this,' . $progressArr[$c]["id"] . ',\'startdate\')" /></td>'
							. '<td align="center"><input onclick="do_edit2(this)" class="text_area_item" type="text" name="enddate" value="'. $progressArr[$c]["enddate"] . '" onblur="do_leave2(this,' . $progressArr[$c]["id"] . ',\'enddate\')" /></td>' . '</tr>';
					}
				}
				//echo "<".$html.">";
				
			}
			if(count($taskArr) < $num){//查到的条数少于应查的条数，next
				$start = 0;//下次查寻从第几条开始
				$num -= count($taskArr);//下次查寻多少条
				$g_count++;//查询下一条总体任务
				if($g_count < count($generalTaskList)){
					$html .= '<tr><td width="100%" height="100%" colspan="9" class="table_title">' . $generalTaskList[$g_count]['name'] . '</td></tr>';//输出总体任务
				}
				continue;
			}else{
				$start += count($taskArr);//下次查寻从第几条开始
				$page++;
				break;
			}
			//if($task_count >= 5){
			//	$page++;
				/*echo "<br>start:".$start;
				//下次查询多少条
				echo "<br>task_count:".$task_count;
				echo "<br>num:".$num;
				echo "<br>page:".$page;
				echo "<br>g:".$g_count;
				echo "break";*/
			//	break;
			//}
		}
	}
	$res = array(
		"g"		=>		$g_count,
		"page"	=>		$page,
		"start"	=>		$start,
		"total"	=>		$i,
		"html"	=>		$html);

	return $res;
}
function insert_into_progress($taskid){
	$mLink = new mysql;
	$sql = "insert into progress (taskid) value (" . $taskid . ")";
	$res = $mLink->insert($sql);
	if($res){
		return $res;
	}
}
function get_generaltask_list(){
	$mLink = new mysql;
	$sql = "select * from generaltask order by id";
	$res = $mLink->getAll($sql);
	if($res){
		return json_encode($res);
	}
}

//通过id删除task及相关信息
function delete_task_by_id($id){
	$mLink = new mysql;
	$sql = "update task set status = 0 where id = " . $id;
	$res = $mLink->update($sql);
	if($res){
		return true;
	}else{
		return false;
	}
}

//查询台账类型
function get_type_list(){
	$mLink = new mysql;
	$sql = "select id, type, img from type order by id asc";
	$res = $mLink->getAll($sql);
	$mLink->closelink();
	return json_encode($res);
}

