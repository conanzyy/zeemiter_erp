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
var api = frameElement.api,
	curId = api.data.id || -1 ,
	data = api.data || {},
	locationId = -1;
	isSingle = data.isSingle || 0,
	skuMult = data.skuMult,
	THISPAGE = {
		init: function() {
			this.initDom(), this.loadGrid(), this.addEvent()
		},
		initDom: function() {
			
			this.storageCombo = $("#storage").combo({
				data: function() {
					return parent.SYSTEM.storageInfo
				},
				text: "name",
				value: "id",
				width: 120,
				defaultSelected: 0,
				addOptions: {
					text: "所有仓库",
					value: -1
				},
				cache: !1,
				callback: {
					onChange: function(a) {
						a && a.id && (locationId = a.id, THISPAGE.reloadData())
					}
				}
			}).getCombo();
			
			
			this.$_matchCon = $("#matchCon"), api.data.text ? this.$_matchCon.val(api.data.text) : this.$_matchCon.placeholder(), this.goodsCombo = this.$_matchCon.combo({
				data: function() {
					var a = Public.getDefaultPage();
					return a.SYSTEM.goodsInfo
				},
				formatText: function(a) {
					return "" === a.spec ? a.number + " " + a.name : a.number + " " + a.name + "_" + a.spec
				},
				value: "id",
				defaultSelected: ["id", api.data.id],
				editable: !0,
				maxListWidth: 500,
				cache: !1,
				forceSelection: !0,
				maxFilter: 10,
				trigger: !1,
				listHeight: 182,
				listWrapCls: "ui-droplist-wrap",
				callback: {
					onChange: function(a) {
						a && a.id && (curId = a.id, THISPAGE.reloadData())
					}
				},
				queryDelay: 0,
				inputCls: "edit_subject",
				wrapCls: "edit_subject_wrap",
				focusCls: "",
				disabledCls: "",
				activeCls: ""
			}).getCombo()
		},
		loadGrid: function() {
			$(window).height() - $(".grid-wrap").offset().top - 84;
			$("#grid").jqGrid({
				url: "scm/invSa/justIntimeInvAll?action=justIntimeInv&invId="+curId+"&locationId=" + locationId,
				datatype: "json",
				width: 660,
				height: 264,
				altRows: !0,
				gridview: !0,
				colModel: [{
					name: "id",
					label: "ID",
					width: 0,
					hidden: !0
				},{
					name: "number",
					label: "商品编号",
					width: 100,
					title: !1
				},{
					name: "name",
					label: "商品名称",
					width: 450
				}, {
					name: "qty",
					label: "库存数量",
					width: 100,
					title: !1,
					align: "right"
				},{
					name: "unitName",
					label: "单位",
					width: 100,
					title: !1
				},{
					name: "unitId",
					label: "单位ID",
					width: 0,
					hidden: !0
				},{
					name: "spec",
					label: "规格型号",
					width: 100,
					title: !1
				},{
					name: "skuId",
					label: "规格型号",
					width: 0,
					hidden: !0,
					title: !1
				},{
					name: "locationId",
					label: "编码",
					width: 0,
					hidden: !0
				},{
					name: "locationName",
					label: "仓库名称",
					width: 0,
					hidden: !0
				}],
				cmTemplate: {
					sortable: !1
				},
				page: 1,
				multiselect:!0,
				sortname: "number",
				sortorder: "desc",
				pager: "#page",
				rowNum: 2e3,
				rowList: [20, 50, 100],
				scroll: 1,
				loadonce: !0,
				viewrecords: !0,
				shrinkToFit: !0,
				forceFit: !1,
				jsonReader: {
					root: "data.rows",
					repeatitems: !1,
					id: 0
				},
				loadError: function() {},
				ondblClickRow: function() {
					isSingle && (callback(), frameElement.api.close())
				},
				onSelectRow: function(a, b) {
					if (b) {
						var c = $grid.jqGrid("getRowData", a);
						skuMult && c.skuClassId > 0 ? ($("#grid").jqGrid("setSelection", a, !1), $.dialog({
							width: 470,
							height: 400,
							title: "选择【" + c.number + " " + c.name + "】的属性",
							content: "url:http://" + defaultPage.location.hostname + "/settings/assistingProp-batch.jsp",
							data: {
								isSingle: isSingle,
								skey: "",
								skuClassId: c.skuClassId,
								callback: function(b, d) {
									for (var e = [], f = 0, g = b.length; g > f; f++) {
										var h = b[f],
											i = $.extend(!0, {}, c);
										if (i.skuName = h.skuName, i.skuId = h.skuId, i.qty = h.qty, 0 === f) $("#grid").jqGrid("setRowData", a, i);
										else {
											var j = f;
											!
											function l() {
												$("#" + a + "_" + j).length && (j++, l())
											}(), i.id = a + "_" + j, $("#grid").jqGrid("addRowData", i.id, i, "after", a)
										}
										addList[i.id] = i, e.push(i)
									}
									for (var f = 0; f < e.length; f++) {
										var k = $("#" + e[f].id).find("input:checkbox")[0];
										k && !k.checked && $("#grid").jqGrid("setSelection", e[f].id, !1)
									}
									d.close()
								}
							},
							init: function() {},
							lock: !0,
							ok: !1,
							cancle: !1
						})) : addList[a] = c
					} else addList[a] && delete addList[a]
				},
				onSelectAll: function(a, b) {
					for (var c = 0, d = a.length; d > c; c++) {
						var e = a[c];
						if (b) {
							var f = $grid.jqGrid("getRowData", e);
							addList[e] = f
						} else addList[e] && delete addList[e]
					}
				},
				gridComplete: function() {
					isComplete = true;
					for (_item in addList) {
						var a = $("#" + addList[_item].id);
						!a.length && a.find("input:checkbox")[0].checked && $grid.jqGrid("setSelection", _item, !1)
					}
				}
				
			})
		},
		reloadData: function() {
			var $a = this;
			addList = {},$("#grid").jqGrid("setGridParam", {
				url: "scm/invSa/justIntimeInvAll?action=justIntimeInv&invId="+curId+"&locationId=" + locationId,
				datatype: "json"
			}).trigger("reloadGrid")
		},
		addEvent: function() {
			var a = this;
			$("#search").click(function() {
				locationId = a.storageCombo.getValue(), curId = a.goodsCombo.getValue(), curId && THISPAGE.reloadData()
			}), $("#refresh").click(function() {
				THISPAGE.reloadData()
			}), this.$_matchCon.bind("focus", function() {
				var a = this;
				$(this).val() && setTimeout(function() {
					a.select()
				}, 10)
			})
		}
	};
