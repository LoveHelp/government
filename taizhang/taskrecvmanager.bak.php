<?php
include_once '../mysql.php';
include_once '../constant.php';
// 每月天数
// $days = array('1'=>31, '2'=>29, '3'=>31, '4'=>30, '5'=>31, '6'=>30, '7'=>31, '8'=>31, '9'=>30, '10'=>'31', '11'=>30, '12'=>31);
define('MONTH_DAY', "return array('1'=>31, '2'=>29, '3'=>31, '4'=>30, '5'=>31, '6'=>30, '7'=>31, '8'=>31, '9'=>30, '10'=>'31', '11'=>30, '12'=>31);");
//文件上传保存路径
define('BASEDIR_REPORT', '/government/upload/report/');
//督查室主任ID
define('SUPER_ROLE', "return 6;");
$superRoleId=eval(SUPER_ROLE);

session_start();
if(!isset($_SESSION['userID'])){
	exit('<script>top.location.href="/government/index.php"</script>');
}

$deptid = $_SESSION['userDeptID'];
$userid = $_SESSION['userID'];
$userRoleId = $_SESSION['userRoleID'];

/**
 * 控制器
 */
if(!empty($_REQUEST['do'])){
	$do = $_REQUEST['do'];
	
	if($do == 'recvTask'){	
		$res = recvTask();
	}else if($do =='backTask'){
		$res = backTask();
	}else if($do == 'feedbacktask'){
		$res = feedbacktask($deptid, $userid);
	}else if($do == 'getFeedbackByTaskid'){
		$res = getFeedbackByTaskid();
	}else if($do == 'reviewTask'){
		$res = reviewTask($userid, $userRoleId);
	}else if($do == 'querynextpage'){
		$res = querynextpage();
	}else if($do == 'ajaxDeptsTask'){
		$res = ajaxDeptsTask();
	}else if($do == 'ajaxDeptsRecvedTask2'){
		$res = ajaxDeptsRecvedTask2();
	}else if($do == 'getfeedbackbyid'){
		$res = getfeedbackbyid();
	}else if($do == 'leaderdetail'){
		$deptid = $_POST["deptid"];
		$sort = $_POST["sort"];
		$res = getTaskByLeader($deptid, $sort);
	}else if($do == 'deleteFeedback'){
		$res = deleteFeedback();
	}else if($do == 'acceptFeedback'){
		$res = acceptFeedback($userid);
	}else if($do == 'returnFeedback'){
		$res = returnFeedback($userid);
	}
	echo $res;
}

//获取台账类别
function getTaskType(){
	$pdo = new mysql;
	$sql = "select code, value from codemap where type='事项类别' order by code;";
	$res = $pdo->getAll($sql);

	if(!$res)
		return 0;

	$types=array();
	foreach ($res as $row) {
		$types[$row['code']] = $row['value'];
	}
	return $types;
}

//获取领导负责的所有工作
function getTaskByLeader($deptid, $sort){
	if($sort == 1){
		$s = " asc";
	}else{
		$s = " desc";
	}
	$sql ="select t.id id, t.id taskid, t.generaltaskid gtaskid, g.name gtask, t.target gtarget, t.title title, t.investment investment, ifnull(t.onbacktime, t.regbacktype) backtype, r.isover status, r.remark remark from task t join generaltask g on t.generaltaskid=g.id left join (select taskid, isover, remark from taskreview order by viewtime desc, isover desc) r on t.id=r.taskid join taskrecv rc on t.id=rc.taskid where t.status>=3 and rc.deptid in (:deptid) group by t.id order by r.isover" . $s . ", t.id asc;";
	if(empty($deptid))
		$sql = str_replace(':deptid', '', $sql);
	else
		$sql = str_replace(':deptid', $deptid, $sql);
	$pdo = new mysql;
	$res = $pdo->getAll($sql);

	$data = formatData($pdo, $res);
	for($i=0; $i<count($data); $i++){
		for($j=0; $j<count($data[$i]['proj']); $j++){
			$tmp_taskid = 0;
			if(!empty($data[$i]['proj'][$j]['taskid']))
				$tmp_taskid = $data[$i]['proj'][$j]['taskid'];

			$headers = formatDeptnames($pdo, $tmp_taskid, $deptid);
			$data[$i]['proj'][$j]['header_s'] = $headers['header_s'];
			$data[$i]['proj'][$j]['header_l'] = $headers['header_l'];

			if(empty($data[$i]['proj'][$j]['status']))
				$data[$i]['proj'][$j]['status'] = 1;
		}
	}

	$html = '';
	
	if(is_array($data) && count($data) > 0){
		$total_count = sizeof($data);
		$z = 0; //序号
		for($i=0; $i<$total_count; $i++){
			$html .= '<tr height="35px">'
					. '<td colspan="11" class="table_title"><?=$data[$i]["gtask"]?></td>'
					. '</tr>';
			for($j=0; $j<sizeof($data[$i]['proj']); $j++){
				$z = $z + 1;
				$rows = $data[$i]['proj'][$j]['rowspan'];
							
				$stages = $data[$i]['proj'][$j]['stage'];
				$sdates = $data[$i]['proj'][$j]['sdate'];
				$edates = $data[$i]['proj'][$j]['edate'];
							
				$state = $data[$i]['proj'][$j]['status'];
				$state_text = "未完成";
				if($state == 2)
					$state_text = "完成";
							
				$stages0 = count($stages)>0? $stages[0]:"";
				$sdates0 = count($sdates)>0? $sdates[0]:"";
				$edates0 = count($edates)>0? $edates[0]:"";
				$html .= '<tr>'
						. '<td rowspan="' . $rows . '" style="text-align:center;">' . $z . '</td>'
						. '<td rowspan="' . $rows . '" >' . $data[$i]["proj"][$j]["gtarget"] . '</td>'
						. '<td rowspan="' . $rows . '" >' . $data[$i]["proj"][$j]["title"] . '</td>'
						. '<td rowspan="' . $rows . '" style="text-align:center;">' . $data[$i]["proj"][$j]["investment"] . '</td>'
						. '<td style="padding: 5px 5px;" >' . $stages0 . '</td>'
						. '<td style="text-align:center;" >' . $sdates0 . '</td>'
						. '<td style="text-align:center;" >' . $edates0 . '</td>'
						. '<td rowspan="' . $rows . '" width="6%">' . $data[$i]["proj"][$j]["header_s"]
						. '<div class="link" onclick="show_header('.$z.');">查看全部<input type="hidden" id="h_header'.$z.'" value="'.$data[$i]["proj"][$j]["header_l"].'"></div></td>'
						. '<td rowspan="' . $rows . '" style="text-align:center; ">' . $state_text . '</td>'
						. '</tr>';
				for($k=1; $k<$rows; $k++){
					$html .= '<tr>'
							. '<td style="padding: 5px 5px;">' . $stages[$k] . '</td>'
							. '<td style="text-align:center;">' . $sdates[$k] . '</td>'
							. '<td style="text-align:center;">' . $edates[$k] . '</td>'
							. '</tr>';
				}
			}
		}
	}else{
		$html .= '<tr class="alternate_line1">'
				. '<td colspan="11" style="line-height: 35px; text-align:center;">'
				. '<font size="2">没有符合条件的记录</font>'
				. '</td>'
				. '</tr>';
	}
	echo $html;
}

