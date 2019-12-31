define(['core', 'tpl'], function (core, tpl) {
    var modal = {
		rid:0,
		page:1
	};
    modal.init = function (params) {
		modal.rid = params.rid;
        core.json('manage/get_today', {rid:modal.rid}, function (json) {
            if (json.status == 1) {
                $("#today_repair").text(json.result.today_repair);
                $("#today_suggest").text(json.result.today_suggest);
                $("#today_member").text(json.result.today_member)
            }
        });
		core.json('manage/get_status', {rid:modal.rid}, function (json) {
            if (json.status == 1) {
                $("#repair_status").text(json.result.repair_status);
                $("#suggest_status").text(json.result.suggest_status);
                $("#door_status").text(json.result.door_status)
				$("#repairp_status").text(json.result.repairp_status);
            }
        });
		core.json('manage/get_member', {rid:modal.rid}, function (json) {
            if (json.status == 1) {
                $("#member_otype0").text(json.result.member_otype0);
                $("#member_otype1").text(json.result.member_otype1);
                $("#member_otype2").text(json.result.member_otype2)
            }
        });
		core.json('manage/get_fee', {rid:modal.rid}, function (json) {
            if (json.status == 1) {
                $("#fee_total").text(json.result.fee_total);
                $("#payfee_month").text(json.result.payfee_month);
                $("#payfee_today").text(json.result.payfee_today)
            }
        });
        if ($(".index-notice").length > 0) {
            var _this = $(".index-notice");
            var speed = 3000;
            setInterval(function () {
                var length = _this.find("li").length;
                if (length > 1) {
                    _this.find("ul").animate({marginTop: "-1rem"}, 500, function () {
                        $(this).css({marginTop: "0px"}).find("li:first").appendTo(this)
                    })
                }
            }, speed)
        }
		$("#myregions").unbind('click').click(function () {
            var html = $(".pop-article-hidden").html();
            var container = new RhUIModal({
                content: html, extraClass: "popup-modal", maskClick: function () {
                    container.close()
                }
            });
            container.show();
            $('.verify-pop').find('.close').unbind('click').click(function () {
                container.close()
            });  
			$('.verify-pop').find('.fui-icon-col').unbind('click').click(function () {
                container.close()
            }); 			
        })
    };
    modal.initPerson = function (params) {
		modal.rid = params.rid;
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
            if (modal.stop) {
                return
            }
			var teamid = $.trim($("#teamid").val());
            var realname = $.trim($("#realname").val());
            var mobile = $.trim($("#mobile").val());
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
            if (mobile == '') {
                RhUI.toast.show("请输入手机号");
                return
            }
           
            modal.stop = true;
            var obj = {rid:modal.rid,teamid:teamid,avatar:avatar, realname: realname,nickname:nickname, mobile: mobile};
            core.json('manage/myset', obj, function (json) {
                if (json.status == 1) {
                    RhUI.toast.show("保存成功");
                    location.href = core.getUrl('manage/my');
                    return
                } else {
                    RhUI.toast.show(json.result.message)
                }
                modal.stop = false
            }, true, true)
        });       
    };
	modal.initNotice = function (params) {
		modal.rid = params.rid;
       $('.fui-content').infinite({
            onLoading: function () {
                modal.getNoticeList()
            }
        });
        if (modal.page == 1) {
            modal.getNoticeList()
        }        
    };
	modal.getNoticeList = function() {		
		core.json('manage/notice', {
			page: modal.page,rid:modal.rid
		}, function(ret) {
			if (ret.status == 0) {
				$('.content-empty').show();
				$('.fui-content').infinite('stop')
				return
			}
			var result = ret.result;
			if (result.total <= 0) {
				$('.content-empty').hide();
				$('.fui-content').infinite('stop')
			} else {
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop')					
				}
			}
			modal.page++;
			core.tpl('.container', 'tpl_notice_list', result, modal.page > 1);			
		})
	};
    return modal
});