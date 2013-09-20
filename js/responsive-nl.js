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

			//create the mobile navigation button
			jQuery('#nl-top-nav').before('<div class="jquery_mobile mobile-nav-div" id="mobile-nav-block">CAMPUS<a class="btn btn-small jquery_mobile" id="branding_nav_icon"><i class="icon-reorder"></i></a></div>');
			jQuery('#nl-top-nav').hide();
			jQuery('#mobile-nav-block').click(function(){
				jQuery('#nl-top-nav').slideToggle();
				jQuery('#branding_nav_icon').toggleClass('btn-success');
			});


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
			jQuery('#nl-top-nav').show();
			jQuery('#zone-content iframe').each(function(){  //remove the iframe class added for mobile view
				jQuery(this).removeClass('jquery_iframe');
			});
		}



		//----------------------------------------------narrow view
		if(windowWidth>740-scrollBarWidth && windowWidth<980-scrollBarWidth)
		{
			jQuery('.jquery_740').remove();
			jQuery('.campus-closeup').removeAttr('width');
			jQuery('.campus-closeup').css('width', '');
		}
		else
		{
			jQuery('.jquery_740').remove();
		}

		//----------------------------------------------normal view
		if(windowWidth>960-scrollBarWidth && windowWidth<1220-scrollBarWidth)
		{
			jQuery('.jquery_960').remove();
			jQuery('.campus-closeup').removeAttr('width');
			jQuery('.campus-closeup').css('width', '');
		}
		else
		{
			jQuery('.jquery_960').remove();
		}

		/* no tweaks are needed at this size
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
