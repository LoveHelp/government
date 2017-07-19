<?php

session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
ini_set('date.timezone','Asia/Shanghai');
header("Content-type:text/html;charset=utf-8");
//$type=1;
//$name="Excel导入";
//$excelfilename='ImportExcel\\2016.xls';
if(isset($_POST['type']))
{
	$type=$_POST["type"];
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
$li=array("id"=>"","target"=>"","title"=>"","investment"=>"","stage"=>"","startdate"=>"","enddate"=>"","depts"=>"");
for($i=1;$i<=5;$i++)
	{
	for($j=0;$j<=10;$j++)
		{
		$val=$sheet->getCellByColumnAndRow($j, $i)->getValue();
		if(preg_match("/系统内编号/", $val)){$li["id"]=$j;}
		if(preg_match("/工作目标/", $val)) {$li["target"]=$j;}
		if(preg_match("/支撑项目/", $val)) {$li["title"]=$j;}
		if(preg_match("/年度投资/", $val)) {$li["investment"]=$j;}
		if(preg_match("/工作标准/", $val)) {$li["stage"]=$j;}
		if(preg_match("/启动\s*时间/", $val)) {$li["startdate"]=$j;}
		if(preg_match("/完成\s*时间/", $val)) {$li["enddate"]=$j;$start=$i+1;}
		if(preg_match("/责任主体/", $val)) {$li["depts"]=$j;}	
		}		
	}
	if(empty($li["id"])) $li["id"]=100;
	foreach($li as $e){	if(empty($e)){echo "表格式不正确,请检查是否包括序号、工作目标、支撑项目、年度投资、工作标准、启动时间、完成时间、责任主体这8列。";	exit;}}
	//echo "start=$start";
//判断导入表最后5行是否存在"备注:",如存在,去掉备注行,不作为数据导入内容
for($i=0;$i<5;$i++)
	{
		$val=$sheet->getCellByColumnAndRow(0, $highestRow-$i)->getValue();
		if(preg_match("/备注\s*/", $val)) $end=$highestRow-$i-1;				
	}
	//echo "end=$end";
//按行读取数据,判断是否是总体目标的跨行,是的话放入当前的$generaltask,否则检验每行相应的数据合法性.放入二维数组$tasklist,其中工作标准、启动时间、完成时间为子二维数组，放入$tasklist中
$generaltask="";
$tasklist=array();
$inputrow=0;
$task=array();
$progress=array();
$task["type"]=$type;
for($i=$start;$i<=$end;$i++)
	{
		$investment="";
		$stage="";
		$startdate="";
		$enddate="";		
		$val=trim($sheet->getCellByColumnAndRow(0, $i)->getValue());
		$target=trim($sheet->getCellByColumnAndRow($li["target"], $i)->getValue());	
		if(preg_match("/^[\x{4e00}-\x{9fa5}]+/u", $val) && empty($target))	
		{//判断是否为总体任务				
		$task["generaltask"]=$val;	
		continue;		
		//echo $task["generaltask"]."<br>";
		}//不是总体任务	
		$investment=trim($sheet->getCellByColumnAndRow($li["investment"], $i)->getValue());
		$stage=trim($sheet->getCellByColumnAndRow($li["stage"], $i)->getValue());
		$startdate=trim($sheet->getCellByColumnAndRow($li["startdate"], $i)->getValue());
		$enddate=trim($sheet->getCellByColumnAndRow($li["enddate"], $i)->getValue());
		if(empty($startdate) || !preg_match("/^\d{4}\.\d{1,2}\.{0,1}\d{0,2}$/",$startdate)) $startdate="2000-01-01";
		if(empty($enddate) || !preg_match("/^\d{4}\.\d{1,2}\.{0,1}\d{0,2}$/",$enddate)) $enddate="2000-01-01";
		if(preg_match("/^\d{4}\.\d{1,2}$/",$startdate)){$startdate=str_replace('.', '-', $startdate)."-1";}
		if(preg_match("/^\d{4}\.\d{1,2}\.\d{1,2}$/",$startdate)){$startdate=str_replace('.', '-', $startdate);}
		if(preg_match("/^\d{4}\.\d{1,2}$/",$enddate)){$enddate=str_replace('.', '-', $enddate)."-28";}
		if(preg_match("/^\d{4}\.\d{1,2}\.\d{1,2}$/",$enddate)){$enddate=str_replace('.', '-', $enddate);}
		$title=trim($sheet->getCellByColumnAndRow($li["title"], $i)->getValue());
		$depts=trim($sheet->getCellByColumnAndRow($li["depts"], $i)->getValue());
		if(!empty($target))
			{//判断工作目标是否为空,不为空新加一行数据进入$tasklist
				if(count($progress)>0)  $task["progress"]=$progress;				
					$inputrow++;
				if(!empty($task['target'])){
					array_push($tasklist,$task);
					//清除已存入数组$tasklist的数据,保留$task["generaltask"]和$task["target"]的内容
					$progress=array();					
					$task['title']='';$task['investment']='';$task['progress']='';$task['depts']='';
				}									    
				//添加老数据结束,新的一条数据加入读取到的本行内容			
				$task["target"]=$target;
				$task["title"]=$title;				
				$task["depts"]=$depts;		
				$task["investment"]=$investment;
				$task["id"]=trim($sheet->getCellByColumnAndRow($li["id"], $i)->getValue());
				$arr=array("stage"=>$stage,"startdate"=>$startdate,"enddate"=>$enddate);
				if(count($arr)>0) array_push($progress,$arr);					
			}else{//判断工作目标是否为空,为空时判断支撑项目是否存在多个				
				if(!empty($title)){//工作目标为空,支撑项目不为空,再次新增一条数据					
				if(count($progress)>0)  $task["progress"]=$progress;	
				$inputrow++;
				  if(!empty($task['target'])){
					array_push($tasklist,$task);
					//清除已存入数组$tasklist的数据,保留$task["generaltask"]和$task["target"] $task["title"] $task["depts"]的内容
					$progress=array();					
					$task['investment']='';$task['progress']='';
				  }	
				 //添加老数据结束,新的一条数据加入读取到的本行内容					
				$task["title"]=$title;				
				if(!empty($depts)) $task["depts"]=$depts;
				$task["investment"]=$task["investment"].$investment;
				$task["id"]=trim($sheet->getCellByColumnAndRow($li["id"], $i)->getValue());
				$arr=array("stage"=>$stage,"startdate"=>$startdate,"enddate"=>$enddate);
				array_push($progress,$arr);	
				$task["title"]=$title;			
				}else{
				$task["investment"]=$task["investment"].$investment;
				if(!empty($stage)){
				$arr=array("stage"=>$stage,"startdate"=>$startdate,"enddate"=>$enddate);
				array_push($progress,$arr);	
				    }					
				}		
			}
	$task['progress']=$progress;
	if($i==$end) array_push($tasklist,$task);							
	}
//var_dump($tasklist);
echo "读出:".$inputrow."条记录.";
writedata($tasklist,$name);
}
//写入数据库
function writedata($tasklist,$name)
{
	include_once "../mysql.php";
	$link=new mysql;
	//逐条取出数据,
	$inputrow=0;
	foreach($tasklist as $task){
//		//如果读取的数据中id非空,做修改处理,否则做新增处理
		if($task["id"]>0)
		{
		$inputrow++;		
		$updatesql='UPDATE task SET target=?,title=?,investment=?,modifier=?,modtime=now() WHERE id='.$task['id'];
		$link->update($updatesql,array($task['target'],$task['title'],$task['investment'],$name));		
					if(is_array($task["progress"])){
					$prosql='SELECT taskid FROM progress WHERE taskid=?';
					$prores=$link->getAll($prosql,array($task["id"]));
					if(count($prores)>0){$link->query("DELETE FROM progress WHERE taskid=".$task["id"]);}
					foreach($task["progress"] as $progress)
					{
					$inpsql='INSERT INTO progress(taskid,stage,startdate,enddate,modifier,modtime) VALUES (?,?,?,?,?,now())';
					$pres=$link->insert($inpsql,array($task["id"],$progress['stage'],$progress['startdate'],$progress['enddate'],'Excel导入'));
					if($pres==0) echo "{存入".$progress['stage'].$progress['startdate'].$progress['enddate']."失败}";
					}
				}
		}else{
		$gtsql='SELECT id FROM generaltask WHERE name=?';			
		$gtres=$link->getAll($gtsql,array($task["generaltask"]));
		if(count($gtres)>0){
			$generaltaskid=$gtres[0]['id'];
		}else
			{			
			$gtsql='INSERT INTO generaltask(name) VALUES(?)';			
			$generaltaskid=$link->insert($gtsql,array($task["generaltask"]));
			}			
		$resql='SELECT id FROM task WHERE type=? and generaltaskid=? and target=? and title=?';			
		$reres=$link->getAll($resql,array($task['type'],$generaltaskid,$task['target'],$task['title']));
		  if(count($reres)<1)
		  { 
			$insertsql='INSERT INTO task(type,generaltaskid,target,title,investment,modifier) VALUES(?,?,?,?,?,?)';
			//echo $insertsql;
			$id=$link->insert($insertsql,array($task['type'],$generaltaskid,$task['target'],$task['title'],$task['investment'],$name));
			if($id>0){
				$inputrow++;
				if(is_array($task['progress'])){					
					foreach($task['progress'] as $progress)
					{
					$inpsql='INSERT INTO progress(taskid,stage,startdate,enddate,modifier) VALUES (?,?,?,?,?)';
					$pres=$link->insert($inpsql,array($id,$progress['stage'],$progress['startdate'],$progress['enddate'],$name));
					}
				}
			 }
		  }else{
		  		if(is_array($task["progress"])){
		  			$inputrow++;				
					$prosql='SELECT id FROM progress WHERE taskid=?';
					$prores=$link->getAll($prosql,array($reres[0]['id']));
					if(count($prores)>0){$link->query("DELETE FROM progress WHERE taskid=".$reres[0]['id']);}
					foreach($task["progress"] as $progress)
					{
					$inpsql='INSERT INTO progress(taskid,stage,startdate,enddate,modifier,modtime) VALUES (?,?,?,?,?,now())';
					$pres=$link->insert($inpsql,array($reres[0]['id'],$progress['stage'],$progress['startdate'],$progress['enddate'],'Excel导入'));
					if($pres==0) echo "{存入".$progress['stage'].$progress['startdate'].$progress['enddate']."失败}";
					}
				}
		  }
		}
	}


echo "存入:".$inputrow."记录";
}