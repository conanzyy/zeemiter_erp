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

/**
 * 展开树
 * @param treeId
 */
function expand_ztree(treeId){
    var treeObj = $.fn.zTree.getZTreeObj(treeId);
    treeObj.expandAll(true);
}

/**
 * 收起树：只展开根节点下的一级节点
 * @param treeId
 */
function close_ztree(treeId){
    var treeObj = $.fn.zTree.getZTreeObj(treeId);
    var nodes = treeObj.transformToArray(treeObj.getNodes());
    var nodeLength = nodes.length;
    for (var i = 0; i < nodeLength; i++) {
        if (nodes[i].id == '0') {
            //根节点：展开
            treeObj.expandNode(nodes[i], true, true, false);
        } else {
            //非根节点：收起
            treeObj.expandNode(nodes[i], false, true, false);
        }
    }
}

/**
 * 搜索树，高亮显示并展示【模糊匹配搜索条件的节点s】
 * @param treeId
 * @param searchConditionId 文本框的id
 */
function search_ztree(treeId, searchConditionId){
    searchByFlag_ztree(treeId, searchConditionId, "");
}

/**
 * 搜索树，高亮显示并展示【模糊匹配搜索条件的节点s】
 * @param treeId
 * @param searchConditionId     搜索条件Id
 * @param flag                  需要高亮显示的节点标识
 */
function searchByFlag_ztree(treeId, searchConditionId, flag){
    //<1>.搜索条件
    var searchCondition = $('#' + searchConditionId).val();
    //<2>.得到模糊匹配搜索条件的节点数组集合
    var highlightNodes = new Array();
    if (searchCondition != "") {
        var treeObj = $.fn.zTree.getZTreeObj(treeId);
        highlightNodes = treeObj.getNodesByParamFuzzy("name", searchCondition, null);
    }
    //<3>.高亮显示并展示【指定节点s】
    highlightAndExpand_ztree(treeId, highlightNodes, flag);
}

/**
 * 高亮显示并展示【指定节点s】
 * @param treeId
 * @param highlightNodes 需要高亮显示的节点数组
 * @param flag           需要高亮显示的节点标识
 */
function highlightAndExpand_ztree(treeId, highlightNodes, flag){
    var treeObj = $.fn.zTree.getZTreeObj(treeId);
    //<1>. 先把全部节点更新为普通样式
    var treeNodes = treeObj.transformToArray(treeObj.getNodes());
    for (var i = 0; i < treeNodes.length; i++) {
        treeNodes[i].highlight = false;
        treeObj.updateNode(treeNodes[i]);
    }
    //<2>.收起树, 只展开根节点下的一级节点
    close_ztree(treeId);
    //<3>.把指定节点的样式更新为高亮显示，并展开
    if (highlightNodes != null) {
        for (var i = 0; i < highlightNodes.length; i++) {
            if (flag != null && flag != "") {
                if (highlightNodes[i].flag == flag) {
                    //高亮显示节点，并展开
                    highlightNodes[i].highlight = true;
                    treeObj.updateNode(highlightNodes[i]);
                    //高亮显示节点的父节点的父节点....直到根节点，并展示
                    var parentNode = highlightNodes[i].getParentNode();
                    var parentNodes = getParentNodes_ztree(treeId, parentNode);
                    treeObj.expandNode(parentNodes, true, false, true);
                    treeObj.expandNode(parentNode, true, false, true);
                }
            } else {
                //高亮显示节点，并展开
                highlightNodes[i].highlight = true;
                treeObj.updateNode(highlightNodes[i]);
                //高亮显示节点的父节点的父节点....直到根节点，并展示
                var parentNode = highlightNodes[i].getParentNode();
                var parentNodes = getParentNodes_ztree(treeId, parentNode);
                treeObj.expandNode(parentNodes, true, false, true);
                treeObj.expandNode(parentNode, true, false, true);
            }
        }
    }
}

/**
 * 递归得到指定节点的父节点的父节点....直到根节点
 */
