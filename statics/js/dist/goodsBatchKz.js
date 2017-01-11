
var SEARCH_URL;
var compress_id;

function callbackSp() {
	var a = parent.THISPAGE || api.data.page,
		b = a.curID,
		c = (a.newId, "fix1"),
		d = (api.data.callback, $("#grid").jqGrid("getGridParam", "selarrrow")),
		e = d.length,
		f = oldRow = parent.curRow,
		g = parent.curCol;
	if (e > 0) {
		parent.$("#fixedGrid").jqGrid("restoreCell", f, g);
		var h = Public.getDefaultPage(),
			i = $("#grid").jqGrid("getRowData", d[0]);
		if (i.id = i.id.split("_")[0], h.SYSTEM.goodsInfo.push(i), "" === i.spec) var j = i.number + " " + i.name;
		else var j = i.number + " " + i.name + "_" + i.spec;
		var k = $.extend(!0, {}, i);
		if (k.goods = j, k.id = c, b) var l = parent.$("#fixedGrid").jqGrid("setRowData", b, {});
		l && parent.$("#" + b).data("goodsInfo", i).data("storageInfo", {
			id: i.locationId,
			name: i.locationName
		}).data("unitInfo", {
			unitId: i.unitId,
			name: i.unitName
		}).data("areaInfo", {
			id: i.locationAreaId,
			name: i.locationArea
		}), parent.$("#fixedGrid").jqGrid("setRowData", c, k)
	}
	return d
}
function callback() {
	var a = parent.THISPAGE || api.data.page,
		b = a.curID,
		c = a.newId,
		d = api.data.callback,
		e = $("#grid").jqGrid("getGridParam", "selarrrow"),
		f = e.length,
		g = oldRow = parent.curRow,
		h = parent.curCol,
		//得到默认仓库
		defaultStorage = defaultPage.SYSTEM.defaultStorage;

	if (isSingle) {
		parent.$("#grid").jqGrid("restoreCell", g, h);
		var i = $("#grid").jqGrid("getRowData", $("#grid").jqGrid("getGridParam", "selrow"));

		//设置默认仓库
		if(defaultStorage){
			i.locationId = i.locationId == null || i.locationId == '' || i.locationId == 0 ? defaultStorage.id : i.locationId;
			i.locationName = i.locationName == null || i.locationName == '' ? defaultStorage.name : i.locationName;
		}

		if (i.id = i.id.split("_")[0], delete i.amount, defaultPage.SYSTEM.goodsInfo.push(i), "" === i.spec) var j = i.number + " " + i.name;
		else var j = i.number + " " + i.name + "_" + i.spec;
		if (g > 8 && g > oldRow) var k = g;
		else var k = b;
		var l = parent.$("#grid").jqGrid("getRowData", Number(b));
		l = $.extend({}, l, {
			id: i.id,
			goods: j,
			invNumber: i.number,
			invName: i.name,
			unitName: i.unitName,
			qty: 1,
			price: i.salePrice,
			spec: i.spec,
			skuId: i.skuId,
			skuName: i.skuName,
			isSerNum: i.isSerNum
		});
		var m = $.extend(!0, {}, l);
		parent.$("#" + k).data("goodsInfo", m).data("storageInfo", {
			id: i.locationId,
			name: i.locationName
		}).data("unitInfo", {
			unitId: i.unitId,
			name: i.unitName
		}).data("areaInfo", {
			id: i.locationAreaId,
			name: i.locationArea
		}), d(k, l)
	} else if (f > 0) {

		parent.$("#grid").jqGrid("restoreCell", g, h);

		for (rowid in addList) {
			var i = addList[rowid];
			//设置默认仓库
			if(defaultStorage){
				i.locationId = i.locationId == null || i.locationId == '' || i.locationId == 0 ? defaultStorage.id : i.locationId;
				i.locationName = i.locationName == null || i.locationName == '' ? defaultStorage.name : i.locationName;
			}
			i.gid = i.id;
			if (i.id = i.id.split("_")[0], delete i.amount, defaultPage.SYSTEM.goodsInfo.push(i), "" === i.spec) var j = i.number + " " + i.name;
			else var j = i.number + " " + i.name + "_" + i.spec;
			if (b) var k = b;
			else var k = c;
			var n = $.extend(!0, {}, i);
			if (n.goods = j, n.id = k, n.qty = n.qty || 1, b) var o = parent.$("#grid").jqGrid("setRowData", Number(b), {});
			else {1
				var o = parent.$("#grid").jqGrid("addRowData", Number(c), {}, "last");
				c++
			}
			
			if(urlParam.bussType == 'in'){
				n.qty = n.minNum || 1;
				n.amount = n.qty * n.purPrice;
			}
			
			if(parent.settlePrice){
				var oprice = parent.settlePrice[i.id];
				var priceType = "";
				if(urlParam.bussType == 'in'){
					priceType = "最近无采购";
					n.oprice = oprice ? Public.numToCurrency(oprice['price']) : priceType;
				}else{
					priceType = "最近无销售";
					n.oprice = oprice ? Public.numToCurrency(oprice['price']) : priceType;
				}
			}
			o && parent.$("#" + k).data("goodsInfo", i).data("storageInfo", {
				id: i.locationId,
				name: i.locationName
			}).data("unitInfo", {
				unitId: i.unitId,
				name: i.unitName
			}).data("areaInfo", {
				id: i.locationAreaId,
				name: i.locationArea
			}), parent.$("#grid").jqGrid("setRowData", k, n), g++;
			var p = parent.$("#" + b).next();
			b = p.length > 0 ? parent.$("#" + b).next().attr("id") : ""
		}
		d(c, b, g), $("#grid").jqGrid("resetSelection"), addList = {}
	}
	return e
}
var queryConditions = {
	skey: (frameElement.api.data ? frameElement.api.data.skey : "") || "",
	mBrand: (frameElement.api.data ? frameElement.api.data.mBrand : "") || "",
	cars: (frameElement.api.data ? frameElement.api.data.cars : "") || "",
	models: (frameElement.api.data ? frameElement.api.data.models : "") || "",
	mYear: (frameElement.api.data ? frameElement.api.data.mYear : "") || "",
	displacement: (frameElement.api.data ? frameElement.api.data.displacement : "") || "",
	width:(frameElement.api.data ? frameElement.api.data.width : "") || "",
	bussType : $bussType,
},
	$grid = $("#grid"),
	addList = {},
	urlParam = Public.urlParam(),
	zTree_kz, defaultPage = Public.getDefaultPage(),
	SYSTEM = defaultPage.SYSTEM,
	taxRequiredCheck = SYSTEM.taxRequiredCheck;
