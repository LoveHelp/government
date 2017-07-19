<!Doctype html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<title>导航页面</title>
<link href='css/default2.css' rel='stylesheet' type='text/css'>
<link rel='stylesheet' type='text/css' href='css/menu2.css'>
<script src="js/jquery.min.js"></script>
<script type="text/javascript">

$(function(){
	$("div.MenuBg1").click(function(){
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
	$("div.MenuBg1Down").click(function(){
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
});
</script>
</head>
<body class='left' style="overflow-x: hidden;text-align:center;">
<div>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tbody>
      <tr>
        <td bgcolor="#FFFFFF" valign="top">
<!-- 显示你的收藏夹内容代码开始 --> 
<div class="outer" style="width: 100%;margin: 3px;font-size:12px;font-weight:bold;color:#ffffff;"> 
<div class="inner" style="width: 100%;"> 
<div class="favMenu" id="aMenu" style="margin: 0px;"> 
<?php 
include_once "shouye.php";
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:index.php');
	exit;
}
$roleid = isset($_SESSION['userRoleID']) ? $_SESSION['userRoleID'] : "";
$menuList = json_decode(get_menu_list($roleid), true);
$i = 0;
if(is_array($menuList) && count($menuList) > 0){
	foreach($menuList as $menu){
		$i++;
		if($i == 1){
			$class = "MenuBg1Down";
			$display = "display:block;";
		}else{
			$class = "MenuBg1";
			$display = "display:none;";
		}
		if(is_array($menu['sub']) && count($menu['sub']) > 0){
						// 主菜单
						echo '<div class="' . $class . '" id="Menu' . $i . '"><img src="' . $menu['icon'] . '" /></div>'
							. '<div class="sub" id="Menu' . $i . 'Sub" style="' . $display .'">';
						foreach($menu['sub'] as $sub){
							// 子菜单 
							echo '<div class="subItem"><a href="' . $sub['menuurl'] . '"  target="mainFrame">' . $sub['name'] . '</a></div>';
						}
						echo '</div>';
					}
	}
}
?>
			
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
</body>
</html>
