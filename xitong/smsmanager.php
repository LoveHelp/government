<?php 
include_once $_SERVER['DOCUMENT_ROOT'] . '/government/mysql.php';

session_start();
if(!isset($_SESSION['userID'])){
	exit('<script>top.location.href="/government/index.php"</script>');
}

$sendid = $_SESSION['userID'];
$sendname = $_SESSION['userName'];

//控制器
if(!empty($_REQUEST['do'])){
	$do = $_REQUEST['do'];
	if($do == 'getdeptbytype'){
		$res = getdeptbytype();
	}else if($do=='getcontactsbydeptid'){
		$res = getcontactsbydeptid();
	}else if($do=='getcontactsbylevel'){
		$res = getcontactsbylevel();
	}else if($do == 'sendsms'){
		$content = empty($_POST['content']) ? 0 : trim($_POST['content']);
		$tels = empty($_POST['tels']) ? 0 : trim($_POST['tels']);
		$res = sendsms($tels, $content, $sendid, $sendname);
	}else if($do == 'sendtodepts'){
		$res = sendtodepts($sendid, $sendname);
	}else if($do == 'sendsmsbytaskid'){
		$res = sendsmsbytaskid($sendid, $sendname);
	}else if($do == "sendsmstoall"){
		$res = sendsmstoall($sendid, $sendname);
	}else if($do == 'getnofeedbackdept'){
		$res = getnofeedbackdept();
	}else if($do == 'getnorecvdept'){
		$res = getnorecvdept();
	}
	echo $res;
}

//获取所有的短信信息
function getallsms($sdate, $edate){
	$sql = "select * from sms where 1=1";
	$param = array();

	if(!empty($sdate)){
		$sql .= " and sendtime>=:sdate";
		$param[':sdate'] = $sdate;
	}

	if(!empty($edate)){
		$sql .= " and sendtime<=:edate";
		$param[':edate'] = $edate;
	}
	$sql .= " order by id desc;";

	$pdo = new mysql;
	$res = $pdo->getAll($sql, $param);
	if(!empty($res)){
		for($i=0; $i<count($res); $i++){
			$sql = "select concat(name,'[',dp.deptname, '](', tel, ')') as telinfo from telbook tb join dept dp on tb.deptid=dp.deptid where tel in({$res[$i]['tels']});";
			$tels = $pdo->getAll($sql);
			$tmp="";
			if(!empty($tels)){
				foreach ($tels as $row) {
					if(!empty($tmp))
						$tmp .= '; ';
					$tmp .= $row['telinfo'];
				}
			}else{
				$arr = explode(",", $res[$i]['tels']);
				foreach($arr as $row){
					if(!empty($tmp))
						$tmp .= '; ';
					$tmp .= $row;
				}				
			}
			$res[$i]['tels']=$tmp;
		}
	}
	return $res;
}

//根据部门类型获取部门信息
function getdeptbytype($type){
	if($type == 0)
		return 0;

	$sql = "select deptcode, deptid, deptname from dept where deptid>1 and areacode=:areacode;";
	$param=array(':areacode'=>$type);

	$pdo = new mysql;
	$res = $pdo->getAll($sql, $param);
	return $res;
}

//根据部门id获取部门联系人信息
function getcontactsbydeptid(){
	$deptid = empty($_POST['deptid']) ? 0 : $_POST['deptid'];
	if($deptid == 0)
		return 0;

	$sql = "select name, tel, level  from telbook where deptid=:deptid;";
	$param=array(':deptid'=>$deptid);

	$pdo = new mysql;
	$res = $pdo->getAll($sql, $param);
	return json_encode($res);
}

//获取部门特定职务的联系人信息
function getcontactsbylevel(){
	$level = empty($_POST['level']) ? 0 : $_POST['level'];
	if($level == 0)
		return 0;
	$sql = "select name, tel, deptname from telbook tb join dept d on tb.deptid=d.deptid where level=:level";
	$param = array(':level' => $level);
	
	$pdo= new mysql;
	$res = $pdo->getAll($sql, $param);
	return json_encode($res);
}

