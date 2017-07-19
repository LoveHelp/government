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
$li=array("id"=>"","target"=>"","title"=>"","investment"=>"","stage"=>"","startdate"=>"","enddate"=>"","depts"=>"");
for($i=1;$i<=3;$i++)
	{
	for($j=0;$j<=10;$j++)
		{
		$val=$sheet->getCellByColumnAndRow($j, $i)->getValue();
		if(preg_match("/系统内编号/", $val)){$li["id"]=$j;}
		if(preg_match("/工作目标/", $val)) {$li["target"]=$j;}
		if(preg_match("/支撑\s项目/", $val)) {$li["title"]=$j;}
		if(preg_match("/年度\s投资/", $val)) {$li["investment"]=$j;}
		if(preg_match("/工作标准/", $val)) {$li["stage"]=$j;}
		if(preg_match("/启动\s*时间/", $val)) {$li["startdate"]=$j;}
		if(preg_match("/完成\s*时间/", $val)) {$li["enddate"]=$j;$start=$i+1;}
		if(preg_match("/责任\s主体/", $val)) {$li["depts"]=$j;}	
		if(preg_match("/完成\s情况/", $val)) {$li["remark"]=$j;}	
		if(preg_match("/是否\s完成/", $val)) {$li["isover"]=$j;}	
		}		
	}	
	foreach($li as $e){	if(empty($e)){echo "表格式不正确,请检查是否包括系统内编号、工作目标、支撑项目、年度投资、工作标准、启动时间、完成时间、责任主体、系统内编号、完成情况、是否完成等列。";	exit;}}
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
for($i=$start;$i<=$end;$i++)
	{
		$id='';
		$remark='';
		$isover=1;
		$isoverstr='';
		$id=trim($sheet->getCellByColumnAndRow($li["id"], $i)->getValue());
		if(empty($id)) continue;
		$remark=trim($sheet->getCellByColumnAndRow($li["remark"], $i)->getValue());
		$isoverstr=trim($sheet->getCellByColumnAndRow($li["isover"], $i)->getValue());		
		if(preg_match("/完成/", $isoverstr)) $isover=3;
		if(preg_match("/基本\s完成/", $isoverstr)) $isover=2;
	$remarkarr=array('taskid'=>$id,'userid'=>$_SESSION['userID'],'remark'=>$remark,'isover'=>$isover);		
	array_push($remarklist,$remarkarr);							
	}
echo "读出:".count($remarklist)."条记录.";
writedata($remarklist,$name);
}
//写入数据库
function writedata($remarklist,$name)
{
	include_once "../mysql.php";
	$link=new mysql;
	//逐条取出数据,	
	foreach($remarklist as $remarkarr){
		$sql='insert into taskreview(taskid,userid,remark,isover,viewtime) values(?,?,?,?,?)';
		$param=array($remarkarr['taskid'],$remarkarr['userid'],$remarkarr['remark'],$remarkarr['isover'],date('Y-m-d'));
		$link->insert($sql,$param);
	}	
//	
echo "存入:".count($remarklist)."记录";
}