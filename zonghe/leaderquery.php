<?php 
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}

include_once '../mysql.php';

$sql = "select leadername lname, leaderphoto lphoto, deptids, leaderId from leader order by leadersort;";
$pdo = new mysql;
$res = $pdo->getAll($sql);

$count=0;
if($res)
	$count = sizeof($res);

?>
<!doctype html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>南阳市人民政府领导</title>
	<link rel="stylesheet" href="../css/common.css" />
<style>
div.title{font-size:20px;height:40px;line-height:40px;}
.container{background:#ecf7ff;padding:8px 33px;text-align:center;}
.container ul li{border-bottom:1px #ccdce8 solid;padding:16px 0;}
.container ul li.zl{height:auto;overflow:hidden;}
.container ul li.zl .fl{padding-top:66px;}
.container ul li .fl{font-size:18px;font-weight:bold;}
.container ul li .fr{width:552px;overflow:hidden;display:inline;}
.container ul li dl{width:84px;float:left;margin-right:33px;}
a{text-decoration:none;color:#000;}
.fl{float:left;}
.fr{float:left;padding-left:20px;}
body{font-family:"宋体";}
div.llist{margin-top:30px;}
div.lbody ul{overflow:hidden;}
</style>
</head>
<body class="main">
	<div class="title">南阳市人民政府领导</div>
	<!--<div class="container">
       	  <ul>
        	  <li class="zl">
        	    <div class="fl">市&nbsp;&nbsp;&nbsp;长</div>
                <div class="fr">
                	<dl>
					<?php
					if($count > 0){
						echo '<dt><a href="deptquery.php?deptids="' . $res[0]['deptids'] . '"&lname="' . $res[0]['lname'] . '"><img src="' . $res[0]['lphoto'] . '" width="91" height="115"></a></dt>'
							. '<dt><a href="deptquery.php?deptids="' . $res[0]['deptids'] . '"&lname="' . $res[0]['lname'] . '">' . $res[0]['lname']  . '</a></dt>';
					}
					?>
					</dl>
                </div>
              </li>
        	  <li class="zl">
        	    <div class="fl">副&nbsp;市&nbsp;长</div>
                <div class="fr">
					<?php
					for($i=1; $i<$count; $i++){
						$class="";
						if($i%4==0){
							$class="lastli";
						}
						echo '<dl><dt><a href="deptquery.php?deptids="' . $res[$i]['deptids'] . '"&lname="' . $res[$i]['lname'] . '"><img src="' . $res[$i]['lphoto'] . '" width="91" height="115"></a></dt>'
							. '<dt><a href="deptquery.php?deptids="' . $res[$i]['deptids'] . '"&lname="' . $res[$i]['lname'] . '">' . $res[$i]['lname']  . '</a></dt></dl>';
					}
					?>
                </div>
              </li>
      	  </ul>
        </div>-->
	<div class="llist">
		<div class="lbody">
			<?php
				if($count > 0){
					?><a href="leaderdetail.php?leaderId=<?=$res[0]['leaderId'] ?>" style="cursor:pointer;">
						<img src="<?=$res[0]['lphoto']?>" />
						<div style="line-height: 35px;"><?=$res[0]['lname']?></div>
					</a><?php
				}
			?>
			<ul>
				<?php
					for($i=1; $i<$count; $i++){
						$class="";
						if($i%4==0)
							$class="lastli";
							// $deptids = empty($res[0]['deptids']) ? 0 : $res[0]['deptids'];
							// $img = empty($res[0]['lphoto']) ? '' : $res[0]['lphoto'];
							// $lname = empty($res[0]['lname']) ? '' : $res[0]['lname'];
						?><li class="<?=$class?>">
							<a href="leaderdetail.php?leaderId=<?=$res[$i]['leaderId'] ?>">
								<img src="<?=$res[$i]['lphoto']?>" />
								<div style="line-height: 35px;"><?=$res[$i]['lname']?></div>
							</a>
						</li><?php
					}
				?>
			</ul>
		</div>
	</div>
	<div style="text-align:center;margin-top:50px;"><a href="leadertasksort.php" style="cursor:hand;color:blue;" >查看市政府领导分管工作完成情况统计</a></div>
</body>
</html>
