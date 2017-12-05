<?php
/*-------------------------------------------------文件夹与文件操作开始------------------------------------------------------------------*/
//读取文件
function read_file($l1){
	return @file_get_contents($l1);
}
//写入文件
function write_file($l1, $l2=''){
	$dir = dirname($l1);
	if(!is_dir($dir)){
		mkdirss($dir);
	}
	return @file_put_contents($l1, $l2);
}
//递归创建文件
function mkdirss($dirs,$mode=0777) {
	if(!is_dir($dirs)){
		mkdirss(dirname($dirs), $mode);
		return @mkdir($dirs, $mode);
	}
	return true;
}
// 数组保存到文件
function arr2file($filename, $arr=''){
	if(is_array($arr)){
		$con = var_export($arr,true);
	} else{
		$con = $arr;
	}
	$con = "<?php\nreturn $con;\n?>";//\n!defined('IN_MP') && die();\nreturn $con;\n
	write_file($filename, $con);
}
/*-------------------------------------------------系统路径相关函数开始------------------------------------------------------------------*/
//获取当前地址栏URL
function get_http_url(){
	return htmlspecialchars("http://".$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"]);
}
//获取根目录路径
function get_site_path($filename){
    $basepath = $_SERVER['PHP_SELF'];
    $basepath = substr($basepath,0,strpos($basepath,$filename));
	return $basepath;
}
//相对路径转绝对路径
function get_base_url($baseurl,$url){
	if("#" == $url){
		return "";
	}elseif(FALSE !== stristr($url,"http://")){
		return $url;
	}elseif( "/" == substr($url,0,1) ){
		$tmp = parse_url($baseurl);
		return $tmp["scheme"]."://".$tmp["host"].$url;
	}else{
		$tmp = pathinfo($baseurl);
		return $tmp["dirname"]."/".$url;
	}
}
//获取指定地址的域名
function get_domain($url){
	preg_match("|http://(.*)\/|isU", $url, $arr_domain);
	return $arr_domain[1];
}
/*-------------------------------------------------字符串处理开始------------------------------------------------------------------*/
// UT*转GBK
function u2g($str){
	return iconv("UTF-8","GBK",$str);
}
// GBK转UTF8
function g2u($str){
	return iconv("GBK","UTF-8//ignore",$str);
}
// 去掉换行
function nr($str){
	$str = str_replace(array("<nr/>","<rr/>"),array("\n","\r"),$str);
	return trim($str);
}
//去掉连续空白
function nb($str){
	$str = str_replace("　",' ',str_replace("&nbsp;",' ',$str));
	$str = ereg_replace("[\r\n\t ]{1,}",' ',$str);
	return trim($str);
}
// 转换成JS
function t2js($l1, $l2=1){
  $I1 = str_replace(array("\r", "\n"), array('', '\n'), addslashes($l1));
  return $l2 ? "document.write(\"$I1\");" : $I1;
}
//字符串截取(同时去掉HTML与空白)
function msubstr($str, $start=0, $length, $suffix=false){
	return ff_msubstr(eregi_replace('<[^>]+>','',ereg_replace("[\r\n\t ]{1,}",' ',nb($str))),$start,$length,'utf-8',$suffix);
}
function ff_msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true){
	$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
	$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
	$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
	preg_match_all($re[$charset], $str, $match);
	$length_new = $length;
	for($i=$start; $i<$length; $i++){
		if (ord($match[0][$i]) > 0xa0){
			//中文
		}else{
			$length_new++;
			$length_chi++;
		}
	}
	if($length_chi<$length){
		$length_new = $length+($length_chi/2);
	}
	$slice = join("",array_slice($match[0], $start, $length_new));
    if($suffix && $slice != $str){
		return $slice."…";
	}
    return $slice;
}
// 汉字转拼单
function ff_pinyin($str,$ishead=0,$isclose=1){
	$str = u2g($str);//转成GBK
	global $pinyins;
	$restr = '';
	$str = trim($str);
	$slen = strlen($str);
	if($slen<2){
		return $str;
	}
	if(count($pinyins)==0){
		$fp = fopen('./Public/data/pinyin.dat','r');
		while(!feof($fp)){
			$line = trim(fgets($fp));
			$pinyins[$line[0].$line[1]] = substr($line,3,strlen($line)-3);
		}
		fclose($fp);
	}
	for($i=0;$i<$slen;$i++){
		if(ord($str[$i])>0x80){
			$c = $str[$i].$str[$i+1];
			$i++;
			if(isset($pinyins[$c])){
				if($ishead==0){
					$restr .= $pinyins[$c];
				}
				else{
					$restr .= $pinyins[$c][0];
				}
			}else{
				//$restr .= "_";
			}
		}else if( eregi("[a-z0-9]",$str[$i]) ){
			$restr .= $str[$i];
		}
		else{
			//$restr .= "_";
		}
	}
	if($isclose==0){
		unset($pinyins);
	}
	return $restr;
}
//生成字母前缀
function ff_url_letter($s0){
	$firstchar_ord=ord(strtoupper($s0{0})); 
	if (($firstchar_ord>=65 and $firstchar_ord<=91)or($firstchar_ord>=48 and $firstchar_ord<=57)) return $s0{0}; 
	$s=iconv("UTF-8","gb2312", $s0); 
	$asc=ord($s{0})*256+ord($s{1})-65536; 
	if($asc>=-20319 and $asc<=-20284)return "A";
	if($asc>=-20283 and $asc<=-19776)return "B";
	if($asc>=-19775 and $asc<=-19219)return "C";
	if($asc>=-19218 and $asc<=-18711)return "D";
	if($asc>=-18710 and $asc<=-18527)return "E";
	if($asc>=-18526 and $asc<=-18240)return "F";
	if($asc>=-18239 and $asc<=-17923)return "G";
	if($asc>=-17922 and $asc<=-17418)return "H";
	if($asc>=-17417 and $asc<=-16475)return "J";
	if($asc>=-16474 and $asc<=-16213)return "K";
	if($asc>=-16212 and $asc<=-15641)return "L";
	if($asc>=-15640 and $asc<=-15166)return "M";
	if($asc>=-15165 and $asc<=-14923)return "N";
	if($asc>=-14922 and $asc<=-14915)return "O";
	if($asc>=-14914 and $asc<=-14631)return "P";
	if($asc>=-14630 and $asc<=-14150)return "Q";
	if($asc>=-14149 and $asc<=-14091)return "R";
	if($asc>=-14090 and $asc<=-13319)return "S";
	if($asc>=-13318 and $asc<=-12839)return "T";
	if($asc>=-12838 and $asc<=-12557)return "W";
	if($asc>=-12556 and $asc<=-11848)return "X";
	if($asc>=-11847 and $asc<=-11056)return "Y";
	if($asc>=-11055 and $asc<=-10247)return "Z";
	return 0;//null
}
/*--------------------------------------------------系统安全加密函数开始-----------------------------------------------------------------*/
//加密
function ff_encrypt($data, $key){
	$key  = md5(empty($key) ? 'feifeicms' : $key);
	$x  = 0;
	$len = strlen($data);
	$l  = strlen($key);
	for ($i = 0; $i < $len; $i++)
	{
			if ($x == $l) 
			{
			 $x = 0;
			}
			$char .= $key{$x};
			$x++;
	}
	for ($i = 0; $i < $len; $i++)
	{
			$str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
	}
	return base64_encode($str);
}
//解密
function ff_decrypt($data, $key){
 	$key  = md5(empty($key) ? 'feifeicms' : $key);
	$x = 0;
	$data = base64_decode($data);
	$len = strlen($data);
	$l = strlen($key);
	for ($i = 0; $i < $len; $i++)
	{
			if ($x == $l) 
			{
			 $x = 0;
			}
			$char .= substr($key, $x, 1);
			$x++;
	}
	for ($i = 0; $i < $len; $i++)
	{
			if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1)))
			{
					$str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
			}
			else
			{
					$str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
			}
	}
	return $str;
}
/*--------------------------------------------------系统常用快捷函数开始-----------------------------------------------------------------*/
// 返回数组的值
function ff_array($array, $key=0){
	return $array[$key];
}
// 递归多维数组转为一级数组
function ff_arrays2array($array){
	static $result_array=array();
	foreach($array as $value){
		if(is_array($value)){
			ff_arrays2array($value);
		}else{
			$result_array[]=$value;
		}
	}
	return $result_array;
}
// 逗号分隔的字符串去重 如模板栏目ID参数 $cids = array(1,2,3,...)
function ff_unique($cids){
	$cidarr = explode(',',$cid);
	$cidarr = array_unique($cidarr);
	return $cidarr;
}
// 默认值处理
function ff_default($value, $default){
	if($value){
		return $value;
	}
	return $default;
}
//获取模型名称
function ff_sid2module($sid){
	$module = array();
	$module[1] = 'vod';
	$module[2] = 'news';
	$module[3] = 'special';
	$module[4] = 'tag';
	$module[5] = 'guestbook';
	$module[6] = 'forum';
	$module[7] = 'scenario';
	$module[8] = 'star';
	$module[9] = 'role';
	$module[10] = 'pic';
	$module[11] = 'link';
	$module[12] = 'ads';
	return $module[$sid];
}
//获取模型ID
function ff_module2sid($sidname){
	$module = array();
	$module['vod'] = 1;
	$module['news'] = 2;
	$module['special'] = 3;
	$module['tag'] = 4;
	$module['guestbook'] = 5;
	$module['forum'] = 6;
	$module['scenario'] = 7;
	$module['star'] = 8;
	$module['role'] = 9;
	$module['pic'] = 10;
	$module['link'] = 11;
	$module['ads'] = 12;
	return intval($module[$sidname]);
}
/*-------------------------------------------------系统采集函数开始------------------------------------------------------------------*/
// 采集内核
function ff_file_get_contents($url, $timeout=10, $referer=''){
	if(function_exists('curl_init')){
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt ($ch, CURLOPT_REFERER, $referer);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		$content = curl_exec($ch);
		curl_close($ch);
		if($content){
			return $content;
		}
	}
	$ctx = stream_context_create(array('http'=>array('timeout'=>$timeout)));
	$content = @file_get_contents($url, 0, $ctx);
	if($content){
		return $content;
	}
	return false;
}
// 采集-匹配规则结果
function ff_preg_match($rule,$html){
	$arr = explode('$$$',$rule);
	if(count($arr) == 2){
	    preg_match('/'.$arr[1].'/', $html, $data);
		return $data[$arr[0]].'';
	}else{
	    preg_match('/'.$rule.'/', $html, $data);
		return $data[1].'';
	}
}
// 采集-匹配规则结果all
function ff_preg_match_all($rule,$html){
	$arr = explode('$$$',$rule);
	if(count($arr) == 2){
	    preg_match_all('/'.$arr[1].'/', $html, $data);
		return $data[$arr[0]];
	}else{
	    preg_match_all('/'.$rule.'/', $html, $data);
		return $data[1];
	}
}
// 采集-倒序采集
function ff_krsort_url($listurl){
   krsort($listurl);
   foreach($listurl as $val){
     $list[]=$val;
   }
   return $list;
}
// 采集-将所有替换规则保存在一个字段
function ff_implode_rule($arr){
    foreach($arr as $val){
	    $array[] = trim(stripslashes($val));
	}
	return implode('|||',$array);
}
//  采集-规则替换
function ff_replace_rule($str){
	//$str = str_replace(array("\n","\r"),array("<nr/>","<rr/>"),strtolower($str));
	$arr1 = array('?','"','(',')','[',']','.','/',':','*','||');
	$arr2 = array('\?','\"','\(','\)','\[','\]','\.','\/','\:','.*?','(.*?)');
	//$str = str_replace(array("\n","\r"),array("<nr/>","<rr/>"),strtolower($str));
	return str_replace('\[$feifeicms\]','([\s\S]*?)',str_replace($arr1,$arr2,$str));
}
//生成随机伪静态简介
function ff_rand_str($string){
  $arr = C('collect_original_data');//同义词数据库
  //$all=mb_strlen($string,'utf-8');
	$all=iconv_strlen($string,'utf-8');
  $len=floor(mt_rand(0,$all-1));
  $str = msubstr($string,0,$len);
	$str2 = msubstr($string,$len,$all);
	return $str.$arr[array_rand($arr,1)].$str2;
}
//获取绑定分类对应ID值
function ff_bind_id($key){
	$bindcache = F('_cj/bind');
	return $bindcache[$key];
}
//TAG分词自动获取
function ff_tag_auto($title,$content){
	$data = ff_file_get_contents('http://cdn.feifeicms.co/server/v3/kw.php?source='.rawurlencode(msubstr($title.$content,0,200)));
	if($data) {
		return implode(',',json_decode($data,true));
	}
	return false;
}
// 格式化采集影片名称
function ff_xml_vodname($vodname){
	$vodname = str_replace(array('【','】','（','）','(',')','{','}'),array('[',']','[',']','[',']','[',']'),$vodname);
	$vodname = preg_replace('/\[([a-z][A-Z])\]|([a-z][A-Z])版/i','',$vodname);
	$vodname = preg_replace('/TS清晰版|枪版|抢先版|HD|BD|TV|DVD|VCD|TS|\/版|\[\]/i','',$vodname);
	return trim($vodname);
}
// 格式化采集影片主演
function ff_xml_vodactor($vodactor){
	return str_replace(array('/','，','|','、',' ',',,,'),',',$vodactor);	
}
/*-----------------------------------------------------------模板标签解析函数开始-----------------------------------------------------------------*/
//获得某天前的时间戳
function ff_linux_time($day){
	$day = intval($day);
	return mktime(23,59,59,date("m"),date("d")-$day,date("y"));
}
// 获取标题颜色
function ff_color($str,$color){
	if(empty($color)){
		return $str;
	}else{
		return '<font color="'.$color.'">'.$str.'</font>';
	}
}
// 获取时间颜色
function ff_color_date($type='Y-m-d H:i:s',$time,$color='red'){
	if((time()-$time)>86400){
		return date($type,$time);
	}else{
		return '<font color="'.$color.'">'.date($type,$time).'</font>';
	}
}
// 处理积分样式
function ff_gold($fen){
	$array = explode('.',$fen);
	return '<strong>'.$array[0].'</strong>.'.$array[1];
}
// 获取循环标签分页统计 records|currentpage|totalpages
function ff_page_count($pageid='pageid', $key='records'){
	// 通过GET全局变量获当前定义的
	$page = $_GET['ff_page_'.$pageid];
	if(!$page){
		return false;
	}
	return $page[$key];
}
// 处理最大分页参数
function ff_page_max($currentpage, $totalpages){
	if ($currentpage > $totalpages){
		$currentpage = $totalpages;
	}
	return $currentpage;
}
// 获取热门关键词
function ff_site_hot($type='home'){
	if(!C('site_hot')){
		return false;
	}
	$array_hot = array();
	foreach(explode(chr(13),trim(C('site_hot'))) as $key=>$value){
		$array = explode('|',$value);
		if($array[2]){
			$target = ' target="'.$array[2].'"';
		}else{
			$target = '';
		}
		if($array[1]){
			$array_hot[$key] = '<a href="'.$array[1].'"'.$target.'>'.trim($array[0]).'</a>';
		}else{
			$array_hot[$key] = '<a href="'.ff_url('vod/search',array('wd'=>urlencode(trim($array[0]))),true).'"'.$target.'>'.trim($array[0]).'</a>';
		}
	}
	if(C('url_html') && $type=='home'){
		return '<span class="ff-site-hot" id="ff-site-hot">'.implode('',$array_hot).'</span>';
	}
	return implode('',$array_hot);
}
// 获取与处理人气值
function ff_get_hits($sidname, $type='hits', $array, $js=true){
	if((C('url_html') && $js) || $type=='insert'){
		return '<span class="ff-hits" data-sid="'.$sidname.'" data-id="'.$array[$sidname.'_id'].'" data-type="'.$type.'"></span>';
	}else{
		return $array[$type];
	}
}
//迅雷专用链
function ff_ThunderEncode($url) {
	$thunderPrefix = "AA";
	$thunderPosix = "ZZ";
	$thunderTitle = "thunder://";
	if(strstr($url,"thunder://")){
		return $url;
	}elseif(strstr($url,"magnet")){
		return $url;
	}else{
		$thunderUrl = $thunderTitle.base64_encode($thunderPrefix.$url.$thunderPosix);
	}
	return $thunderUrl;
}
// 返回下一篇或上一篇的内容的信息
function ff_detail_array($module='vod', $type='next', $id, $cid, $field='vod_id,vod_cid,vod_status,vod_name,vod_ename,vod_jumpurl'){
	$where = array();
	$where[$module.'_cid'] = $cid;
	$where[$module.'_status'] = 1;
	if($type == 'next'){
		$where[$module.'_id'] = array('gt', $id);
		$order = $module.'_id asc';
	}else{
		$where[$module.'_id'] = array('lt', $id);
		$order = $module.'_id desc';
	}
	if($module != 'vod'){
		$field = str_replace('vod_', $module.'_', $field);
	}
	$array = D(ucfirst($module))->ff_find($field, $where, 'cache_page_'.$module.$type.'_'.$id, false, $order);
	return $array;
}
//播放列表格式化
function ff_play_list($server, $play, $url){
	//加载配置文件
	$conf_play = F('_feifeicms/player');
	$conf_server = C('play_server');
	//分解播放器组
	$array_server = explode('$$$',$server);
	$array_play = explode('$$$',$play);
	$array_url = explode('$$$',$url);
	//定义播放器每一组对应的地址合集(如有多个youku)
	$array = array();
	foreach($array_play as $sid=>$val){
		$array[$val][$sid] = $array_url[$sid];
	}
	//按播放器配置排序组合成前台循环的二维数组
	$play_list = array();
	foreach($conf_play as $conf_key=>$conf_value){
		if($play_one = $array[$conf_key]){
			foreach($play_one as $sid=>$url_one){
				$son = ff_play_list_one($url_one, $conf_server[$array_server[$sid]]);
				$play_list[$sid+1] = array(
					'server_name' => $array_server[$sid],
					'server_url' => $conf_server[$array_server[$sid]],
					'player_sid' => $sid+1,
					'player_name_en' => $conf_key,
					'player_name_zh' => $conf_value[0],
					'player_copyright' => $conf_value[2],
					'player_info' => $conf_value[1],
					'player_jiexi' => $conf_value[3],
					'player_count' => count($son),
					'son' => $son,
				);
			}
		}
	}
	return $play_list;
}
//分解单组播放地址链接
function ff_play_list_one($url_one, $server_url){
	$url_list = array();
	$array_url = explode(chr(13),str_replace(array("\r\n", "\n", "\r"),chr(13),$url_one));
	foreach($array_url as $key=>$val){
		list($title, $url, $logo, $title_rc, $player) = explode('$', $val);
		if ( empty($url) ) {
			$url_list[$key]['title'] = '第'.($key+1).'集';
			$url_list[$key]['url'] = $server_url.$title;
		}else{
			$url_list[$key]['title'] = $title;
			$url_list[$key]['url'] = $server_url.$url;
		}
		$url_list[$key]['url'] = str_replace('{feifeicms}','',$url_list[$key]['url']);
		$url_list[$key]['logo'] = str_replace('{feifeicms}','',$logo);
		$url_list[$key]['title_rc'] = str_replace('{feifeicms}','',$title_rc);
		$url_list[$key]['player'] = str_replace('{feifeicms}','',$player);
	}
	return $url_list;
}
//获取某一组的播放地址
function ff_play_one($vod_play_list, $play_name='max'){
	if($play_name == 'max'){
		$max = $vod_play_list[0]['player_count'];
		$max_key = 0;
		foreach($vod_play_list as $key=>$value){
			if( $value['player_count'] > $max && (!in_array($value['player_name_en'], array('yugao','down'))) ){
				$max = $value['player_count'];
				$max_key = $key;
			}
		}
		return $vod_play_list[$max_key];
	}else{
		$play = list_search($vod_play_list, array('player_name_en'=>$play_name));
		return $play[0];
	}
}
//播放器载入
function ff_player($array){
	//云播放器
	if(C('play_cloud')){
		return '<script type="text/javascript" src="'.C('play_cloud').'?u='.base64_encode($array['play_url']).'&p='.$array['play_name_en'].'&c='.$array['play_copygiht'].'&j='.base64_encode($array['play_jiexi']).'&x='.$array['play_second'].'&y='.base64_encode($array['play_buffer']).'&z='.base64_encode($array['play_url_next']).'"></script>'."\n";
	}
	//本地播放器
	$json = array();
	$json['url'] = $array['play_url'];
	$json['copyright'] = $array['play_copygiht'];
	$json['name'] = $array['play_name_en']; if($array['play_copygiht']){ $json['name'] = 'copyright'; }	
	$json['jiexi'] = $array['play_jiexi'];
	$json['time'] = $array['play_second'];
	$json['buffer'] = $array['play_buffer'];
	$json['next_url'] = $array['play_url_next'];
	return '<script>var cms_player = '.json_encode($json).';</script><script type="text/javascript" src="'.C('site_path').'Public/player/'.$json['name'].'.js" ></script>';
}
/*-------------------------------------------------访问路径函数开始------------------------------------------------------------------*/
// 动态模式 自定义路由反向生成对应的链接URL
function ff_url_replace_route($model, $params, $url){
	$array_key = explode('/',$model);
	foreach($params as $key=>$value){
		array_push($array_key, $key);
	}
	//根据U参数生成KEY值
	$key = implode('/',$array_key);
	//加载反向对应规则并检测是否存在
	$url_rules = C('url_rewrite_rules');
	//将对应的URL网址按对应规则替换
	if($url_rules[$key]){
		$url = preg_replace("/".$url_rules[$key]['find']."/i", $url_rules[$key]['replace'], $url);
	}
	return $url;
}
//静态模式 格式化页码为1的首个链接
function ff_url_replace_html($model, $link){
	$replace = false;
	if( $model=='news/show' ){
		if(C('url_news_list')){
			$replace = true;
		}
	}else if( $model=='vod/show' ){
		if(C('url_vod_list')){
			$replace = true;
		}
	}else if( $model=='news/read' ){
		if(C('url_news_detail')){
			$replace = true;
		}
	}else if( $model=='vod/read' ){
		if(C('url_vod_detail')){
			$replace = true;
		}
	}
	if($replace){
		$old = array('/index'.C('html_file_suffix'), '-1'.C('html_file_suffix'), '_1'.C('html_file_suffix'));
		$new = array('/', C('html_file_suffix'), C('html_file_suffix'));
		$link = str_replace($old, $new, $link);
	}
	return $link;
}
// 根据后台设置的静态网页规则生成保存路径
function ff_url_build($module, $params){
	//$params['list_id'],$params['list_dir'],$params['id'],$params['pinyin'],$params['p'],$params['jumpurl']
	$old = array('{listid}', '{listdir}', '{pinyin}', '{id}', '{md5}', '{page}', '{sid}', '{pid}');
	$new = array($params['list_id'], $params['list_dir'], $params['pinyin'], $params['id'], md5($params['id']), $params['p'], $params['sid'], $params['pid']);
	if('vod/read' == $module){
		$html_path = C('url_vod_detail');
	}else if('vod/play' == $module){
		$html_path = C('url_vod_play');
	}else if('vod/show' == $module){
		$html_path = C('url_vod_list');
	}else if('news/read' == $module){
		$html_path = C('url_news_detail');
	}else if('news/show' == $module){
		$html_path = C('url_news_list');
	}
	$html_path = str_replace($old, $new, $html_path);
	//第一页去除页码规则
	if($params['p'] == 1){
		$html_path .= 'BUILD';
		$old = array( '/1BUILD', '-1BUILD', '_1BUILD');
		$new = array('/indexBUILD', 'BUILD', 'BUILD');
		$html_path = str_replace('BUILD', '', str_replace($old, $new, $html_path));
	}
	//首页index处理
	$suffix = strrchr($html_path, '/');
	if($suffix == '/'){
		$html_path .= 'index';
	}
	return C('site_path').$html_path;
}
// 共用分类页链接函数
function ff_url_show($model='vod/show', $params, $suffix=true){
	//$params['id'],$params['p'],$params['list_dir']
	if ($params['jumpurl']) {
		return $params['jumpurl'];
	}
	if(C('url_html')){
		$model = str_replace(array('news_list_page','vod_list_page'),'show', $model);
		if( $model=='news/show' ){
			if(C('url_news_list')){
				$url = ff_url_build('news/show', $params).C('html_file_suffix');
				return ff_url_replace_html($model, $url);
			}
		}else if( $model=='vod/show' ){
			if(C('url_vod_list')){
				$url = ff_url_build('vod/show', $params).C('html_file_suffix');
				return ff_url_replace_html($model, $url);
			}
		}
	}
	// 动态模式
	if( $model=='vod/show'){
		if(C('url_rewrite_rules.vod/category/id')){
			$params['id'] = $params['list_dir'];
			$model = 'vod/category';
		}
	}elseif( $model=='news/show'){
		if(C('url_rewrite_rules.news/category/id')){
			$params['id'] = $params['list_dir'];
			$model = 'news/category';
		}
	}
	unset($params['list_dir']);
	// 伪静态
	if(C('URL_ROUTER_ON')){
		if($params['p'] == 'FFLINK'){
			$params['p'] = 0.20161212;
			return str_replace('0.20161212', 'FFLINK', ff_url($model, $params, $suffix));
		}
	}
	return ff_url($model, $params, $suffix);
}
// 共用内容页链接函数
function ff_url_detail($model='vod/read', $params, $suffix=true){
	//$params['id'],$params['p'],$params['pinyin'],$params['list_id'],$params['list_dir'],$params['jumpurl']
	if ($params['jumpurl']) {
		return $params['jumpurl'];
	}
	if(C('url_html')){
		if( $model=='news/read' ){
			if(C('url_html') && C('url_news_detail')){
				$url = ff_url_build('news/read', $params).C('html_file_suffix');
				return ff_url_replace_html($model, $url);
			}
		}else if( $model=='vod/read' ){
			if(C('url_html') && C('url_vod_detail')){
				$url = ff_url_build('vod/read', $params).C('html_file_suffix');
				return ff_url_replace_html($model, $url);
			}
		}else if($model=='vod/play'){
			if(C('url_html') && C('url_vod_play')){
				$url = ff_url_build('vod/play', $params).C('html_file_suffix');
				return ff_url_replace_html($model, $url);
			}
		}
	}
	// 动态模式
	if( $model=='vod/read' ){
		if(C('url_rewrite_rules.vod/ename/id')){
			$params['id'] = $params['pinyin'];
			$model = 'vod/ename';
		}
	}elseif( $model=='vod/play' ){
		if(C('url_rewrite_rules.vod/eplay/id/sid/pid')){
			$params['id'] = $params['pinyin'];
			$model = 'vod/eplay';
		}
	}elseif( $model=='news/read' ){
		if(C('url_rewrite_rules.news/ename/id')){
			$params['id'] = $params['pinyin'];
			$model = 'news/ename';
		}
	}
	unset($params['pinyin']);
	unset($params['jumpurl']);
	unset($params['list_id']);
	unset($params['list_dir']);
	return ff_url($model, $params, $suffix);
}
//动态链接函数 ff_url('news/read',array('id'=>3,'p'=>2),true);
function ff_url($model, $params, $suffix=true){
	//是否存在跳转链接
	if ($params['jumpurl']) {
		return $params['jumpurl'];
	}
	//第1页格式化掉
	if($params['p'] == 1){
		unset($params['p']);
	}
	/*过滤掉无效参数
	foreach($params as $key=>$value){
		if(!$value){
			unset($params[$key]);
		}
	}*/
	//TP系统动态风址
	$url = U($model, $params, false, $suffix);
	$url = str_replace(array('Admin-','Home-','Plus-'),'',$url);
	// 自定义路由反向生成对应的URL
	if( C('URL_ROUTER_ON') ){
		$url = ff_url_replace_route($model, $params, $url);
	}
	// 伪静态
	if(C('url_rewrite')){
		$url = str_replace('index.php?s=/','',$url);
	}
	return $url;
}
//分页带链接展示函数
function ff_url_page($model, $params, $suffix=false, $pageid='pageid', $halfPer=5, $page = false){
	if(!$page){
		$page = $_GET['ff_page_'.$pageid];
	}
	if(!$page){
		return false;
	}
	$jumpurl = ff_url_show($model, $params, $suffix);
	//
	if($page['currentpage'] < $halfPer){
		$halfPer = $halfPer+($halfPer-$page['currentpage']);
	}else{
		if($page['currentpage']+$halfPer>$page['totalpages']){
			$halfPer = $halfPer+($halfPer-($page['totalpages']-$page['currentpage']));
		}
	}
	$link='';
	if($page['currentpage'] > $halfPer){
		$link = '<li><a href="'.str_replace('FFLINK', 1, $jumpurl).'">1..</a></li>';
	}
	if( $page['currentpage'] > 1){
		$link .= '<li><a href="'.str_replace('FFLINK', ($page['currentpage']-1), $jumpurl).'">&laquo;</a></li>';
	}
	for($i=$page['currentpage']-$halfPer,$i>1||$i=1,$j=$page['currentpage']+$halfPer,$j<$page['totalpages']||$j=$page['totalpages'];$i<$j+1;$i++){
		//格式化第一页
		if($i == 1){
			$params['p'] = 1;
			$firrt_page = ff_url_show($model, $params, $suffix);
			if($page['currentpage']==1){
				$link .= '<li class="disabled"><a href="'.$firrt_page.'">1</a></li>';
			}else{
				$link .= '<li><a href="'.$firrt_page.'">1</a></li>';
			}
		}else{
			if($i == $page['currentpage']){
				$link .= '<li class="disabled"><a href="'.str_replace('FFLINK', $i, $jumpurl).'">'.$i.'</a></li>';
			}else{
				$link .= '<li><a href="'.str_replace('FFLINK', $i, $jumpurl).'">'.$i.'</a></li>';
			}
		}
	}
	if($page['currentpage']+$halfPer < $page['totalpages']){
		$link .= '<li><a href="'.str_replace('FFLINK', $page['totalpages'], $jumpurl).'">...'.$page['totalpages'].'</a></li>';
	}
	if($page['currentpage'] < $page['totalpages']){
		$link .= '<li><a href="'.str_replace('FFLINK', ($page['currentpage']+1), $jumpurl).'">&raquo;</a></li>';
	}
	//动态删除多余标签与自定义路由反向对应规则
	unset($params['list_dir']);
	return $link;
}
// 视频分类页链接函数
function ff_url_vod_show($list_id,$list_dir,$list_page,$suffix=true){
	return ff_url_show('vod/show', array('id'=>$list_id,'list_dir'=>$list_dir,'p'=>$list_page), $suffix);
}
// 文章分类页链接函数
function ff_url_news_show($list_id,$list_dir,$list_page,$suffix=true){
	return ff_url_show('news/show', array('id'=>$list_id,'list_dir'=>$list_dir,'p'=>$list_page), $suffix);
}
// 视频详情页链接函数
function ff_url_vod_read($list_id,$list_dir,$vod_id,$vod_ename,$vod_jumpurl,$suffix=true){
	return ff_url_detail('vod/read',array('list_id'=>$list_id,'list_dir'=>$list_dir,'id'=>$vod_id,'pinyin'=>$vod_ename,'jumpurl'=>$vod_jumpurl), $suffix);
}
// 视频播放页链接函数
function ff_url_vod_play($list_id,$list_dir,$vod_id,$vod_ename,$play_sid,$play_pid,$suffix=true){
	return ff_url_detail('vod/play',array('list_id'=>$list_id,'list_dir'=>$list_dir,'id'=>$vod_id,'pinyin'=>$vod_ename,'sid'=>$play_sid,'pid'=>$play_pid),$suffix);
}
// 文章详情页链接函数
function ff_url_news_read($list_id,$list_dir,$news_id,$news_ename,$news_jumpurl,$news_page=1,$suffix=true){
	return ff_url_detail('news/read',array('list_id'=>$list_id,'list_dir'=>$list_dir,'id'=>$news_id,'pinyin'=>$news_ename,'jumpurl'=>$news_jumpurl,'p'=>$news_page), $suffix);
}
// 获取影片最后一集 return array(sid,pid,jiname,jipath,pic,info)
function ff_url_play_end($vod_url){
	$arr_url = array();
	$arr_urls = explode('$$$',trim($vod_url));
	$array = array();
	foreach($arr_urls as $key=>$value){
		$arr_url[$key] = explode(chr(13), str_replace(array("\r\n", "\n", "\r"), chr(13), $value) );
		$array[$key] = array(count($arr_url[$key]), $key);
	}
	$max_key = max($array);//最多的播放地址的总集数与KEY
	$play_url_max = explode('$',end($arr_url[$max_key[1]]));//最多播放地址的最后一集
	if($play_url_max[1]){
		return array($max_key[1]+1, $max_key[0], $play_url_max[0], $play_url_max[1], $play_url_max[2], $play_url_max[3]);
	}else{
		return array($max_key[1]+1, $max_key[0], '第'.$max_key[0].'集', $play_url_max[0], $play_url_max[2], $play_url_max[3]);
	}
}
// 获取广告调用地址
function ff_url_ads($str,$charset="utf-8"){
	return '<script type="text/javascript" src="'.C('site_path').C('admin_ads_file').'/'.$str.'.js" charset="'.$charset.'"></script>';
}
// 获取搜索带链接
function ff_url_search($str, $type="actor", $sidname='vod', $action='search'){
	if(!$str){
		return '佚名';
	}
	$array = array();
  $str = str_replace(array('/','|',',','，'),',',$str);
	$arr = explode(',',$str);
	foreach($arr as $key=>$val){
		$array[$key] = '<a href="'.ff_url($sidname.'/'.$action, array($type=>urlencode($val)), true).'">'.$val.'</a>';
	}
	return implode('',$array);
}
// Tag链接
function ff_url_tags($str, $tag_list='vod_tag'){
	list($module,$type) = explode('_',$tag_list);
	return ff_url($module.'/tags', array('name' => urlencode($str) ), true);
}
// 内容页Tag链接
function ff_url_tags_content($content, $array_tag){
	if($array_tag){
		foreach($array_tag as $key=>$value){
			if($value['tag_name']){
				$keyword = stripslashes($value['tag_name']);
				$url = '<a href="'.ff_url_tags($value['tag_name'], $value['tag_list']).'" class="ff-tag-link" >'.$keyword.'</a>';
				$regEx = '\'(?!((<.*?)|(<a.*?)))('. $keyword . ')(?!(([^<>]*?)>)|([^>]*?</a>))\'s';
				$content = preg_replace($regEx, $url, $content);
			}
		}
	}
	return $content;
}
// 获取某图片的访问地址
function ff_url_img($file, $content, $number=1){
	if(!$file){
		return ff_url_img_preg($content, $number);
	}
	$array = parse_url($file);
	if(in_array($array['scheme'],array('http','https','ftp'))){
		return $file;
	}
	$prefix = C('upload_http_prefix');
	if(!empty($prefix)){
		return $prefix.$file;
	}else{
		return C('site_path').C('upload_path').'/'.$file;
	}
}
// 获取某图片的缩略图地址
function ff_url_img_small($file, $content, $number=1){
	if(!$file){
		return ff_url_img_preg($content, $number);
	}
	$array = parse_url($file);
	if(in_array($array['scheme'],array('http','https','ftp'))){
		return $file;
	}
	$length = strpos($file, '/');
	$file_s = substr($file, 0, $length).'-s'.substr($file, $length);
	$prefix = C('upload_http_prefix');
	if(!empty($prefix)){
		return $prefix.$file_s;
	}else{
		return C('site_path').C('upload_path').'/'.$file_s;
	}
}
//正则提取正文里指定的第几张图片地址
function ff_url_img_preg($content, $number=1, $ext='gif|jpg|jpeg|bmp|png'){
	preg_match_all("/(href|src)=([\"|']?)([^ \"'>]+\.($ext))\\2/i", $content, $matches);
	$imgarr = array_unique($matches[3]);
	$countimg = count($imgarr);
	if($number > $countimg){
		$number = $countimg;
	}
	$imgurl = $imgarr[($number-1)];
	if($imgurl){
		return $imgurl;
	}
	return C('site_path').'Public/images/no.png';
}
// 获取26个字母链接
function ff_url_letters($file='vod',$str=''){
	if(C('url_html')){
		$index='index.html';
	}else{
		$index='index.php';
	}
    for($i=1;$i<=26;$i++){
	   $url = ff_url($file.'/search', array('id'=>chr($i+64),'x'=>'letter'), true);
	   $str.='<a href="'.$url.'" class="letter_on">'.chr($i+64).'</a>';
	}
	return $str;
}
/*-------------------------------------------------栏目分类相关函数开始------------------------------------------------------------------*/
//通过栏目条件获取对应的栏目名称/别名等
function ff_list_find($cid, $field='list_name'){
	$info = D("List")->ff_find($field, array('list_id'=>array('eq',$cid)), false);
	if($info){
		return $info[$field];
	}else{
		return false;
	}
}
// 检查当前栏目是否没有小类
function ff_list_isson($pid){
	$count = M("List")->where('list_pid='.$pid)->count('list_id');
	if($count){
		return false;
	}else{
		return true;
	}
}
//获取当前分类的子类
function ff_list_ids($cid){
	$tree = list_search(ff_mysql_list('limit:0;cahce_name:default;cahce_time:default;order:list_oid;sort:asc'), 'list_id='.$cid);
	$array = array();
	if (!empty($tree[0]['list_son'])) {
		foreach($tree[0]['list_son'] as $val){
			$array[] = $val['list_id'];
		}
	}
	array_push($array, $cid);
	return implode(',', array_unique($array)); 
}
// 获取栏目数据统计
function ff_list_count($cid){
	$where = array();
	if(999 == $cid){
		$rs = M("Vod");
		$where['vod_cid'] = array('gt',0);
		$where['vod_addtime'] = array('gt',ff_linux_time(1));//当天更新的影视
		$count = $rs->where($where)->count('vod_id');
	}elseif(0 == $cid){
		$rs = M("Vod");
		$where['vod_cid'] = array('gt',0);
		$count = $rs->where($where)->count('vod_id');	
	}else{
		$sid = ff_list_find($cid,'list_sid');
		if ($sid == '2'){
			$where['news_cid'] = $cid;
			$where['news_status'] = 1;
			$rs = M("News");
			$count = $rs->where($where)->count('news_id');
		}else{
			$where['vod_cid'] = array('in',ff_list_ids($cid));	
			$where['vod_status'] = 1;
			$rs = M("Vod");
			$count = $rs->where($where)->count('vod_id');
		}
	}
	//dump($rs->getLastSql());
	return $count+0;
}
/*------------------------------------------------------标签解析函数开始------------------------------------------------------------------*/
//路径参数处理函数
function ff_param_url(){
	$array = array();
	$array['id'] = intval($_REQUEST['id']);
	$array['type'] = htmlspecialchars(urldecode(trim($_REQUEST['type'])));
	$array['area'] = htmlspecialchars(urldecode(trim($_REQUEST['area'])));
	$array['year'] = htmlspecialchars($_REQUEST['year']);
	$array['star'] = htmlspecialchars(urldecode(trim($_REQUEST['star'])));
	$array['state'] = htmlspecialchars(urldecode(trim($_REQUEST['state'])));
	$array['ispay'] = htmlspecialchars(trim($_REQUEST['ispay']));
	$array['order'] = ff_order_by($_GET['order']);
	$array['limit'] = !empty($_GET['limit']) ? intval($_GET['limit']) : 10;
	$array['page'] = !empty($_GET['p']) ? intval($_GET['p']) : 1;
	$array['ajax'] = intval($_REQUEST['ajax']);
	//
	$array['sid'] = intval($_REQUEST['sid']);
	$array['cid'] = intval($_REQUEST['cid']);
	$array['wd'] = htmlspecialchars(urldecode(trim($_REQUEST['wd'])));
	$array['language'] = htmlspecialchars(urldecode(trim($_REQUEST['language'])));
	$array['letter'] = htmlspecialchars(trim($_REQUEST['letter']));
	$array['actor'] = htmlspecialchars(urldecode(trim($_REQUEST['actor'])));
	$array['director'] = htmlspecialchars(urldecode(trim($_REQUEST['director'])));
	$array['name'] = htmlspecialchars(urldecode(trim($_REQUEST['name'])));
	$array['ename'] = htmlspecialchars(trim($_REQUEST['ename']));
	$array['remark'] = htmlspecialchars(urldecode(trim($_REQUEST['remark'])));
	$array['play'] = htmlspecialchars(urldecode(trim($_REQUEST['play'])));
	$array['inputer'] = htmlspecialchars(urldecode(trim($_REQUEST['inputer'])));
	$array['tag'] = htmlspecialchars(urldecode(trim($_REQUEST['tag'])));
	return $array;
}
//分页跳转参数处理(多余空的将去除)
function ff_param_jump($where){
	if($where['sid']){
		$jumpurl['sid'] = $where['sid'];
	}
	if($where['id']){
		$jumpurl['id'] = $where['id'];
	}
	if($where['year']){
		$jumpurl['year'] = $where['year'];
	}	
	if($where['language']){
		$jumpurl['language'] = urlencode($where['language']);
	}
	if($where['area']){
		$jumpurl['area'] = urlencode($where['area']);
	}
	if($where['letter']){
		$jumpurl['letter'] = $where['letter'];
	}
	if($where['actor']){
		$jumpurl['actor'] = urlencode($where['actor']);
	}
	if($where['director']){
		$jumpurl['director'] = urlencode($where['director']);
	}
	if($where['wd']){
		$jumpurl['wd'] = urlencode($where['wd']);
	}		
	if($where['order'] != 'addtime' && $where['order']){
		$jumpurl['order'] = $where['order'];
	}
	if($where['limit']){
		$jumpurl['limit'] = $where['limit'];
	}
	$jumpurl['p'] = '';
	return $jumpurl;
}
//返回安全的orderby
function ff_order_by($order = 'addtime'){
	if(empty($order)){
		return 'hits';
	}
	$array = array();
	$array['id'] = 'id';
	$array['hits'] = 'hits';
	$array['hits_month'] = 'hits_month';
	$array['hits_week'] = 'hits_week';
	$array['stars'] = 'stars';
	$array['up'] = 'up';
	$array['down'] = 'down';
	$array['gold'] = 'gold';
	$array['golder'] = 'golder';
	$array['score'] = 'score';
	$array['year'] = 'year';
	$array['letter'] = 'letter';
	$array['addtime'] = 'addtime';
	$array['filmtime'] = 'filmtime';
	$array['logtime'] = 'logtime';
	$array['deadtime'] = 'deadtime';
	return $array[trim($order)];
}
// 生成参数列表,以数组形式返回
function ff_param_lable($tag = ''){
	//3.3增加传入数组则直接解析
	if(is_array($tag)){
		return $tag;
	}
	$param = array();
	$array = explode(';', $tag);
	foreach ($array as $v){
		list($key,$val) = explode(':',trim($v));
		$param[trim($key)] = trim($val);
	}
	return $param;
}
// 循环标签查询参数格式化
function ff_mysql_param($tag){
	$params = array();
	// 数据表字段
	$params['field']= isset($tag['field']) ? $tag['field'] : '*';
	// 查询条目
	$params['limit']= isset($tag['limit']) ? $tag['limit'] : '10';
	// 排序参数
	$params['order']= isset($tag['order']) ? $tag['order'] : '';
	$params['sort']= isset($tag['sort']) ? $tag['sort'] : '';
	// 分组参数
	$params['group']= isset($tag['group']) ? $tag['group'] : '';
	// 分页参数
	$params['page_is']= isset($tag['page_is']) ? $tag['page_is'] : false;
	$params['page_id']= isset($tag['page_id']) ? $tag['page_id'] : '';
	$params['page_p']= isset($tag['page_p']) ? $tag['page_p'] : '';
	// 缓存参数
	if($tag['cache_name'] ==  'default'){
		$params['cache_name'] = md5(C('cache_foreach_prefix').'_'.implode('_',$tag));
	}else{
		$params['cache_name']= isset($tag['cache_name']) ? md5(C('cache_foreach_prefix').'_'.$tag['cache_name']) : '';
	}
	// 缓存时间
	if($tag['cache_time'] == 'default'){
		$params['cache_time']= intval(C('cache_foreach'));
	}else{
		$params['cache_time']= isset($tag['cache_time']) ? intval($tag['cache_time']) : '';
	}
	return $params;
}
// 循环标签.导航
function ff_mysql_nav($tag){
	$tag = ff_param_lable($tag);
	$where = array();
	$where['nav_status'] = array('eq',1);
	if ($tag['ids']) {
		$where['nav_id'] = array('in',$tag['ids']);
	}
	if (isset($tag['pid'])) {
		$where['nav_pid'] = array('in',$tag['pid']);
	}
	return D('Nav')->ff_select_page(ff_mysql_param($tag), $where);
}
// 循环标签.轮播
function ff_mysql_slide($tag){
	$tag = ff_param_lable($tag);
	$where = array();
	$where['slide_status'] = array('eq',1);
	if ($tag['ids']) {
		$where['slide_id'] = array('in',$tag['ids']);
	}
	if ($tag['cid']) {
		$where['slide_cid'] = array('in',$tag['cid']);
	}
	return D('Slide')->ff_select_page(ff_mysql_param($tag), $where);
}
// 循环标签.分类
function ff_mysql_list($tag){
	$tag = ff_param_lable($tag);
	$where = array();
	$where['list_status'] = array('eq',1);
	if ($tag['ids']) {
		$where['list_id'] = array('in',$tag['ids']);
	}
	if (isset($tag['pid'])) {
		$where['list_pid'] = array('in',$tag['pid']);
	}
	if ($tag['sid']) {
		$where['list_sid'] = array('in',$tag['sid']);
	}
	return D('List')->ff_select_page(ff_mysql_param($tag), $where);
}
// 循环标签.友链
function ff_mysql_link($tag){
	$tag = ff_param_lable($tag);
	$where = array();
	if ($tag['ids']) {
		$where['link_id'] = array('in',$tag['ids']);
	}
	if ($tag['type']) {
		$where['link_type'] = array('eq',$tag['type']);
	}
	return D('Link')->ff_select_page(ff_mysql_param($tag), $where);
}
// 循环标签.TAG话题
function ff_mysql_tags($tag_str){
	$tag = ff_param_lable($tag_str);
	$where = array();
	//根据参数生成查询条件
	if ($tag['ids']) {
		$where['tag_id'] = array('in',$tag['ids']);
	}
	if ($tag['cid']) {
		$where['tag_cid'] = array('in',$tag['cid']);
	}
	if ($tag['list']) {
		$where['tag_list'] = array('eq',''.$tag['list'].'');
	}
	$rs = D('Tag');
	return $rs->ff_select_page(ff_mysql_param($tag), $where);
}
// 循环标签.视频
function ff_mysql_vod($tag_str){
	$tag = ff_param_lable($tag_str);
	$where = array();
	//根据参数生成查询条件
	$where['vod_status'] = array('eq',1);	
	if ($tag['ids']) {
		$where['vod_id'] = array('in',$tag['ids']);
	}
	if ($tag['ids_not']) {
		$where['vod_id'] = array('not in',$tag['ids_not']);
	}
	if ($tag['cid']) {
		$where['vod_cid'] = array('in',$tag['cid']);
	}
	if ($tag['cid_not']) {
		$where['vod_cid'] = array('not in',$tag['cid_not']);
	}
	if ($tag['stars']) {
		$where['vod_stars'] = array('in',$tag['stars']);
	}
	if ($tag['letter']) {
		$where['vod_letter'] = array('in',$tag['letter']);
	}
	if ($tag['upday']) {
		$where['vod_addtime'] = array('gt',ff_linux_time($tag['upday']));
	}
	if ($tag['lastday']) {
		$where['vod_hits_lasttime'] = array('gt',ff_linux_time($tag['lastday']));
	}
	if (isset($tag['filmday'])) {
		$where['vod_filmtime'] = array('gt',ff_linux_time($tag['filmday']));
	}
	if (isset($tag['lastday'])) {
		$where['vod_hits_lasttime'] = array('gt',ff_linux_time($tag['lastday']));
	}
	if($tag['isend'] == 'true'){
		$where['vod_isend'] = 1;
	}else if($tag['isend'] == 'false'){
		$where['vod_isend'] = 0;
	}
	if($tag['continu']){
		$where['vod_continu'] = array('neq',0);
	}
	if($tag['pic_slide']){
		$where['vod_pic_slide'] = array('neq','');
	}
	if($tag['pic_bg']){
		$where['vod_pic_bg'] = array('neq','');
	}	
	if($tag['scenario']){
		$where['vod_scenario'] = array('neq','');
	}	
	if ($tag['list_ename']) {
		$where['list_dir'] =  array('eq',$tag['list_ename']);
	}
	if ($tag['year']) {
		$year = explode(',',$tag['year']);
		if (count($year) > 1) {
			$where['vod_year'] = array('between',$year[0].','.$year[1]);
		}else{
			$where['vod_year'] = array('eq',$tag['year']);
		}
	}	
	if ($tag['state']) {
		$where['vod_state'] = array('eq',$tag['state']);
	}
	if ($tag['version']) {
		$where['vod_version'] = array('eq',$tag['version']);
	}
	if ($tag['series']) {
		$where['vod_series'] = array('eq',$tag['series']);
	}
	if ($tag['tv']) {
		$where['vod_tv'] = array('eq',$tag['tv']);
	}	
	if ($tag['inputer']) {
		$where['vod_inputer'] = array('eq',$tag['inputer']);
	}
	if ($tag['name']) {
		$where['vod_name'] = array('like','%'.$tag['name'].'%');
	}
	if ($tag['title']) {
		$where['vod_title'] = array('like','%'.$tag['title'].'%');
	}
	if ($tag['actor']) {
		$where['vod_actor'] = array('like','%'.$tag['actor'].'%');
	}
	if ($tag['director']) {
		$where['vod_director'] = array('like','%'.$tag['director'].'%');
	}
	if ($tag['weekday']) {
		//$where['vod_weekday'] = array('in','周一,周二');
		//$where['vod_weekday'] = array(array('like','周一%'), array('like','周二%'), 'or');
		foreach(explode(',',$tag['weekday']) as $key=>$value){
			$where['vod_weekday'][] = array('like','%'.$value.'%');
		}
		$where['vod_weekday'][] = 'or';
	}	
	if ($tag['area']) {
		foreach(explode(',',$tag['area']) as $key=>$value){
			$where['vod_area'][] = array('like','%'.$value.'%');
		}
		$where['vod_area'][] = 'or';
	}
	if ($tag['language']) {
		foreach(explode(',',$tag['language']) as $key=>$value){
			$where['vod_language'][] = array('like','%'.$value.'%');
		}
		$where['vod_language'][] = 'or';
	}
	if ($tag['play']) {
		$where['vod_play'] = array('like','%'.$tag['play'].'%');
	}
	if ($tag['wd']) {
		$search = array();
		$search['vod_name'] = array('like','%'.$tag['wd'].'%');
		$search['vod_title'] = array('like','%'.$tag['wd'].'%');
		$search['vod_actor'] = array('like','%'.$tag['wd'].'%');
		$search['vod_director'] = array('like','%'.$tag['wd'].'%');
		$search['_logic'] = 'or';
		$where['_complex'] = $search;
	}
	if ($tag['hits']) {
		$hits = explode(',',$tag['hits']);
		if (count($hits) > 1) {
			$where['vod_hits'] = array('between',$hits[0].','.$hits[1]);
		}else{
			$where['vod_hits'] = array('gt',$hits[0]);
		}
	}
	if ($tag['up']) {
		$up = explode(',',$tag['up']);
		if (count($up) > 1) {
			$where['vod_up'] = array('between',$up[0].','.$up[1]);
		}else{
			$where['vod_up'] = array('gt',$up[0]);
		}
	}
	if ($tag['down']) {
		$down = explode(',',$tag['down']);
		if (count($down) > 1) {
			$where['vod_down'] = array('between',$down[0].','.$down[1]);
		}else{
			$where['vod_down'] = array('gt',$down[0]);
		}
	}
	if ($tag['gold']) {
		$gold = explode(',',$tag['gold']);
		if (count($gold) > 1) {
			$where['vod_gold'] = array('between',$gold[0].','.$gold[1]);
		}else{
			$where['vod_gold'] = array('gt',$gold[0]);
		}
	}
	if ($tag['golder']) {
		$golder = explode(',',$tag['golder']);
		if (count($golder) > 1) {
			$where['vod_golder'] = array('between',$golder[0].','.$golder[1]);
		}else{
			$where['vod_golder'] = array('gt',$golder[0]);
		}
	}
	if (isset($tag['copyright'])) {
		$where['vod_copyright'] = array('gt',$tag['copyright']);
	}
	if ($tag['ispay'] != "") {
		$where['vod_ispay'] = array('eq', intval($tag['ispay']));
	}
	//分支加载不同的模型查询数据开始
	if($tag['tag_name']){//视图模型查询
		$where['tag_name'] = array('in',$tag['tag_name']);
		if($tag['tag_cid']){
			$where['tag_cid'] = array('in',$tag['tag_cid']);
		}
		if($tag['tag_list']){
			$where['tag_list'] = $tag['tag_list'];
		}
		if($tag['tag_ename']){
			$where['tag_ename'] = $tag['tag_ename'];
		}
		$rs = D('TagvodView');
	}else{
		$rs = D('VodView');
	}
	return $rs->ff_select_page(ff_mysql_param($tag), $where);
}
// 循环标签.文章
function ff_mysql_news($tag_str){
	$tag = ff_param_lable($tag_str);
	$where = array();
	//根据参数生成查询条件
	$where['news_status'] = array('eq',1);
	if ($tag['ids']) {
		$where['news_id'] = array('in',$tag['ids']);
	}
	if ($tag['ids_not']) {
		$where['news_id'] = array('not in',$tag['ids_not']);
	}
	if ($tag['cid']) {
		$where['news_cid'] = array('in',$tag['cid']);
	}	
	if ($tag['cid_not']) {
		$where['news_cid'] = array('not in',$tag['cid_not']);
	}		
	if ($tag['list_ename']) {
		$where['list_dir'] =  array('eq',$tag['list_ename']);
	}
	if ($tag['stars']) {
		$where['news_stars'] = array('in',$tag['stars']);
	}	
	if ($tag['letter']) {
		$where['news_letter'] = array('in',$tag['letter']);
	}
	if ($tag['day']) {
		$where['news_addtime'] = array('gt',ff_linux_time($tag['day']));
	}		
	if (isset($tag['lastday'])) {
		$where['news_hits_lasttime'] = array('gt',ff_linux_time($tag['lastday']));
	}
	if ($tag['hits']) {
		$hits = explode(',',$tag['hits']);
		if (count($hits) > 1) {
			$where['news_hits'] = array('between',$hits[0].','.$hits[1]);
		}else{
			$where['news_hits'] = array('gt',$hits[0]);
		}
	}
	if ($tag['up']) {
		$up = explode(',',$tag['up']);
		if (count($up) > 1) {
			$where['news_up'] = array('between',$up[0].','.$up[1]);
		}else{
			$where['news_up'] = array('gt',$up[0]);
		}
	}
	if ($tag['down']) {
		$down = explode(',',$tag['down']);
		if (count($down) > 1) {
			$where['news_down'] = array('between',$down[0].','.$down[1]);
		}else{
			$where['news_down'] = array('gt',$down[0]);
		}
	}
	if ($tag['gold']) {
		$gold = explode(',',$tag['gold']);
		if (count($gold) > 1) {
			$where['news_gold'] = array('between',$gold[0].','.$gold[1]);
		}else{
			$where['news_gold'] = array('gt',$gold[0]);
		}
	}
	if ($tag['golder']) {
		$golder = explode(',',$tag['golder']);
		if (count($golder) > 1) {
			$where['news_golder'] = array('between',$golder[0].','.$golder[1]);
		}else{
			$where['news_golder'] = array('gt',$golder[0]);
		}
	}	
	if ($tag['name']) {
		$where['news_name'] = array('like','%'.$tag['name'].'%');
	}	
	if ($tag['title']) {
		$where['news_title'] = array('like','%'.$tag['title'].'%');
	}
	if ($tag['remark']) {
		$where['news_remark'] = array('like','%'.$tag['remark'].'%');
	}
	if($tag['pic_slide']){
		$where['news_pic_slide'] = array('neq','');
	}
	if($tag['pic_bg']){
		$where['news_pic_bg'] = array('neq','');
	}
	if ($tag['wd']) {
		$search = array();
		$search['news_name'] = array('like','%'.$tag['wd'].'%');
		$search['news_remark'] = array('like','%'.$tag['wd'].'%');
		$search['_logic'] = 'or';
		$where['_complex'] = $search;
	}	
	//分支加载不同的模型查询数据开始
	if($tag['tag_name']){//视图模型查询
		$where['tag_name'] = array('in',$tag['tag_name']);
		if($tag['tag_cid']){
			$where['tag_cid'] = array('in',$tag['tag_cid']);
		}
		if($tag['tag_list']){
			$where['tag_list'] = $tag['tag_list'];
		}
		if($tag['tag_ename']){
			$where['tag_ename'] = $tag['tag_ename'];
		}
		$rs = D('TagnewsView');
	}else{
		$rs = D('NewsView');
	}
	return $rs->ff_select_page(ff_mysql_param($tag), $where);
}
/*数据调用-专题循环标签*/
function ff_mysql_special($tag){
	$tag = ff_param_lable($tag);
	$where = array();
	//根据参数生成查询条件
	$where['special_status'] = array('eq',1);	
	if ($tag['ids']) {
		$where['special_id'] = array('in',$tag['ids']);
	}
	if ($tag['hits']) {
		$hits = explode(',',$tag['hits']);
		if (count($hits) > 1) {
			$where['special_hits'] = array('between',$hits[0].','.$hits[1]);
		}else{
			$where['special_hits'] = array('gt',$hits[0]);
		}
	}
	if ($tag['name']) {
		$where['special_name'] = array('like','%'.$tag['name'].'%');
	}
	if ($tag['ename']) {
		$where['special_ename'] = array('like','%'.$tag['ename'].'%');
	}
	//分支加载不同的模型查询数据开始
	if($tag['tag_name']){
		$where['tag_name'] = array('in',$tag['tag_name']);
		if($tag['tag_cid']){
			$where['tag_cid'] = array('in',$tag['tag_cid']);
		}
		if($tag['tag_list']){
			$where['tag_list'] = $tag['tag_list'];
		}
		if($tag['tag_ename']){
			$where['tag_ename'] = $tag['tag_ename'];
		}
		$rs = D('TagspecialView');
	}else{
		$rs = D('Special');
	}
	return $rs->ff_select_page(ff_mysql_param($tag), $where);
}
// 循环标签.讨论
function ff_mysql_forum($tag_str){
	$tag = ff_param_lable($tag_str);
	$where = array();
	//根据参数生成查询条件
	if (isset($tag['status'])) {
		$where['forum_status'] = array('eq', $tag['status']);
	}
	if ($tag['sid']) {
		$where['forum_sid'] = array('eq',$tag['sid']);
	}		
	if ($tag['ids']) {
		$where['forum_id'] = array('in',$tag['ids']);
	}
	if ($tag['ids_not']) {
		$where['forum_id'] = array('not in',$tag['ids_not']);
	}
	if ($tag['cid']) {
		$where['forum_cid'] = array('in',$tag['cid']);
	}
	if ($tag['cid_not']) {
		$where['forum_cid'] = array('not in',$tag['cid_not']);
	}
	if (isset($tag['pid'])) {
		$where['forum_pid'] = array('in',$tag['pid']);
	}	
	if (isset($tag['pid_not'])) {
		$where['forum_pid'] = array('not in',$tag['pid_not']);
	}	
	if ($tag['uid']) {
		$where['forum_uid'] = array('in',$tag['uid']);
	}
	if ($tag['uid_not']) {
		$where['forum_uid'] = array('not in',$tag['uid_not']);
	}	
	if ($tag['up']) {
		$up = explode(',',$tag['up']);
		if (count($up) > 1) {
			$where['forum_up'] = array('between',$up[0].','.$up[1]);
		}else{
			$where['forum_up'] = array('gt',$up[0]);
		}
	}
	if ($tag['down']) {
		$down = explode(',',$tag['down']);
		if (count($down) > 1) {
			$where['forum_down'] = array('between',$down[0].','.$down[1]);
		}else{
			$where['forum_down'] = array('gt',$down[0]);
		}
	}
	if ($tag['upday']) {
		$where['forum_addtime'] = array('gt',ff_linux_time($tag['upday']));
	}
	if ($tag['wd']) {
		$search = array();
		$search['forum_content'] = array('like','%'.$tag['wd'].'%');
		$search['forum_ip'] = array('like','%'.$tag['wd'].'%');
		$search['user_name'] = array('like','%'.$tag['wd'].'%');
		$search['_logic'] = 'or';
		$where['_complex'] = $search;
	}
	// 分支加载不同的视图模型查询
	if($tag['sid'] == 1){
		$viewFields = array (
			'Forum'=>array('*'),
			'User'=>array('user_id','user_name','user_face','_on'=>'Forum.forum_uid = User.user_id'),
			'Vod'=>array('vod_id','vod_cid','vod_name','vod_title','vod_ename','vod_actor','vod_year','vod_area','vod_language','vod_pic','vod_content','vod_continu','vod_isend','vod_total','vod_hits','vod_up','vod_down','vod_addtime','vod_play','_on'=>'Forum.forum_cid = Vod.vod_id'),
		);
	}else if($tag['sid'] == 2){
		$viewFields = array (
			'Forum'=>array('*'),
			'User'=>array('user_id','user_name','user_face','_on'=>'Forum.forum_uid = User.user_id'),
			'News'=>array('*','_on'=>'Forum.forum_cid = News.news_id'),
		);
	}else if($tag['sid'] == 3){
		$viewFields = array (
			'Forum'=>array('*'),
			'User'=>array('user_id','user_name','user_face','_on'=>'Forum.forum_uid = User.user_id'),
			'Special'=>array('*','_on'=>'Forum.forum_cid = Special.special_id'),
		);
	}else{
		$viewFields = '';
	}
	return D('ForumView')->ff_select_page(ff_mysql_param($tag), $where, $viewFields);
}
// 循环标签.用户资料
function ff_mysql_user($tag_str){
	$tag = ff_param_lable($tag_str);
	$where = array();
	//根据参数生成查询条件
	if (isset($tag['status'])) {
		$where['user_status'] = array('eq', $tag['status']);
	}
	if ($tag['ids']) {
		$where['user_id'] = array('in',$tag['ids']);
	}
	if ($tag['pid']) {
		$where['user_pid'] = array('in',$tag['pid']);
	}
	if ($tag['follow']) {
		$arr = explode(',',$tag['follow']);
		if (count($arr) > 1) {
			$where['user_follow'] = array('between',$arr[0].','.$arr[1]);
		}else{
			$where['user_follow'] = array('gt',$arr[0]);
		}
	}
	if ($tag['up']) {
		$up = explode(',',$tag['up']);
		if (count($up) > 1) {
			$where['user_up'] = array('between',$up[0].','.$up[1]);
		}else{
			$where['user_up'] = array('gt',$up[0]);
		}
	}
	if ($tag['down']) {
		$down = explode(',',$tag['down']);
		if (count($down) > 1) {
			$where['user_down'] = array('between',$down[0].','.$down[1]);
		}else{
			$where['user_down'] = array('gt',$down[0]);
		}
	}
	if ($tag['hits']) {
		$arr = explode(',',$tag['hits']);
		if (count($arr) > 1) {
			$where['user_hits'] = array('between',$arr[0].','.$arr[1]);
		}else{
			$where['user_hits'] = array('gt',$arr[0]);
		}
	}
	if ($tag['addtime']) {
		$where['user_addtime'] = array('gt',ff_linux_time($tag['addtime']));
	}
	if ($tag['logtime']) {
		$where['user_logtime'] = array('gt',ff_linux_time($tag['logtime']));
	}
	if ($tag['wd']) {
		$search = array();
		$search['user_name'] = array('like','%'.$tag['wd'].'%');
		$search['user_email'] = array('like','%'.$tag['wd'].'%');
		//$search['user_ip'] = array('like','%'.$tag['wd'].'%');
		$search['_logic'] = 'or';
		$where['_complex'] = $search;
	}
	return D('User')->ff_select_page(ff_mysql_param($tag), $where);
}
// 循环标签.订单
function ff_mysql_orders($tag_str){
	$tag = ff_param_lable($tag_str);
	$where = array();
	//根据参数生成查询条件
	if (isset($tag['status'])) {
		$where['order_status'] = array('eq', $tag['status']);
	}	
	if (isset($tag['ispay'])) {
		$where['order_ispay'] = array('eq', $tag['ispay']);
	}
	if (isset($tag['shipping'])) {
		$where['order_shipping'] = array('eq', $tag['shipping']);
	}
	if ($tag['ids']) {
		$where['order_id'] = array('in',$tag['ids']);
	}
	if ($tag['uid']) {
		$where['order_uid'] = array('in',$tag['uid']);
	}
	if ($tag['gid']) {
		$where['order_gid'] = array('in',$tag['gid']);
	}
	if ($tag['money']) {
		$arr = explode(',',$tag['money']);
		if (count($arr) > 1) {
			$where['order_money'] = array('between',$arr[0].','.$arr[1]);
		}else{
			$where['order_money'] = array('gt',$arr[0]);
		}
	}
	if ($tag['addtime']) {
		$where['order_addtime'] = array('gt',ff_linux_time($tag['addtime']));
	}
	if ($tag['paytime']) {
		$where['order_paytime'] = array('gt',ff_linux_time($tag['paytime']));
	}
	if ($tag['confirmtime']) {
		$where['order_confirmtime'] = array('gt',ff_linux_time($tag['confirmtime']));
	}
	if ($tag['wd']) {
		$search = array();
		$search['order_sign'] = array('like','%'.$tag['wd'].'%');
		$search['order_info'] = array('like','%'.$tag['wd'].'%');
		$search['_logic'] = 'or';
		$where['_complex'] = $search;
	}
	return D('OrdersView')->ff_select_page(ff_mysql_param($tag), $where);
}
// 循环标签.记录
function ff_mysql_record($tag_str){
	$tag = ff_param_lable($tag_str);
	$where = array();
	//根据参数生成查询条件
	if ($tag['ids']) {
		$where['record_id'] = array('in',$tag['ids']);
	}	
	if ($tag['sid']) {
		$where['record_sid'] = array('in',$tag['sid']);
	}
	if ($tag['did']) {
		$where['record_did'] = array('in',$tag['did']);
	}
	if ($tag['uid']) {
		$where['record_uid'] = array('in',$tag['uid']);
	}	
	if ($tag['type']) {
		$where['record_type'] = array('in',$tag['type']);
	}
	if ($tag['upday']) {
		$where['record_addtime'] = array('gt',ff_linux_time($tag['upday']));
	}
	// 分支加载不同的关联条件
	if($tag['sid'] == 1){
		$viewFields = array (
		 'Record'=>array('*'),
		 'User'=>array('user_id','user_name','user_face','_on'=>'Record.record_uid = User.user_id'),
		 'Vod'=>array('*','_on'=>'Record.record_did = Vod.vod_id'),
		);
	}else if($tag['sid'] == 2){
		$viewFields = array (
			'Record'=>array('*'),
			'User'=>array('user_id','user_name','user_face','_on'=>'Record.record_uid = User.user_id'),
			'News'=>array('*','_on'=>'Record.record_did = News.news_id'),
		);
	}else if($tag['sid'] == 3){
		array (
			'Record'=>array('*'),
			'User'=>array('user_id','user_name','user_face','_on'=>'Record.record_uid = User.user_id'),
			'Special'=>array('*','_on'=>'Record.record_did = Special.special_id'),
		);
	}else{
		$viewFields = '';
	}
	return D('RecordView')->ff_select_page(ff_mysql_param($tag), $where, $viewFields);
}
// 循环标签.记录
function ff_mysql_score($tag_str){
	$tag = ff_param_lable($tag_str);
	$where = array();
	//根据参数生成查询条件
	if ($tag['ids']) {
		$where['score_id'] = array('in',$tag['ids']);
	}	
	if ($tag['sid']) {
		$where['score_sid'] = array('in',$tag['sid']);
	}
	if ($tag['did']) {
		$where['score_did'] = array('in',$tag['did']);
	}
	if ($tag['uid']) {
		$where['score_uid'] = array('in',$tag['uid']);
	}	
	if ($tag['type']) {
		$where['score_type'] = array('in',$tag['type']);
	}
	if ($tag['addtime']) {
		$where['score_addtime'] = array('gt',ff_linux_time($tag['addtime']));
	}
	// 分支加载不同的关联条件
	if($tag['sid'] == 1){
		$viewFields = array (
		 'Score'=>array('*'),
		 'User'=>array('user_id','user_name','user_face','_on'=>'Score.score_uid = User.user_id'),
		 'Vod'=>array('vod_name'=>'detail_name','vod_cid'=>'detail_cid','_on'=>'Record.record_did = Vod.vod_id'),
		);
	}else if($tag['sid'] == 2){
		$viewFields = array (
			'Score'=>array('*'),
			'User'=>array('user_id','user_name','user_face','_on'=>'Score.score_uid = User.user_id'),
			'News'=>array('new_name'=>'detail_name','news_cid'=>'detail_cid','_on'=>'Record.record_did = News.news_id'),
		);
	}else if($tag['sid'] == 3){
		array (
			'Score'=>array('*'),
			'User'=>array('user_id','user_name','user_face','_on'=>'Score.score_uid = User.user_id'),
			'Special'=>array('special_name'=>'detail_name','_on'=>'Record.record_did = Special.special_id'),
		);
	}else{
		$viewFields = '';
	}
	return D('ScoreView')->ff_select_page(ff_mysql_param($tag), $where, $viewFields);
}
/*---------------------------------------ThinkPhp扩展函数库开始------------------------------------------------------------------
 * @category   Think
 * @package  Common
 * @author   liu21st <liu21st@gmail.com>*/
