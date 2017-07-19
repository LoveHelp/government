<?php 
include_once $_SERVER['DOCUMENT_ROOT'] . '/government/mysql.php';

session_start();
if(!isset($_SESSION['userID'])){
	exit('<script>top.location.href="/government/index.php"</script>');
}

if(!empty($_REQUEST['do'])){
	$do = $_REQUEST['do'];
	if($do == 'queryRoles'){		
		$res = queryRoles();
	}else if($do=='queryDeptByAreacode'){
		$areaCode = trim($_REQUEST['areaCode']);
		$res = queryDeptByAreacode($areaCode);
	}else if($do == 'queryUserById'){
		$uid = trim($_REQUEST['uid']);
		$res = queryUserById($uid);
	}else if($do == 'queryDeptByDeptId'){
		$res = queryDeptByDeptId();
	}else{
		$info = getData();
		if($do == 'userAdd'){
			$res = userAdd($info);
		}else if($do == 'userModify'){
			$res = userModify($info);
		}
	}
	echo $res;
}

function getAllUsers($name, $department, $depttype){
	$query_sql = "select distinct r.role rname,u.uid uid, u.ucode ucode, u.uname uname, d.deptName dname from user u join dept d on u.deptid = d.deptId join rolemenu r on u.ROLEID = r.roleid where u.uname like ? order by d.deptid;";
	
	$sep = '%';
	$param = array();
	$param[] = $sep.$name.$sep; //默认按照名称模糊查询
	if($department != 0){
		$query_sql .= ' and u.deptid=?';
		$param[] = $department;
	}
	else if($depttype != 0){//选择部门类别，未选部门，则按照大的类别查询
		$query_sql .= ' and d.areaCode = ?';
		$param[] = $depttype;
	}
	$query_sql .= ';';

	$pdo = new mysql();
	$res = $pdo->getAll($query_sql, $param);
	return $res;
}

function queryRoles(){
	$sql = "select distinct roleid, role from rolemenu;";
	$pdo = new mysql();
	$res  = $pdo->getAll($sql);
	if(!$res){
		$res['roleid'] = 0;
		$res['role'] = "暂定";
	}
	return json_encode($res);
}

function queryDeptByAreacode($areaCode){
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

function queryDeptByDeptId(){
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

function queryUserById($uid){
	$sql = "select ucode, uname, upasswd, sex, mobile, email, caid, status, remark, supuser, deptid, roleid, modperson, modtime from user where uid = :uid;";
	$param = array('uid' => $uid);

	$pdo = new mysql;
	$res = $pdo->getRow($sql, $param);
	return json_encode($res);
}

function getData()
{
	$uid = trim($_POST['uid']);
	$ucode = trim($_POST['ucode']);
	$uname = trim($_POST['uname']);
	$upasswd = trim($_POST['upasswd']);
	$sex = trim($_POST['sex']);
	$mobile = trim($_POST['mobile']);
	$email = trim($_POST['email']);
	$caid = trim($_POST['caid']);
	$status = trim($_POST['status']);
	$modperson = trim($_SESSION['userName']);
	$modtime = date("Y-m-d H:i:s",time());
	$remark = trim($_POST['remark']);
	$supuser = trim($_POST['supuser']);
	$deptid = trim($_POST['deptid']);
	$roleid = trim($_POST['roleid']);
	$param=array(
		':ucode' => $ucode,
		':uname' => $uname,
		':sex' => $sex,
		':mobile' => $mobile,
		':email' => $email,
		':caid' => $caid,
		':status' => $status,
		':modperson' => $modperson,
		':modtime' => $modtime,
		':remark' => $remark,
		':supuser' => $supuser,
		':deptid' => $deptid,
		':roleid' => $roleid,
		':upasswd' => $upasswd,
		':uid' => $uid
	);
	// $param=array(
	// 	$ucode,$uname, $sex, $mobile, $email, $caid, $status, $modperson, $modtime, $remark, $supuser, $deptid, $roleid, $upasswd, $uid);
	// var_dump($param);
	return $param;
}

function usercodeCheck($ucode){
	$sql = "select count(ucode) as num from user where ucode=:ucode;";
	$pdo = new mysql;

}

function userAdd($info){

	$pdo = new mysql;
	$sql = "select count(ucode) as num from user where ucode=:ucode;";
	$param = array(':ucode'=>$info[':ucode']);
	$res = $pdo->getRow($sql, $param);
	if($res['num'] == 0){
		$sql = "insert into user(ucode, uname, upasswd, sex, mobile, email, caid, status, modperson, modtime, remark, supuser, deptid, roleid) values(:ucode, :uname, :upasswd, :sex, :mobile, :email, :caid, :status, :modperson, :modtime, :remark, :supuser, :deptid, :roleid);";
		unset($info[':uid']);
		$info[':upasswd'] = md5($info[':upasswd']);
		$res = $pdo->insert($sql, $info);
		if($res != 0)
			$res = 1;
	}else{
		$res = 2;
	}
	return $res;
}

function userModify($info){
	$sql_pre = "update user set ucode=:ucode, uname=:uname, sex=:sex, mobile=:mobile, email=:email, caid=:caid, status=:status, modperson=:modperson, modtime=:modtime, remark=:remark, supuser=:supuser, deptid=:deptid, roleid=:roleid";
	$sql_pwd = ", upasswd=:upasswd";
	$where = " where uid=:uid;";

	if(!empty($info[':upasswd'])){
		$sql = $sql_pre . $sql_pwd . $where;
		$info[':upasswd'] = md5($info[':upasswd']);
	}
	else{
		$sql = $sql_pre . $where;
		unset($info[':upasswd']);
	}

	$pdo = new mysql;
	$res = $pdo->update($sql, $info);
	return $res;
}