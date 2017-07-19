<?php
error_reporting(0);//关闭提示
include_once "../mysql.php";
header("Content-type:text/html;charset=utf-8");
//var_dump($do);

mainfunc();

function mainfunc(){
	$do= trim($_GET['do']);
	if($do=="dept_getRow"){//进入修改页面时，获取初始绑定值
		$deptId = trim($_POST['deptId']);
		$result = dept_getRow($deptId);
	}else{
		$info=getData();
		if($do=="dept_insert"){//添加
			//$result=$deptName;
			$result = dept_insert($info);
		}else if($do=="dept_update"){//修改
			$result = dept_update($info);
		}
	}
	echo $result;
	//var_dump($result);
}

function getData(){
	$deptId = trim($_POST['deptId']);
	$deptCode = trim($_POST['deptCode']);
	$deptName = trim($_POST['deptName']);
	$deptSort = trim($_POST['deptSort']);
	$deptHead = trim($_POST['deptHead']);
	$webSiteName = trim($_POST['webSiteName']);
	$webSiteAddress = trim($_POST['webSiteAddress']);
	$isOnline = intval($_POST['isOnline']);
	$isShenpi = intval($_POST['isShenpi']);
	$status = intval($_POST['status']);
	$areaCode = intval($_POST['areaCode']);
	$remark = trim($_POST['remark']);
	$adminCode = trim($_POST['adminCode']);
	$addtime = trim($_POST['addtime']);
	$info=array(
		'deptId'=>$deptId,
		'deptCode'=>$deptCode,
		'deptName'=>$deptName,
		'deptSort'=>$deptSort,
		'deptHead'=>$deptHead,
		'webSiteName'=>$webSiteName,
		'webSiteAddress'=>$webSiteAddress,
		'isOnline'=>$isOnline,
		'isShenpi'=>$isShenpi,
		'status'=>$status,
		'areaCode'=>$areaCode,
		'remark'=>$remark,
		'adminCode'=>$adminCode,
		'addtime'=>$addtime
	);
	return $info;
}

//添加部门信息
function dept_insert($info){
	$mLink=new mysql;
	$sql="insert into dept(deptCode,deptName,deptSort,deptHead,webSiteName,webSiteAddress,isOnline,isShenpi,status,areaCode,remark,adminCode,addtime)";
	$sql.=" values(?,?,?,?,?,?,?,?,?,?,?,?,?)";
	//$sql.=" values('".$info['deptCode']."','".$info['deptName']."',".$info['deptSort'].",'".$info['deptHead']."','".$info['webSiteName']."','".$info['webSiteAddress']."',".$info['isOnline'].",".$info['isShenpi'].",".$info['status'].",".$info['areaCode'].",'".$info['remark']."','".$info['adminCode']."','".$info['addtime']."')";
	//return $sql;
	//$result=$mLink->insert($sql);
	$deptId=date('YmdHis',time()).rand(1000,9999);
	$row=array(
		$info['deptCode'],
		$info['deptName'],
		$info['deptSort'],
		$info['deptHead'],
		$info['webSiteName'],
		$info['webSiteAddress'],
		$info['isOnline'],
		$info['isShenpi'],
		$info['status'],
		$info['areaCode'],
		$info['remark'],
		$info['adminCode'],
		$info['addtime']
	);
	$result=$mLink->insert($sql,$row);
	$mLink->closelink();
	//echo $result;
	return $result;
}

//修改部门信息
function dept_update($info){
	$sql="update dept set deptCode='".$info['deptCode']."',deptName='".$info['deptName']."',deptSort=".$info['deptSort'].",deptHead='".$info['deptHead']."',webSiteName='".$info['webSiteName']."',webSiteAddress='".$info['webSiteAddress']."',isOnline=".$info['isOnline'].",isShenpi=".$info['isShenpi'].",status=".$info['status'].",areaCode=".$info['areaCode'].",remark='".$info['remark']."' where deptId = '".$info['deptId']."'";
	$mLink=new mysql;
	$result=$mLink->update($sql);
	$mLink->closelink();
	return $result;
}

function dept_getRow($deptId){
	$sql="select * from dept where deptId = '".$deptId."'";
	$mLink=new mysql;
	$result=$mLink->getRow($sql);
	$mLink->closelink();
	return json_encode($result);
}



?>