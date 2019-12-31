define(['core', 'tpl'], function(core, tpl) {
	var modal = {
		rid:0,
		cateid: '',
		total:0,
		page:1	
	};
	modal.init = function(params) {		
		modal.rid = params.rid;
		modal.total = params.total;		
		$(document).off('click', '.fui-tab-scroll .item').on('click', ".fui-tab-scroll .item", function() {
			var left = 0;
			var tab = $(this).closest(".fui-tab-scroll");
			var container = tab.find(".article-container");
			var cateid = $(this).data('cateid');
			modal.page = 1;
			modal.cateid = cateid;
			$(this).addClass('on').siblings().removeClass('on');
			if (container.length > 0) {
				left = container.scrollLeft()
			}
			tab.html(tab.html());
			tab.find(".article-container").scrollLeft(left);
			$('.content-empty').hide(), $('.infinite-loading').show();
			$('.container').html('');
			modal.getList(modal.cateid)
		});
		$(document).off('click', '.fui-icon-col').on('click', ".fui-icon-col", function() {
			var left = 0;			
			var cateid = $(this).data('cateid');
			var tab = $("#cate"+cateid).closest(".fui-tab-scroll");
			var container = tab.find(".article-container");
			modal.page = 1;
			modal.cateid = cateid;
			$("#cate"+cateid).addClass('on').siblings().removeClass('on');
			if (container.length > 0) {
				left = container.scrollLeft()
			}
			tab.html(tab.html());
			tab.find(".article-container").scrollLeft(left);
			$('.content-empty').hide(), $('.infinite-loading').show();
			$('.container').html('');
			modal.getList(modal.cateid)
		});

		$("#articlecate").unbind('click').click(function () {
            var html = $(".pop-article-hidden").html();
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
			onLoading: function() {
				modal.getList(modal.cateid)
			}
		});
		if (modal.page == 1) {
			if ($(".container a").length <= 0) {
				modal.getList(modal.cateid)
			} 
			else {
				modal.page++
			}
		}	
	};
	modal.getList = function(cateid) {		
		core.html('article/index', {
			rid:modal.rid,
			page: modal.page,
			cateid: cateid
		}, function(html) {
			$('.infinite-loading').hide();
			var length1 = $(".container a").length;	
			if (length1== modal.total) {
				$('.fui-content').infinite('stop')
			}
			else{
				if (html=='') {
					if(length1==0){
						$('.content-empty').show();
					}
					$('.fui-content').infinite('stop')
				} 
				else {
					$('.content-empty').hide();
					$('.fui-content').infinite('init');
					$('.container').append(html);
					var length2 = $(".container a").length;	
					if (length2== modal.total) {
						$('.fui-content').infinite('stop')
					}
				}
			}
			modal.page++;									
		});
	};	
	return modal
});