define(['core', 'tpl'], function (core, tpl) {
    var modal = {
		sid:0,
		page:1,
		cfrom:'',
		op:'add'
	};
    modal.init = function (params) {
		modal.sid = params.sid;		
        core.json('express/get_today', {sid:modal.sid}, function (json) {
            if (json.status == 1) {
                $("#today_repair").text(json.result.today_repair);
                $("#today_suggest").text(json.result.today_suggest);
                $("#today_member").text(json.result.today_member)
            }
        });
				
		core.json('express/get_fee', {sid:modal.sid}, function (json) {
            if (json.status == 1) {
                $("#payfee_week").text(json.result.payfee_week);
                $("#payfee_month").text(json.result.payfee_month);
                $("#payfee_today").text(json.result.payfee_today)
            }
        });
		
		core.json('express/get_express', {sid:modal.sid}, function (json) {
            if (json.status == 1) {
                $("#express_take").text(json.result.express_take);
                $("#express_send").text(json.result.express_send);
                $("#express_notice").text(json.result.express_notice)
            }
        });
		$(".icon-scan").on('click',function(){		
			wx.scanQRCode({
				needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
				scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
				success: function (res) {
					var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果	
					if(result.indexOf(",") != -1){
						var arr = result.split(",");
						var expresssn = arr[1];
					}
					else{
						var expresssn = result;
					}			
					core.json('express/takeexpress', {sid:modal.sid,expresssn:expresssn,cfrom:0}, function (ret) {
						if (ret.status == 0) {
							RhUI.toast.show(ret.result.message);					
							return
						}
						RhUI.toast.show(ret.result.message);
					});
				}
			});
		});	
		
		$(".icon-edit").on('click',function(){
			var obj = $(this);					
			$(".pop-takeexpress-hidden").show();
			$("#expresssn").focus();
			$('.verify-pop').find('.btn-close').unbind('click').click(function () {
                $(".pop-takeexpress-hidden").hide();
            }); 
			$('.verify-pop').find('.btn-submit').unbind('click').click(function () {
				var sn = $("#expresssn").val();
				if (sn=='') {
					RhUI.toast.show('请输入取件码');
					$("#expresssn").focus();
					return
				}
				core.json('express/takeexpress', {sid:modal.sid,expresssn:sn,cfrom:0}, function (ret) {
					if (ret.status == 0) {
						RhUI.toast.show(ret.result.message);					
						return
					}
					$(".pop-takeexpress-hidden").hide();
					$("#expresssn").val('');
					RhUI.toast.show(ret.result.message);
				});		
			});							
		});	
    };
	modal.initAdd = function (params) {
		modal.sid = params.sid;
		modal.cfrom = params.cfrom;
		modal.op = params.op;
		$('.fui-uploader').click(function(){
			//core.wxUploadImages($(this),"images",core.getUrl('util/wxuploader')+'&nodisplay=1',core.getUrl('util/remove'));	
			var obj = $(this);
			var uploadUrl = core.getUrl('util/yt_imageocr')+'&nodisplay=1';
			var removeUrl = core.getUrl('util/remove');
			var max = 1;		
			var count = $("#images li").length;		
			var images = {
				localIds: [],
			};	
			wx.chooseImage({
				count: 1, // 最多选几张
				sizeType: ['compressed'], // 可以指定是原图还是压缩图，默认二者都有
				sourceType: ['album','camera'], // 可以指定来源是相册还是相机，默认二者都有
				success: function(res) {
					images.localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
					var i = 0; var length = images.localIds.length;
					var upload = function() {
						wx.uploadImage({
							localId:'' + images.localIds[i],
							isShowProgressTips: 1,
							success: function(res) {
								var serverId = res.serverId;									
								$.ajax({   
									 url:uploadUrl,   
									 type:'post', 
									 data:{
										media_id:serverId,
									 },
									 dataType:'json',
									 success:function(data){ 										
										if (data.error == 1) {
											RhUI.toast.show(data.message);
										} else {
											$("#expresssn").val(data.expresssn);
											$("#mobile").val(data.mobile);
											$("#remark").val(data.remark);
											var removeHTML = "";
											  removeHTML += '<span class="image-remove">';
											  removeHTML += "<i class='icon icon-roundclose'></i>";
											  removeHTML += '</span>';												
											 var imageHTML = '<li style="background-image:url(\'' + data.imgurl + '\')" class="image image-sm" data-filename="' + data.realimgurl + '">' + removeHTML + '</li>';
											
											$("#images").append(imageHTML);
											count++;
											if(max<=count){
												obj.hide();
											}
											$('.image-remove').unbind('click').click(function () {												
												core.removeMyImages($(this),removeUrl);				
											});
											if(data.mobile==''){
												RhUI.toast.show(data.message);
											}
										}  
									 }
								});								
							}
						});                    
					};
					upload();
				}
			}); 
		});
		
		$(".icon-scan").on('click',function(){			
			modal.myscan();			
		});	
		
		$('#mobile').bind('input propertychange', function() {						
			var that = $(this);
			var mobile = that.val();
			if(mobile.length>2 && mobile.length<11){				
				 core.json('express/get_mobile', {sid:modal.sid,mobile:mobile}, function(rjson) {
					var result = rjson.result;
					if (rjson.status == 1) {						
						if (result.total <= 0) {
							$('#mobilelist').hide();					
						}
						else{
							$('#mobilelist').show();	
							core.tpl('#mobilelist','tpl_mobile_list',result,false);
							$('.selectmobile').unbind('click').click(function() {										
								var selectmobile = $(this).data('mobile');																																								
								$('#mobilelist').hide();
								that.val(selectmobile);							
								$("#expresssn").focus();
							});
						}												
					}					
				 });
			}
			else{
				$('#mobilelist').hide();
			}
		});	
	
		$('.icon-record').on('touchstart', function(event){
			RhUI.loader.hide();
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
							var mobiles = res.translateResult;
							var mobile = mobiles.replace(/。/g,'');
						//	RhUI.toast.show(mobile);
							$("#mobile").val(mobile);
						}
				   });
				   // uploadVoice();
				  },
				  fail: function (res) {
					RhUI.toast.show(res.errMsg);
				  }
				});
			}
		});
		
		$(".category-type a").on("click", function() {
			if ($(this).hasClass("active")) {
				return
			} else {
				$(".category-type a").removeClass("active");
				$(this).addClass("active");
				$("#category_type").val($(this).attr("data-label"))
			}
		});
		
		$('.btn-submit').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			if ($('#compid').isEmpty()) {
				RhUI.toast.show('请选择快递公司');
				return
			}	
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				$("#mobile").focus();
				return
			}
			if ($('#expresssn').isEmpty()) {
				RhUI.toast.show('快件单号不能为空');
				$("#expresssn").focus();
				return
			}			
			var expresssn = $("#expresssn").val();
			var mobile = $('#mobile').val();
			var compid = $('#compid').val();
			var remark = $('#remark').val();
			var cabstloca = $('#cabstloca').val();
			var label = $('#category_type').val();
			var images = [];
			$('#images').find('li').each(function() {
				images.push($(this).data('filename'))
			});
						
			$(this).html('正在处理...').attr('stop', 1);
			core.json('express/' + modal.op, {
				'sid': modal.sid,
				'mobile': mobile,
				'expresssn': expresssn,
				'images': images,
				'cabstloca':cabstloca,
				'remark': remark,
				'compid': compid,
				'label':label,
				'cfrom':modal.cfrom,
				'expressid':$('#expressid').val()
			}, function(ret) {
				if (ret.status == 0) {
					$('.btn-submit').removeAttr('stop').html('提交');
					RhUI.toast.show(ret.result.message);					
					return
				}
				else if(ret.status == 1){
					RhUI.toast.show('提交成功');
					if(modal.cfrom=='store' || modal.cfrom=='employee'){
						setTimeout(function(){
							location.href = core.getUrl('express/add') + '&sid=' + modal.sid;
						},1000);	
					}
					else{
						setTimeout(function(){
							location.href = core.getUrl('express/adde') + '&sid=' + modal.sid + '&compid=' + compid;
						},1000);	
					}
				}				
				else if(ret.status == 2){
					RhUI.toast.show('提交成功');
					if(modal.cfrom=='store' || modal.cfrom=='employee'){
						setTimeout(function(){
							location.href = core.getUrl('express/express') + '&sid=' + modal.sid + '&cate=2';
						},1000);	
					}
					else{
						setTimeout(function(){
							location.href = core.getUrl('express/expresse') + '&sid=' + modal.sid + '&compid=' + compid + + '&cate=2';
						},1000);	
					}
				}
			}, true, true);		
		});        
    };
	modal.initAddwap = function (params) {
		modal.sid = params.sid;
		modal.cfrom = params.cfrom;
		modal.op = params.op;
		$('.fui-uploader').uploader({
			uploadUrl: core.getUrl('util/uploader')+'&nodisplay=1',
			removeUrl: core.getUrl('util/remove')
		});
		
		$(".icon-scan").on('click',function(){			
			modal.myscan();			
		});	
		$(".category-type a").on("click", function() {
			if ($(this).hasClass("active")) {
				return
			} else {
				$(".category-type a").removeClass("active");
				$(this).addClass("active");
				$("#category_type").val($(this).attr("data-label"))
			}
		});
		$('.btn-submit').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			if ($('#compid').isEmpty()) {
				RhUI.toast.show('请选择快递公司');
				return
			}	
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码!');
				$("#mobile").focus();
				return
			}
			if ($('#expresssn').isEmpty()) {
				RhUI.toast.show('快件单号不能为空!');
				$("#expresssn").focus();
				return
			}
			var expresssn = $("#expresssn").val();
			var mobile = $('#mobile').val();
			var compid = $('#compid').val();
			var remark = $('#remark').val();
			var cabstloca = $('#cabstloca').val();
			var label = $('#category_type').val();
			var images = [];
			$('#images').find('li').each(function() {
				images.push($(this).data('filename'))
			});
						
			$(this).html('正在处理...').attr('stop', 1);
			core.json('express/' + modal.op, {			
				'sid': modal.sid,
				'mobile': mobile,
				'expresssn': expresssn,
				'images': images,
				'cabstloca':cabstloca,
				'remark': remark,
				'compid': compid,
				'label':label,
				'cfrom':modal.cfrom,
				'expressid':$('#expressid').val()	
			}, function(ret) {
				if (ret.status == 0) {
					$('.btn-submit').removeAttr('stop').html('提交');
					RhUI.toast.show(ret.result.message);					
				}
				else if(ret.status == 1){
					RhUI.toast.show('提交成功');
					if(modal.cfrom=='store' || modal.cfrom=='employee'){
						setTimeout(function(){
							location.href = core.getUrl('express/add') + '&sid=' + modal.sid;
						},1000);	
					}
					else{
						setTimeout(function(){
							location.href = core.getUrl('express/adde') + '&sid=' + modal.sid + '&compid=' + compid;
						},1000);	
					}
				}				
				else if(ret.status == 2){
					RhUI.toast.show('提交成功');
					if(modal.cfrom=='store' || modal.cfrom=='employee'){
						setTimeout(function(){
							location.href = core.getUrl('express/express') + '&sid=' + modal.sid + '&cate=2';
						},1000);	
					}
					else{
						setTimeout(function(){
							location.href = core.getUrl('express/expresse') + '&sid=' + modal.sid + '&compid=' + compid + + '&cate=2';
						},1000);	
					}
				}
			}, true, true);		
		});        
    };
	modal.initBatchadd = function (params) {
		modal.sid = params.sid;	
		modal.cfrom = params.cfrom;
		modal.bindEvents();
		
		$(".icon-scan").on('click',function(){	
			if ($('#compid').isEmpty()) {
				RhUI.toast.show('请选择快递公司');
				return
			}	
			modal.mybscan();			
		});	
		
		$('#mobile').bind('input propertychange', function() {
			var that = $(this);
			var mobile = that.val();
			if(mobile.length>2 && mobile.length<11){				
				core.json('express/get_mobile', {sid:modal.sid,mobile:mobile}, function(rjson) {
					var result = rjson.result;
					if (rjson.status == 1) {						
						if (result.total <= 0) {
							$('#mobilelist').hide();					
						}
						else{
							$('#mobilelist').show();	
							core.tpl('#mobilelist','tpl_mobile_list',result,false);
							$('.selectmobile').unbind('click').click(function() {										
								var selectmobile = $(this).data('mobile');																
								$('#mobilelist').hide();
								that.val(selectmobile);
								$("#remark").focus();								
							});
						}												
					}					
				});
			}
			else{
				$('#mobilelist').hide();
			}
		});
		
		$(".icon-add").on('click',function(){
			if ($('#compid').isEmpty()) {
				RhUI.toast.show('请选择快递公司');
				return
			}	
			if ($('#expresssn').isEmpty()) {
				RhUI.toast.show('快件单号不能为空');
				$("#expresssn").focus();
				return
			}
			var compid = $('#compid').val();
			var expresssn = $("#expresssn").val();
			
			core.json('express/batchadd', {
				expresssn: expresssn,
				sid: modal.sid,
				compid:compid,
				cfrom:modal.cfrom
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);					
					return
				}
				var html = '<div class="fui-cell"><div class="fui-cell-info expresssn" data-sn="' + expresssn + '" data-mb="">'+ expresssn + '</div>' +				
					'<div class="fui-cell-remark noremark"><i class="icon icon-close text-danger" data-sn="'+expresssn+'"></i></div></div>';			
				$("#expresslist").prepend(html);	
				modal.bindEvents();
				$("#expresssn").val('');
				$("#expresssn").focus();
			}, true, true);				
		});	
		$('.fui-uploader').click(function(){
			core.wxUploadImages($(this),"images",core.getUrl('util/wxuploader')+'&nodisplay=1',core.getUrl('util/remove'));	
		});
        $('.icon-record').on('touchstart', function(event){
			$('#mobile').val("");
			RhUI.loader.hide();
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
							var mobiles = res.translateResult;
							var mobile = mobiles.replace(/。/g,'');
						//	RhUI.toast.show(mobile);
							$("#mobile").val(mobile);
						}
				   });
				   // uploadVoice();
				  },
				  fail: function (res) {
					RhUI.toast.show(res.errMsg);
				  }
				});
			}
		});
		
		$('.icon-voice').on('touchstart', function(event){
			var html = $('#expresslist').html();
			RhUI.loader.hide();
			if($.isEmpty(html)){
				RhUI.toast.show('还未录入快件');
				return
			}
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
		$('.icon-voice').on('touchend', function(event){
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
							var mobiles = res.translateResult;
							var mobile = mobiles.replace(/。/g,'');
							var compid = $('#compid').val();
							if(mobile=='发送'){
								$("#expresslist .expresssn").each(function(){
									var sn = $(this).data("sn");
									var mb = $(this).data("mb");
									var $that = $(this);
									if($.isEmpty(mb)){										
									}
									else{
										core.json('express/send', {
											'expresssn': sn,
											'sid': modal.sid,
											'mobile': mb,
											'compid':compid,
											'cfrom':modal.cfrom
											}, function(ret) {
												if (ret.status == 0) {
													RhUI.toast.show(ret.result.message);					
													return
												}
												RhUI.toast.show(ret.result.message);
												$that.next().html('<i class="icon icon-check text-success"></i>');
												$that.removeClass("expresssn");
												$that.unbind();
											}, true, true);	
									}
								});
							}
							else{
								$("#expresslist .expresssn").each(function(){
									var mb = $(this).data("mb");
									var expresssn = $(this).data("sn");
									if($.isEmpty(mb)){
										$(this).data("mb",mobile);
										var aaaa = expresssn + '<span class="pull-right text-danger" style="margin-right:1rem;">' + mobile + '</span>'
										$(this).html(aaaa);
										core.json('express/savemobile', {
											expresssn: expresssn,
											mobile:mobile,
											sid: modal.sid
											}, function(ret) {
												if (ret.status == 0) {	
													RhUI.toast.show(ret.result.message);
													return
												}													
											}, false, true);	
										return false;
									}
								});
							}
						}
				   });
				   // uploadVoice();
				  },
				  fail: function (res) {
					RhUI.toast.show(res.errMsg);
				  }
				});
			}
		});
    };
	modal.myscan = function() {
		wx.scanQRCode({
			needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
			scanType: ["barCode"], // 可以指定扫二维码还是一维码，默认二者都有
			success: function (res) {
				var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果	
				var arr = result.split(",");
				var expresssn = arr[1];
				if($.isEmpty(expresssn) || expresssn.length<10 ){
				//	RhUI.alert('识别不成功', '', function() {					
						modal.myscan();					
				//	})
					return
				}
				$("#expresssn").val(expresssn);
				$("#remark").focus();				
			}
		});
	}
	modal.mybscan = function() {
		wx.scanQRCode({
			needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
			scanType: ["barCode"], // 可以指定扫二维码还是一维码，默认二者都有
			success: function (res) {
				var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果	
				var arr = result.split(",");
				var expresssn = arr[1];
				if($.isEmpty(expresssn) || expresssn.length<10 ){
				//	RhUI.alert('识别不成功', '', function() {					
						modal.mybscan();					
				//	})
					return
				}
				var compid = $('#compid').val();
				core.json('express/batchadd', {
					expresssn: expresssn,
					sid: modal.sid,
					compid:compid
					}, function(ret) {
						if (ret.status == 0) {	
							RhUI.alert(ret.result.message, '', function() {					
								modal.mybscan();					
							})
							return
						}
						var html = '<div class="fui-cell"><div class="fui-cell-info expresssn" data-sn="' + expresssn + '" data-mb="">'+ expresssn + '</div>' +				
							'<div class="fui-cell-remark noremark"><i class="icon icon-close text-danger" data-sn="'+expresssn+'"></i></div></div>';			
						$("#expresslist").prepend(html);
						modal.bindEvents();
						$("#expresssn").val('');
						$("#expresssn").focus();
						modal.mybscan();
					}, true, true);						
			}
		});
	}
	modal.bindEvents = function() {
		$(".icon-close").on('click',function(){
			var obj = $(this);					
			var expresssn = obj.data('sn');	
			core.json('express/del', {
				expresssn: expresssn,
				sid: modal.sid
				}, function(ret) {
					if (ret.status == 0) {
						RhUI.toast.show(ret.result.message);					
						return
					}
					obj.parents(".fui-cell").remove();	
				}, true, true);	
		});	
		$(".category-type a").on("click", function() {
			if ($(this).hasClass("active")) {
				return
			} else {
				$(".category-type a").removeClass("active");
				$(this).addClass("active");
				$("#category_type").val($(this).attr("data-label"))
			}
		});
		$(".expresssn").on('click',function(){
			var obj = $(this);					
			var expresssn = obj.data('sn');	
			var mobile = obj.data('mb');
			$('#mobile').val(mobile);
			$(".pop-article-hidden").show();
			$("#datasn").val(expresssn);
		    $('.verify-pop').find('.btn-close').unbind('click').click(function () {
                $(".pop-article-hidden").hide();
            }); 
			$('.verify-pop').find('.btn-submit').unbind('click').click(function () {
				if (!$('#mobile').isMobile()) {
					RhUI.toast.show('请输入11位手机号码');
					$("#mobile").focus();
					return
				}		
				var mobile = $('#mobile').val();
				var cabstloca = $('#cabstloca').val();	
				var compid = $('#compid').val();
				var remark = $('#remark').val();
				var label = $('#category_type').val();
				
				var images = [];
				$('#images').find('li').each(function() {
					images.push($(this).data('filename'))
				});
				obj.data('mb',mobile);
				var aaaa = expresssn + '<span class="pull-right text-danger" style="margin-right:1rem;">' + mobile + '</span>'
				obj.html(aaaa);
				core.json('express/send', {
					'expresssn': expresssn,
					'sid': modal.sid,
					'mobile': mobile,
					'images': images,
					'cabstloca':cabstloca,
					'remark': remark,
					'compid': compid,
					'label':label,
					'cfrom': modal.cfrom
					}, function(ret) {
						if (ret.status == 0) {
							RhUI.toast.show(ret.result.message);					
							return
						}
						RhUI.toast.show(ret.result.message);
						obj.next().html('<i class="icon icon-check text-success"></i>');
						$(".pop-article-hidden").hide();
						$('#images').html("");
						obj.removeClass("expresssn");
						obj.unbind();
					}, true, true);			
			});							
		});	
	}
    modal.initSet = function (params) {
		modal.sid = params.sid;
		var uploadUrl = core.getUrl('util/wxuploader')+'&nodisplay=1';
		core.showImages('.fui-list-media img');	
		$(".fui-list-inner").click(function(){			
			var images = {
				localIds: [],
			};
			wx.chooseImage({
				count: 1, // 最多选几张
				sizeType: ['compressed'], // 可以指定是原图还是压缩图，默认二者都有
				sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
				success: function(res) {
					images.localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
					var i = 0; var length = images.localIds.length;
					var upload = function() {
						wx.uploadImage({
							localId:'' + images.localIds[i],
							isShowProgressTips: 1,
							success: function(res) {
								var serverId = res.serverId;									
								$.ajax({   
									 url:uploadUrl,   
									 type:'post', 
									 data:{
										media_id:serverId,
									 },
									 dataType:'json',
									 success:function(data){ 										
										if (data.error == 1) {
											RhUI.toast.show(data.message);
										} else {
											$("#showthumb").attr('src',data.imgurl); 
											$("#thumb").val(data.realimgurl);
										}  
									 }
								});
								//如果还有照片，继续上传
								i++;
								if (i < length) {
									upload();
								}
							}
						});                    
					};
					upload();
				}
			}); 
		});
		
        $("#btn-submit").unbind('click').click(function () {
            if (modal.stop) {
                return
            }
            var title = $.trim($("#title").val());
            var mobile = $.trim($("#mobile").val());
            var telphone = $.trim($("#telphone").val());
			var thumb = $.trim($("#thumb").val());
			var tplchecked=$('#istplmsg').is(':checked');
			var smschecked=$('#issmsmsg').is(':checked');
			var istplmsg = 0;
			var issmsmsg = 0;
			if(tplchecked){
				istplmsg = 1;
			}
			if(smschecked){
				issmsmsg = 1;
			}
            if (title == '') {
                RhUI.toast.show("请输入驿站名称");
                return
            }
		    if (telphone == '') {
                RhUI.toast.show("请输入电话");
                return
            }
            if (mobile == '') {
                RhUI.toast.show("请输入手机号");
                return
            }
           
            modal.stop = true;
            var obj = {sid:modal.sid,thumb:thumb,title: title,telphone:telphone, mobile: mobile,istplmsg:istplmsg,issmsmsg:issmsmsg};
            core.json('express/myset', obj, function (json) {
                if (json.status == 1) {
                    RhUI.toast.show("保存成功");
                    location.href = core.getUrl('express/mindex') +'&id=' + modal.sid;
                    return
                } else {
                    RhUI.toast.show(json.result.message)
                }
                modal.stop = false
            }, true, true)
        });       
    };
	return modal
});