// 获取客户端IP地址
function get_client_ip(){
   if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
       $ip = getenv("HTTP_CLIENT_IP");
   else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
       $ip = getenv("HTTP_X_FORWARDED_FOR");
   else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
       $ip = getenv("REMOTE_ADDR");
   else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
       $ip = $_SERVER['REMOTE_ADDR'];
   else
       $ip = "unknown";
   return($ip);
}
//输出安全的html
function h($text, $tags = null){
	$text	=	trim($text);
	//完全过滤注释
	$text	=	preg_replace('/<!--?.*-->/','',$text);
	//完全过滤动态代码
	$text	=	preg_replace('/<\?|\?'.'>/','',$text);
	//完全过滤js
	$text	=	preg_replace('/<script?.*\/script>/','',$text);

	$text	=	str_replace('[','&#091;',$text);
	$text	=	str_replace(']','&#093;',$text);
	$text	=	str_replace('|','&#124;',$text);
	//过滤换行符
	$text	=	preg_replace('/\r?\n/','',$text);
	//br
	$text	=	preg_replace('/<br(\s\/)?'.'>/i','[br]',$text);
	$text	=	preg_replace('/(\[br\]\s*){10,}/i','[br]',$text);
	//过滤危险的属性，如：过滤on事件lang js
	while(preg_match('/(<[^><]+)( lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1],$text);
	}
	while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1].$mat[3],$text);
	}
	if(empty($tags)) {
		$tags = 'table|td|th|tr|i|b|u|strong|img|p|br|div|strong|em|ul|ol|li|dl|dd|dt|a';
	}
	//允许的HTML标签
	$text	=	preg_replace('/<('.$tags.')( [^><\[\]]*)>/i','[\1\2]',$text);
	//过滤多余html
	$text	=	preg_replace('/<\/?(html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|script|style|xml)[^><]*>/i','',$text);
	//过滤合法的html标签
	while(preg_match('/<([a-z]+)[^><\[\]]*>[^><]*<\/\1>/i',$text,$mat)){
		$text=str_replace($mat[0],str_replace('>',']',str_replace('<','[',$mat[0])),$text);
	}
	//转换引号
	while(preg_match('/(\[[^\[\]]*=\s*)(\"|\')([^\2=\[\]]+)\2([^\[\]]*\])/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1].'|'.$mat[3].'|'.$mat[4],$text);
	}
	//过滤错误的单个引号
	while(preg_match('/\[[^\[\]]*(\"|\')[^\[\]]*\]/i',$text,$mat)){
		$text=str_replace($mat[0],str_replace($mat[1],'',$mat[0]),$text);
	}
	//转换其它所有不合法的 < >
	$text	=	str_replace('<','&lt;',$text);
	$text	=	str_replace('>','&gt;',$text);
	$text	=	str_replace('"','&quot;',$text);
	 //反转换
	$text	=	str_replace('[','<',$text);
	$text	=	str_replace(']','>',$text);
	$text	=	str_replace('|','"',$text);
	//过滤多余空格
	$text	=	str_replace('  ',' ',$text);
	return $text;
}
// 随机生成一组字符串
function build_count_rand ($number,$length=4,$mode=1) {
    if($mode==1 && $length<strlen($number) ) {
        //不足以生成一定数量的不重复数字
        return false;
    }
    $rand   =  array();
    for($i=0; $i<$number; $i++) {
        $rand[] =   rand_string($length,$mode);
    }
    $unqiue = array_unique($rand);
    if(count($unqiue)==count($rand)) {
        return $rand;
    }
    $count   = count($rand)-count($unqiue);
    for($i=0; $i<$count*3; $i++) {
        $rand[] =   rand_string($length,$mode);
    }
    $rand = array_slice(array_unique ($rand),0,$number);
    return $rand;
}
//XSS漏洞过滤
function remove_xss($val) {
   $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
   $search = 'abcdefghijklmnopqrstuvwxyz';
   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $search .= '1234567890!@#$%^&*()';
   $search .= '~`";:?+/={}[]-_|\'\\';
   for ($i = 0; $i < strlen($search); $i++) {
      $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
      $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
   }
   $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
   $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
   $ra = array_merge($ra1, $ra2);
   $found = true; // keep replacing as long as the previous round replaced something
   while ($found == true) {
      $val_before = $val;
      for ($i = 0; $i < sizeof($ra); $i++) {
         $pattern = '/';
         for ($j = 0; $j < strlen($ra[$i]); $j++) {
            if ($j > 0) {
               $pattern .= '(';
               $pattern .= '(&#[xX]0{0,8}([9ab]);)';
               $pattern .= '|';
               $pattern .= '|(&#0{0,8}([9|10|13]);)';
               $pattern .= ')*';
            }
            $pattern .= $ra[$i][$j];
         }
         $pattern .= '/i';
         $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
         $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
         if ($val_before == $val) {
            // no replacements were made, so exit the loop
            $found = false;
         }
      }
   }
   return $val;
}
/*** 把返回的数据集转换成Tree
 +----------------------------------------------------------
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array 
 */
