/*!
 * responsive NMU javascrips
 */

jQuery(document).ready(function($){  //run after the DOM has loaded
	//check for firefox and add a modernizr class (used in image widths below)
	Modernizr.addTest('firefox', function () {
	 return !!navigator.userAgent.match(/firefox|opera/i);
	});

	responsive_tweaks('load');

	//any time a user clicks on a page, we check to ensure the sidebars are the correct size.  this is needed to keep the sidebars in sync with toggled elements
	$(document).click(function(e) {
		sidebar_sizer();
	});

	//run the tweaks on page resize (but only if the page actually resizes)
	var x;
	var w=$(window).resize(function()
	{
		var newx=w.width();
		if(x!=newx)
		{
			$('#zone-branding-wrapper *, #zone-menu-wrapper *, #header_mobile_tools *').unbind('click');  //this will make sure that all the click event handlers are unbound from the nav areas before firing the responsive tweaks, otherwise the menus will grow and shrink as many times as the resize function is called during the window resize on each click
			responsive_tweaks('resize');
		}
		x=newx;
	});
});

jQuery(window).load(function($){  //run after all images have loaded
	sidebar_sizer();
});

function get_width(theType){
	//we get these dimensions at the function level because they cannot consistently be generated as global variables
	if(theType == 'window'){
		var windowWidth = jQuery(window).width();
		return windowWidth;
	}
	if(theType == 'scroll'){
		var scrollBarWidth = 0;
		if (jQuery.browser.mozilla)
			scrollBarWidth = window.innerWidth - jQuery("body").width(); //firefox will use the scroll bar when calculating the window width, webkit will not.  this eliminates that discrepancy
		return scrollBarWidth;
	}
}

function sidebar_sizer(){
	windowWidth = get_width('window');
	scrollBarWidth = get_width('scroll');
	jQuery('.region-content, .region-sidebar-first, .region-sidebar-second').css('min-height', '');  //reset the min-height first
	if(windowWidth<740-scrollBarWidth)
	{
		return false;
	}
	else
	{
		if (jQuery('.region-sidebar-second').is(":visible") && jQuery('.region-sidebar-first').is(":visible"))  //check to see if sidebars are shown
			jQuery('.region-content, .region-sidebar-first, .region-sidebar-second').equalHeights();
		else if(jQuery('.region-sidebar-second').is(":visible"))
			jQuery('.region-content, .region-sidebar-second').equalHeights();
		else if(jQuery('.region-sidebar-first').is(":visible"))
			jQuery('.region-content, .region-sidebar-first').equalHeights();
		else
			return false;
	}
}

function responsive_tweaks(theMethod){
	if(Modernizr.mq('(min-width: 0px)')) //only run the tweaks in browsers that understand the min-width media queries (as defined by modernizr)
	{
		windowWidth = get_width('window');
		scrollBarWidth = get_width('scroll');

		//re-determine the column's min height because it will change as the browser is resized.
		sidebar_sizer();

		//----------------------------------------------mobile view
		if(windowWidth<740-scrollBarWidth)
		{
			jQuery('.jquery_mobile').remove(); //make sure added items only appear once (keep this as the first item)

			//pull out the image height from all images in the content zone in mobile view
			//this allows an image to scale on small screens
			jQuery('#zone-content img').each(function(){
				jQuery(this).removeAttr('height');
				jQuery(this).css('height', '');
			});

			//pull out table widths in mobile view so they can scale to screen size
			//if a user shrinks and then grows their browser this will cause the widths to be lost until reload
			jQuery('#zone-content table').each(function(){
				jQuery(this).removeAttr('width')
				jQuery(this).css('width', '');
			});

			//add a class to iframes so they fit into mobile view
			jQuery('#zone-content iframe').each(function(){
				jQuery(this).addClass('jquery_iframe');
			});

		}
		else
		{
			jQuery('.jquery_mobile').remove(); //make sure items added at this size are removed
			jQuery('#zone-content iframe').each(function(){  //remove the iframe class added for mobile view
				jQuery(this).removeClass('jquery_iframe');
			});
		}

		/* no tweaks are needed at these sizes

		//----------------------------------------------narrow view
		if(windowWidth>740-scrollBarWidth && windowWidth<980-scrollBarWidth)
		{
			jQuery('.jquery_740').remove();
		}
		else
		{
			jQuery('.jquery_740').remove();
		}

		//----------------------------------------------normal view
		if(windowWidth>960-scrollBarWidth && windowWidth<1220-scrollBarWidth)
		{
			jQuery('.jquery_960').remove();
		}
		else
		{
			jQuery('.jquery_960').remove();
		}

		//----------------------------------------------wide view
		if(windowWidth>1220-scrollBarWidth)
		{
			jQuery('.jquery_1220').remove();
		}
		else
		{
			jQuery('.jquery_1220').remove();
		}
		*/
	}
}
