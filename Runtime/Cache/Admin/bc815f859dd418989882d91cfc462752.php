<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>资源采集管理</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel='stylesheet' type='text/css' href='__PUBLIC__/css/admin-style.css' />
<script charset="utf-8" src="__PUBLIC__/jquery/1.11.3/jquery.min.js"></script>
<script src="__PUBLIC__/js/admin.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$feifeicms.show.table();
});
</script>
</head>
<body class="body ff-cj">
<div class="title">
	<div class="left">资源库说明</div>
</div>
<div class="add">
	<div class="left">
  <?php if(!empty($jumpurl)): ?><p><a href="<?php echo ($jumpurl); ?>" style="color:#F00" target="_blank">上次有采集任务没有完成，是否接着采集?</a></p><?php endif; ?>
  <p>评论采集适用于建站初期人气较少的情况下模拟真实用户评论，请根据需要采集，可以增加网站内容提高搜索引擎收录机会。</p>
  <p>评论内容需要关联视频，故采集时需检查视频是否有存在相同的来源网址后才新增入库。</p>
  <p>采集评论时需先设置用户随机ID范围，如果未设置，请先设置。</p>
  </div>
</div>
<table border="0" cellpadding="0" cellspacing="0" class="table">
<thead>
<tr><th colspan="6" class="r">
<span style="float:left">API资源站（用户）列表</span>
<span style="float:right"><a href="?s=Admin-Cj-Add">添加资源库</a></span>
</th></tr>
</thead>
<?php if(is_array($list_cj)): $i = 0; $__LIST__ = $list_cj;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$feifei): ++$i;$mod = ($i % 2 )?><tr>
<td class="r pd"><?php echo ($i); ?>、</td>
<td class="r pd"><a href="?s=Admin-Cj-Add-id-<?php echo ($feifei["cj_id"]); ?>" class="h"><b><?php echo ($feifei["cj_name"]); ?></b> <?php echo ($feifei["cj_url"]); ?></a></td>
<td class="l ct"><a href="?s=Admin-Cj-Apis-cjid-<?php echo ($feifei["cj_id"]); ?>-cjtype-5-action-days-xmlurl-<?php echo (base64_encode($feifei["cj_url"])); ?>-h-24" target="_blank">采集当天</a></td>
<td class="l ct"><a href="?s=Admin-Cj-Apis-cjid-<?php echo ($feifei["cj_id"]); ?>-cjtype-5-action-days-xmlurl-<?php echo (base64_encode($feifei["cj_url"])); ?>-h-98" target="_blank">采集本周</a></td>
<td class="l ct"><a href="?s=Admin-Cj-Apis-cjid-<?php echo ($feifei["cj_id"]); ?>-cjtype-5-action-all-xmlurl-<?php echo (base64_encode($feifei["cj_url"])); ?>" target="_blank">采集所有</a></td>
<td class="r ct"><a href="?s=Admin-Cj-Add-id-<?php echo ($feifei["cj_id"]); ?>">修改</a> <a href="?s=Admin-Cj-Del-id-<?php echo ($feifei["cj_id"]); ?>" onClick="return confirm('确定删除吗?')" title="点击删除">删除</a></td>
</tr><?php endforeach; endif; else: echo "" ;endif; ?>
</table>
<center>Powered by <a href="<?php echo L("feifeicms_homeurl");?>" target="_blank">feifeicms</a> <font color="#FF0000"><?php echo L("feifeicms_version");?></font></center>
</body>
</html>