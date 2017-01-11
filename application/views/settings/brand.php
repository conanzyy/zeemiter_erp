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

	#confirm_dialog{
		margin:10px;
	}
	
	select{
		height: 30px;
		padding: 2px;
	    border: 1px solid #cccccc;
	    background-color: white;
	}

</style>
<div id="confirm_dialog">
    <div class="order-confirm">
        <div class="form-row">
            <ul>
                <li class="form-row">
                    <label for="" class="form-label">筛选条件：</label>
                    <span class="form-act">
                  	<select id="models" class = "search" children="model_year">
                  		<option value = "">------选择车型------</option>
                  	</select>
                  	<select id="model_year" class = "search" children="displacement" parent="models" >
                  		<option value = "">------选择年款------</option>
                  	</select>
                  	<select id="displacement" class = "search" parent="model_year" >
                  		<option value = "">------选择排量------</option>
                  	</select>
                </span>
                </li>
            </ul>
        </div>
        <div class="form-footer" style = "border-top:1px solid #f0f0f0">
            <div class = "row" id = "carList">

            </div>
        </div>
    </div>
</div>
<script>
    /*
     $(function(){
     var params = new Object();
     var filter = $.parseJSON('<{$pfilter}>');
     params['models'] = $.parseJSON('<{$cars.models_json}>');
     params['model_year'] = $.parseJSON('<{$cars.modelyear_json}>');
     params['displacement'] = $.parseJSON('<{$cars.displacement_json}>');


     var first = $(".search:first");
     for(var i = 0;i<params[first.prop('id')].length;i++){

     first.append("<option value = '"+params[first.prop('id')][i][first.prop('id')]+"'>"+params[first.prop('id')][i][first.prop('id')]+"</option>")
     }

     $(".search").on("change",function(){
     var e = $(this);
     var c = $("#"+e.attr("children"))
     initselect(c);
     var list = params[e.attr("children")];
     for(var i = 0;list != null && i<list.length;i++){
     if(e.val() == list[i][c.attr("parent")]){
     c.append("<option value = '"+list[i][e.attr("children")]+"'>"+list[i][e.attr("children")]+"</option>")
     }
     }
     searchCarList();
     })

     function initselect(c){
     var init = c.find(":first");
     c.children("option").remove();
     c.append(init);
     if(c.attr("children") == null || c.attr("children") == "") return;
     initselect($("#"+c.attr("children")));
     }

     function searchCarList(){
     var searchList = $(".search");
     searchList.each(function(i,e){
     _this = $(e);
     if(_this.val() == ""){
     delete filter[_this.prop("id")];
     }else{
     filter[_this.prop("id")] = _this.val();
     }
     })
     $("#carList").load('<{url action="topc_ctl_brandnew@getCarList"}>',filter);
     }

     }) */

    $(function(){
        var params = new Object();
        var filter = $.parseJSON('<?php echo $pfilter ?>');
        params['models'] = $.parseJSON('<?php echo $models_json ?>');
        params['model_year'] = $.parseJSON('<?php echo $modelyear_json ?>');
        params['displacement'] = $.parseJSON('<?php echo $displacement_json ?>');
        var first = $("#models");
        for(var i = 0;i<params[first.prop('id')].length;i++){
            first.append("<option value = '"+params[first.prop('id')][i][first.prop('id')]+"'>"+params[first.prop('id')][i][first.prop('id')]+"</option>")
        }

        var sec=$('#model_year');
        var t_arr=[];
        for(var i = 0;i<params[sec.prop('id')].length;i++){
            if(t_arr.indexOf(trim(params[sec.prop('id')][i][sec.prop('id')]))<0){
                t_arr.push(trim(params[sec.prop('id')][i][sec.prop('id')]));
            }
        }
        t_arr.sort();
        for(var i = 0;i<t_arr.length;i++){
            sec.append("<option value = '"+t_arr[i]+"'>"+t_arr[i]+"</option>");
        }

        var trd=$('#displacement');
        var t_arr1=[];
        for(var i = 0;i<params[trd.prop('id')].length;i++){
            if(t_arr1.indexOf(trim(params[trd.prop('id')][i][trd.prop('id')]))<0){
                t_arr1.push(trim(params[trd.prop('id')][i][trd.prop('id')]));
            }
        }
        t_arr1.sort();
        for(var i = 0;i<t_arr1.length;i++){
            trd.append("<option value = '"+t_arr1[i]+"'>"+t_arr1[i]+"</option>");
        }

        $(".search").on("change",function(){
            searchCarList();
        })

        function searchCarList(){
            var searchList = $(".search");
            searchList.each(function(i,e){
                _this = $(e);
                if(_this.val() == ""){
                    delete filter[_this.prop("id")];
                }else{
                    filter[_this.prop("id")] = _this.val();
                }
            })
            $("#carList").load('../inventory/getCarList',filter);
        }

        function trim(str){ //删除右边的空格
            return str.replace(/(\s*$)/g,"");
        }
    });

</script>
</body>
</html>