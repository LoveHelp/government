<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
ini_set('date.timezone','Asia/Shanghai');
header("Content-type:text/html;charset=utf-8");
//$type=1;
$name="Excel导入";
//$excelfilename='ImportExcel\\003.xlsx';
$regbacktype = "1";
$backtime="";
if(isset($_POST['tkType']))
{
	$type=$_POST["tkType"];
	$regbacktype = trim($_POST['regbacktype']);
	$backtime = trim($_POST['backtime']);
	if (!empty($_FILES['file']['name'])){
		if(preg_match("/.xlsx$/", $_FILES['file']['name']))	
		{
			$excelfilename='ImportExcel\\'.date("YmdHis").".xlsx";
		}else
			{
		if(preg_match("/.xls$/", $_FILES['file']['name'])) {
		$excelfilename='ImportExcel\\'.date("YmdHis").".xls";	
		    }	
		else{echo "必须导入Excel表格.";exit;}
		}
		//echo $excelfilename;	
		move_uploaded_file($_FILES['file']['tmp_name'], $excelfilename);
	}else{
		echo "上传失败!";exit;
	}
	readexcel($type,$name,$excelfilename);

}

//readexcel($type,$name,$excelfilename);
/*
 * 读取excel,兼容2003和2007,中文文件名有些问题
 */
function readexcel($type,$name,$excelfilename){
	include_once("Classes/PHPExcel/IOFactory.php");
	if(preg_match("/.xlsx$/", $excelfilename))
	{	
		$reader = PHPExcel_IOFactory::createReader('Excel2007'); 
	}
	else{	
		$reader = PHPExcel_IOFactory::createReader('Excel5'); 
		}
	$PHPExcel = $reader->load($excelfilename); // 文档名称
	$sheet = $PHPExcel->getSheet(0); // 读取第一个工作表(编号从 0 开始)
	$highestRow = $sheet->getHighestRow(); // 取得总行数
	$highestColumn = $sheet->getHighestColumn(); // 取得总列数
	//echo $highestRow.$highestColumn;
	//取前5行,判断导入的表格是否包括8列:"序号,工作目标,支撑项目,工作标准,,时间节点,,责任主体",
	//下边一行是否存在"年度投资,工作标准,启动时间,完成时间";如都存在,视为合法导入格式.否则给出错误提示
	//取出相应列的列标号,取出起始行行标号
	$start=0;//数据起始行
	$end=$highestRow;//备注起始行
	//$li=array("id"=>"","target"=>"","title"=>"","investment"=>"","stage"=>"","startdate"=>"","enddate"=>"","depts"=>"");
	$li=array("target"=>"", "title"=>"", "depts"=>"");
	for($i=1;$i<=4;$i++){
		for($j=0;$j<=10;$j++){
			$val=$sheet->getCellByColumnAndRow($j, $i)->getValue();
			//if(preg_match("/序号/", $val)){$li["id"]=$j;}
			if(preg_match("/工作目标/", $val)) {$li["target"]=$j;}
			//if(preg_match("/支撑\s项目/", $val)) {$li["title"]=$j;}
			//if(preg_match("/年度\s投资/", $val)) {$li["investment"]=$j;}
			//if(preg_match("/工作标准/", $val)) {$li["stage"]=$j;}
			//if(preg_match("/启动\s*时间/", $val)) {$li["startdate"]=$j;}
			//if(preg_match("/完成\s*时间/", $val)) {$li["enddate"]=$j;$start=$i+1;}
			if(preg_match("/支撑项目/", $val)) {$li["title"]=$j;}	
			if(preg_match("/责任主体/", $val)) {$li["depts"]=$j; $start=$i+1;}	
			//if(preg_match("/是否\s完成/", $val)) {$li["isover"]=$j;}	
		}		
	}
	foreach($li as $e){
		if(empty($e)){
			//echo "表格式不正确,请检查是否包括系统内编号、工作目标、支撑项目、年度投资、工作标准、启动时间、完成时间、责任主体、系统内编号、完成情况、是否完成等列。";	
			echo "表格式不正确,请检查是否包括工作目标、支撑项目、责任主体等列。";
			exit;
		}
	}
	//echo "start=$start";
	//判断导入表最后5行是否存在"备注:",如存在,去掉备注行,不作为数据导入内容
	for($i=0;$i<5;$i++)
	{
		$val=$sheet->getCellByColumnAndRow(0, $highestRow-$i)->getValue();
		if(preg_match("/备注\s*/", $val)) $end=$highestRow-$i-1;				
	}
	//echo "end=$end";
	//按行读取数据,判断是否是总体目标的跨行,是的话放入当前的$generaltask,否则检验每行相应的数据合法性.放入二维数组$tasklist,其中工作标准、启动时间、完成时间为子二维数组，放入$tasklist中
	$remarklist=array();
	$gtask="";
	for($i=$start;$i<=$end;$i++)
	{
		$target = trim($sheet->getCellByColumnAndRow($li["target"], $i)->getValue());
		$title = trim($sheet->getCellByColumnAndRow($li["title"], $i)->getValue());
		$depts = trim($sheet->getCellByColumnAndRow($li["depts"], $i)->getValue());
		
		if(!empty($depts))
			$depts = dept_oper($depts);
		//工作目标、支撑项目、责任主体都为空，表明该条数据可能是总体任务，也可能是空数据
		if(empty($target) && empty($title) && empty($depts)){
			$tmp = trim($sheet->getCellByColumnAndRow(0, $i)->getValue());
			if(!empty($tmp) && mb_strlen($tmp)>3)//$tmp的长度小于3，则表明改数据为非法的工体任务，排除
				$gtask = $tmp;
			continue;
		}else if(!empty($title)){//支撑项目不为空
			$lastIndex = sizeof($remarklist)-1;
			if(empty($target)){//工作目标为空，则取前一条台账的工作目标
				$target = $remarklist[$lastIndex]["target"];
			}
			if(empty($depts)){//责任主体为空，则取前一条台账的责任主体（如果这个台账的责任主体为空是正确的，该如何处理）
				$depts=array();
				$depts['header'] = $remarklist[$lastIndex]["header"];
				$depts['depts'] = $remarklist[$lastIndex]["depts"];
			}
		}else if(!empty($target)){
			if(empty($title)){
				$title = $target;
			}
		}

		$data=array();
		$data['gtask'] = $gtask;
		//if(empty($gtask)){
		//	$len = sizeof($remarklist);
		//	$data['gtask'] = $remarklist[$len-1]['gtask'];
		//}
		$data['target'] = $target;
		$data['title'] = $title;
		$data['header'] = $depts['header'];
		$data['depts'] = $depts['depts'];
		
		array_push($remarklist,$data);
	}
	//将数据保存到数据库
	$err_list=array();
	writedata($remarklist, $name, $type, $err_list);

	$msg_head="读出:".count($remarklist)."条记录！<br>";
	$err_count = sizeof($err_list);//错误记录数量
	$count = sizeof($remarklist) - $err_count;//成功记录数量
	$msg_cons = implode(";", $err_list)."<br>";
	$msg = $msg_head ."{$count}条记录导入成功！<br>{$err_count}条记录导入失败！";
	if($err_count > 0){
		$msg .= "失败记录如下：<br>";
		$msg .= $msg_cons;
	}
	echo $msg;
}

