<?php
include_once '../mysql.php';
header("Content-type:text/html;charset=utf-8");

/*mainfunc();

function mainfunc(){
	if(isset($_GET['do'])){
		$do = trim($_GET['do']);
		switch($do){
			case "tasksort"://总排名
				$page = $_GET['page'];
				$result = get_task_sort($page);
				echo $result;
				break;
			case "typesort"://按工作类型排名
				$page = $_GET['page'];
				$type = $_GET['type'];
				$result = get_sort_by_type($page, $type);
				echo $result;
			default:
				break;
		}
	}
}*/

function get_task_sort($page, $sort, $time1, $time2){
	if($sort == 1){
		$s = " desc";
	}else{
		$s = " asc";
	}
	$where = "";
	if($time1 != ""){
		$where .= " and recvdate >= '" . $time1 . "'";  
	}
	if($time2 != ""){
		$where .= " and recvdate <= '" . $time2 . "'";
	}
	$mLink = new mysql;
	//$num = ($page-1)*30;
	//$sql = "select d.deptId, d.deptName, if(ISNULL(table1.total),0,table1.total) as total,if(ISNULL(table2.complete),0,table2.complete) as complete, (complete)/total as rate from dept d left join (select a.deptId as deptid, a.deptName, b.taskid as taskid, count(b.deptid) as total from dept a join taskrecv b on a.deptId = b.deptid GROUP BY b.deptid) table1 on d.deptId = table1.deptid left join (select count(deptId) as complete, deptId from (select * from (select a.deptId, a.deptName, b.taskid, c.isover from dept a join taskrecv b on a.deptId = b.deptid join taskreview c on b.taskid = c.taskid order by deptId asc,b.taskid asc, c.isover desc) res1 group by deptId, taskid) res2 where isover = 2 or isover = 3 group by deptId) table2 on d.deptid = table2.deptid order by rate desc,total desc, complete desc limit " . $num . ",30";
	$sql = "select r0.deptId,r0.deptName, IFNULL(r1.total_count,0) as total_count,IFNULL(r1.total_point,0) as total_point, IFNULL(r2.complete_count,0) as complete_count,IFNULL(r2.complete_point,0) as complete_point,IFNULL(r2.complete_point/r1.total_point,0) as rate from dept r0 left join (select sum(point) as total_point, count(point) as total_count,deptId, deptName from (select 
a.deptId, a.deptName, IF(b.ishead=1,2,IF(b.ishead=0,1,0)) as point, b.taskid
from dept a
join taskrecv b on a.deptId = b.deptid" . $where . " order by a.deptId) res1  GROUP BY deptId)r1 on r0.deptId = r1.deptId
left join (select deptId,sum(point) as complete_point,count(point) as complete_count from (select * from (select a2.deptId,a2.deptName,IF(b2.ishead=1,2,IF(b2.ishead=0,1,0)) as point,c2.taskid,c2.isover from dept a2 join taskrecv b2 on a2.deptId = b2.deptid" . $where . " join taskreview c2 on b2.taskid = c2.taskid order by a2.deptId,c2.id) res2 group by deptId,taskid)tab2 where isover = 2 GROUP BY deptId) r2
on r0.deptId = r2.deptId order by rate" . $s . ",total_count" . $s . ", complete_count" . $s . ",deptId" . $s;
	$data = $mLink->getAll($sql);
	$num = 0;
	$html = "";
	foreach($data as $key=>$d){
		if($num%2 == 0){
			$html .= '<tr class="alternate_line1">';
		}else{
			$html .=  '<tr class="alternate_line2">';
		}
		$num++;
		$uncomplete = $d['total_count'] - $d['complete_count'];
		$html .=  '<td height="100%">' . $num . '</td>'
				. '<td height="100%">' . $d['deptName'] . '</td>'
				. '<td height="100%">' . $d['total_count'] . '</td>'
				. '<td height="100%">' . $uncomplete . '</td>'
				. '<td height="100%">' . $d['complete_count'] . '</td>'
				. '<td height="100%">' . round($d['rate']*100,2) . '%</td></tr>';
	}
	return $html;
}