//督查通知、督查通报发送短信调用接口
function sendtodepts($sendid, $sendname){
	$deptids = empty($_POST['deptids'])? 0 : trim($_POST['deptids']);
	$smstype = empty($_POST['smstype'])? 0 : trim($_POST['smstype']);
	$title = empty($_POST['title']) ? 0 : trim($_POST['title']);
	$pdo = new mysql;
	
	if($deptids == 0){
		$pdo->log->msg('[sms]短信接收单位列表为空！');
		return;	
	}
	//去除空元素以及元素两端的空格，并生成新的字符串以逗号（,）分隔
	$deptlist = array_unique(explode(',', $deptids));
	$deptlist = array_filter($deptlist);
	$deptids = implode(',',trimarray($deptlist));
	$sql = "select tel from telbook where deptid in (";
	$sql .= $deptids .");";
	
	$res = $pdo->getAll($sql);
	if(empty($res))
		return;
	
	$tels=trim('');
	foreach($res as $row){
		if(!empty($tels))
			$tels .= ',';
		$tels .= $row['tel'];
	}
	$content = $title.'已发布，请注意查收';
	$ret = sendsms($tels, $content, $sendid, $sendname);
	return json_encode($ret);
}

//发送短信
function sendsms($tels, $content, $sendid, $sendname){
	if($tels==0){
		$pdo = new mysql;
		$pdo->log->msg('[sms]短信发送失败：收信人列表为空！');
		$ret = array();
		$ret['state'] = 0;
		$ret['msg'] = "短信号码列表为空";
		return json_encode($ret);
	}
	//去除空元素以及元素两端的空格，并生成新的字符串以逗号（,）分隔
	$telist = array_unique(explode(',', $tels));
	$telist = array_filter($telist);
	$tels = implode(',',trimarray($telist));
	//$pdo->log->msg($tels);
	
	//数据库sql及参数
	$sql = "insert into sms(contents, tels, state, err, msg_group, sendtime, senderid, sendername)values(:cons, :tels, :state, :err, :mgroup, now() , :uid, :uname);";
	$param=array(
		':cons' => $content,
		':tels' => $tels,
		':state'=> 0,
		':err'  => '',
		':mgroup' =>'',
		':uid'  => $sendid,
		':uname'=> $sendname);
	
	//保存token的文件
	$file = "mas_auth.ini";
	//默认值
	$mas_user_id = 0;
	$access_token = 0;
	//文件不存在，则重新进行认证
	if(!file_exists($file)){
		$ret = mas_login($sql, $param);
		if($ret['state'] !== 1)//认证失败，则返回
			return json_encode($ret);
		$mas_user_id = $ret['mas_user_id'];
		$access_token = $ret['access_token'];
	}else{//文件存在，则读取信息
		$cons = file_get_contents($file);
		$tmp_arr = explode(":", $cons);
		//文件内容为空或者不合法（格式mas_user_id:access_token）
		//重新进行认证
		if(empty($tmp_arr) || sizeof($tmp_arr) < 2){
			$ret = mas_login($sql, $param);
			if($ret['state'] !== 1)//认证失败，则返回
				return json_encode($ret);
			$mas_user_id = $ret['mas_user_id'];
			$access_token = $ret['access_token'];
		}else{//文件存在，且内容合法
			$mas_user_id = $tmp_arr[0];
			$access_token = $tmp_arr[1];
		}
	}
	//发送短信
	//如果提示token失效，则重新进行认证并再次发送信息
	//发送成功（0），或者其他错误（1），则直接返回
	$ret = mas_send($tels, $content, $sql, $param, $mas_user_id, $access_token);
	if($ret['state'] === 1){
		return json_encode($ret);
	}

	//token失效或者未登陆错误，则重新进行认证并再次发送信息
	if($ret['state'] === 2){
		unset($ret);
		$ret = resendsms($tels, $content, $sql, $param);
		return json_encode($ret);
	}
}