//格式化数据
function formatData($pdo, $source){
	$data = array();
	$generaltask = array();
	$i=$j=0;
	if(empty($source))
		return;
	foreach ($source as $row) {
		$arr = array();
		$arr['id'] = $row['id'];
		$arr['taskid'] = $row['taskid'];
		$arr['gtarget'] = $row['gtarget'];
		$arr['title'] = $row['title'];
		$arr['investment'] = empty($row['investment'])? "" : $row['investment'];
		$arr['backtype'] = $row['backtype'];
		$arr['status'] = $row['status'];
		$arr['remark'] = empty($row['remark'])? "" : $row['remark'];

		$details = getTaskDetailById($pdo, $row['taskid']);
		$rowspan = 1;
		if(count($details) == 0){
			$arr['pro_id']=array();
			$arr['stage']=array();
			$arr['sdate']=array();
			$arr['edate']=array();
			$arr['pro_id'][0]='0';
			$arr['stage'][0]='';
			$arr['sdate'][0]='';
			$arr['edate'][0]='';
		}else{
			for($k=0; $k<count($details); $k++){
				$arr['pro_id'][$k] = $details[$k]['id'];
				$arr['stage'][$k] = $details[$k]['stage'];
				$arr['sdate'][$k] = $details[$k]['startdate'];
				$arr['edate'][$k] = $details[$k]['enddate'];
			}
			$rowspan = $k;
		}
		$arr['rowspan'] = $rowspan;
		if(!isset($generaltask[$row['gtaskid']])){
			$generaltask[$row['gtaskid']] = array();
			$generaltask[$row['gtaskid']]['gtask'] = $row['gtask'];
			$generaltask[$row['gtaskid']]['gtaskid'] = $row['gtaskid'];
			$generaltask[$row['gtaskid']]['proj'] = array();
		}
		$generaltask[$row['gtaskid']]['proj'][] = $arr;
		
		// if(empty($data[$i]['gtaskid'])){
		// 	$j=0;
		// 	$data[$i]['gtask'] = $row['gtask'];
		// 	$data[$i]['gtaskid'] = $row['gtaskid'];
		// 	$data[$i]['proj'][$j] = $arr;
		// 	$j++;
		// }else if($data[$i]['gtaskid'] != $row['gtaskid']){
		// 	$i++;
		// 	$data[$i]['gtask'] = $row['gtask'];
		// 	$data[$i]['gtaskid'] = $row['gtaskid'];
		// 	$j=0;
		// 	$data[$i]['proj'][$j] = $arr;
		// 	$j++;
		// }else if($data[$i]['gtaskid'] == $row['gtaskid']){
		// 	if($j > 0 && $data[$i]['proj'][$j-1]['id'] != $arr['id']){
		// 		$data[$i]['proj'][$j] = $arr;
		// 		$j++;
		// 	}
		// }
	}
	foreach($generaltask as $key=>$val){
		$data[] = $generaltask[$key];
	}
	
	return $data;
}

//格式化数据
function formatDeptnames($pdo, $taskid, $deptid){
	//获取责任主体
	$pheader = '<b>牵头单位：</b>';
	$header_s = '<br><b>责任单位：</b>'; //最多保留3个责任单位
	$header_l = "<br><b>责任单位：</b>"; //保留所有的责任单位
	$pheader_count = $header_count = 0;
	$tmp = "";
	$res = getRelativeDeptNameByTaskid($pdo, $taskid);
	if(empty($res))
		return;
	foreach ($res as $row) {
		if($row['ishead'] == 1){
			if($pheader_count > 0)
				$pheader .= '；';
			$pheader .= $row['deptname'];
			$pheader_count = $pheader_count + 1;
		}else if($row['ishead'] == 0){
			$tmp = "";
			if($header_count > 0){
				$tmp .= '；';
			}
			$tmp .= $row['deptname'];
			if($header_count < 3)
				$header_s .= $tmp;
			else if($header_count == 3)
				$header_s .= ' ...';
			$header_l .= $tmp;
			$header_count = $header_count + 1;
		}	
	}
	if(sizeof($res) > 0)
		return array('header_s' =>$pheader.$header_s, 'header_l'=>$pheader.$header_l);
	return '';
}

//根据部门获取历史反馈记录，并格式化
function formatHistoryFeedBack($pdo, $taskid, $deptid){
	$res = getFeedBacks($pdo, $taskid, $deptid);
	$data = '';
	if(empty($res))
		return;
	foreach ($res as $row) {
		//$data .= $row['backtime'] . '：' . $row['progress'] . '%<br>';
		//$data .= '&emsp;' . $row['remark'].'<br>';
		$data .= $row['backtime'] . '：' . $row['progress'] . '%';
		$data .= '&nbsp;' . $row['remark'].'<br>';
		if(!empty($row['reporturl'])){
			$arr = explode(';', $row['reporturl']);
			for($i=0; $i<count($arr); $i++){
				if(!empty($arr[$i])){
					$filename = pathinfo($arr[$i], PATHINFO_BASENAME);
					$data .= '&nbsp;<a href="' . $arr[$i] . '" target="_blank">'. substr($filename, 21) .'</a><br>';
				}
			}	
		}
		$rvwTxt = "";
		$txtColor = "darkblue";
		if($row['rvwstate'] == 1)
			$rvwTxt = "已通过";
		else if($row['rvwstate'] == 2){
			$txtColor = "darkred";
			$rvwTxt = "已驳回";
		}
		if(!empty($rvwTxt))
			$data .= "&nbsp;<span title='{$row['rvwmark']}' style='color:{$txtColor}; cursor:pointer;'><b>{$rvwTxt}</b></span><br>";
	}

	return $data;
}