function getParentNodes_ztree(treeId, node){
    if (node != null) {
        var treeObj = $.fn.zTree.getZTreeObj(treeId);
        var parentNode = node.getParentNode();
        return getParentNodes_ztree(treeId, parentNode);
    } else {
        return node;
    }
}

/**
 * 设置树节点字体样式
 */
function setFontCss_ztree(treeId, treeNode) {
    if (treeNode.id == 0) {
        //根节点
        return {color:"#333", "font-weight":"bold"};
    } else if (treeNode.isParent == false){
        //叶子节点
        return (!!treeNode.highlight) ? {color:"#ff0000", "font-weight":"bold"} : {color:"#660099", "font-weight":"normal"};
    } else {
        //父节点
        return (!!treeNode.highlight) ? {color:"#ff0000", "font-weight":"bold"} : {color:"#333", "font-weight":"normal"};
    }
}
</script>

<style>
#matchCon { width: 200px; }
.grid-wrap{position:relative;}
.ztreeDefault{position: absolute;right: 0;top: 2px;z-index: 10;background-color: #fff;border: 1px solid #D6D5D5;padding-top: 30px;width: 150px;height: 380px;overflow-y: auto;}
.padd{position: absolute;right:3px;top:7px;z-index: 20;}
.padd input{height: 20px;width: 120px;font-size: 12px;border:1px solid #eee;}
.padd button{border: 0px;width:32px;height:22px;cursor:pointer}
.ui-tab{border-left:none;border-bottom: 2px solid #f08200;  background-color: #fff;  overflow: hidden;}
.ui-tab li {border:1px solid #EBEBEB;border-top:1px solid #EBEBEB;border-bottom:1px solid #fff;;}
.ui-tab li:hover{border:1px solid #f4f4f4;}
.ui-tab li.cur:hover{border:1px solid #f08200}
.mod-bottom .filter{
    position: relative;
    overflow: hidden;
}

.mod-bottom .filter .filter-loading{
    width:60px;
    text-align:center;
    height:50px;
    overflow:hidden;
    border:1px solid #f0f0f0;
    position: absolute;
    left:50%;
    top:-60px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    background:#fff;
    margin-left:-30px;
    z-index:10
}
/*车型探索*/
.membercenter-box-bd .safe-setting-point{
    border:2px solid #ed7474;
    -webkit-box-shadow: 0 0 2px 2px rgba(182, 29, 29, 0.1);
    -moz-box-shadow: 0 0 2px 2px rgba(182, 29, 29, 0.1);
    box-shadow: 0 0 2px 2px rgba(182, 29, 29, 0.1);;
}

.safe-setting-row .sale-brand-info{
    color:#686868;
}

.safe-setting-row .sale-brand-info .brand-detail{
    margin-top:2px;
    padding:5px ;
    width:700px;
    line-height:27px;
    overflow:hidden;
}

.brand-detail span{
    margin:0px 5px 0px 5px;

}

.brand-detail span a{
    cursor:pointer;
}

.brand-detail span a:HOVER{
    text-decoration: underline;
    color: #d10000;
}

.safe-setting .sort-class{
    font-size: 14px;
    color:#00639e;
    font-weight: bold;
}
.safe-setting .brand-class{
    color:#444;
    width:200px;
    font-size: 16px;
    font-weight: normal;
}

.membercenter-box-bd .brand-border {
    border-bottom:1px solid #898989;
    background:#fff;
}



.safe-setting .brand-border h5{
    color:#787878;
}
.membercenter-box-bd .brand-py a{
    font-size:13px;
    font-weight: bold;
    color:#c81623;
    float:left;
    width:35px;
    line-height:35px;
    height:35px;
    text-align:center;
    cursor:pointer;
}
.membercenter-box-bd .brand-py a.active,.membercenter-box-bd .brand-py a.active:hover{
    color:#fff;
    background:#898989;
}
.membercenter-box-bd .brand-py a:hover{
    color:#787878;
    background:#f0f0f0;
}
.letter-postion{
    position: fixed;
    width:100%;;
    top: 0px;
}
.form-label {
     float: left;
     width: 90px;
     font-weight:bold;
     min-height: 1px;
     text-align: right;
     line-height: 30px;
 }
.form-label em {
    margin-right: 5px;
    color: #EF4521;
}
.safe-setting {
     margin: 10px 10px;
     padding: 10px;
     background:#fff;
     -webkit-box-shadow: 0 0 1px 1px rgba(136, 136, 136, 0.1);
     -moz-box-shadow: 0 0 1px 1px rgba(136, 136, 136, 0.1);
     box-shadow: 0 0 1px 1px rgba(136, 136, 136, 0.1);
 }
.safe-setting .safe-setting-row {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
}
.safe-setting .safe-setting-row:after {
    content: "\0020";
    display: block;
    visibility: hidden;
    clear: both;
}
.safe-setting .safe-setting-row:first-child {
    margin: 0;
    padding: 0;
    border-top: none;
}
.safe-setting .safe-setting-row i,
.safe-setting .safe-setting-row span {
    float: left;
}
.safe-setting .safe-setting-row a {
    float: right;
}
.safe-setting i {
    margin-right: 20px;
    font-size: 32px;
}
.safe-setting i.icon-checkmark-c {
    color: #8AB660;
}
.safe-setting i.icon-alert {
    color: #eaca17;
}
.safe-setting .safe-setting-title {
    margin-right: 40px;
    font-size: 14px;
    font-weight: bold;
    color: #666;
}


.safe-setting .safe-setting-intro {
    display: inline-block;
    width: 430px;
    color: #666;
    margin:2px 0px;
}

.safe-setting .safe-setting-scroll{
    clear: both;
    height:12px;
    margin:20px 0px 10px 0px;
    background:#eaedf4;
}

.safe-setting .safe-setting-active{
    clear: both;
    height:12px;
    margin:20px 0px 10px 0px;
    width:20%;
    background:#57a3ee;
}



.safe-setting .safe-trade-row {
    margin-top: 10px;
    padding-top: 10px;
}



.safe-setting .safe-trade-title {
    margin-right: 20px;
    margin-left:10px;
    width: 480px;
    font-weight: bold;
    color: #888;
}

.safe-setting .safe-trade-intro {
    display: inline-block;
    width: 200px;
    color: #ea544a;
    margin:2px 0px;
}
.rollback-button{
    position: fixed;
    bottom:20px;
    right:20px;
    background:#fff;
    cursor:pointer;
    border:1px solid #ddd;
    padding:5px 10px 5px 10px;
    color:#898989;
}

.rollback-button:hover{
    background:#ed7474;
    border:1px solid #ed7474;
    color:#fff;

}

.rollback-button i{
}

.rollback-button .rollbak-info{
}
.icon-arrow-up:before {
     content: "⠓";
}
.safe-setting .brand-class{
      color:#444;
      width:150px;
      font-size: 16px;
      font-weight: normal;
      text-align:center;
  }
  
  .search-input{
    box-shadow: 1px 1px 1px #f0f0f0 inset;
    border-radius: 3px;
  }
  
  .storage{
  		font-weight:bolder !important;
  		color:#c81623;
  }
  .models{
  		font-size:13px;
  		padding:4px 0px;
  		font-weight: bold;
  }
  
</style>
</head>

<body class="wapper">
<?php if($bussType == 'out') { ?>
<div class="hd cf">
	<ul class="ui-tab" id="tab">
		<li data-type="kz" class="cur">快准车服</li>
        <li data-type="other" >第三方供应商</li>
	</ul>
</div>
<?php }?>
<div class="container fix p15">
	  <div class="mod-search m0 cf">
	    <div class="fl key_search_wapper">
		    <ul class="ul-inline">
		   		<li>
		        	<input type="text" id="matchCon" class="ui-input ui-input-ph search-input" style="width:300px;" value="商品名称/商品编码/商品品牌/车型/vin码">
		        </li>
		        <li>
			        <a class="ui-btn mrb" id="search" >查询</a>
			    </li>
		 	</ul>
	      	<ul class="ul-inline ul-inline-show"  style="margin-top: 40px;display:none;">
		      	<li>
		      		<div class="ctn-wrap"><span id="category"></span></div>
		      	</li>
		        <li>
		          	<label>车型:</label>
		          	<span class="ui-combo-wrap" id="matchModel"></span>
		        </li>
		        <li>
		          	<label>年款:</label>
		          	<span class="ui-combo-wrap" id="matchYear"></span>
		        </li>
		        <li>
		          	<label>排量:</label>
		          	<span class="ui-combo-wrap" id="matchDisplacement"></span>
		        </li>
		        <li style="margin-top: 5px;">
		          	<label>备货类型:</label>
		          	<span class="ui-combo-wrap" id="stockType"></span>
		        </li>
	      	</ul>
	    </div>
	    <div class="fl model_search_wapper hide">
	     	<a class="ui-btn ui-btn-sp display_select" id = "models_name">选择车型</a>
	    </div>
	    <div class="fl">
	    	&nbsp;&nbsp;
           	<label for="storage"><input type="checkbox" id="storage"/><span class = 'text f14' style = "margin-top:5px;">&nbsp;&nbsp;有库存商品</span></label>
       	</div>
	    
	    <div class = 'fr'>
			<a class="ui-btn ui-btn-sp display_select2">车型查找 ></a>
			<a class="ui-btn key_select hide">关键字查找 ></a>
		</div>
	  </div>
	  <div class="grid-wrap">
	    <table id="grid">
	    </table>
	    <div id="page"></div>
	  </div>
</div>
<div class="wrap-lg" style="display:none;">
    <div class="mod-bottom" id="cart_main">
        <div class="membercenter-box-bd" style = "min-height:36px;">
            <div class="row brand-border" id = "letter-search">
                <a class="ui-btn ui-btn-sp mrb fl " id="return"  style="margin-left: 10px; margin-top:2px;">&lt;&nbsp;返回</a>
                <label class="form-label" style = "margin-top:2px;">按首字母查找：</label>
                <span class="form-act brand-py" >
                	<?php foreach ($list['brandSort'] as $sort){ ?>
						<a class = "brand_tag" hrandTag="<?php echo $sort['brand_sort'] ?>"><?php echo $sort['brand_sort'] ?></a>
					<?php  } ?>
                </span>
            </div>
            <div class = "rollback-button text-center hide"><i class = "icon icon-arrow-up"></i><div class = "rollbak-info">TOP</div></div>
        </div>
        <div class="membercenter-box-bd">
            <?php foreach ($list['brands'] as $item){ ?>
            <div class="safe-setting">
                <div class="safe-setting-row sort-class" id = "<?php echo $item['brand_sort'] ?>">
                    <?php echo $item['brand_sort'] ?>
                </div>
                <?php foreach ($item['brands'] as $brand){ ?>
                <div class="safe-setting-row safe-trade-row" id="<?php echo $item['brand_sort'].'_'.$brand['paixu_id'] ?>">
		          <span class="safe-trade-title brand-class text-center">
		          <div>
			          	<img src='<?php echo $brand['brand_img'] ?>' data-haszoom="700">
		          </div>
		          <?php echo $brand['brand'] ?>
		          </span>
                    <div class="safe-trade-intro sale-brand-info">
                        <?php foreach($brand['factorys'] as $factory){ ?>
                        <div><strong><?php echo $factory['factory'] ?></strong></div>
                        <div class = "brand-detail">
                            <?php foreach ($factory['carsList'] as $ci=>$car){ ?>
                            <?php if($ci>0){ ?><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span><?php  }?>
                            <?php if($car['is_strong'] == 1){ ?>
                            <span class = "brand-cars"><a href="javascript:;"  params='<?php echo $car['params'] ?>' class = "cars-search" style="color:red;font-weight:700;"><?php echo $car['cars'] ?></a></span>
                            <?php  }?>
                            <?php if($car['is_strong'] == 2){ ?>
                            <span class = "brand-cars"><a href="javascript:;"  params='<?php echo $car['params'] ?>' class = "cars-search"><?php echo $car['cars'] ?></a></span>
                            <?php  } ?>
                            <?php if(!$car['is_strong']){ ?>
                            <span class = "brand-cars"><a href="javascript:;"  params='<?php echo $car['params'] ?>' class = "cars-search"><?php echo $car['cars'] ?></a></span>
                            <?php } ?>
                           <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <?php  }?>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<script src="<?php echo base_url()?>/statics/js/dist/goodsBatchKz.js?3"></script>
<script type="text/javascript">
    $(function(){
    	 document.onkeydown = function(e) {
             var ev = document.all ? window.event : e;
             if (ev.keyCode == 13) {
                 $("#search").trigger("click");
             }
         };
		var index = true;



		$('.display_select2').on('click',function(){
			$(this).addClass('hide');
			$(".display_select").click()
		})
		
		$('.display_select').on('click',function(){
				$(".key_select").removeClass('hide');
			    SEARCH_URL = "../basedata/inventory/carSearch";
			    $(".model_search_wapper").removeClass('hide');
			    $(".key_search_wapper").hide();
				$('.wrap-lg').show();
				$('.container').hide();
		})
		$('.key_select').on('click',function(){
				$(this).addClass('hide');
				$(".display_select").html("选择车型")
				$(".display_select2").removeClass('hide');
				$(".model_search_wapper").addClass('hide');
				$(".key_search_wapper").show();
			    SEARCH_URL = "../basedata/inventory?action=kzlist";
		})
	    $('#return').on('click',function(){
		    	//$('.key_select').click();
	            $('.wrap-lg').hide();
	            $('.container').show();
	    })
	    $('.show_select').on('click',function(){
	        if(index){
	            $('.ul-inline-show').show();
	            index = false;
	        }else{
	            $('.ul-inline-show').hide();
	            index = true;
	        }
	    })
	    
        var point = $("#letter-search").offset().top;
        
        //选择字母定位
        $(".brand_tag").on("click",function(){
            var e = $(this);
            if(e.hasClass("active")) return;
            var tag =  $("#"+e.attr("hrandTag"));
            $("div.safe-setting-point").removeClass("safe-setting-point");
            $("a.active").removeClass("active");
            $("html,body").stop().animate({"scrollTop":tag.offset().top - 55},300,function(){
                tag.parent().addClass("safe-setting-point");
                e.addClass(" active");
            })
        });

        $(".rollback-button").on("click",function(){
            $("html,body").stop().animate({"scrollTop":0},300,function(){})
        })

        //单个品牌定位
        $(".brand_tag_l").on("click",function(){
            var e = $(this);
            var tag =  $("#"+e.attr("hrandTag"));
            $("div.safe-setting-point").removeClass("safe-setting-point");
            $("a.active").removeClass("active");
            $("html,body").stop().animate({"scrollTop":tag.position().top - $("#letter-search").height()},300,function(){
                tag.parent().addClass("safe-setting-point");
                e.addClass(" active");
            })
        });

        $(".rollback-button").on("click",function(){
            $("html,body").stop().animate({"scrollTop":0},300,function(){})
        })

        //字母浮动
        $(window).on("scroll",function(){
            scrollNvg();
        })

        //选择车型
        $('.cars-search').click(function (e) {
            var _this = $(this);
            if (_this.attr("params") == "") return;
            var params = $.parseJSON(_this.attr("params"));
            var width = $(window).width() * 0.8;
            var height = $(window).height() * 0.8;
            $.dialog({
                autoOpen: true,
                width: width,
                height: height,
                title: params.factory + " " + params.cars,
                lock: !0,
                min: !1,
                max: !1,
                resize: !1,
                content: "url:../basedata/inventory/brand?brand_sort="+params.brand_sort+"&brand="+params.brand+"&factory="+params.factory+"&cars="+params.cars,
            })
        });
        function scrollNvg(){
            var letter = $("#letter-search");
            if($(document).scrollTop() >= point && !letter.hasClass("letter-postion")){
                $("#letter-search").addClass("letter-postion");
                $(".rollback-button").removeClass("hide");

            }else if($(document).scrollTop() < point){
                $(".rollback-button").addClass("hide");
                $(".letter-postion").removeClass("letter-postion");
            }
        }

        scrollNvg();
    })
</script>
</body>
</html>