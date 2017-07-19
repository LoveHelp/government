<!Doctype html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<link rel="stylesheet" type="text/css" href="../css/common.css">
<script src="../js/jquery.min.js"></script>
<script src="../js/layer/layer.js"></script>
<title>菜单管理</title>
<style type="text/css">
.main{margin-top:10px;}
#menucontent{margin-top:10px;}
.select{width:100%;}
input[type="text"]{width:98%;border:none;overflow:hidden;}
tr{height:32px;line-height:32px;background-color:#DEEFFF;}
#searchCon{padding:15px;}
</style>
</head>
<?php 
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
include_once "../mysql.php";
if(isset($_POST["menuid"])) {changemenu();}
function querymenu(){		
$mLink=new mysql;
$sqstr="select * from menu";
if(!isset($_GET["topmenu"])){
	//echo "<script type='text/javascript'> alert('querymenu');</script>";
	$output=$mLink->getAll($sqstr);
	}else{
	$topmenu=trim($_GET["topmenu"]);
	if(!empty($topmenu)){
			$sqstr=$sqstr." where menuclass=?";
			$output=$mLink->getAll($sqstr,Array($topmenu));
			}else{
			$output=$mLink->getAll($sqstr);
			}
	}
if(empty($output))
			{
				echo '<tr class="alternate_line1"><td colspan="6" align="center"><font size="2">没有符合条件的纪录</font></td></tr>';
			}    	    
			else 
			{
					foreach($output as $v)
					{	if($v["menuid"]%2 == 0){
							echo '<tr onclick="changerow(this);" class="alternate_line1">';
						}else{
							echo '<tr onclick="changerow(this);" class="alternate_line2">';
						}					
						
						echo '<td width="5%" height="100%" align="center">'.$v["menuid"].'</td>';
						echo '<td width="20%" height="100%" align="center">'.$v["name"].'</td>';
						echo '<td width="20%" height="100%">'.$v["menuico"].'</td>';
						echo '<td width="20%" height="100%">'.$v["menuurl"].'</td>';
						echo '<td width="20%" height="100%" align="center">'.$v["menuclass"].'</td>';
						echo '<td width="20%" height="100%" align="center"><input type="button" value="修改" style="height:30px;width:100%;cursor:pointer"; /></td>';
						echo '</tr>';
					}
			}
}
//添加或修改菜单	
function changemenu(){
$menuid=$_POST["menuid"];
$menuname=$_POST["menuname"];
$menuico=$_POST["menuico"];
$menuurl=$_POST["menuurl"];
$menuclass=$_POST["menuclass"];
$mLink=new mysql;
$sqlstr=$menuid.$menuname.$menuico.$menuurl.$menuclass;
if($menuid>-1){
	
	echo $sqlstr."添加";
//	$output=$mLink->getAll($sqstr);
//	}else{
//	$menuclass=trim($_GET["menuclass"]);
//	if(!empty($menuclass)){
//			$sqstr=$sqstr." where menuclass=?";
//			$output=$mLink->getAll($sqstr,Array($_GET["menuclass"]));
//			}else{
//			$output=$mLink->getAll($sqstr);
//			}

	}else{
			echo $sqlstr;
	}
}	
    ?>
<body class="main">
	<div id="search">
	<table width="100%"  cellpadding="4" cellspacing="1" class="table01">
    <tr>
      <td colspan="4" class="table_title" >菜单管理</td>
      </tr>
      <tr>
      <td class="td_title">一级菜单</td>
      <td class="td_content" style="width:190px;">
       <select id="topmenu"  class="select" style="width:180px;">>
	   <option id="topmenu0" ></option>
	   <option id="topmenu1" >台账管理</option>
	   <option id="topmenu2" >综合查询</option>
	   <option id="topmenu3" >信息发布</option>
	   <option id="topmenu4" >系统管理</option>
	   <option id="topmenu5" >个人设置</option>
	   <option  id="topmenu6" >邮件系统</option>
	   </select>
      </td>     
      <td >
       <input type="button"  Class="button1" style='cursor:pointer' onclick="query();" value="查询" /> 
	   <input type="button" class="button1" name="ww" value="添加" onclick="addrow();" style='cursor:pointer'>
      </td>
    </tr>
</table>
</div>
<div id="result">
<table id="menucontent" align="center" cellpadding="5" cellspacing="1" class="table01" width="100%">	
          	<tr>
            <td width="5%" height='100%' class="table_title">序号</td>
            <td width="20%" height='100%' class="table_title">菜单名称</td>
            <td width="20%" height='100%' class="table_title">菜单图标</td>
            <td width="20%" height='100%' class="table_title">菜单链接</td>
            <td width="20%" height='100%' class="table_title">所属分类</td>
			<td width="15%" height='100%' class="table_title">修改操作</td>
            </tr>
		<?php querymenu();?>
        </table>
</div>		
          
  <div class="show" id="tree" title="菜单管理" style="display:none;">
  	<div style="background-color:#DEEFFF;height:auto">
  	<table border="0" cellpadding="0" cellspacing="1" class="tab">
     <tbody>
     <tr>
		<td class="tab-td-title">菜单名称</td>
		<td class="tab-td-content">
      		<input type="hidden" name="menuid" id="menuid" value="">
			<input type="text" name="menuname" id="menuname" value="">
		</td>
	</tr>
	<tr>
		<td class="tab-td-title">菜单图标</td>
		<td class="tab-td-content">
			<input type="text" name="menuico" id="menuico" value="">
		</td>
	</tr>
	<tr>
		<td class="tab-td-title">菜单链接</td>
		<td class="tab-td-content">
			<input type="text" name="menuurl" id="menuurl" value="">
		</td>
	</tr>
	<tr>
		<td class="tab-td-title">菜单分类</td>
		<td class="tab-td-content">
			<input type="text" name="menuclass" id="menuclass" value="">
		</td>
    </tr>
    <tr>  
      <td colspan="2" align="center" id="searchCon"><!--定义好摆放按钮的TD的ID -->
        <input type="submit" value="保存修改" style="cursor:pointer" class="button1" onclick="hch.save();">&nbsp;&nbsp;
        <input type="button" value="关闭窗口" style="cursor:pointer" class="button1" onclick="hch.close();">     
      </td>
    </tr>

  </tbody></table>
  	</div>
  </div>
      
</body>

</html>
<script type="text/javascript"> 
var menuid,menuname,menuico,menuurl,menuclass;
//点击查询按钮后,根据一级菜单类别查询菜单


function query(){
	$("#menucontent").innerHTML="";
  	var topmenu=document.getElementById("topmenu").value;  	
  	self.location.href="menu.php?topmenu="+topmenu;

}
  
 function changerow(myrow){
 	//点击行内内容时,取出行内每格的内容
 	//alert(myrow);
 	menuid=$(myrow).find("td").eq(0).text();
 	menuname=$(myrow).find("td").eq(1).text();
 	menuico=$(myrow).find("td").eq(2).text();
 	menuurl=$(myrow).find("td").eq(3).text();
 	menuclass=$(myrow).find("td").eq(4).text(); 
 	$("#menuid").val(menuid);  	
  	//alert(menuurl);
  	hch.open();  	  	
  }
  
   function addrow(){
 	//点击行内内容时,取出行内每格的内容
 	//alert(myrow);
 	menuid="";
 	menuname="";
 	menuico="";
 	menuurl="";
 	menuclass=""; 
 	$("#menuid").val("-1"); 	
  	//alert(menuurl);
  	hch.open();  	  	
  }
  
	//弹出层
    var hch = {
        inInt: function () {
            //this.showStyle();
        },
        open: function () {
        	//opentype为1是修改,为0是添加
        		$("#menuname").val(menuname);
        		$("#menuico").val(menuico);
        		$("#menuurl").val(menuurl);
        		$("#menuclass").val(menuclass);
            	this.index = layer.open({
                type: 1,
                title: $('#tree').attr("title"),
                skin: 'layui-layer-rim', //加上边框
                area: ['560px', 'auto'], //宽高
                content: $("#tree")
            });

        },
        save: function () {
        	menuid=$("#menuid").val();
 			menuname=$("#menuname").val().trim();
 			menuico=$("#menuico").val().trim();
 			menuurl=$("#menuurl").val().trim();
 			menuclass=$("#menuclass").val().trim();
 			if (menuname !== null && menuname !== undefined && menuname !== '' && menuurl!== null && menuurl !== undefined && menuurl !== '' )
 			{        	     	
			mydata="menuid="+encodeURIComponent(menuid)+"&menuname="+encodeURIComponent(menuname)+"&menuico="+encodeURIComponent(menuico)+"&menuurl="+encodeURIComponent(menuurl)+"&menuclass="+encodeURIComponent(menuclass);
       		$.ajax({
   			type: "POST",
   			url: "savemenu.php",
   			data: mydata,
   			success: function(msg){
   			if(1==msg) {layer.msg("保存成功！");setTimeout("hch.close();query();",500);}else{layer.msg("保存失败,请检查！");}
   				}
			});
			}else{
				alert("菜单名称和菜单链接必须设置.");
			}
        },
        close: function () {
            layer.close(this.index);
        }
    }
    $(function () {
        hch.inInt();
    });
</script>