function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0)
{
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}
/**----------------------------------------------------------
 * 在数据列表中搜索
 +----------------------------------------------------------
 * @param array $list 数据列表
 * @param mixed $condition 查询条件
 * 支持 array('name'=>$value) 或者 name=$value
 * @return array
 */
function list_search($list,$condition) {
    if(is_string($condition))
        parse_str($condition,$condition);
    // 返回的结果集合
    $resultSet = array();
    foreach ($list as $key=>$data){
        $find   =   false;
        foreach ($condition as $field=>$value){
            if(isset($data[$field])) {
                if(0 === strpos($value,'/')) {
                    $find   =   preg_match($value,$data[$field]);
                }elseif($data[$field]==$value){
                    $find = true;
                }
            }
        }
        if($find)
            $resultSet[]     =   &$list[$key];
    }
    return $resultSet;
}
/**
 +----------------------------------------------------------
 * 字节格式化 把字节数格式为 B K M G T 描述的大小
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function byte_format($size, $dec=2)
{
	$a = array("B", "KB", "MB", "GB", "TB", "PB");
	$pos = 0;
	while ($size >= 1024) {
		 $size /= 1024;
		   $pos++;
	}
	return round($size,$dec)." ".$a[$pos];
}
/**
 +----------------------------------------------------------
 * 对查询结果集进行排序
 +----------------------------------------------------------
 * @access public
 +----------------------------------------------------------
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 +----------------------------------------------------------
 * @return array
 +----------------------------------------------------------
 */
function list_sort_by($list,$field, $sortby='asc') {
   if(is_array($list)){
       $refer = $resultSet = array();
       foreach ($list as $i => $data)
           $refer[$i] = &$data[$field];
       switch ($sortby) {
           case 'asc': // 正向排序
                asort($refer);
                break;
           case 'desc':// 逆向排序
                arsort($refer);
                break;
           case 'nat': // 自然排序
                natcasesort($refer);
                break;
       }
       foreach ( $refer as $key=> $val)
           $resultSet[] = &$list[$key];
       return $resultSet;
   }
   return false;
}
?>