<?php
include_once('../mysql.php');
header("Content-type:text/html;charset=utf-8");
include("../constant.php");
mainfunc();

function mainfunc(){
	$do = isset($_GET['do']) ? $_GET['do'] : "";
	switch($do){
		case 'add_generaltask':
			$generaltask = trim($_POST['name']);
			$result = add_generaltask($generaltask);
			echo $result;
			break;
		case 'del_generaltask':
			$id = trim($_POST['id']);
			$result = del_generaltask_by_id($id);
			echo $result;
			break;
		case 'add_target':
			$result = add_target_by_generaltaskid();
			echo $result;
			break;
		case 'update_generaltask':
			$id = $_POST['id'];
			$value = trim($_POST['value']);
			$result = update_generaltask($id, $value);
			return $result;
			break;
		case 'update_target':
			$id = $_POST['id'];
			$value = trim($_POST['value']);
			$result = update_target($id, $value);
			break;
		case 'get_deptList'://根据targetid获取部门列表
			$targetid = trim($_POST['targetid']);
			$result = get_dept_list_by_targetid($targetid);
			echo $result;
			break;
		case 'update_dept'://根据targetid修改台账责任单位列表
			$result = update_dept_by_targetid();
			echo $result;
			break;
		case 'add_task'://添加台账
			$result = add_task_by_target_and_title();
			echo $result;
			break;
		default:
			break;
	}
}

//通过总体任务的id删除该条总体任务
function del_generaltask_by_id($id){
	$mLink = new mysql;
	$sql = "select count(id) as num from p_target where generaltaskid = " . $id;
	$count = $mLink->getRow($sql);
	if($count['num'] > 0){
		return "2";//总体任务正在使用，不能删除
	}

	$sql = "delete from generaltask where id = " . $id;
	$res = $mLink->update($sql);
	if($res){
		return "1";//删除成功
	}else{
		return "0";//删除失败
	}
}

function add_generaltask($generaltask){
	$mLink = new mysql;
	$sql1 = "select id from generaltask where name = '" . $generaltask . "'";
	$id = $mLink->getRow($sql1);
	if($id){
		return "fail";
	}else{
		$sql2 = "insert into generaltask (name) values ('" . $generaltask . "')";
		$res = $mLink->insert($sql2);
		if($res){
			return $res;
		}
	}
}

function add_target_by_generaltaskid(){
	$generaltaskid = $_POST['generaltaskid'];
	$target = trim($_POST['target']);
	$type = $_POST['type'];
	$fromdate = $_POST['fromdate'];
	$handledate = $_POST['handledate'];
	$modperson = $_POST['modperson'];
	$time = date("Y-m-d H:i:s");

	$mLink = new mysql;
	$sql1 = "select id from p_target where generaltaskid = " . $generaltaskid . " and type = " . $type . " and target = '" . $target . "'";
	$id = $mLink->getRow($sql1);
	if($id){
		return "exist";
	}else{
		$sql2 = "insert into p_target set generaltaskid = " . $generaltaskid . ", type = " . $type . ", target = '" . $target . "', fromdate = '" . $fromdate . "', handledate = '" . $handledate . "', modperson = '" . $modperson . "', modtime = '" . $time . "'";
		$res = $mLink->insert($sql2);
		if($res){
			return $res;
		}else{
			return "fail";
		}
	}
}

function update_generaltask($id, $generaltask){
	$mLink = new mysql;
	$sql = "update generaltask set name = '" . $generaltask . "' where id = " . $id;
	$res = $mLink->update($sql);
	if($res){
		return $res;
	}
}