//写入数据库
//msg记录导入失败的工作目标
function writedata($remarklist, $name, $type, $err_list)
{
	$pre_gtask="";//前一个总体任务
	$pre_gtaskid=0;//前一个总体任务id;
	include_once "../mysql.php";
	$link=new mysql;
	//逐条取出数据
	$num=0;
	//获取所有的部门信息
	$sql = "select deptid, deptname, areacode from dept order by areacode asc, deptid asc;";
	$dept_arr = $link->getAll($sql);
	$year=date('Y',time());
	$fromdate = "$year-01-01";
	$handledate = "$year-12-31";

	global $regbacktype, $backtime;
	//更新数据库
	foreach($remarklist as $row){
		if($pre_gtask != $row['gtask']){//总体任务和前一个不一样，则插入数据库
			$pre_gtask = $row['gtask'];
			//总体任务存在，则获取id
			$sql = "select id from generaltask where name=:gtask;";
			$param=array(":gtask"=>$row['gtask']);
			$res = $link->getFirst($sql, $param);
			//不存在，则插入并保存id
			if(empty($res)){
				$sql = "insert into generaltask (name) values(:gtask);";
				$pre_gtaskid = $link->insert($sql, $param);
			}
			else
				$pre_gtaskid = $res;
		}
		if(!empty($pre_gtaskid)){//总任务id获取成功，则查询工作目标是否存在，不存在则插入
			unset($param);
			$param = array();
			$target = $row['target'];
			$title = $row['title'];
			$sql = "select id from task where type={$type} and generaltaskid={$pre_gtaskid} and target='{$target}' and title='{$title}';";
			$res = $link->getFirst($sql, $param);
			if(empty($res)){
				if($regbacktype != 3 && empty($backtime))
					$sql = "insert into task(generaltaskid, type, target, title, fromdate, handledate, createtime, modifier, modtime, status, regbacktype) values({$pre_gtaskid}, {$type}, '{$target}', '{$title}', '{$fromdate}', '{$handledate}', now(),'{$name}', now(), 3, '{$regbacktype}');";
				else if(!empty($backtime))
					$sql = "insert into task(generaltaskid, type, target, title, fromdate, handledate, createtime, modifier, modtime, status, onbacktime) values({$pre_gtaskid}, {$type}, '{$target}', '{$title}', '{$fromdate}', '{$handledate}', now(),'{$name}', now(), 3, '{$backtime}');";
				$taskid = $link->insert($sql);
			}else
				$taskid = $res;
				$sql = "update task set status=3 where id={$taskid};";
				$link->update($sql);
			//工作目标插入成功，则插入相应的单位
			if(empty($taskid)){
				$err_list[]= "工作目标：[{$row['target']}]导入失败！<br>";
			}else{
				//获取单位对应的id
				$header = $row['header'];//牵头单位
				InsertToTaskrecv($link, $taskid, $header, $dept_arr, 1);
				$depts = $row['depts'];//责任单位列表
				InsertToTaskrecv($link, $taskid, $depts, $dept_arr, 0);
			}
		}
	}
}
//如果工作目标对应的责任单位存在，则更新ishead标志
//如果不存在，则插入数据
function InsertToTaskrecv($link, $taskid, $depts, $dept_arr, $header){
	if(empty($depts))
		return;
	$arr = explode(";", $depts);
	$len = sizeof($arr);
	for($i=0; $i<$len; $i++){
		$dname = trim($arr[$i]);
		if(empty($dname))
			continue;
		//去除单位名称前的“市”字
		if(mb_strpos($dname, "市") === 0)
			$dname = mb_substr($dname, 1, mb_strlen($dname)-1, "utf-8");
		//部门id数组：一维数组
		$ret = find_deptid($dept_arr, $dname);
		if(empty($ret))
			continue;
		foreach($ret as $row){
			$did = $row;
			if(empty($did))
				continue;
			//清除旧记录
			$sql = "delete from taskrecv where taskid={$taskid} and deptid={$did};";
			$link->query($sql);
			//插入新的记录
			$sql = "insert into taskrecv(taskid, deptid, ishead, pubtime) values({$taskid}, {$did}, {$header}, now());";
			$link->insert($sql);
			
		}
	}
}

