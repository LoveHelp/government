<?php
include_once "mysql.php";
if(isset($_SESSION['userDeptID'])){
	$deptid = $_SESSION['userDeptID'];
}else{
	exit;
}
//$deptid=1;
$green=0;
$yellow=0;
$orange=0;
$red=0;
$link=new mysql;
//查询待填报工作目标记录条数
$sql='select targetid from targetrecv where deptid=?';
$res=$link->getAll($sql,array($deptid));
$alltarget=count($res);
$sql='select targetid from targetrecv where deptid=? and status=?';
$res=$link->getAll($sql,array($deptid,1));
$waittarget=count($res);
//台账接收情况
//本部门所有台账数.
$sql='select taskid from taskrecv where deptid=?';
$res=$link->getAll($sql,array($deptid));
$recvall=count($res);
//本部门待接收台账数
$sql='select taskid from taskrecv where deptid=? and status=?';
$res=$link->getAll($sql,array($deptid,0));
$waitrecv=count($res);
//本部门已接收台账数
$sql='select taskid from taskrecv where deptid=? and status=?';
$taskids=$link->getAll($sql,array($deptid,1));
$hasrecv=count($res);
//本部门驳回台账数
$sql='select taskid from taskrecv where deptid=? and status=?';
$res=$link->getAll($sql,array($deptid,2));
$backrecv=count($res);
//查询本部门已接收需反馈的工作任务列表
	$sql='select task.id,task.generaltaskid,task.type,task.onbacktime,task.regbacktype from taskrecv INNER JOIN task ON taskrecv.taskid=task.id WHERE taskrecv.deptid='.$deptid.' and taskrecv.status=1 GROUP BY id;';
	$tasklist=$link->getAll($sql);
	//var_dump($tasklist);
//按反馈类型分类本部门待反馈工作列表
$jibaolist=array();
$yuebaolist=array();
$anshibaolist=array();
$shuangyuelist=array();
$zhoubaolist=array();
foreach($tasklist as $t)
{	
	if($t['regbacktype']==1) {array_push($jibaolist,$t);continue;}
	if($t['regbacktype']==2) {array_push($yuebaolist,$t);continue;}
	if($t['regbacktype']==3) {array_push($anshibaolist,$t);continue;}
	if($t['regbacktype']==4) {array_push($shuangyuelist,$t);continue;}
	if($t['regbacktype']==5) {array_push($zhoubaolist,$t);continue;}
}
//统计本单位按季反馈的工作任务
//季度截止期划分
$m=date('m');
$start='';
$end='';
if($m<=3){
	$start=date('Y-1-1');
	$end=date('Y-3-31');
	$flagday=date('Y-3-25');
}else{
	if($m<=6){
		$start=date('Y-4-1');
		$end=date('Y-6-30');
		$flagday=date('Y-6-25');
	}else{
		if($m<=9){
			$start=date('Y-7-1');
			$end=date('Y-9-30');
			$flagday=date('Y-9-25');
		}else{
			$start=date('Y-10-1');
			$end=date('Y-12-31');
			$flagday=date('Y-12-25');
		}
	}
}
$jibaoyifan=0;//按期已反馈季报数
foreach($jibaolist as $jibao){
	$sql='select id from taskfeedback where taskid=? and deptid=? and backtime>=? and backtime<=?';
	$j=$link->getAll($sql,array($jibao['id'],$deptid,$start,$end));
	if(count($j)>0)$jibaoyifan++;
}
$jiebaoweifan=count($jibaolist)-$jibaoyifan;

$today=date('Y-m-d');
$days=(strtotime($flagday)-strtotime($today))/86400;
$green+=$jibaoyifan;
if($days>0){
$yellow+=$jiebaoweifan;
}else{
	if($days>=-3){
		$orange+=$jiebaoweifan;
	}else{
		$red+=$jiebaoweifan;
	}
}
//按期反馈月反馈类型已反馈数
$yuebaofan=0;
$start='';
$end='';
$start=date('Y-m-1');
$end=date('Y-m-d', strtotime("$start +1 month -1 day"));
$flagday=date('Y-m-25');
foreach($yuebaolist as $yuebao){
	$sql='select id from taskfeedback where taskid=? and deptid=? and backtime>=? and backtime<=?';
	$j=$link->getAll($sql,array($yuebao['id'],$deptid,$start,$end));
	if(count($j)>0)$yuebaofan++;
}
$yuebaoweifan=count($yuebaolist)-$yuebaofan;
$green+=$yuebaofan;
$days=(strtotime($flagday)-strtotime($today))/86400;
if($days>0){
$yellow+=$yuebaoweifan;
}else{
	if($days>=-3){
		$orange+=$yuebaoweifan;
	}else{
		$red+=$yuebaoweifan;
	}
}

