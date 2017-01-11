<style>
    .cars-list-block-a {
        float: left;
        padding: 8px;
        margin: 5px 5px 0px 0px;
        width: calc(25% - 25px);
        color: #787878;
        cursor: pointer;
    }
    
    .cars-list-block-a:hover {
    	background: #ed7474;
    	color: #fff;
	}
    
    .safe-setting {
        padding: 10px;
        background:#fff;
        -webkit-box-shadow: 0  1px 3px rgba(000, 000, 000, 0.1);
        -moz-box-shadow: 0  1px 3px rgba(000, 000, 000, 0.1);
    }
</style>
<div class="cars-wapper">
    <h4 class = "cars-title" style="font-size: 16px;"><?php echo $pagedata['models'] ?> &nbsp;<?php if($pagedata['model_year']){ echo $pagedata['model_year']; ?>款<?php  }?></h4>
</div>
<div class = "cars-wapper" style="overflow:hidden;min-height: 200px">
    <?php foreach($cars as $item){ ?>
    <a class="safe-setting cars-list-block-a" href = "javascript:;" params = "<?php echo $item['compress_id'] ?>">
        <span style="text-align: center;"><?php echo $item['brand'] ?>&nbsp;<?php echo $item['models'] ?>丨<?php echo $item['model_year'] ?>款 | <?php echo $item['displacement'] ?>&nbsp;<?php if($item['suction_type'] == '机械增压' || $item['suction_type'] == '涡轮增压' || $item['suction_type'] == '机械+涡轮增压' || $item['suction_type'] == '双涡轮增压'){ ?>T <?php } ?> </span>
    </a>
    <?php } ?>
</div>
<script>
    $(function(){
        $('.cars-list-block-a').click(function () {
            var compress_id = $(this).attr("params");
            parent.$("#grid").jqGrid("setGridParam", {
                url: parent.SEARCH_URL,
                datatype: "json",
                postData:{compress_id:compress_id},
                page: 1
            }).trigger("reloadGrid");
            parent.$('.wrap-lg').hide();
            parent.$('.container').show();
            parent.$('.ui_close').click();
        })
    })
</script>