function dept_oper($val)
{
	$val = str_replace(" ", ";", $val);//替换半角空格
	$val = str_replace("　", ";", $val);//替换全角空格
	$val = str_replace(array("\r\n", "\r", "\n"), ';', $val);//替换换行符
	
	$val = str_replace("：", ";", $val);
	$val = str_replace(":", ";", $val);
	$arr = explode(";", $val);
	$sIndex = array_search("牵头单位", $arr);
	$eIndex = array_search("责任单位", $arr);
	
	$header = "";
	for($i=$sIndex+1; $i<$eIndex; $i++)
	{
		if(!empty($header))
			$header .= ";";
		$header .= $arr[$i];
	}
	$dept = "";
	for($i=$eIndex+1; $i<sizeof($arr); $i++)
	{
		if(!empty($dept))
			$dept .= ";";
		$dept .= $arr[$i];
	}
	return array("header"=>$header, "depts"=>$dept);
}

function find_deptid($dept_arr, $dname){
	$data = array();
	$res1 = mb_strpos($dname, "各县区");
	$res2 = mb_strpos($dname, "12县区");
	$res3 = mb_strpos($dname, "市直各部门");
	if($res1 !== false){//各县区
		foreach($dept_arr as $row){
			if($row['areacode'] !== 4)
				continue;
			$data[] = $row['deptid'];
		}
	}else if($res2 !== false){//12县区
		foreach($dept_arr as $row){
			if($row['areacode'] !== 4)
				continue;
			$data[] = $row['deptid'];
			if(sizeof($data) == 12)
				break;
		}
	}else if($res3 !== false){
		foreach($dept_arr as $row){
			if($row['areacode'] !== 3)
				continue;
			$data[] = $row['deptid'];
		}
	}else{
		foreach($dept_arr as $row){
			if(match_deptname($row['deptname'], $dname) !== true)
				continue;
			$data[] = $row['deptid'];
			break;
		}
	}
	return $data;
}

//字符串匹配
//方法：
//1、找出长串、短串
//2、取短串的每一个字符，在长串中查找是否存在
//3、存在则保存位置，然后依次按顺序取短串中剩余字符串，并在长串剩余的字符中进行查找
//4、短串在长串中存在，并且顺序一致，则返回TRUE，否则返回FALSE
function match_deptname($source, $target){
	$minlen = mb_strlen($source);
	$maxlen = mb_strlen($target);
	$index = 0;
	if($minlen >= $maxlen){
		$longstr = $source;
		$smallstr = $target;
		$minlen = $maxlen;
	}else{
		$longstr = $target;
		$smallstr = $source;
	}
	
	for($i=0; $i<$minlen; $i++){
		$sub = mb_substr($smallstr, $i, 1);
		$index = mb_strpos($longstr, $sub, $index);
		if($index === false)
			return false;
	}
	return true;
}
