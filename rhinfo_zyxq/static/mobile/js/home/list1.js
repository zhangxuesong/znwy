define(['core', 'tpl', 'http://api.map.baidu.com/getscript?v=2.0&ak=TPBXoGaFt9pXVq0tVZ7puGRUneloGhnh'], function (core, tpl) {
    var modal = {page: 1, keyword: '',lbs:'',op:'',range:''};
    modal.init = function (params) {
        modal.keyword = params.keyword ? params.keyword : '' ;
        modal.page = 1;
        modal.lat = '';
        modal.lng = '';
        modal.range = params.range ? params.range : '' ;
		modal.lbs = params.lbs ? params.lbs : '' ;
		modal.op = params.op ? params.op : '' ;
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
        var geolocation = new BMap.Geolocation();

        geolocation.getCurrentPosition(function (r) {
            var _this = this;
            if (this.getStatus() == BMAP_STATUS_SUCCESS) {
                modal.lat = r.point.lat;
                modal.lng = r.point.lng;
            }

            core.json('home/list', {page: modal.page, keyword: modal.keyword, lat: modal.lat, lng: modal.lng, range: modal.range,lbs:modal.lbs,op:modal.op}, function (ret) {
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
            }, {enableHighAccuracy: true})
        })
    };

    return modal
});