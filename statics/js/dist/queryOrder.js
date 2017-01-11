var defalutPage = Public.getDefaultPage(),
SYSTEM = system = defalutPage.SYSTEM,
CONFIG = defalutPage.CONFIG,
hiddenAmount = !1,
billRequiredCheck = system.billRequiredCheck,
urlParam = Public.urlParam(),
isSingle = 0,
addList = {},
api = frameElement.api,
queryConditions = {
	matchCon: "",
	bussType:"query",
	transType:urlParam.transType == null || urlParam.transType == "" ? "150501" : urlParam.transType,
	contact:urlParam.contact
},
URLMAP = {
	'150501': CONFIG.SITE_URL+"/scm/invPu?action=list",
	'150601': CONFIG.SITE_URL+"/scm/invSa?action=list"
};


var THISPAGE = {
	init: function() {
		this.initDom();
		this.loadGrid();
		this.addEvent();
	},
	initDom: function() {
		this.$_matchCon = $("#matchCon");
		this.$_beginDate = $("#beginDate").val(system.beginDate);
		this.$_endDate = $("#endDate").val(system.endDate); 
		this.$_matchCon.placeholder(); 
		this.$_beginDate.datepicker(); 
		this.$_endDate.datepicker()
	},
	loadGrid: function() {
		var grid = Public.setGrid();
		
		queryConditions.beginDate = this.$_beginDate.val();
		queryConditions.endDate = this.$_endDate.val();
		var c = "150501" == queryConditions.transType ? "付" : "收";
		
		$("#grid").jqGrid({
			url: URLMAP[queryConditions.transType],
			postData: queryConditions,
			datatype: "json",
			autowidth: !0,
			height: grid.h,
			altRows: !0,
			gridview: !0,
			multiselect: !0,
			colNames: ["单据日期", "单据编号", "客户", "采购金额", "优惠后金额", "已" + c + "款", c + "款状态", "制单人", "审核人", "备注", "订单来源"],
			colModel: [{
				name: "billDate",
				index: "billDate",
				width: 90,
				align: "center"
			}, {
				name: "billNo",
				index: "billNo",
				width: 140,
				align: "center"
			}, {
				name: "contactName",
				index: "contactName",
				width: 100
			}, {
				name: "totalAmount",
				index: "totalAmount",
				hidden: hiddenAmount,
				width: 100,
				align: "right",
				formatter: "currency"
			}, {
				name: "amount",
				index: "amount",
				hidden: hiddenAmount,
				width: 100,
				align: "right",
				formatter: "currency"
			}, {
				name: "rpAmount",
				index: "rpAmount",
				hidden: hiddenAmount,
				width: 100,
				align: "right",
				formatter: "currency"
			}, {
				name: "hxStateCode",
				index: "hxStateCode",
				width: 80,
				fixed: !0,
				align: "center",
				title: !0,
				classes: "ui-ellipsis",
				formatter: function(a) {
					switch (a) {
					case 0:
						return "未" + c + "款";
					case 1:
						return "部分" + c + "款";
					case 2:
						return "全部" + c + "款";
					default:
						return "&#160"
					}
				}
			}, {
				name: "userName",
				index: "userName",
				width: 80,
				fixed: !0,
				align: "center",
				title: !0,
				classes: "ui-ellipsis"
			}, {
				name: "checkName",
				index: "checkName",
				width: 80,
				hidden: billRequiredCheck ? !1 : !0,
				fixed: !0,
				align: "center",
				title: !0,
				classes: "ui-ellipsis"
			}, {
				name: "description",
				index: "description",
				width: 200,
				classes: "ui-ellipsis",
				sortable: !1
			}, {
				name: "disEditable",
				label: "不可编辑",
				index: "disEditable",
				hidden: !0
			}],
			cmTemplate: {
				sortable: !0,
				title: !1
			},
			page: 1,
			pager: "#page",
			rowNum: 100,
			multiselect: isSingle ? !1 : !0,
			rowList: [100, 200, 500],
			viewrecords: !0,
			shrinkToFit: !1,
			forceFit: !1,
			jsonReader: {
				root: "data.rows",
				records: "data.records",
				total: "data.total",
				repeatitems: !1,
				id: "id"
			},
			loadComplete: function(a) {
				if (billRequiredCheck) {
					for (var b = a.data.rows, c = 0; c < b.length; c++) {
						var d = b[c];
						d.checked || $("#" + d.id).addClass("gray");
					}
				}
			},
			loadError: function() {},
			ondblClickRow: function(a) {},
			onSelectRow:function (obj, bool){
				//单选时将数据加载到JS对象addList中
				if(bool){
					var row = $("#grid").jqGrid("getRowData", obj);
					addList[obj] = row
				}else{
					addList[obj] && delete addList[obj];
				}
			},
			onSelectAll: function(obj, bool) {
				//和单选逻辑一样--全选时将数据加载到JS对象addList中
				for (var i = 0 ; obj != null && i < obj.length ; i++) {
					var id = obj[i];
					if (bool) {
						var row = $("#grid").jqGrid("getRowData", id);
						addList[id] = row;
					} else {
						addList[e] && delete addList[e];
					}
				}
			},
		})
	},
	
	reloadData: function(a) {
		addList = {} //初始化变量
		$("#grid").jqGrid("setGridParam", {
			url: URLMAP[queryConditions.transType],
			datatype: "json",
			postData: a
		}).trigger("reloadGrid")
	},
	addEvent: function() {
		var a = this;
		$("#search").click(function() {
			queryConditions.matchCon = "请输入单据号或供应商或备注" === a.$_matchCon.val() ? "" : a.$_matchCon.val();
			queryConditions.beginDate = a.$_beginDate.val();
			queryConditions.endDate = a.$_endDate.val();
			THISPAGE.reloadData(queryConditions);
		});
		
		$(window).resize(function() {
			Public.resizeGrid();
		})
	}
};


