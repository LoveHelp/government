<?php
include_once "../mysql.php";
//根据infoid获取附件列表
function get_attachList($infoId){
	$infoId = empty($infoId) ? 0 : trim($infoId);
	$sql="select * from attachment where infoId = " . $infoId .';';
	$mLink=new mysql;
	$result=$mLink->getAll($sql);
	$mLink->closelink();
	return $result;
}
function del_attachByAttachId($attachId){
	$sql="delete from attachment where attachId = ".$attachId;
	$mLink=new mysql;
	$result=$mLink->update($sql);
	$mLink->closelink();
	return $result;
}
?>