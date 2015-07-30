jQuery(document).ready(function($) {
	
	var ADMIN_MENU_SLIDE = function() {
		
	//Initialization:
		
		//get information about the screen and exit if touchscreen
		var is_touch= (('ontouchstart' in window) || (navigator.MaxTouchPoints > 0) || (navigator.msMaxTouchPoints > 0));
		if(is_touch) return;
		
		var object=this;
		
		var resizeTimeout;
		
		var can_ajax=true;
		var ajax_again=false;
		var ajaxTimer;
		
		var settings={ 
			"sidebar_slide": parseInt(AMS_PHP.sidebar_slide),
			"sidebar_slide_duration": parseInt(AMS_PHP.sidebar_slide_duration),
			"sidebar_collapse": ( $('body').hasClass('folded') || ( $('body').hasClass('auto-fold') && $(window).width()<=960 ) )

		}
	
		var locale_text={
			"enable_menuslide": AMS_PHP.text_enable_menuslide,
			"disable_menuslide": AMS_PHP.text_disable_menuslide
		}
		
		this.init = function() {
			
			//add slide button
			var slide_button
			if (settings["sidebar_slide"]===0)
				slide_button='<li id="adminmenuslide-toggle"><div>'+AMS_PHP.svgIcons['menu_slide_enable']+'</div><span class="wp-menu-name">'+locale_text["enable_menuslide"]+'</span></li>';
			else
				slide_button='<li id="adminmenuslide-toggle"><div>'+AMS_PHP.svgIcons['menu_slide_disable']+'</div><span class="wp-menu-name">'+locale_text["disable_menuslide"]+'</span></li>';

			$('#adminmenu').append(slide_button);
					
			if ($('body').hasClass('folded')) sidebar_slide_position='-36px';
			else sidebar_slide_position='-160px';
			
			//resize event
			$(window).on('resize', object.resizeHandler);
		
		
			$('html').on('mouseenter', 'body.admin-menu-slide #adminmenuwrap', function() { $('#adminmenuwrap').stop(true, false).animate({left: "0px", paddingRight: "0px"},settings["sidebar_slide_duration"]); });
			$('html').on('mouseleave', 'body.admin-menu-slide #adminmenuwrap', function() { $('#adminmenuwrap').stop(true, false).animate({left: sidebar_slide_position, paddingRight: "5px"},settings["sidebar_slide_duration"]); });
			$('#adminmenu').on('click','#adminmenuslide-toggle', function() { object.sidebarSlideToggle(); });
			
			
			$('#adminmenu').on('click','#collapse-menu', function() { object.sidebarCollapseToggle(); });
			
			$(document).on('heartbeat-send', function(e, data) {
				data['windowpress-menu-slide']=settings["sidebar_slide"];
			});

		}

	//Functions:

		this.resizeHandler = function() { //screen resize
			clearTimeout(resizeTimeout);
			resizeTimeout=setTimeout(function() {
				
				object.sidebarCollapseAgent();
							
				//disable sidebar sliding if screen is too small
				if ($(window).width()<783 && settings["sidebar_slide"]) object.sidebarSlideDisable();
			},100);
		}
		
		this.sidebarSlideEnable = function() {
			settings["sidebar_slide"]=1;
			$('#adminmenuslide-toggle span').html(locale_text["disable_menuslide"]);
			$('#adminmenuslide-toggle > div').html(AMS_PHP.svgIcons["menu_slide_disable"]);
			$('body').addClass('admin-menu-slide');
			$('body').addClass('admin-menu-slide');

			
		}
		
		this.sidebarSlideDisable = function() {
		
			settings["sidebar_slide"]=0;
			$('#adminmenuslide-toggle span').html(locale_text["enable_menuslide"]);
			$('#adminmenuslide-toggle > div').html(AMS_PHP.svgIcons["menu_slide_enable"]);
			$('body').removeClass('admin-menu-slide');
			$('body').removeClass('admin-menu-slide');
			$('#adminmenuwrap').css({left:"0px",paddingRight:"0px"});
	
		}
		
		this.sidebarSlideToggle = function() {
		
			if ( $('body').hasClass('admin-menu-slide') ) { object.sidebarSlideDisable(); }
			else { object.sidebarSlideEnable(); }
			
			//update user meta, allow only one request every 3 seconds
			if (can_ajax) { 
				can_ajax=false;
				object.sidebarSlideToggleAjax();
				ajaxTimer=setTimeout(function() {
					can_ajax=true;
					if (ajax_again) {
						object.sidebarSlideToggleAjax();
						ajax_again=false;
					}
				},3000);
			}
			else ajax_again=true;
		}
		
		this.sidebarSlideToggleAjax = function() {
			$.get( AMS_PHP.ajax_url, { action: "adminmenuslide_toggle", value: settings["sidebar_slide"] } );
		}
	
		this.sidebarCollapseToggle = function() {
				
			if (settings["sidebar_collapse"]) {
				sidebar_slide_position='-160px';
				settings["sidebar_collapse"]=false;
			}
			else {
				sidebar_slide_position='-36px';
				settings["sidebar_collapse"]=true;	
			}
		}
	
		this.sidebarCollapseAgent = function() {
			//detect if menu folded/unfolded automatically
			
			var collapsed = ( $('body').hasClass('folded') || ( $('body').hasClass('auto-fold') && $(window).width()<=960 ) );
			var collapsed_settings=settings["sidebar_collapse"];
			
			//toggle collapse if settings do not match
			if ( collapsed !== collapsed_settings ) { 
				object.sidebarCollapseToggle();
				$('#adminmenuwrap').css('left','0px');
			}
		}
		
		object.init();
	}
	
	var AdminMenuSlide=new ADMIN_MENU_SLIDE();

});