//token过期，则重新登陆并再次发送短信
function resendsms($tels, $content, $sql, $param){
	$ret = mas_login($sql, $param);
	//认证失败，则返回
	if($ret['state'] !== 1)
		return $ret;
	
	//成功
	$mas_user_id = $ret['mas_user_id'];
	$access_token = $ret['access_token'];
	unset($ret);
	$ret = mas_send($tels, $content, $sql, $param, $mas_user_id, $access_token);
	return $ret;
}

//mas账户认证
function mas_login($sql, $param){
	// 正式账户
	$param_auth = array(
		'ec_name' => '南阳市人民政府',
		'user_name' => 'szfdcs',
		'user_passwd' => '63135185'
	);
	// 测试账户
	// $param_auth = array(
	// 	'ec_name' => '云MAS体验01',
	// 	'user_name' => 'test122304',
	// 	'user_passwd' => 'test12345'
	// );
	$ret = array('state'=>0, 'msg'=>'', "mas_user_id"=>0, 'access_token'=>0);
	$mas_user_id = 0;
	$access_token = 0;
	$access_token_expire_seconds = 0;
	$curl = curl_init();
	//curl_setopt($curl, CURLOPT_URL, "http://112.33.1.13:80/app/http/authorize");
	curl_setopt($curl, CURLOPT_URL, "http://112.33.1.10/app/http/authorize");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);   //只需要设置一个秒的数量就可以  
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $param_auth);
	$res = json_decode(curl_exec($curl));
	$pdo = new mysql;
	if($res->status == 'Success'){
		$mas_user_id = $res->mas_user_id;
		$access_token = $res->access_token;
		$access_token_expire_seconds = $res->access_token_expire_seconds;
		$file = "mas_auth.ini";
		file_put_contents($file, "{$mas_user_id}:{$access_token}");
		//发送成功
		$pdo->log->msg('[sms]短信群发账户验证成功！');
		$ret['state'] = 1;
		$ret['mas_user_id']=$mas_user_id;
		$ret['access_token']=$access_token;
	}else if($res->status == 'SC:4060'){
		//更新数据库短信发送状态
		$msg = "登录验证请求超速";
		$param[':state'] = 0;
		$param[':err'] = $msg;
		//$pdo->log->msg($param[':err']);
		$res = $pdo->insert($sql, $param);	
		//发送失败
		$pdo->log->msg("[sms]".$msg);
		$ret['state'] = 0;
		$ret['msg']=$param[':err'];
		$pdo->insert($sql, $param);
	}else if(strpos($res->status, 'Error') !== false){
		//更新数据库短信发送状态
		$msg="未通过授权";
		$param[':state'] = 0;
		$param[':err'] = $msg;
		//$pdo->log->msg($param[':err']);
		$res = $pdo->insert($sql, $param);	
		//发送失败
		$pdo->log->msg("[sms]".$msg);
		$ret['state'] = 0;
		$ret['msg']=$param[':err'];
		$pdo->insert($sql, $param);
	}else{
		$msg="账户验证请求超时";
		$param[':state'] = 0;
		$param[':err'] = $msg;
		//$pdo->log->msg($param[':err']);
		$res = $pdo->insert($sql, $param);	
		//发送失败
		$pdo->log->msg("[sms]".$msg);
		$ret['state'] = 0;
		$ret['msg']=$param[':err'];
		$pdo->insert($sql, $param);
	}
	curl_close($curl);
	return $ret;
}

