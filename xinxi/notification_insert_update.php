<?php
include_once "../mysql.php";
header("Content-type:text/html;charset=utf-8");
$mLink=new mysql;

$msg="";
if(isset($_GET['flag']) && $_GET['flag']=="del"){//删除
	$infoId=$_GET['infoId'];
	$sql="delete from information where infoId = ".$infoId;
	$result = $mLink->update($sql);
	if($result = 1){
			$msg = "<script>alert('删除成功！');history.go(-1);</script>";
	}
}else{//修改或添加
	$infoTitle = trim($_POST['infoTitle']);
	$infoContent = trim($_POST['infoContent']);
	$addTime = trim($_POST['addTime']);
	$infoCode = trim($_POST['infoCode']);
	$startTime = trim($_POST['startTime']);
	$deptId = trim($_POST['hd_deptId']);
	$hd_recvDeptIds = trim($_POST['hd_recvDeptIds']);

	if(empty($_POST['hd_infoId'])){//添加
		$sql="insert into information(infoType,infoTitle,infoContent,addTime,infoCode,startTime,deptId,recvDeptIds)";
		$sql.=" values(?,?,?,?,?,?,?,?)";
		//return $sql;
		$param=array(
			2,
			$infoTitle,
			$infoContent,
			$addTime,
			$infoCode,
			$startTime,
			$deptId,
			$hd_recvDeptIds
		);
		$result = $mLink->insert($sql,$param);
		if($result>0){
			$infoId=$result;
			//hd_attachUrls
			if(!empty($_POST['hd_attachUrls'])){
				$attachUrls=$_POST['hd_attachUrls'];
				$attachNames=$_POST['hd_attachNames'];	
				$attachUrlsArray=explode(",",$attachUrls);
				$attachNamesArray=explode(",",$attachNames);
				if(is_array($attachUrlsArray)){
					$sql="insert into attachment(infoId,attachName,attachUrl) values ";
					for($i=0;$i<count($attachUrlsArray);$i++){
						$sql.="(".$infoId.",'".$attachNamesArray[$i]."','".$attachUrlsArray[$i]."'),";
					}
					$sql=substr($sql,0,strlen($sql)-1);
					$mLink->insert($sql);
				}
				
			}
			//$msg = "<script>alert('发布成功！');window.parent.close();</script>";
			$msg="1";//发布成功！
		}
	}else{//修改
		$infoId = $_POST['hd_infoId'];
		$sql="update information set infoTitle=?,infoContent=?,infoCode=?,startTime=?,recvDeptIds=? where infoId=?";
		$param=array(
			$infoTitle,
			$infoContent,
			$infoCode,
			$startTime,
			$hd_recvDeptIds,
			$infoId
		);
		$result = $mLink->update($sql,$param);
		if($result = 1){
			//$msg = "<script>alert('修改成功！');window.parent.close();</script>";
			$msg="3";//发布成功！
		}
	}
}

$mLink->closelink();
echo $msg;

?>