<?php
error_reporting(0);//关闭提示
include_once "../mysql.php";
include_once "attachment.php";
header("Content-type:text/html;charset=utf-8");

mainFunc();

function mainFunc(){
	$do= trim($_GET['do']);
	if($do=="attach_delete"){//进入修改页面时，获取初始绑定值
		$attachId = trim($_POST['attachId']);
		$infoId=$_POST["infoId"];
		del_attachByAttachId($attachId);
		$result = json_encode(get_attachList($infoId));
	}else{
		$result = uploadAttachment();
	}
	echo $result;
}

/** 
 *  - Ajax提交验证 
 */  
function uploadAttachment(){ 
	$result=array();  
    //设置图片要上传保存到服务器的绝对路径  
    $path = $_SERVER['DOCUMENT_ROOT'].'/government/upload/info/notice/'; 
    //文件显示的路径  
    $showPath = '';  
    if(isset($_FILES['upfile'])){  
        //若上传错误，则弹出错误id  
        if($_FILES['upfile']['error'] == 0){   
        	$FileName = $_FILES['upfile']['name'];   
        	$savePath    = $path.$FileName;  //图片的存储路径  
            $showPath    = '/government/upload/info/notice/'.$FileName;   
            //move_uploaded_file($_FILES['upfile']['tmp_name'], $savePath);  
            move_uploaded_file($_FILES["upfile"]["tmp_name"], iconv("UTF-8", "gb2312", $savePath));
			$infoId=$_POST["infoId"];
			if($infoId==""){//添加通知时
				$result=array(
					'attachName'=>$FileName,
					'attachUrl'=>$showPath
				);
			}else{
				if(insert_attachment($infoId,$FileName,$showPath)>0){
	            	$result=get_attachList($infoId);
	            }
			}
            
        } 
    } 
       
    return json_encode($result);
} 

//上传附件
function insert_attachment($infoId,$FileName,$showPath){
	$sql="insert into attachment(infoId,attachName,attachUrl) values(".$infoId.",'".$FileName."','".$showPath."')";
	$mLink=new mysql;
	$result=$mLink->insert($sql);
	$mLink->closelink();
	return $result;
}


?>