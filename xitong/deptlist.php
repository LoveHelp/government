<?php
session_start();
if(!isset($_SESSION['userID'])){
	header('Location:../index.php');
	exit;
}
error_reporting(0);//关闭提示
$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : "";
$userName = isset($_SESSION['userName']) ? $_SESSION['userName'] : "";
include_once "../mysql.php";
include_once "../constant.php";
header("Content-type:text/html;charset=utf-8");
$mLink=new mysql;

$where=" where 1=1";
if(!empty($_POST["deptId_S"])){
	$where.=" and deptId like '%".$_POST["deptId_S"]."%'";
}
if(!empty($_POST["deptCode_S"])){
	$where.=" and deptCode like '%".$_POST["deptCode_S"]."%'";
}
if(!empty($_POST["deptName_S"])){
	$where.=" and deptName like '%".$_POST["deptName_S"]."%'";
}
if(!empty($_POST["areaCode_S"]) && $_POST["areaCode_S"]!="0"){
	$where.=" and areaCode = ".$_POST["areaCode_S"];
}
if(!empty($_POST["status_S"]) && $_POST["status_S"]!="0"){
	$where.=" and status = ".$_POST["status_S"];
}
$where .= " order by deptSort asc";

$res=$mLink->getAll("select * from dept".$where." limit 0,20");
//echo json_encode($res);

?>

<!doctype html>
<html xmlns=http://www.w3.org/1999/xhtml>
<head> 
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<title>部门管理</title>
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/dept.css" />
</head>

<body>

<div class="right-main">
<form action="deptlist.php" method="POST">
	<!--定义查询条件路入框的范围ID-->
	<div id="top_div" class="search">
   <table border="0" cellpadding="0" cellspacing="1" class="tab">
     <tbody><tr>
      <td height="25" colspan="7" class="tab-title" align="center">部门管理</td>
     </tr>
     <tr style="display: none;">
      <td class="tab-td-title">部门编号
      </td><td class="tab-td-content">
        <input type="text" name="deptId_S" value="<?php if(!empty($_POST['deptId_S'])){echo $_POST['deptId_S'];}else{echo "";}?>">
      </td>
      <td class="tab-td-title">组织机构代码</td>
      <td class="tab-td-content">
        <input type="text" name="deptCode_S" value="<?php if(!empty($_POST['deptCode_S'])){echo $_POST['deptCode_S'];}else{echo "";}?>">
      </td>
    </tr>
    <tr>
      <td class="tab-td-title" style="width:120px;">单位类型</td>
      <td class="tab-td-content" style="width:160px;  padding:0px 2px !important;">
        <select name="areaCode_S" id="areaCode_S" style="width:150px;">
	        <option value="0"></option>  
	        <?php 
				if(!empty($areaCode)){
					for ($i=1; $i<=count($areaCode); $i++) {
						echo '<option value="'.$i.'">'.$areaCode[$i].'</option>';
					}
				}
			?>
        </select>	  
      </td>
     <td class="tab-td-title" style="width:120px;">部门名称</td>
      <td class="tab-td-content" style="width:210px; padding:0px 2px !important;">
        <input type="text" name="deptName_S" value="<?php if(!empty($_POST['deptName_S'])){echo $_POST['deptName_S'];}else{echo "";}?>" style="width:200px;">
      </td>
       <td class="tab-td-title" style="width:80px;">状态</td>
       <td class="tab-td-content" style="width:80px;  padding:0px 2px !important;">
          <select name="status_S" id="status_S" style="width:70px;">
          	<option value="0"></option>   
           <option value="1">有效</option>
           <option value="2">无效</option></select>
       </td>
       <td class="tab-td-title" style="text-align:left; padding-left:5px;">
	        <input type="submit" value="查 询" style="cursor:pointer" class="button1">
		   	<!--<input type="reset" value="重 置" style="cursor:pointer" class="button1"> -->  
		   	<input type="button" value="添加" class="button1" style="cursor:pointer" onclick="hch.open('0');">
			<input type="hidden" name="hd_deptId" value="0" />			
      </td>
      </tr>
  </tbody></table>
  </div>
  </form>
  
  <div style="height:10px;"></div>
  
  <div id="result" class="search"><!--定义查询返回结果框的范围ID-->

  <table id="container" border="0" cellpadding="6" cellspacing="1" class="tab" style="background-color:#bebabb;">
          <tbody>
          <tr>
          <td class="tab-title">部门编号</td>
          <td class="tab-title">部门类型 </td>
          <td class="tab-title">部门名称 </td>       
          </tr>
          
