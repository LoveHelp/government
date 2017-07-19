<?php
include_once '../mysql.php';

header("Content-type:text/html;charset=utf-8");

session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}

$taskid = empty($_REQUEST['taskid']) ? 0 : trim($_REQUEST['taskid']);
$data = array();
if($taskid != 0){
	$sql = "select f.backtime, f.remark, f.reporturl, d.deptname from taskfeedback f JOIN dept d on f.deptid=d.deptid where taskid=:taskid;";
	$param = array(':taskid'=>$taskid);
	$pdo = new mysql;
	$res = $pdo->getAll($sql, $param);
	$i=0;
	if(!empty($res)){
		$i=0;
		$rootdir = $_SERVER['DOCUMENT_ROOT'];
		foreach($res as $row){
			$picArr = explode(';', $row['reporturl']);
			$len = count($picArr);
			if($len > 0)
				unset($row['reporturl']);
			for($k=0; $k<$len; $k++){
				$ext = pathinfo($rootdir.$picArr[$k], PATHINFO_EXTENSION);
				$ext = strtolower($ext);
				if( $ext == 'jpg'
					|| $ext == 'png'
					|| $ext == 'jpeg'){
					$row['url'] = $picArr[$k];
					$fname = pathinfo($rootdir.$picArr[$k], PATHINFO_BASENAME);

					$row['backtime']="";
					$row['backtime'] .= substr($fname, 4, 4) . '-';
					$row['backtime'] .= substr($fname, 8, 2) . '-';
					$row['backtime'] .= substr($fname, 10, 2) . ' ';
					$row['backtime'] .= substr($fname, 12, 2) . ':';
					$row['backtime'] .= substr($fname, 14, 2) . ':';
					$row['backtime'] .= substr($fname, 16, 2);
					$data[$i] = $row;

					$i++;		
				}	
			}
		}
	}
}
?>
<!DOCTYPE html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>工作完成情况图片列表</title>
	<script type="text/javascript" src="../js/jquery.min.js"></script>
	<script type="text/javascript" src="../js/layer/layer.js" ></script>
	<style type="text/css">
		ul{
			margin: 10px auto;
			padding: 0px;
			list-style: none;
			text-align:center;
			width:100%;
			overflow:hidden;
			padding-top:2%;
			border:1px #dddddd solid;
		}
		ul li{
			width:33%;
			float: left;
			text-align: center;
			margin-bottom:2%;
		}
		ul li img{
			cursor:pointer;
			border:10px solid #f2f2f2;
		}
		ul li img:hover{
			border:10px solid #86bcea;
		}
		ul li p{
			line-height: 22px;
			margin: 0px;
			padding: 0px;
			font-size: 14px;
		}
		body.main{
			/*background-color: #ECF6FB;*/
			background-color: #FFF;
			min-width:1200px;
			margin:2% 3%;
		}
		div.title {
			height: 35px;
			line-height: 35px;
			top:10;
			right:10px;
			position:fixed; z-index:99999;
		}
		.button1 {
			width: 68px;
			height: 24px;
			text-align: center;
			color: #FFFFFF;
			font-weight: bold;
			background-image: url(../img/button2.jpg);
			background-repeat: no-repeat;
			background-position: left;
			border-left: 0px solid #2F3C4D;
			border-right: 0px solid #2F3C4D;
			border-top: 0px solid #2F3C4D;
			border-bottom: 0px solid #2F3C4D;
			padding-top: 2px;
		}
		div.msg{
			width: 100%;
			/*font-size: 14px;*/
			text-align: center;
			height: 35px;
			line-height: 35px;
			margin: 0px;
			background-color: #FFFFEF;
		}
		.p_title{
			/*background:rgba(0,0,0, 0.3);*/
			color:#6c6c6c;
			width:90%;
			margin-left:5%;
			bottom:48px;
			line-height:140%;
			height:40px;
			text-align:left;
			overflow:hidden;
		}
	</style>
