<li>
  <a href="{:ff_url_vod_read($feifei['list_id'],$feifei['list_dir'],$feifei['vod_id'],$feifei['vod_ename'],$feifei['vod_jumpurl'])}">{$feifei.vod_name|msubstr=0,14,'utf-8',true}</a>
  <small class="pull-right">{$feifei.vod_hits|number_format}</small>
</li>