<?php 
					  if(!empty($res)){
						foreach ($res as $info){
				?>
				
    <tr class="hang alternate_line1" onclick="hch.open('<?php echo $info['deptId']?>');" style="cursor: pointer;">

  	  <td style="text-align:center;"><?php echo $info['deptId']?></td>
  	  <td style="text-align:center;">
  	  	<?php 
			$type=$info['areaCode'];
			echo $areaCode[$type];
		?>	
  	  	</td>
  	  <td style="text-align:center;"><?php echo $info['deptName']?></td>
  	</tr>

<?php          
					  }}
					?>
  </tbody></table>
	
  </div>
  
  <div id="loadmore" onclick="hch.loadmore();" style="cursor: pointer;height: 35px;line-height: 35px;text-align: center;">加载更多</div>
  
  <div class="show" id="tree" title="添加/修改部门信息" style="display:none;">
  	<div style="padding: 10px 10px;background-color:#DEEFFF;height: 394px;">
  		
  		<table border="0" cellpadding="0" cellspacing="1" class="tab">
     <tbody>
     <tr>
       <td class="tab-td-title">单位类型</td>
       <td class="tab-td-content">
          <select name="areaCode">
          	<?php 
				if(!empty($areaCode)){
					for ($i=1; $i<=count($areaCode); $i++) {
						echo '<option value="'.$i.'">'.$areaCode[$i].'</option>';
					}
				}
			?>
          </select>
        <span id="Star">★</span>
       </td>
      <td class="tab-td-title">部门名称</td>
      <td class="tab-td-content">
        <input type="text" name="deptName" value="">
        <span id="Star">★</span>
      </td>
    </tr>
     <tr>
      <td class="tab-td-title">排序
      </td><td class="tab-td-content">
        <input type="text" name="deptSort" value="0">
        <span id="Star"></span>
      </td>
      <td class="tab-td-title">部门负责人</td>
      <td class="tab-td-content">
        <input type="text" name="deptHead" value="">
      </td>
    </tr>
     <tr>
      <td class="tab-td-title">门户网站名称
      </td><td class="tab-td-content">
        <input type="text" name="webSiteName" value="">
      </td>
      <td class="tab-td-title">门户网站地址</td>
      <td class="tab-td-content">
        <input type="text" name="webSiteAddress" value="">
      </td>
    </tr>
    <tr>
     <td class="tab-td-title">是否在网</td>
      <td class="tab-td-content">
        <select name="isOnline"> 
          	<option value="1">在网</option>
          	<option value="2">不在</option>
        </select>	  
      </td>
      <td class="tab-td-title">是否行政审批部门</td>
      <td class="tab-td-content">
        <select name="isShenpi"> 
	        <option value="1">是</option>
	        <option value="2">否</option>
        </select>	  
      </td>
     </tr>
      <tr>
       <td class="tab-td-title">状态</td>
       <td class="tab-td-content">
          <select name="status"> 
	          <option value="1">有效</option>
	          <option value="2">无效</option>
          </select>
       </td>
      <td class="tab-td-title">组织机构代码
      </td><td class="tab-td-content">
        <input type="text" name="deptCode" value="">
      </td>
      </tr>
     <tr>
      <td class="tab-td-title">备注
      </td>
      <td class="tab-td-content" colspan="3" style="height: 90px;padding-top:10px;">
        	<textarea name="remark" rows="5" cols="70"></textarea>
      </td>
    </tr>
     <tr>
      <td class="tab-td-title">修改人
      </td><td class="tab-td-content">
        <input type="text" name="adminName" disabled="disabled" value="<?php echo $userName;?>">
        <input type="hidden" name="adminCode" disabled="disabled" value="<?php echo $userID;?>">
      </td>
      <td class="tab-td-title">添加时间</td>
      <td class="tab-td-content">
        <input type="text" name="addtime" disabled="disabled" value="<?php echo date('Y-m-d H:i:s',time());?>">
      </td>
    </tr>
    <tr>
      <td colspan="4" id="searchCon"><!--定义好摆放按钮的TD的ID -->
        <input type="submit" value="提 交" style="cursor:pointer" class="button1" onclick="hch.check();">&nbsp;
        <!--<input type="reset" value="重 置" style="cursor:pointer" class="button1" onclick="hch.rewrite();"> &nbsp;-->
        <input type="button" value="关闭窗口" style="cursor:pointer" class="button1" onclick="hch.close();">     
      </td>
    </tr>
  </tbody></table>

  	</div>
  </div>
  
  
</div>

