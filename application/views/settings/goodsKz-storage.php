<?php $this->load->view('header');?>
<script type="text/javascript">

var item_id = '<?php echo $item_id?>';
var DOMAIN = document.domain;
var WDURL = "";
var SCHEME= "<?php echo sys_skin()?>";
try{
	document.domain = '<?php echo base_url()?>';
}catch(e){
}
//ctrl+F5 增加版本号来清空iframe的缓存的
$(document).keydown(function(event) {
	/* Act on the event */
	if(event.keyCode === 116 && event.ctrlKey){
		var defaultPage = Public.getDefaultPage();
		var href = defaultPage.location.href.split('?')[0] + '?';
		var params = Public.urlParam();
		params['version'] = Date.parse((new Date()));
		for(i in params){
			if(i && typeof i != 'function'){
				href += i + '=' + params[i] + '&';
			}
		}
		defaultPage.location.href = href;
		event.preventDefault();
	}
});
</script>

<style>
#matchCon { width: 200px; }
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>

<body>
<div class="container" style="margin:10px 10px 0;">
  <div class="mod-search m0 cf" style="padding-right:0;">
    <div class="fl">
      <ul class="ul-inline">
        <li>
        	<span id="storage"></span>
        </li>
        <li>
          <input type="text" id="matchCon" class="ui-input" placeholder="货位编码/货位名称">
        </li>
        <li><a class="ui-btn mrb" id="search">查询</a>
      </ul>
    </div>
  </div>
  <div class="grid-wrap">
    <table id="grid">
    </table>
    <div id="page"></div>
  </div>
</div>
<script src="<?php echo base_url()?>statics/js/dist/goodsKzStorage.js?ver=20150522"></script>
</body>
</html>