taxRequiredInput = SYSTEM.taxRequiredInput;
var api = frameElement.api,
	test,
	data = api.data || {},
	isSingle = data.isSingle || 0,
	skuMult = data.skuMult,
	THISPAGE = {
		init: function() {
			this.initDom(), this.initZtree(),this.loadGrid(), this.addEvent();
			$("#tab li").on("click",function(){
				if ($(this).hasClass("cur")) return;
				location.href ="../settings/goods_batch_all?bussType=out&supType="+$(this).data('type');
			});
		},
		initDom: function() {
			this.modelsCombo = Business.ModelsCombo($("#matchModel"), { width: 170 }),
			this.matchYearCombo = Business.YearsCombo($("#matchYear"), { width: 170 },this.modelsCombo),
			this.displacementsCombo = Business.DisplacementsCombo($("#matchDisplacement"), {  width: 170 },this.modelsCombo),
			this.stockCombo  = Business.stockTypeCombo($("#stockType"), { width: 170 }),
			this.$_matchCon = $("#matchCon").val(queryConditions.skey || "商品名称/商品编码/商品品牌/车型/vin码"), this.$_matchCon.placeholder()

		},
		initZtree: function() {
			zTree = Public.zTree_kz.init($(".grid-wrap"), {
				defaultClass: "ztreeDefault",
				showRoot: !1,
				height:300,
			}, {
				callback: {
					beforeClick: function(a, b) {
						queryConditions.assistId = b.id, $("#search").trigger("click")
					}
				}
			})
		},
		loadGrid: function() {
			function a(a, b, c) {
				var d = '<div class="operating" data-id="' + c.id + '"><!--<span class="min-btn btn-info ui-icon-search" title="查询"><span class="fa fa-search"></span>库存查询</span>--><!--<span class="min-btn btn-warning ui-icon-copy" title="商品图片"><span><span class = "fa fa-image"></span>图片</span></span>--><span class="min-btn btn-primary ui-icon-detail" title="商品详情"><span><span class = "fa fa-sign-out"></span>商品详情</span></div>';
				return d
			}
			$("#grid").jqGrid({
				url: "../basedata/inventory?action=kzlist",
				postData: queryConditions,
				datatype: "json",
				width: queryConditions.width - 200,
				height: 350,
				altRows: !0,
				gridview: !0,
				colModel: [{
					name: "id",
					label: "ID",
					width: 0,
					hidden: !0
				}, {
					name: "operating",
					label: "操作",
					width: 75,
					fixed: !0,
					formatter: a,
					align: "center"
				}, {
					name: "name",
					label: "商品名称",
					width: 200,
					fixed: !0,
					classes: "ui-ellipsis",
				}, {
					name: "skuClassId",
					label: "skuClassId",
					width: 0,
					hidden: !0
				}, {
					name: "skuId",
					label: "skuId",
					width: 0,
					hidden: !0
				}, {
					name: "saleModel",
					label: "saleModel",
					width: 0,
					hidden: !0
				},{
					name: "skuName",
					label: "属性",
					width: 100,
					hidden: !skuMult,
					classes: "ui-ellipsis"
				}, {
					name: "qty",
					label: "数量",
					width: 60,
					hidden: !skuMult,
					formatter: function(a) {
						return a || "&#160;"
					}
				}, {
					name: "spec",
					label: "规格型号",
					width: 80,
					fixed: !0,
					title: !1
				},
				{
					name: "productCode",
					label: "厂家产品码",
					width: 80,
					fixed: !0,
					title: !1
				},{
					name: "unitName",
					label: "单位",
					width: 40,
					fixed: !0,
					title: !1
				}, {
					name: "unitId",
					label: "单位ID",
					width: 0,
					hidden: !0
				},{
					name: "packSpec",
					label: "包装规格",
					width: 60,
					fixed: !0,
					title: !1
				},{
					name: "minNum",
					label: "订货批量",
					width: 60,
					fixed: !0,
					title: !1
				}, {
					name: "lprice",
					label: "结算价",
					width: 60,
					fixed: !0,
					formatter: "currency",
					hidden: !1
				},{
					name: "salePrice",
					label: "指导价",
					width: 60,
					formatter: "currency",
					fixed: !0,
					hidden: !1
				},{
					name: "purPrice",
					label: "结算价",
					width: 60,
					fixed: !0,
					formatter: "currency",
					hidden: !0
				},
				{
					name: "locationId",
					label: "仓库ID",
					width: 0,
					hidden: !0
				}, {
					name: "locationName",
					label: "仓库名称",
					width: 0,
					hidden: !0
				},{
					name: "locationAreaId",
					label: "区域ID",
					width: 0,
					hidden: !0
				},{
					name: "locationArea",
					label: "区域",
					width: 0,
					hidden: !0
				}, {
					name: "isSerNum",
					label: "是否启用序列号",
					width: 0,
					hidden: !0
				},{
					name: "stockType",
					label: "备货类型",
					width: 0,
					fixed: !0,
					hidden:!0,
					title: !1
				},{
					name: "retailPrice",
					label: "价格集合",
					width: 80,
					fixed: !0,
					hidden:!0,
					title: !1
				},
				{
					name: "storageSum",
					label: "当前库存",
					width: 60,
					classes : 'storage',
					fixed: !0,
					title: !1
				},{
					name: "saleModel",
					label: "组织",
					width: 60,
					align: 'center',
					fixed: !0,
					title: !1
				},{
					name: "number",
					label: "商品编号",
					width: 115,
					fixed: !0,
					hidden:!0,
					title: !1
				},{
					name: "skuHot",
					label: "物料分级",
					width: 60,
					fixed: !0,
					align: 'center',
					title: !1
				},{
					name: "skuStatus",
					label: "物料状态",
					width: 60,
					fixed: !0,
					align: 'center',
					formatter: function(a, b, c) {
						switch (a) {
							case "1":
								d = '<span class="ui-label ui-label-success">正常</span>';break;
							case "2":
								d = '<span class="ui-label ui-label-warning">暂供</span>';break;
							case "3":
								d = '<span class="ui-label ui-label-important">停供</span>';break;
							case "4":
								d = '<span class="ui-label ui-label-default">停用</span>';break;
							case "5":
								d = '<span class="ui-label ui-label-default">新品</span>';break;
							default:
								d = ""; break;
						}
						return d;
					},
					title: !1
				},{
					name: "carModel",
					label: "适用车型",
					width: 150,
					fixed: !0,
					classes: "ui-ellipsis",
				}],
				cmTemplate: {
					sortable: !1
				},
				multiselect: isSingle ? !1 : !0,
				page: 1,
				sortname: "number",
				sortorder: "desc",
				pager: "#page",
				page: 1,
				rowNum: 10,
				rowList: [10, 50, 100],
				viewrecords: !0,
				shrinkToFit: !0,
				forceFit: !1,
				jsonReader: {
					root: "data.rows",
					records: "data.records",
					total: "data.total",
					repeatitems: !1,
					id: "id"
				},
				loadError: function() {},
				ondblClickRow: function() {
					isSingle && (callback(), frameElement.api.close())
				},
				ondblClickRow: function(a) {
				},
				onSelectRow: function(a, b) {
					var c = $("#grid").jqGrid("getRowData", a);
					if(!c.spec){
						var d = c.number + " " + c.name;
					}else{
						var d = c.number + " " + c.name + "_" + c.spec;
					}
					var DataId = parent.$("#grid").getDataIDs();
					for(var i = 0;i<DataId.length;i++){
						if(parent.$("#grid").jqGrid("getRowData", i).goods == d){
							return parent.Public.tips({
								type: 2,
								content: "商品重复，请重新选择！"
							})
						}
					}
					if (b) {
						var c = $grid.jqGrid("getRowData", a);
						addList[a] = c;
					} else {
						addList[a] && delete addList[a];
					}
				},
				onSelectAll: function(a, b) {
					for (var c = 0, d = a.length; d > c; c++) {
						var e = a[c];
						var f = $("#grid").jqGrid("getRowData", e);
						if(!f.spec){
							var D = f.number + " " + f.name;
						}else{
							var D = f.number + " " + f.name + "_" + f.spec;
						}
						var DataId = parent.$("#grid").getDataIDs();
						for(var i = 0;i<DataId.length;i++){
							if(parent.$("#grid").jqGrid("getRowData", i).goods == D){
								return parent.Public.tips({
									type: 2,
									content: "商品重复，请重新选择！"
								})
							}
						}
						if (b) {
							var f = $grid.jqGrid("getRowData", e);
							addList[e] = f
						} else addList[e] && delete addList[e]
					}
				},
				gridComplete: function() {
					/*for (_item in addList) {=.........
						var a = $("#" + addList[_item].id);
						!a.length && a.find("input:checkbox")[0].checked && $grid.jqGrid("setSelection", _item, !1)
					}*/
				},
				loadComplete :function(data){
					$("#models").addClass("hide");
					if(data.data.models){
						$("#models").removeClass("hide");
						$("#models_name").html(data.data.models);
					}
				}
			})
		},
		reloadData: function(a) {
			addList = {}, $("#grid").jqGrid("setGridParam", {
				url: SEARCH_URL,
				datatype: "json",
				postData: a
			}).trigger("reloadGrid")
		},
		addEvent: function() {
			var a = this;

			$("#storage").on("click",function(){
				$("#search").click();
			})

			$(".grid-wrap").on("click", ".ui-icon-search", function(a) {
				a.preventDefault();
				var b = $(this).parent().data("id");
				Business.forSearch(b, "")
			}), $(".grid-wrap").on("click", ".ui-icon-copy", function(a) {
				a.preventDefault();
				var b = $(this).parent().data("id"),
					c = "商品图片";
					$.dialog({
						content: "url:../settings/fileUpload",
						data: {
							title: c,
							id: b,
							from:'kz',
							callback: function() {}
						},
						title: c,
						width: $(window).width() * 0.8,
						height: $(window).height() * 0.8,
						max: !1,
						min: !1,
						lock: !0,
						cache: !1,
					})
			}), $(".grid-wrap").on("click", ".ui-icon-detail", function(a) {
				a.preventDefault();
				var b = $(this).parent().data("id");
				window.open('http://www.kzmall.cn/item.html?item_id='+b);

			}), $("#search").click(function() {
				queryConditions.catId = a.catId,
				queryConditions.skey = "商品名称/商品编码/商品品牌/车型/vin码" === a.$_matchCon.val() ? "" : a.$_matchCon.val(),
				queryConditions.models = a.modelsCombo.getValue(),
				queryConditions.mYear = a.matchYearCombo.getValue(),
				queryConditions.displacement =  a.displacementsCombo.getValue(),
				queryConditions.stock = a.stockCombo.getValue(),
				queryConditions.bussType = $bussType,
				queryConditions.storage = $("#storage").prop("checked");
				a.reloadData(queryConditions)
			}), $("#refresh").click(function() {
				a.reloadData(queryConditions)
			})
		}
	};
THISPAGE.init();