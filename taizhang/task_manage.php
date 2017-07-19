<?php
error_reporting(0);//关闭提示
include_once "../mysql.php";
//var_dump($do);

mainfunc();

function mainfunc(){
	$do= trim($_GET['do']);
	switch($do){
		case "task_getDeptListByTaskId"://根据TaskId获取部门列表
			$taskId = trim($_POST['taskId']);
			$result = task_getDeptListByTaskId($taskId);
			break;
		case "task_updateDeptBytaskId"://根据taskId修改台账责任单位列表
			$result = task_updateDeptBytaskId();
			break;
		default:
			break;
	}

	echo $result;
	//var_dump($result);
}

//根据TaskId获取部门列表
function task_getDeptListByTaskId($taskId){
	$sql="select deptId,onbacktime,regbacktype from taskrecv r left join task t on t.id=r.taskid where taskid = ".$taskId;
	$mLink=new mysql;
	$result=$mLink->getAll($sql);
	
	//获取牵头单位deptid
	$sql_head="select deptid from taskrecv where taskid = ".$taskId." and ishead = 1";
	$result_head=$mLink->getAll($sql_head);
	$mLink->closelink();
	$deptHeadIds="";
	foreach($result_head as $info){
		$deptHeadIds.=$info["deptid"].",";
	}
	if(strlen($deptHeadIds)>0){
		$deptHeadIds=substr($deptHeadIds,0,strlen($deptHeadIds)-1);
	}
	
	$deptIds="";
	$onbacktime=$result[0]["onbacktime"];
	$regbacktype=$result[0]["regbacktype"];
	foreach($result as $row){
		$deptIds.=$row["deptId"].",";
	}
	if(strlen($deptIds)>0){
		$deptIds=substr($deptIds,0,strlen($deptIds)-1);
	}
	
	return $deptIds.";".$onbacktime.";".$regbacktype.";".$deptHeadIds;
}


//添加部门信息
function task_updateDeptBytaskId(){
	$taskId = trim($_POST['taskId']);
	$deptIds = trim($_POST['deptIds']);
	$deptHeadIds = trim($_POST['deptHeadIds']);
	$onbacktime = trim($_POST['onbacktime']);
	$regbacktype = trim($_POST['regbacktype']);
	$mLink=new mysql;
	
	//修改task表
	$sql="update task set status = 3";
	
	if(!empty($onbacktime)){
		$sql.=",onbacktime = '".$onbacktime."'";
	}
	else{
		$sql.=",onbacktime = NULL";
	}
	$sql.=",regbacktype = '".$regbacktype."'";
	$sql.=" where id = ".$taskId;//转办
	$mLink->update($sql);
	
	//根据$TaskId删除taskrecv中数据
	$sql="delete from taskrecv where taskid = ".$taskId;
	$mLink->update($sql);
	//向taskrecv中添加责任单位$deptIds
	$sql="insert into taskrecv (taskid,deptid,pubtime) values";
	$arr=explode(',',$deptIds);
	foreach($arr as $deptId){
		$sql.=" (".$taskId.",".$deptId.",now()),";
	}
	$sql=substr($sql,0,strlen($sql)-1);
	$result=$mLink->insert($sql);
	
	//设置牵头单位
	if(strlen($deptHeadIds)>0){
		$sql="update taskrecv set ishead = 1 where taskid = ".$taskId." and deptid in (".$deptHeadIds.") ";
		$sql=substr($sql,0,strlen($sql)-1);
		$result=$mLink->update($sql);
	}
	
	
	$res="";
	if($result){
		$sql="select deptname from dept where deptid in (".$deptHeadIds.") ";//牵头单位
		$result = $mLink->getAll($sql);
		if($result && is_array($result)){
			$res.="<strong>牵头单位：</strong><br/>";
			//var_dump($result);
			foreach($result as $row){
				$res .= $row["deptname"]."(未接收)<br/>";
			}
		}
		$sql="select deptname from dept where deptid in (".$deptIds.") ";//责任单位
		$result1 = $mLink->getAll($sql);
		if($result1 && is_array($result1)){
			$res.="<strong>责任单位：</strong><br/>";
			foreach($result1 as $row){
				if($row["deptname"] != $result[0]["deptname"]){
					$res .= $row["deptname"]."(未接收)<br/>";
				}
				
			}
		}
	}
	
	$mLink->closelink();
	return $res;
}




?>