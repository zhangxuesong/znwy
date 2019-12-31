define(['core', 'tpl'], function (core, tpl) {
    var modal = {
		bid:0,
		page:1
	};
 
    modal.init = function (params) {
		modal.bid = params.bid;
        core.json('business/get_today', {bid:modal.bid}, function (json) {
            if (json.status == 1) {
                $("#today_repair").text(json.result.today_repair);
                $("#today_suggest").text(json.result.today_suggest);
                $("#today_member").text(json.result.today_member)
            }
        });
				
		core.json('business/get_fee', {bid:modal.bid}, function (json) {
            if (json.status == 1) {
                $("#payfee_week").text(json.result.payfee_week);
                $("#payfee_month").text(json.result.payfee_month);
                $("#payfee_today").text(json.result.payfee_today)
            }
        });
		
		core.json('business/get_credit', {bid:modal.bid}, function (json) {
            if (json.status == 1) {
                $("#paycredit_week").text(json.result.paycredit_week);
                $("#paycredit_month").text(json.result.paycredit_month);
                $("#paycredit_today").text(json.result.paycredit_today)
            }
        });
    };
    modal.initSet = function (params) {
		modal.bid = params.bid;
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
			
            if (title == '') {
                RhUI.toast.show("请输入商家名称");
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
            var obj = {id:modal.bid,thumb:thumb,title: title,telphone:telphone, mobile: mobile};
            core.json('business/myset', obj, function (json) {
                if (json.status == 1) {
                    RhUI.toast.show("保存成功");
                    location.href = core.getUrl('business/mindex') +'&id=' + modal.bid;
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