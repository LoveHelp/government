<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:index.php');
	exit;
}
?>
<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>南阳市政务督查管理系统</title>
</head>

<frameset rows="125,*" frameborder="NO" border="0" framespacing="0">
	<frame src="top2.php" name="topFrame" scrolling="NO" noresize >
    <frameset cols="250,*" framespacing="0" frameborder="NO" border="0" id=underFrame>
		<frame src="left2.php" name="leftFrame" scrolling="yes">
			<frameset cols="9,*" framespacing="0" frameborder="NO" border="0">
				<frame src="middle2.php" name="middleFrame" scrolling="NO" noresize>
				<frameset rows="*,0" frameborder="NO" border="0" framespacing="0">	
				<!--<frame src='<%=mainFramePage%>' name ="mainFrame" id="mainFrame" scrolling="" >-->
				<frame src="main3.php" name="mainFrame" id="mainFrame" scrolling="">
				<!--<frame src="Bottom.php" name="bottomFrame" scrolling="NO" noresize>-->
			 </frameset>  
		</frameset>
		
	</frameset>
</frameset>
<noframes><body>

</body></noframes>

</html>

