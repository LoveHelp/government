<?php
//文件上传保存路径
define('BASEDIR_REPORT', '/government/upload/report/');

if(isset($_POST['session'])){
	session_id($_POST['session']);
}
session_start();
if(empty($_SESSION['userID'])){
    header('location:index.php');
    exit;
}
$userid = $_SESSION['userID'];
$deptid = $_SESSION['userDeptID'];
$roleid = $_SESSION['userRoleID'];

include_once 'mysql.php';

//控制器
if(!empty($_REQUEST['do'])){
    $do=$_REQUEST['do'];
	if($do == 'uploadfiles'){
        $res = uploadfiles($userid, $deptid);
    }else if($do == 'deletefile'){
        $res = deletefile();
    }else if($do == 'getfilesfromdb'){
		$page = empty($_POST['page']) ? 0 : trim($_POST['page']);
        $res = getfilesfromdb($page);
    }
    echo $res;
}

//从文件夹读取文件列表
function getfilesfromdir($deptid){
    //返回值
    $data = array();
    $fileinfo=array();
    //参数
    $deptid = empty($deptid) ? 0 : trim($deptid); //去除非法值
    if(empty($deptid))
        return 0;
    //网站根目录
    $RootDir   = $_SERVER['DOCUMENT_ROOT'];
    //相对路径（包含项目名）
    $relativeDir = BASEDIR_REPORT.$deptid.'/';
    //文件上传的绝对路径
    $uploadDir = $RootDir . $relativeDir;
    //非文件夹直接跳出
    if(!is_dir($uploadDir))
        return $data;
    //
    $targetdir = dir($uploadDir);  
	while($file = $targetdir->read())
	{
        clearstatcache();
		if((is_dir("$uploadDir/$file")) or ($file==".") or ($file==".."))
            continue;

        //$utf8file = mb_convert_encoding($file, "UTF-8", "auto");
		$utf8file = iconv("gbk", "UTF-8", $file);
        $prefix = substr($utf8file, 0, 21);
        $filename = substr($utf8file, 21);
        $fileinfo['fname'] = empty($filename) ? $file : $filename;
        $fileinfo['furl'] = $relativeDir . $utf8file;
        $fileinfo['uploadtime'] = date("Y-m-d H:i:s", filemtime("$uploadDir/$file"));
        if(!empty($filename)){
            $fileinfo['uploadtime'] = substr($prefix, 4, 4) . '-';
            $fileinfo['uploadtime'] .= substr($prefix, 8, 2) . '-';
            $fileinfo['uploadtime'] .= substr($prefix, 10, 2) . ' ';
            $fileinfo['uploadtime'] .= substr($prefix, 12, 2) . ':';
            $fileinfo['uploadtime'] .= substr($prefix, 14, 2) . ':';
            $fileinfo['uploadtime'] .= substr($prefix, 16, 2);
        }
        $data[] = $fileinfo;
	} 
	$targetdir->close();     
    $bRes = usort($data, function($a, $b){
		if($a['uploadtime'] == $b['uploadtime'])
			return 0;
		else if($a['uploadtime'] > $b['uploadtime'])
			return -1;
		else
			return 1;
	});
    return $data;
}