</body>
</html>

    <script type="text/javascript" src="../js/jquery.min.js"></script>
    <script type="text/javascript" src="../js/layer/layer.js"></script>
    <script type="text/javascript">
	var page = 1; 
    var hch = {
        inInt: function () {
            this.showStyle();
            this.load();
        },
        showStyle: function () {//间隔行显示样式
            $.each("table.tab tr.hang", function (i) {
                if (i % 2 > 0) {
                    $("table.tab tr.hang").eq(i).addClass("alternate_line2");
                }
            });
        },
        open: function (deptId) {
            this.index = layer.open({
                type: 1,
                title: $('#tree').attr("title"),
                skin: 'layui-layer-rim', //加上边框
                area: ['auto', 'auto'], //宽高
                content: $("#tree")
            });
	        //console.log(deptId);
            $("input[name='hd_deptId']").val(deptId);
            if(deptId!='0'){//修改，绑定初始值
            	//$("input[name='deptCode']").val(deptCode);
            	$.post("dept_insert_update.php?do=dept_getRow",{'deptId':deptId}, function (res) {
	                //console.log(res.deptCode);
	                $("input[name='deptCode']").val(res.deptCode);
	                $("input[name='deptName']").val(res.deptName);
	                $("input[name='deptSort']").val(res.deptSort);
	                $("input[name='deptHead']").val(res.deptHead);
	                $("input[name='webSiteName']").val(res.webSiteName);
	                $("input[name='webSiteAddress']").val(res.webSiteAddress);
	                $("select[name='isOnline']").val(res.isOnline);
	                $("select[name='isShenpi']").val(res.isShenpi);
	                $("select[name='status']").val(res.status);
	                $("select[name='areaCode']").val(res.areaCode);
	                $("textarea[name='remark']").val(res.remark);
	                $("input[name='adminCode']").val(res.adminCode);
	                $("input[name='addtime']").val(res.addtime);
	            },'json');
            }else{
            	$("input[name='deptCode']").val("");
	                $("input[name='deptName']").val("");
	                $("input[name='deptSort']").val("0");
	                $("input[name='deptHead']").val("");
	                $("input[name='webSiteName']").val("");
	                $("input[name='webSiteAddress']").val("");
	                $("select[name='isOnline']").val("1");
	                $("select[name='isShenpi']").val("1");
	                $("select[name='status']").val("1");
	                $("select[name='areaCode']").val("1");
	                $("textarea[name='remark']").val("");
            }

        },
        close: function () {
            layer.close(this.index);
        },
        check:function(){
        	var deptCode = $("input[name='deptCode']").val();
        	var deptName = $("input[name='deptName']").val();
        	var deptSort = $("input[name='deptSort']").val();
        	var deptHead = $("input[name='deptHead']").val();
        	var webSiteName = $("input[name='webSiteName']").val();
        	var webSiteAddress = $("input[name='webSiteAddress']").val();
        	var isOnline = $("select[name='isOnline']").val();
        	var isShenpi = $("select[name='isShenpi']").val();
        	var status = $("select[name='status']").val();
        	var areaCode = $("select[name='areaCode']").val();
        	var remark = $("textarea[name='remark']").val();
        	var adminCode = $("input[name='adminCode']").val();
        	var addtime = $("input[name='addtime']").val();
        	
//	        if (!deptCode) {
//	            layer.msg("组织机构代码不能为空！");
//	            return false;
//	        }
//	        if(deptCode.length > 9){
//	        	layer.msg("组织机构代码长度不能大于9！");
//	            return false;
//	        }
        	
	        if (!deptName) {
	            layer.msg("部门名称不能为空！");
	            return false;
	        }
        	
	        if (!deptSort) {
	            layer.msg("排序不能为空！");
	            return false;
	        }
	        if(isNaN(deptSort)){
	        	layer.msg("排序只能为数字！");
	            return false;
	        }
	        var deptId=$("input[name='hd_deptId']").val();
	        //console.log(deptId);
	        var param={
	        	'deptId': deptId,
                'deptCode': deptCode,
                'deptName': deptName,
                'deptSort':deptSort,
                'deptHead':deptHead,
                'webSiteName':webSiteName,
                'webSiteAddress':webSiteAddress,
                'isOnline':isOnline,
                'isShenpi':isShenpi,
                'status':status,
                'areaCode':areaCode,
                'remark':remark,
                'adminCode':adminCode,
                'addtime':addtime
	        }
	        //console.log(param);
	        if(deptId=='0'){//添加
	        	$.post("dept_insert_update.php?do=dept_insert",param, function (res) {
      				//console.log("JSON.stringify："+JSON.stringify(res));
      				//console.log(typeof(res));
      				if(res!="0"){
      					layer.msg("添加成功");
            			location.reload();
      				}else{
      					layer.msg("添加失败！");
      				}
	                
	            });
	        }else{//修改
	        	$.post("dept_insert_update.php?do=dept_update",param, function (res) {
	                if(res=="1"){
      					layer.msg("修改成功");
            			location.reload();
      				}else{
      					layer.msg("修改失败！");
      				}
	            });
	        }
            
        },
		load:function(){
			var selvalue_areaCode = '<?php if(!empty($_POST['areaCode_S'])){echo $_POST['areaCode_S'];}else{echo "0";}?>'; 
			if(selvalue_areaCode!=""){
				var t = document.getElementById("areaCode_S");    
				for(i=0;i<t.length;i++){    //给select赋值   
				if(t.options[i].value== selvalue_areaCode){  
						t.options[i].selected=true;
					}
				}
			}
			var selvalue_status = '<?php if(!empty($_POST['status_S'])){echo $_POST['status_S'];}else{echo "0";}?>'; 
			if(selvalue_status!=""){
				var t = document.getElementById("status_S");    
				for(i=0;i<t.length;i++){    //给select赋值   
				if(t.options[i].value== selvalue_status){  
						t.options[i].selected=true;
					}
				}
			}
		},
		loadmore:function(){ //点击div加载更多
    		var pageSize=20;
			var where = '<?php echo $where;?>';
            var param={
	            'page': page,
	            'pageSize':pageSize,
			    'where':where
	        };
			$.post("../pager_scroll.php?do=pager_dept", param, function(res) {  
	        	//console.log(res);
	            if (res.length>0) {  
			        <?php
						$jsObject = 'var arry_areaCode = {';
						foreach($areaCode as $key=>$value)
						{
							$jsObject .= $key.':\''.$value.'\',';
						}
						$jsObject .= 'phpObject:1};';
						echo $jsObject;
					?>
			        //console.log(arry_areaCode);
	                var str = "";  
	                $.each(res, function(index, array) {  
	                	str += "<tr class=\"hang alternate_line1\" onclick=\"hch.open('"+array['deptId']+"');\" style=\"cursor: pointer;\">";
	                	str +="<td style=\"text-align:center;\">"+array['deptId']+"</td>";
	                	str +="<td style=\"text-align:center;\">";
	                	var areaCode = parseInt(array['areaCode']); 
	                	//console.log(areaCode);
	                	str +=arry_areaCode[areaCode];
	                	str +="</td>";
	                	str +="<td style=\"text-align:center;\">"+array['deptName']+"</td>";
	                	str +="</tr>";
		            }); 
	                $("#container").append(str);   
	                page++;  
	            }else {
		            layer.msg("已经全部加载完了。。。");
		            $("#loadmore").html("没有可以加载的了");
		            return false;
		        }
	        },'json');  
		}
    }
    $(function () {
        hch.inInt();
    });
