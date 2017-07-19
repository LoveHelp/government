<?php
include_once "mysql.php";
header("Content-type:text/html;charset=utf-8");
$mLink = new mysql;
$res = $mLink->getAll("select * from menu order by menuid ");

$html = "";
$mLink2 = new mysql;
$arr = $mLink2->getAll("select DISTINCT(menuclass) as menu from menu ORDER BY menuid");
$k = 0;
foreach($arr as $key=>$value) {
	$i = $key + 1;
	if($value['menu'] != '邮件系统'){
		$html .= '<div class="topFolder" id="Menu' . $i .'"><table width="100%" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td height="32" class="MenuBg1">' . $value['menu'] .'</td></tr></tbody></table></div><div class="sub" id="Menu' . $i . 'Sub" style="display: none;">';
		foreach($res as $key2=>$value2){
			if($value2['menuclass'] == $value['menu']){
				$k++;
				$html .= '<div class="subItem"><table width="98%" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td width="17"><img width="17" height="22" src="img/Menu14.gif"></td><td height="21" class="MenuBg0"><a href="' . $value2['menuurl'] . '"target="mainFrame">' . $value2['name'] . '</a></td></tr><tr bgcolor="#f87521"><td colspan="2"><img name="shim" width="1" height="1" alt="" src=""></td></tr></tbody></table></div>'; 
			}
		}
		$html .= '</div>';
	}	
}
$email = '<table width="98%" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td height="21" class="MenuBg0"><a href="" target="_blank"><img src="img/001.png" border="0"></a></td></tr></tbody></table>';

$html .= $email;
echo $html;