//获取历史审核记录并格式化
function formatHistoryReview($res){
	$data = '';
	if(empty($res))
		return $data;
	foreach ($res as $row) {
		$data .= $row['uname'].'：';
		if(!empty($row['progress']))
			$data .= $row['progress'] . '%<br>&emsp;';
		$data .= $row['viewtime'] . '<br>&emsp;';
		
		$data .= $row['remark'].'<br>';
	}

	return $data;
}

//获取最近的反馈记录
function formatlatestFeedBack($pdo, $taskid){
	$res = getLatestFeedback($pdo, $taskid);
	$data = array();
	if(empty($res))
		return '';

	$cur_year = date('Y'); //当前年份
	$cur_month = intval(date('m')); //当前月份
	$month_day=eval(MONTH_DAY);
	$btype = 0; //默认按时反馈
	
	$backtype=1;
	if(!empty($res[0]['backtype']))
		$backtype = $res[0]['backtype'];
	
	$str_dept = "";//未反馈的单位名称列表
	$fid = ""; //已反馈的id列表
	$y_count = 0;//已反馈单位数量
	$n_count = 0;//未反馈单位数量
	//季报
	if($backtype == 1){
		if($cur_month % 3 == 0){
			$start = date('Y-m-d',strtotime(sprintf("%4d-%02d-%02d", $cur_year, $cur_month-2, 1)));
			$end = date('Y-m-d',strtotime(sprintf("%4d-%02d-%02d", $cur_year, $cur_month, $month_day[$cur_month])));
		}
		else if($cur_month % 3 == 1){
			$start = date('Y-m-d',strtotime(sprintf("%4d-%02d-%02d", $cur_year, $cur_month, 1)));
			$end = date('Y-m-d',strtotime(sprintf("%4d-%02d-%02d", $cur_year, $cur_month+2, $month_day[$cur_month+2])));
		}
		else if($cur_month % 3 == 2){
			$start = date('Y-m-d',strtotime(sprintf("%4d-%02d-%02d", $cur_year, $cur_month-1, 1)));
			$end = date('Y-m-d',strtotime(sprintf("%4d-%02d-%02d", $cur_year, $cur_month+1, $month_day[$cur_month+1])));
		}
	}
	//月报
	else if($backtype == 2){
		$start = date('Y-m-d',strtotime(sprintf("%4d-%02d-%02d", $cur_year, $cur_month, 1)));
		$end = date('Y-m-d',strtotime(sprintf("%4d-%02d-%02d", $cur_year, $cur_month, $month_day[$cur_month])));
	}
	//不用报
	else if($backtype == 3){
		return "";
	}
	//双月报
	else if($backtype == 4){
		if($cur_month % 2 == 0){
			$start = date('Y-m-d',strtotime(sprintf("%4d-%02d-%02d", $cur_year, $cur_month-1, 1)));
			$end = date('Y-m-d',strtotime(sprintf("%4d-%02d-%02d", $cur_year, $cur_month, $month_day[$cur_month])));
		}
		else if($cur_month % 2 == 1){
			$start = date('Y-m-d',strtotime(sprintf("%4d-%02d-%02d", $cur_year, $cur_month, 1)));
			$end = date('Y-m-d',strtotime(sprintf("%4d-%02d-%02d", $cur_year, $cur_month+1, $month_day[$cur_month+1])));
		}
	}
	//周报
	else if($backtype == 5){
		$cur_week = intval(date("w"));
		switch($cur_week){
		case 0: $start = date("Y-m-d", strtotime("-6 day")); $end = date("Y-m-d");break;
		case 1: $start = date("Y-m-d"); $end = date("Y-m-d", strtotime("+6 day"));break;
		case 2: $start = date("Y-m-d", strtotime("-1 day")); $end = date("Y-m-d", strtotime("+5 day"));break;
		case 3: $start = date("Y-m-d", strtotime("-2 day")); $end = date("Y-m-d", strtotime("+4 day"));break;
		case 4: $start = date("Y-m-d", strtotime("-3 day")); $end = date("Y-m-d", strtotime("+3 day"));break;
		case 5: $start = date("Y-m-d", strtotime("-4 day")); $end = date("Y-m-d", strtotime("+2 day"));break;
		case 6: $start = date("Y-m-d", strtotime("-5 day")); $end = date("Y-m-d", strtotime("+1 day"));break;
		default:$start = date("Y-m-d"); $end = $start; break;
		}
	}
	//日报
	else if($backtype == 6){
		$start = date("Y-m-d");
		$end = date("Y-m-d");
	}
	//按时报
	else{
		$start = date("Y-01-01");
		$end = $backtype;
	}
	
	$sql = "select taskid, isover, remark from taskreview where taskid={$taskid} order by viewtime desc, isover desc";
	$taskInfo = $pdo->getRow($sql);
	$bOver = true;
	if(empty($taskInfo))
		$bOver = false;
	else if($taskInfo['isover'] == 1)
		$bOver = false;
	foreach ($res as $row) {
		if(!empty($row['backtime'])){
			if(!$bOver){
				if($row['backtime'] >= $start && $row['backtime']<=$end){
					if($y_count > 0)
						$fid .= ',';
					$fid .= $row['id'];
					$y_count = $y_count + 1;
					continue;
				}
			}else{
				if($y_count > 0)
					$fid .= ',';
				$fid .= $row['id'];
				$y_count = $y_count + 1;
				continue;
			}
		}
		
		if($n_count > 0)
			$str_dept .= '，';
		$str_dept .= $row['deptname'];
		$n_count = $n_count + 1;	
	}

	$data['f_yes'] = $fid;
	$data['y_cnt'] = $y_count;
	$data['f_no'] = $str_dept;
	$data['n_cnt'] = $n_count;
	return $data;
}

//根据任务id，获取所有相关责任主体信息
function getRelativeDeptNameByTaskid($pdo, $taskid){
	// $sql = "select deptname from dept where deptid in (select deptid from taskrecv where taskid=:taskid);";
	$sql = "select d.deptid deptid, d.deptname deptname, tr.ishead ishead from taskrecv tr join dept d on tr.deptid = d.deptid where tr.taskid = :taskid order by d.areacode asc, d.deptid asc;";
	$param=array();
	$param[':taskid'] = $taskid;

	if(empty($pdo))
		$pdo = new mysql;
	$res = $pdo->getAll($sql, $param);

	return $res;
}

