define(['core', 'tpl'], function (core, tpl) {
    var modal = {page: 1, keyword: '',lat:'',lng:'',io:1,sid:0,range:0};
    modal.init = function (params) {
        modal.keyword = params.keyword ? params.keyword : '' ;	
		modal.range = params.range ? params.range : 500;
		modal.lat = params.lat ? params.lat :'';
		modal.lng = params.lng ? params.lng : '';
		
        $('.fui-content').infinite({
            onLoading: function () {
                modal.getList()
            }
        });
        if (modal.page == 1) {
            modal.getList()
        }
    };
	modal.initStore = function (params) {
        modal.keyword = params.keyword ? params.keyword : '' ;		
		modal.lat = params.lat ? params.lat :'';
		modal.lng = params.lng ? params.lng : '';
		
        $('.fui-content').infinite({
            onLoading: function () {
                modal.getStore()
            }
        });
        if (modal.page == 1) {
            modal.getStore()
        }
    };
    modal.getList = function () {
            var _this = this;            
            core.json('expressp/list', {page: modal.page, keyword: modal.keyword, lat: modal.lat, lng: modal.lng,range: modal.range}, function (ret) {
                var result = ret.result;
				var content = $(".container").html();  
                if (result.total <= 0) {
					if(content == null || content.length == 0) {
						$('.content-empty').show();
					}
                    $('.fui-content').infinite('stop')
                } else {
                    $('.content-empty').hide();
                    $('.container').show();
                    $('.fui-content').infinite('init');
                    if (result.list.length <= 0 || result.list.length < result.pagesize) {
                        $('.fui-content').infinite('stop')
                    }
                }
                modal.page++;
                core.tpl('.container', 'tpl_region_list', result, modal.page > 1);
            }, false, true );
    }; 
	modal.getStore = function () {
            var _this = this;            
            core.json('expressp/selectstore', {page: modal.page, keyword: modal.keyword, lat: modal.lat, lng: modal.lng}, function (ret) {
                var result = ret.result;
				var content = $(".container").html();  
                if (result.total <= 0) {
					if(content == null || content.length == 0) {
						$('.content-empty').show();
					}
                    $('.fui-content').infinite('stop')
                } else {
                    $('.content-empty').hide();
                    $('.container').show();
                    $('.fui-content').infinite('init');
                    if (result.list.length <= 0 || result.list.length < result.pagesize) {
                        $('.fui-content').infinite('stop')
                    }
                }
                modal.page++;
                core.tpl('.container', 'tpl_region_list', result, modal.page > 1);
            }, false, true );
    }; 
    modal.initAddress = function (params) {
        modal.keyword = params.keyword ? params.keyword : '' ;		
		modal.io = params.io ? params.io :1;
		modal.sid = params.sid ? params.sid :0;
        $('.fui-content').infinite({
            onLoading: function () {
                modal.getAddress()
            }
        });
        if (modal.page == 1) {
            modal.getAddress()
        }
    };
	modal.getAddress = function() {		
		core.json('expressp/address', {
			page: modal.page,
			sid: modal.sid,
			io: modal.io,
			keyword: modal.keyword
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
			core.tpl('.container', 'tpl_address_list', result, modal.page > 1);			
			modal.bindEvents();
		})
	};
   	modal.bindEvents = function() {
		$('*[data-toggle=delete]').unbind('click').click(function() {			
			var item = $(this).closest('.address-item');
			var aid = item.data('addressid');
			RhUI.confirm('删除后无法恢复, 确认要删除吗 ?', function() {
				core.json('expressp/deladdress', {
					aid: aid
				}, function(ret) {
					if (ret.status == 1) {						
						item.remove();
						setTimeout(function() {
							if ($(".address-item").length <= 0) {
								$('.content-empty').show()
							}
						}, 100);
						return
					}
					RhUI.toast.show(ret.result.message)
				}, true, true)
			})								
		});	
		$('*[data-toggle=edit]').unbind('click').click(function() {			
			var item = $(this).closest('.address-item');
			var aid = item.data('addressid');
			location.href = core.getUrl('expressp/editaddress') + "&aid=" + aid;							
		});	
	};	
	modal.initPostadd = function (params) {	
		modal.io = params.io;
		$('#copyaddress').bind('input blur', function() {			
			var address = $("#copyaddress").val();
			 core.json('expressp/resolveadd', {address:address}, function(rjson) {
				var address = rjson.result.address; 
				if (rjson.status == 1) {
					 $("#mobile").val(address.mobile);
					 $("#realname").val(address.realname);
					 $("#address").val(address.address);
					 $("#mobile").val(address.mobile);
					 $("#areas").val(address.province + ' ' + address.city + ' ' + address.area);
				}
			 });
		});
		$('.icon-record').on('touchstart', function(event){
			event.preventDefault();
			START = new Date().getTime();			
			recordTimer = setTimeout(function(){
				wx.startRecord({
					success: function(){
						RhUI.loader.show('录音中','icon icon-voicefill');
						localStorage.rainAllowRecord = 'true';
					},
					cancel: function () {
						RhUI.toast.show('用户拒绝授权录音');
					}
				});
			},300);
		});
		//松手结束录音
		$('.icon-record').on('touchend', function(event){
			RhUI.loader.hide();
			event.preventDefault();
			END = new Date().getTime();			
			if((END - START) < 300){
				END = 0;
				START = 0;
				//小于300ms，不录音
				clearTimeout(recordTimer);
			}
			else{
				wx.stopRecord({
				  success: function (res) {
					wx.translateVoice({
						localId: res.localId, // 需要识别的音频的本地Id，由录音相关接口获得
						isShowProgressTips: 1, // 默认为1，显示进度提示
						success: function (res) {
							var addresss = res.translateResult;
							var address = addresss.replace(/。/g,'');
							$("#address").val(address);
						}
				   });
				  },
				  fail: function (res) {
					RhUI.toast.show(res.errMsg);
				  }
				});
			}
		});
		$("#btnSubmit").click(function () {
			if ($('#btnSubmit').attr('stop')) {
				return
			}			
			if($('#realname').val()==""){
			   RhUI.toast.show('姓名不能为空');
			   return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}	
			if($('#areas').val()==""){
			   RhUI.toast.show('地区不能为空');
			   return
			}
			if($('#address').val()==""){
			   RhUI.toast.show('详细地址不能为空');
			   return
			}
			$('#btnSubmit').html('正在保存...').attr('stop', 1);
			core.json('expressp/postaddress', {
				mobile: $('#mobile').val(),
				realname: $('#realname').val(),
				areas: $('#areas').val(),
				address: $('#address').val(),
				io: modal.io,
				aid:$('#addressid').val()
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#btnSubmit').html('保存地址').removeAttr('stop');
					return
				}
				RhUI.alert(ret.result.message, '', function() {					
					location.href = core.getUrl('expressp/address') + "&io=" + modal.io;					
				})
			}, true, true)
		})
    };
	modal.initSend = function (params) {
		$("#pickerspec").unbind('click').click(function () {
			$(".pop-express-hidden").show();			
        })
		$('.closebtn').unbind('click').click(function () {
				$(".pop-express-hidden").hide();
            }); 
			
		$(".minus").unbind('click').click(function () {
			var qty = $("#qty").val();	
			if(qty>1){
				qty = qty - 1;
				$("#qty").val(qty);
			}
        })
		$(".plus").unbind('click').click(function () {
			var qty = $("#qty").val();
			if(!$.isNumber(qty)){
				qty = 0;
			}
			qty =  qty*1 + 1;
			$("#qty").val(qty);	
        })
		$(".category-type a").on("click", function() {
			if ($(this).hasClass("active")) {
				return
			} else {
				$(".category-type a").removeClass("active");
				$(this).addClass("active");
				$("#title").val($(this).attr("data-label"))
			}
		});
		$('.btnsubmit').unbind('click').click(function () {
			var paytype = $.trim($("input[name=paytype]:checked").val());	
			paytype = paytype==1?'到付':'寄付';
			var title = $('#title').val();
			var weight = $('#weight').val();
			var qty = $('#qty').val();
			if(title==""){
				RhUI.toast.show('物品类别不能为空');
				return
			}
			if(weight=="" || weight==0){
				RhUI.toast.show('物品重量必须大于0');
				return
			}				
			$("#selectpicker").html(paytype +' '+ title + '*'+ qty + ' ' + weight + 'kg');
			$(".pop-express-hidden").hide();
		});
		
		$("#sendorder").click(function () {
			if ($('#sendorder').attr('stop')) {
				return
			}
			var paytype = $.trim($("input[name=paytype]:checked").val());	
			var sid = $('#sid').val();	
			var fromaid = $('#fromaid').val();
			var toaid = $('#toaid').val();
			var compid = $('#compid').val();
			var paytype = $.trim($("input[name=paytype]:checked").val());	
			var title = $('#title').val();
			var weight = $('#weight').val();
			var qty = $('#qty').val();			
			if(fromaid==""){
				RhUI.toast.show('寄件地址不能为空');
			    return
			}
			if(toaid==""){
				RhUI.toast.show('收件地址不能为空');
			    return
			}
			if(compid==""){
				RhUI.toast.show('请选择快递公司');
			    return
			}
			if(title==""){
				RhUI.toast.show('物品类别不能为空');
				return
			}
			if(weight=="" || weight==0){
				RhUI.toast.show('物品重量必须大于0');
				return
			}				
			$('#sendorder').html('正在下单...').attr('stop', 1);
			core.json('expressp/sendexpress', {
				sid: sid,
				fromaid: fromaid,
				toaid: toaid,
				compid: compid,
				remark: $('#remark').val(),
				paytype: paytype,
				title:title,
				weight:weight,
				qty:qty				
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					$('#sendorder').html('下单').removeAttr('stop');
					return
				}
				RhUI.alert(ret.result.message, '', function() {					
					location.href = core.getUrl('expressp/myexpress')+'&cate=1';					
				})
			}, true, true)
		})
    };
    return modal
});