function get_sort_by_type($page, $type, $sort, $time1, $time2){
	if($sort == 1){
		$s = " desc";
	}else{
		$s = " asc";
	}
	$where = "";
	if($time1 != ""){
		$where .= " and recvdate >= '" . $time1 . "'";  
	}
	if($time2 != ""){
		$where .= " and recvdate <= '" . $time2 . "'";
	}
	$mLink = new mysql;
	//$i = ($page-1)*30;
	//$sql = "select d.deptId, d.deptName, if(ISNULL(table1.total),0,table1.total) as total,if(ISNULL(table2.complete),0,table2.complete) as complete, (complete)/total as rate from dept d left join (select a.deptId as deptid, a.deptName, b.taskid as taskid, count(b.deptid) as total from dept a join taskrecv b on a.deptId = b.deptid join task t on b.taskid = t.id and t.`status` > 0 and t.type = " . $type . " GROUP BY b.deptid) table1 on d.deptId = table1.deptid left join (select count(deptId) as complete, deptId from (select * from (select a.deptId, a.deptName, b.taskid, c.isover from dept a join taskrecv b on a.deptId = b.deptid join taskreview c on b.taskid = c.taskid join task t on b.taskid = t.id and t.`status` > 0 and t.type = " . $type . " order by deptId asc,b.taskid asc, c.isover desc) res1 group by deptId, taskid) res2 where isover = 2 or isover = 3 group by deptId) table2 on d.deptid = table2.deptid order by rate desc,total desc,complete desc,deptId limit " . $i . ",30";
	$sql = "select 
	d.deptId, d.deptName, IFNULL(table1.total_count,0) as total_count,IFNULL(table1.total_point,0) as total_point,IFNULL(table2.complete_count,0) as complete_count,IFNULL(table2.complete_point,0) as complete_point, IFNULL(complete_count/total_count,0) as rate 
from 
	dept d left join (
		select s1.deptId,s1.deptName,taskid, count(s1.deptid) as total_count,sum(point) as total_point from (select 
			a.deptId as deptid, a.deptName, b.taskid as taskid, IF(b.ishead=1,2,IF(b.ishead=0,1,0)) as point
		from 
			dept a 
			join taskrecv b on a.deptId = b.deptid " . $where .
			" join task t on b.taskid = t.id and t.`status` > 0 and t.type  = " . $type . ")s1 GROUP BY s1.deptid
	) table1 on d.deptId = table1.deptid 
	left join (
		select count(deptId) as complete_count, deptId,sum(point) as complete_point from (
			select * from (
				select a.deptId, a.deptName, b.taskid, c.isover,IF(b.ishead=1,2,IF(b.ishead=0,1,0)) as point from dept a join taskrecv b on a.deptId = b.deptid" . $where . " join taskreview c on b.taskid = c.taskid join task t on b.taskid = t.id and t.`status` > 0 and t.type = " . $type . " order by deptId asc,b.taskid asc, c.isover desc
			) res1 group by deptId, taskid) res2 where isover = 2 group by deptId) table2 on d.deptid = table2.deptid order by rate" . $s . ",total_count" . $s . ",complete_count" . $s . ",deptId" .$s;
	$dataArr3 = $mLink->getAll($sql);
	$html = "";
	$i = 0;
	if(is_array($dataArr3) && count($dataArr3) > 0){
		foreach($dataArr3 as $d){
			if($i%2 == 0){
				$html .= '<tr class="alternate_line1">';
			}else{
				$html .= '<tr class="alternate_line2">';
			}
			$i++;
			$uncomplete = $d['total_count'] - $d['complete_count'];
			$html .= '<td height="100%">' . $i . '</td>'
					. '<td height="100%">' . $d['deptName'] . '</td>'
					. '<td height="100%">' . $d['total_count'] . '</td>'
					. '<td height="100%">' . $uncomplete . '</td>'
					. '<td height="100%">' . $d['complete_count'] . '</td>'
					. '<td height="100%">' . round($d['rate']*100,2) . '%</td></tr>';
		}
	}
	return $html;
}
?>