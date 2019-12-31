define(['core', 'tpl'], function (core, tpl) {
    var modal = {page: 1, keyword: '',lbs:'',range:500,sorttype:0,cateid:0,lat:'',lng:''};
    modal.init = function (params) {
        modal.keyword = params.keyword ? params.keyword : '' ;
		modal.lbs = params.lbs ? params.lbs : '' ;
        modal.cateid = params.cateid ? params.cateid : 0 ;      
		modal.sorttype = params.sorttype ? params.sorttype : 0 ;
		modal.range = params.range ? params.range : 500;
		modal.lat = params.lat ? params.lat :'';
		modal.lng = params.lng ? params.lnt : '';
		
		if (modal.cateid > 0) {
            $('.sortmenu_cate ul li').each(function(){
                if ($(this).attr('cateid') == modal.cateid) {
                    $('#sortmenu_cate_text').html($(this).attr('text'));
                }
            });
        }


        $(".sortMenu > li").off("click").on("click",function(){
            var menuclass = $(this).attr("data-class");
            if($("."+menuclass+"").css("display")=="none"){
                $(".sortMenu > div").hide();
                $("."+menuclass+"").show();
                $(".sort-mask").show();
            }else{
                $("."+menuclass+"").hide();
                $(".sort-mask").hide();
            }

        });

        $(".sort-mask").off("click").on("click",function(){
            $(this).hide();
            $(".sortMenu > div").hide();
        });

        $('.sortmenu_rule ul li').click(function () {
            modal.range = $(this).attr('range');
            var text = $(this).attr('text');
            $('#sortmenu_rule_text').html(text);
            $('.sortmenu_rule').hide();
            modal.page = 1;
            $(".container").empty();
            $(".sort-mask").hide();
            $(".sortMenu > div").hide();
            modal.getList()
        });

        $('.sortmenu_cate ul li').click(function () {
            modal.cateid = $(this).attr('cateid');
            var text = $(this).attr('text');
            $('#sortmenu_cate_text').html(text);
            $('.sortmenu_cate').hide();
            modal.page = 1;
            $(".container").empty();
            $(".sort-mask").hide();
            $(".sortMenu > div").hide();
            modal.getList()
        });

        $('.sortmenu_sort ul li').click(function () {
            modal.sorttype = $(this).attr('sorttype');
            var text = $(this).attr('text');
            $('#sortmenu_sort_text').html(text);
            $('.sortmenu_sort').hide();
            modal.page = 1;
            $(".container").empty();
            $(".sort-mask").hide();
            $(".sortMenu > div").hide();
            modal.getList()
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
            core.json('business/list', {page: modal.page, keyword: modal.keyword, lat: modal.lat, lng: modal.lng, range: modal.range,lbs:modal.lbs,sortype:modal.sorttype,cateid:modal.cateid}, function (ret) {
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
            }, false, true );
    }; 
   
    return modal
});