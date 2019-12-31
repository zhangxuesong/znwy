define(['core', 'tpl'], function (core, tpl) {
    var modal = {page: 1, lat:0,lng:0,keyword: ''};
    modal.init = function (params) {
        modal.keyword = params.keyword ? params.keyword : '' ;
        modal.page = 1;
        modal.lat = params.lat;
        modal.lng = params.lng;
		$('#mysearch').click(function(){
			var keyword = $("#keyword").val();
			if(keyword==''){
			   RhUI.toast.show('关键字不能为空');
			   return
			}
			$("#myform").submit();			
		});		
        $('.fui-content').infinite({
            onLoading: function () {
                modal.getList()
            }
        });
        if (modal.page == 1) {
            modal.getList()
        }		
    };
    modal.getList = function () {
            var _this = this;      
            core.json('proprietor/list', {page: modal.page, keyword: modal.keyword, lat: modal.lat, lng: modal.lng}, function (ret) {
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
				modal.bindEvents();
            }, false, true );				
    }; 
	modal.bindEvents = function() {
		$('.follow').unbind('click').click(function() {
			var follow = $(this).data('follow');
			if(follow==1){
				return
			}			
			if ($(this).attr('stop')) {
				return
			}
			var rid = $(this).data('rid');						
			$(this).attr('stop');
			var link = $(this);			
			core.json('proprietor/follow', {
				rid: rid
			}, function(ret) {
				if (ret.status == 0) {
					link.removeAttr('stop');
					RhUI.toast.show(ret.result.message);
					return
				}
				link.removeAttr('stop');
				var html = "<i class='icon icon-likefill' style='color:red'></i><p style='font-size:0.7rem;'>关注</p>";
				$(".follow[data-rid='" + rid + "']").html(html);
				RhUI.toast.show(ret.result.message);
			}, true, true)
		});
	};
    return modal
});