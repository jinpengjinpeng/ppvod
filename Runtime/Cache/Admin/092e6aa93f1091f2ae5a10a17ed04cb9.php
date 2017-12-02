<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>定时采集生成器</title>
<style type="text/css">
table{ border:1px solid #F0F0F0; font-size:12px; width:800px; padding:5px; font-size:14px;}
table th{height:80px;background:#DEEDFE;color:#0000FF;font-size:16px;}
select{width:95%;height:400px;font-size:14px;}
input{ text-align:center; font-size:14px; font-weight:bold; color: #00F}
.submit{height:28px;line-height:24px;border:1px solid #c6d2e3;background:url("__PUBLIC__/images/admin/button_bg.gif");font-size:13px;cursor:pointer;letter-spacing:3px; font-weight:normal; color:#000}
.w100{ width:100px}
.w500{ width:500px}
</style></head>
<body>
<form action="?s=Admin-Cj-Wait" method="post">
<table border='0' cellpadding='0' cellspacing='0' align='center'>
<tr><th colspan="2">API视频资源库定时采集与静态生成</th></tr>
<tr>
<td width="60%" rowspan="5">
<select name="ds[url][]" multiple>
<?php if(is_array($list_cj)): $i = 0; $__LIST__ = $list_cj;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$feifei): ++$i;$mod = ($i % 2 )?><option value="?s=Admin-Cj-Apis-cjid-<?php echo ($feifei["cj_id"]); ?>-cjtype-<?php echo ($feifei["cj_type"]); ?>-action-days-xmlurl-<?php echo (base64_encode($feifei["cj_url"])); ?>-h-24"><?php echo ($i); ?>、<?php echo ($feifei["cj_name"]); ?> <?php echo ($feifei["cj_url"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
</select>
</td>
<td>采集当天数据频率(分钟)： <input name="ds[caiji]" type="text"  value="30" class="w100"/></td>
</tr>
<tr>
  <td>采集后生成静态间隔(分钟)： <input name="ds[create]" type="text"  value="5" class="w100"/></td>
</tr>
<tr>
  <td>使用方法：从左侧选择需要定时采集的资源库，并设定定时频率提交即可。</td>
</tr>
<tr><td>注：为节约服务器资源，分类页将不生成，请手动定期生成；不需要定时操作的模块，请填写0。</td></tr>
<tr><td colspan="2" style="text-align:center"><input type="submit" class="submit" value="执行定时任务"/></td></tr>
</table>
</form>
</body>
</html>