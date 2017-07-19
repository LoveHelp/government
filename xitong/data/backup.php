<?php
//备份执行过程：
//1 首先强制清空网站根目录下的临时目录
//2 在临时目录下生成数据库结构、数据库数据、网站数据、note等文件
//3 将临时目录下的文件添加到一个压缩包中，命名规则YYYYmmddHHiiss+标识符+.zip：d代表归档；r代表恢复；b代表备份
//4 将临时目录下生成的压缩包，移动到备份文件夹下
//备注：以上4步任何地方出现错误，都会导致本次备份失败，不会生成备份文件压缩包
//备注：数据恢复口令：zwdc@rv	数据年度归档口令：zwdc@gd
header("Content-type:text/html;charset=utf-8");

session_start();
if(empty($_SESSION['userID'])){
    $firspage = $_SERVER['DOCUMENT_ROOT'] . '/government/index.php';
	header('Location:'.$firspage);
	exit;
}
$userCode = $_SESSION['userCode'];

include_once 'utils.php';

$db_user = "root";
$db_passwd = "s123";
$db_name = "government";

if(!empty($_REQUEST['do'])){
	$do = $_REQUEST['do'];
	if($do == 'dataBackup'){
		$res = dataBackup($db_user, $db_passwd, $db_name, $userCode, 'b');
	}else if($do == 'dataRecovery'){
		$res = dataRecovery($db_user, $db_passwd, $db_name, $userCode);
	}else if($do == 'dataGuidang'){
		$res = dataGuidang($db_user, $db_passwd, $db_name, $userCode);
	}else if($do == 'getBackupList'){
		$res = getBackupList();
	}else{
        $res = 0;
   }
	echo $res;
}

