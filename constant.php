<?php
header("Content-type:text/html;charset=utf-8");
define('leaderType',"return array('1'=>'市政府领导','2'=>'市委领导','3'=>'市人大领导','4'=>'市政协领导');");
$leaderType=eval(leaderType);
//var_dump($leaderType);
$GLOBALS['areaCode'] = $areaCode;
define('areaCode',"return array('1'=>'市政府办组成科室','2'=>'垂直单位','3'=>'市直单位','4'=>'县区');");
$areaCode=eval(areaCode);
//var_dump($areaCode);

//定期反馈上报类型：1季报；2月报；3不用报；4双月；5周报；
define('regbacktype',"return array('1'=>'季报','2'=>'月报','3'=>'不用报','4'=>'双月','5'=>'周报','6'=>'日报');");
$regbacktype=eval(regbacktype);
//var_dump($regbacktype);

//任务接收状态: 0未接收，1接收，2退回
define('taskrecv_status',"return array('0'=>'未接收','1'=>'接收','2'=>'退回');");
$taskrecv_status=eval(taskrecv_status);
//var_dump($taskrecv_status);

//法律法规分类：1国家法律；2国务院条例；3省级条例；4市级条例；5区级条例
define('lawType',"return array('1'=>'国家法律','2'=>'国务院条例','3'=>'省级条例','4'=>'市级条例','5'=>'区级条例');");
$lawTypeArray=eval(lawType);
//var_dump($taskrecv_status);

//台账类型
define('task_type',"return array('1'=>'重点工作','2'=>'重大项目','3'=>'市长台账','4'=>'领导批示','5'=>'会议纪要','6'=>'建议提案','7'=>'舆情监控','8'=>'民生工程','9'=>'中央项目');");
$task_type = eval(task_type);

//信息类型：1督查通知；2督查通报；3督查动态；4督查文件；5法律法规
define('infoType',"return array('1'=>'督查通知','2'=>'督查通报','3'=>'督查动态','4'=>'督查文件','5'=>'法律法规');");
$infoTypeArray = eval(infoType);
?>