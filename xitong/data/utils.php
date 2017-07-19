<?php
define("RV_KEY", "4f83cafd51cd43bc04a3189b28b64e45");
define("GD_KEY", "fa448929a0abf0d0cb40907e21e4c45d");
define("KEY", "d41d8cd98f00b204e9800998ecf8427e");

//删除目录或文件
function del_dir($dir){
    if(is_dir($dir)){
        foreach(scandir($dir) as $row){
            if($row == '.' || $row == '..'){
                continue;
            }
            $path = $dir .'/'. $row;
            if(filetype($path) == 'dir'){
                del_dir($path);
            }else{
                unlink($path);
            }
        }
        rmdir($dir);
    }else{
        unlink($dir);
    }
}
//将文件夹打包生成zip文件
function addFileToZip($zip, $path, $parent_dir){
    $handler = dir($path);
    while($filename = $handler->read()){
        if($filename == "." || $filename == ".."){
            continue;
        }
        if(is_dir($path."/".$filename)){
            addFileToZip($zip, $path."/".$filename, "$parent_dir/$filename");
        }else { //将文件加入zip对象
            $zip->addFile($path."/".$filename, "$parent_dir/$filename");
        }
    }
}
//加解密
function encrypt($string, $operation, $key=''){ 
    $key=md5($key); 
    $key_length=strlen($key); 
    $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string; 
    $string_length=strlen($string); 
    $rndkey=$box=array(); 
    $result=''; 
    for($i=0;$i<=255;$i++){ 
		$rndkey[$i]=ord($key[$i%$key_length]); 
       $box[$i]=$i; 
    } 
    for($j=$i=0;$i<256;$i++){ 
        $j=($j+$box[$i]+$rndkey[$i])%256; 
        $tmp=$box[$i]; 
        $box[$i]=$box[$j]; 
        $box[$j]=$tmp; 
    } 
    for($a=$j=$i=0;$i<$string_length;$i++){ 
        $a=($a+1)%256; 
        $j=($j+$box[$a])%256; 
        $tmp=$box[$a]; 
        $box[$a]=$box[$j]; 
        $box[$j]=$tmp; 
        $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256])); 
    } 
    if($operation=='D'){ 
        if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)){ 
            return substr($result,8); 
        }else{ 
            return''; 
        } 
    }else{ 
        return str_replace('=','',base64_encode($result)); 
    } 
} 