//数据备份
function dataBackup($db_user, $db_passwd, $db_name, $userCode, $type){
	$user = $db_user;
    $password = $db_passwd;
    $dbname = $db_name;

    $backup_dir = '../../backup/';
    $tmp_dir = '../../tmp/';
    $upload_dir = "../../upload/";
    $db_structure_fname = "{$tmp_dir}dtb_structrue.sql";
    $db_fname = "{$tmp_dir}dtb.sql";
    $web_data_fname = "{$tmp_dir}web_data.zip";
    $note_fname = "{$tmp_dir}note.log";
	
	if($type == 'b')
		operationLog(trim('------------------------------------------------------------------'));
	operationLog("[数据备份]开始执行");
	if($type == 'b'){
		operationLog("[数据备份]操作员：{$userCode}");
		$miwen = encrypt("{$user}_{$password}-{$dbname}",'E', KEY);
		operationLog("[数据备份]参数：{$miwen}");
	}
    //检测并创建临时文件夹
    if(!file_exists($tmp_dir))
        mkdir($tmp_dir, 0777, true);

    //刪除临时目录下的所有文件及子目录
    if(is_dir($tmp_dir)){
        $dir = opendir($tmp_dir);
        while($file = readdir($dir)){
            if($file=='.' || $file=='..')
                continue;
            else if(is_file($tmp_dir.$file)){
                unlink($tmp_dir.$file);
            }else if(is_dir($tmp_dir.$file)){
                del_dir($tmp_dir.$file);
            }
        }
		operationLog("[数据备份]清空临时目录成功");
    }
	
    //保存note.log
    $optime = date("Y-m-d H:i:s", time());
    $txt = '';
    $txt = "-------------------------------".PHP_EOL;
    $txt .= "日期：$optime".PHP_EOL;
    $txt .= "操作人员：$userCode".PHP_EOL;
    $txt .= "备份内容：数据库结构、数据库数据、网站数据".PHP_EOL;
    file_put_contents($note_fname, $txt);
	operationLog("[数据备份]记录note日志成功");

    //备份数据库结构
    $command = "mysqldump.exe -u{$user} -p{$password} -d {$dbname} > {$db_structure_fname}";
    unset($res_exec);
    exec($command, $res_exec, $code_exec);
    if($code_exec){
		 operationLog("[数据备份]备份数据库结构失败，错误码：{$code_exec}");
		 return 2;
    }
	operationLog("[数据备份]备份数据库结构成功");
	
    //备份数据库结构及数据
    $command = "mysqldump.exe -u{$user} -p{$password} {$dbname} > {$db_fname}";
    unset($res_exec);
    exec($command, $res_exec, $code_exec);
    if($code_exec){
		operationLog("[数据备份]备份数据库结构及数据失败，错误码：{$code_exec}");
        return 3;
    }
	operationLog("[数据备份]备份数据库结构及数据成功");

    //备份网站数据文件
    $zip = new ZipArchive;
    $res = $zip->open($web_data_fname, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
    if ($res === TRUE){
        addFileToZip($zip, $upload_dir, trim(''));
        $zip->close();
    }else{
		operationLog("[数据备份]备份并压缩网站数据失败");
        return 4;
    }
	operationLog("[数据备份]备份并压缩网站数据成功");

    //temp中所有文件压缩，放入backup文件夹下
    $zip = new ZipArchive;
    $zipname = date("YmdHis",time()).$type.'.zip';
    $res = $zip->open($backup_dir.'/'.$zipname, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
    if($res === true){
        addFileToZip($zip, $tmp_dir, trim(''));
        $zip->close();
		operationLog("[数据备份]压缩数据库备份文件及网站备份文件成功");
    }else{
		operationLog("[数据备份]压缩数据库备份文件及网站备份文件失败");
        return 5;
    }
	operationLog("[数据备份]执行完毕");
    return 1;
}

//数据年度归档
function dataGuidang($db_user, $db_passwd, $db_name, $userCode){
    $user = $db_user;
    $password = $db_passwd;
    $dbname = $db_name;
    $tmp_dir = "../../tmp/";
	
	operationLog(trim('------------------------------------------------------------------'));
	operationLog("[数据年度归档]开始执行");
	operationLog("[数据年度归档]操作员：{$userCode}");
	$miwen = encrypt("{$user}_{$password}-{$dbname}",'E', KEY);
	operationLog("[数据年度归档]参数：{$miwen}");
	//验证数据年终归档口令
	$key = $_REQUEST['gdkey'];
	if($key != GD_KEY){
		operationLog("[数据年度归档]口令输入错误：{$key}");
		return -1;
	}
	operationLog("[数据年度归档]口令验证通过");
    //1、备份数据
    $res = dataBackup($db_user, $db_passwd, $db_name, $userCode, 'd');
    if($res != 1){
        operationLog("[数据年度归档]备份失败，错误码：{$res}");
		return 2;
	}
    //2、导出所有非基础表结构
    //TABLE{db、dept、leader、menu、rolemenu、telbook、user}
    $base_tale = "attachment generaltask information message p_progress p_target p_task progress sms targetrecv task taskfeedback taskrecv taskreview";
    $sql_file = "{$tmp_dir}unbase_table.sql";
    $command = "mysqldump -u{$user} -p{$password} -d {$dbname} {$base_tale} > {$sql_file}";
    unset($res_exec);
    exec($command, $res_exec, $code_exec);
    if($code_exec){
        operationLog("[数据年度归档]导出所有非基础表结构失败，错误码：{$code_exec}");
		return 3;
    }
	operationLog("[数据年度归档]导出所有非基础表结构成功");
	
    //3、导出所有非基础表数据
    $sql_file = "{$tmp_dir}unbase_table_data.sql";
    $command = "mysqldump -u{$user} -p{$password} {$dbname} {$base_tale} > {$sql_file}";
    unset($res_exec);
    exec($command, $res_exec, $code_exec);
    if($code_exec){
		operationLog("[数据年度归档]导出所有非基础表结构及数据失败，错误码：{$code_exec}");
        return 4;
    }
	 operationLog("[数据年度归档]导出所有非基础表结构及数据成功");
	 
    //3、清空数据库非基础表：直接使用刚刚备份的表结构文件恢复数据库表结构
    $sql_file = "{$tmp_dir}unbase_table.sql";
    $command = "mysql -u{$user} -p{$password} -D{$dbname} < {$sql_file}";
    unset($res_exec);
    exec($command, $res_exec, $code_exec);
    if($code_exec){
		operationLog("[数据年度归档]清空所有非基础表数据失败，错误码：{$code_exec}");
		operationLog("[数据年度归档]开始恢复非基础表结构及数据");
        $sql_file = "{$tmp_dir}unbase_table_data.sql";
        $command = "mysql -u{$user} -p{$password} -D{$dbname} < {$sql_file}";
        unset($res_exec);
        exec($command, $res_exec, $code_exec);
		if(!$code_exec){
			operationLog("[数据年度归档]恢复非基础表结构及数据成功");
		}
        return 0;
    }
	operationLog("[数据年度归档]执行完毕");
    return 1;
    /////////////////////////////////////////////////////////////////
    //此方式可能会因为外键的原因导致清空表数据失败
    /////////////////////////////////////////////////////////////////
    // //配置信息
    // $cfg_dbhost = 'localhost';
    // $cfg_dbname = 'government';
    // $cfg_dbuser = 'root';
    // $cfg_dbpwd = 's123';
    // $cfg_db_language = 'utf8';
    
    // //链接数据库
    // $link = mysql_connect($cfg_dbhost,$cfg_dbuser,$cfg_dbpwd);
    // mysql_select_db($cfg_dbname);
    // //选择编码
    // mysql_query("set names {$cfg_db_language}");
    // //获取数据库中表列表，并清空表数据
    // $tables = mysql_list_tables($cfg_dbname);
    // //将这些表记录到一个数组
    // $table_name_arr = array();
    // while($row = mysql_fetch_row($tables)){
    //     $table_name_arr[] = $row[0];
    // }

    //  foreach($table_name_arr as $row){
    //     //获取并备份表的创建sql语句
    //     $sql = "delete from $row;";
    //     mysql_query($sql, $link);
    // }
    // mysql_close($link);
    ///////////////////////////////////////////////////////////////
}

//数据恢复
function dataRecovery($db_user, $db_passwd, $db_name, $userCode){
    $user = $db_user;
    $password = $db_passwd;
    $dbname = $db_name;

    $tmp_dir = "../../tmp/";
    $backup_dir = "../../backup/";
    $tmp_fname = "{$tmp_dir}backup.zip";
	
	operationLog(trim('------------------------------------------------------------------'));
	operationLog("[数据恢复]开始执行");
	operationLog("[数据恢复]操作员：{$userCode}");
	$miwen = encrypt("{$user}_{$password}-{$dbname}",'E', KEY);
	operationLog("[数据恢复]参数：{$miwen}");
	//验证数据恢复口令
	$key = $_REQUEST['rvkey'];
	if($key != RV_KEY){
		operationLog("[数据恢复]数据恢复口令输入错误");
		return -1;
	}
	operationLog("[数据恢复]数据恢复口令验证通过");
    //获取选择的备份文件名称
    $param = $_REQUEST['param'];
    if(empty($param)){
		operationLog("[数据恢复]未选择用来恢复数据的备份文件");
		return 0;
	}
    operationLog("[数据恢复]选择的备份文件是：{$param}.zip");    
    $backup_fname = "{$backup_dir}{$param}.zip";
    //1、备份数据
    $res = dataBackup($db_user, $db_passwd, $db_name, $userCode, 'r');
    if($res != 1){
		operationLog("[数据恢复]备份失败，错误码：{$res}");
		return 2;
	}
    operationLog("[数据恢复]备份成功！");
	
    //2、将选择的备份文件拷贝到临时目录中
    copy($backup_fname, $tmp_fname);
    if(!file_exists($tmp_fname)){
		operationLog("[数据恢复]拷贝备份文件失败");
		return 3;
	}
	operationLog("[数据恢复]拷贝备份文件成功");
        
    //3、解压文件
    $target_dir = "{$tmp_dir}backup/";
    if(!file_exists($target_dir))
        mkdir($target_dir, 0777, true);
    $zip = new ZipArchive;
    $res = $zip->open($tmp_fname);
    if($res === true){
        $dd = $zip->extractTo($target_dir);
        $zip->close();
        if(!$dd){
            return 4;
        }
    }else{
		operationLog("[数据恢复]解压缩备份文件失败");
        return 5;
    }
	operationLog("[数据恢复]解压缩备份文件成功");
	
    //4、恢复数据
    $sql_file = "{$target_dir}dtb.sql";
	if(!file_exists($sql_file)){
		operationLog("[数据恢复]脚本文件不存在");
		return 6;
	}
		
    $command = "mysql -u{$user} -p{$password} -D{$dbname} < {$sql_file}";
    unset($res_exec);
    exec($command, $res_exec, $code_exec);
    if($code_exec){
		operationLog("[数据恢复]数据恢复失败，错误码：{$code_exec}");
        $sql_file = "{$tmp_dir}dtb.sql";
        $command = "mysql -u{$user} -p{$password} -D{$dbname} < {$sql_file}";
        unset($res_exec);
        exec($command, $res_exec, $code_exec);
        return 7;
    }
	operationLog("[数据恢复]执行完毕");
    return 1;
}

//获取备份文件列表
function getBackupList(){
	$RootDir   = $_SERVER['DOCUMENT_ROOT'];
	$backup_dir = "{$RootDir}/government/backup/";
	if(!file_exists($backup_dir))
		mkdir($backup_dir, 0777, true);
	$handle = dir($backup_dir);
	$data = array();
	while($fname = $handle->read()){
		if($fname == '.' || $fname == '..')
			continue;
        $file = $backup_dir.$fname;
		if(is_file($file)){
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if($ext != 'zip')
                continue;
            $row = array();
            $row["key"] = substr($fname, 0, 15);
			$row["val"] = "";
	        $row["val"] .= substr($fname, 0, 4) . '-';
	        $row["val"] .= substr($fname, 4, 2) . '-';
	        $row["val"] .= substr($fname, 6, 2) . ' ';
	        $row["val"] .= substr($fname, 8, 2) . ':';
	        $row["val"] .= substr($fname, 10, 2) . ':';
	        $row["val"] .= substr($fname, 12, 2);
            $data[] = $row;
		}
	}
	if(empty($data))
		return 0;
    $data = array_reverse($data);
	return json_encode($data);
}

function operationLog($msg){
	$log_fname = date("Y", time())."_backup.log";
	$log_dir = "../../backup/log";
	//备份操作日志文件目录不存在，则先创建
	if(!file_exists($log_dir))
		mkdir($log_dir, 0777, true);
	$log_file = "{$log_dir}/{$log_fname}";
	//记录备份操作日志
	$opt_time = date("Y-m-d H:i:s", time());
	$msg = "$opt_time $msg". PHP_EOL;
	file_put_contents($log_file, $msg, FILE_APPEND);
}