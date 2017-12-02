<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Top</title>
<link rel='stylesheet' type='text/css' href='__PUBLIC__/css/admin-top.css' />
<script charset="utf-8" src="__PUBLIC__/jquery/1.11.3/jquery.min.js"></script>
<script charset="utf-8" src="__PUBLIC__/js/admin.js"></script>
<script >
$(document).ready(function(){
	$("#clear").click(function(){
		$ajaxurl = $(this).attr('href');
		$.get($ajaxurl,null,function(data){
			$("#cache").show();
			$("#cache").html(' <font color=#ff6600>'+data+'</font> ');
			window.setTimeout(function(){
				$("#cache").hide();
			},2000);
		});
		return false;
	});
	$("#cache").click(function(){
		$("#cache").hide();
		return false;
	});
});
var Tabs = function(obj,sid){
	var tabList = document.getElementById("topmenu").getElementsByTagName("a");
	for(var i=0;i<tabList.length;i++){
		if(obj.innerHTML == tabList[i].innerHTML){
			tabList[i].className = "abcd";
		}else{
			tabList[i].className = "";
		}
	}
	var _frmleft = parent.left;
	for(var i=0;i<12;i++){
		if(i == sid){
			_frmleft.document.getElementById('menu'+i).style.display = 'block';
		}else{
			_frmleft.document.getElementById('menu'+i).style.display = 'none';
		}
	}
}
</script>
</head>
<body>
<div class="header">
  <div class="logo">
  	<img src="__PUBLIC__/images/admin/top_logo.gif">
  </div>
  <div class="tabs" id="topmenu">
    <li><a href="?s=Admin-Config-Base" target="right" onClick="Tabs(this,0)"><span>系 统</span></a></li>
    <li><a href="?s=Admin-Cache-Show" target="right" onClick="Tabs(this,1)"><span>工 具</span></a></li>
    <li><a href="?s=Admin-Nav-Show" target="right" onClick="Tabs(this,2)"><span>导 航</span></a></li>
    <li><a href="?s=Admin-List-Show" target="right" onClick="Tabs(this,3)"><span>分 类</span></a></li>
    <li><a href="?s=Admin-Cj-Show-type-1" target="right" onClick="Tabs(this,4)"><span>采 集</span></a></li>
    <li><a href="?s=Admin-Vod-Show" target="right" onClick="Tabs(this,5)"><span>视 频</span></a></li>
    <li><a href="?s=Admin-News-Show" target="right" onClick="Tabs(this,6)"><span>文 章</span></a></li>
    <li><a href="?s=Admin-Special-Show" target="right" onClick="Tabs(this,7)"><span>专 题</span></a></li>
    <li><a href="?s=Admin-Forum-Show" target="right" onClick="Tabs(this,8)"><span>评 论</span></a></li>
    <li><a href="?s=Admin-User-Show" target="right" onClick="Tabs(this,9)"><span>用 户</span></a></li>
    <li><a href="?s=Admin-Create-Show" target="right" onClick="Tabs(this,10)"><span>生 成</span></a></li>
    <li><a href="?s=Admin-Data-Show" target="right" onClick="Tabs(this,11)"><span>数据库</span></a></li>
  </div>
</div>
<div class="tophr">
	<div class="left">你好：<?php echo $_SESSION["admin_name"];?>，<?php echo L("feifeicms_welcome");?></div>
  <div class="right"><a href="?s=Admin-Cache-Del" target='right' id="clear" title="包括模板,数据库等系统缓存">清理系统缓存</a> <a id="cache"></a> | <a href="?s=Admin-Index" target='_top'>后台首页</a> | <a href="<?php echo C("site_path");?>" target="_blank">网站首页</a> | <a href="?s=Admin-Login-Logout" target="_top">注销退出</a></div>
</div>
</body>
</html>