var provinces = new Array("京","沪","浙","苏","粤","鲁","晋","冀",
            "豫","川","渝","辽","吉","黑","皖","鄂",
            "津","贵","云","桂","琼","青","新","藏",
            "蒙","宁","甘","陕","闽","赣","湘");

var keyNums = new Array("1","2","3","4","5","6","7","8","9","0",
            "Q","W","E","R","T","Y","U","P","<img src='../addons/rhinfo_zyxq/static/mobile/images/backspace.png'/>",
            "A","S","D","F","G","H","J","K","L",
            "Z","X","C","V","B","N","M","字","确定",
			"领","警","学","挂","港","澳","试","超");
			
var keyTxts = new Array("领","警","学","挂","港","澳","试","超");
			
var next=0;			
	function showProvince(){
			$("#pro").html("");
			var ss="";
			for(var i=0;i<provinces.length;i++){
				ss=ss+addKeyProvince(i)
			} 
			$("#pro").html("<ul class='clearfix ul_pro'>"+ss+"<li class='li_close' onclick='closePro();'><span>关闭</span></li><li class='li_clean' onclick='cleanPro();'><span>清空</span></li></ul>");
	} 
	function showKeybord(){
			$("#pro").html("");
			var sss="";
			for(var i=0;i<keyNums.length;i++){
				sss=sss+'<li class="ikey ikey'+i+(i>9?" li_zm":" li_num") + (i==36?" li_enter":"") + (i>36?" li_w":"")+'" ><span onclick="choosekey(this,'+i+');">'+keyNums[i]+'</span></li>'
				
			} 
			$("#pro").html("<ul class='clearfix ul_keybord'>"+sss+"</ul>");
	}
    function addKeyProvince(provinceIds){
        var addHtml = '<li>';
            addHtml += '<span onclick="chooseProvince(this);">'+provinces[provinceIds]+'</span>';
            addHtml += '</li>';
            return addHtml;
    }

    function chooseProvince(obj){
       $(".input_pro span").text($(obj).text());
	   $(".input_pro").addClass("hasPro");
	   $(".input_pp").find("span").text("");
       $(".ppHas").removeClass("ppHas");
	   next=0;
	   showKeybord();
	}	
	
	
	function choosekey(obj,jj){			
		if(jj==36){			
			$("#banner").show();
			layer.closeAll();					
		}
		else if(jj==18){
			if($(".ppHas").length==0){
				$(".hasPro").find("span").text("");			
				$(".hasPro").removeClass("hasPro");	
				showProvince();
				next=0;
			}
			$(".ppHas:last").find("span").text("");			
			$(".ppHas:last").removeClass("ppHas");	
			next=next-1;
			if(next<1){
				next=0;
			}
			getpai();
			console.log(next);
		}
		else if(jj==35){
			$(".li_w").show();
		}		
		else{
			if(next>6){
				return
			}
			console.log(next);
			for(var i = 0; i<$(".input_pp").length;i++){
				if(next==0 & jj<10 & $(".input_pp:eq("+next+")").hasClass("input_zim")){
					layer.open({
						content: '车牌第二位为字母',
						skin: 'msg',
						time: 1
					});
					return
				}
				$(".input_pp:eq("+next+")").find("span").text($(obj).text());
				$(".input_pp:eq("+next+")").addClass("ppHas");
				next=next+1;
				if(next>6){
					next=7;
				}
				getpai();
				if(jj>35){
					$(".li_w").hide();
				}
				return
			}			
		}
		
		
       
	}
	function closePro(){	  
	   $("#banner").show();
       layer.closeAll()	   
	}		
	function cleanPro(){
       $(".ul_input").find("span").text("");
       $(".hasPro").removeClass("hasPro");
       $(".ppHas").removeClass("ppHas");
	   next=0;
	}	
	function trimStr(str){return str.replace(/(^\s*)|(\s*$)/g,"");}
	function getpai(){
		var pai=trimStr($(".car_input").text());
		$(".car_input").attr("data-pai",pai);
	}
 
   window.onload = function() {
	$(".input_pro").click(function(){
		$("#banner").hide();
		 layer.open({
			type: 1
			,content: '<div id="pro"></div>'
			,anim: 'up'
			,shade :false 
			,style: 'position:fixed; bottom:0; left:0; width: 100%; height: auto; padding:0; border:none;'
		  });
		 showProvince()
	})
	$(".input_pp").click(function(){
		 if($(".input_pro").hasClass("hasPro")){ // 如果已选择省份
			$("#banner").hide();
			 layer.open({
				type: 1
				,content: '<div id="pro"></div>'
				,anim: 'up'
				,shade :false 
				,style: 'position:fixed; bottom:0; left:0; width: 100%; height: auto; padding:0; border:none;'
			  });
			 showKeybord()
		 }else{
			 $(".input_pro").click()
		 }
	})
}