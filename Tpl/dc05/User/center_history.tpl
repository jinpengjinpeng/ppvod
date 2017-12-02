<php>$item_news = ff_mysql_record('sid:2;uid:'.$user_id.';type:2;group:record_did;limit:20;page_is:true;page_id:record;page_p:'.$user_page.';cache_name:default;cache_time:default;order:record_id;sort:desc;field:list_id,list_name,list_dir,news_id,news_cid,news_name,news_ename,news_jumpurl,news_addtime,news_pic,news_keywords,news_up,news_down,news_hits,news_remark,news_content');
$page = ff_url_page('user/center',array('action'=>'history','p'=>'FFLINK'),true,'record',4);
$totalpages = ff_page_count('record', 'totalpages');
</php><!DOCTYPE html>
<html lang="zh-cn">
<head>
<include file="User:header" />
<title>观看记录_{$site_name}</title>
<meta name="keywords" content="{$site_name}用户中心">
<meta name="description" content="欢迎回到{$site_name}用户中心">
</head>
<body class="user-center">
<include file="User:center_nav" />
<div class="container ff-bg">
<div class="row">
  <div class="col-xs-12 ff-col">
    <div class="page-header">
      <h4><span class="glyphicon glyphicon-menu-right ff-text"></span> 我喜欢的</h4>
    </div>
    <volist name="item_news" id="feifei">
    	<include file="Block:news_item" />
    </volist>
  </div>
  <!-- -->
  <gt name="totalpages" value="1">
    <div class="clearfix"></div>
    <div class="col-xs-12 ff-col text-center">
      <ul class="pagination pagination-lg hidden-xs">
        {$page}
      </ul>
      <ul class="pager visible-xs">
      	<gt name="user_page" value="1">
          <li><a id="ff-prev" href="{:ff_url('user/center', array('action'=>'history','p'=>($user_page-1)), true)}">上一页</a></li>
        </gt>
        <lt name="user_page" value="$totalpages">
          <li><a id="ff-next" href="{:ff_url('user/center', array('action'=>'history','p'=>($user_page+1)), true)}">下一页</a></li>
        </lt>
       </ul> 
    </div>
  </gt>
</div><!--row end -->
</div>
<include file="User:footer" />
</body>
</html>