//按时反馈类型已反馈数
$anshifan=0;
foreach($anshibaolist as $anshibao){
	$sql='select id from taskfeedback where taskid=? and deptid=?';
	$j=$link->getAll($sql,array($anshibao['id'],$deptid));
	if(count($j)>0){
		 $anshifan++; 
	}else{
		$days=(strtotime($anshibao['onbacktime'])-strtotime($today))/86400;
		if($days>0){
		$yellow++;
		}else{
			if($days>=-3){
			$orange++;
			}else{
				$red++;
				}
			}
	}
}
$anshiweifan=count($anshibaolist)-$anshifan;
$green+=$anshiweifan;
$days=(strtotime($flagday)-strtotime($today))/86400;
if($days>0){
$yellow+=$yuebaoweifan;
}else{
	if($days>=-3){
		$orange+=$yuebaoweifan;
	}else{
		$red+=$yuebaoweifan;
	}
}

//按期反馈双月反馈类型统计
//截止期划分
$m=date('m');
$start='';
$end='';
$flagday='';
if($m<=2){
	$start=date('Y-1-1');
	$end=date('Y-2-31');
	$flagday=date('Y-2-24');
}else{
	if($m<=4){
		$start=date('Y-3-1');
		$end=date('Y-4-30');
		$flagday=date('Y-4-25');
	}else{
		if($m<=6){
			$start=date('Y-5-1');
			$end=date('Y-6-30');
			$flagday=date('Y-6-25');
		}else{
		if($m<=8){
			$start=date('Y-7-1');
			$end=date('Y-8-30');
			$flagday=date('Y-8-25');
		}else{
		if($m<=10){
			$start=date('Y-9-1');
			$end=date('Y-10-30');
			$flagday=date('Y-10-25');
		}else{			
			$start=date('Y-11-1');
			$end=date('Y-12-31');
			$flagday=date('Y-12-25');
		}
		}
		}
	}
}
$shuangyuefan=0;
foreach($shuangyuelist as $shuangyue){
	$sql='select id from taskfeedback where taskid=? and deptid=? and backtime>=? and backtime<=?';
	$j=$link->getAll($sql,array($shuangyue['id'],$deptid,$start,$end));
	if(count($j)>0)$shuangyuefan++;
}
$shuangyueweifan=count($shuangyuelist)-$shuangyuefan;
$green+=$shuangyuefan;
$days=(strtotime($flagday)-strtotime($today))/86400;
if($days>0){
$yellow+=$shuangyueweifan;
}else{
	if($days>=-3){
		$orange+=$shuangyueweifan;
	}else{
		$red+=$shuangyueweifan;
	}
}


//按期反馈周反馈类型统计
//截止期划分,w为0-6,0为周日
$w=date('w');
$start='';
$end='';
$flagday='';
if($w==0){
	$start=date('Y-m-d',strtotime("$start -7 day"));
	$end=$today;
	$flagday=date('Y-m-d',strtotime("$start +4 day"));
}else{
	$start=date('Y-m-d',strtotime("$start -$w day"));
	$end=date('Y-m-d',strtotime("$start +7 day"));
	$flagday=date('Y-m-d',strtotime("$start +4 day"));
}
$zhoufan=0;
foreach($zhoubaolist as $zhoubao){
	$sql='select id from taskfeedback where taskid=? and deptid=? and backtime>? and backtime<=?';
	$j=$link->getAll($sql,array($zhoubao['id'],$deptid,$start,$end));
	if(count($j)>0) $zhoufan++;
}
$zhouweifan=count($zhoubaolist)-$zhoufan;
$green+=$zhoufan;
$days=(strtotime($flagday)-strtotime($today))/86400;
if($days>0){
$yellow+=$zhouweifan;
}else{
	if($days>=-1){
		$orange+=$zhouweifan;
	}else{
		$red+=$zhouweifan;
	}
}

?>