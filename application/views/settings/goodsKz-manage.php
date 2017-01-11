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

<link rel="stylesheet" href="<?php echo base_url()?>statics/js/common/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?php echo base_url()?>statics/js/common/plugins/validator/jquery.validator.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>statics/js/common/plugins/validator/local/zh_CN.js"></script>
<style>
.ui-combo-wrap{position:static;}
.mod-form-rows .label-wrap{font-size:12px;}
.manage-wrap .ui-input{width: 198px;}
.base-form{*zoom: 1;margin:0 -10px;}
.base-form:after{content: '.';display: block;clear: both;height: 0;overflow: hidden;}
.base-form .row-item{float: left;width: 270px;;height: 31px;margin: 0 10px;overflow: visible;padding-bottom:15px;}
.manage-wrapper{margin:0 auto;width:270px;padding: 15px;}
.manage-wrap textarea.ui-input{width: 100%;height: 80px;*vertical-align:auto;overflow: hidden;box-sizing: border-box;}
#manage-form{overflow: hidden;}
.contacters{margin-bottom: 10px;}
.contacters h3{margin-bottom: 10px;font-weight: normal;}
.ui-jqgrid-bdiv .ui-state-highlight { background: none; }
.operating .ui-icon{ margin:0; }
.ui-icon-plus { background-position:-80px 0; }
.ui-icon-trash { background-position:-64px 0; }
.mod-form-rows .ctn-wrap{overflow: visible;;}
.mod-form-rows .pb0{margin-bottom:0;}
.jdInfo{display:none;margin-top: 5px;}
.jdInfo h3{position: absolute;left: 50%;margin-left: -62px;top: -11px;background-color: #fff;padding: 0 10px;color: #ccc;}
.jdInfo a{cursor: help;border-bottom:dotted #555 1px;}
.hasJDStorage .jdInfo{display:block;position:relative;z-index:1;padding-top: 15px;margin: 10px 0;border-top: solid 1px #ccc;}
.hasJDStorage .manage-wrapper{width: 290px;}
/*.hasJDStorage .manage-wrap textarea.ui-input{height: 34px;width:958px;}*/
.base-form .row-item{width: 276px;padding-bottom:9px;}
.mod-form-rows .label-wrap{width:90px;}
.manage-wrap .ui-input{width: 155px;}
.ui-tab{border-left:none;border-bottom: 2px solid #f08200; position: absolute;   background-color: #fff;  top: 0;  left: 0;  width: 100%;}
.ui-tab li {border-top:none;border-bottom:none;border-color:#EBEBEB;}
.ui-tab li {border:1px solid #EBEBEB;border-top:1px solid #EBEBEB;border-bottom:1px solid #fff;;}
.ui-tab li:hover{border:1px solid #f4f4f4;}
.ui-tab li.cur:hover{border:1px solid #f08200}
.prop-wrap{line-height: 26px;margin-bottom: 20px;}
.prop-wrap.on{background: #f4f4f4;}
.qur-wrap{line-height: 42px;margin-bottom: 20px;}
.qur-wrap.on{background: #f4f4f4;}
.prop-wrap input{vertical-align: middle;margin-right: 3px;}
.prop-wrap label {margin-right: 8px;}
.prop-wrap .content{padding: 5px 0 12px 15px;height: 35px;overflow-y: auto;}
#createSku{color:#948D8D; font-size: 20px;border: dotted 2px #948D8D;padding: 0 15px;cursor: pointer;line-height: 15px;display: inline-block;vertical-align: middle;}
#createSku:hover{color:#1680F3;border-color:#1680F3;}
.serField{padding: 10px 0;}
#isSerNum{vertical-align: middle;margin-left: 17px;}
#isWarranty{vertical-align: middle;margin-left: 17px;}
.isWarrantyData{float: left; margin-left: 17px;}
.fl{float: left;}
.cle{clear: both;}
.salePrice{
	color:red;
	font-size: 16px
}
</style>
</head>
<body>
<div id="tabContent">
    <div class="manage-wrapper">
        <div id="manage-wrap" class="manage-wrap">
            <form id="manage-form" action="">
                <ul class="mod-form-rows base-form cf" id="base-form">
                    <li class="row-item">
                        <div class="label-wrap"><label for="salePrice">指导价：</label></div>
                        <div class="ctn-wrap"><strong class = 'salePrice' id = "salePrice"></strong></div>
                    </li>
                    <?php foreach ( $priceList as $key => $value ){ ?>
                    	<li class=row-item>
                    		<div class=label-wrap><label for=salePrice><?php echo $value['name']?>售价</label></div>
							<div class=ctn-wrap>
								<input type=text class="ui-input money lsj" name=salePrice  value='' id='lsj_<?php echo $value['id']?>' kh_id='<?php echo $value['id']?>'>
							</div>
						</li>
                    <?php } ?>
                </ul>
            </form>
        </div>
    </div>
</div>
<script src="<?php echo base_url()?>statics/js/dist/goodsKzManage.js?ver=20150522"></script>

</body>
</html>

 