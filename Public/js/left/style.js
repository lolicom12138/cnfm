$(function(){
	$(".banking_processing_con_tit tr").click(function(){
		if($(this).hasClass("tr_selected")||$(this).hasClass("fenye_style")){
			$(this).removeClass("tr_selected");
		}else{
			$(this).addClass("tr_selected");
			$(this).siblings(".tr_addcolor").removeClass("tr_selected");
		}
	})
	var content=[
	             ".fa:contains('我的主页')",
	             ".fa:contains('财务处理') ",
	             ".fa:contains('开票流水查询') ",
	             ".fa:contains('批量管理')",
	             ]
	 var icons=[
	        	"fa-bank",
	        	"fa-desktop",
	        	"fa-credit-card",
	        	"fa-list-alt",
	          ]
	var icons_selected=[
	        	"fa-bank-selected",
	        	"fa-desktop-selected",
	        	"fa-credit-card-selected",
	        	"fa-list-alt-selected",
	          ]
	var length=content.length=icons.length;
	var first=$(".layout").children().children("div:eq(0)").find(".fa");
	for(var index=0;index<length;index++){
		$(".layout").find(content[index]).addClass(icons[index]);
		if(first.hasClass(icons[index])){
			$(".layout").find(content[index]).removeClass(icons[index]).addClass(icons_selected[index]);
		}
	}
	
	
		$(".menu ").click(function(){
			var contents=[
	             ".fa:contains('我的主页')",
	             ".fa:contains('财务处理') ",
	             ".fa:contains('开票流水查询') ",
	             ".fa:contains('批量管理')",
	             ]
			var icon=[
	        	"fa-bank",
	        	"fa-desktop",
	        	"fa-credit-card",
	        	"fa-list-alt",
	          ]

			var icon_selected=[
			        	"fa-bank-selected",
			        	"fa-desktop-selected",
			        	"fa-credit-card-selected",
			        	"fa-list-alt-selected",
			          ]
			var length=icon.length=icon_selected.length;
			var otherdiv=$(this).parent().siblings().children("div");
			for(var index=0;index<length;index++){	
				if($(this).hasClass(icon[index])){
					for(var n=0;n<length;n++){
						if(otherdiv.hasClass(icon_selected[n])){
						  $(this).parent().siblings().find(contents[n]).addClass(icon[n]).removeClass(icon_selected[n]);
						}
					}
					$(this).addClass(icon_selected[index]).removeClass(icon[index]);
				}else if($(this).hasClass(icon_selected[index])){
					$(this).addClass(icon[index]).removeClass(icon_selected[index]);
				}
			}
			var nextul=$(this).next('ul');
			var otherul=$(this).parent().siblings().children("ul");
			if(nextul.hasClass("hide")){
				nextul.addClass("show").removeClass("hide");
				$(this).children("span").css("color","#fff");
				$(this).parent().siblings().children("div").find("span").css("color","#E4E4E4");
				otherul.addClass("hide");
			}else{
				nextul.addClass("hide").removeClass("show");
				$(this).children("span").css("color","#E4E4E4");
			}
			
		})

		$(".leftmenu li").click(function(){
			var textt =$(this).index();
			var texttp=$(this).parent().parents().index();
			var tabids="tabids_"+texttp+"_"+textt;
			var con=$(this).text();
			var conlengths=con.length;
			$(this).children("a").addClass("a_selectedcolor");
			$(this).siblings('li').children("a").removeClass("a_selectedcolor");
			$(this).parents("div").siblings('div').children("ul").find("a").removeClass("a_selectedcolor");
			if(conlengths>2){
				var conomiss=con.substring(0,3)+"..";
			}else{
				var conomiss=con.substring(0,3);
			}
			
			var texts =$(this).parents("div").index();
			var textsdiv=texts+(1);
		
				var mcont0divone=$(this).parent().parents("div").siblings("#bb").children("div:eq(0)");
				
				var mcont0ul=$(this).parent().parents("div").siblings("#bb").children("div:eq(" + textsdiv + ")");
				
				var mcont0uls=mcont0ul.children("ul:eq(" + textt + ")");
				
				var mcont0ulsdiv=mcont0uls.children("div").length;
				
				mcont0uls.addClass('show ').removeClass('hide ');
				mcont0uls.siblings().removeClass('show ').addClass('hide ');
				mcont0uls.parent().siblings().children("ul").removeClass('show ').addClass('hide');

				var mcontdivtab=mcont0divone.children("a:contains(" + conomiss + ")").index();

				var mcontdivtabcon=mcont0divone.children("a:eq(" + mcontdivtab + ")").text();
				
					if(conomiss!=mcontdivtabcon){

						mcont0divone.prepend("<a href='#' class='tab'"+"id="+">"+conomiss+"<span class='fa fa-remove'></span>"+"</a>");
						
					}

				mcont0divone.children("a:contains(" + conomiss + ")").attr("title",con);

				mcont0divone.children("a:contains(" + conomiss + ")").attr("id",tabids);
				mcont0divone.children("a:contains(" + conomiss + ")").addClass("selecteds");
				mcont0divone.children("a:contains(" + conomiss + ")").siblings().removeClass("selecteds");
				if(mcont0ulsdiv<1){

					mcont0uls.prepend("<div class='hide'>"+conomiss+"</div>");
				}
				

				$(".tabdiv a").click(function(){

						var tabdiva=$(this).index();

						var tabdivacon=$(this).text();

						var mcont0uldivcon=$(this).parent().siblings().find("div:contains(" + tabdivacon + ")");

						$(this).addClass("selecteds");
						$(this).siblings().removeClass("selecteds");

						mcont0uldivcon.parent().addClass('show').removeClass('hide');
						mcont0uldivcon.parent().siblings("ul").addClass('hide').removeClass('show');
						mcont0uldivcon.parent().parent().siblings().children("ul").addClass('hide').removeClass('show');
				})	

				$(".tabdiv span").click(function(){
					$(this).parent().remove();
				})
				
				

				$('.tabdiv a').contextMenu('myMenu', {

		      menuStyle: {
		        border: '1px solid #ccc'
		      },

		      itemStyle: {
		        backgroundColor : '#F5F2F2',
		        color: 'rgb(70, 67, 67)',
		        border: 'none',
		        padding: '5px'
		      },

		      itemHoverStyle: {
		        color: 'blue',
		        backgroundColor: 'red',
		        border: 'none'
		      },

		      bindings: 
		          {
		            'close': function(t) {
            			$('.tabdiv').find("#"+t.id).remove();
		            },
		            'close_other': function(t) {
              			$('.tabdiv').find("#"+t.id).siblings().remove();
		            },
		            'close_all': function(t) {
		              $('.tabdiv a').remove();
		            }
		          }
		    })
		})
		

		$(document).ready(function(){
          $('.slider6').bxSlider({
							mode: 'fade',
              slideWidth: 700, 
            	slideMargin: 10,
            	auto: true,
            	speed: 500, 
            	randomStart:false,
            	captions: true,
            	autoControls: true,
            	captions: false,
            	controls: true, 
            	pause: 2000, 
            	autoHover: true,  
          });
        });
        
       
      
        
	})

	$(function(){
		updatePastTime();
	})


