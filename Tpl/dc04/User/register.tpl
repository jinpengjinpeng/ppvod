<!DOCTYPE html>
<html lang="zh-cn">
<head>
<include file="Block:user_header" />
<title>欢迎加入_{$site_name}</title>
<meta name="keywords" content="{$site_name}用户注册界面">
<meta name="description" content="欢迎加入{$site_name}大家庭。">
<script>
$(document).ready(function(){	
$("#ff-register").on('submit',function(e){
	$('#user-alert').html('');
	$('#ff-register .help-block').remove();
	$('#ff-register .form-group').removeClass('has-error');
	if($("#user_name").val().length < 3 || $("#user_name").val().length >12){
		$('#user_name').parent().addClass('has-error');
		$('#user_name').after('<span class="help-block">用户名长度为3-12个字符</span>');
		return false;
	}
	if($("#user_email").val().search(/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/)==-1){
		$('#user_email').parent().addClass('has-error');
		$('#user_email').after('<span class="help-block">请输入正确的EMAIL格式</span>');
		return false;
	}
	if($("#user_pwd").val().length < 6){
		$('#user_pwd').parent().addClass('has-error');
		$('#user_pwd').after('<span class="help-block">请至少输入6个字符作为密码</span>');
		return false;
	}
	$.ajax({
		url: $(this).attr('action'),
		type: 'POST',
		dataType: 'json',
		timeout: 6000,
		data: $(this).serialize(),
		beforeSend: function(xhr){
			$('#user-submit').html('正在注册');
		},
		error : function(){
			$('#user-submit').html('请求失败，请刷新网页。');
		},
		success: function(json){
			if(json.status == 200){
				location = '{:ff_url("user/center",array("action"=>"index"))}';
			}else if(json.status == 201){
				$('#user-submit').html('已注册');
				feifei.alert.success('#user-alert',json.info);
			}else{
				$('#user-submit').html('注册');
				feifei.alert.warning('#user-alert',json.info);
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
<body>
<div class="container ff-user-register">
<div class="row">
<div class="col-md-8 col-md-offset-2">
  <h2 class="text-center">
  	<a href="{:ff_url('user/register')}">欢迎加入{$site_name}</a>
  </h2>
  <h5 class="text-center">
    <a class="ff-text" href="{$root}">返回首页</a>
    <a class="ff-text" href="{:ff_url('user/forget')}">忘记密码</a>
    <a class="ff-text" href="{:ff_url('user/login')}">已有帐号登录</a>
  </h5>
  <div class="row">
  <div class="col-xs-12">
  	<h4 class="text-muted">
      创建新账号
    </h4>
    <form class="form-horizontal" id="ff-register" action="{:ff_url('user/post')}" method="post" role="form" target="_blank">
      <div class="form-group">
        <label for="user_name" class="col-md-3 control-label">昵称</label>
        <div class="col-md-8">
          <input class="form-control" name="user_name" id="user_name" type="text" placeholder="字母、数字等，用户名唯一" title="" data-toggle="tooltip" data-placement="bottom" required>
        </div>
      </div>
      <div class="form-group">
        <label for="user_email" class="col-md-3 control-label">邮箱</label>
        <div class="col-md-8">
          <input class="form-control" name="user_email" id="user_email" type="text" placeholder="常用邮箱 test@feifeicms.com" required>
        </div>
      </div>
      <div class="form-group">
        <label for="user_pwd" class="col-md-3 control-label">密码</label>
        <div class="col-md-8">
          <input class="form-control" name="user_pwd" id="user_pwd" type="password" placeholder="不少于 6 位" required>
        </div>
      </div>
      <div class="form-group">
        <label for="user_pwd_re" class="col-md-3 control-label">确认密码</label>
        <div class="col-md-8">
          <input class="form-control" name="user_pwd_re" id="user_pwd_re" type="password" placeholder="重复输入密码" required>
        </div>
      </div>
      <div class="form-group">
        <div class="col-xs-8 checkbox text-right">
           <label id="ajax-info">同意并接受<a href="javascript:;">《服务条款》</a></label>
         </div>
         <div class="col-xs-3 text-right">
           <button type="submit" class="btn ff-btn-play" id="user-submit">注册</button>
         </div>
      </div>
  	</form>
    <h6 id="user-alert">
    </h6>
  </div>
  </div>
</div>    
</div>
</div>
<include file="Block:user_footer" />
</body>
</html>