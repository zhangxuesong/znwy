define(['core','tpl'], function(core, tpl) {
	var modal = {
		page: 1,
		sid: 0,
		cate:0,
		ctime:'',
		compid:0,
		today:0,
		keyword:'',
		cfrom:''
	};	
	modal.init = function(params) {
		modal.sid = params.sid;
		modal.cate = params.cate;	
		modal.ctime = params.ctime;	
		modal.compid = params.compid;
		modal.today = params.today;
		modal.keyword = params.keyword;
		modal.cfrom = params.cfrom;
		$(".icon-scan").on('click',function(){			
			modal.myscan();			
		});	
		var reqParams = ['rhui.picker'];
		require(reqParams, function() {			
			$('#ctime').datePicker()
		});	
		$(".onekey").on('click',function(){	
			RhUI.confirm('确定一键群发消息?', function() {
				modal.postsend();		
			});
		});	
		$('.fui-content').infinite({
            onLoading: function () {
                modal.getExpress()
            }
        });
				
        if (modal.page == 1) {
            modal.getExpress()
        }
	};
	modal.getExpress = function() {	
		core.json('express/express', {
			page: modal.page,
			sid: modal.sid,
			cate: modal.cate,
			ctime: modal.ctime,
			compid:modal.compid,
			today:modal.today,
			keyword:modal.keyword,
			cfrom:modal.cfrom
		}, function(ret) {
			if (ret.status == 0) {
				$('.content-empty').show();
				$('.onekey').hide();
				$('.fui-content').infinite('stop')
				return
			}
			var result = ret.result;
			if (result.total <= 0) {
				$('.content-empty').show();
				$('.onekey').hide();
				$('.fui-content').infinite('stop')
			} else {
				$('.content-empty').hide();
				$('.onekey').show();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop')					
				}
			}
			modal.page++;
			core.tpl('.container', 'tpl_express_list', result, modal.page > 1);	
			modal.bindEvents(modal.cate);
		})
	};
	modal.initTab = function(params) {
		modal.sid = params.sid;
		modal.cate = params.cate;	
		modal.ctime = params.ctime;		
		modal.compid = params.compid;
		modal.today = params.today;
		modal.keyword = params.keyword;
		modal.cfrom = params.cfrom;
		var reqParams = ['rhui.picker'];
		require(reqParams, function() {			
			$('#ctime').datePicker()
		});	
		$(".icon-scan").on('click',function(){			
			modal.myscan();			
		});	
		$('.fui-content').infinite({
            onLoading: function () {
				if(modal.cate==1){
					modal.getList1();
				}
				if(modal.cate==2){
					modal.getList2();
				}
            }
        });
        if (modal.page == 1) {
          	if(modal.cate==1){
				modal.getList1();
			}
			if(modal.cate==2){
				modal.getList2();
			}
        }
		
		RhUI.tab({
			container: $('#tab'),
			handlers: {			
				tab1: function() {
					modal.changeTab(1)
				},
				tab2: function() {
					modal.changeTab(2)
				}
			}
		});
	};
	modal.changeTab = function(cate) {
		$('.fui-content-inner').infinite('init');
		$('.content-empty').hide();
		$('.container').html('');
		$('.infinite-loading').show();
		modal.page = 1;
		modal.cate = cate;		
		$("#cate").val(cate);
		modal.ctime = '';
		$("#ctime").val('');
		if(modal.cate==1){
			modal.getList1();
		}
		if(modal.cate==2){
			modal.getList2();
		}
	};
	modal.getList1 = function() {		
		core.json('express/tabexpress', {
			page: modal.page,
			sid: modal.sid,
			cate: modal.cate,
			ctime: modal.ctime,
			compid:modal.compid,
			today:modal.today,
			keyword:modal.keyword,
			cfrom:modal.cfrom
		}, function(ret) {
			if (ret.status == 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop')
				return
			}
			var result = ret.result;
			if (result.total <= 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop')
			} else {
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop')					
				}
			}
			modal.page++;
			core.tpl('.container', 'tpl_express_send_list', result, modal.page > 1);			
		})
	};
	modal.getList2 = function() {		
		core.json('express/tabexpress', {
			page: modal.page,
			sid: modal.sid,
			cate: modal.cate,
			ctime: modal.ctime,
			compid:modal.compid,
			today:modal.today,
			keyword:modal.keyword,
			cfrom:modal.cfrom
		}, function(ret) {
			if (ret.status == 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop');
				return
			}
			var result = ret.result;
			if (result.total <= 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop');
			} else {
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop');				
				}
			}
			modal.page++;
			core.tpl('.container', 'tpl_express_take_list', result, modal.page > 1);			
		})
	};
	modal.bindEvents = function(cate) {
		$('*[data-toggle=take]').unbind('click').click(function() {
			var obj = $(this);					
			var id = obj.data('id');
			var sn = obj.data('sn');
			RhUI.confirm('取件码'+sn+',确认快件已取?', function() {
				core.json('express/posttake', {
					id: id,
					sid: modal.sid
					}, function(ret) {
						if (ret.status == 0) {
							RhUI.toast.show(ret.result.message);					
							return
						}
						obj.parents(".fui-card").remove();	
						RhUI.toast.show(ret.result.message);
					},false, true)
			})	
		});	
		$('*[data-toggle=msg]').unbind('click').click(function() {
			var obj = $(this);					
			var id = obj.data('id');
			var notice = obj.data('notice');
			RhUI.confirm('确认发送消息?', function() {
				core.json('express/postsend', {
					id: id ,
					sid: modal.sid,
					cate:modal.cate
					}, function(ret) {
						if (ret.status == 0) {
							RhUI.toast.show(ret.result.message);					
							return
						}
						if(notice==1){
							obj.parents(".fui-card").remove();	
						}
						RhUI.toast.show(ret.result.message);
						
					},false, true)
			})
		});	
		$('*[data-toggle=store]').unbind('click').click(function() {
			var obj = $(this);					
			var id = obj.data('id');
			var info = obj.data('info');
			var price = obj.data('price');
			var sn = obj.data('sn');
			$('.pop-express-hidden').show();
			$('.expressinfo').html(info);
			$('#expressprice').hide();
			$('#expresssn').val(sn);
			$("#expresssn").focus();
			$('.verify-pop').find('.btn-close').unbind('click').click(function () {
                $(".pop-express-hidden").hide();
            }); 
			$('.verify-pop').find('.btn-submit').unbind('click').click(function () {				
				var price = $('#price').val();
				var expresssn = $('#expresssn').val();
				if($.isEmpty(expresssn)){
					RhUI.toast.show('快件编号不能为空');
					return
				}
				core.json('express/postprice', {
					'id': id,
					'sid': modal.sid,
					'price': price,
					'expresssn':expresssn
					}, function(ret) {
						if (ret.status == 0) {
							RhUI.toast.show(ret.result.message);					
							return
						}
						RhUI.toast.show(ret.result.message);
						$(".pop-express-hidden").hide();
						$('#price').val('');
						$('#expresssn').val('');
						obj.data('price',price);
						obj.data('sn',expresssn);
						obj.html('<i class="icon icon-store text-success"></i> 已接单');
					}, true, true);			
			});					
		});	
		$('*[data-toggle=price]').unbind('click').click(function() {
			var obj = $(this);					
			var id = obj.data('id');
			var info = obj.data('info');
			var price = obj.data('price');
			var sn = obj.data('sn');
			$('.pop-express-hidden').show();
			$('.expressinfo').html(info);
			$('#expressprice').show();
			$("#expresssn").focus();
			if(!$.isEmpty(price) && price !="0.00"){
				$('#price').val(price);
			}
			$('#expresssn').val(sn);
			$('.verify-pop').find('.btn-close').unbind('click').click(function () {
                $(".pop-express-hidden").hide();
            }); 
			$('.verify-pop').find('.btn-submit').unbind('click').click(function () {				
				var price = $('#price').val();
				var expresssn = $('#expresssn').val();
				if($.isEmpty(expresssn)){
					RhUI.toast.show('快件编号不能为空');
					return
				}
				if($.isEmpty(price)){
					RhUI.toast.show('费用不能为空');
					return
				}
				if(!$.isNumber(price)){
					RhUI.toast.show('费用输入不正确');
					return
				}

				core.json('express/postprice', {
					'id': id,
					'sid': modal.sid,
					'price': price,
					'expresssn':expresssn
					}, function(ret) {
						if (ret.status == 0) {
							RhUI.toast.show(ret.result.message);					
							return
						}
						RhUI.toast.show(ret.result.message);
						$(".pop-express-hidden").hide();
						$('#price').val('');
						$('#expresssn').val('');
						obj.data('price',price);
						obj.data('sn',expresssn);
						obj.html('<i class="icon icon-money text-success"></i> 已补价');
					}, true, true);			
			});					
		});	
	};
	modal.myscan = function() {
		wx.scanQRCode({
			needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
			scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
			success: function (res) {
				var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果	
				var arr = result.split(",");
				var expresssn = arr[1];
				if($.isEmpty(expresssn)){
					$("#keyword").val(result);
				}
				else{
					$("#keyword").val(expresssn);
				}
				$("#myform").submit();
			}
		});
	};
	modal.postsend = function() {
		$(".container .fui-card-list").each(function(){
			var obj = $(this);					
			var id = obj.data('id');
			var notice = obj.data('notice');
			core.json('express/postsend', {
				id: id ,
				sid: modal.sid,
				cate:modal.cate
				}, function(ret) {
					if (ret.status == 0) {
						RhUI.toast.show(ret.result.message);					
						return
					}
					if(notice==1){
						obj.parents(".fui-card").remove();	
					}
				//	RhUI.toast.show(ret.result.message);					
				},false, true);
		});
	};
	modal.initSmslog = function(params) {
		modal.sid = params.sid;
		modal.keyword = params.keyword;	
		$('.fui-content').infinite({
            onLoading: function () {
				modal.getsmslog();	
            }
        });
        if (modal.page == 1) {
			modal.getsmslog();		
        }
	}
	modal.getsmslog = function() {		
		core.json('express/smslog', {
			page: modal.page,
			sid: modal.sid,
			keyword:modal.keyword
		}, function(ret) {			
			var result = ret.result;
			if (result.total <= 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop')
			}
			else {
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop')					
				}
			}
			modal.page++;
			core.tpl('.container', 'tpl_smslog_list', result, modal.page > 1);			
		})
	};
	modal.initStat = function(params) {
		var reqParams = ['rhui.picker'];
		require(reqParams, function() {			
			$('#startdate').datePicker();
			$('#enddate').datePicker();
		});	
	}
	return modal
});