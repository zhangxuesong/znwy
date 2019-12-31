define(['core', 'tpl'], function (core, tpl) {
    var modal = {page: 1,range:500,lat:0,lng:0};
    modal.init = function (params) {      
		modal.range = params.range ? params.range : 500;
		modal.lat = params.lat;
		modal.lng = params.lng;	
		
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
            core.json('charging/list', {page: modal.page,lat: modal.lat, lng: modal.lng, range: modal.range},
			function (ret) {
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
                core.tpl('.container', 'tpl_charging_list', result, modal.page > 1);
            }, false, true );
    }; 
   
    return modal
});