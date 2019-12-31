define(['core', 'tpl'], function (core, tpl) {
    var modal = {page: 1,cate:0,keyword:'',lat:'',lng:''};
    modal.init = function (params) {
        modal.cate = params.cate;
        modal.keyword = params.keyword;
		modal.lat = params.lat;
		modal.lng = params.lng;
		
		$("#telcate").unbind('click').click(function () {
            var html = $(".pop-telcate-hidden").html();
            container = new RhUIModal({
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
        core.json('telphone/list', {
            page: modal.page,
            cate: modal.cate,
            keyword: modal.keyword,
			lat: modal.lat,
			lng: modal.lng,
        }, function (ret) {
            var result = ret.result;
            if (result.total <= 0) {
                $("#container").hide();
                $('.fui-content').infinite('stop')
                $(".content-empty").show();
            } else {
                $("#container").show();
                $(".content-empty").hide();
                $('.fui-content').infinite('init');
                if (result.list.length <= 0 || result.list.length < result.pagesize) {
                    $('.fui-content').infinite('stop')
                }
            }
            modal.page++;
            core.tpl('#container', 'tpl_list', result, modal.page > 1);
        })
    };
    return modal
});