//从数据库读取文件列表：督查室及管理员用
function getfilesfromdb($page=0){
    $deptcode = empty($_POST['dcode']) ? 0 : trim($_POST['dcode']);
    $fname = empty($_POST['fname']) ? 0: trim($_POST['fname']);
    $fromtime = empty($_POST['fromtime']) ? 0 : trim($_POST['fromtime']);
    $totime = empty($_POST['totime']) ? 0 : trim($_POST['totime']);
	$page_size = 20;
    $sql = "select sf.fname, sf.furl, sf.uploadtime, d.deptname, d.deptcode, u.uname from sharedfiles sf join dept d on sf.deptid=d.deptid join user u on sf.userid=u.uid where d.deptcode like'%{$deptcode}%'";
    if($deptcode === 0)
        $sql = "select sf.fname, sf.furl, sf.uploadtime, d.deptname, d.deptcode, u.uname from sharedfiles sf join dept d on sf.deptid=d.deptid join user u on sf.userid=u.uid where 1=1";
    if($fname !== 0)
        $sql .= " and sf.fname like'%{$fname}%'";
    if($fromtime !== 0)
        $sql .= " and sf.uploadtime>='{$fromtime}'";
    if($totime !== 0)
        $sql .= " and sf.uploadtime<='{$totime}'";
    $page = $page * $page_size;
    $sql .= " order by id desc limit {$page}, {$page_size};";
    $pdo = new mysql;

    $res = $pdo->getAll($sql);
    if(!empty($res))
        return json_encode($res);
    return 0;
}

//上传文件
function uploadfiles($userid, $deptid){
    if(empty($_FILES['ufile']))
        return 0;
    
    $RootDir   = $_SERVER['DOCUMENT_ROOT'];
    $relativeDir = BASEDIR_REPORT.$deptid.'/';
    $uploadDir = $RootDir . $relativeDir;
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $sql = 'insert into sharedfiles(fname, deptid, userid, furl, uploadtime) values(:fname, :deptid, :userid, :furl, now());';
    $pdo = new mysql;
    $ufile = $_FILES['ufile'];
    //var_dump($ufile);
    if ($ufile['error'] == 0) {
        $index = $_POST['index'];
        $filename = sprintf('u%03d', $userid).date('YmdHis') . sprintf('%03d', $index) . $ufile['name'];
        $gbkname = iconv("UTF-8", "gb2312", $filename);//文件名使用gb2312编码，防止中文名称乱码
        $file = $uploadDir . $gbkname;
        if(move_uploaded_file($ufile['tmp_name'], $file)){//文件保存成功，则将数据插入数据库
            $param = array(
                ':fname'=>$ufile['name'],
                ':deptid'=>$deptid,
                ':userid'=>$userid,
                'furl'=>$relativeDir.$filename
            );
            
            $res = $pdo->insert($sql, $param);
            if(intval($res) == 0){//插入数据失败，则删除文件
                unlink($file);
                return 0;
            }
            return 1; //上传文件成功
        }
        return 0; //上传文件失败
    }else {
        switch ($_FILES[$field]['error']) {
            case 1:
                // 文件大小超出了服务器的空间大小
                $pdo->log->msg("The file is too large (server).");
                break;

            case 2:
                // 要上传的文件大小超出浏览器限制
                $pdo->log->msg("The file is too large (form).");
                break;

            case 3:
                // 文件仅部分被上传
                $pdo->log->msg("The file was only partially uploaded.");
                break;

            case 4:
                // 没有找到要上传的文件
                $pdo->log->msg("No file was uploaded.");
                break;

            case 5:
                // 服务器临时文件夹丢失
                $pdo->log->msg("The servers temporary folder is missing.");
                break;

            case 6:
                // 文件写入到临时文件夹出错
                $pdo->log->msg("Failed to write to the temporary folder.");
                break;
        }
        return 0;
    } 
}

//删除文件
function deletefile(){
    $fileid = empty($_POST['fid']) ? 0 : trim($_POST['fid']);
    $sql = 'select furl from sharedfiles where id=:fileid;';
    $param = array(':fileid'=>$fileid);
    $pdo = new mysql;

    $res = $pdo->getfirst($sql, $param);
    if(!empty($res)){
        $url = $res; //保存文件路径
        //删除服务器中文件
        $RootDir   = $_SERVER['DOCUMENT_ROOT'];
        $file = $RootDir.$url;
        if(file_exists($file))
            unlink($file);
        
        //删除数据库记录
        $sql = 'delete from sharedfiles where id=:fileid;';
        $res = $pdo->update($sql, $param);
        if(empty($res))
            return 0;
    }   
    return 1;
}