function update_target($id, $target){
	$mLink = new mysql;
	$sql = "update p_target set target = '" . $target . "' where id = " . $id;
	$res = $mLink->update($sql);
	if($res){
		return $res;
	}
}
//get_all_generaltask_and_target();
function get_all_generaltask_and_target($type, $generaltaskid, $target, $is_turn, $is_task){
	$mLink = new mysql;
	if($generaltaskid != ""){
		$sql1 = "select * from generaltask where id = " . $generaltaskid . " order by id";
	}else{
		$sql1 = "select * from generaltask order by id";
	}
	$generaltaskList = $mLink->getAll($sql1);
	$where = "";
	if($type != ""){
		$where .= " and type = " . $type;
	}
	if($target != ""){
		$where .= " and target like '%" . $target . "%'";
	}
	if($is_turn != ""){
		$where .= " and status = " . $is_turn;
	}
	if($is_task != ""){
		$where .= " and is_task = " . $is_task;
	}
	$res = array();
	foreach($generaltaskList as $g){
		$sql2 = "select * from p_target where generaltaskid = " . $g['id'] . $where . " order by id asc";
		
		$targetList = $mLink->getAll($sql2);
		$target = array();
		if(!empty($targetList)){
			foreach($targetList as $t){
				$sql3 = "select a.ishead, a.status, b.deptName from targetrecv a left join dept b on a.deptid = b.deptId where a.status > 0 and a.targetid = " . $t['id'] . " order by a.id asc";
				$responseList = $mLink->getAll($sql3);
				$main = "<b>牵头单位：</b>";
				$sub = "<br /><b>责任单位：</b>";
				$response = "";
				if(is_array($responseList) && count($responseList) > 0){
					$count1 = 0;
					$count2 = 0;
					foreach($responseList as $r){
						if($r['ishead'] == 1){
							if($r['status'] == 1){
								if($count1 == 0){
									$main .= $r['deptName'] . "（未反馈）";
								}else{
									$main .= "," . $r['deptName'] . "（未反馈）";
								}
							}else{
								if($count1 == 0){
									$main .= $r['deptName'] . "（已反馈）";
								}else{
									$main .= "," . $r['deptName'] . "（已反馈）";
								}
							}
							$count1++;
						}else{
							if($r['status'] == 1){
								if($count2 == 0){
									$sub .= $r['deptName'] . "（未反馈）";
								}else{
									$sub .= "," . $r['deptName'] . "（未反馈）";
								}
							}else{
								if($count2 == 0){
									$sub .= $r['deptName'] . "（已反馈）";
								}else{
									$sub .= "," . $r['deptName'] . "（已反馈）";
								}
							}
							$count2++;
						}
					}
					$response = $main . $sub;
				}
				
				$target[] = array(
					"response"		=>		$response,
					"id"			=>		$t['id'],
					"target"		=>		$t['target'],
					"fromdate"		=>		$t['fromdate'],
					"handledate"		=>		$t['handledate']);
			}
		}
		$res[] = array(
			"id"			=>		$g['id'],
			'name'			=>		$g['name'],
			'targetList'	=>		$target);
	}
	return json_encode($res);
}

function get_dept_list_by_targetid($targetid){
	$mLink = new mysql;
	$sql1  = "select deptid from targetrecv where targetid = " . $targetid;
	$result = $mLink->getAll($sql1);
	$deptIds = "";
	foreach($result as $row){
		$deptIds .= $row["deptid"].",";
	}
	if(strlen($deptIds) > 0){
		$deptIds = substr($deptIds, 0, strlen($deptIds)-1);
	}

	$sql2 = "select deptid from targetrecv where ishead = 1 and targetid = " . $targetid;
	$result_head = $mLink->getAll($sql2);
	$deptHeadIds = "";
	foreach($result_head as $info){
		$deptHeadIds .= $info["deptid"].",";
	}
	if(strlen($deptHeadIds) > 0){
		$deptHeadIds = substr($deptHeadIds, 0, strlen($deptHeadIds)-1);
	}
	return $deptIds . ";" . $deptHeadIds;
}

function update_dept_by_targetid(){
	$targetid = trim($_POST['targetid']);
	$deptIds = trim($_POST['deptIds']);
	$deptHeadIds = trim($_POST['deptHeadIds']);
	$onbacktime = trim($_POST['onbacktime']);
	$regbacktype = trim($_POST['regbacktype']);
	$time = date("Y-m-d H:i:s");

	$mLink = new mysql;

	//修改task表
	$sql = "update p_target set status = 2";
	
	if(!empty($onbacktime)){
		$sql .= ", onbacktime = '" . $onbacktime . "'";
	}
	$sql .= ", regbacktype = '" . $regbacktype . "'";
	$sql .=" where id = " . $targetid;//转办
	$mLink->update($sql);
	//根据targetid删除taskrecv中数据
	$sql = "delete from targetrecv where targetid = " . $targetid;
	$mLink->update($sql);
	//向taskrecv中添加责任单位$deptIds
	$sql = "insert into targetrecv (targetid,deptid,pubtime) values";
	$arr = explode(',',$deptIds);
	foreach($arr as $deptId){
		$sql .= " (" . $targetid . "," . $deptId . ", '" . $time . "'),";
	}
	$sql = substr($sql, 0, strlen($sql)-1);
	$result = $mLink->insert($sql);
	//设置牵头单位
	if(strlen($deptHeadIds) > 0){
		$sql = "update targetrecv set ishead = 1 where targetid = " . $targetid." and deptid in (" . $deptHeadIds . ") ";
		$sql = substr($sql, 0, strlen($sql)-1);
		$result = $mLink->update($sql);
	}
	//$mLink->closelink();
	return $result;
}

function get_target_by_dept($deptid){
	$mLink = new mysql;
	$sql = "select a.id as id, b.target as target from targetrecv a left join p_target b on a.targetid = b.id where deptid = " . $deptid;
	$res = $mLink->getAll($sql);

	return json_encode($res);
}

