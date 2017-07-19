<?php 
include_once "../mysql.php";
ini_set('date.timezone','Asia/Shanghai');
switch($_POST['do']){
	case 1:
	savetask();
	break;
	case 2:
	saveprogress();
	break;
	case 3:
	getprogress();
	break;	
}
//保存录入的支撑项目
function savetask(){
if(isset($_POST['title'])){
session_start();
$targetid=$_POST['targetid'];
$deptid=$_SESSION['userDeptID'];	
$title=$_POST['title'];
$investment=$_POST['investment'];
$createtime=date('Y-m-d H:i:s');		
$mLink=new mysql;
$sqstr='INSERT INTO p_task (targetid,deptid,title,investment,is_adopt,createtime) VALUES(?,?,?,?,?,?)';
$rows=$mLink->insert($sqstr,array($targetid,$deptid,$title,$investment,0,$createtime));	
$sql='update targetrecv set recvtime=?,status=? where targetid='.$targetid.' and deptid='.$deptid;
$res=$mLink->update($sql,array($createtime,2));
if($rows>0){echo $rows;}else{echo 0;};
}	
}
//保存录入的工作标准
function saveprogress(){
session_start();
$deptid=$_SESSION['userDeptID'];	
if(isset($_POST['stage'])){
$taskid=$_POST['taskid'];	
$stage=$_POST['stage'];
$createtime=date('Y-m-d H:i:s');
$startdate=$_POST['startdate'];
$enddate=$_POST['enddate'];		
$mLink=new mysql;
$sqstr='INSERT INTO p_progress (deptid,taskid,stage,startdate,enddate,is_adopt,createtime) VALUES(?,?,?,?,?,?,?)';
$rows=$mLink->insert($sqstr,array($deptid,$taskid,$stage,$startdate,$enddate,0,$createtime));	
$sql='update targetrecv set recvtime=?,status=? where targetid in (select targetid from p_task where id='.$taskid.')  and  deptid='.$deptid;
$res=$mLink->update($sql,array($createtime,2));
if($rows>0){echo $rows;}else{echo 0;};
}	
}
//查询工作标准
function getprogress(){
	if(isset($_POST['taskid'])){
		$taskid=$_POST['taskid'];			
		$mLink=new mysql;
		$sqstr='SELECT dept.deptName,p_progress.id,p_progress.stage,p_progress.startdate,p_progress.enddate FROM p_progress LEFT JOIN dept ON p_progress.deptid=dept.deptId WHERE p_progress.taskid='.$taskid;
		//echo $sqstr;
		$rows=$mLink->getAll($sqstr);
		if(count($rows)>0){
			$count = 0;
			foreach($rows as $row){
				if($count%2 == 0){
					echo '<tr class="alternate_line1">';
				}else{
					echo '<tr class="alternate_line2">';
				}
				echo '<td align="center" width="90px">'.$row['deptName'].'</td>';
				echo '<td align="center" name="stage" id="stage' . $row['id'] . '" onclick="do_edit(\'stage' . $row['id'] . '\')">' . $row['stage'] . '</td>';
				echo '<td align="center" width="90px">'.$row['startdate'].'</td>';
				echo '<td align="center" width="90px">'.$row['enddate'].'</td>';
				echo '</tr>';
				$count++;
			}
		}else{
			echo "0";
		}
	}
}
