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
.print-settings-wrap{padding: 15px}
.print-settings-wrap a{text-decoration: underline;}
.print-settings-wrap .num-input{width: 50px;text-align: right;}
.print-settings-wrap .row-btns{margin-top: 30px;}
.print-settings-wrap .taoda-margin{margin-bottom: 5px;}
.print-settings-wrap .settings-ctn{margin-top: 25px;font-size: 14px;}
.print-settings-wrap .settings-ctn .print-tips{color: #dd4e4e;}
.print-settings-wrap .settings-ctn .print-tips a{color: #2383c0;}
.print-settings-wrap .mod-form-rows{margin-top: 20px;}
.taoda-settings .mod-form-rows .label-wrap{width: 110px;}
.taoda-settings .mod-form-rows a{color: #999;margin-left: 10px;}
.taoda-settings .mod-form-rows a:hover{color: #2383c0;}
#printTemp{margin-right: 10px;}
#setDefaultTemp{white-space: nowrap;margin-left: 0}
.template-list .list-item{font-size: 12px;}
</style>

</head>
<body class="ui-dialog-body">
<div class="print-settings-wrap">
  <ul class="ui-tab" id="printSelect" style = "display:none;">
      <li data-type="pdf" class="cur">PDF打印</li>
  </ul>
  <div class="settings-ctn" id="printSettings">
      <div class="item">
        <p class="print-tips">为了保证您的正常打印，请先下载安装<a href="http://dl.pconline.com.cn/html_2/1/81/id=1322&pn=0&linkPage=1.html" target="_blank">Adobe PDF阅读器</a></p>
        <ul class="mod-form-rows" id="pdfSettings">
          <li class="row-item">
            <div class="label-wrap">
              <label>打印纸型:</label>
            </div>
            <div class="ctn-wrap">
              <label class="radio-wrap"><input type="radio" name="paperType"  value="" checked="checked"/><span class='text' style = 'margin-top:5px;'>&nbsp;&nbsp;A4</span></label>
            </div>
          </li>
        </ul>
      </div>
  </div>
</div>
<form method="post" id="downloadForm" style="display:none;"></form>
<script>
 (function($){
    var billType = frameElement.api.data.billType;
    var printMethod = $.cookie('printMethod') || 'pdf';
    var taodaData = frameElement.api.data.taodaData;
    var pdfData = frameElement.api.data.pdfData;
    var pdfUrl = frameElement.api.data.pdfUrl;
    var defaultSelectValue = frameElement.api.data.opt.defaultSelectValue;//必须为数组形式：[key, value]
    init();
    
    function init(){
      //初始化设置
      initPrintMethod(printMethod);
      initSettings();

      //绑定事件
      $('#printSelect li').on('click', function(){
        if($(this).hasClass('cur')){return ;}
        printMethod = $(this).data('type');
        initPrintMethod(printMethod);
      });
    }


    function doPrint(){
      $.cookie('printMethod',printMethod);
      if (printMethod == 'pdf') {
         pdfPrint();
      } else if(printMethod == 'taoda'){
        tadaoPrint();
      }
    }
    window.doPrint = doPrint;


    function initPrintMethod(Method){
      var obj = $('#printSelect li').filter('[data-type='+printMethod+']');
      obj.addClass('cur').siblings('li').removeClass('cur');
      var idx = obj.index($('#printSelect li'));
      $('#printSettings .item').eq(idx).show().siblings().hide();
    }

    function initSettings(){
      $('#pdfStatX').val($.cookie('pdfMarginLeft') || 50);
      $('#entrysPerNote').val($.cookie('entrysPerNote') || 0);
      $('#taodaStartX').val($.cookie('taodaStartX') || 0);
      $('#taodaStartY').val($.cookie('taodaStartY') || 0);
      if ($.cookie('isEmptyLinePrint') != null) {
        var checked = $.cookie('isEmptyLinePrint') == 1 ? true : false;
        $('#isEmptyLinePrint').attr('checked', checked);
      }
      if ($.cookie('printFirstLayer') != null) {
        var checked = $.cookie('printFirstLayer') == 1 ? true : false;
        $('#printFirstLayer').attr('checked', checked);
      }
    }
    
    function getBillType(TypeId) {
    	switch(TypeId) {
    	case 0:
    		return 'Voucher';
    	case 10101:
    		return 'PUR';
    	case 10201:
    		return 'SAL';
    	case 10301:
    		return 'SCM_INV_PUR';
    	case 10303:
    		return 'SCM_INV_SALE';
    	default:
    		return '0';
    	}
    }


    function tadaoPrint(){
     
    }

    function pdfPrint(){
      pdfData.marginLeft = $('#pdfStatX').val(); //设置左边距
      $.cookie('pdfMarginLeft', pdfData.marginLeft, {expires: 365});
      Business.getFile(pdfUrl, pdfData, true, false);
      frameElement.api.close();
    }
   
    //打印运单处理
    if(!pdfUrl){
  	  $('#printSelect li:eq(1)').trigger('click');
  	  $('#printSelect').hide();
  	  $('#setDefaultTemp').hide();
    }
 })(jQuery);
</script>
</body>
</html>


 