THISPAGE.init();


function callback() {
	var a = parent.THISPAGE || api.data.page,
	b = a.curID,
	c = a.newId,
	d = api.data.callback,
	e = $("#grid").jqGrid("getGridParam", "selarrrow"),
	f = e.length,
	g = oldRow = parent.curRow,
	h = parent.curCol;
	if (isSingle) {
		parent.$("#grid").jqGrid("restoreCell", g, h);
		var i = $("#grid").jqGrid("getRowData", $("#grid").jqGrid("getGridParam", "selrow"));
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
			skuName: i.skuName
		});
		var m = $.extend(!0, {}, l);
		parent.$("#" + k).data("goodsInfo", m).data("storageInfo", {
			id: i.locationId,
			name: i.locationName
		}).data("unitInfo", {
			unitId: i.unitId,
			name: i.unitName
		}), d(k, l)
	} else if (f > 0) {
		parent.$("#grid").jqGrid("restoreCell", g, h);
		for (rowid in addList) {
			var i = addList[rowid];
			if (i.id = i.id.split("_")[0], delete i.amount, defaultPage.SYSTEM.goodsInfo.push(i), "" === i.spec) var j = i.number + " " + i.name;
			else var j = i.number + " " + i.name + "_" + i.spec;
			if (b) var k = b;
			else var k = c;
			var n = $.extend(!0, {}, i);
			if (n.goods = j, n.id = k, n.qty = n.qty || 1, b) var o = parent.$("#grid").jqGrid("setRowData", Number(b), {});
			else {
				var o = parent.$("#grid").jqGrid("addRowData", Number(c), {}, "last");
				c++
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