function get_all_generaltask(){
	$mLink = new mysql;
	$sql = "select * from generaltask order by id asc";
	$res = $mLink->getAll($sql);

	return json_encode($res);
}

function get_p_task_list($mLink, $targetid){
	$sql = "select a.id, a.title, a.investment, b.deptName from p_task a left join dept b on a.deptid = b.deptId where targetid = " . $targetid;
	$res = $mLink->getAll($sql);

	return json_encode($res);
}

function get_p_progress_list($mLink, $taskid){
	$sql = "select * from p_progress where taskid = " . $taskid;
	$res = $mLink->getAll($sql);

	return json_encode($res);
}

function get_target_by_id($mLink, $targetid){
	$sql = "select target from p_target where id = " . $targetid;
	$res = $mLink->getRow($sql);

	return json_encode($res);
}

function add_task_by_target_and_title(){
	$mLink = new mysql();
	$task_str = str_replace('\"', '"', $_POST['task']);
	$targetList = json_decode($task_str, true);
	$targetid = $_POST['targetid'];
	$username = $_POST['username'];
	$time = $_POST['time'];
	$sql1 = "select * from p_target where id = " . $targetid;
	$res = $mLink->getRow($sql1);
	$where = "";
	if(empty($res['onbacktime'])){
		$where =  ", regbacktype = " . $res['regbacktype'];
	}else{
		$where =  ", onbacktime = '" . $res['onbacktime'] . "', regbacktype = " . $res['regbacktype'];
	}
	foreach($targetList as $target){
		$update_sql = "update p_target set is_task = 1 where id = " . $targetid;
		$mLink->update($update_sql);
		$sql2 = "insert into task set generaltaskid = " . $res['generaltaskid'] . ", type = " . $res['type'] . ", target = '" . $res['target'] . "', title = '" . $target['title'] . "', fromdate = '" . $res['fromdate'] . "', handledate = '" . $res['handledate'] . "', investment = '" . $target['investment'] . "', creater = '" . $username . "', createtime = '" . $time . "'" . $where . ", status = 3";
		$taskid = $mLink->insert($sql2);
		if($taskid){
			$progressList = $target['progress'];
			if(is_array($progressList) && count($progressList) > 0){
				foreach($progressList as $progress){
					$sql3 = "insert into progress set taskid = " . $taskid . ", stage = '" . $progress['stage'] . "', startdate = '" . $progress['startdate'] . "', enddate = '" . $progress['enddate'] . "', creater = '" . $username . "', createtime = '" . $time . "'";
					$result = $mLink->insert($sql3);
					if(!$result){
						return "添加台账失败！";
					}
				}
			}
			//转办
			$sql4 = "select deptid, ishead from targetrecv where targetid = " . $targetid;
			$deptList = $mLink->getAll($sql4);
			if(is_array($deptList) && count($deptList) > 0){
				foreach($deptList as $dept){
					$sql5 = "insert into taskrecv set taskid = " . $taskid . ", deptid = " . $dept['deptid'] . ", pubtime = '" . $time . "', ishead = " . $dept['ishead'];
					$mLink->insert($sql5);
				}
			}
		}else{
			return "添加台账失败！";
		}
	}
	return "添加台账成功";
}

function get_dept_list($targetid, $key){
	$sql = "select d.deptId,d.deptName,t.ishead,t.status from dept d left join targetrecv t on d.deptId = t.deptid and t.targetid = " . $targetid . " where d.areaCode = " . $key . " order by d.deptId";
	$mLink = new mysql;
	$result = $mLink->getAll($sql);
	$mLink->closelink();
	return $result;
}

function get_head_dept_list($targetid){
	$mLink = new mysql;
	$sql = "select t.ishead, t.deptid, d.deptName from targetrecv t, dept d where t.deptid = d.deptId and t.targetid = " . $targetid . " order by t.deptid"; 

	$res = $mLink->getAll($sql);
	$mLink->closelink();
	return $res;
}

function get_regbacktype_by_targetid($targetid){
	$mLink = new mysql;
	$sql = "select onbacktime, regbacktype from p_target where id = " . $targetid;
	$res = $mLink->getRow($sql);
	$mLink->closelink();
	return $res;
}