function updatePastTime(){
	var date = new Date();
	var time = date.getTime(); 
	$(".settime").each(function(i){
		var endDate =this.getAttribute("pasttime"); 

		var endDate1 = eval('new Date(' + endDate.replace(/\d+(?=-[^-]+$)/, function (a) { return parseInt(a, 10) - 1; }).match(/\d+/g) + ')');
		var date1=new Date(endDate1);
		if((new Date()-date1)>0){
	 		var totalSecs=(new Date()-date1)/1000;
	 		var days=Math.floor(totalSecs/3600/24);
	 		var hours=Math.floor((totalSecs-days*24*3600)/3600);
	 		var mins=Math.floor((totalSecs-days*24*3600-hours*3600)/60);
	 		var secs=Math.floor((totalSecs-days*24*3600-hours*3600-mins*60));
			if(days>0){
			 	$(this).html(days+"天"+hours+"小时"+mins+"分钟"+secs+"秒");
			 	
			}else if(hours>0){
				$(this).html(hours+"小时"+mins+"分钟"+secs+"秒");
			 
			}else if(mins>0){
				$(this).html(mins+"分钟"+secs+"秒");
				
			}else{
				$(this).html(secs+"秒");
			}
		}else{
			$(this).html("过去时间大于当前时间了，请用倒计时！！");
		}
	});
	setTimeout("updatePastTime()",1000);
}

$(document).ready(function() {
    
    $('.pagination').jqPagination({
        link_string: '/?page={page_number}',
        max_page: 40,
        paged: function(page) {
            $('.log').prepend('<li>Requested page ' + page + '</li>');
        }
    });

});