// bbf-slider-autoplay.js
// This script enables auto-rotation for sliders with class 'bbf-slider' using Slick's API.
// Requires jQuery and Slick Slider.

// bbf-slider-autoplay.js
// This script enables auto-rotation for sliders with class 'bbf-slider' using Slick's API.
// Requires jQuery and Slick Slider.

(function ($) {
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