//台账填报
function querytargetlist($deptid, $where){
	$mLink = new mysql;
	$tasklist=array();
	$sqstr='SELECT id,name FROM generaltask ORDER BY id';
	$generaltasklist=$mLink->getAll($sqstr);
	$sqstr="SELECT a.id,a.generaltaskid,a.type,b.type as typename, a.target, a.fromdate, a.handledate FROM p_target a left join type b on a.type = b.id WHERE a.id in (SELECT targetid FROM targetrecv WHERE deptid=$deptid)" . $where;
	$targetlist=$mLink->getAll($sqstr);	
	if(count($targetlist)>0){
		$i=0;
		foreach($targetlist as $t){
			$sqstr="SELECT id,deptid,targetid,title,investment FROM p_task WHERE targetid=".$t['id'];
			$tasklist=$mLink->getAll($sqstr);
			$sql = 'select deptname from dept where deptid in (SELECT deptid FROM targetrecv WHERE ishead=1 AND targetid=?)';
			$deptnames=$mLink->getAll($sql,array($t["id"]));
			if(count($deptnames)>0){	
				$tmp= "<b>牵头单位:</b>";
				$count1 = 0;
				foreach ($deptnames as $row) {
					if($count1 == 0){
						$tmp .= $row["deptname"];
					}else{
						$tmp .= ',' . $row["deptname"];
					}
					$count1++;
				}		
			}			
			$sql = "select deptname from dept where deptid in (select deptid from targetrecv where  ishead=0 and targetid=?)";
			$deptnames=$mLink->getAll($sql,array($t["id"]));
			if(count($deptnames)>0){	
				$tmp=$tmp."<br /><b>责任单位:</b>";
				$count2 = 0;
				foreach ($deptnames as $row) {
					if($count2 == 0){
						$tmp .= $row["deptname"];
					}else{
						$tmp .= ',' . $row["deptname"];
					}
					$count2++;
				}			
			}
			$targetlist[$i]['depts']=$tmp;
			$taskcount=count($tasklist);		
			$targetlist[$i]['taskcount']=$taskcount;
			if($taskcount>0){
				$targetlist[$i]['tasklist']=$tasklist;
			}else{
				$targetlist[$i]['tasklist']='';
			}
			$i++;
		}

		$rowid=1;
		foreach($generaltasklist as $g){
			foreach($targetlist as $target){
				if($target['generaltaskid'] == $g['id']){	
					echo '<tr><td align="center" colspan="9" class="table_title">'.$g['name'].'</td></tr>';
					echo '<tr class="alternate_line1">';				
					if($target['taskcount']>0){
						echo '<td style="display:none" align="center"  rowspan="'.$target['taskcount'].'">'.$target['typename'].'</td>';	
						echo '<td  align="center"  rowspan="'.$target['taskcount'].'">'.$rowid.'</td>';	
						echo '<td align="center"  rowspan="'.$target['taskcount'].'">'.$target['target'].'</td>';
						echo '<td align="center"  rowspan="'.$target['taskcount'].'">'.$target['fromdate'].'</td>';
						echo '<td align="center"  rowspan="'.$target['taskcount'].'">'.$target['handledate'].'</td>';
						echo '<td style="text-align:left;" rowspan="'.$target['taskcount'].'">'.$target['depts'].'</td>';	
						echo '<td  align="center" rowspan="'.$target['taskcount'].'"><button id="'.$target['id'].'" type="button" style="cursor:pointer" class="button1" onclick="addtitle(this);">添  加</button></td>';
						for($j=0;$j<count($target['tasklist']);$j++){
							if($j>0) echo '<tr class="alternate_line1">';
							echo '<td name="title" id="title' . $target['tasklist'][$j]['id'] . '" onclick="do_edit(\'title' . $target['tasklist'][$j]['id'] . '\')">' . $target['tasklist'][$j]['title'].'</td>';
							echo '<td align="center">'.$target['tasklist'][$j]['investment'].'</td>';
							echo '<td align="center"><button id="'.$target['tasklist'][$j]['id'].'" type="button" style="cursor:pointer" class="button1" onclick="inputprogress(this);">填  报</button></td>';
							if($j>0) echo '</tr>';
						}	
					}else{
						echo '<td style="display:none" align="center">'.$target['typename'].'</td>';	
						echo '<td align="center">'.$rowid.'</td>';	
						echo '<td align="center">'.$target['target'].'</td>';
						echo '<td align="center">'.$target['fromdate'].'</td>';
						echo '<td align="center">'.$target['handledate'].'</td>';
						echo '<td style="text-align:left;">'.$target['depts'].'</td>';
						echo '<td align="center"><button id="'.$target['id'].'" type="button" style="cursor:pointer" class="button1" onclick="addtitle(this);">添  加</button></td>';
						echo '<td></td>';
						echo '<td></td>';
						echo '<td align="center"><button id="t'.$target['id'].'" type="button" style="cursor:pointer" class="button1" onclick="inputprogress(this);">填  报</button></td>';
					}					
					echo '</tr>';
					$rowid++;	
				}
			}
		}
	}else{
		echo '<tr class="alternate_line1"><td colspan="9" align="center"><font size="2">没有符合条件的纪录</font></td></tr>';
	}
}