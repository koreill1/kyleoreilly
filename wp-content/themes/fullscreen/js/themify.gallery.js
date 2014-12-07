/**
 * Fullscreen gallery and controller scripts.
 * Copyright (c) Themify
 */
;(function($){

	function showCaption(){
		$('.fullscreen-active .current-slide .fullscreen-caption').fadeIn(800);
	}
	function hideCaption(){
		$('.fullscreen-caption').fadeOut(500);
	}
	function highlight( items ){
		items.addClass('current-slide');
		showCaption();
	}
	function unhighlight(){
		hideCaption();
		$('#footer-slider').find('li').removeClass('current-slide');
	}
	function changeImage( items ){
		var bgImage = items.filter(':first').attr('data-bg');
		setTimeout(function(){
			$('body').find('.backstretch').first().remove();
		},15000 );
		$.backstretch(bgImage);
	}
	function swipe(event, direction){
		if( 'left' == direction ) {
			$('#footer-slider').find('.slides').trigger('next');
		} else {
			if( 'right' == direction ) {
				$('#footer-slider').find('.slides').trigger('prev');
			}
		}
	}

	// Toggle Content and go fullscreen
	var toggleContent = function(event){
		var $body = $('body');

		if( 'keyup' == event.type ){
			if( 27 != event.which || !$body.hasClass('fullscreen-active') ){
				return;
			}
		}
		var $fsButton = $('#fullscreen-button');

		if( ! $fsButton.is(':animated') ) {
			$fsButton.toggleClass('active');

			if($fsButton.hasClass('active')){
				// go fullscreen
				$fsButton.animate({
					right: '-40px'
				}, 100).animate({
						top: '10px'
					}, 100).animate({
						right: '10px'
					});
			} else {
				// back from fullscreen
				$fsButton.css({
					'right' : '-40px',
					'top' : ''
				}, 100).animate({
						bottom: '10px'
					}, 100).animate({
						right: '10px'
					});
			}
		}
		$body.toggleClass('fullscreen-active');
		$('#pagewrap, #pattern').fadeToggle();

		if( $body.hasClass('fullscreen-active') ) {
			// go fullscreen
			$('.fullscreen-active #gallery-controller').animate({bottom: '10px'}, 700);
			showCaption();

			// Enable swipe
			$body.swipe( {
				swipe:swipe,
				threshold:75
			} );
		} else {
			// back from fullscreen
			$('#gallery-controller').animate({bottom: '-100px'}, 700);
			hideCaption();
			// Remove swipe
			$body.swipe('destroy');
		}
	};

	// Adjust slider on window resizing stop
	$(window).on('resizestop', function() {
		if( ! $('.carousel-playback').hasClass('paused') ) {
			$('#footer-slider').find('.slides').trigger('next');
		}
	});

	$(document).ready(function() {

		/////////////////////////////////////////////
		// Parse injected vars
		/////////////////////////////////////////////
		themifyVars.autoplay = parseInt(themifyVars.autoplay)*1000;
		themifyVars.speed = parseInt(themifyVars.speed);
		themifyVars.play = (themifyVars.play != 'no');

		/////////////////////////////////////////////
		// Go fullscreen
		/////////////////////////////////////////////
		$('#fullscreen-button').on( 'true' == themifyScript.isTouch? 'touchend' : 'click', toggleContent );
		$('body').on( 'keyup', toggleContent);

		/////////////////////////////////////////////
		// Pause carousel
		/////////////////////////////////////////////
		$('.carousel-playback').click(function(){
			$(this).toggleClass('paused');
		});

	});

	$(window).load(function(){

		/////////////////////////////////////////////
		// Initialize fullscreen background
		/////////////////////////////////////////////

		var themifyImages = [],
			$footerSlider = $('#footer-slider');

		// Initialize images array with URLs
		$footerSlider.find('li').each(function(){
			themifyImages.push( $(this).attr('data-bg') );
		});

		$(themifyImages).each(function() {
			$('<img/>').attr('src', this);
		});

		// Hide loading animation
		$( '#pattern' ).removeClass( 'loading' );

		// Call backstretch for the first time
		$.backstretch(themifyImages[0], {
			fade : 500
		});

		/////////////////////////////////////////////
		// Slider
		/////////////////////////////////////////////
		if($footerSlider.length > 0){
			// Leave initial image for as long as themifyVars.autoplay/2 before starting with the rest and autoplay
			setTimeout(function(){
				var itemIndex = ($footerSlider.find('li').length > 5)? '0': '1';
				$footerSlider.find('.slides').carouFredSel({
					responsive: true,
					prev: {
						button: '#footer-slider .carousel-prev',
						key: 'left',
						onBefore: function(items) {
							var newItems = items.items.visible;
							unhighlight();
							changeImage( newItems );
						},
						onAfter	: function(items) {
							var newItems = items.items.visible;
							highlight( newItems.filter(':eq(0)') );
						}
					},
					next: {
						button: '#footer-slider .carousel-next',
						key: 'right',
						onBefore: function(items) {
							var newItems = items.items.visible;
							unhighlight();
							changeImage( newItems );
						},
						onAfter	: function(items) {
							var newItems = items.items.old;
							highlight( newItems.filter(':eq('+itemIndex+')') );
						}
					},
					width: '100%',
					auto: {
						play: themifyVars.play,
						timeoutDuration: themifyVars.autoplay,
						button: '#footer-slider .carousel-playback'
					},
					swipe: true,
					scroll: {
						items: 1,
						duration: themifyVars.speed,
						onBefore: function(items) {
							var newItems = items.items.visible;
							unhighlight();
							changeImage( newItems );
						},
						onAfter: function(items) {
							var newItems = items.items.visible;
							highlight( newItems.filter(':eq('+itemIndex+')') );
						}
					},
					items: {
						visible: 5,
						minimum: 1,
						width: 70
					},
					onCreate: function(){
						$footerSlider.css({
							'height': 'auto',
							'visibility' : 'visible'
						});

						$('#footer-slider .carousel-next, #footer-slider .carousel-prev').wrap('<div class="carousel-arrow"/>');
						$footerSlider.find('.caroufredsel_wrapper + .carousel-nav-wrap').remove();

						$footerSlider.find('li:first').addClass('current-slide');

						if( $footerSlider.find('li').length > 2 ) {
							$('.carousel-playback').css('display', 'inline-block');
						}

						if( ! themifyVars.play ) {
							$('.carousel-playback').hide();
						}
					}
				}).find('li').on('click', function() {
					hideCaption();
					$footerSlider.find('li').removeClass('current-slide');
					$(this).addClass('current-slide');
					showCaption();
					$footerSlider.find('li').trigger('slideTo', [
						$(this),
						0,
						false,
						{
							items: 1,
							duration: 300,
							onBefore: function(items){	},
							onAfter	: function(items){	}
						},
						null,
						'next']);

					// Set image and index using current data properties
					changeImage( $(this) );

				}).css('cursor', 'pointer');

			}, themifyVars.autoplay/2 ); // end setTimeout

		} // end if #footer-slider

	});

}(jQuery));