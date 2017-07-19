<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
ini_set('date.timezone','Asia/Shanghai');
header("Content-type:text/html;charset=utf-8");
getdata();
function getdata()
{	//查询出电话薄数据
	include_once "../mysql.php";
	$link=new mysql;
	$sql="SELECT telbook.id,telbook.name,telbook.tel,dept.deptName,telbook.weixin FROM telbook LEFT JOIN dept ON telbook.deptid=dept.deptId ORDER BY dept.deptSort";
	$tellist=$link->getall($sql);	

	export($tellist);	
}	
/*
 * array(1) { [0]=> array(5) { ["id"]=> int(1) ["name"]=> string(9) "赵无极" ["tel"]=> string(11) "13723007095" ["deptName"]=> string(9) "综合科" ["weixin"]=> string(11) "13723007095" } }
 * * 
 */
function export($tellist)
{	
	include_once("../taizhang/Classes/PHPExcel.php");
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
	$midrow=(int)(count($tellist)/2);	
	for($i=ord("A");$i<=ord("I");$i++)
	{
	$Sheet->getStyle(chr($i))->getFont()->setName('仿宋_GB2312')
								 ->setSize(10); 
	$Sheet->getColumnDimension(chr($i))->setWidth(12);	
	}							 								 
	$Sheet->getStyle('A1')->getFont()->setName('黑体')
									->setSize(26);	
	$Sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
											->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);							
	$Sheet->getRowDimension('1')->setRowHeight(63);
	$Sheet->getRowDimension('2')->setRowHeight(30);	
	//设定头部	
	$Sheet->setCellValue("A1",date('Y')."督查管理系统通讯录")
				->mergeCells("A1:I1")
				->setCellValue("A2","部门")		
				->setCellValue("B2","姓名")				
				->setCellValue("C2","电话")				
				->setCellValue("D2","微信")			
				->setCellValue("E2","")
				->setCellValue("F2","部门")			
				->setCellValue("G2","姓名")
				->setCellValue("H2","电话")
				->setCellValue("I2","微信")				
				->setTitle('督查通讯录');
	$startrow=2;			
	if(count($tellist)>0)
	{
		//输出电话号码		
	  foreach($tellist as $t)
	  {
			$startrow=$startrow+1;	
			if($startrow<=$midrow+3)	
			{				
			$Sheet->setCellValue('A'.$startrow,$t["deptName"])
				  ->setCellValue('B'.$startrow,$t["name"])
				  ->setCellValue('C'.$startrow,$t["tel"])
				  ->setCellValue('D'.$startrow,$t["weixin"]);	
			}else{
			$Sheet->setCellValue('F'.($startrow-$midrow-1),$t["deptName"])
				  ->setCellValue('G'.($startrow-$midrow-1),$t["name"])
				  ->setCellValue('H'.($startrow-$midrow-1),$t["tel"])
				  ->setCellValue('I'.($startrow-$midrow-1),$t["weixin"]);		
			}	
	  }
	  $me="E2:E".($midrow+3);	
	  $Sheet->mergeCells($me); 
	//设置所有输出数据的边框
	for($l=ord("A");$l<=ord("I");$l++)
		{
			for($r=2;$r<=$midrow+3;$r++)
			{
				$lr=chr($l).$r;
				$Sheet->getStyle($lr)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);	
		
			}
		}

	}				
	$objWriter = PHPExcel_IOFactory::createWriter($Excel, 'Excel2007');		
	$name = 'Exporttel/tel.xlsx';	
	$execlName=dirname(__FILE__).'\Exporttel\\tel.xlsx';	
	$Sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objWriter->save($execlName);
	echo $name;
}