<?php 
include_once '../mysql.php';

session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
$deptid = $_SESSION['userDeptID'];
$roleid = $_SESSION['userRoleID'];

$duty = array();
$duty['1'] = array(
	'11'=>'办公室领导',
	'12'=>'科室负责人',
	'13'=>'科室成员');
$duty['2'] = array(
	'21'=>'主要领导',
	'22'=>'分管领导',
	'23'=>'办公室主任',
	'24'=>'具体负责人');
$duty['3'] = array(
	'31'=>'主要领导',
	'32'=>'分管领导',
	'33'=>'办公室主任',
	'34'=>'具体负责人');
$duty['4'] = array(
	'41'=>'县区长',
	'42'=>'分管领导',
	'43'=>'办公室主任',
	'44'=>'督查室主任',
	'45'=>'督查室人员');
$json_duty = json_encode($duty);
	
if(!empty($_REQUEST['do'])){
	$do = $_REQUEST['do'];
	
	if($do == 'queryContactById'){
		$id = trim($_REQUEST['id']);
		$res = queryContactById($id);
	}else if($do == 'queryDeptByAreacode'){
		$areaCode = empty($_POST['areacode']) ? 0 : trim($_POST['areacode']);
		$res=queryDeptByAreacode($areaCode);
	}else if($do == 'queryDeptsBydeptid'){
		$res = queryDeptsBydeptid();
	}else{
		$info = getData();
		if($do == 'contactAdd'){
			$res = contactAdd($info);
		}else if($do == 'contactModify'){
			$res = contactModify($info);
		}
	}
	echo $res;
}

function getAllContacts($name, $areaCode, $dept){
	$sep = '%';
	$param = array();
	$param[':name'] = $sep.$name.$sep; //默认按照名称模糊查询
	global $roleid, $deptid;
	//非管理员或者督查室
	if($roleid > 2)	
		$dept = $deptid;
	$sql = "select b.id, b.name, b.level, b.tel, b.weixin, b.deptid, d.deptname, d.areacode from telbook b join dept d on b.deptid=d.deptid where 1=1 and b.name like :name ";
	if($areaCode != 0 && $dept==0){
		$sql .= " and d.areacode=:areacode";
		$param[':areacode'] = $areaCode;
	}else if($dept != 0){
		$sql .= " and b.deptid=:deptid";
		$param[':deptid']=$dept;
	}
	$sql .= " order by d.areacode, b.deptid, b.level;";
	
	$pdo = new mysql();
	$res = $pdo->getAll($sql, $param);
	return $res;
}

function queryDeptByAreacode($areaCode){
	if($areaCode == 0)
		return;
	$sql = "select distinct deptid, deptname from dept where areaCode = :areaCode;";
	$param = array(':areaCode' => $areaCode);
	$pdo = new mysql();
	$res  = $pdo->getAll($sql, $param);
	if(!$res){
		$res['deptid'] = 0;
		$res['deptname'] = "";
	}
	return json_encode($res);
}

function queryDeptsBydeptid(){
	$deptid = empty($_POST['deptid']) ? 0 : $_POST['deptid'];
	if(empty($deptid))
		return 0;
	$sql = "select distinct areaCode from dept where deptid=:deptid";
	$param=array(':deptid' => $deptid);
	$pdo = new mysql;
	$res = $pdo->getFirst($sql, $param);
	if($res==0)
		return 0;
	$areaCode = $res;
	$sql = "select distinct deptid, deptname from dept where areaCode=:areaCode;";
	$param[':areaCode'] = $areaCode;
	unset($param[':deptid']);
	$res = $pdo -> getAll($sql, $param);
	if($res == 0){
		return 0;
	}
	$data = array('areaCode'=>$areaCode, 'depts'=>$res);
	return json_encode($data);
}

function queryContactById($id){
	$sql = "select name, tel, weixin from telbook where id = :id;";
	$param = array(':id' => $id);

	$pdo = new mysql;
	$res = $pdo->getRow($sql, $param);
	return json_encode($res);
}

function getData()
{
	$id = trim($_POST['id']);
	$name = trim($_POST['name']);
	$deptid = trim($_POST['deptid']);
	$level = trim($_POST['duty']);
	$tel = trim($_POST['tel']);
	$weixin = trim($_POST['weixin']);
	$param=array(
		':level' => $level,
		':name' => $name,
		':deptid' => $deptid,
		':tel' => $tel,
		':weixin' => $weixin,
		':id' => $id
	);
	return $param;
}

function contactAdd($info){
	$pdo = new mysql;
	$sql = "select count(name) as num from telbook where name=:name and tel=:tel;";
	$param = array(':name'=>$info[':name'], ':tel'=>$info[':tel']);
	$res = $pdo->getRow($sql, $param);
	if($res['num'] == 0){
		$sql = "insert into telbook(deptid, level, name, tel, weixin) values(:deptid,:level, :name, :tel, :weixin);";
		unset($info[':id']);
		//$info[':deptid'] = trim($_SESSION['userDeptID']);
		$res = $pdo->insert($sql, $info);
		if($res != 0)
			$res = 1;
	}else{
		$res = 2;
	}
	return $res;
}

function contactModify($info){
	$sql = "update telbook set name=:name, deptid=:deptid, level=:level, tel=:tel, weixin=:weixin where id=:id;";

	$pdo = new mysql;
	$res = $pdo->update2($sql, $info);
	if($res == -1)
		return 0;
	return 1;
}