//mas发送短信
function mas_send($tels, $content, $sql, $param, $mas_user_id=0, $access_token=0){
	$sign="gsCnUOgq";
	// $sign="Qe4dXDTb"; //测试接口
	$serial = "";
	$mac = strtoupper(md5($mas_user_id.$tels.$content.$sign.$serial.$access_token));
	// $mac = bin2hex($mac_md5);
	$param_sms = array(
		'mas_user_id' => $mas_user_id,
		'mobiles' => $tels,
		'content' => $content,
		'sign' => $sign,
		'serial' => $serial,
		'mac' => $mac
	);
	// var_dump($param_sms);
	$curl = curl_init();
	//curl_setopt($curl, CURLOPT_URL, "http:// 112.33.1.13:80/app/http/sendSms");
	curl_setopt($curl, CURLOPT_URL, "http://112.33.1.10/app/http/sendSms");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 60);   //只需要设置一个秒的数量就可以
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $param_sms);
	$res = json_decode(curl_exec($curl), true);
	// var_dump($ret);
	$ret_msg="短信发送失败";
	switch($res['RET-CODE']){
		case 'SC:0000':
			$param[':state'] = 1;
			$param[':err'] = '短信已经成功提交至云mas平台';
			$ret_msg = "短信发送成功";
			break;
		case 'SC:4060':
			$param[':state'] = 0;
			$param[':err'] = '用户登陆请求超速';
			$ret_msg = "发送失败：请求速度过快，请2分钟之后再发送短信";
			break;
		case 'SC:4010':
			$param[':state'] = 2;
			$param[':err'] = '用户 TOKEN 不存在，可能已失效或未登录';
			break;
		case 'SC:4000':
			$param[':state'] = 2;
			$param[':err'] = 'HTTP MAC 校验错误，请注意签名参数顺序';
			break;
		case 'SC:4140':
			$param[':state'] = 0;
			$param[':err'] = '手机号码数量超过200';
			$ret_msg = "发送失败：手机号码数量超过200";
			break;
		case 'SC:4141':
			$param[':state'] = 0;
			$param[':err'] = '短信内容字符数超过5000';
			$ret_msg = "发送失败：短信内容字符数超过5000";
			break;
		case 'SC:4011':
			$param[':state'] = 2;
			$param[':err'] = '用户信息缺失，可能已失效或未登录';
			break;
		case 'SC:5001':
			$param[':state'] = 0;
			$param[':err'] = '接口处理异常，请联系技术支持或稍候再试';
			break;
		case 'SC:7002':
			$param[':state'] = 0;
			$param[':err'] = '非法号码批次';
			break;
		case 'SC:7003':
			$param[':state'] = 0;
			$param[':err'] = '重复号码';
			$ret_msg = "发送失败：出现重复号码";
			break;
		case 'SC:112':
			$param[':state'] = 0;
			$param[':err'] = '签名错误或普通短信不允许使用模板短信的签名编码';
			break;
		default:$param[':state'] = 0;
			$param[':err'] = '发送超时';
			$ret_msg = "发送失败：请求超时";
			break;
	}
	$param[':mgroup'] = empty($res['MSG-GROUP']) ? '':$res['MSG-GROUP'];
	curl_close($curl);
	$pdo = new mysql;
	$pdo->log->msg("[sms]".$param[':err']);
	//返回值
	$ret = array();
	//token失效或者是未登录
	if($param[':state'] === 2){
		$ret['state'] = 2;
		$ret['msg'] = $param[':err'];
		return $ret;
	}
	//发送成功
	if($param[':state'] === 1){
		$ret['state'] = 1;
		$ret['msg'] = $ret_msg;
		$pdo->log->msg('[sms]短信批次号：'.$param[':mgroup']);
	}else{//发送失败
		$ret['state'] = 0;
		$ret['msg'] = $ret_msg;
	}
	//更新数据库，并返回
	$res = $pdo->insert($sql, $param);
	return $ret;
}

// 获取提交报告
function getsubmitreport($data){}

// 获取状态报告
function getstatereport($data){}

// 
function trimarray($input){
	if(!is_array($input))
		return trim($input);
	return array_map('trimarray', $input);
}

