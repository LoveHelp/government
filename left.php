<!Doctype html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<title>导航页面</title>
<link href='css/default.css' rel='stylesheet' type='text/css'>
<link rel='stylesheet' type='text/css' href='css/menu.css'>
<link rel="stylesheet" href="css/progress.css" type="text/css" charset="utf-8">
<script src="js/jquery.min.js"></script>
<script type="text/javascript">

$(function(){
	$("td.MenuBg1").click(function(){
		var menuClass = $(this).attr("class");
		if(menuClass == "MenuBg1"){
			$(this).attr("class","MenuBg1Down");
		}else{
			$(this).attr("class","MenuBg1");
		}

		var sub = "#"+ $(this).attr("id") + "Sub";
		var display = $(sub).css("display");

		if(display == "none"){
			$(sub).css("display", "block");
		}else{
			$(sub).css("display", "none");
		}
	});
	$("td.MenuBg1Down").click(function(){
		var menuClass = $(this).attr("class");
		if(menuClass == "MenuBg1"){
			$(this).attr("class","MenuBg1Down");
		}else{
			$(this).attr("class","MenuBg1");
		}

		var sub = "#"+ $(this).attr("id") + "Sub";
		var display = $(sub).css("display");

		if(display == "none"){
			$(sub).css("display", "block");
		}else{
			$(sub).css("display", "none");
		}
	});
	$("td.MenuBg0").click(function(){
		var menuClass = $(this).attr("class");
		
		if(menuClass == "MenuBg0"){
			$(".MenuBg0Down").attr("class","MenuBg0");
			$(this).attr("class","MenuBg0Down");
		}else{
			$(this).attr("class","MenuBg0");
		}
	});
});
</script>
</head>
<body class='left' style="overflow-x: hidden;">
<div>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tbody><tr>
    <td width="8">&nbsp;</td>
    <td>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" height="530">
      <tbody><tr>
        <td colspan="3" height="26">
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tbody><tr>
              <td colspan="2"><img src="img/caidan.jpg"></td>
            </tr>          
          </tbody></table>
        </td>
      </tr>
      <tr>
        <td bgcolor="#FFFFFF" valign="top">
<!-- 显示你的收藏夹内容代码开始 --> 
<div class="outer" style="width: 100%;margin: 3px;font-size:12px;font-weight:bold;color:#ffffff;"> 
<div class="inner" style="width: 100%;"> 
<div class="favMenu" id="aMenu" style="margin: 0px;"> 
<?php 
session_start();
$roleid = isset($_SESSION['userRoleID']) ? $_SESSION['userRoleID'] : "";
include_once "mysql.php";
include_once "reminddata.php";
function get_menu_list($roleid){
	$mLink = new mysql;
	$arr = $mLink->getAll("select DISTINCT(menuclass) as menu from menu ORDER BY menuid");

	foreach($arr as $v){
		$sub = $mLink->getAll("select b.menuclass, b.menuurl, b.name from rolemenu a, menu b where a.menuid = b.menuid and a.roleid = " . $roleid . " and b.menuclass = '" . $v['menu'] . "' order by b.menuid");
		$result[] = array(
			'top' => $v['menu'],
			'sub' => $sub
		);
	}
	return json_encode($result);
}
$menu = json_decode(get_menu_list($roleid), true);
$i = 0;
foreach($menu as $v){
	$i++;
	if($i == 1){
		$class = "MenuBg1Down";
		$display = "display:block;";
	}else{
		$class = "MenuBg1";
		$display = "display:none;";
	}
	if(is_array($v['sub']) && count($v['sub']) > 0){
		echo '<div class="topFolder" >'
		. '<table width="100%" border="0" cellpadding="0" cellspacing="0">'
		. '<tbody>'
		. '<tr><td id="Menu' . $i . '" height="32" class="' .$class . '">' . $v['top'] . '</td></tr>'
		. '</tbody></table></div>'
		. '<div class="sub" id="Menu' . $i . 'Sub" style="' . $display .'">';
		foreach($v['sub'] as $sub){
			echo '<div class="subItem" style="border-bottom:1px solid #F87521">'
				. '<table width="98%" border="0" cellspacing="0" cellpadding="0">'
				. '<tbody><tr stype="border-bottom:2px solid #F87521;">'
				. '<td width="17"><img src="img/Menu14.gif" width="17" height="22"></td>'
				. '<td height="21" class="MenuBg0"><a href="' . $sub['menuurl'] . '" target="mainFrame">' . $sub['name'] . '</a></td>'
				. '</tr></tbody></table></div>';
		}
		echo '</div>';
	}
}
?>
			<div  style="height: 200px;width: 200px;"  id="p3" title="" class="panel-body portal-p portal-p-div">
				<p style="color: #000000">填报提醒:<?=$recvtarget?>个未填报</p>
				<div class="progress">				
     			 <span  id="1" onclick="query(this);" class="orange" style="width:<?php echo($recvtarget*100/$alltarget);?>%;"><span><?=$recvtarget?>/<?=$alltarget?></span></span>
				</div>
				<p style="color: #000000">接收提醒:<?=$weishou?>个未接收</p>				
				<div class="progress">
     			<span  id="2" onclick="query(this);" class="orange" style="width:<?php echo($weishou*100/($jieshou+$weishou));?>%;"><span><?=$weishou?>/<?=($jieshou+$weishou)?></span></span>
				</div>	
				<p style="color: #000000">反馈提醒:<?=($jieshou+$weishou-$anqifan)?>个待反馈</p>			
				<div class="progress">
     			 <span  id="3" onclick="query(this);" class="orange" style="width:<?php echo(($jieshou+$weishou-$anqifan)*100/($jieshou+$weishou));?>%;"><span><?=($jieshou+$weishou-$anqifan)?></span></span>
				</div>
			</div>
</div><!-- for favMenu--> 
</div><!-- for inner-->
</div><!-- for outer--> 

<div id="aMenuUp" class="scrollButton" style="left: 16px; top: 79px; width: 217px; display: none;">

</div><!-- These are the two --> 
<div id="aMenuDown" class="scrollButton" style="left: 16px; top: 203px; width: 217px; display: none;"></div><!-- scroll buttons --> 
<!-- 代码结束 -->

		</td>		
     </tr>
    </tbody></table></td>
  </tr>
</tbody></table>
</div>
<script type="text/javascript">
	function query(spanbtn){
		var id=spanbtn.id;
		switch(id){
			case "1":			
			window.open("taizhang/inputtitle.php");
			break;
			case "2":		
			window.open("taizhang/taskrecv.php");
			break;
			case "3":
			window.open("taizhang/taskfeedbackreg.php");
			break;	
		}
	}
</script>
</body>
</html>
