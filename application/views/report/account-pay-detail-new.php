<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!doctype html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable
=no">
<meta name="renderer" content="webkit">
<title>在线进销存</title>
<link href="<?php echo base_url()?>statics/css/common.css?ver=20140430" rel="stylesheet">
<link href="<?php echo base_url()?>statics/css/<?php echo sys_skin()?>/ui.min.css?ver=20140430" rel="stylesheet">
<script src="<?php echo base_url()?>statics/js/common/seajs/2.1.1/sea.js?ver=20140430" id="seajsnode"></script>
<script src="<?php echo base_url()?>statics/js/common/libs/jquery/jquery-1.10.2.min.js"></script>
<script type="text/javascript">
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
<link rel="stylesheet" href="<?php echo base_url()?>statics/css/report.css" />
<style type="text/css">
.ui-icon-ellipsis{ right:5px; }
.ul-inline li{position:relative;}
#chk-wrap{line-height: 30px;}
.frozen-sdiv{ display: none;}
#grid tr{cursor:pointer;}
.mod-choose-input .ui-input{width:150px;}
</style>
</head>
<body>
<div class="mod-report">
  <div class="search-wrap cf">
    <div class="s-inner cf">
    	<div class = "fl">
    		<ul class="ul-inline fix">
		      	<li>
		          <label>日期:</label>
		          <input type="text" value="" class="ui-input ui-datepicker-input" name="filter-fromDate" id="filter-fromDate" /> - 
		          <input type="text" value="" class="ui-input ui-datepicker-input" name="filter-toDate"id="filter-toDate" />
		        </li>
		        <li>
		          <label>供应商:</label>
		          <span class="mod-choose-input" id="filter-customer">
		          	<input type="text" class="ui-input" id="supplierAuto"/>
		          	<span class="ui-icon-ellipsis"></span>
		          </span>
		        </li>
		        
		        <li><a class="ui-btn mrb" id="filter-submit">查询</a></li>
		      </ul>
    	</div>
    	<div class="fr"><!--<a href="#" class="ui-btn ui-btn-sp mrb fl" id="btn-print">打印</a>--><a href="#" class="ui-btn fl" id="btn-export">导出</a></div>  
    </div>
    
  </div>
  
    
	<div class="ui-print">
		<div class="grid-wrap" id="grid-wrap">
			<div class="grid-title">应付账款明细表</div>
			<div class="grid-subtitle"></div>
	    	<div id="grid-layout"><table id="grid"></table></div>
	   	</div>
	</div>
  	<div class="no-query"></div>
  
</div>
<script>
	seajs.use("dist/accountPayDetailNew");
</script>
</body>
</html>