//根据任务id向相关责任单位发送短信
//不用
function sendsmsbytaskid($sendid, $sendname){
	$taskid = empty($_REQUEST['taskid']) ? 0 : $_REQUEST['taskid'];
	$ret = array();
	if($taskid === 0){
		$ret['state'] = 1;
		$ret['msg'] = "发送短信失败：参数错误";
		return json_encode($ret);
	}
	
	//获取任务的反馈时间（类型）
	$sql = "select id, target, ifnull(onbacktime, regbacktype) backtype from task where id={$taskid} and status=4;";
	$pdo = new mysql;
	$task = $pdo->getAll($sql);
	if(empty($task)){
		$ret['state'] = 2;
		$ret['msg'] = "发送短信失败：获取台账信息失败";
		return json_encode($ret);
	}
	
	$sms_txt = "请尽快提交关于工作“{$task[0]['target']}”的完成情况报告";
	//获取任务的所有反馈
	$sql = "SELECT d.deptid, df.backtime, df.progress FROM (SELECT r.deptid, f.backtime, f.progress FROM taskfeedback f RIGHT JOIN taskrecv r ON r.deptid=f.deptid AND r.taskid=f.taskid LEFT JOIN task t ON r.taskid=t.id WHERE r.taskid={$taskid} ORDER BY r.deptid ) df JOIN dept d ON df.deptid=d.deptid ORDER BY d.areacode ASC, d.deptid ASC, df.backtime DESC, df.progress DESC; ";
	$res = $pdo->getAll($sql);
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
	//获取任务最近反馈日期
	$backtype = trim($task[0]['backtype']);
	if(strlen($backtype) > 1){	//按时报
		$starttime = 0;
		$endtime = $backtype;
	}
	else{//定期报
		if($backtype == 1){//季报
			$m = date('m');
			if($m <= 3){
				$starttime = date('Y-1-1');
				$endtime = date('Y-3-31');
			}
			else if($m <= 6){
				$starttime = date('Y-4-1');
				$endtime = date('Y-6-31');
			}
			else if($m <= 9){
				$starttime = date('Y-7-1');
				$endtime = date('Y-9-31');
			}
			else{
				$starttime = date('Y-10-1');
				$endtime = date('Y-12-31');
			}
		}		
		else if($backtype == 2){//月报
			$starttime = date("Y-m-1");
			$endtime = date("Y-m-31");
		}else if($backtype == 4){//双月报
			$m = $date('m');
			if($m<=2){
				$starttime = date("Y-1-1");
				$endtime = date("Y-2-29");
			}else if($m<=4){
				$starttime = date("Y-3-1");
				$endtime = date("Y-4-30");
			}else if($m<=6){
				$starttime = date("Y-5-1");
				$endtime = date("Y-6-30");
			}else if($m<=8){
				$starttime = date("Y-7-1");
				$endtime = date("Y-8-31");
			}else if($m<=10){
				$starttime = date("Y-9-1");
				$endtime = date("Y-10-31");
			}else{
				$starttime = date("Y-11-1");
				$endtime = date("Y-12-31");
			}
		}else if($backtype == 5){//周报
			$w = date('w');
			$curtime = time();
			$starttime = date("Y-m-d", $curtime-86400*($w-1));
			$endtime = date("Y-m-d", strtotime("$starttime +6 day"));
		}else if($backtype == 6){
			$starttime = date("Y-m-d");
			$endtime = date("Y-m-d");
		}
	}
	$deptList = "";//保存未反馈单位的id
	for($i=0; $i<sizeof($data); $i++){
		if(empty($data[$i]['backtime'])){
			if(!empty($deptList))
				$deptList .= ",";
			$deptList .= $data[$i]['deptid'];
		}else{
			if($starttime === 0) //按时反馈：已反馈
				continue;
			else if($data[$i]['backtime'] >= $starttime
				&& $data[$i]['backtime'] <= $endtime) //定期反馈：已反馈
				continue;
			else{//未反馈
				if(!empty($deptList))
					$deptList .= ",";
				$deptList .= $data[$i]['deptid'];
			}
		}
	}
	
	//所有单位都已反馈
	if(empty($deptList)){
		$ret['state'] = 3;
		$ret['msg'] = "相关单位都已反馈工作完成情况";
		return json_encode($ret);
	}
	
	//获取所有未反馈单位的负责人的联系方式
	$tellist = "";
	$sql = "select tel, deptid from telbook where deptid in ($deptList) order by deptid, id desc;";
	$res = $pdo->getAll($sql);
	$preDeptid=0;
	foreach($res as $row){
		if(empty($tellist)){
			$tellist .= $row['tel'];
			$preDeptid = $row['deptid'];
		}else if($preDeptid != $row['deptid']){
			$tellist .= ",".$row['tel'];
			$preDeptid = $row['deptid'];
		}
	}
	
	//发送短信
	$ret = sendsms($tellist, $sms_txt, $sendid, $sendname);
	return $ret;
}

