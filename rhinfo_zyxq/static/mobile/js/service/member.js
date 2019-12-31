define(['core','tpl'], function(core, tpl) {
	var modal = {};
	modal.init = function (params) {		
		var uploadUrl = core.getUrl('util/wxuploader');
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
											$("#showavatar").attr('src',data.imgurl); 
											$("#avatar").val(data.realimgurl);
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
			var uid = $.trim($("#uid").val());
            var realname = $.trim($("#realname").val());
            var nickname = $.trim($("#nickname").val());
			var avatar = $.trim($("#avatar").val());
			
            if (realname == '') {
                RhUI.toast.show("请输入真实姓名");
                return
            }
		    if (nickname == '') {
                RhUI.toast.show("请输入昵称");
                return
            }          
            var obj = $("form").serialize();
            core.json('service/myset' + '&' + obj ,{}, function (ret) {
                if (ret.status != 1) {
                    RhUI.toast.show(ret.result.message);
                    return
                }
				RhUI.toast.show(ret.result.message);
				location.href = core.getUrl('member/index');
            }, true, true)
        });       
    };
	modal.initBind = function(params) {
		modal.endtime = params.endtime;		
		modal.iswap = params.iswap;	
		
		$('#btnCode').click(function() {
			if ($('#btnCode').hasClass('disabled')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}	
			$('#btnCode').addClass('disabled');
			core.json('auth/bindverifycode', {
				mobile: $('#mobile').val(),
			}, function(ret) {
				if (ret.status != 1) {
					RhUI.toast.show(ret.result.message);
					$('#btnCode').html('获取验证码').removeClass('disabled').removeAttr('disabled')
				}
				if (ret.status == 1) {
					modal.seconds = 60;
					modal.verifycode();
				}
			}, false, true)
		});
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
				RhUI.toast.show('请输入5位数字验证码');
				return
			}
			if(modal.iswap==1){
				if ($('#pwd').isEmpty()) {
					RhUI.toast.show('请输入登录密码');
					return
				}
				if ($('#pwd').val() !== $('#pwd1').val()) {
					RhUI.toast.show('两次密码输入不一致');
					return
				}
			}
			RhUI.confirm('注册即表示同意上述协议',function(){		
				$('#btnSubmit').html('正在绑定...').attr('stop', 1);
				core.json('auth/bind', {
					mobile: $('#mobile').val(),
					verifycode: $('#verifycode').val(),
					pwd: $('#pwd').val(),
					ishb:0
				}, function(ret) {
					if (ret.status == 0) {
						RhUI.toast.show(ret.result.message);
						$('#btnSubmit').html('立即绑定').removeAttr('stop');
						return
					}
					if (ret.status == 2) {
						RhUI.confirm('需要合并吗?','温馨提示',function(){	
							core.json('auth/bind', {
								mobile: $('#mobile').val(),
								verifycode: $('#verifycode').val(),
								pwd: $('#pwd').val(),
								ishb:1
							}, function(ret) {
								if (ret.status == 0) {
									RhUI.toast.show(ret.result.message);
									$('#btnSubmit').html('立即绑定').removeAttr('stop');
									return
								}
								RhUI.alert(ret.result.message, '', function() {					
									location.href = core.getUrl('member/index');					
								});
							});
						}, true, true);
						return
					}
					RhUI.alert(ret.result.message, '', function() {	
						location.href = core.getUrl('member/index');	
					});
				}, true, true);
			});
		})
	};
	modal.verifycode = function() {
		modal.seconds--;
		if (modal.seconds > 0) {
			$('#btnCode').html(modal.seconds + '秒后重发').addClass('disabled').attr('disabled', 'disabled');
			setTimeout(function() {
				modal.verifycode()
			}, 1000)
		} else {
			$('#btnCode').html('获取验证码').removeClass('disabled').removeAttr('disabled')
		}
	};
	modal.initRegpro = function(params) {
		modal.endtime = params.endtime;		
		$('#btnCode').click(function() {
			if ($('#btnCode').hasClass('disabled')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}	
			$('#btnCode').addClass('disabled');
			core.json('auth/regproverifycode', {
				mobile: $('#mobile').val(),
			}, function(ret) {
				if (ret.status != 1) {
					RhUI.toast.show(ret.result.message);
					$('#btnCode').html('获取验证码').removeClass('disabled').removeAttr('disabled')
				}
				if (ret.status == 1) {
					modal.seconds = 60;
					modal.verifycode();
				}
			}, false, true)
		});
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			if ($('#property').isEmpty()) {
				RhUI.toast.show('请输入物业公名称');
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
				RhUI.toast.show('请输入5位数字验证码');
				return
			}
			if ($('#userno').isEmpty()) {
				RhUI.toast.show('请输入用户账号');
				return
			}
			if ($('#pwd').isEmpty()) {
				RhUI.toast.show('请输入登录密码');
				return
			}
			if ($('#pwd').val() !== $('#pwd1').val()) {
				RhUI.toast.show('两次密码输入不一致');
				return
			}
			RhUI.confirm('注册即表示同意上述协议',function(){								
				$('#btnSubmit').html('正在注册...').attr('stop', 1);
				core.json('auth/regproperty', {
					mobile: $('#mobile').val(),
					verifycode: $('#verifycode').val(),
					pwd: $('#pwd').val(),
					property:$('#property').val(),
					userno:$('#userno').val()
				}, function(ret) {
					if (ret.status == 0) {
						RhUI.toast.show(ret.result.message);
						$('#btnSubmit').html('立即入驻').removeAttr('stop');
						return
					}				
					RhUI.alert(ret.result.message, '', function() {					
						location.href = core.getUrl('home/index');					
					});
				}, true, true);
			});
		})
	};
	modal.initRegbusi = function(params) {
		modal.endtime = params.endtime;
		require(['rhui.picker'], function() {
			$('#city').cityPicker({
				showArea: true
			});			
		});
		$('#btnCode').click(function() {
			if ($('#btnCode').hasClass('disabled')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}	
			$('#btnCode').addClass('disabled');
			core.json('auth/regbusiverifycode', {
				mobile: $('#mobile').val(),
			}, function(ret) {
				if (ret.status != 1) {
					RhUI.toast.show(ret.result.message);
					$('#btnCode').html('获取验证码').removeClass('disabled').removeAttr('disabled')
				}
				if (ret.status == 1) {
					modal.seconds = 60;
					modal.verifycode();
				}
			}, false, true)
		});
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			if ($('#title').isEmpty()) {
				RhUI.toast.show('请输入商家名称');
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
				RhUI.toast.show('请输入5位数字验证码');
				return
			}			
			if ($('#city').isEmpty()) {
				RhUI.toast.show('请选择所在城市');
				return
			}
			if ($('#address').isEmpty()) {
				RhUI.toast.show('请输入详细地址');
				return
			}
			var citys = $('#city').val().split(' ');
			RhUI.confirm('注册即表示同意上述协议',function(){								
				$('#btnSubmit').html('正在注册...').attr('stop', 1);
				core.json('auth/regbusiness', {
					title:$('#title').val(),
					mobile:$('#mobile').val(),
					verifycode: $('#verifycode').val(),					
					address:$('#address').val(),
					province:citys[0],
					city:citys[1],
					district:citys[2],
				}, function(ret) {
					if (ret.status == 0) {
						RhUI.toast.show(ret.result.message);
						$('#btnSubmit').html('立即入驻').removeAttr('stop');
						return
					}				
					RhUI.alert(ret.result.message, '', function() {					
						location.href = core.getUrl('business/mindex');					
					});
				}, true, true);
			});
		})
	};
	modal.initRegion = function(params) {
		modal.endtime = params.endtime;	
		require(['rhui.picker'], function() {
			$('#city').cityPicker({
				showArea: true
			});			
		});
		$('#btnCode').click(function() {
			if ($('#btnCode').hasClass('disabled')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}		
			$('#btnCode').addClass('disabled');
			core.json('auth/regionverifycode', {
				mobile: $('#mobile').val(),
			}, function(ret) {
				if (ret.status != 1) {
					RhUI.toast.show(ret.result.message);
					$('#btnCode').html('获取验证码').removeClass('disabled').removeAttr('disabled')
				}
				if (ret.status == 1) {
					modal.seconds = 60;
					modal.verifycode();
				}
			}, false, true)
		});
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			if ($('#title').isEmpty()) {
				RhUI.toast.show('请输入小区名称');
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
				RhUI.toast.show('请输入5位数字验证码');
				return
			}
			
			if ($('#city').isEmpty()) {
				RhUI.toast.show('请选择所在城市');
				return
			}
			if ($('#address').isEmpty()) {
				RhUI.toast.show('请输入详细地址');
				return
			}
			var citys = $('#city').val().split(' ');
			RhUI.confirm('注册即表示同意上述协议',function(){								
				$('#btnSubmit').html('正在注册...').attr('stop', 1);
				core.json('auth/regregion', {
					title:$('#title').val(),
					mobile:$('#mobile').val(),
					verifycode: $('#verifycode').val(),					
					address:$('#address').val(),
					province:citys[0],
					city:citys[1],
					district:citys[2],
				}, function(ret) {
					if (ret.status == 0) {
						RhUI.toast.show(ret.result.message);
						$('#btnSubmit').html('立即入驻').removeAttr('stop');
						return
					}				
					RhUI.alert(ret.result.message, '', function() {					
						location.href = core.getUrl('home/index') + "&rid=" + ret.result.rid;					
					});
				}, true, true);
			});
		})
	};
	modal.initWxappBind = function(params) {
		modal.endtime = params.endtime;		
		modal.iswap = params.iswap;	
		
		$('#btnCode').click(function() {
			if ($('#btnCode').hasClass('disabled')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			$('#btnCode').addClass('disabled');
			core.json('auth/bindverifycode', {
				mobile: $('#mobile').val(),
			}, function(ret) {
				if (ret.status != 1) {
					RhUI.toast.show(ret.result.message);
					$('#btnCode').html('获取验证码').removeClass('disabled').removeAttr('disabled')
				}
				if (ret.status == 1) {
					modal.seconds = 60;
					modal.verifycode();
				}
			}, false, true)
		});
		$('#btnSubmit').click(function() {
			if ($('#btnSubmit').attr('stop')) {
				return
			}
			if (!$('#mobile').isMobile()) {
				RhUI.toast.show('请输入11位手机号码');
				return
			}
			if (!$('#verifycode').isInt() || $('#verifycode').len() != 5) {
				RhUI.toast.show('请输入5位数字验证码');
				return
			}
			RhUI.confirm('注册即表示同意上述协议',function(){		
				$('#btnSubmit').html('正在绑定...').attr('stop', 1);
				core.json('auth/wxappbind', {
					mobile: $('#mobile').val(),
					verifycode: $('#verifycode').val(),
					pwd: $('#pwd').val(),
					ishb:0
				}, function(ret) {
					if (ret.status == 0) {
						RhUI.toast.show(ret.result.message);
						$('#btnSubmit').html('立即绑定').removeAttr('stop');
						return
					}
					if (ret.status == 2) {						
						core.json('auth/wxappbind', {
							mobile: $('#mobile').val(),
							verifycode: $('#verifycode').val(),
							pwd: $('#pwd').val(),
							ishb:1
						}, function(ret) {
							if (ret.status == 0) {
								RhUI.toast.show(ret.result.message);
								$('#btnSubmit').html('立即绑定').removeAttr('stop');
								return
							}
							RhUI.alert(ret.result.message, '', function() {					
								wx.miniProgram.getEnv(function(res) {
									if(res.miniprogram){
										var path = '/pages/onedoor/onedoor';
										wx.miniProgram.navigateTo({
											url: path
										}); 							
									}
									else{
										location.href = core.getUrl('member/index');
									}
								})							
							});
						});
						return
					}
					RhUI.alert(ret.result.message, '', function() {	
						wx.miniProgram.getEnv(function(res) {
							if(res.miniprogram){
								var path = '/pages/onedoor/onedoor';
								wx.miniProgram.navigateTo({
									url: path
								}); 							
							}
							else{
								location.href = core.getUrl('member/index');
							}
						})		
					});
				}, true, true);
			});
		})
	};
	return modal
});