//获取历史反馈记录
function getFeedBacks($pdo, $taskid, $deptid){
	$sql = "select backtime, progress, remark, reporturl, fbr.rvwstate, fbr.rvwmark from taskfeedback fb left join fb_review fbr on fb.id=fbr.fbid where taskid=:taskid and deptid=:deptid order by backtime desc, progress desc;";
	$param=array();
	$param[':taskid'] = $taskid;
	$param[':deptid'] = $deptid;

	if(empty($pdo))
		$pdo = new mysql;
	$res = $pdo->getAll($sql, $param);

	return $res;
}

//获取历史审核记录
function getHistoryReview($pdo, $taskid){
	$sql = "select u.uid, u.uname, u.roleid, tr.viewtime, tr.progress, tr.remark, tr.backendtime from taskreview tr join user u on tr.userid=u.uid where tr.taskid=:taskid order by tr.viewtime desc, tr.progress desc, id desc;";
	$param=array();
	$param[':taskid'] = $taskid;

	if(empty($pdo))
		$pdo = new mysql;
	$res = $pdo->getAll($sql, $param);

	return $res;
}

//根据台账id获取所有部门的最新反馈记录
function getLatestFeedback($pdo, $taskid){
	$sql = "select df.id, d.deptid, d.deptname,df.backtime,df.progress,df.remark,df.reporturl,df.backtype from (select f.id, r.deptid,f.backtime,f.progress,f.remark,f.reporturl,ifnull(t.onbacktime, t.regbacktype) backtype from taskfeedback f right join taskrecv r on r.deptid = f.deptid and r.taskid=f.taskid left join task t on r.taskid = t.id where r.taskid=:taskid order by r.deptid) df join dept d on df.deptid = d.deptid ORDER BY d.areacode asc, d.deptid asc, df.backtime desc, progress desc;";

	$param=array();
	$param[':taskid'] = $taskid;

	if(empty($pdo))
		$pdo = new mysql;
	$res = $pdo->getAll($sql, $param);

	$data = array();
	$preDeptid = 0;
	foreach($res as $row){
		if(empty($data)){
			$data[] = $row;
			$preDeptid = $row['deptid'];
		}else if($preDeptid != $row['deptid']){
			$data[] = $row;
			$preDeptid = $row['deptid'];
		}
	}
	//echo $data;
	return $data;
}

//获取任务的阶段性信息
function getDetailsByTaskid($pdo, $taskid){
	$sql = "select stage, startdate, enddate from progress where status > 0 and taskid=:taskid;";
	$param=array();
	$param[':taskid'] = $taskid;

	if(empty($pdo))
		$pdo = new mysql;
	$res = $pdo->getAll($sql, $param);

	return $res;
}

//获取所有任务信息
function getAllTaskInfo($pdo, $sql, $generaltaskid, $state, $type, $target, $deptid){
	$param = array();
	if($generaltaskid != "")
		$param[":generaltaskid"] = $generaltaskid;
	if($state > 1)
		$param[":state"] = $state;
	if($type != 0)
		$param[":type"] = $type;
	$param[":target"] = $target;
	if($deptid != 0)
		$param[':deptid'] = $deptid;
	// if(!isset($beginTm) || empty($beginTm))
	// 	$param[":stm"] = sprintf("%4d-%02d-%02d", date('Y'), 1, 1);
	// if(!isset($endTm) || empty($endTm))
	// 	$param[":etm"] = sprintf("%4d-%02d-%02d", date('Y'), 12, 31);

	if(empty($pdo))
		$pdo = new mysql;
	$ret = $pdo->getAll($sql, $param);

	return $ret;
}

//根据部门获取任务信息
function getTaskInfoByDeptId($pdo, $sql, $deptid, $target, $type){
	$param = array();
	if($type != 0)
		$param[":type"] = $type;
	$param[":target"] = $target;
	$param[':deptid'] = $deptid; 

	if(empty($pdo))
		$pdo = new mysql;
	$ret = $pdo->getAll($sql, $param);

	return $ret;
}

//接受任务
function recvTask(){
	$id = trim($_REQUEST['id']);
	$taskid = trim($_REQUEST['taskid']);

	$sql = "update taskrecv set status=:status, recvdate=now() where id=:id;";
	$param = array();
	$param[':id'] = $id;
	$param[':status'] = 1;

	$pdo = new mysql;
	$ret = $pdo->update($sql, $param);

	//更新台账表task总状态
	$sql = "select status from taskrecv where taskid = :taskid;";
	unset($param[':status']);
	unset($param[':id']);
	$param[':taskid'] = $taskid;
	$res = $pdo->getAll($sql, $param);
	$isallrecv = true;
	foreach($res as $row){
		if($row['status'] != 1){
			$isallrecv = false;
			break;
		}
	}
	if($isallrecv){
		$sql = 'update task set status = 4 where id=:id;';
		unset($param[':taskid']);
		$param[':id'] = $taskid;
		$pdo->update($sql, $param);
	}

	if($ret)
		return 1;
	return 0;
}

//退回任务
function backTask(){
	$id = trim($_REQUEST['id']);
	$remark = trim($_REQUEST['remark']);

	$sql = "update taskrecv set status=:status, remark=:remark where id=:id;";
	$param = array();
	$param[':id'] = $id;
	$param[':status'] = 2;
	$param[':remark'] = $remark;

	$pdo = new mysql;
	$ret = $pdo->update($sql, $param);
	if($ret)
		return 1;
	return 0;
}