//获取有任务未反馈的单位列表
function getnofeedbackdept(){
	$deptArr = array(); //未反馈单位数组
	$taskArr = array();	//台账列表
	$dept_count = 0;	//单位数量
	$pdo = new mysql;
	
	$tasktype = empty($_POST['tasktype']) ? 0 : trim($_POST['tasktype']);
	//获取所有已接受的台账信息
	$sql = "select id, ifnull(onbacktime, regbacktype) backtype from task where (status='4' or status='3') and type=:type order by id;";
	$param=array(":type"=>$tasktype);
	if(empty($tasktype)){
		$sql = str_replace("and type=:type", "", $sql);
		unset($param[':type']);
	}
	$res = $pdo->getAll($sql, $param);
	if(empty($res)){
		$ret = array();
		$ret['state'] = 0;
		$ret['msg'] = "群发短信失败：已接收台账的数量为0，不必发送短信通知";
		return json_encode($ret);
	}
	
	foreach($res as $row){
		$taskArr[] = $row;
	}
	
	//获取单位数量
	$sql = "select count(deptid) num from dept;";
	$res = $pdo->getAll($sql);
	if(empty($res))
		$dept_count = 0;
	else
		$dept_count = $res[0]['num'];
	
	
	//获取任务的所有最新反馈
	$sql = "SELECT d.deptid, df.backtime, df.progress FROM (SELECT r.deptid, f.backtime, f.progress FROM taskrecv r LEFT JOIN taskfeedback f ON r.deptid=f.deptid AND r.taskid=f.taskid LEFT JOIN task t ON r.taskid=t.id WHERE r.taskid=:taskid ORDER BY r.deptid ) df JOIN dept d ON df.deptid=d.deptid ORDER BY d.areacode ASC, d.deptid ASC, df.backtime DESC, df.progress DESC; ";
	$param=array();
	for($i=0; $i<sizeof($taskArr); $i++){
		$param[':taskid'] = $taskArr[$i]['id'];
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
		
		//获取任务最近反馈日期
		$backtype = trim($taskArr[$i]['backtype']);
		if(strlen($backtype) > 1){	//按时报
			$starttime = 0;
			$endtime = $backtype;
		}
		else{//定期报
			if($backtype == 1){//季报
				$m = date('m');
				if($m <= 3){
					$starttime = date('Y-01-01');
					$endtime = date('Y-03-31');
				}
				else if($m <= 6){
					$starttime = date('Y-04-01');
					$endtime = date('Y-06-31');
				}
				else if($m <= 9){
					$starttime = date('Y-07-01');
					$endtime = date('Y-09-31');
				}
				else{
					$starttime = date('Y-10-01');
					$endtime = date('Y-12-31');
				}
			}		
			else if($backtype == 2){//月报
				$starttime = date("Y-m-01");
				$endtime = date("Y-m-31");
			}else if($backtype == 4){//双月报
				$m = $date('m');
				if($m<=2){
					$starttime = date("Y-01-01");
					$endtime = date("Y-2-29");
				}else if($m<=4){
					$starttime = date("Y-03-01");
					$endtime = date("Y-04-30");
				}else if($m<=6){
					$starttime = date("Y-05-01");
					$endtime = date("Y-06-30");
				}else if($m<=8){
					$starttime = date("Y-07-01");
					$endtime = date("Y-08-31");
				}else if($m<=10){
					$starttime = date("Y-09-01");
					$endtime = date("Y-10-31");
				}else{
					$starttime = date("Y-11-01");
					$endtime = date("Y-12-31");
				}
			}else if($backtype == 5){//周报
				$w = date('w');
				$curtime = time();
				$starttime = date("Y-m-d", $curtime-86400*($w-1));
				$endtime = date("Y-m-d", strtotime("$starttime +6 day"));
			}
		}
		
		for($j=0; $j<sizeof($data); $j++){
			if(empty($data[$j]['backtime'])){
				if(!in_array($data[$j]['deptid'], $deptArr))
					$deptArr[] = $data[$j]['deptid'];
			}else{
				if($starttime === 0) //按时反馈：已反馈
					continue;
				else if($data[$j]['backtime'] >= $starttime
					&& $data[$j]['backtime'] <= $endtime) //定期反馈：已反馈
					continue;
				else{//未反馈
					if(!in_array($data[$j]['deptid'], $deptArr))
						$deptArr[] = $data[$j]['deptid'];
				}
			}
			//如果所有的单位都有未反馈的任务，则不再遍历台账列表
			if(sizeof($deptArr) >= $dept_count)
				break;
		}
	}
	if(empty($deptArr))
	{
		$ret['state'] = 3;
		$ret['msg'] = "相关单位都已反馈工作完成情况";
		return json_encode($ret);
	}

	$ret['state'] = 1;
	$ret['dids'] = implode(",", $deptArr);
	$ret['msg'] = "查询单位列表成功";
	return json_encode($ret);
}