</head>
<body class="main" >
	<div class="title">
		<input type="button" value="对比" onclick="showwindow(this);" class="button1"/>
	</div>
	<div id="content" style="text-align:center;">
	<?php
		if(empty($data)){
		?>
		<div class="msg"><small>没有查询到任何图片数据！</small></div>
		<?php		
		}else{
			$count = count($data);
		?>
		<ul>
		<?php
			$i = 0;
			foreach($data as $row){
			?>
			<li>
				<img src="<?=$row['url']?>" title="<?=$row['remark']?>" onclick="showpic(this);">
				<p class="p_title" title="<?php echo $row['remark']; ?>">
				<?php 
					if(mb_strlen($row['remark'],'utf-8') > 72) {
						echo mb_substr($row['remark'],0,72,'utf-8') . "...";
					}else{
						echo $row['remark'];
					}
				?>
				</p>
				<input type="hidden" name="remark" value="<?=$row['remark']?>" />
				<p><input type="checkbox" value="<?=$row['url']?>" onchange="selectpic(this);" /><?=$row['deptname']?>&emsp;<?=$row['backtime']?></p>
			</li>
			<?php
			$i++;
			}
		?>
		</ul>
		<?php	
		}
	?>
	</div>
</body>
</html>
<script type="text/javascript">
var param=[];
var count=0;

window.onload = function(){
	$(window).resize();
}

window.onresize = function(){
	var contentObj = $("div#content");
	
	var nTotalHeight = $(window).height()*0.88-100;
	var nTotalWidth = contentObj.width()*0.94;
	var nHeight = nTotalHeight;
	
	var img_obj = $("div#content>ul>li>img");
	var p_obj = img_obj.next().next();
	
	var halfHeight = parseInt(nHeight/2)-1;
	var pHeight = p_obj.height();
	img_obj.height(halfHeight-pHeight*2);
	img_obj.width(nTotalWidth/3-50);
	/*$("p.p_title").width(img_obj.width());
	var left_width = (nTotalWidth*0.99/3 - img_obj.width())/2;
	$("p.p_title").css("left", left_width);*/
}

function selectpic(obj){
	var pObj = $(obj).parent();
	var pic={};
	pic.url = $(obj).val();
	pic.date = $(pObj).text();
	pic.note = $(pObj).prev().val();
	//pic.dname = $(obj).next().text();
	
	var index = existscheck(pic.url);
	if($(obj).prop('checked')){
		if(index==-1 && count < 6){
			param.push(pic);
			count = count + 1;
		}else if(count >= 6){
			layer.msg("最多只能选择6张图片进行对比！");
			$(obj).prop('checked', false);
			return false;
		}
	}else{
		if(index >= 0){
			param.splice(index, 1);
			count = count - 1;
		}
	}
	
}

function existscheck(url){
	for(var o in param){
	 	if(param[o].url == url){
	 		return o;
	 	}
	}
	return -1;
}

function showwindow(obj){
	var url="piccontrast.php";
	if(param.length == 0){
		layer.msg("请选择图片！");
		return false;
	}
	post(url, {'pics': JSON.stringify(param)});
}

function post(URL, PARAMS) { 
	var temp_form = document.createElement("form");      
    temp_form.action = URL;      
    temp_form.target = "_blank";
    temp_form.method = "post";      
    temp_form.style.display = "none";
    for (var x in PARAMS) { 
    	var opt = document.createElement("textarea");      
        opt.name = x;      
        opt.value = PARAMS[x];      
        temp_form.appendChild(opt);      
    }      
    document.body.appendChild(temp_form);      
    temp_form.submit();     
}

function showpic(obj){
	var pic={};
	var arr=[];
	pic.url = $(obj).prop('src');
	var url = "pic.php";
	pic.date = $(obj).next().next().text();
	pic.note = $(obj).next().text();
	pic.dname = $(obj).next().next().next().text();
	arr.push(pic);
	post(url, {"pics": JSON.stringify(arr)});
}
</script>