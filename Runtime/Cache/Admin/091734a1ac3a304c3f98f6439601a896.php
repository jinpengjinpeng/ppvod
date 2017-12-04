<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>用户管理</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/admin-style.css" />
<script charset="utf-8" src="__PUBLIC__/jquery/1.11.3/jquery.min.js"></script>
<script charset="utf-8" src="__PUBLIC__/js/admin.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	//影币操作
	$('.user-score').on('click',function(){
		var offset = $(this).offset();
		$('#showbg').css({width:$(window).width(),height:$(window).height()});	
		$('#form-score').css({left:offset.left,top:offset.top+20}).show();
		$('.user-score-tips').html('增加20填+20；减少20填-20');
	});
	$('#form-score input[type=reset]').on('click',function(){
		$('#showbg').css({width:0,height:0});
		$('#form-score').hide();
	});
	$("#form-score").on('submit',function(){
		if(!$.isNumeric($(this).find('#score_ext').val())){
			$(this).find('#score_ext').select();
			$('.user-score-tips').html('请填写数值，增加20填+20；减少20填-20。');
			return false;
		}
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			dataType: 'json',
			timeout: 6000,
			data: $(this).serialize(),
			beforeSend: function(xhr){
				$('.user-score-tips').html('loading...');
			},
			error : function(){
				$('.user-score-tips').html('<a href="javascript:location.reload();">请求失败，请刷新网页。</a>');
			},
			success: function(json){
				if(json.status == 200){
					location.reload();
				}else{
					$('.user-score-tips').html(json.info);
					$('#form-score').hide();
				}
			},
			complete: function(xhr){
			}
		});
		return false;			
	});
	//VIP时间
	$('.user-deadtime').on('click',function(){
		var offset = $(this).offset();
		$('#showbg').css({width:$(window).width(),height:$(window).height()});	
		$('#form-deadtime').css({left:offset.left,top:offset.top+20}).show();
		$('.user-deadtime-tips').html('增加2天填+2；减少2天填-2');
	});
	$('#form-deadtime input[type=reset]').on('click',function(){
		$('#showbg').css({width:0,height:0});
		$('#form-deadtime').hide();
	});
	$("#form-deadtime").on('submit',function(){
		if(!$.isNumeric($(this).find('#score_ext').val())){
			$(this).find('#score_ext').select();
			$('.user-deadtime-tips').html('请填写数值，增加2天填+2；减少2天填-2。');
			return false;
		}
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			dataType: 'json',
			timeout: 6000,
			data: $(this).serialize(),
			beforeSend: function(xhr){
				$('.user-deadtime-tips').html('loading...');
			},
			error : function(){
				$('.user-deadtime-tips').html('<a href="javascript:location.reload();">请求失败，请刷新网页。</a>');
			},
			success: function(json){
				if(json.status == 200){
					location.reload();
				}else if(json.status == 501){
					$('.user-deadtime-tips').html('影币不足，共需要影币（'+json.info+'）个，请先手动添加影币。');
					$('#showbg').css({width:0,height:0});
					$('#form-deadtime').hide();
				}else{
					$('.user-deadtime-tips').html(json.info);
					$('#showbg').css({width:0,height:0});
					$('#form-deadtime').hide();
				}
			},
			complete: function(xhr){
			}
		});
		return false;			
	});
});
</script>
</head>
<body class="body">
<script charset="utf-8" src="__PUBLIC__/jquery/1.11.3/jquery.min.js"></script>
<!--图片预览框-->
<div id="showpic" class="showpic" style="display:none;"><img name="showpic_img" id="showpic_img" width="120" height="160"></div>
<!--背景灰色变暗-->
<div id="showbg" style="position:absolute;left:0px;top:0px;filter:Alpha(Opacity=0);opacity:0.0;background-color:#fff;z-index:8;"></div>
<form action="?s=Admin-User-Update" method="post" name="myform" id="myform">
<?php if(($user_id)  >  "0"): ?><input type="hidden" name="user_id" id="user_id" value="<?php echo ($user_id); ?>"><?php endif; ?>
<div class="title">
	<div class="left">用户中心</div>
    <div class="right"><a href="?s=Admin-User-Show">返回用户管理</a></div>
</div>
<div class="add">
<ul><li class="left">用户名：</li>
  <li class="right"><input type="text" name="user_name" id="user_name" value="<?php echo ($user_name); ?>" class="w200"></li>
</ul>
<ul><li class="left">用户邮箱：</li>
   <li class="right"><input type="text" name="user_email" id="user_email" value="<?php echo ($user_email); ?>" class="w200"></li>
</ul>
<ul><li class="left">用户头像：</li>
   <li class="right">
   <label style="float:left; margin-top:3px; margin-right:5px"><input type="text" name="user_face" id="user_face" value="<?php echo ($user_face); ?>" class="w200" onMouseOver="if(this.value)showpic(event,this.value,'<?php echo C("upload_path");?>/');" onMouseOut="hiddenpic();"></label>
   <iframe src="?s=Admin-Upload-Show-sid-user-fileback-user_face" scrolling="no" topmargin="0" width="280" height="26" marginwidth="0" marginheight="0" frameborder="0" align="left" style="margin-top:3px; float:left"></iframe>
   </li>
