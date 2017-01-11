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

<script src="<?php echo base_url()?>statics/js/common/plugins/jquery.spinbox.js">
</script>

<style>
#para-wrapper{font-size:14px; }
#para-wrapper .para-item{margin-bottom:30px;}
#para-wrapper .para-item h3{font-size:14px;font-weight:bold;margin-bottom:10px;}

.mod-form-rows .label-wrap { width:140px; }
.para-item .ui-input{width:220px;font-size:14px;}

.subject-para .ui-input{width:40px;}

.code-length .ui-spinbox-wrap{margin-right:0;}

.books-para input{margin-top:-3px;}

#currency{width: 68px;}
.ui-droplist-wrap .list-item {font-size:14px;}
</style>
</head>
<body>
<div class="wrapper">
  <div id="para-wrapper">
    <div class="para-item">
      <h3>公司信息</h3>
      <ul class="mod-form-rows" id="establish-form">
        <li class="row-item">
          <div class="label-wrap">
            <label for="companyName">公司名称：</label>
          </div>
          <div class="ctn-wrap">
            <input type="text" name="companyName" class="ui-input" id="companyName" />
          </div>
        </li>
        <li class="row-item">
          <div class="label-wrap">
            <label for="companyBank">开户行：</label>
          </div>
          <div class="ctn-wrap">
            <input type="text" name="companyBank" class="ui-input" id="companyBank" />
          </div>
        </li>
        <li class="row-item">
          <div class="label-wrap">
            <label for="companyAccount">账号：</label>
          </div>
          <div class="ctn-wrap">
            <input type="text" name="companyAccount" class="ui-input" id="companyAccount" />
          </div>
        </li>
        <li class="row-item">
          <div class="label-wrap">
            <label for="companyAddress">公司地址：</label>
          </div>
          <div class="ctn-wrap">
            <input type="text" name="companyAddress" class="ui-input" id="companyAddress" />
          </div>
        </li>
        <li class="row-item">
          <div class="label-wrap">
            <label for="companyCode">统一社会信息代码：</label>
          </div>
          <div class="ctn-wrap">
            <input type="text" name="companyCode" class="ui-input" id="companyCode" />
          </div>
        </li>
        <li class="row-item">
          <div class="label-wrap">
            <label for="companyTel">公司电话：</label>
          </div>
          <div class="ctn-wrap">
            <input type="text" name="companyTel" class="ui-input" id="companyTel" />
          </div>
        </li>
      </ul>
      
      <h3>配置信息</h3>
      <ul class="mod-form-rows">
        <li class="row-item" style="display:none">
          <div class="label-wrap">
            <label for="companyFax">公司传真：</label>
          </div>
          <div class="ctn-wrap">
            <input type="text" name="companyFax" class="ui-input" id="companyFax" />
          </div>
        </li>
        <li class="row-item" style="display:none">
          <div class="label-wrap">
            <label for="postcode">公司邮编：</label>
          </div>
          <div class="ctn-wrap">
            <input type="text" name="postcode" class="ui-input" id="postcode" />
          </div>
        </li>
        <li class="row-item" style="display:none">
          <div class="label-wrap">
            <label for="startDate">启用时间：</label>
          </div>
          <div class="ctn-wrap">
            <input type="text" name="startDate" class="ui-input ui-input-dis" id="startDate" disabled="disabled" />
          </div>
        </li>
        <li class="row-item">
          <div class="label-wrap">
            <label for="currency">本位币：</label>
          </div>
          <div class="ctn-wrap">
            <input type="text" class="ui-input ui-input-dis" id="currency" disabled="disabled"/>
            <!--<select id="currency-sel" name="currency-sel">
							<option value="RMB">RMB</option>
						</select>--> 
          </div>
        </li>
        
        <li class="row-item" style="display:none;">
          <div class="label-wrap" >
            <label>数量小数位：</label>
          </div>
          <div class="ctn-wrap">
            <input type="text" name="qtyPlaces" id="qtyPlaces" class="ui-input" />
          </div>
        </li>
        <li class="row-item" style="display:none;">
          <div class="label-wrap">
            <label>单价小数位：</label>
          </div>
          <div class="ctn-wrap">
            <input type="text" name="pricePlaces" id="pricePlaces" class="ui-input" />
          </div>
        </li>
<!--        <li class="row-item">
          <div class="label-wrap">
            <label>金额小数位：</label>
          </div>
          <div class="ctn-wrap">
            <input type="text" name="amountPlaces" id="amountPlaces" class="ui-input" />
          </div>
        </li>-->
        <li class="row-item">
          <div class="label-wrap">
            <label for="valMethods">存货计价方法：</label>
          </div>
          <div class="ctn-wrap">
          	<span id="valMethods"></span>
          </div>
        </li>
        <li class="row-item hide">
          <div class="label-wrap">
            <label for="requiredCheckStore">是否检查负库存：</label>
          </div>
          <div class="ctn-wrap">
          	<label>
          		<input type="radio" class="requiredCheckStore" value="1" name ="requiredCheckStore"><span class='text'>&nbsp;是</span>&nbsp;&nbsp;&nbsp;&nbsp;
          	</label>
          	<label>
          		<input type="radio" class="requiredCheckStore" value="0" name ="requiredCheckStore"><span class='text'>&nbsp;否</span>
          	</label>
          </div>
        </li>
        <li class="row-item">
          <div class="label-wrap">
            <label>结算价加成：</label>
          </div>
          <div class="ctn-wrap">
            <input type="text" name="settlePlaces" id="settlePlaces" class="ui-input" /><span>%</span>
          </div>
        </li>
        <li class="row-item">
          <div class="label-wrap">
            <label for="showLowPrice">最低销售价：</label>
          </div>
          <div class="ctn-wrap">
          	<label>
          		<input type="radio" class="showLowPrice" value="1" name ="showLowPrice"><span class='text'>&nbsp;显示</span>&nbsp;&nbsp;&nbsp;&nbsp;
          	</label>
          	<label>
          		<input type="radio" class="showLowPrice" value="0" name ="showLowPrice"><span class='text'>&nbsp;不显示</span>
          	</label>
          </div>
        </li>
      </ul>
    </div>
    
    <div class="para-item dn">
      <h3>功能参数</h3>
      <ul class="mod-form-rows" id="establish-form2">
        
      </ul>
    </div>
    
    <div class="btn-wrap"> <a id="save" class="ui-btn ui-btn-sp">保存</a> </div>
  </div>
</div>
<script src="<?php echo base_url()?>/statics/js/dist/parameter.js?ver=20140430"></script>
</body>
</html>
