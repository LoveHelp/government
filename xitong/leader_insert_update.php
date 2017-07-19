<?php
error_reporting(0);//关闭提示
include_once "../mysql.php";
include_once "../constant.php";
header("Content-type:text/html;charset=utf-8");
//var_dump($do);

mainfunc();

function mainfunc(){
	$do= trim($_GET['do']);
	switch($do){
		case "leader_insert"://添加
			$info=getData();
			$result = leader_insert($info);
			break;
		case "leader_update"://修改
			$info=getData();
			$result = leader_update($info);
			break;
		case "leader_getRow"://进入修改页面时，获取初始绑定值
			$leaderId = trim($_POST['leaderId']);
			$result = leader_getRow($leaderId);
			break;
		case "leader_updateDept"://修改分管部门
			$deptIds = trim($_POST['deptIds']);
			$leaderId = trim($_POST['leaderId']);
			$result = leader_updateDept($deptIds,$leaderId);
			break;
		case "leader_getDeptList"://获取部门列表
			$result = show_deptList();// leader_getDeptList();
			break;
		case "leader_getDeptListByLeaderId"://根据leaderId获取部门列表
			$leaderId = trim($_POST['leaderId']);
			$result = leader_getDeptListByLeaderId($leaderId);
			break;
		case "leader_getLeaderPhoto"://根据leaderId获取领导头像
			$leaderId = trim($_POST['leaderId']);
			$result = leader_getLeaderPhoto($leaderId);
			break;
		default:
			break;
	}

	echo $result;
	//var_dump($result);
}

function getData(){
	$leaderId = trim($_POST['leaderId']);
	$leaderpost = trim($_POST['leaderpost']);
	$leaderName = trim($_POST['leaderName']);
	$leaderType = trim($_POST['leaderType']);
	$leaderSort = trim($_POST['leaderSort']);
	$leaderwork = trim($_POST['leaderwork']);
	$discription = trim($_POST['discription']);
	$info=array(
		'leaderId'=>$leaderId,
		'leaderpost'=>$leaderpost,
		'leaderName'=>$leaderName,
		'leaderType'=>$leaderType,
		'leaderSort'=>$leaderSort,
		'leaderwork'=>$leaderwork,
		'discription'=>$discription
	);
	return $info;
}

//添加领导信息
function leader_insert($info){
	$mLink=new mysql;
	$sql="insert into leader(leaderName,leaderType,leaderSort,leaderpost,leaderwork,discription)";
	$sql.=" values(?,?,?,?,?,?)";
	//return $sql;
	$row=array(
		$info['leaderName'],
		$info['leaderpost'],
		$info['leaderType'],
		$info['leaderSort'],
		$info['leaderwork'],
		$info['discription']
	);
	$result=$mLink->insert($sql,$row);
	//echo $result;
	$mLink->closelink();
	return $result;
}

//修改领导信息
function leader_update($info){
	$sql="update leader set leaderName='".$info['leaderName']."',leaderSort=".$info['leaderSort'].",leaderType='".$info['leaderType']."',leaderpost='".$info['leaderpost']."',leaderwork='".$info['leaderwork']."',discription='".$info['discription']."' where leaderId = '".$info['leaderId']."'";
	$mLink=new mysql;
	$result=$mLink->update($sql);
	$mLink->closelink();
	return $result;
}

//进入修改页面时，获取初始绑定值
function leader_getRow($leaderId){
	$sql="select * from leader where leaderId = '".$leaderId."'";
	$mLink=new mysql;
	$result=$mLink->getRow($sql);
	$mLink->closelink();
	return json_encode($result);
}

//修改分管部门
function leader_updateDept($deptIds,$leaderId){
	$sql="update leader set deptIds='".$deptIds."' where leaderId = '".$leaderId."'";
	$mLink=new mysql;
	$result=$mLink->insert($sql);
	$mLink->closelink();
	return $result;
}

//获取部门列表
function leader_getDeptList($i){
	$sql="select deptId,deptName from dept where areaCode = ".$i;
	$mLink=new mysql;
	$result=$mLink->getAll($sql);
	$mLink->closelink();
	return $result;
	//return json_encode($result);
}

function show_deptList(){
	global $areaCode;
	$result='';
	if(!empty($areaCode)){
		$result='';
		for ($i=1; $i<=count($areaCode); $i++) {
			$result .= '<div style="line-height:40px;font-weight:bold;overflow:hidden;" id="areaCode'.$i.'"><span class="spselname">'.$areaCode[$i].'</span>';
			$result .= '<span class="spselall"><input type="checkbox" class="selall" name="selall'.$i.'" id="selall'.$i.'" value="selallxq'.$i.'"/><label for="selall'.$i.'">全选</label></span>';	
			$result .= '<div style="padding-left:10px;font-weight:100;clear:both;" id="deptList'.$i.'">';
			$res = leader_getDeptList($i);
			for($j=0;$j<count($res);$j++){
				$result .= '
				<p id="p'.$res[$j]["deptId"].'">
					<input type="checkbox" name="ckbDept" value="'.$res[$j]["deptId"].'" id="'.$res[$j]["deptId"].'"/><label for="'.$res[$j]["deptId"].'">'.$res[$j]["deptName"].'</label>
				</p>
				';
			}
			$result .= '</div></div>';
		}
	}
	return $result;
}

//根据leaderId获取部门列表
function leader_getDeptListByLeaderId($leaderId){
	$sql="select deptIds from leader where leaderId = '".$leaderId."'";
	$mLink=new mysql;
	$result=$mLink->getRow($sql);
	$mLink->closelink();
	return json_encode($result);
}

function leader_getLeaderPhoto($leaderId){
	$sql="select leaderPhoto from leader where leaderId = '".$leaderId."'";
	$mLink=new mysql;
	$result=$mLink->getRow($sql);
	$mLink->closelink();
	return json_encode($result);
}

?>