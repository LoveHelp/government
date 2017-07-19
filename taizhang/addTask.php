<?php
include_once "../mysql.php";
//header("Content-type:text/html;charset=utf-8");
$id = $_POST['id'];
$type = $_POST['type'];
$fromcompany = $_POST['fromcompany'];
$fromdate = $_POST['fromdate_str'];
$handledate = $_POST['handledate_str'];
$generaltaskid = $_POST['generaltaskid'];
$title = $_POST['title'];
$target = $_POST['target'];
$investment = $_POST['investment'];
$postilleader = $_POST['postilleader'];
$headleader = $_POST['headleader'];
$handleperson = $_POST['handleperson'];
$creater = $_POST['creater'];
$createtime = $_POST['createtime'];
$status = 1;
$progressList = $_POST['list'];
$delete = explode(",", $_POST['delete']);

$mLink = new mysql;
/*$g_sql = "select * from generaltask where name = '" . $generaltask . "'";
$g_res = $mLink->getRow($g_sql);
if($g_res){
	$generaltaskid = $g_res['id'];
}else{	
	$generaltaskid = $mLink->insert("insert into generaltask (name) value ('" . $generaltask . "')");
}*/
if($id == ''){
	$sql = "insert into task (type, fromcompany, fromdate, handledate, generaltaskid, title, target, investment, postilleader, headleader,handleperson, creater, createtime, status) values (".$type.",'".$fromcompany . "', '" . $fromdate . "', '" . $handledate . "', " . $generaltaskid . ", '" . $title . "', '" . $target . "', '" . $investment . "', '" . $postilleader . "', '" . $headleader . "', '" . $handleperson . "', '" . $creater . "', '" . $createtime . "'," . $status .")";

	$res = $mLink->insert($sql);

	if($res){
		foreach($progressList as $progress){
			$p_sql = "insert into progress(taskid, stage, startdate, enddate, creater, createtime, status) values (" . $res . ", '" . $progress["stage"] . "', '" . $progress["startdate"] . "', '" . $progress["enddate"] . "', '" . $creater . "', '" . $createtime . "', 1)";
			$res2 = $mLink->insert($p_sql);
		}
		echo '<script>window.parent.close();</script>'; 
	}

}else{
	$sql = "update task set type=" . $type . ", fromcompany='" . $fromcompany . "', fromdate='" . $fromdate . "', handledate='" . $handledate . "', generaltaskid=" . $generaltaskid . ", title='" . $title . "', target='" . $target . "', investment='" . $investment . "', postilleader='" . $postilleader . "', headleader='" . $headleader . "', handleperson='" . $handleperson . "', modifier='" . $creater . "', modtime='" . $createtime . "' where id=" . $id;
	$res = $mLink->update($sql);
	$current = array();
	foreach($progressList as $progress){
		
		if($progress["type"] == "0"){
			$sql1 = "insert into progress(taskid, stage, startdate, enddate, creater, createtime, status) values (" . $id . ", '" . $progress["stage"] . "', '" .			$progress["startdate"] . "', '" . $progress["enddate"] . "', '" . $creater . "', '" . $createtime . "', 1)";
			
			$mLink->insert($sql1);
		}else{
			$current[] = $progress["id"];
			$sql2 = "update progress set stage='" . $progress["stage"] . "', startdate='" . $progress["startdate"] . "',  enddate='" . $progress["enddate"] . "', modifier='" . $creater . "', modtime='" . $createtime . "', status=1 where id=" . $progress["id"];
			$mLink->update($sql2);
		}
	}

	if(is_array($delete)){
		foreach($delete as $d){
			if(!in_array($d, $current)){
				$sql3 = "update progress set status = 0 where id=" . $d;
				$mLink->update($sql3);
			}
		}
	}
	if($res){
		echo '<script>window.parent.close();</script>'; 
	}
}


