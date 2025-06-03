jQuery(document).ready(function ($) {
	var loading = false;
	$('#load-more-posts').on('click', function () {
		if (loading) return;
		var $btn = $(this);
		var currentPage = parseInt($btn.data('current-page'));
		var maxPages = parseInt($btn.data('max-pages'));
		var archiveUrl = $btn.data('archive-url');
		if (currentPage >= maxPages) return;
		loading = true;
		var initialBtnText = $btn.text();
		$btn.text('Loading...');
		$.ajax({
			url: healthcare_ajax_loadmore.ajax_url,
			type: 'POST',
			data: {
				action: 'healthcare_load_more',
				query_vars: healthcare_ajax_loadmore.query_vars,
				paged: currentPage + 1,
			},
			success: function (res) {
				if (res.success && res.data.html) {
					$('.archive-grid').append(res.data.html);
					$btn.data('current-page', currentPage + 1);
					if (currentPage + 1 >= maxPages) {
						$btn.hide();
					} else {
						$btn.text(initialBtnText);
					}
				} else {
					$btn.hide();
				}
				loading = false;
			},
			error: function () {
				$btn.text(initialBtnText);
				loading = false;
			},
		});
	});
});
