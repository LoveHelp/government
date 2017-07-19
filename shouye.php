<?php
include_once('mysql.php');
header("Content-type:text/html;charset=utf-8");

mainfunc();

function mainfunc(){
	if(isset($_GET['do'])){
		$do = trim($_GET['do']);//pager_taskmanage
		//$where = trim($_POST['where']);
		switch($do){
			case "dc"://办结申请
				$type = trim($_POST['type']);
				$deptid = trim($_POST['deptid']);
				$noticeList = json_decode(get_info_by_type($type,$deptid), true);
				$html = "";
				if(is_array($noticeList) && count($noticeList) > 0){
					$i = 0;
					foreach($noticeList as $key=>$n){
						$i++;
						if($i == 1){
							$class="li_11";
						}else{
							$class="li_22";
						}
						if($type == 1){
							$url = "xinxi/noticedetail.php?id=" . $n['infoId'];
						}else if($type ==2){
							$url = "xinxi/notificationdetail.php?id=" . $n['infoId'];
						}else if($type == 3){
							$url = "xinxi/infodetail.php?id=" . $n['infoId'] . "&type=3";
						}else if($type == 4){
							$url = "xinxi/infodetail.php?id=" . $n['infoId'] . "&type=4";
						}
						$html .= '<li class="' . $class . '"><a href="' . $url . '" target="_blank"><p><span style="float:left;">&nbsp;&nbsp;>&nbsp;&nbsp;' . $n['infoCode'] . " " . $n['infoTitle'] . '</span><span class="s_right">' . $n['addTime'] . '</span></p></a></li>';
					}
				}else{
					$html .= '<p>暂无信息</p>';
				}
				echo $html;
				break;
			default:
				break;
		}
	}
}
function get_menu_list($roleid){
	$mLink = new mysql;
	$arr = $mLink->getAll("select DISTINCT(menuclass)as menu, menuico from menu ORDER BY menuid");

	foreach($arr as $v){
		$sub = $mLink->getAll("select b.menuclass, b.menuurl, b.name from rolemenu a, menu b where a.menuid = b.menuid and a.roleid = " . $roleid . " and b.menuclass = '" . $v['menu'] . "' order by b.menuid");
		$result[] = array(
			'top' => $v['menu'],
			"icon"=> $v['menuico'],
			'sub' => $sub
		);
	}
	return json_encode($result);
}

//获得台账统计
function get_task_by_type($itemtype){
	$mLink = new mysql;
	$where = "";
	if($itemtype != ""){
		$where .= " and type = " . $itemtype;
	}
	$sql = "select count(*) as count from task where status > 0" . $where;
	$total_res = $mLink->getRow($sql);
	$total = $total_res['count'];//总任务数

	$sql = "SELECT count(*) as count FROM (SELECT id,isover FROM (SELECT task.id, taskreview.isover FROM task LEFT JOIN taskreview ON task.id=taskreview.taskid WHERE 1=1" . $where ." ORDER BY isover DESC) AS res1 GROUP BY id) AS res2 WHERE isover = 2";//2:完成
	$complete_res = $mLink->getRow($sql);
	$complete = $complete_res['count'];//总任务完成
	
	$uncomplete = $total - $complete;//未完成
	$res = array(
		"total"			=>		$total,
		"uncomplete"	=>		$uncomplete,
		"complete"		=>		$complete);
	return $res;
}

//首页在线交流列表
function get_messages_by_id($userid){
	$mLink = new mysql;
	$sql = "select a.id, a.content, a.time, b.UNAME as uname from message a left join user b on a.fromuser = b.uid where a.fromuser = " . $userid . " or a.touser = " . $userid . " or a.touser = 0 order by id desc";
	$res = $mLink->getAll($sql);
	if($res){
		return $res;
	}
}

//督查通知、督查通报、督查动态、督查文件
function get_info_by_type($type,$deptid){
	$mLink = new mysql;
	$sql = "select * from information where infoType = " .$type;
//	if($type==1){
//		$sql.=" and recvDeptIds REGEXP '^".$deptid."$|^".$deptid.",|,".$deptid.",|,".$deptid."$'";
//	}
	
	//$sql .= " order by infoId desc limit 0,5";
	$sql .= " order by infoId desc";
	$res = $mLink->getAll($sql);
	if($res){
		return json_encode($res);
	}
}

//取最新的五张照片
/*function getimgsfromdir(){
	//返回值
    $fileinfo=array();

    //取照片的目录
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/ueditor/php/upload/image/';
    //非文件夹直接跳出
    if(!is_dir($uploadDir))
        return json_encode($fileinfo);
    //
    $targetdir = dir($uploadDir);  
	$latestdir = "";
	while($file = $targetdir->read())
	{
        clearstatcache();
		if((is_dir("$uploadDir/$file")) or ($file==".") or ($file==".."))
			$latestdir = $file;
            continue;
	} 
	$targetdir = dir($uploadDir . $latestdir . "/");
	$count = 0;
	while($file2 = $targetdir->read())
	{
		
        clearstatcache();
		if((is_dir("$uploadDir/$file2")) or ($file2 == ".") or ($file2 == ".."))
            continue;
		$fileinfo[] = $latestdir . "/" . iconv('gb2312', 'UTF-8', $file2);
		$count++;
		if($count >= 5)
			break;
	} 
	$targetdir->close();     
    return json_encode($fileinfo);
}*/

//首页浮动窗数据
function floatdiv(){
	$mLink = new mysql;
	$sql = "select infoId,infoType,infoTitle,addTime from information order by infoId desc limit 3";
	$res = $mLink->getAll($sql);
	if($res){
		return $res;
	}
}

function getimgsfromdir(){
	//返回值	
    $fileinfo=array();  
    $mLink=new mysql;
	$sql='select infoId,infoTitle,infoType,infoContent from information where infoType=3 order by infoId desc';
	$imgsrc='/\/ueditor\/php\/upload\/image\/.*?jpg/';
    $infos=$mLink->getAll($sql);
	foreach($infos as $info){
		$array=array();
		preg_match_all($imgsrc,$info['infoContent'],$arr);
	    foreach($arr[0] as $a){
			array_push($fileinfo,$a);
			}		
		if(count($fileinfo)>4) break;
	}	
    return json_encode($fileinfo);
}

?>