function callback() {
	var page = parent.THISPAGE || api.data.page,
		frame_curId = page.curID,
		frame_newId = page.newId,
		frame_callback = api.data.callback,
		ids = $("#grid").jqGrid("getGridParam","selarrrow"),
		curRow = frame_oldRow = parent.curRow,
		frame_curCol = parent.curCol,
		
	b = page.curID,
	c = page.newId,
	d = api.data.callback,
	ids = $("#grid").jqGrid("getGridParam","selarrrow"),
	g = oldRow = parent.curRow,
	h = parent.curCol;
	
	if (isSingle) {
		
		parent.$("#accountGrid").jqGrid("restoreCell", curRow, frame_curCol);
		var curRowData = $("#grid").jqGrid("getRowData", $("#grid").jqGrid("getGridParam", "selrow"));
		alert(JSON.stringify(curRowData));
		alert(curRowData.billNo);
		/*if (i.id = i.id.split("_")[0], delete i.amount, defaultPage.SYSTEM.goodsInfo.push(i), "" === i.spec) var j = i.number + " " + i.name;
		else var j = i.number + " " + i.name + "_" + i.spec;*/
		if (curRow > 8 && curRow > frame_oldRow) 
			var k = curRow;
		else 
			var k = frame_curId;
		
		parent.$("#" + k).data("settlementInfo",{
			id  : curRowData.billNo,
			billNo: curRowData.billNo
		});
		
		var param = {'settlement' : curRowData.billNo};
		
		parent.$("#accountGrid").jqGrid("setRowData", k, {'settlement':curRowData.billNo})
		frame_callback(k, param);
	
	} else if (ids.length > 0) {
		parent.$("#accountGrid").jqGrid("restoreCell", curRow, frame_curCol);
		for (rowid in addList) {
			var curRowData = addList[rowid];
			
			if (frame_curId) {
				var k = frame_curId;
				var o = parent.$("#accountGrid").jqGrid("setRowData", Number(frame_curId), {});
			}else{
				var k = frame_newId;
				var o = parent.$("#accountGrid").jqGrid("addRowData", Number(frame_newId), {}, "last");
				c++
			}
			
			o && parent.$("#" + k).data("settlementInfo",{
				id  : curRowData.billNo,
				billNo: curRowData.billNo
			}),parent.$("#accountGrid").jqGrid("setRowData", k, {'settlement':curRowData.billNo}),curRow++;
			
			var p = parent.$("#" + frame_curId).next();
			frame_curId = p.length > 0 ? parent.$("#" + frame_curId).next().attr("id") : ""
		}
		frame_callback(frame_newId, frame_curId, curRow), $("#grid").jqGrid("resetSelection"), addList = {}
	}
	return ids
}


THISPAGE.init();