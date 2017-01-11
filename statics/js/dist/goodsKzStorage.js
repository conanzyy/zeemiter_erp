var queryConditions = {
	skey: (frameElement.api.data ? frameElement.api.data.skey : "") || ""
},
$grid = $("#grid"),
addList = {},
urlParam = Public.urlParam(),
defaultPage = Public.getDefaultPage(),
SYSTEM = defaultPage.SYSTEM,
taxRequiredCheck = SYSTEM.taxRequiredCheck;
taxRequiredInput = SYSTEM.taxRequiredInput;
var defaultPage = Public.getDefaultPage();
var api = frameElement.api,
	curId = api.data.id || -1 ,
	data = api.data || {},
	locationId = -1;
	area_code = "";
	isSingle = data.isSingle || 0,
	skuMult = data.skuMult,
	THISPAGE = {
		init: function() {
			this.initDom(), this.loadGrid(), this.addEvent()
		},
		initDom: function() {
			this.storageCombo = $("#storage").combo({
				data: function() {
					return defaultPage.SYSTEM.storageInfo
				},
				text: "name",
				value: "id",
				width: 120,
				defaultSelected: 0,
				cache: !1,
				callback: {
					onChange: function(a) {
						a && a.id && (locationId = a.id, THISPAGE.reloadData())
					}
				}
			}).getCombo();
			
			
			this.$_matchCon = $("#matchCon"), api.data.text ? this.$_matchCon.val(api.data.text) : this.$_matchCon.placeholder()
		},
		loadGrid: function() {
			$("#grid").jqGrid({
				url:"../basedata/area?&locationId=" + locationId,
				datatype: "json",
				width: 450,
				height: 358,
				altRows: !0,
				gridview: !0,
				colModel: [{
					name: "id",
					label: "ID",
					width: 0,
					hidden: !0
				},{
					name: "area_code",
					label: "货位编号",
					width: 60,
					title: !1
				},{
					name: "area_name",
					label: "货位名称",
					width: 60
				}, {
					name: "qty",
					label: "货位备注",
					width: 60,
					title: !1,
					align: "right"
				}],
				cmTemplate: {
					sortable: !1
				},
				page: 1,
				pager: "#page",
				rowNum: 99999,
				rowList: [20, 50, 100],
				scroll: 1,
				onselectrow: !1,
				multiselect: !0,
				multiboxonly: !0,
				loadonce: !0,
				viewrecords: !0,
				shrinkToFit: !0,
				forceFit: !1,
				jsonReader: {
					root: "data.rows",
					records: "data.records",
					total: "data.total",
					repeatitems: !1,
					id: 0
				},
				loadError: function() {},
				ondblClickRow: function() {
					isSingle && (callback(), frameElement.api.close())
				},
				onSelectRow: function(a, b) {
					Public.ajaxPost("../basedata/area/saveRel", { area_id : a , item_id : item_id , storage_id : locationId }, function(c) {
						if(c && 200 == c.status){
							 parent.Public.tips({ content: "设置默认货位成功！"});
						}else{
							 parent.Public.tips({ type: 1, content: "设置默认仓库失败！" + c.msg })
						}
					})
				},
				gridComplete: function() {
					isComplete = true;
					$("#jqgh_grid_cb").hide();
					
					for(var _item = 0 ; _item < addList.length ; _item++){
						var a = $("#" + addList[_item].area_id);
						a.find("input[type=checkbox]").prop("checked",true);
						$grid.jqGrid("setSelection", addList[_item].area_id, !1);
						//$grid.jqGrid("setSelection", _item, !1);
						//!a.length && a.find("input:checkbox")[0].checked && $grid.jqGrid("setSelection", 1, !1)
					}
				}
				
			})
		},
		reloadData: function() {
			var $a = this;
			Public.ajaxGet("../basedata/area/getRel", { item_id : item_id , storage_id : locationId }, function(data) {
				addList = data.data;
				$("#grid").jqGrid("setGridParam", {
					url: "../basedata/area?&locationId=" + locationId + "&area_code=" + area_code ,
					datatype: "json"
				}).trigger("reloadGrid")
			})
			
		},
		addEvent: function() {
			var a = this;
			$("#search").click(function() {
				locationId = a.storageCombo.getValue();
				area_code = a.$_matchCon.val();
				THISPAGE.reloadData();
			});
			
		}
	};
THISPAGE.init();
document.onkeydown = function(e) {
    var ev = document.all ? window.event : e;
    if (ev.keyCode == 13) {
        $("#search").trigger("click");
    }
};

