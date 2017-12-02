// JavaScript Document
(function($){
		var goTo = $(".num");/*整个屏幕的div*/
		var guide = $(".top");/*ul*/
		var guideLi = $(".top li");
		var index=0;
		var direct=0; 
		//设置宽高
		var resetFun = function(){ goTo.each(function(){  $(this).height( $(window).height() ) }); }
		resetFun();
		//屏幕滚动
		var goToFun = function(){ 
			direct=0; 
			if(index<0){ index=0; return }
			if(index>=guideLi.size()){ index=guideLi.size()-1; return }
			$("html,body").stop().animate({ scrollTop:$(window).height()*index },300,"swing",function(){direct=0; }); guideLi.removeClass("on").eq(index).addClass("on");  
		}
		guideLi.each(function(i){ $(this).click(function(){  index=guideLi.index( $(this) ); goToFun(); }) });
		$(window).resize(function(){ resetFun() });
		/* 滚轮事件 */ 
		var scrollFunc=function(e){ 
			e=e || window.event; 
			if(e.wheelDelta){ direct+= (-e.wheelDelta/120); }else if(e.detail){ direct+=e.detail/3 ; } 
			
			if( direct>=8 ){ goToFun( index++ ) }
			if( direct<=-8 ){ goToFun( index-- ) }
		} 
		if( document.addEventListener){ document.addEventListener('DOMMouseScroll',scrollFunc,false); }
		 window.onmousewheel=document.onmousewheel=scrollFunc; 

	})(jQuery);