</script>

<!--
	描述：滚动鼠标加载更多
-->
<script type="text/javascript">
	var where = '<?php echo $where;?>';
	<?php
		$jsObject = 'var arry_areaCode = {';
		foreach($areaCode as $key=>$value){
			$jsObject .= $key.':\''.$value.'\',';
		}
		$jsObject .= 'phpObject:1};';
		echo $jsObject;
	?>
	//console.log(arry_areaCode);
    $(window).scroll(function (){ 
    	var pageSize=20;
		totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop()); 
		if($(document).height() <= totalheight){ 
		    var param={
			    'page': page,
			    'pageSize':pageSize,
			    'where':where
			};
			$.post("../pager_scroll.php?do=pager_dept", param, function(res) {  
			    //console.log(res);
			    if (res && res.length>0) {  
			    	var str = "";  
					$.each(res, function(index, array) {  
					    str += "<tr class=\"hang alternate_line1\" onclick=\"hch.open('"+array['deptId']+"');\" style=\"cursor: pointer;\">";
					    str +="<td style=\"text-align:center;\">"+array['deptId']+"</td>";
					    str +="<td style=\"text-align:center;\">";
					    var areaCode = parseInt(array['areaCode']); 
					    str +=arry_areaCode[areaCode];
					    str +="</td>";
					    str +="<td style=\"text-align:center;\">"+array['deptName']+"</td>";
					    str +="</tr>";
					}); 
					$("#container").append(str);  
			        page++;  
			    } else {
			        layer.msg("别滚动了，已经到底了。。。");
				    $("#loadmore").html("没有可以加载的了");
			        return false;
			    }
			},'json'); 
		} 
	});
</script>