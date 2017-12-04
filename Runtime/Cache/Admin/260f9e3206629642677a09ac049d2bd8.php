<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>用户注册与访问配置</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel='stylesheet' type='text/css' href='__PUBLIC__/css/admin-style.css' />
<script charset="utf-8" src="__PUBLIC__/jquery/1.11.3/jquery.min.js"></script>
<script charset="utf-8" src="__PUBLIC__/js/admin.js"></script>
<script>
$(document).ready(function(){
	// 评论功能切换
	$(":radio[name='config[forum_type]']").on("click", function(){
		$id = $(this).val();
		forum_type_tab($id);
	});
	forum_type_check('<?php echo ($forum_type); ?>');
});
function forum_type_tab($id){
	if($id!='feifei'){
		$('#forum_type').show();
		$('.forum_type').hide();
		$("#forum_"+$id).show();
	}else{
		$('#forum_type').hide();
	}
}
function forum_type_check($forum_type){
	if($forum_type=='uyan'){
		$('#forum_radio_uyan').attr("checked", true);
	}else if($forum_type=='changyan'){
		$('#forum_radio_changyan').attr("checked", true);
	}else{
		$('#forum_radio_feifei').attr("checked", true);
	}
	forum_type_tab($forum_type);
}
</script>
</head>
<body class="body">
<div class="title">
  <div class="left">用户注册、留言、评论参数配置</div>
