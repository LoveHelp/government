<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
ini_set('date.timezone','Asia/Shanghai');
header("Content-type:text/html;charset=utf-8");
include_once "../constant.php";
//$type=1;
if(isset($_GET["type"])) getdata($_GET["type"]);
function getdata($type)
{	
	include_once "../mysql.php";
	$link=new mysql;
	//查询总体任务	
	$sql="SELECT generaltask.id,generaltask.name FROM generaltask  JOIN task ON generaltask.id=task.generaltaskid where type=$type GROUP BY generaltask.id";
	$generaltasklist=$link->getall($sql);
	//var_dump($generaltasklist);
	if($type==1){//重点工作的启动时间和完成时间精确到月,其他类型精确到天
	$datestr="DATE_FORMAT(progress.startdate,'%Y.%c') AS startdate,DATE_FORMAT(enddate,'%Y.%c') AS enddate";
	}else{
	$datestr="DATE_FORMAT(progress.startdate,'%Y.%c.%e') AS startdate,DATE_FORMAT(enddate,'%Y.%c.%e') AS enddate";
	}
	$tasklist=array();	
	if($generaltasklist>0)
	{	
	foreach($generaltasklist as $g)
		{
	//查询每个总体目标包含的工作目标
	$mydept=$_SESSION['userDeptID'];	
	$sql="SELECT task.type,task.id,task.target,task.title,task.investment FROM task LEFT JOIN taskrecv ON task.id=taskrecv.taskid WHERE task.generaltaskid='".$g["id"]."' AND taskrecv.deptid=$mydept AND task.type=".$type." ORDER BY task.id";
	$tlist=$link->getall($sql);	
	//var_dump($tlist);
	//echo "<br/><br/>";
	if($tlist>0)
	{
		foreach($tlist as $t){
			//查询每个工作目标包含的工作标准条数
			$sql="SELECT count(taskid) FROM progress WHERE taskid='".$t["id"]."'";
			$li=$link->getall($sql);
			$tmp="";
			$sql = "select deptname from dept where deptid in (select deptid from taskrecv where ishead=1 and taskid=?)";
			$deptnames=$link->getall($sql,array($t["id"]));
			if(count($deptnames)>0)
			{	
			$tmp= "牵头单位:\n";
			foreach ($deptnames as $row) {
			$tmp= $tmp.$row["deptname"]."\n";
				}
			$tmp=substr($tmp, 0,strlen($tmp));
			}			
			$sql = "select deptname from dept where deptid in (select deptid from taskrecv where  ishead=0 and taskid=?)";
			$deptnames=$link->getall($sql,array($t["id"]));
			if(count($deptnames)>0)
			{	
			$tmp=$tmp."责任单位:\n";
			foreach ($deptnames as $row) {
			$tmp= $tmp.$row["deptname"]."\n";
				}
			$tmp=substr($tmp, 0,strlen($tmp));
			}	
			$sql = "SELECT task.id,progress.stage,$datestr FROM progress LEFT JOIN task on task.id=progress.taskid WHERE progress.taskid=?";		
			
			$progresslist=$link->getall($sql,array($t["id"]));		
			$a=array(
			"depts"=>$tmp,
			"li"=>$li[0]["count(taskid)"],
			"type"=>$t["type"],
			"generaltaskid"=>$g["id"],
			"id"=>$t["id"],
			"target"=>$t["target"],
			"title"=>$t["title"],
			"investment"=>$t["investment"],
			"progresslist"=>$progresslist);
			array_push($tasklist,$a);			
		}
				
	}
	
		}
	}
	//var_dump($generaltasklist);
	export($type,$generaltasklist,$tasklist);	
}	
/*
 * $type台账类型  $type=1
 * $generaltasklist(二维数组) 主体任务 array(1) { [0]=> array(2) { ["generaltask"]=> string(36) "社会消费品零售总额增长12%" ["count(task.generaltask)"]=> int(4) } }
 * $tasklist(二维数组)  台账数据array(1) { [0]=> array(6) { ["generaltask"]=> string(36) "社会消费品零售总额增长12%" ["id"]=> int(5) ["target"]=> string(36) "社会消费品零售总额增长12%" ["title"]=> string(36) "社会消费品零售总额增长12%" ["investment"]=> string(4) "1600" ["count(task.id)"]=> int(4) } }
 */
