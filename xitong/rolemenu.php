<!Doctype html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<link rel="stylesheet" type="text/css" href="../css/common.css">
<script src="../js/jquery.min.js"></script>
<script src="../js/layer/layer.js"></script>
<title>角色管理</title>
<style>
tr{height:32px;line-height:32px;}
#menucontent{margin-top:10px;}
input[type="text"]{width:98%;height:98%;border:none;}
.bg-yellow{background:#FFFFEF;}
#searchCon{padding:15px;}
.input_item{width:135px;float:left;}
</style>
</head>
<?php 
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
include_once "../mysql.php";
function queryrole(){		
$mLink=new mysql;
$sqstr="select roleid,role from rolemenu GROUP BY roleid";
$output=$mLink->getAll($sqstr);
$maxroleid=$mLink->getAll("select max(roleid) from rolemenu");
echo '<input  type="hidden" name="maxroleid" id="maxroleid" value="'.($maxroleid[0]["max(roleid)"]+1).'">'; 
if(!empty($output))
{
	$rolemenu="";
	for($i=0;$i<count($output);$i++)
	{
		
		$sqlmenulist="select rolemenu.menuid,menu.name from rolemenu left join menu on rolemenu.menuid=menu.menuid where roleid=".$output[$i]["roleid"]." order by rolemenu.menuid";
		$menu=$mLink->getAll($sqlmenulist);
		$menulist="";
		$menuidlist="";		
		foreach($menu as $m)
		{
			$menuidlist=$menuidlist.$m["menuid"].",";
			$menulist=$menulist.$m["name"].",";			
		}		
		$menuidlist=substr($menuidlist, 0, strlen($menuidlist)-1);
		$menulist=substr($menulist, 0, strlen($menulist)-1);		
		$output[$i]["menulist"]=$menulist;
		$output[$i]["menuidlist"]=$menuidlist;		
	}
	if(empty($output)){
				echo '<tr class="alternate_line1"><td colspan="6" align="center"><font size="2">没有符合条件的纪录</font></td></tr>';
			}    	    
			else 
			{
					foreach($output as $v)
					{
						if($v["roleid"]%2 == 0){
							echo '<tr onclick="changerow(this);" class="alternate_line1">';	
						}else{
							echo '<tr onclick="changerow(this);" class="alternate_line2">';	
						}
						echo '<td width="5%" height="100%" align="center">'.$v["roleid"].'</td>';
						echo '<td width="20%" height="100%" align="center">'.$v["role"].'</td>';
						echo '<td width="65%" height="100%" >'.$v["menulist"].'</td>';
						echo '<td style="display:none">'.$v["menuidlist"].'</td>';
						echo '<td align="center" width="10%"><input type="button" value="修改" style="height:30px;width:100%;cursor:pointer"/></td>';
						echo '</tr>';
					}
			}
	}
}
?>
<body class="main">
	<div id="search">
	<table width="100%"  cellpadding="4" cellspacing="1" class="table01">
		<tr>
			<td colspan="4" class="table_title" >权限管理</td>
		</tr>
		<tr>    
			<td colspan="4" class="td_title">     
				<input type="button" class="button1" name="ww" value="添加" onclick="addrow();" style='cursor:pointer'>
			</td>
		</tr>
	</table>
	</div>
	<div id="result">
	<table id="menucontent" align="center" cellpadding="5" cellspacing="1" class="table01" width="100%">	
        <tr>
            <td width="5%" height='100%' class="table_title">序号</td>
            <td width="20%" height='100%' class="table_title">角色名称</td>
            <td width="65%" height='100%' class="table_title">权限菜单</td>
            <td width="10%" height='100%' class="table_title">权限修改</td>
        </tr>
		<?php queryrole();?>
    </table>
    </div>  
          
  <div class="show" id="tree" title="角色管理" style="align:center;display:none;">
  	<div style="background-color:#DEEFFF;height:auto;">
  	<table border="0" cellpadding="0" cellspacing="1" class="tab">
     <tbody>
     <tr>
		<td class="tab-td-title" style="width:100px;">角色序号</td>
		<td class="tab-td-content">      	
			<input disabled type="text" name="roleid" id="roleid" value="">       
		</td>
		<td class="tab-td-title">角色名称</td>
		<td class="tab-td-content">
			<input type="text" name="role" id="role" value="">
		</td>
	</tr>
         
 <?php
 $Link=new mysql;
 $sql="select menuclass from menu group by menuclass order by menuid";
 $menuc=$Link->getAll($sql);
 //var_dump($menu); 
 foreach($menuc as $v)
 {
 	echo '<tr><td class="tab-td-title" align="center">'.	$v["menuclass"].'</td><td class="tab-td-content bg-yellow" colspan="3">';	
	$sql="select menuid,name from menu where menuclass='".$v["menuclass"]."' order by menuid";
	$menul=$Link->getAll($sql);
	if(!empty($menul)){

	 foreach($menul as $l)
	 {
	 	echo '<div class="input_item"><input name="menucheck" style="cursor:pointer" type="checkbox" id="'.$l["menuid"].'" />'.$l["name"].'</div>';
	 }
	 echo '</td></tr>';
 	}
 }
 ?>
    <tr>  
      <td colspan="4" align="center" id="searchCon"><!--定义好摆放按钮的TD的ID -->
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
var roleid,role,action;

function query(){
  	self.location.href="rolemenu.php";
}
  
 function changerow(myrow){
 	//点击行内内容时,取出行内每格的内容 	
 	action=1;
 	roleid=$(myrow).find("td").eq(0).text();
 	role=$(myrow).find("td").eq(1).text();
	var menulist = $(myrow).find("td").eq(3).text();	
	//console.log(menulist);
	var menuchecked=menulist.split(",");
 	var checkboxs=$("input[name='menucheck']");
 	for (var i = 0; i < checkboxs.length; i++) {//清空选中状态
	 checkboxs[i].checked = false;
	}
 	 for (var i = 0; i < menuchecked.length; i++) {//获取选中状态 	 	
		document.getElementById(menuchecked[i]).checked = true;
	}	
  	hch.open();  	  	
  }
  
   function addrow(){
 	//点击行内内容时,取出行内每格的内容 	
 	action=0;
 	roleid=$("#maxroleid").val();
 	role="";
 	var checkboxs=$("input[name='menucheck']");
 	 for (var i = 0; i < checkboxs.length; i++) {//设定选中状态
	 checkboxs[i].checked = false;
	}	
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
        		$("#roleid").val(roleid);
        		$("#role").val(role);
            	this.index = layer.open({
                type: 1,
                title: $('#tree').attr("title"),
                skin: 'layui-layer-rim', //加上边框
                area: ['800px;', 'auto'], //宽高
                content: $("#tree")
            });

        },
        save: function () {
        	roleid=$("#roleid").val();
 			role=$("#role").val().trim();
 			var savemenuidlist="";
 			var checkboxs=$("input[name='menucheck']");
 			for (var i = 0; i < checkboxs.length; i++) {//设定选中状态
			if(checkboxs[i].checked ==true){
				savemenuidlist=savemenuidlist+checkboxs[i].id+",";
				}				
			}
			savemenuidlist=savemenuidlist.substr(0,savemenuidlist.length-1);
			//console.log(savemenuidlist);
 			if (role !== null && role !== undefined && role !== "")
 			{ 				        	     	
			mydata="roleid="+encodeURIComponent(roleid)+"&role="+encodeURIComponent(role)+"&savemenuidlist="+encodeURIComponent(savemenuidlist)+"&action="+encodeURIComponent(action);
       		$.ajax({
   			type: "POST",
   			url: "saverolemenu.php",
   			data: mydata,
   			success: function(msg){
   			if(1==msg) {layer.msg("保存成功");var t=setTimeout("hch.close();query();",1000);}else{layer.msg("保存失败");}
   				}
			});
			}else{
				alert("角色名称必须设定.");
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
