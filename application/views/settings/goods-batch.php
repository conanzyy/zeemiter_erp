<?php $this->load->view('header');?>

<script type="text/javascript">
var DOMAIN = document.domain;
var WDURL = "";
var SCHEME= "<?php echo sys_skin()?>";
try{
	document.domain = '<?php echo base_url()?>';
}catch(e){
}

var $bussType = '<?php echo $bussType?>';

</script>

<style>
#matchCon { width: 200px; }
.grid-wrap{position:relative;}
.ztreeDefault{position: absolute;right: 0;top: 0;background-color: #fff;border: 1px solid #D6D5D5;width: 140px;height: 420px;overflow-y: auto;}
.ui-tab{border-left:none;border-bottom: 2px solid #f08200;  background-color: #fff;  overflow: hidden;}
.ui-tab li {border:1px solid #EBEBEB;border-top:1px solid #EBEBEB;border-bottom:1px solid #fff;;}
.ui-tab li:hover{border:1px solid #f4f4f4;}
.ui-tab li.cur:hover{border:1px solid #f08200}
</style>
</head>

<body class="bgwh">
<?php if($bussType == 'out') { ?>
<div class="hd cf">
	<ul class="ui-tab" id="tab">
		<li data-type="kz" >快准车服</li>
        <li data-type="other" class="cur" >第三方供应商</li>
	</ul>
</div>
<?php }?>
<div class="container fix p15">
	  <div class="mod-search m0 cf">
	    <div class="fl">
	      <ul class="ul-inline">
	        <li>
	          <input type="text" id="matchCon" class="ui-input ui-input-ph" value="请输入商品编号或名称或型号">
	        </li>
	        <li><a class="ui-btn mrb" id="search">查询</a><!-- <a class="ui-btn" id="refresh">刷新</a> --></li>
	      </ul>
	    </div>
	  </div>
	  <div class="grid-wrap">
	    <table id="grid">
	    </table>
	    <div id="page"></div>
	  </div>
</div>
<script src="<?php echo base_url()?>/statics/js/dist/goodsBatch.js?2"></script>
</body>
</html>