//获取有任务未接收的单位列表
function getnorecvdept(){
	$pdo = new mysql;
	$sql = "select tr.deptid from taskrecv tr join task tk on tr.taskid=tk.id where tr.status=0 group by tr.deptid;";

	$res = $pdo->getAll($sql);
	$ret = array();
	if(empty($res)){
		$ret['state'] = 0;
		$ret['msg'] = "短信提醒失败：所有单位都已接收所下发的任务，不必发送短信通知";
		return json_encode($ret);
	}

	$ret['state'] = 1;
	$arr = array();
	foreach($res as $row){
		$arr[] = $row['deptid'];
	}
	sort($arr);
	$ret['dids'] = implode(",", $arr);
	$ret['msg'] = "查询单位列表成功";
	return json_encode($ret);
}

//不用
function sendsmstoall($sendid, $sendname){
	$deptArr = array();
	
	//单位数量 大于200，则分开发送短信
	$deptlist = '';
	if(sizeof($deptArr) > 200){
		//第一次发送前200个号码
		for($i=0; $i<200; $i++){
			if(!empty($deptlist))
				$deptlist .= ',';
			$deptlist .= $deptArr[$i];
		}
		//获取各部门的联系方式
		$tellist = "";
		$sql = "select tel, deptid from telbook where deptid in ($deptlist) order by deptid, level desc;";
		$res = $pdo->getAll($sql);
		$preDeptid=0;
		foreach($res as $row){
			if(empty($tellist)){
				$tellist .= $row['tel'];
				$preDeptid = $row['deptid'];
			}else if($preDeptid != $row['deptid']){
				$tellist .= ",".$row['tel'];
				$preDeptid = $row['deptid'];
			}
		}
		$ret = sendsms($tellist, "您有工作未反馈，请尽快反馈完成情况报告", $sendid, $sendname);
		$tmp = json_decode($ret);
		//发送失败，则返回
		if($tmp['state'] !== 1)
			return $ret;
		
		//发送后面的号码
		$deptlist = '';
		$tellist = "";
		for($i=200; $i<sizeof($deptArr); $i++){
			if(!empty($deptlist))
				$deptlist .= ',';
			$deptlist .= $deptArr[$i];
		}
		//获取各部门的联系方式
		$sql = "select tel, deptid from telbook where deptid in ($deptlist) order by deptid, id desc;";
		$res = $pdo->getAll($sql);
		$preDeptid=0;
		foreach($res as $row){
			if(empty($tellist)){
				$tellist .= $row['tel'];
				$preDeptid = $row['deptid'];
			}else if($preDeptid != $row['deptid']){
				$tellist .= ",".$row['tel'];
				$preDeptid = $row['deptid'];
			}
		}
		$ret = sendsms($tellist, "您有工作未反馈，请尽快反馈完成情况报告", $sendid, $sendname);
		return $ret;
	}
	
	//短信条数不超过200
	$deptlist = implode(",", $deptArr);
	$tellist = "";
	//获取各部门的联系方式
	$sql = "select tel, deptid from telbook where deptid in ($deptlist) order by deptid, id desc;";
	$res = $pdo->getAll($sql);
	$preDeptid=0;
	foreach($res as $row){
		if(empty($tellist)){
			$tellist .= $row['tel'];
			$preDeptid = $row['deptid'];
		}else if($preDeptid != $row['deptid']){
			$tellist .= ",".$row['tel'];
			$preDeptid = $row['deptid'];
		}
	}
	$ret = sendsms($tellist, "您有工作未反馈，请尽快反馈完成情况报告", $sendid, $sendname);
	return $ret;	
}

