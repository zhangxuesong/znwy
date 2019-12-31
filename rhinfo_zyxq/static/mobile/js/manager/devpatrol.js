define(['core','tpl'], function(core, tpl) {
	var modal = {status: '',rid:'',pageIndex:0};
	modal.init = function(params) {
		modal.status = params.status;	
		modal.rid = params.rid;	
		$('.fui-content').infinite({
			onLoading: function() {
				modal.getList()
			}
		});		
		modal.getList();
		RhUI.tab({
			container: $('#tab'),
			handlers: {
				tab: function() {
					modal.changeTab('')
				},
				tab1: function() {
					modal.changeTab(1)
				},
				tab2: function() {
					modal.changeTab(2)
				},
				tab3: function() {
					modal.changeTab(3)
				},
				tab4: function() {
					modal.changeTab(4)
				}
			}
		});
	};
	modal.changeTab = function(status) {
		$('.fui-content').infinite('init');
		$('.content-empty').hide();
		$('.container').html('');
		$('.infinite-loading').show();
		modal.status = status;		
		modal.getList();
	};
	modal.getList = function() {
		core.html('manager/devtasklist', {
			status: modal.status,
			rid: modal.rid
		}, function(html) {
			if (html=='') {	
				$('.content-empty').show();
			} else {
				$('.content-empty').hide();
			}
			$('.container').html(html);
			$('.fui-content').infinite('stop');
		})
	};
	modal.initItem = function(params) {
		modal.rid = params.rid;	
		core.showImages('.fui-images img');
		$('.fui-uploader').uploader({
			uploadUrl: core.getUrl('util/uploader')+'&nodisplay=1',
			removeUrl: core.getUrl('util/remove')
		});
		modal.paging();
		$('#btnSubmit').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			var that = $(this);
			$('.fui-images').each(function() {
				var image = $(this).data('image');
				$("input[name="+image+"]").val($(this).find('li').data('filename'));			
			});
			RhUI.confirm('确认提交吗？','巡检确认',function(){					
				that.html('正在处理...').attr('stop', 1);						
				core.json('manager/devdetail',
				$("#myform").serializeObject(),
				function(rjson) {
					if (rjson.status != 1) {
						that.html('提交').removeAttr('stop');					
						RhUI.toast.show(rjson.result.message);
						return
					}
					RhUI.toast.show(rjson.result.message);	
					location.href = core.getUrl('manager/devpatrol') + "&rid=" + modal.rid;
				})
			})
		})
	};
	modal.paging = function() {
		modal.changeBtn();	
		var $btnPre=$("#btnPre");
		var $btnNext=$("#btnNext");
		var $btnHome=$("#btnHome");
		$btnNext.click(function(){
			modal.pageIndex++;
			$(".question-group").removeClass("now");
			$(".question-group").eq(modal.pageIndex).addClass("now");
			modal.changeBtn();
		})
		$btnPre.click(function(){
			modal.pageIndex--;
			$(".question-group").removeClass("now");
			$(".question-group").eq(modal.pageIndex).addClass("now");
			modal.changeBtn();
		})
		$btnHome.click(function(){
			modal.pageIndex=0;
			$(".question-group").removeClass("now");
			$(".question-group").eq(modal.pageIndex).addClass("now");
			modal.changeBtn();
		})
	};
	modal.changeBtn = function() {
		var $btnPre=$("#btnPre");
		var $btnNext=$("#btnNext");
		var $btnHome=$("#btnHome");
		var $btnSubmit=$("#btnSubmit");
		var len = $(".question-group").length;
		$(".fui-content").animate({scrollTop:0});
		$(".question-group").each(function(idx){
			if($(this).hasClass("now")){
				if(len==(idx+1)&&len>1){
					$btnNext.hide();
					$btnPre.show();
					$btnHome.show();
					$btnSubmit.show();
				}else if(idx==0&&len>1){
					$btnPre.hide();
					$btnHome.hide();
					$btnNext.show();
				}else if(len==1){
					$btnPre.hide();
					$btnNext.hide();
					$btnHome.hide();
					$btnSubmit.show();
				}else if(idx>0){
					$btnNext.show();
					$btnPre.show();
					$btnHome.hide();
				}
			}
		});
	};
	return modal
});