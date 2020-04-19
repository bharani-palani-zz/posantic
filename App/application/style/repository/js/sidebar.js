$(function() {

	//side menu toggle (init)
	if (isIE() <= 9) {
		$('#sidebar').find("li.active").has("ul").children("ul").collapse("show");
		$('#sidebar').find("li").not(".active").has("ul").children("ul").collapse("hide");
	} else {
		$('#sidebar').find("li.active").has("ul").children("ul").addClass("collapse in");
		$('#sidebar').find("li").not(".active").has("ul").children("ul").addClass("collapse");
	}
	
	//side menu toggle (setting)
	$("#sidebar-area .dropdown-collapse").on((jQuery.support.touch ? "tap" : "click"), function(e) {
		e.preventDefault();
		
		if ($("body").hasClass("sidebar-closed")) {
			return false;
		}
		
		$(this).parent("li").toggleClass("active").children("ul").collapse("toggle")
		
		//if ($toggle) { //toggle On ・ Off
		
		$(this).parent("li").siblings().removeClass("active").children("ul.in").collapse("hide");
		
		//}
		return false;
	});
			
	handleSidebarToggler();
});


//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
	var width = (window.innerWidth > 0) ? window.innerWidth : screen.width;

	if ($.cookie && $.cookie('sidebar-closed') === '1' && !$('body').hasClass("sidebar-closed") && width >= 768) {
		$('body').addClass("sidebar-closed");
		$('#sidebar .nav-second-level, #sidebar .nav-third-level').removeClass('collapse');
	}
	
	$(window).bind("load resize", function() {
		topOffset = 50;
		var body = $('body');
		var sidebarMenuSubs = $('#sidebar .nav-second-level, #sidebar .nav-third-level');
		
		width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
		
		if (width < 768) {
			$('div.navbar-collapse').addClass('collapse');
			topOffset = 100; // 2-row-menu
			
			if (body.hasClass("sidebar-closed")) {
				body.removeClass("sidebar-closed");
				sidebarMenuSubs.addClass('collapse');
			}

		} else {
			$('div.navbar-collapse').removeClass('collapse');

			/*
			if ($.cookie) {
				if ($.cookie('sidebar-closed') === 1 && !$('body').hasClass("sidebar-closed")) {
					body.addClass("sidebar-closed");
					sidebarMenuSubs.removeClass('collapse');
				}
			}*/
			if($('body').hasClass("sidebar-closed"))
			{
				$('.usage_div').hide();
			} else {
				$('.usage_div').show();				
			}

		}

		height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
		height = height - topOffset;
		if (height < 1) height = 1;
		if (height > topOffset) {
			$("#page-wrapper").css("min-height", (height) + "px");
		} 
	});

	var url = window.location;
	var element = $('ul.nav a').filter(function() {
		return this.href == url;
	}).addClass('active').parent().parent().addClass('in').parent();
	if (element.is('li')) {
		element.addClass('active');
	}
	
	//ページローディング画面の非表示化
	if ($('#pageLoading').css("display") == "block") {
		$('#pageLoading').delay(100).css("display","none");
	}
	//$("link#toggle_stylesheet").attr("href",$.cookie("example"))

	$('#theme_options li a').on('click',function(){
		var base_url = $('#base_url').val()
		var root = $(this).attr('data-root')
		var user = $('#theme_user').val()
		var error_root = $('#theme_options li:first a').attr('data-root')
		$.ajax({ 
			url: $('#theme_url').val(),
			data: { theme_root: root, theme_user: user, csrf_test_name : $("input[name=csrf_test_name]").val()},
			type: "POST",
			beforeSend: function(data){
				$('#theme_icon').removeClass('fa-heart')
				$('#theme_icon').addClass('fa-circle-o-notch fa-spin')
			},
			success: function(data){
				if(data == 1)
				{
					$("link#toggle_stylesheet").attr("href",base_url+root);	
				}
			},
			complete: function(){
				$('#theme_icon').removeClass('fa-circle-o-notch fa-spin')
				$('#theme_icon').addClass('fa-heart')
			},
			error: function(jqXHR, textStatus, errorThrown) {		
				$("link#toggle_stylesheet").attr("href",base_url+error_root);	
			}
		});
	});
	
});
//Top Toggler
var handleSidebarToggler = function () {
	var body = $('body');
	
	// handle sidebar show/hide
	body.on('click', '.sidebar-toggler', function (e) {
		var sidebarMenuSubs = $('#sidebar .nav-second-level, #sidebar .nav-third-level');
		$("#sidebar-area .dropdown-collapse").parent("li").children("ul").css({'height' : ''});
		
		$(".sidebar-search", $('.page-sidebar')).removeClass("open");
		if (body.hasClass("sidebar-closed")) {
			body.removeClass("sidebar-closed");
			sidebarMenuSubs.addClass('collapse');
			
			if ($.cookie) {
				$.cookie('sidebar-closed', '0');
			}
		} else {
			body.addClass("sidebar-closed");
			sidebarMenuSubs.removeClass('collapse');
			
			if ($.cookie) {
				$.cookie('sidebar-closed', '1');
			}
		}
		$(this).find('i').toggleClass('fa-chevron-circle-left fa-chevron-circle-right');
		
		$(window).trigger('resize');
	});
};

//IE Checker
var isIE = function() {
	var undef,
		v = 3,
		div = document.createElement("div"),
		all = div.getElementsByTagName("i");
	while (
		div.innerHTML = "<!--[if gt IE " + (++v) + "]><i></i><![endif]-->",
		all[0]
	) {
		return v > 4 ? v : undef;
	}
}

