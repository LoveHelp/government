<!Doctype html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<link rel="stylesheet" type="text/css" href="../css/xtstyle.css">
<script src="../js/jquery.min.js"></script>
<script src="../js/layer/layer.js"></script>
<title>菜单管理</title>
</head>
<button value="延迟" onclick="show();">延迟</button>
 <p><span id="mySpan">我是span标签的原始内容！</span></p>
<script type="text/javascript">
function show(){
var t=setTimeout('document.getElementById("mySpan").innerHTML = "我是被JS改变后的内容"',1000);
}
</script>