</div>
<div class="add">
  <form action="?s=Admin-Config-Update-type-user" method="post" name="myform" id="myform">
  <ul><li class="left">是否开放注册：</li>
    <li class="right">
    	<input type="radio" class="radio" name="config[user_register]" value="1" <?php if(($user_register)  ==  "1"): ?>checked="checked"<?php endif; ?> />开启
      <input type="radio" class="radio" name="config[user_register]" value="0" <?php if(($user_register)  ==  "0"): ?>checked="checked"<?php endif; ?> />关闭
     </li>
  </ul>
  <ul><li class="left">注册是否需要审核：</li>
    <li class="right">
    <input type="radio" class="radio" name="config[user_register_check]" value="1" <?php if(($user_register_check)  ==  "1"): ?>checked="checked"<?php endif; ?>/>开启
    <input type="radio" class="radio" name="config[user_register_check]" value="0" <?php if(($user_register_check)  ==  "0"): ?>checked="checked"<?php endif; ?>/>关闭
    </li>
  </ul>
  <ul><li class="left">用户评论功能模块选择：</li>
    <li class="right" style="text-align:left">
    <input type="radio" class="radio" name="config[forum_type]" id="forum_radio_feifei" value="feifei"/>系统自带
    <input type="radio" class="radio" name="config[forum_type]" id="forum_radio_uyan" value="uyan"/>有言
    <input type="radio" class="radio" name="config[forum_type]" id="forum_radio_changyan"value="changyan"/>畅言
    </li>
  </ul>
  <ul id="forum_type" style="display:none;">
  	<li class="left">社交插件参数配置：</li>
    <li class="right forum_type" id="forum_uyan" style="display:none;">
    <input type="text" name="config[forum_type_uyan_uid]" value="<?php echo (($forum_type_uyan_uid)?($forum_type_uyan_uid):'1528513'); ?>" maxlength="50" class="w400">
    <label>用户ID</label>
    </li>
    <li class="right forum_type" id="forum_changyan" style="display:none;">
    <p><input type="text" name="config[forum_type_changyan_appid]" value="<?php echo (($forum_type_changyan_appid)?($forum_type_changyan_appid):'cysXPib5E'); ?>" maxlength="100" class="w400">
    <label>APPID</label></p>
    <p><input type="text" name="config[forum_type_changyan_conf]" value="<?php echo (($forum_type_changyan_conf)?($forum_type_changyan_conf):'prod_68505537e56f813cfafdcf88027d242e'); ?>" maxlength="100" class="w400">
    <label>CONF</label></p>
    </li>
  </ul>
  <ul><li class="left">用户评论与留言需登录：</li>
    <li class="right">
    <input type="radio" class="radio" name="config[user_forum]" value="1" <?php if(($user_forum)  ==  "1"): ?>checked="checked"<?php endif; ?>/>开启
    <input type="radio" class="radio" name="config[user_forum]" value="0" <?php if(($user_forum)  ==  "0"): ?>checked="checked"<?php endif; ?>/>关闭
    </li>
  </ul>
  <ul><li class="left">用户评论与留言需审核：</li>
    <li class="right">
    <input type="radio" class="radio" name="config[user_check]" value="1" <?php if(($user_check)  ==  "1"): ?>checked="checked"<?php endif; ?>/>开启
    <input type="radio" class="radio" name="config[user_check]" value="0" <?php if(($user_check)  ==  "0"): ?>checked="checked"<?php endif; ?>/>关闭
    </li>
  </ul>  
  <ul><li class="left">用户推广奖励影币：</li>
    <li class="right"><input type="text" class="w120" name="config[user_register_score_pid]" maxlength="4" value="<?php echo (($user_register_score_pid)?($user_register_score_pid):0); ?>"></li>
  </ul>
  <ul><li class="left">用户注册后赠送影币：</li>
    <li class="right"><input type="text" class="w120" name="config[user_register_score]" maxlength="4" value="<?php echo (($user_register_score)?($user_register_score):0); ?>"></li>
  </ul>  
  <ul><li class="left">用户注册后赠送VIP天数：</li>
    <li class="right"><input type="text" class="w120" name="config[user_register_vipday]" maxlength="4" value="<?php echo (($user_register_vipday)?($user_register_vipday):0); ?>"></li>
  </ul>
  <ul><li class="left">用户注册时间间隔（秒）：</li>
    <li class="right"><input type="text" class="w120" name="config[user_register_second]" maxlength="4" value="<?php echo ($user_register_second); ?>"></li>
  </ul>
  <ul><li class="left">用户交互时间间隔（秒）：</li>
    <li class="right"><input type="text" class="w120" name="config[user_second]" maxlength="4" value="<?php echo ($user_second); ?>">
    <label>两次评论/留言/顶踩/评分等间隔时间多少秒</label></li>
  </ul>
  <ul><li class="left">用户充值最低金额：</li>
    <li class="right"><input type="text" class="w120" name="config[user_pay_small]" maxlength="8" value="<?php echo ($user_pay_small); ?>">
    <label>用户每次最低充值金额</label>
    </li>
  </ul>    
  <ul><li class="left">用户充值影币比例：</li>
    <li class="right"><input type="text" class="w120" name="config[user_pay_scale]" maxlength="8" value="<?php echo ($user_pay_scale); ?>">
    <label>1元人民币等于多少影币</label></li>
  </ul>
  <ul><li class="left">VIP售价（影币/天）：</li>
    <li class="right"><input type="text" class="w120" name="config[user_pay_vip_ext]" maxlength="8" value="<?php echo ($user_pay_vip_ext); ?>">
    <label>按（天）计算</label></li>
  </ul>
  <ul><li class="left">购买VIP最少天数：</li>
    <li class="right"><input type="text" class="w120" name="config[user_pay_vip_small]" maxlength="8" value="<?php echo ($user_pay_vip_small); ?>">
    <label>用户升级时自动计算所需影币。</label></li>
  </ul>
  <ul><li class="left">用户发贴敏感词过滤 <font color="red">用|分隔</font>：</li>
    <li class="right" style="height:165px;"><textarea name="config[user_replace]" id="user_replace" style="width:400px;height:150px"><?php echo ($user_replace); ?></textarea></li>
  </ul> 
  <!-- -->
  <ul class="footer">
    <input type="submit" name="submit" value="提交"> <input type="reset" name="reset" value="重置">
  </ul>
  </form>
</div>
<center>Powered by <a href="<?php echo L("feifeicms_homeurl");?>" target="_blank">feifeicms</a> <font color="#FF0000"><?php echo L("feifeicms_version");?></font></center>
</body>
</html>