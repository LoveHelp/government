<?php
$itemid = isset($_GET['id'])?$_GET['id']:'';
$name = $_GET['name'];
?>
<html>
<head>
<title>处理页面框架</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<frameset rows="54,*" frameborder="NO" border="0" framespacing="0" onunload="opener.location.reload();">
  <frame src="handle_top.php?moduleTitle=null" name="topFrame" scrolling="NO" noresize>
  <frame src="<?php if($itemid != '') echo $name.'?id='.$itemid; else echo $name; ?>"name="mainFrame">
</frameset>
<body >   
浏览器版本太低，请升级！ 
</body>
</noframes>

</html>
