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
				<div class="label-wrap"><label for="number">仓库编号: <em style="color:red">*</em></label></div>
				<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="number" id="number"></div>
				<input type="hidden" value="<?php echo $locationNo ?>" id="preNum" />
			</li>
			<li class="row-item">
				<div class="label-wrap"><label for="name">仓库名称: <em style="color:red">*</em></label></div>
				<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="name" id="name"></div>
			</li>
			<li class="row-item">
				<div class="label-wrap"><label for="address">仓库地址:</label></div>
				<div class="ctn-wrap"><textarea type="text" class="ui-input" name="address" id="address"></textarea></div>
			</li>
			<li>
				<div class="label-wrap"><label for="name">默认仓库:</label></div>
				<div class="ctn-wrap">
					<label>
						<input type = "radio" name="isDefault" value="1"><span class='text'> 是</span>
					</label>
					&nbsp;&nbsp;&nbsp;
					<label>
						<input type = "radio" name="isDefault" value="0"><span class='text'> 否</span>
					</label>	
				</div>
			</li>
			<li class="row-item" style = "display:none">
				<div class="label-wrap"><label for="name">仓库区域: <em style="color:red">*</em></label></div>
				<div class="ctn-wrap click_monitor" id="click_monitor">
					<div class="add_input fl"></div>
					<div class="jia l_label">添加区域</div>
				</div>

			</li>
		</ul>
	</form>
</div>
    
    
    
<script src="<?php echo base_url()?>/statics/js/dist/storageManage.js?ver=20140430"></script>
</body>
<!-- <script type="text/javascript">	
	var inputData = [];
	var n = 0;
	
	$('.jia').on('click',function(){
		var inputHtml = $('<input style=width:88px;float:left;margin-right:10px; class="ui-input end_input" type=text >');

		inputHtml.on('blur',function(){
			var inputVal =$(this).val();
			if(inputVal == ''){
				$('.add_input').empty();
			}else{
				if(inputData.length>0){
					var isHave = xunhuan(inputVal);
					if(!isHave){
						inputData.push(inputVal);console.log(inputData)
						n++;
						$('.add_input').after('<div class="l_label" id="l_label_'+n+'"><em style="position: absolute;display: inline-block;width: 20px;height: 20px;background:url(<?php echo base_url()?>/statics/css/img/cha.png) no-repeat center center;cursor:pointer;top: -3px;right: -5px;" index='+n+'></em>'+inputVal+'</div>');
					}
				}else{
					inputData.push(inputVal);
					$('.add_input').after('<div class="l_label" id="l_label_'+n+'"><em style="position: absolute;display: inline-block;width: 20px;height: 20px;background:url(<?php echo base_url()?>/statics/css/img/cha.png) no-repeat center center;cursor:pointer;top: -3px;right: -5px;" index='+n+'></em>'+inputVal+'</div>');
				}
				$('.add_input').empty();

			}
		})
		$('.add_input').prepend(inputHtml);

	})
	$('.click_monitor').delegate('em','click',function(){
		var idx = $(this).attr('index');
		inputData.splice(idx,1);
		$('#l_label_'+idx).remove();
	})
	function xunhuan(inputVal){
		for (var i=0;i<inputData.length;i++){
			if(inputVal == inputData[i]){
				alert('已有该区域');
				return 'false';
				break;
			}
		}
	}
</script> -->
</html>
 