//根据任务id获取相关责任单位联系人
//审核进度（台账转办）-短信提醒
function getcontactsbytaskid($taskid){
	if(empty($taskid))
		return array();
	$sql = "select tb.name, tb.tel, dp.deptname from taskrecv tk join telbook tb on tk.deptid=tb.deptid join dept dp on tk.deptid=dp.deptid where taskid={$taskid} order by tb.deptid, tb.level desc;";
	$pdo = new mysql;
	$res = $pdo->getAll($sql);

	$sql = "select title, target from task where id={$taskid};";
	$sms = $pdo->getRow($sql);

	$data = array();
	$data['title'] = str_replace(array("\r\n", "\r", "\n"), ';', $sms['title']);//替换换行符
	if(empty($sms['title']))
		$data['title'] = str_replace(array("\r\n", "\r", "\n"), ';', $sms['target']);
	$data['tels'] = $res;
	return $data;

}

//根据工作目标获取相关责任单位联系人
//台账采集-短信提醒
function getcontactsbytargetid($targetid){
	if(empty($targetid))
		return array();
	$sql = "select tb.name, tb.tel, dp.deptname from targetrecv tr join telbook tb on tr.deptid=tb.deptid join dept dp on tr.deptid=dp.deptid where tr.targetid={$targetid} order by tb.deptid, tb.level desc;";
	$pdo = new mysql;
	$res = $pdo->getAll($sql);

	$sql = "select target from p_target where id={$targetid};";
	$sms = $pdo->getRow($sql);

	$data = array();
	$data['title'] = $sms['target'];
	$data['tels'] = $res;
	return $data;
}

//审核进度-短信提醒（全部）
//台账转办-短信提醒（全部）
function getcontactsbydepts($dids){
	if(empty($dids))
		return array();

	//获取各部门的联系方式
	$sql = "select tb.name, tb.tel, dp.deptname from telbook tb join dept dp on tb.deptid=dp.deptid where tb.deptid in ($dids) order by tb.deptid, tb.level desc;";
	$pdo = new mysql;
	$res = $pdo->getAll($sql);
	
	$data = array();
	$data['title'] = "";
	$data['tels'] = $res;
	return $data;
}