//反馈任务进度信息
function feedbacktask($deptid, $userid){
	$taskid = empty($_POST['htaskid']) ? 0 : $_POST['htaskid'];
	$remark = empty($_POST['remark']) ? '' : trim($_POST['remark']);
	$progress = empty($_POST['progress']) ? '' : trim($_POST['progress']);
	
	if(empty($taskid))
		return 2;

	$relativeDir = BASEDIR_REPORT.$deptid.'/';
	$dir = $_SERVER['DOCUMENT_ROOT'].$relativeDir;
	if(!file_exists($dir))
		mkdir($dir,0777,true);

	$reporturl = '';//反馈报告文件路径列表分号（；）间隔
	if(!empty($_FILES['file1'])){
		$file_count = count($_FILES['file1']['name']);
		for($i=0; $i<$file_count; $i++){
			if($_FILES['file1']['name'][$i]){
				$num = '1'.sprintf('%02d',($i+1));
				$filename = sprintf('%04d', $taskid).date('YmdHis').sprintf('%03d', $num).$_FILES['file1']['name'][$i];
				move_uploaded_file($_FILES['file1']['tmp_name'][$i], iconv("UTF-8", "gb2312", $dir.$filename));
				$arrImgType = explode(".", $filename);
				$nLen = sizeof($arrImgType);
				$filetype = $arrImgType[$nLen-1];
				$filetype = strtolower($filetype);
				if($filetype == "jpg" || $filetype =="jpeg" || $filetype =="png" || $filetype == "bmp")
					resizeImage($dir.$filename, 1024, 768, $dir.$filename, $filetype);
				if(!empty($reporturl)){
					$reporturl .= ';';
				}
				$reporturl .= $relativeDir.$filename;
			}
		}
	}
	if(!empty($_FILES['file2'])){
		$file_count = count($_FILES['file2']['name']);
		for($i=0; $i<$file_count; $i++){
			if(!empty($_FILES['file2']['name'][$i])){
				$num = '2'.sprintf('%02d', ($i+1));
				$filename = sprintf('%04d', $taskid).date('YmdHis').sprintf('%03d', $num).$_FILES['file2']['name'][$i];
				move_uploaded_file($_FILES['file2']['tmp_name'][$i], iconv('UTF-8', 'gb2312', $dir.$filename));
				$arrImgType = explode(".", $filename);
				$nLen = sizeof($arrImgType);
				$filetype = $arrImgType[$nLen-1];
				$filetype = strtolower($filetype);
				if($filetype == "jpg" || $filetype =="jpeg" || $filetype =="png" || $filetype =="bmp")
					resizeImage($dir.$filename, 1024, 768, $dir.$filename, $filetype);
				if(!empty($reporturl)){
					$reporturl .= ';';
				}
				$reporturl .= $relativeDir.$filename;
			}			
		}
	}
	
	$sql = 'insert into taskfeedback (taskid, deptid, userid, progress, remark, reporturl, backtime) values (?, ?, ?, ?, ?, ?, now());';
	$param = array($taskid, $deptid, $userid, $progress, $remark, $reporturl);

	$pdo = new mysql;
	$ret = $pdo->insert($sql, $param);
	if($ret)
		return 1;
	return 0;
}

//根据任务id获取所有的反馈信息
//默认按照反馈时间降序排列
function getFeedbackByTaskid(){
	$taskid = $_REQUEST['taskid'];
	if(empty($taskid))
		return json_encode(array());
	$rvwstate = empty($_POST['state']) ? 0 : trim($_POST['state']);
	
	$sql = "select d.deptname dname, u.uname uname, f.id, f.progress progress, f.remark remark, f.reporturl, f.reporturl fname, f.backtime, fbr.rvwstate, fbr.rvwmark, tr.ishead from taskfeedback f join taskrecv tr on f.deptid=tr.deptid and f.taskid=tr.taskid left join fb_review fbr on f.id=fbr.fbid join dept d on f.deptid=d.deptid join user u on f.userid=u.uid where f.taskid=:taskid and fbr.rvwstate=:state order by rvwstate, f.backtime desc, f.progress desc;";
	
	$param =array();
	$param[':taskid'] = $taskid;
	$param[':state'] = $rvwstate;
	if($rvwstate == 0){
		$sql = str_replace('and fbr.rvwstate=:state', '', $sql);
		unset($param[':state']);
	}

	$pdo = new mysql;
	$res = $pdo->getAll($sql, $param);
	if($res != 0){
		for($i=0; $i<count($res); $i++){
			$arrUrl = explode(';',$res[$i]['reporturl']);
			$res[$i]['reporturl'] = array();
			$res[$i]['fname'] = array();
			for($j=0; $j<count($arrUrl); $j++){
				if(!empty($arrUrl[$j])){
					$res[$i]['reporturl'][$j] = $arrUrl[$j];
					$res[$i]['fname'][$j] = substr(pathinfo($arrUrl[$j], PATHINFO_BASENAME), 21);
				}
			}
		}
	}
	if($res == 0)
		return json_encode(array());
	return json_encode($res);
}

//审核任务
function reviewTask($userid, $userRoleId){
	$taskid = $_REQUEST['taskid'];
	$state = $_REQUEST['state'];
	$progress = $_REQUEST['progress'];
	$remark = $_REQUEST['remark'];
	
	$param =array();
	$param[':taskid'] = $taskid;
	$param[':userid'] = $userid;
	$param[':state'] = $state;
	$param[':progress'] = $progress;
	$param[':remark'] = $remark;
	
	$pdo = new mysql;
	//非督查室主任
	//查询台账是否被督查室主任审核过，及本次反馈周期的截止日期
	//解决问题：同一天，督查室主任与普通成员同时审核任务，前台js可能无法判断出主任是否审核过，所以只能后台进行判断
	global $superRoleId;
	if($userRoleId != $superRoleId){
		$sql_1 = "select u.roleid, tr.viewtime, tr.progress, tr.remark, tr.backendtime from taskreview tr join user u on tr.userid=u.uid where tr.taskid={$taskid} order by tr.viewtime desc, tr.progress desc, id desc;";
		$res = $pdo->getRow($sql_1);
		if(!empty($res)){
			if($res['roleid']==$superRoleId && date('Y-m-d')<=$res['backendtime'])
				return 2;
		}
	}
	
	$sql = "insert into taskreview(taskid, userid, progress, remark, isover, viewtime, backendtime) values (:taskid, :userid, :progress, :remark, :state, now(), :backendtime);";
	
	//获取台账的报告类型：定期报、按时报
	$sql_2 = "select ifnull(onbacktime, regbacktype) backtype from task where id={$taskid};";
	$res = $pdo->getRow($sql_2);
	//定期报，默认：季报
	if($res['backtype'] == 0)
		$res['backtype'] = 1;
	//获取定期报类型
	$tmp = "";
	if(intval($res['backtype']) > 0 && intval($res['backtype']) < 6)
		$tmp = $GLOBALS['regbacktype'][$res['backtype']];
	//获取台账报告区间的结束日期
	if(!empty($tmp)){
		$month = date('m');
		$pre='';
		if(intval($month) < 10)
			$pre = '0';
		$week = date('w');
		$month_day=eval(MONTH_DAY);
		switch($res['backtype']){
		case 1:if($month % 3 == 1) $month = intval($month) + 2;										//季报
			else if($month % 3 == 2) $month = intval($month) + 1;
			$backtime = date('Y').'-'.$pre.$month.'-'.$month_day[intval($month)];	
			break;
		case 2:$backtime = date('Y').'-'.$pre.$month.'-'.$month_day[intval($month)];				//月报
			break;
		case 4:if($month % 2 == 1) $month = intval($month) + 1;										//双月
			$backtime = date('Y').'-'.$pre.$month.'-'.$month_day[intval($month)];	
			break;
		case 5: if($week == 0) $bactime = date('Y-m-d');											//周报
			else{ $diff = 7-$week; $backtime = date('Y-m-d', strtotime("+{$diff} day"));}	
			break;
		default:
			break;
		}
		if(empty($backtime))
			$param[':backendtime'] = 1;
		else
			$param[':backendtime'] = $backtime;
	}
	else
		$param[':backendtime'] = 0;	//按时报
	$res = $pdo->insert($sql, $param);
	if($res)
		return 1;
	return 0;
}

