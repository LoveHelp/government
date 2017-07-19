<?php
error_reporting(0);//关闭提示
include_once "mysql.php";
include_once "constant.php";
header("Content-type:text/html;charset=utf-8");

mainfunc();

function mainfunc(){
	$do= trim($_GET['do']);//pager_taskmanage
	$page = intval(trim($_POST['page']));
	$pageSize = intval(trim($_POST['pageSize'])); 
	$where = trim($_POST['where']); 
	switch($do){
		case "pager_dept"://绑定部门列表
			$result = get_deptList($page,$pageSize,$where);
			break;
		case "pager_taskmanage"://绑定管理台账列表
			$result = get_taskManage($page,$pageSize,$where);
			break;
		default:
			break;
	}
	echo $result;
	//var_dump($result);
}

//绑定部门列表
function get_deptList($page,$pageSize,$where){
	$start = $page*$pageSize; 
	$sql="select * from dept ".$where." limit ".$start.",".$pageSize;
	$mLink=new mysql;
	$result=$mLink->getAll($sql);
	$mLink->closelink();
	return json_encode($result);
}

//绑定管理台账列表
function get_taskManage($page,$pageSize,$where){
	$start = $page*$pageSize; 
	$sql="select id,target,title,investment,postilleader,status from task ".$where." limit ".$start.",".$pageSize;
	$mLink=new mysql;
	$res=$mLink->getAll($sql);
	$mLink->closelink();
	$html="";
	if(!empty($res) && count($res) > 0){
		foreach($res as $value){
			$progressList = get_progressList($value["id"]);
			$size = sizeof($progressList);
			//责任主体：牵头单位+责任单位
			$deptNames_array=get_deptListByTaskid($value["id"]);//责任单位
			$zrztNames = "<font color='red'>未转办</font>";
			if(is_array($deptNames_array) && count($deptNames_array)>0){
				$deptHeadNames="<strong>牵头单位：</strong><br />";
				$deptNames = "<br /><strong>责任单位：</strong><br />";
				$remark="";
				foreach($deptNames_array as $row){
					$tv_status="未接收";
					$status=intval($row["status"]);
					if($status==1){
						$tv_status="接收";
					}else if($status==2){
						$tv_status="退回";
					}
					if(intval($row["ishead"])==1){//牵头单位
						$deptHeadNames.=$row["deptName"]."(".$tv_status.")<br/>";
					}else{//责任单位
						$deptNames.=$row["deptName"]."(".$tv_status.")<br/>";
					}
					if(intval($row["status"])==2){//退回状态
						$remark=$row["remark"];
					}
				}
				$zrztNames=$deptHeadNames.$deptNames;
			}
			$html .= '<tr class="alternate_line1" style="cursor:pointer;line-height:30px;height:30px;">'
			. '<td rowspan="' . $size . '" align="center">' . $value['id'] . '</td>'
			. '<td rowspan="' . $size . '" align="center">' . $value['target'] . '</td>'
			. '<td rowspan="' . $size . '" align="center">' . $value['title'] . '</td>'
			. '<td rowspan="' . $size . '" align="center">'. $value['investment'] . '</td>'
			. '<td align="center">'. $progressList[0]["stage"] . '</td>'
			. '<td align="center">'. $progressList[0]["startdate"] . '</td>'
			. '<td align="center">'. $progressList[0]["enddate"] . '</td>'
			. '<td rowspan="' . $size . '" align="center">' . $zrztNames . '</td>'. '</tr>'
			. '<td rowspan="' . $size . '" align="center">' . $remark . '</td>'
			. '<td rowspan="' . $size . '" align="center"><input type="button" value="转办" onclick="hch.open_dept(' . $value['id'] . ')" style="cursor:hand" class="button1"></td>'. '</tr>';
			if($size > 1){
				for($j=1; $j<$size; $j++){
					$html .=  '<tr class="alternate_line1" onclick="hch.open_dept(' . $value['id'] . ')" style="cursor:pointer;line-height:30px;height:30px;">'
						. '<td align="center">'. $progressList[$j]["stage"] . '</td>'
						. '<td align="center">'. $progressList[$j]["startdate"] . '</td>'
						. '<td align="center">'. $progressList[$j]["enddate"] . '</td>' . '</tr>';
				}
			}
		}
	}
	return $html;
}


//根据taskid查询工作标准
function get_progressList($taskid){
	$mLink = new mysql;
	$res = $mLink->getAll("select id,stage,startdate,enddate from progress where status > 0 and taskid = " . $taskid);
	$mLink->closelink();
	return $res;
}

//根据taskid查询taskrecv中责任单位列表
function get_deptListByTaskid($taskid){
	$mLink = new mysql;
	$deptNames="";
	$res = $mLink->getAll("select deptName,t.status,t.remark,ishead from taskrecv t left join dept d on t.deptid = d.deptId where taskid = " . $taskid);
	$mLink->closelink();
	return $res;
}
?>