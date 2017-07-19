<?php
include_once "../mysql.php";
//根据部门id获取部门名称
function get_deptName($deptId){
	$res="";
	if($deptId>0){
		$sql="select deptName from dept where deptId = '".$deptId."'";
		$mLink=new mysql;
		$result=$mLink->getRow($sql);
		$mLink->closelink();
		$res = $result["deptName"];
	}
	return $res;
}
function get_deptNameList($deptIds){
	$result="";
	if($deptIds>0){
		$sql="select deptName from dept where deptId in (".$deptIds.")";
		$mLink=new mysql;
		$result=$mLink->getAll($sql);
		$mLink->closelink();
	}
	return $result;
}
function get_deptList(){
	$result="";
	$sql="select deptId,deptName from dept";
	$mLink=new mysql;
	$result=$mLink->getAll($sql);
	$mLink->closelink();
	return $result;
}
function get_infoList($infoType,$infoId){
	$result = "";
	if($infoId != ""){
		$sql="select * from information where infoType = ".$infoType." and infoId = ".$infoId;
		$mLink=new mysql;
		$result=$mLink->getRow($sql);
		$mLink->closelink();
	}
	return $result;
}
function get_infoCount($infoId,$deptId){
	$result = "";
	$sql="select * from information where infoId = ".$infoId;
	$sql.=" and recvDeptIds REGEXP '^".$deptId."$|^".$deptId.",|,".$deptId.",|,".$deptId."$'";
	$mLink=new mysql;
	$res=$mLink->getAll($sql);
	$result=count($res);
	$mLink->closelink();
	return $result;
}


?>