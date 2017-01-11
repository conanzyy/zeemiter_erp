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

<link href="<?php echo base_url()?>statics/css/<?php echo sys_skin()?>/bills.css?ver=20150522" rel="stylesheet" type="text/css">
<style>
#barCodeInsert{margin-left: 10px;font-weight: 100;font-size: 12px;color: #fff;background-color: #B1B1B1;padding: 0 5px;border-radius: 2px;line-height: 19px;height: 20px;display: inline-block;}
#barCodeInsert.active{background-color: #23B317;}
#imp{margin-left: 10px;font-weight: 100;font-size: 12px;color: #fff;background-color: #B1B1B1;padding: 0 5px;border-radius: 2px;line-height: 19px;height: 20px;display: inline-block;}
#mouid{font-weight: 50;font-size: 10px;padding: 0 5px;border-radius: 2px;line-height: 19px;height: 20px;display: inline-block;text-decoration:none}
.leibie{width: 65%;float: right;text-align: left;border: 1px solid #ccc;margin-right: 15px;position: absolute;top: 30px;right: 0;z-index: 10000;display: none;}
.show{ display: block; }
.leibie li{padding-left: 3%;background-color: #fff;}
.leibie li.on{ background-color: #d2d2d2; }
.leibie li:hover{background-color: #eee;}
.trigger_c{ cursor: pointer; }
</style>
</head>
<body>
<input type="file" id="fileupload" name="fileupload" style="FILTER: alpha(opacity=0); moz-opacity: 0; opacity: 0;display:none;" />
<div class="wrapper">
  <span id="config" class="ui-icon hide ui-state-default ui-icon-config"></span>
  <div class="mod-toolbar-top mr0 cf dn" id="toolTop"></div>
  <div class="bills cf">
    <div class="con-header">
      <dl class="cf">
        <dd class="mr20 hide">
          <label>供应商:</label>
          <span class="ui-combo-wrap" id="customer">
          <input type="text" name="" class="input-txt" autocomplete="off" value="" data-ref="date">
          <i class="ui-icon-ellipsis"></i></span>
        </dd>
        <dd class="mr20 tc" style="position: relative;">
          <label>单据类型:</label>
          <!-- <span class="ui-combo-wrap" style="padding:0;">
            <input type="text" class="input-txt" id='leibie' leibie='30-Cxx-01' readonly='readonly'>
            <i class="trigger trigger_c"></i>
          </span>
          <ul class="leibie ">
            <li lbId="30-Cxx-01" class="on">普通调入申请</li>
            <li lbId="30-Cxx-02">厂家直发调入申请</li>
            <li lbId="30-Cxx-03">补单调入申请</li>
            <li lbId="30-Cxx-07">当日件调入申请</li>
          </ul> -->
          
          <span class="ui-combo-wrap" id="orderType">
              <input type="text" class="input-txt" autocomplete="off">
          <i class="trigger"></i></span>
          
        </dd>
        <dd class="mr20 tc hide">
          <label>单据日期:</label>
          <input type="text" id="date" class="ui-input ui-datepicker-input" value="">
        </dd>
        <dd id="identifier" class="pct20 tc" style="display: none;">
          <label>单据编号:</label>
          <span id="number"><?php echo str_no('CGO')?></span></dd>
      </dl>
      <hr class = "hrcls">
      
      <div class="cf  button-row">
        <div class="fl hide">
          <span class = "min-btn ui-icon-plus btn-success"><span class="fa fa-plus" title="新增行"></span>新增行&nbsp;</span>
        </div>
        <div class="fr">
        	<span class="min-btn ui-icon-cart btn-info linkToCheck"><span class="fa fa-search" title="历史采购订单"></span>历史采购订单</span>
        </div>
      </div>
    </div>
    <div class="grid-wrap">
      <table id="grid">
      </table>
      <div id="page"></div>
    </div>
    <div class="con-footer cf">
      <div class="mb10">
      	<textarea type="text" id="note" class="ui-input ui-input-ph">暂无备注信息</textarea>
      </div>
      <ul id="amountArea" class="cf">
        <li style="display: none;">
          <label>优惠率:</label>
          <input type="text" id="discountRate" class="ui-input" data-ref="deduction">%
        </li>
        <li style="display: none;">
          <label>优惠金额:</label>
          <input type="text" id="deduction" class="ui-input" data-ref="payment">
        </li>
        <li style="display: none;">
          <label>优惠后金额:</label>
          <input type="text" id="discount" class="ui-input ui-input-dis" data-ref="discountRate" disabled>
        </li>
        <li style="display: none;">
          <label id="paymentTxt">本次付款:</label>
          <input type="text" id="payment" class="ui-input">&emsp;
        </li>
        <li id="accountWrap" class="dn" style = "dispaly:none;">
          <label>结算账户:</label>
          <span class="ui-combo-wrap" id="account" style="padding:0;">
          <input type="text" class="input-txt" autocomplete="off">
          <i class="trigger"></i></span><a id="accountInfo" class="ui-icon ui-icon-folder-open" style="display:none;"></a>
        </li>
        <li style="display: none;">
          <label>本次欠款:</label>
          <input type="text" id="arrears" class="ui-input ui-input-dis" disabled>
        </li>
        <li class="dn">
          <label>累计欠款:</label>
          <input type="text" id="totalArrears" class="ui-input ui-input-dis" disabled>
        </li>
      </ul>
      <ul class="c999 cf">
        <li>
          <label>制单人:</label>
          <span id="userName"></span>
        </li>
        <li>
          <label>审核人:</label>
          <span id="checkName"></span>
        </li>
        <li>
          <label>最后修改时间:</label>
          <span id="modifyTime"></span>
        </li>
      </ul>
    </div>
    <div class="cf" id="bottomField">
    	<div class="fr" id="toolBottom"></div>
    </div>
    <div id="mark"></div>
  </div>
  
  <div id="initCombo" class="dn">
    <input type="text" class="textbox goodsAuto goodsText" name="goods" autocomplete="off">
    <input type="text" class="textbox storageAuto" name="storage" autocomplete="off">
    <input type="text" class="textbox unitAuto" name="unit" autocomplete="off">
    <input type="text" class="textbox batchAuto" name="batch" autocomplete="off">
    <input type="text" class="textbox dateAuto" name="date" autocomplete="off">
    <input type="text" class="textbox areaAuto" name="area" autocomplete="off">
  </div>
  <div id="storageBox" class="shadow target_box dn">
  </div>
</div>
<script src="<?php echo base_url()?>statics/js/dist/purchaseOrders.js?ver=20161012"></script>
<script src="<?php echo base_url()?>statics/js/common/plugins/fileupload/js/vendor/jquery.ui.widget.js"></script>
<script src="<?php echo base_url()?>statics/js/common/plugins/fileupload/js/jquery.fileupload.js"></script>


<script>

var url='<?php echo site_url()?>/scm/invOd?action=uploadexcelfile';
var load;
$(function () {
	$('#mouid').click(function(){
		window.location.href="<?php echo site_url()?>/scm/invOd?action=download";
						
	})
	$('#imp').click(function(){
			//	
		var ie = !-[1,]; 
		if(ie){
			   jQuery('input:file').trigger('click').trigger('change');
		}else{
			   jQuery('input:file').trigger('click');
		}
	});	
    $('#fileupload').fileupload({
        url: url,
        dataType: 'json',
        done: function (e, data) {
			
			if(data.result['error']){
				alert('文件上传错误');
			}else{
				var rst = data.result;
				//这里跳转
				
				load = $.dialog.tips("正在导入，请稍候...", 10e3, "loading.gif", !0);
				loadexceldata(data.result.file_name);
				//alert(JSON.stringify(data));
			}
        },
        progressall: function (e, data) {
			//console.log('test2');
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
});


var originalData = {
		id: -1,
		status: "add",
		customer: 0,
		transType: 150501,
					entries: [{
						id: "1"
					}, {
						id: "2"
					}, {
						id: "3"
					}, {
						id: "4"
					}, {
						id: "5"
					}, {
						id: "6"
					}, {
						id: "7"
					}, {
						id: "8"
					}],
					description: "",
					totalQty: 0,
					totalDiscount: 0,
					totalAmount: 0,
					totalTax: 0,
					totalTaxAmount: 0,
					disRate: 0,
					disAmount: 0,
					amount: "0.00",
					rpAmount: "0.00",
					arrears: "0.00",
					accId: 0
				};
				
				
	function loadexceldata(exefile){
		hasLoaded =0;
		Public.ajaxGet(CONFIG.SITE_URL + "/scm/invOd?action=getExecel&filename="+exefile, {}, function(b) {


			if(b.status == 250){
				$.dialog({
					width:400,
					height:100,
					title: "提示信息",
					content:b.msg,
					cancel:true,
				});
				load.close();
			}
				
			
			
			var text = "共导入物料"+b.data.IAllNumber+"条，成功<font color=green>"+b.data.ISuccessNumber+"</font>条，失败<font color=red>"+b.data.IErrorNumber+"</font>条<br/>";
			if(b.data.filename){
				text = "共导入物料"+b.data.IAllNumber+"条，成功<font color=green>"+b.data.ISuccessNumber+"</font>条，失败<font color=red>"+b.data.IErrorNumber+"</font>条(<a href='"+CONFIG.SITE_URL + "/scm/invOd?action=downError&filename="+b.data.filename+"' target=blank  id = 'error_down'>点击下载失败列表)</a><br/>";
			}

			$.dialog({
				width:400,
				height:100,
				title: "导入完成",
				content:text,
				cancel:true,
			});

			200 === b.status ?  ( originalData.entries  = b.data.rows,
			originalData.totalQty  = b.data.totalQty,
			originalData.totalAmount  = b.data.totalAmount,
			filldata(originalData), hasLoaded = !0) : 
		    ( parent.Public.tips({ type: 1, content: b.msg }) );
		
		});
	}
	function filldata(a){
		$("#grid").clearGridData();
		$("#grid").jqGrid("setGridParam", {
			data: a.entries,
			userData: {
				qty: a.totalQty,
				deduction: a.totalDiscount,
				amount: a.totalAmount,
				tax: a.totalTax,
				taxAmount: a.totalTaxAmount
			}
		}).trigger("reloadGrid")
		load.close();
		//var list = $("#grid").getDataIDs;
		var list = a.entries;
		var defaultStorage = defaultPage.SYSTEM.defaultStorage;

		
		for(var i =  0 ;i < list.length ; i++){

			//设置默认仓库
			if(defaultStorage){
				i.locationId = i.locationId == null || i.locationId == '' || i.locationId == 0 ? defaultStorage.id : i.locationId;
				i.locationName = i.locationName == null || i.locationName == '' ? defaultStorage.name : i.locationName;
			}
			
			$("#"+list[i]['id']).data("goodsInfo",list[i]).data("storageInfo", {
				id: defaultStorage.id,
				name: defaultStorage.name
			}).data("unitInfo", {
				unitId: list[i].unitId,
				name: list[i].mainUnit
			})
		}
	}
</script>
</body>
</html>


