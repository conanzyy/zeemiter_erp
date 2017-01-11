var CONFIG = parent.CONFIG
var queryConditions = {
	skey: "",
	mBrand: "",
	cars: "",
	models: "",
	mYear: "",
};


$(function() {
	function a() {
		this.modelsCombo = Business.ModelsCombo($("#matchModel"), { width: 170 }),
		this.matchYearCombo = Business.YearsCombo($("#matchYear"), { width: 170 },this.modelsCombo),
		this.displacementsCombo = Business.DisplacementsCombo($("#matchDisplacement"), {  width: 170 },this.modelsCombo),
		this.$_matchCon = $("#matchCon").val(queryConditions.skey || "商品名称/商品编码/商品品牌/车型/vin码"), this.$_matchCon.placeholder()
		this.stockCombo  = Business.stockTypeCombo($("#stockType"), { width: 170 }),
		Public.zTree_kz.init($("#tree"), {
			defaultClass: "innerTree",
			showRoot: !1,
			rootTxt: "全部"
		}, {
			callback: {
				beforeClick: function(a, b) {
					$("#currentCategory").data("id", b.id).html(b.name), $("#search").trigger("click")
				}
			}
		})
	}
	function b() {
		var a = Public.setGrid(f, g),
			b = parent.SYSTEM.rights,
			c = !(parent.SYSTEM.isAdmin || b.AMOUNT_COSTAMOUNT),
			h = !(parent.SYSTEM.isAdmin || b.AMOUNT_INAMOUNT),
			k = !(parent.SYSTEM.isAdmin || b.AMOUNT_OUTAMOUNT),
			l = [{
				name: "operate",
				label: "操作",
				width: 220,
				fixed: !0,
				align: 'left',
				formatter: function(a, b, c) {
					
					/* <span class="min-btn btn-success set-first" title="期初设置"><span class="fa fa-edit"></span>期初设置</span>*/
					var d = '<div class="operating" data-id="' + c.id + '"> <span class="min-btn btn-warning set-price" title="修改"><span class="fa fa-yen"></span>配置售价</span> <span class="min-btn btn-success set-storage" title="配置货位"><span class="fa fa-home"></span>配置货位</span></div>';

					return d
				},
				title: !1
			},{
				name: "categoryName",
				label: "商品类别",
				index: "categoryName",
				width: 100,
				hidden:true,
				title: !1
			}, {
				name: "number",
				label: "商品编号",
				index: "number",
				width: 100,
				title: !1
			}, {
				name: "name",
				label: "商品名称",
				index: "name",
				width: 200,
				classes: "ui-ellipsis"
			}, {
				name: "spec",
				label: "规格型号",
				index: "spec",
				width: 100,
				classes: "ui-ellipsis"
			},{
				name: "productCode",
				label: "厂家产品码",
				width: 100,
				fixed: !0,
				title: !1
			},{
				name: "unitName",
				label: "单位",
				index: "unitName",
				width: 60,
				align: "center",
				title: !1
			}, /*{
				name: "currentQty",
				label: "当前库存",
				index: "currentQty",
				width: 80,
				align: "right",
				title: !1,
				formatter: i.currentQty
			}, {
				name: "quantity",
				label: "期初数量",
				index: "quantity",
				width: 80,
				align: "right",
				title: !1,
				formatter: i.quantity
			}, {
				name: "unitCost",
				label: "单位成本",
				index: "unitCost",
				width: 100,
				align: "right",
				hidden:!0,
				formatter: "currency",
				formatoptions: {
					showZero: !0,
					decimalPlaces: d
				},
				title: !1,
				hidden: c
			}, {
				name: "amount",
				label: "期初总价",
				index: "amount",
				width: 100,
				align: "right",
				formatter: "currency",
				formatoptions: {
					showZero: !0,
					decimalPlaces: e
				},
				title: !1,
				hidden: c
			},*/ {
				name: "lprice",
				label: "结算价",
				index: "lprice",
				width: 80,
				align: "right",
				title: !1,
				hidden: h
			}, {
				name: "salePrice",
				label: "指导价",
				index: "salePrice",
				width: 80,
				align: "right",
				formatter: "currency",
				title: !1,
				hidden: k
			}, {
				name: "stockType",
				label: "备货类型",
				width: 80,
				hidden:!0,
				fixed: !0,
				title: !1
			}, {
				name: "storageSum",
				label: "当前总库存",
				width: 80,
				fixed: !0,
				classes : 'storage',
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
			}];
		$("#grid").jqGrid({
			url: "../basedata/inventory?action=kzlist&isDelete=2&query=true",
			datatype: "json",
			width: a.w - 10,
			height: a.h - 10,
			altRows: !0,
			gridview: !0,
			onselectrow: !1,
			colModel: l,
			pager: "#page",
			viewrecords: !0,
			multiselect: !0,
			cmTemplate: {
				sortable: !1
			},
			rowNum: 50,
			rowList: [50, 100, 200],
			shrinkToFit: !1,
			forceFit: !0,
			jsonReader: {
				root: "data.rows",
				records: "data.records",
				total: "data.total",
				repeatitems: !1,
				id: "id"
			},
			loadComplete: function(a) {
				if (a && 200 == a.status) {
					var b = {};
					a = a.data;
					for (var c = 0; c < a.rows.length; c++) {
						var d = a.rows[c];
						b[d.id] = d
					}
					$("#grid").data("gridData", b)
				}
			},
			loadError: function() {
				parent.Public.tips({
					type: 1,
					content: "操作失败了哦，请检查您的网络链接！"
				})
			},
			resizeStop: function(a, b) {
				j.setGridWidthByIndex(a, b, "grid")
			}
		}).navGrid("#page", {
			edit: !1,
			add: !1,
			del: !1,
			search: !1,
			refresh: !1
		})
	}
	function c() {
		$this = this,$_matchCon = $("#matchCon"), $_matchCon.placeholder(),
		$("#storage").on("click",function(){
			$("#search").click();
		}),	$("#search").on("click", function(a) {
			a.preventDefault();
			queryConditions.assistId = $("#currentCategory").data("id"),
			queryConditions.skey = "商品名称/商品编码/商品品牌/车型/vin码" === $this.$_matchCon.val() ? "" : $this.$_matchCon.val(),
			queryConditions.models = $this.modelsCombo.getValue(),
			queryConditions.mYear = $this.matchYearCombo.getValue(),
			queryConditions.displacement =  $this.displacementsCombo.getValue(),
			queryConditions.stock = $this.stockCombo.getValue(),
			queryConditions.bussType = $bussType,
			queryConditions.storage = $("#storage").prop("checked");
			$("#grid").jqGrid("setGridParam", {
				page: 1,
				postData: queryConditions
			}).trigger("reloadGrid")
		}), $("#btn-add").on("click", function(a) {
			a.preventDefault(), Business.verifyRight("INVENTORY_ADD") && h.operate("add")
		}), $("#btn-print").on("click", function(a) {
			a.preventDefault()
		}), $("#btn-import").on("click", function(a) {
			a.preventDefault(), Business.verifyRight("BaseData_IMPORT") && parent.$.dialog({
				width: 560,
				height: 300,
				title: "批量导入",
				content: "url:../import",
				lock: !0
			})
		}),$("#btn-goods-import").on("click",function(a){
			a.preventDefault(),Business.verifyRight("BaseData_IMPORT") && $.dialog({
				width: 560,
				height: 300,
				title: "快准商品期初导入",
				content: "url:"+CONFIG.SITE_URL+"/basedata/invlocation/goodsInitImport",
				lock: !0
			})
		}),$("#btn-goods-price").on("click",function(a){
			a.preventDefault(),Business.verifyRight("BaseData_IMPORT") && $.dialog({
				width: 560,
				height: 300,
				title: "快准商品售价导入",
				content: "url:"+CONFIG.SITE_URL+"/basedata/invlocation/goodsPriceImport",
				lock: !0
			})
		}), $("#btn-export").on("click", function() {
			if (Business.verifyRight("INVENTORY_EXPORT")) {
				var a = "按商品编号，商品名称，规格型号等查询" === $_matchCon.val() ? "" : $.trim($_matchCon.val()),
					b = $("#currentCategory").data("id") || "";
				$(this).attr("href", "../basedata/inventory/exporter?action=exporter&isDelete=2&skey=" + a + "&assistId=" + b)
			}
		}), $("#grid").on("click", ".operating .ui-icon-pencil", function(a) {
			if (a.stopPropagation(),a.preventDefault(), Business.verifyRight("INVENTORY_UPDATE")) {
				var b = $(this).parent().data("id");
				h.operate("edit", b)
			}
		}),$("#grid").on("click", ".operating .set-price", function(a) {
			if (a.stopPropagation(),a.preventDefault(), Business.verifyRight("INVENTORY_UPDATE")) {
				var b = $(this).parent().data("id");
				var url = "goodsKz_manage";
				h.operate("edit", b,'配置商品价格',url,300,300)
			}
		}),$("#grid").on("click", ".operating .set-first", function(a) {
			if (a.stopPropagation(),a.preventDefault(), Business.verifyRight("INVENTORY_UPDATE")) {
				var b = $(this).parent().data("id");
				var url = "goodsKz_set";
				h.operate("edit", b,'期初设置',url,900,500)
			}
		}),$("#grid").on("click", ".operating .set-storage", function(a) {
			if (a.stopPropagation(),a.preventDefault(), Business.verifyRight("INVENTORY_UPDATE")) {
				var b = $(this).parent().data("id");
				var url = "goodsKz_storage?item_id="+b;
				h.operate("edit", b,'配置默认货位',url,480,480)
			}
		}),$("#grid").on("click", ".operating .ui-icon-trash", function(a) {
			if (a.stopPropagation(),a.preventDefault(), Business.verifyRight("INVENTORY_DELETE")) {
				var b = $(this).parent().data("id");
				h.del(b + "")
			}
		}), $("#grid").on("click", ".operating .ui-icon-pic", function(a) {
			a.preventDefault();
			var b = $(this).parent().data("id"),
				c = "商品图片";
			$.dialog({
				content: "url:../settings/fileUpload",
				data: {
					title: c,
					id: b,
					callback: function() {}
				},
				title: c,
				width: 775,
				height: 470,
				max: !1,
				min: !1,
				cache: !1,
				lock: !0
			})
		}), $("#btn-batchDel").click(function(a) {
			if (a.preventDefault(), Business.verifyRight("INVENTORY_DELETE")) {
				var b = $("#grid").jqGrid("getGridParam", "selarrrow");
				b.length ? h.del(b.join()) : parent.Public.tips({
					type: 2,
					content: "请选择需要删除的项"
				})
			}
		}), $("#btn-disable").click(function(a) {
			a.preventDefault();
			var b = $("#grid").jqGrid("getGridParam", "selarrrow").concat();
			return b && 0 != b.length ? void h.setStatuses(b, !0) : void parent.Public.tips({
				type: 1,
				content: " 请先选择要禁用的商品！"
			})
		}), $("#btn-enable").click(function(a) {
			a.preventDefault();
			var b = $("#grid").jqGrid("getGridParam", "selarrrow").concat();
			return b && 0 != b.length ? void h.setStatuses(b, !1) : void parent.Public.tips({
				type: 1,
				content: " 请先选择要启用的商品！"
			})
		}), $("#hideTree").click(function(a) {
			a.preventDefault();
			var b = $(this),
				c = b.html();
			"&gt;&gt;" === c ? (b.html("&lt;&lt;"), g = 0, $("#tree").hide(), Public.resizeGrid(f + 10, g)) : (b.html("&gt;&gt;"), g = 270, $("#tree").show(), Public.resizeGrid(f, g))
		}), $("#grid").on("click", ".set-status", function(a) {
			if (a.stopPropagation(), a.preventDefault(), Business.verifyRight("INVLOCTION_UPDATE")) {
				var b = $(this).data("id"),
					c = !$(this).data("delete");
				h.setStatus(b, c)
			}
		}), $(window).resize(function() {
			Public.resizeGrid(f + 10 , g), $(".innerTree").height($("#tree").height() - 90)
		}), Public.setAutoHeight($("#tree")), $(".innerTree").height($("#tree").height() - 90)
	}
	var d = (parent.SYSTEM, Number(parent.SYSTEM.qtyPlaces), Number(parent.SYSTEM.pricePlaces)),
		e = Number(parent.SYSTEM.amountPlaces),
		f = 95,
		g = 270,
		h = {
			operate: function(a, b, c ,url,width,height) {
				if ("add" == a) var c = "新增商品",
					d = {
						oper: a,
						callback: this.callback
					};
				else
					d = {
						oper: a,
						rowId: b,
						callback: this.callback_p
					};
				var e = 400;
				_h = 480, $.dialog({
					title: c,
					content: "url:" + url,
					data: d,
					width: width,
					height: height,
					max: !1,
					min: !1,
					cache: !1,
					lock: !0
				})
			},
			del: function(a) {
				$.dialog.confirm("删除的商品将不能恢复，请确认是否删除？", function() {
					Public.ajaxPost("../basedata/inventory/delete?action=delete", {
						id: a
					}, function(b) {
						if (b && 200 == b.status) {
							var c = b.data.id || [];
							parent.Public.tips({
								content: "成功删除个商品！"
							});
							for (var d = 0, e = c.length; e > d; d++) $("#grid").jqGrid("setSelection", c[d]), $("#grid").jqGrid("delRowData", c[d])
						} else parent.Public.tips({
							type: 1,
							content: "删除商品失败！" + b.msg
						})
					})
				})
			},
			setStatus: function(a, b) {
				a && Public.ajaxPost("../basedata/inventory/disable?action=disable", {
					invIds: a,
					disable: Number(b)
				}, function(c) {
					c && 200 == c.status ? (parent.Public.tips({
						content: "商品状态修改成功！"
					}), $("#grid").jqGrid("setCell", a, "delete", b)) : parent.Public.tips({
						type: 1,
						content: "商品状态修改失败！" + c.msg
					})
				})
			},
			setStatuses: function(a, b) {
				if (a && 0 != a.length) {
					var c = $("#grid").jqGrid("getGridParam", "selarrrow"),
						d = c.join();
					Public.ajaxPost("../basedata/inventory/disable?action=disable", {
						invIds: d,
						disable: Number(b)
					}, function(c) {
						if (c && 200 == c.status) {
							parent.Public.tips({
								content: "商品状态修改成功！"
							});
							for (var d = 0; d < a.length; d++) {
								var e = a[d];
								$("#grid").jqGrid("setCell", e, "delete", b)
							}
						} else parent.Public.tips({
							type: 1,
							content: "商品状态修改失败！" + c.msg
						})
					})
				}
			},
			callback: function(a, b, c) {
				var d = $("#grid").data("gridData");
				d || (d = {}, $("#grid").data("gridData", d)), d[a.id] = a, "edit" == b ? ($("#grid").jqGrid("setRowData", a.id, a), c && c.api.close()) : ($("#grid").jqGrid("addRowData", a.id, a, "last"), c && c.resetForm(a))
			},
			callback_p: function(c) {
				//alert(3);
				c && c.api.close();
			}

		},
		i = {
			money: function(a) {
				var a = Public.numToCurrency(a);
				return a || "&#160;"
			},
			currentQty: function(a) {
				if ("none" == a) return "&#160;";
				var a = Public.numToCurrency(a);
				return a
			},
			quantity: function(a) {
				var a = Public.numToCurrency(a);
				return a || "&#160;"
			},
			statusFmatter: function(a, b, c) {
				if(c.isSelf == 1) return "";
				var d = a === !0 ? "已禁用" : "已启用",
					e = a === !0 ? "ui-label-default" : "ui-label-success";
				return '<span class="set-status ui-label ' + e + '" data-delete="' + a + '" data-id="' + c.id + '">' + d + "</span>"
			}
		},
		j = Public.mod_PageConfig.init("goodsListKz");
	b(), a(), c()
});