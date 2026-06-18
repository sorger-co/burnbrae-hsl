// bbf-slider-autoplay.js
// This script enables auto-rotation for sliders with class 'bbf-slider' using Slick's API.
// Requires jQuery and Slick Slider.

(function ($) {
	// Prevent adaptiveHeight from shrinking the slider when shorter slides are shown.
	// Without this, transitioning to a slide with less content creates a white gap
	// between the slider and the next page section.
	function pinSliderMinHeight($slider) {
		var maxHeight = 0;
		$slider.find('.wpr-slider-item').each(function () {
			var h = $(this).outerHeight(true);
			if (h > maxHeight) maxHeight = h;
		});
		if (maxHeight > 0) {
			$slider.find('.slick-list').css('min-height', maxHeight + 'px');
		}
	}

	// Wait for Elementor frontend and Slick to be ready
	function initAutoplay() {
		// Find all sliders inside .bbf-slider that are initialized with Slick
		$('.bbf-slider .wpr-advanced-slider').each(function () {
			var $slider = $(this);
			// Only proceed if Slick is initialized
			if (!$slider.hasClass('slick-initialized')) return;

			// Prevent multiple intervals
			if ($slider.data('bbf-autoplay')) return;
			$slider.data('bbf-autoplay', true);

			// Pin min-height to tallest slide to prevent gap on shorter slides
			pinSliderMinHeight($slider);

			var interval = 5000; // 5 seconds
			var timer = setInterval(function () {
				$slider.slick('slickNext');
			}, interval);

			// Pause on hover
			$slider.hover(
				function () {
					clearInterval(timer);
				},
				function () {
					timer = setInterval(function () {
						$slider.slick('slickNext');
					}, interval);
				}
			);
		});
	}

	// Elementor/Royal Addons may initialize sliders after DOM ready
	$(document).ready(function () {
		// Try immediately
		initAutoplay();
		// Listen for Elementor frontend events (for dynamic content)
		$(window).on('elementor/frontend/init', function () {
			setTimeout(initAutoplay, 500);
		});
		// Listen for Royal Addons slider events (if any)
		$(document).on('wpr-slick-init', function () {
			setTimeout(initAutoplay, 500);
		});
		// Fallback: try again after 2 seconds in case of async load
		setTimeout(initAutoplay, 2000);
	});
})(jQuery);