//二维数组去重
function array_unique_fb($array2D){
	$temp = array();
	$temp2 = array();
	foreach ($array2D as $k => $v){
  		$v=join(',',$v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
  		$temp[$k]=$v;
 	}
 	$temp=array_unique($temp); //去掉重复的字符串,也就是重复的一维数组
 	foreach ($temp as $k => $v){
  		$array=explode(',',$v); //再将拆开的数组重新组装
  		//下面的索引根据自己的情况进行修改即可
  		$arr['taskid'] = $array[0];
  		$arr['gtarget'] = $array[1];
  		$arr['title'] = $array[2];
  		$arr['investment'] = $array[3];
  		$arr['status'] = $array[4];
  		$temp2[] = $arr;
 	}
 	return $temp2;
}

//分页：默认加载第一页数据
function gettaskofpage($generaltaskid, $state, $type, $target, $page=0, $size=15, $deptid=0){
	//分页查询所有status>=3的台账
	$sql = "select t.id, t.id taskid, t.generaltaskid gtaskid, g.name gtask, t.target gtarget, t.title, t.investment, ifnull(t.onbacktime, t.regbacktype) backtype, r.isover status, r.remark from task t join generaltask g on t.generaltaskid=g.id left join (select taskid, isover, remark from taskreview order by viewtime desc, isover desc) r on t.id=r.taskid where t.status>=3 and r.isover=:state and t.type=:type and t.target like :target and t.generaltaskid=:generaltaskid group by t.generaltaskid, t.id";
	if(!empty($deptid))
		$sql = "select t.id, t.id taskid, t.generaltaskid gtaskid, g.name gtask, t.target gtarget, t.title, t.investment, ifnull(t.onbacktime, t.regbacktype) backtype, r.isover status, r.remark from task t join generaltask g on t.generaltaskid=g.id left join (select taskid, isover, remark from taskreview order by viewtime desc, isover desc) r on t.id=r.taskid join taskrecv rc on t.id=rc.taskid where t.status>=3 and r.isover=:state and t.type=:type and t.target like :target and rc.deptid=:deptid and t.generaltaskid=:generaltaskid group by t.generaltaskid, t.id";
	if($state==0){
		$sql = str_replace('and r.isover=:state', '', $sql);
	}else if($state == 1){
		$sql = str_replace('and r.isover=:state', 'and (r.isover=1 or r.isover is null)', $sql);//从未审核过的台账isover是null
	}
	if(empty($generaltaskid)){
		$sql = str_replace('and t.generaltaskid=:generaltaskid', '', $sql);
	}
	if($type == 0){
		$sql = str_replace('and t.type=:type', '', $sql);
	}
	$sql .= ' limit '.($page*$size).', '.$size;
	$sql .= ';';

	$pdo = new mysql;
	$res = getAllTaskInfo($pdo, $sql, $generaltaskid, $state, $type, $target, $deptid);
	$data = formatData($pdo, $res);
	for($i=0; $i<count($data); $i++){
		for($j=0; $j<count($data[$i]['proj']); $j++){
			$tmp_taskid = 0;
			if(!empty($data[$i]['proj'][$j]['taskid']))
				$tmp_taskid = $data[$i]['proj'][$j]['taskid'];

			$res = getHistoryReview($pdo, $tmp_taskid);
			$data[$i]['proj'][$j]['roleid'] = -1;
			$data[$i]['proj'][$j]['backendtime'] = -1;
			$data[$i]['proj'][$j]['complete_perc'] = 0;
			if(!empty($res)){
				$data[$i]['proj'][$j]['roleid'] = $res[0]['roleid'];
				$data[$i]['proj'][$j]['backendtime'] = $res[0]['backendtime'];
				$data[$i]['proj'][$j]['complete_perc'] = $res[0]['progress'].'%';
			}
				
			$reviews = formatHistoryReview($res);
			$data[$i]['proj'][$j]['hisReview'] = $reviews;

			$headers = formatDeptnames($pdo, $tmp_taskid, 0);
			$data[$i]['proj'][$j]['header_s'] = $headers['header_s'];
			$data[$i]['proj'][$j]['header_l'] = $headers['header_l'];

			$feedback = formatlatestFeedBack($pdo, $tmp_taskid);
			$data[$i]['proj'][$j]['feedback'] = $feedback;

			if(empty($data[$i]['proj'][$j]['status']))
				$data[$i]['proj'][$j]['status'] = 1;
		}
	}	
	return $data;
}

//审核进度分页：查询指定页的数据
function querynextpage(){
	$generalTask = empty($_POST['generaltask']) ? "" : trim($_POST['generaltask']);
	$state = empty($_POST['state']) ? 0 : trim($_POST['state']);
	$type = empty($_POST['type']) ? 0 : trim($_POST['type']);
	$target = empty($_POST['target']) ? trim('%%') : '%'.trim($_POST['target']).'%';
	$page = empty($_POST['page']) ? 0 : trim($_POST['page']);
	$did = empty($_POST['did']) ? 0 : trim($_POST['did']);
	$size = 15;
	$data = gettaskofpage($generalTask, $state, $type, $target, $page, $size, $did);
	if(empty($data))
		return 0;
	return json_encode($data);
}

//查询所有的总体任务
function queryGeneralTask(){
	$sql = "select id, name from generaltask;";
	$pdo = new mysql;

	$res = $pdo->getAll($sql);
	if(empty($res))
		$res = array();
	return $res;
}

//分页查询所有已下发任务
function queryDeptsTask($gtaskid, $target, $type, $deptid, $state, $page=0, $size=15){
	$sql = "select r.id, r.taskid taskid, t.generaltaskid gtaskid, g.name gtask, t.target gtarget, t.title, t.investment, ifnull(t.onbacktime, t.regbacktype) backtype, r.status, r.remark from task t join generaltask g on t.generaltaskid=g.id join taskrecv r on t.id=r.taskid where t.status>=3 and r.deptid = :deptid and r.status=:state and t.type in (:type) and t.generaltaskid=:gtaskid and t.target like :target order by r.taskid";
	//未选择总体任务
	if($gtaskid == -1){
		$sql = str_replace('and t.generaltaskid=:gtaskid', '', $sql);
	}
	//未输入工作目标
	if(empty($target)){
		$sql = str_replace('and t.target like :target', '', $sql);
	}
	//未选择台账类型
	if($type == -1){
		$sql = str_replace('and t.type in (:type)', '', $sql);
	}
	$sql .= ' limit '.($page*$size).', '.$size;
	$sql .= ';';

	$pdo = new mysql;
	//从数据库查询数据
	$res = getDeptsTaskFromDB($pdo, $sql, $gtaskid, $target, $type, $deptid, $state);
	//格式化数据
	$data = formatDeptsTask($pdo, $res);

	for($i=0; $i<count($data); $i++){
		$taskid = $data[$i]['taskid'];
		$headers = formatDeptnames($pdo, $taskid, $deptid);
		$data[$i]['header_s'] = $headers['header_s'];
		$data[$i]['header_l'] = $headers['header_l'];
		if(sizeof($data[$i]['backtype']) == 1){
			if($data[$i]['backtype'] == '0')
				$data[$i]['backtype'] = 1;
			$tmp = $GLOBALS['regbacktype'][$data[$i]['backtype']];
			if(!empty($tmp))
				$data[$i]['backtype'] = $tmp;
		}
	}
	return $data;
}

//获取部门接收的任务
function queryDeptsRecvedTask2($gtaskid, $target, $type, $deptid, $page=0, $size=15){
	$sql = "select r.id, r.taskid taskid, t.generaltaskid gtaskid, g.name gtask, t.target gtarget, t.title, t.investment, ifnull(t.onbacktime, t.regbacktype) backtype, r.status, r.remark from task t join generaltask g on t.generaltaskid=g.id join taskrecv r on t.id=r.taskid where t.status>=3 and r.deptid = :deptid and r.status=:state and t.type in (:type) and t.generaltaskid=:gtaskid and t.target like :target order by r.taskid";
	//未选择总体任务
	if($gtaskid == -1){
		$sql = str_replace('and t.generaltaskid=:gtaskid', '', $sql);
	}
	//未输入工作目标
	if(empty($target)){
		$sql = str_replace('and t.target like :target', '', $sql);
	}
	//未选择台账类型
	if($type == -1){
		$sql = str_replace('and t.type in (:type)', '', $sql);
	}
	$sql .= ' limit '.($page*$size).', '.$size;
	$sql .= ';';

	$pdo = new mysql;
	//从数据库查询数据
	$res = getDeptsTaskFromDB($pdo, $sql, $gtaskid, $target, $type, $deptid, 1);
	//格式化数据
	$data = formatDeptsTask($pdo, $res);
	

	for($i=0; $i<count($data); $i++){
		$taskid = $data[$i]['taskid'];
		//历史反馈记录
		//$history = formatHistoryFeedBack($pdo, $taskid, $deptid);
		//$data[$i]['historyFeedBack'] = $history;
		//完成情况审核记录
		$res = getHistoryReview($pdo, $taskid);
		$history_review = formatHistoryReview($res);
		$data[$i]['historyReview'] = $history_review;
		//if($backtype == 0){
		if($data[$i]['backtype'] == 0)
			$data[$i]['backtype'] = 1;
		$backtype = intval($data[$i]['backtype']);
		if($backtype >0 && $backtype < 7)
			$tmp = $GLOBALS['regbacktype'][$data[$i]['backtype']];
		if(!empty($tmp))
			$data[$i]['backtype'] = $tmp;
		//}
	}
	return $data;
}

//分页查询数据库：已下发任务
function getDeptsTaskFromDB($pdo, $sql, $gtaskid, $target, $type, $deptid, $state){
	$param = array();
	if($gtaskid != -1)
		$param[":gtaskid"] = $gtaskid;
	if(!empty($target))
		$param[":target"] = $target;
	if($type != -1)
		$param[":type"] = $type;
	$param[':deptid'] = $deptid;
	$param[':state'] = $state;

	if(empty($pdo))
		$pdo = new mysql;
	$ret = $pdo->getAll($sql, $param);

	return $ret;
}

//格式化数据
function formatDeptsTask($pdo, $source){
	$data = array();
	// $generaltask = array();
	if(empty($source))
		return;
	foreach ($source as $row) {
		$arr = array();
		$arr['id'] = $row['id'];
		$arr['taskid'] = $row['taskid'];
		$arr['gtarget'] = $row['gtarget'];
		$arr['title'] = $row['title'];
		$arr['investment'] = $row['investment'];
		$arr['backtype'] = $row['backtype'];
		$arr['status'] = $row['status'];
		$arr['remark'] = $row['remark'];

		$details = getTaskDetailById($pdo, $row['taskid']);
		$rowspan = 1;
		if(empty($details)){
			$arr['stage']=array();
			$arr['sdate']=array();
			$arr['edate']=array();
		}else{
			for($k=0; $k<count($details); $k++){
				$arr['stage'][] = $details[$k]['stage'];
				$arr['sdate'][] = $details[$k]['startdate'];
				$arr['edate'][] = $details[$k]['enddate'];
			}
			$rowspan = $k;
		}
		$arr['rowspan'] = $rowspan;
		$attachList = getTaskAttachListById($pdo, $row['taskid']);
		if(empty($attachList)){
			$arr['attachList']=array();
		}else{
			for($s=0; $s<count($attachList); $s++){
				$arr['attachList'][$s]=array(
						"attachUrl"		=>	$attachList[$s]['attachUrl'],
						"attachName"	=>	$attachList[$s]['attachName']
					);
			}
		}
		// if(!isset($generaltask[$row['gtaskid']])){
		// 	$generaltask[$row['gtaskid']] = array();
		// 	$generaltask[$row['gtaskid']]['gtask'] = $row['gtask'];
		// 	$generaltask[$row['gtaskid']]['gtaskid'] = $row['gtaskid'];
		// 	$generaltask[$row['gtaskid']]['proj'] = array();
		// }
		// $generaltask[$row['gtaskid']]['proj'][] = $arr;
		$data[] = $arr;
	}
	// foreach($generaltask as $key=>$val){
	// 	$data[] = $generaltask[$key];
	// }
	
	return $data;
}

//获取任务的阶段性信息
function getTaskDetailById($pdo, $taskid){
	$sql = "select id, stage, startdate, enddate from progress where status > 0 and taskid=:taskid;";
	$param=array();
	$param[':taskid'] = $taskid;

	if(empty($pdo))
		$pdo = new mysql;
	$res = $pdo->getAll($sql, $param);

	return $res;
}

//获取任务的附件信息
function getTaskAttachListById($pdo, $taskid){
	$sql = "select attachName, attachUrl from attachment where taskid=:taskid;";
	$param=array();
	$param[':taskid'] = $taskid;

	if(empty($pdo))
		$pdo = new mysql;
	$res = $pdo->getAll($sql, $param);

	return $res;
}

//任务接收页面ajax分页查询
function ajaxDeptsTask(){
	$gtaskid = empty($_POST['gtaskid']) ? -1 : trim($_POST['gtaskid']);
	$target = empty($_POST['target']) ? trim('') : '%'.trim($_POST['target']).'%';
	$type = empty($_POST['tasktype']) ? -1 : trim($_POST['tasktype']);
	$deptid = empty($_POST['deptid']) ? 0 : trim($_POST['deptid']);
	$state = empty($_POST['state']) ? 0 : trim($_POST['state']);
	$page = empty($_POST['page']) ? 0 : trim($_POST['page']);
	
	//没单位，则无法获取数据
	if(empty($deptid))
		return 0;
	$data = queryDeptsTask($gtaskid, $target, $type, $deptid, $state, $page);
	if(empty($data))
		return 0;
	return json_encode($data);
}

//任务反馈页面ajax分页查询
function ajaxDeptsRecvedTask2(){
	$gtaskid = empty($_POST['gtaskid']) ? -1 : trim($_POST['gtaskid']);
	$target = empty($_POST['target']) ? trim('') : '%'.trim($_POST['target']).'%';
	$type = empty($_POST['tasktype']) ? -1 : trim($_POST['tasktype']);
	$deptid = empty($_POST['deptid']) ? 0 : trim($_POST['deptid']);
	$page = empty($_POST['page']) ? 0 : trim($_POST['page']);

	$data = queryDeptsRecvedTask2($gtaskid, $target, $type, $deptid, $page);
	if(empty($data))
		return 0;
	return json_encode($data);
}

//根据feedbackid查询反馈记录
function getfeedbackbyid(){
	$fids = $_REQUEST['ids'];
	if(empty($fids))
		return 0;
	$sql = "select d.deptname dname, f.progress progress, f.remark remark, f.reporturl, f.reporturl fname, f.backtime from taskfeedback f join dept d on f.deptid=d.deptid where f.id in ({$fids}) order by d.areacode, d.deptid;";
	
	$param =array();
	$pdo = new mysql;
	$res = $pdo->getAll($sql, $param);
	if($res != 0){
		for($i=0; $i<count($res); $i++){
			$arrUrl = explode(';',$res[$i]['reporturl']);
			$res[$i]['reporturl'] = array();
			$res[$i]['fname'] = array();
			for($j=0; $j<count($arrUrl); $j++){
				$res[$i]['reporturl'][$j] = $arrUrl[$j];
				$tmp = pathinfo($arrUrl[$j], PATHINFO_BASENAME);
				if(empty($tmp) || strlen($tmp) < 21)
					$res[$i]['fname'][$j]="";
				else
					$res[$i]['fname'][$j] = substr($tmp, 21);
			}
		}
	}
	return json_encode($res);
}

//压缩图片大小
function resizeImage($imgfile, $maxwidth,$maxheight,$name,$filetype){
	$im = imagecreatefromjpeg($imgfile);
	$pic_width = imagesx($im);
	$pic_height = imagesy($im);
 
	if(($maxwidth && $pic_width > $maxwidth) || ($maxheight && $pic_height > $maxheight))
	{
		if($maxwidth && $pic_width>$maxwidth)
		{
			$widthratio = $maxwidth/$pic_width;
			$resizewidth_tag = true;
		}
 
		if($maxheight && $pic_height>$maxheight)
		{
			$heightratio = $maxheight/$pic_height;
			$resizeheight_tag = true;
		}
 
		if($resizewidth_tag && $resizeheight_tag)
		{
			if($widthratio<$heightratio)
				$ratio = $widthratio;
			else
				$ratio = $heightratio;
		}
 
		if($resizewidth_tag && !$resizeheight_tag)
			$ratio = $widthratio;
		if($resizeheight_tag && !$resizewidth_tag)
			$ratio = $heightratio;
 
		$newwidth = $pic_width * $ratio;
		$newheight = $pic_height * $ratio;
 
		if(function_exists("imagecopyresampled"))
		{
			$newim = imagecreatetruecolor($newwidth,$newheight);//PHP系统函数
			imagecopyresampled($newim,$im,0,0,0,0,$newwidth,$newheight,$pic_width,$pic_height);//PHP系统函数
		}
		else
		{
			$newim = imagecreate($newwidth,$newheight);
			imagecopyresized($newim,$im,0,0,0,0,$newwidth,$newheight,$pic_width,$pic_height);
		}
 
		//$name = $name.$filetype;
		imagejpeg($newim,$name);
		imagedestroy($newim);
	}
	else
	{
		$name = $name;
		imagejpeg($im,$name);
	}
}

//删除反馈
function deleteFeedback(){
	$id = $_REQUEST['id'];
	
	$sql = "delete from taskfeedback where id = :id;";
	
	$param =array();
	$param[':id'] = $id;

	$pdo = new mysql;
	$res = $pdo->update($sql, $param);
    if(empty($res))
		return 0;
	return 1;
}
//查询总体任务
function get_all_generaltask(){
	$mLink = new mysql;
	$sql = "select * from generaltask order by id asc";
	$res = $mLink->getAll($sql);

	return $res;
}

//审核意见：反馈通过审核
function acceptFeedback($userid){
	$id = trim($_POST['id']);
	if(empty($id))
		return 0;
	$sql = "insert into fb_review(fbid, rvwstate, rvwuser, rvwtime)values({$id}, '1', {$userid}, now());";
	$pdo = new mysql;
	$res = $pdo->insert($sql);
	if($res === 0 )
		return 0;
	return 1;
}

//审核意见：反馈被驳回
function returnFeedback($userid){
	$id = trim($_POST['id']);
	if(empty($id))
		return 0;
	$mark = trim($_POST['mark']);
	$sql = "insert into fb_review(fbid, rvwstate, rvwmark, rvwuser, rvwtime)values('{$id}', '2', '{$mark}', '{$userid}', now());";
	$pdo = new mysql;
	$res = $pdo->insert($sql);
	if($res === 0)
		return 0;
	return 1;
}
