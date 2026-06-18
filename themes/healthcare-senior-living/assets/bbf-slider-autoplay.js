(function ($) {
    function initAutoplay() {
        $('.bbf-slider .wpr-advanced-slider').each(function () {
            var $slider = $(this);
            if (!$slider.hasClass('slick-initialized')) return;
            if ($slider.data('bbf-autoplay')) return;
            $slider.data('bbf-autoplay', true);

            // Turn off Slick's adaptiveHeight so it no longer calls animateHeight()
            // or setHeight() on slide transitions. Heights will be managed below.
            $slider.slick('slickSetOption', 'adaptiveHeight', false, false);

            // Capture the tallest slide height (and the slider's current rendered height)
            // so we can pin both the container and list to this value.
            var maxHeight = $slider.outerHeight() || 0;
            $slider.find('.wpr-slider-item').each(function () {
                var h = $(this).outerHeight(true) || 0;
                if (h > maxHeight) maxHeight = h;
            });

            function pinHeight() {
                if (!maxHeight) return;
                // min-height prevents WPR's afterChange from shrinking the slider
                // (CSS resolves rendered height = max(min-height, height)).
                $slider.css('min-height', maxHeight + 'px');
                $slider.find('.slick-list').css('min-height', maxHeight + 'px');
                // Also set height explicitly to override any in-progress jQuery animate.
                $slider.css('height', maxHeight + 'px');
                $slider.find('.slick-list').css('height', maxHeight + 'px');
            }

            pinHeight();

            // WPR registers its afterChange handler before ours, so ours fires last
            // and overrides the height WPR set on the container.
            $slider.on('afterChange.bbf', function () {
                pinHeight();
            });

            var interval = 5000;
            var timer = setInterval(function () {
                $slider.slick('slickNext');
            }, interval);

            $slider.hover(
                function () { clearInterval(timer); },
                function () {
                    timer = setInterval(function () {
                        $slider.slick('slickNext');
                    }, interval);
                }
            );
        });
    }

    $(document).ready(function () {
        initAutoplay();
        $(window).on('elementor/frontend/init', function () {
            setTimeout(initAutoplay, 500);
        });
        $(document).on('wpr-slick-init', function () {
            setTimeout(initAutoplay, 500);
        });
        setTimeout(initAutoplay, 2000);
    });
})(jQuery);
