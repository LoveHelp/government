<?php
error_reporting(0);//关闭提示
include_once "../mysql.php";
header("Content-type:text/html;charset=utf-8");

mainfunc();

function mainfunc(){
	$do= trim($_GET['do']);
	switch($do){
		case "info_getRowByInfoId"://督查通报：进入修改页面时，获取初始绑定值
			$infoId = trim($_POST['infoId']);
			$result = info_getRowByInfoId($infoId);
			break;
		case "notification_insert"://添加督查通报
			$info=getData();
			$result = notification_insert($info);
			break;
		case "notification_update"://修改督查通报
			$info=getData();
			$result = notification_update($info);
			break;
		case "dynamic_insert"://添加督查动态
			$info=getData_dynamic();
			$result = dynamic_insert($info);
			break;
		case "dynamic_update"://修改督查动态
			$info=getData_dynamic();
			$result = dynamic_update($info);
			break;
		case "law_insert"://添加督查动态
			$info=getData_law();
			$result = law_insert($info);
			break;
		case "law_update"://修改督查动态
			$info=getData_law();
			$result = law_update($info);
			break;
		case "notice_qianshou"://督查通知签收
			$infoId = trim($_POST['infoId']);
			$deptId = trim($_POST['deptId']);
			$result = notice_qianshou($infoId,$deptId);
			break;
		case "notice_getQianshou"://督查通知：获取签收状态
			$infoId = trim($_POST['infoId']);
			$deptId = trim($_POST['deptId']);
			$result = notice_getQianshou($infoId,$deptId);
			break;
		default:
			break;
	}

	echo $result;
	//var_dump($result);
}

//进入修改页面时，获取初始绑定值
function info_getRowByInfoId($infoId){
	$sql="select * from information where infoId = '".$infoId."'";
	$mLink=new mysql;
	$result=$mLink->getRow($sql);
	$mLink->closelink();
	return json_encode($result);
}

//获取督查通报post参数
function getData(){
	$infoId = trim($_POST['infoId']);
	$infoTitle = trim($_POST['infoTitle']);
	$infoCode = trim($_POST['infoCode']);
	$infoContent = trim($_POST['infoContent']);
	$startTime = trim($_POST['startTime']);
	$deptId = trim($_POST['deptId']);
	$info=array(
		'infoId'=>$infoId,
		'infoTitle'=>$infoTitle,
		'infoCode'=>$infoCode,
		'infoContent'=>$infoContent,
		'startTime'=>$startTime,
		'deptId'=>$deptId
	);
	return $info;
}

//添加通报信息
function notification_insert($info){
	$mLink=new mysql;
	$sql="insert into information(infoType,infoTitle,infoCode,infoContent,startTime,addtime,deptId)";
	$sql.=" values(?,?,?,?,?,now(),?)";
	//return $sql;
	$row=array(
		2,
		$info['infoTitle'],
		$info['infoCode'],
		$info['infoContent'],
		$info['startTime'],
		$info['deptId']
	);
	$result=$mLink->insert($sql,$row);
	//echo $result;
	$mLink->closelink();
	return $result;
}

//修改通报信息
function notification_update($info){
	$sql="update information set infoTitle=?,infoContent=?,infoCode=?,startTime=? where infoId=?";
	$param=array(
		$info['infoTitle'],
		$info['infoContent'],
		$info['infoCode'],
		$info['startTime'],
		$info["infoId"]
	);
	$mLink=new mysql;
	$result = $mLink->update($sql,$param);
	
	$mLink->closelink();
	return $result;
}

//获取督查动态post参数
function getData_dynamic(){
	$infoId = trim($_POST['infoId']);
	$infoTitle = trim($_POST['infoTitle']);
	$infoContent = trim($_POST['infoContent']);
	$deptId = trim($_POST['deptId']);
	$info=array(
		'infoId'=>$infoId,
		'infoTitle'=>$infoTitle,
		'infoContent'=>$infoContent,
		'deptId'=>$deptId
	);
	return $info;
}

