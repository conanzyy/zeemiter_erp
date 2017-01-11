<?php $this->load->view('header');?>

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

<style>
body{background: #fff;}
.manage-wrap{margin: 20px auto 10px;width: 300px;}
.manage-wrap .ui-input{width: 200px;font-size:14px;}
.jia{
	padding-left:10px;
	background: url(<?php echo base_url()?>/statics/css/img/jia.png) 5px 6px  no-repeat;
	cursor: pointer;
}
.jia:hover{
	background: url(<?php echo base_url()?>/statics/css/img/jia.png) 5px 6px  #f0f0f0 no-repeat;
}
.l_label{
	float: left;min-width: 100px;
	height: 30px;margin:3px 10px 2px 0;
	line-height: 28px;margin-bottom: 5px;
	-webkit-box-sizing: border-box; 
	box-sizing: border-box; 
    border: 1px solid #D6DEE3;
    text-align: center;position: relative;
}
textarea.ui-input{
	height:50px;
	max-width:200px;
	min-width:200px;
}
</style>
</head>
<body>
<div id="manage-wrap" class="manage-wrap">
	<form id="manage-form" action="#">
		<ul class="mod-form-rows">
			<li class="row-item">
				<div class="label-wrap"><label for="number">货位编号: <em style="color:red">*</em></label></div>
				<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="number" id="number"></div>
				<input type="hidden" value="<?php echo $area_code ?>" id="preNum" />
			</li>
			<li class="row-item">
				<div class="label-wrap"><label for="name">货位名称: <em style="color:red">*</em></label></div>
				<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="name" id="name"></div>
			</li>
			<li class="row-item">
				<div class="label-wrap"><label for="address">备注:</label></div>
				<div class="ctn-wrap"><textarea type="text" class="ui-input" name="desc" id="desc"></textarea></div>
			</li>
		</ul>
	</form>
</div>
    
    
    
<script src="<?php echo base_url()?>/statics/js/dist/areaManage.js?ver=20140430"></script>
</body>
</html>
 