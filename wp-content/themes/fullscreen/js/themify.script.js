;// Themify Theme Scripts - http://themify.me/

(function($){

$(document).ready(function(){


	/////////////////////////////////////////////
	// Add image-wrap to images for styling 							
	/////////////////////////////////////////////
	$('.post-image img, #slider img, .gallery img, .pagewidth .avatar, .flickr_badge_image img, .attachment img, .feature-posts-list .post-img, img.alignleft, img.aligncenter, img.alignright, img.alignnone, .wp-caption img, .slide-image img').each(function() {
		var imgClass = $(this).attr('class');
		$(this).wrap('<span class="image-wrap ' + imgClass + '" style="width: auto; height: auto;"/>');
		$(this).removeAttr('class');
	});
	
	$('.slides img').each(function() {
		var imgClass = $(this).attr('class');
		$(this).wrap('<a href="#" class="image-wrap ' + imgClass + '" style="width: auto; height: auto;"/>');
		$(this).removeAttr('class');
	});

	/////////////////////////////////////////////
	// Scroll to top
	/////////////////////////////////////////////
	$('.back-top a').click(function() {
		$('body,html').animate({
			scrollTop : 0
		}, 800);
		return false;
	});

	
	/////////////////////////////////////////////
	// Toggle menu on mobile
	/////////////////////////////////////////////
	var $mainNav = $('#main-nav' ),
		$menuButton = $('#menu-button');

	$menuButton.hover(
		function() {
			$mainNav.fadeIn().addClass('active');
		}
		,
		function() {
			window.setTimeout(
				function() {
					if( !$mainNav.hasClass('active') ) {
						$mainNav.removeClass('active').fadeOut();
					}
				},
				300
			);
		}
	);

	$menuButton.on('touchstart',
		function() {
			if ( ! $mainNav.hasClass( 'active' ) ) {
				$mainNav.stop().addClass('active' ).fadeIn();
			} else {
				$mainNav.stop().removeClass('active' ).fadeOut();
			}
		}
	);

	$mainNav.hover(
		function(){
			$(this).addClass('active');
		}
		,
		function(){
			$(this).removeClass('active').fadeOut();
		}
	);

	// Lightbox / Fullscreen initialization ///////////
	if(typeof ThemifyGallery !== 'undefined'){ ThemifyGallery.init({'context': jQuery(themifyScript.lightboxContext)}); }

});

$(window).load(function(){
	// expand slider
	$('#slider .slides').css('height','auto');

	if(typeof (jQuery.fn.carouFredSel) !== 'undefined'){
		$('.portfolio .slideshow').each(function(){
			$this = $(this);
			$this.carouFredSel({
				responsive: true,
				prev: '#' + $this.data('id') + ' .carousel-prev',
				next: '#' + $this.data('id') + ' .carousel-next',
				pagination: { container: '#' + $this.data('id') + ' .carousel-pager' },
				circular: true,
				infinite: true,
				swipe: true,
				scroll: {
					items: 1,
					fx: $this.data('effect'),
					duration: parseInt($this.data('speed'))
				},
				auto : {
					play: 'off' != $this.data('autoplay')? true: false,
					timeoutDuration: 'off' != $this.data('autoplay')? parseInt($this.data('autoplay')): 0
				},
				items: {
					visible: {
						min: 1,
						max: 1
					},
					width: 222
				},
				onCreate : function(){
					$('.slideshow-wrap').css({'visibility':'visible', 'height':'auto'});
					$('.carousel-next, .carousel-prev', $this.closest('.slideshow-wrap')).hide();
					$(window).resize();
				}
			});
		});
		
		// Show/Hide direction arrows
		$('#body').on('mouseover mouseout', '.slideshow-wrap', function(event) {
			if (event.type == 'mouseover') {
				if( $(window).width() > 600 ){
					$('.carousel-next, .carousel-prev', $(this)).css('display', 'block');
				}
			} else {
				if( $(window).width() > 600 ){
					$('.carousel-next, .carousel-prev', $(this)).css('display', 'none');
				}
			}
		});
	} // end if typeof caroufredsel

}); // end window load

}(jQuery));