</ul>
<ul><li class="left">用户密码：</li>
  <li class="right"><input type="text" name="user_pwd" id="user_pwd" value="" class="w200"><label>留空则不修改密码</label></li>
</ul>
<ul><li class="left">用户影币：</li>
   <li class="right">
   <input type="text" name="user_score" id="user_score" value="<?php echo (($user_score)?($user_score):0); ?>" class="w200 text-center" disabled="disabled">
   <?php if(($user_id)  >  "0"): ?><label><a class="user-score" href="javascript:;">影币增减</a> <span class="user-score-tips" style="display:inline-block"></span></label><?php endif; ?>
   </li>
</ul>
<ul><li class="left">VIP到期时间：</li>
   <li class="right">
   <input type="text" name="user_deadtime" id="user_deadtime" value="<?php echo (date('Y-m-d H:i:s',$user_deadtime)); ?>" class="w200" disabled="disabled">
   <?php if(($user_id)  >  "0"): ?><label><a class="user-deadtime" href="javascript:;">VIP续期</a> <span class="user-deadtime-tips" style="display:inline-block"></span></label><?php endif; ?>
   </li>
</ul>
<ul><li class="left">最后登录IP：</li>
   <li class="right"><input type="text" name="user_logip" id="user_logip" value="<?php echo ($user_logip); ?>" class="w200"></li>
</ul>
<ul><li class="left">最后登录时间：</li>
   <li class="right"><input type="text" name="user_logtime" id="user_logtime" value="<?php echo (date('Y-m-d H:i:s',$user_logtime)); ?>" class="w200"></li>
</ul>
<ul><li class="left">登录总次数：</li>
   <li class="right"><input type="text" name="user_lognum" id="user_lognum" value="<?php echo ($user_lognum); ?>" class="w200"></li>
</ul>
<ul><li class="left">用户注册IP：</li>
   <li class="right"><input type="text" name="user_joinip" id="user_joinip" value="<?php echo ($user_joinip); ?>" class="w200"></li>
</ul>
<ul><li class="left">用户注册时间：</li>
   <li class="right"><input type="text" name="user_jointime" id="user_jointime" value="<?php echo (date('Y-m-d H:i:s',$user_jointime)); ?>" class="w200"></li>
</ul>
<ul><li class="left">用户QQ：</li>
   <li class="right"><input type="text" name="user_qq" id="user_qq" value="<?php echo ($user_qq); ?>" class="w200"></li>
</ul>
<ul><li class="left">用户状态：</li>
   <li class="right">
   <input type="text" name="user_status" id="user_status" value="<?php echo ($user_status); ?>" class="w200 text-center">
   <label>0=未审核，1=正常用户</label>
   </li>
</ul>
<ul><li class="left">上级用户：</li>
  <li class="right"><input type="text" name="user_pid" id="user_pid" value="<?php echo (($user_pid)?($user_pid):1); ?>" maxlength="11" class="w200 text-center" disabled="disabled"></li>
</ul>
<ul class="footer">
<input type="submit" name="submit" value="提交"> <input type="reset" name="reset" value="重置"> <?php if(($admin_id)  >  "0"): ?>注：不修改密码请留空<?php endif; ?>
</ul>
</div>
</form>
<?php if(($user_id)  >  "0"): ?><form action="?s=Admin-User-Score" name="form-score" id="form-score" class="form-ajax" method="post" target="_blank">
<input type="text" size="8" name="score_ext" id="score_ext" onMouseOver="this.select();">
<input type="hidden" name="score_uid" id="score_uid" value="<?php echo ($user_id); ?>">
<input type="submit" name="submit" class="submit" value="确定"/>
<input type="reset" name="reset" class="submit" value="取消" />
</form>
<form action="?s=Admin-User-Deadtime" name="form-deadtime" id="form-deadtime" class="form-ajax" method="post" target="_blank">
<input type="text" size="8" name="score_ext" id="score_ext" onMouseOver="this.select();">
<input type="hidden" name="user_id" id="user_id" value="<?php echo ($user_id); ?>">
<input type="hidden" name="user_deadtime" id="user_deadtime" value="<?php echo ($user_deadtime); ?>">
<input type="hidden" name="user_score" id="user_score" value="<?php echo ($user_score); ?>">
<input type="submit" name="submit" class="submit" value="确定"/>
<input type="reset" name="reset" class="submit" value="取消" />
</form><?php endif; ?>
<center>Powered by <a href="<?php echo L("feifeicms_homeurl");?>" target="_blank">feifeicms</a> <font color="#FF0000"><?php echo L("feifeicms_version");?></font></center>
</body>
</html>