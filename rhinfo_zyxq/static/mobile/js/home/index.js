define(['core', 'tpl'], function(core, tpl) {
	var modal = {
		rid:0,
		total_forum:0,
		total_article:0,
		status: 0,
		fpage:1,
		apage:1,
		style:0
	};
	modal.init = function(params) {		
		modal.rid = params.rid;
		modal.initSwiper();
		$(".selecthouse").unbind('click').click(function () {
            var html = $(".pop-myregion-hidden").html();
            var container = new RhUIModal({
                content: html, extraClass: "popup-modal", maskClick: function () {
                    container.close()
                }
            });
            container.show();
            $('.verify-pop').find('.close').unbind('click').click(function () {
                container.close()
            });  	
        })
		$('.verify-pop').find('.close').unbind('click').click(function () {
			$(".pop-region-hidden").hide();
        });
		$('.verify-pop').find('.follow').unbind('click').click(function () {
			$(".pop-region-hidden").hide();
			core.json('member/follow', {
				rid: modal.rid
			}, function(ret) {	
				if (ret.status == 1) {
					$(".pop-region-hidden").hide();
				}
				RhUI.toast.show(ret.result.message);				
			}, false, true);
        });			
	};
	modal.initforum = function(params) {		
		modal.rid = params.rid;
		modal.total_forum = params.total_forum;	
		modal.fpage = 1;
		$(".selecthouse").unbind('click').click(function () {
            var html = $(".pop-myregion-hidden").html();
            var container = new RhUIModal({
                content: html, extraClass: "popup-modal", maskClick: function () {
                    container.close()
                }
            });
            container.show();
            $('.verify-pop').find('.close').unbind('click').click(function () {
                container.close()
            });  	
        })
		$('.verify-pop').find('.close').unbind('click').click(function () {
			$(".pop-region-hidden").hide();
        });
		$('.verify-pop').find('.follow').unbind('click').click(function () {
			$(".pop-region-hidden").hide();
			core.json('member/follow', {
				rid: modal.rid
			}, function(ret) {	
				if (ret.status == 1) {
					$(".pop-region-hidden").hide();
				}
				RhUI.toast.show(ret.result.message);				
			}, false, true);
        });		
		modal.initSwiper();
		$('.fui-content').infinite({
			onLoading: function() {
				modal.getList1();				
			}
		});
		if (modal.fpage == 1) {
			modal.getList1();
		}		
	};
	modal.getList = function() {		
		core.json('home/index', {
			id:modal.rid,
			page: modal.fpage,
			cfrom:1
		}, function(ret) {
			var result = ret.result;
			if (result.total <= 0) {
				$('.content-empty').show();								
				$('.fui-content').infinite('stop')
			} else {
				$('.content-empty').hide();
				$('.forum-empty').show();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop')
				}
			}
			modal.fpage++;
			core.tpl('.container', 'tpl_board_post_list', result, modal.fpage > 1);
			modal.bindEvents();
			modal.textShow();
		});
	};
	modal.getList1 = function() {		
		core.html('home/index', {
			rid:modal.rid,			
			page: modal.fpage,
			cfrom:1
		}, function(html) {
			$('.infinite-loading').hide();
			var length1 = $(".container section").length;	
			if (length1== modal.total_forum) {
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
					$('.forum-empty').show();
					$('.fui-content').infinite('init');
					$('.container').append(html);
					var length2 = $(".container section").length;	
					if (length2== modal.total_forum) {
						$('.fui-content').infinite('stop')
					}
					modal.bindEvents();
					modal.textShow();
				}
			}
			modal.fpage++;
		});
	};
	modal.bindEvents = function() {
		$('.like-good').unbind('click').click(function() {
			var link = $(this);
			if ($(this).attr('stop')) {
				return
			}
			var pid = $(this).data('pid');
			var bid = $(this).data('bid');
			$(this).attr('stop');
			core.json('forum/like', {
				bid: bid,
				pid: pid
			}, function(ret) {
				if (ret.status == 0) {
					link.removeAttr('stop');
					RhUI.toast.show(ret.result.message);
					return
				}
				link.removeAttr('stop');
				$(".like-good[data-pid='" + pid + "'] span").html(ret.result.good)
			}, true, true)
		});
		$('.fui-card-images').unbind('click').click(function() {
			var boardid = $(this).data('boardid');
			core.showImages('.'+ boardid +' img');
		});
	};
	modal.textShow = function() {
		$(".sns-content-info").css({
			"-webkit-box-orient": "",
			"-webkit-line-clamp": "",
			"display": "",
			"max-height": "4rem"
		});
		$(".sns-card-show").html("全文");
		$(".sns-card-list").each(function() {
			$(this).find(".sns-card-show").hide();
			var $divHeight = $(this).find(".sns-content-info");
			var $divHeightSub = $(this).find(".sns-content-info-sub");
			var dheight = $divHeight.height();
			var dheightSub = $divHeightSub.height();
			if (dheightSub > dheight) {
				$(this).find(".sns-card-show").show();
				var text = $(this).find(".sns-card-show").html();
				$divHeight.css({
					"-webkit-box-orient": "vertical",
					"-webkit-line-clamp": "4",
					"display": "-webkit-box",
				});
				$(document).on("click", ".sns-card-show", function() {
					if (text == "全文") {
						$divHeight.css({
							"-webkit-box-orient": "",
							"-webkit-line-clamp": "",
							"display": "",
							"max-height": "" + dheightSub + "px"
						});
						$(this).html("收起");
						text = "收起"
					} else if (text == "收起") {
						$divHeight.css({
							"-webkit-box-orient": "vertical",
							"-webkit-line-clamp": "4",
							"display": "-webkit-box",
							"max-height": "" + dheight + "px"
						});
						$(this).html("全文");
						text = "全文"
					}
				})
			}
		})
	};	
	modal.initarticle = function(params) {		
		modal.rid = params.rid;
		modal.total_article = params.total_article;	
		modal.apage = 1;
		$(".selecthouse").unbind('click').click(function () {
            var html = $(".pop-myregion-hidden").html();
            var container = new RhUIModal({
                content: html, extraClass: "popup-modal", maskClick: function () {
                    container.close()
                }
            });
            container.show();
            $('.verify-pop').find('.close').unbind('click').click(function () {
                container.close()
            });  	
        })
		$('.verify-pop').find('.close').unbind('click').click(function () {
			$(".pop-region-hidden").hide();
        });
		$('.verify-pop').find('.follow').unbind('click').click(function () {
			$(".pop-region-hidden").hide();
			core.json('member/follow', {
				rid: modal.rid
			}, function(ret) {	
				if (ret.status == 1) {
					$(".pop-region-hidden").hide();
				}
				RhUI.toast.show(ret.result.message);				
			}, false, true);
        });			
		modal.initSwiper();
		$('.fui-content').infinite({
			onLoading: function() {
				modal.getList2();				
			}
		});
		if (modal.apage == 1) {
			modal.getList2();
		}		
	};
	modal.getList2 = function() {		
		core.html('home/index', {
			rid:modal.rid,
			page: modal.apage,
			cfrom:2
		}, function(html) {			
			$('.infinite-loading').hide();
			var length1 = $(".container a").length;	
			if (length1== modal.total_article) {
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
					$('.forum-empty').show();
					$('.fui-content').infinite('init');
					$('.container').append(html);
					var length2 = $(".container a").length;	
					if (length2== modal.total_article) {
						$('.fui-content').infinite('stop')
					}
				}
			}
			modal.apage++;
		});
	};
	modal.initSwiper = function () {
        if ($(".swiper").length > 0) {
            require(['swiper'], function (modal) {
                $(".swiper").each(function () {
                    var obj = $(this);
                    var ele = $(this).data('element');
                    var container = ele + " .swiper-container";
                    var view = $(this).data('view');
                    var btn = $(this).data('btn');
                    var free = $(this).data('free');
                    var space = $(this).data('space');
                    var callback = $(this).data('callback');
                    var slideTo = $(this).data('slideto');
                    var options = {
                        pagination: container + ' .swiper-pagination',
                        slidesPerView: view,
                        paginationClickable: true,
                        autoHeight: true,
                        nextButton: container + ' .swiper-button-next',
                        prevButton: container + ' .swiper-button-prev',
                        spaceBetween: space > 0 ? space : 0,                     
                        preventLinksPropagation : true,
                        onSlideChangeEnd: function (swiper) {
                            if (swiper.isEnd && callback) {                               
                            }
                        }
                    };
                    if (!btn) {
                        delete options.nextButton;
                        delete options.prevButton;
                        $(container).find(".swiper-button-next").remove();
                        $(container).find(".swiper-button-prev").remove()
                    }
                    if (free) {
                        options.freeMode = true
                    }
                    var swiper = new Swiper(container, options);
                    if(slideTo){
                        swiper.slideTo(slideTo, 0, false);
                    }
                });
            })
        }
    };
	modal.initTwo = function(params) {		
		modal.rid = params.rid;
		modal.total_forum = params.total_forum;	
		modal.total_article = params.total_article;	
		modal.style = params.style;
		modal.fpage = 1;
		modal.apage = 1;
		if(modal.style==4){
			modal.status = 2;
		}
		else{
			modal.status = 1;
		}
		$(".selecthouse").unbind('click').click(function () {
            var html = $(".pop-myregion-hidden").html();
            var container = new RhUIModal({
                content: html, extraClass: "popup-modal", maskClick: function () {
                    container.close()
                }
            });
            container.show();
            $('.verify-pop').find('.close').unbind('click').click(function () {
                container.close()
            });  	
        })
		$('.verify-pop').find('.close').unbind('click').click(function () {
			$(".pop-region-hidden").hide();
        });
		$('.verify-pop').find('.follow').unbind('click').click(function () {
			$(".pop-region-hidden").hide();
			core.json('member/follow', {
				rid: modal.rid
			}, function(ret) {	
				if (ret.status == 1) {
					$(".pop-region-hidden").hide();
				}
				RhUI.toast.show(ret.result.message);				
			}, false, true);
        });		
		modal.initSwiper();
		$('.fui-content').infinite({
			onLoading: function() {
				modal.getListTwo();
			}
		});
		
		if(modal.style==4){
			if (modal.apage == 1) {
				modal.getListTwo();			
			}			
		}
		else{
			if (modal.fpage == 1) {
				modal.getListTwo();		
			}		
		}
		
		RhUI.tab({
			container: $('#tab'),
			handlers: {			
				tab1: function() {
					modal.changeTab(1)
				},
				tab2: function() {
					modal.changeTab(2)
				}
			}
		});
	};
	modal.getListTwo = function(){
		if(modal.status==1){
			modal.getList1();
		}
		if(modal.status==2){
			modal.getList2();
		}
	}
	modal.changeTab = function(status) {
		$('.fui-content-inner').infinite('init');
		$('.content-empty').hide();
		$('.infinite-loading').show();
		modal.status = status;	
		modal.fpage = 1;
		modal.apage = 1;
		$('.container').html('');
		if(modal.status==1){
			$('.mycontainer').html('<div class="container"></div>');
			modal.getList1();
		}
		if(modal.status==2){
			$('.mycontainer').html('<div class="threadlist box_bg"><ul><div class="container"></div></ul></div>');
			modal.getList2();
		}
	};
	return modal
});