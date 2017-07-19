<?php
error_reporting(0);//关闭提示
include_once "../mysql.php";
header("Content-type:text/html;charset=utf-8");

//print_r($_REQUEST);
//var_dump($_POST["leaderId"]);

leader_uploadTouxiang();

/** 
 * 修改用户头像 - Ajax提交验证 
 */  
function leader_uploadTouxiang(){  
    $msg = array();  
    //设置图片要上传保存到服务器的绝对路径  
    $path = $_SERVER['DOCUMENT_ROOT'].'/government/upload/touxiang/';  
    //图片显示的路径  
    $showPath = '';  
    if(isset($_FILES['leaderPhoto'])){  
        //若上传错误，则弹出错误id  
        if($_FILES['leaderPhoto']['error'] > 0){  
            $resultCode = 0;  
            $resultMsg  = '错误代码：'.$_FILES['leaderPhoto']['error'];  
        } else if($_FILES['leaderPhoto']['size'] > (2*1024*1024)){  
            $resultCode = 1;  
            $resultMsg  = '上传照片请不要大于2M';     
        } else {  
            $division = pathinfo($_FILES['leaderPhoto']['name']);  
            $extensionName = $division['extension'];  //获取文件扩展名  
            //如果上传文件不是图片，则不保存  
            if( !in_array($extensionName, array('jpg', 'gif', 'png', 'jpeg'))){  
                $resultCode = 2;  
                $resultMsg  = '错误：只可以上传图片';  
            } else {  
                //对图片进行保存  
                $pattern='1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';  
                for($i=0; $i<10; $i++)  
                {  
                   $key .= $pattern{mt_rand(0,35)};    //生成php随机数  
                }  
                $newFileName = sha1(date('Y-m-d',time()).$key).'.'.$extensionName;  
                $savePath    = $path.$newFileName;  //图片的存储路径  
                $showPath    = '/government/upload/touxiang/'.$newFileName;   
                move_uploaded_file($_FILES['leaderPhoto']['tmp_name'], $savePath);    
                if(!file_exists($savePath)){  
                    $resultCode = 3;  
                    $resultMsg  = '上传失败';  
                } else {  
//                  //将图片路径添加到用户数据表中  
//                  $result = leader_updateLeaderPhoto($showPath);
//                  if($result){  
//                      $resultCode = 4;  
//                      $resultMsg  = '上传成功';  
//                  } else {  
//                      $resultCode = 6;  
//                      $resultMsg  = '保存到数据库失败';  
//                  }   
					leader_updateLeaderPhoto($showPath);
					$resultCode = 4;  
                    $resultMsg  = '上传成功';  
                } 
                      
            }  
            
        }  
    } else {  
        $resultCode = 5;  
        $resultMsg  = '文件未上传';  
    } 
       
    $info=array(
		'codeNum'=>$resultCode,
		'msg'=>$resultMsg,
		'path'=>$showPath
	);
	
	//echo $showPath;
    echo json_encode($info);
} 

//修改头像
function leader_updateLeaderPhoto($showPath){
	$leaderId=$_POST["leaderId"];
	$sql="update leader set leaderPhoto='".$showPath."' where leaderId = '".$leaderId."'";
	$mLink=new mysql;
	$result=$mLink->update($sql);
	$mLink->closelink();
	return $result;
}

?>