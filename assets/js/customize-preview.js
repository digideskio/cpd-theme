/**
 * Live-update changed settings in real time in the Customizer preview.
 */

(function($) {
	var $style = $('#cpd-color-scheme-css'),
		api = wp.customize;

	if (! $style.length) {
		$style = $('head').append('<style type="text/css" id="cpd-color-scheme-css" />').find('#cpd-color-scheme-css');
	}

	// Blog Title
	api('blogname', function(value) {
		value.bind(function(to) {
			$('.site-title').text(to);
		});
	});

	// Blog Description
	api('blogdescription', function(value) {
		value.bind(function(to) {
			$('.site-description').text(to);
		});
	});

	// Advisory Notice
	api('cpd_advisory_notice', function(value) {
		value.bind(function(to) {
			$('.advisory-notice p').text(to);
		});
	});

	// Color Scheme CSS
	api.bind('preview-ready', function() {
		api.preview.bind('update-color-scheme-css', function(css) {
			$style.html(css);
		});
	});
})(jQuery);