//添加动态信息
function dynamic_insert($info){
	$mLink=new mysql;
	$sql="insert into information(infoType,infoTitle,infoContent,addtime,deptId)";
	$sql.=" values(?,?,?,now(),?)";
	$row=array(
		3,
		$info['infoTitle'],
		$info['infoContent'],
		$info['deptId']
	);
	$result=$mLink->insert($sql,$row);
	//echo $result;
	$mLink->closelink();
	return $result;
}

//修改动态信息
function dynamic_update($info){
	$sql="update information set infoTitle=?,infoContent=? where infoId=?";
	$param=array(
		$info['infoTitle'],
		$info['infoContent'],
		$info["infoId"]
	);
	$mLink=new mysql;
	$result = $mLink->update($sql,$param);
	
	$mLink->closelink();
	return $result;
}

//获取法律法规post参数
function getData_law(){
	$infoId = trim($_POST['infoId']);
	$infoTitle = trim($_POST['infoTitle']);
	$infoSort = trim($_POST['infoSort']);
	$infoContent = trim($_POST['infoContent']);
	$startTime = trim($_POST['startTime']);
	$deptId = trim($_POST['deptId']);
	$lawType = trim($_POST['lawType']);
	$info=array(
		'infoId'=>$infoId,
		'infoTitle'=>$infoTitle,
		'infoSort'=>$infoSort,
		'infoContent'=>$infoContent,
		'startTime'=>$startTime,
		'deptId'=>$deptId,
		'lawType'=>$lawType
	);
	return $info;
}

//添加法律法规信息
function law_insert($info){
	$mLink=new mysql;
	$sql="insert into information(infoType,infoTitle,infoSort,infoContent,startTime,addtime,deptId,lawType)";
	$sql.=" values(?,?,?,?,?,now(),?,?)";
	//return $sql;
	$row=array(
		5,
		$info['infoTitle'],
		$info['infoSort'],
		$info['infoContent'],
		$info['startTime'],
		$info['deptId'],
		$info['lawType']
	);
	$result=$mLink->insert($sql,$row);
	//echo $result;
	$mLink->closelink();
	return $result;
}

//修改法律法规信息
function law_update($info){
	$sql="update information set infoTitle=?,infoContent=?,infoSort=?,startTime=?,lawType=? where infoId=?";
	$param=array(
		$info['infoTitle'],
		$info['infoContent'],
		$info['infoSort'],
		$info['startTime'],
		$info['lawType'],
		$info["infoId"]
	);
	$mLink=new mysql;
	$result = $mLink->update($sql,$param);
	
	$mLink->closelink();
	return $result;
}

//督查通知签收
function notice_qianshou($infoId,$deptId){
	$bool = notice_getQianshou($infoId,$deptId);
	$old_signDeptIds=get_signDeptIds($infoId);
	if(strlen($old_signDeptIds)>0){
		$new_signDeptIds = $old_signDeptIds.",".$deptId;
	}else{
		$new_signDeptIds = $deptId;
	}
	
	if($bool==0){//未签收，签收
		$sql="update information set signDeptIds=? where infoId = ?";
		$param=array(
			$new_signDeptIds,
			$infoId
		);
		$mLink=new mysql;
		$result = $mLink->update($sql,$param);
		
		$mLink->closelink();
	}else{//已签收
		$result="0";
	}
	return $result;
}

//督查通知：获取签收状态
function notice_getQianshou($infoId,$deptId){
	$old_signDeptIds = get_signDeptIds($infoId);
	$deptIdsArray = array();
	$bool=0;
	if(strlen($old_signDeptIds)>0){
		$deptIdsArray = explode(",",$old_signDeptIds);
	}
	foreach($deptIdsArray as $row){
		if($row == $deptId){
			$bool=1;
		}
	}
	return $bool;
}

//督查通知签收
function get_signDeptIds($infoId){
	$result="";
	$deptIdsArray=array();
	$sql="select signDeptIds from information where infoId = ".$infoId;
	$mLink=new mysql;
	$res = $mLink->getRow($sql);
	$mLink->closelink();
	if(count($res)>0){
		$result=$res["signDeptIds"];
	}
	return $result;
}

?>