define(['core', 'tpl'], function(core, tpl) {
	var modal = {};
	modal.init = function(params) {
		modal.params = params;		
		$('.fui-uploader').click(function(){				
			core.wxUploadImages($(this),"images",core.getUrl('util/wxuploader')+'&nodisplay=1',core.getUrl('util/remove'));	
		});	

		$(".category-type a").on("click", function() {
			if ($(this).hasClass("active")) {
				return
			} else {
				$(".category-type a").removeClass("active");
				$(this).addClass("active");
				$("#category_type").val($(this).attr("data-type"))
			}
		});
	    modal.bindEvents();		
		$('.btn-submit').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			if ($('#category_type').isEmpty()) {
				RhUI.toast.show('请选择工单类别');
				return
			}
			
			if ($('#content').isEmpty()) {
				RhUI.toast.show('工单内容不能为空');
				return
			}
			var images = [];
			$('#images').find('li').each(function() {
				images.push($(this).data('filename'))
			});
			var content = $('#content').val();
			var category = $('#category_type').val();
			
			$(this).html('正在处理...').attr('stop', 1);
			core.json('manager/repair', {
				'rid':modal.params.rid,
				'cid':category,
				'images': images,
				'content': content
			}, function(ret) {
				if (ret.status == 1) {
					RhUI.toast.show(ret.result.msg);
					setTimeout(function(){
						location.href = core.getUrl('manager/repairtrack') + '&rid=' + modal.params.rid + '&id=' + ret.result.id;
					},1000);					
					return
				}
				$('.btn-submit').removeAttr('stop').html('提交');
				RhUI.toast.show(ret.result.message)
			}, true, true)
		})
	};
	modal.initReply = function(params) {
		modal.params = params;		
		$('.fui-uploader').click(function(){				
			core.wxUploadImages($(this),"images",core.getUrl('util/wxuploader')+'&nodisplay=1',core.getUrl('util/remove'));	
		});

		$(".category-type a").on("click", function() {
			if ($(this).hasClass("active")) {
				return
			} else {
				$(".category-type a").removeClass("active");
				$(this).addClass("active");
				$("#category_type").val($(this).attr("data-type"))
			}
		});
		
		core.showImages('.myrepairimg img');	    
		core.showImages('.myreplyimg img');	
		modal.bindEvents();	
		$('.btn-submit').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			
			if ($('#category_type').isEmpty()) {
				RhUI.toast.show('请选择状态');
				return
			}
			
			if ($('#content').isEmpty()) {
				RhUI.toast.show('内容不能为空');
				return
			}
			var images = [];
			$('#images').find('li').each(function() {
				images.push($(this).data('filename'))
			});
			var content = $('#content').val();
			var category = $('#category_type').val();
			var repair_id = $('#repair_id').val();

			$(this).html('正在处理...').attr('stop', 1);
			core.json('manager/repairreply', {
				'rid':modal.params.rid,
				'id':repair_id,
				'status':category,
				'images': images,
				'content': content
			}, function(ret) {
				if (ret.status == 1) {
					$('.btn-submit').removeAttr('stop').html('提交成功')
					setTimeout(function(){
						location.href = core.getUrl('manage/steward')+'&status=4';
					},1000);					
					return
				}
				$('.btn-submit').removeAttr('stop').html('提交');
				RhUI.toast.show(ret.result.message)
			}, true, true)
		});
		$('#btnSend').click(function() {
			core.json('manager/repairrob', {
				'id':modal.params.id,'rid':modal.params.rid
			}, function(ret) {
				if (ret.status == 0) {
					RhUI.toast.show(ret.result.message);
					return
				}
				RhUI.alert(ret.result.message, '', function() {
					location.href = core.getUrl('manage/steward')+'&status=4';
				})				
			}, true, true)
		});
		$('#btnTake').click(function() {
			$(".pop-repair-hidden").show();
			$('.verify-pop').find('.close').unbind('click').click(function () {
                $(".pop-repair-hidden").hide();
            }); 
			$('.verify-pop').find('.takeuid').unbind('click').click(function () {
				$(".pop-repair-hidden").hide();
				var obj = $(this);
				var teamid = obj.data('teamid');
				core.json('manager/repairtake', {
					id:modal.params.id,
					rid:modal.params.rid,
					teamid:teamid
				}, function(ret) {
					if (ret.status == 0) {
						RhUI.toast.show(ret.result.message);
						return
					}
					RhUI.alert(ret.result.message, '', function() {
						location.href = core.getUrl('manage/steward')+'&status=4';
					})				
				}, true, true)
			});	
			$('.verify-pop').find('#btnSubmit').unbind('click').click(function () {
				var obj = $(this);
				var teamids = '';
				$('.fui-icon-col').each(function(index,obj) {
					var teamid = $(this).data('teamid');				
					if ($(this).find('.check-item').prop('checked')) {
						teamids += teamid + ',';					
					}	
				});
				teamids = teamids.substring(0,teamids.length - 1);
				if(teamids==''){
					RhUI.toast.show('请选择人员');
					return
				}
				$(".pop-repair-hidden").hide();
				core.json('manager/repairtakemuti', {
					id:modal.params.id,
					teamid:teamids,
					rid:modal.params.rid
				}, function(ret) {
					if (ret.status == 0) {
						RhUI.toast.show(ret.result.message);
						return
					}
					RhUI.alert(ret.result.message, '', function() {
						location.href = core.getUrl('manage/steward')+'&status=4';
					})				
				}, true, true)
			});					
		});
		
	};
	modal.initTrack = function(params) {
		modal.params = params;		
		
		core.showImages('.myrepairimg img');	    
		core.showImages('.myreplyimg img');
		
		$('.btn-submit').click(function() {
			if ($(this).attr('stop')) {
				return
			}						
					
			var repair_id = $('#repair_id').val();

			$(this).html('正在处理...').attr('stop', 1);
			core.json('manager/repairfinish', {
				'id':repair_id,'rid':modal.params.rid
			}, function(ret) {
				if (ret.status == 1) {
					$('.btn-submit').removeAttr('stop').html('提交成功')
					setTimeout(function(){
						location.href = core.getUrl('manager/repaircomment') + "&repairid=" + repair_id;
					},1000);					
					return
				}
				$('.btn-submit').removeAttr('stop').html('已处理好');
				RhUI.toast.show(ret.result.message)
			}, true, true)
		})
	};
	modal.initMtrack = function(params) {
		modal.params = params;		
		$('.fui-uploader').click(function(){				
			core.wxUploadImages($(this),"images",core.getUrl('util/wxuploader')+'&nodisplay=1',core.getUrl('util/remove'));	
		});
		modal.bindEvents();	
		$('.btn-submit').click(function() {
			if ($(this).attr('stop')) {
				return
			}						
			if ($('#content').isEmpty()) {
				RhUI.toast.show('内容不能为空');
				return
			}
			var images = [];
			$('#images').find('li').each(function() {
				images.push($(this).data('filename'))
			});
			var content = $('#content').val();			
			var repair_id = $('#repair_id').val();

			$(this).html('正在处理...').attr('stop', 1);
			core.json('manager/repairtrack', {
				'id':repair_id,
				'images': images,
				'content': content,
				'rid':modal.params.rid
			}, function(ret) {
				if (ret.status == 0) {
					$('.btn-submit').removeAttr('stop').html('提交成功')
					setTimeout(function(){
						location.href = core.getUrl('manager/myrepairp');
					},1000);					
					return
				}
				$('.btn-submit').removeAttr('stop').html('提交申请');
				RhUI.toast.show(ret.result.message)
			}, true, true)
		})
	};
	modal.bindEvents = function() {
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
							var contents = res.translateResult;
							var content = contents.replace(/。/g,'');
							$("#content").append(content);
						}
				   });
				  },
				  fail: function (res) {
					RhUI.toast.show(res.errMsg);
				  }
				});
			}
		});
	};
	return modal
});