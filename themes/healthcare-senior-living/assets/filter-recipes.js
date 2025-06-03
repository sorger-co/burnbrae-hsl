jQuery(document).ready(function ($) {
	// Store initial button text for load more
	var $loadMoreBtn = $('#load-more-posts');
	var initialBtnText = $loadMoreBtn.text();

	// Filtering
	var currentFilters = {};
	function getCurrentFilters() {
		var filterData = {};
		$('.recipe-accordion-filter .recipe-filter:checked').each(function () {
			var name = $(this).attr('name').replace('[]', '');
			if (!filterData[name]) filterData[name] = [];
			filterData[name].push($(this).val());
		});
		return filterData;
	}

	$('.recipe-accordion-filter').on('change', '.recipe-filter', function () {
		currentFilters = getCurrentFilters();
		var filterData = Object.assign({}, currentFilters);
		filterData['action'] = 'healthcare_filter_recipes';
		filterData['query_vars'] = healthcare_ajax_loadmore.query_vars;
		// AJAX call for filtering
		$.ajax({
			url: healthcare_ajax_loadmore.ajax_url,
			type: 'POST',
			data: filterData,
			beforeSend: function () {
				$('.archive-grid').addClass('loading');
				$loadMoreBtn.hide();
			},
			success: function (res) {
				if (res.success && res.data.html) {
					$('.archive-grid').html(res.data.html);
					// If there are more pages, show load more and reset its state
					if (res.data.has_more) {
						$loadMoreBtn.data('current-page', 1).show().text(initialBtnText);
					} else {
						$loadMoreBtn.hide();
					}
				}
				$('.archive-grid').removeClass('loading');

				// Mark unavailable filter options
				if (res.data.unavailable_terms) {
					$('.recipe-accordion-filter .recipe-filter').each(function () {
						var $cb = $(this);
						var tax = $cb.attr('name').replace('[]', '');
						var val = $cb.val();
						if (
							res.data.unavailable_terms[tax] &&
							res.data.unavailable_terms[tax].includes(val)
						) {
							$cb
								.prop('disabled', true)
								.closest('label')
								.addClass('unavailable');
						} else {
							$cb
								.prop('disabled', false)
								.closest('label')
								.removeClass('unavailable');
						}
					});
				}
			},
			error: function () {
				$('.archive-grid').removeClass('loading');
			},
		});
	});

	// Load More with filters
	$loadMoreBtn.on('click', function () {
		if ($loadMoreBtn.hasClass('loading')) return;
		var currentPage = parseInt($loadMoreBtn.data('current-page'));
		var maxPages = parseInt($loadMoreBtn.data('max-pages'));
		var filterData = Object.assign({}, currentFilters);
		filterData['action'] = 'healthcare_filter_recipes';
		filterData['query_vars'] = healthcare_ajax_loadmore.query_vars;
		filterData['paged'] = currentPage + 1;
		$loadMoreBtn.addClass('loading').text('Loading...');
		$.ajax({
			url: healthcare_ajax_loadmore.ajax_url,
			type: 'POST',
			data: filterData,
			success: function (res) {
				if (res.success && res.data.html) {
					$('.archive-grid').append(res.data.html);
					$loadMoreBtn.data('current-page', currentPage + 1);
					if (res.data.has_more) {
						$loadMoreBtn.text(initialBtnText);
					} else {
						$loadMoreBtn.hide();
					}
				} else {
					$loadMoreBtn.hide();
				}
				$loadMoreBtn.removeClass('loading');
			},
			error: function () {
				$loadMoreBtn.text(initialBtnText).removeClass('loading');
			},
		});
	});

	// Remove the generic accordion toggle handler to avoid double toggling
	$('.recipe-accordion-filter').off('click', '.accordion-toggle');

	// Accordion toggle for parent and children (fix double toggle)
	$('.recipe-accordion-filter')
		.off('click.accordion')
		.on(
			'click.accordion',
			'.parent-accordion > .accordion-toggle',
			function (e) {
				e.preventDefault();
				e.stopImmediatePropagation();
				var $panel = $(this).next('.accordion-panel');
				var expanded = $(this).attr('aria-expanded') === 'true';
				$(this).attr('aria-expanded', !expanded);
				$panel.stop(true, true).slideToggle(200);
			}
		);
	$('.recipe-accordion-filter')
		.off('click.childaccordion')
		.on(
			'click.childaccordion',
			'.accordion-item:not(.parent-accordion) > .accordion-toggle',
			function (e) {
				e.preventDefault();
				e.stopImmediatePropagation();
				var $panel = $(this).next('.accordion-panel');
				var expanded = $(this).attr('aria-expanded') === 'true';
				$(this).attr('aria-expanded', !expanded);
				$panel.stop(true, true).slideToggle(200);
			}
		);
});