function export($type,$generaltasklist,$tasklist)
{
	global $task_type;
	include_once("Classes/PHPExcel.php");
	$Excel=new PHPExcel;
	$Excel->getProperties()->setCreator("Duchashi")
							 ->setLastModifiedBy("Duchashi")
							 ->setTitle("officeExcel2007")
							 ->setSubject("Document")
							 ->setDescription("officeExcel2007")
							 ->setKeywords("officeExcel2007")
							 ->setCategory("officeExcel2007");
	$Excel->setActiveSheetIndex(0);
	$Sheet=$Excel->getActiveSheet();
	for($i=ord("A");$i<=ord("H");$i++)
	{
	$Sheet->getStyle(chr($i))->getFont()->setName('仿宋_GB2312')
								 ->setSize(10);
	$row1=chr($i)."2";
	$row2=chr($i)."3";
	$Sheet->getStyle($row1)->getFont()->setName('黑体')
										->setBold(true);
	$Sheet->getStyle($row1)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$Sheet->getStyle($row2)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);								
	$Sheet->getStyle($row1)->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
	$Sheet->getStyle($row2)->getFont()->setName('黑体')
										->setBold(true);	
	$Sheet->getStyle($row2)->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);				 
	}						 								 
	$Sheet->getStyle('A1')->getFont()->setName('方正小标宋简体')
									->setSize(26);	
	$Sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
											->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);							
	$Sheet->getColumnDimension("A")->setWidth(4);
	$Sheet->getColumnDimension("B")->setWidth(18);
	$Sheet->getColumnDimension("C")->setWidth(21);
	$Sheet->getColumnDimension("D")->setWidth(9.5);
	$Sheet->getColumnDimension("E")->setWidth(37);
	$Sheet->getColumnDimension("F")->setWidth(10);
	$Sheet->getColumnDimension("G")->setWidth(10);
	$Sheet->getColumnDimension("H")->setWidth(12);
	$Sheet->getColumnDimension("I")->setWidth(0);		
	$Sheet->getRowDimension('1')->setRowHeight(63);
	$Sheet->getRowDimension('2')->setRowHeight(30);
	$Sheet->getRowDimension('3')->setRowHeight(30);
	
	//设定头部	
	$Sheet->setCellValue("A1",date('Y')."年".$task_type[$type]."推进落实清单")
				->mergeCells("A1:H1")
				->setCellValue("A2","序号")
				->mergeCells("A2:A3")
				->setCellValue("B2","工作目标")
				->mergeCells("B2:B3")
				->setCellValue("C2","支撑项目")
				->mergeCells("C2:C3")
				->setCellValue("D2","工作标准")
				->mergeCells("D2:E2")
				->setCellValue("D3","年度投资\n(元)")
				->setCellValue("E3","工作标准")
				->setCellValue("F2","时间节点")
				->mergeCells("F2:G2")
				->setCellValue("F3","启动\n时间")
				->setCellValue("G3","完成\n时间")
				->setCellValue("H2","责任主体")
				->mergeCells("H2:H3")	
				->setCellValue("I2","系统内编号")
				->mergeCells("I2:I3")				
				->setTitle('督查台账');
	$startrow=3;
	$rowid=0;		
	if(count($generaltasklist)>0)
	{
		//输出台账总体目标--跨行
		
	  foreach($generaltasklist as $v)
	  {
			$startrow=$startrow+1;
			$firstli="A".$startrow;
			$endli="H".$startrow;
			//echo $v["generaltask"];
			$Sheet->setCellValue($firstli,$v["name"])
				  ->mergeCells("$firstli:$endli");	
			$Sheet->getStyle($firstli)->getFont()->setBold(true);
			$Sheet->getStyle($firstli)->getAlignment()->setWrapText(true)
													  ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
													  ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
			$Sheet->getRowDimension($startrow)->setRowHeight(33);
			$Sheet->getStyle($firstli)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		//输出台账工作目标--跨列

		if(count($tasklist)>0)
		{
		
			foreach($tasklist as $t)
			{						
				if($t["generaltaskid"]==$v["id"])
				{				
				$firstrowA="A".($startrow+1);
				$firstrowB="B".($startrow+1);				
				$firstrowC="C".($startrow+1);				
				$firstrowD="D".($startrow+1);				
				$firstrowH="H".($startrow+1);
				$firstrowI="I".($startrow+1);
				if($t["li"]>1){
				$endrowA="A".($startrow+$t["li"]);
				$endrowB="B".($startrow+$t["li"]);
				$endrowC="C".($startrow+$t["li"]);
				$endrowD="D".($startrow+$t["li"]);
				$endrowH="H".($startrow+$t["li"]);
				$endrowI="I".($startrow+$t["li"]);
				$Sheet->mergeCells("$firstrowA:$endrowA")
					  ->mergeCells("$firstrowB:$endrowB")
					  ->mergeCells("$firstrowC:$endrowC")
					  ->mergeCells("$firstrowD:$endrowD")
					  ->mergeCells("$firstrowH:$endrowH")
					  ->mergeCells("$firstrowI:$endrowI");					  
							  }
				$rowid++;
				$Sheet->setCellValue($firstrowA,$rowid)				  	 
					 ->setCellValue($firstrowB,$t["target"])				  	 
					 ->setCellValue($firstrowC,$t["title"])				  	
					 ->setCellValue($firstrowD,$t["investment"])				  	 
					 ->setCellValue($firstrowH,$t["depts"])
					 ->setCellValue($firstrowI,$t["id"]);
					 //设置自动换行及垂直居中
				$Sheet->getStyle($firstrowA)->getAlignment()->setWrapText(true)
															->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$Sheet->getStyle($firstrowB)->getAlignment()->setWrapText(true)
															->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$Sheet->getStyle($firstrowC)->getAlignment()->setWrapText(true)
															->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$Sheet->getStyle($firstrowD)->getAlignment()->setWrapText(true)
															->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				
				$Sheet->getStyle($firstrowH)->getAlignment()->setWrapText(true)
															->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					 //输出进度	
					 if(count($t["progresslist"])>0)				
					 foreach($t["progresslist"] as $p)
					 {
					 	
					 		$startrow=$startrow+1;
					 		$prowE="E".$startrow;
							$prowF="F".$startrow;
							$prowG="G".$startrow;
					 		$Sheet->setCellValue($prowE,$p["stage"])
					 			  ->setCellValue($prowF,$p["startdate"])
								  ->setCellValue($prowG,$p["enddate"]);
					 		$Sheet->getStyle($prowE)->getAlignment()->setWrapText(true)
													  ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					 		$Sheet->getStyle($prowF)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$Sheet->getStyle($prowG)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
													  
					 	
					 }else{$startrow=$startrow+1;}	
				}
			}
		}
	  }
	//设置所有输出数据的边框
	for($l=ord("A");$l<=ord("H");$l++)
		{
			for($r=4;$r<=$startrow;$r++)
			{
				$lr=chr($l).$r;
				$Sheet->getStyle($lr)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);	
		
			}
		}

	}			
				
	$objWriter = PHPExcel_IOFactory::createWriter($Excel, 'Excel2007');	
	
	$name = 'ExportExcel/'.date('Y').date('mdHis').$task_type[$type].'台账'.'.xlsx';	
	$execlName=dirname(__FILE__).'\ExportExcel\\'.date('Y').date('mdHis').iconv('UTF-8','GB2312',$task_type[$type]).iconv('UTF-8','GB2312','台账').'.xlsx';	
	$Sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objWriter->save($execlName);
	echo $name;
}