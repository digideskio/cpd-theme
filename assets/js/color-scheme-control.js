/* global colorScheme, Color */
/**
 * Add a listener to the Color Scheme control to update other color controls to new values/defaults.
 * Also trigger an update of the Color Scheme CSS when a color is changed.
 */

// Add new options here along with the corresponding colour scheme number.
// This makes this file much DRY-er!
var colorOptions = {
	'cpd_widget_link_bg_color'		: 6,
	'cpd_widget_link_bg_color_alt'	: 7,
	'cpd_widget_link_color'			: 8,
	'cpd_widget_link_color_alt'		: 9,
	'cpd_widget_heading_bg_color'	: 10,
	'cpd_widget_heading_color'		: 11,
	'cpd_main_bg_color'				: 12,
	'cpd_article_bg_color'			: 13,
	'cpd_article_color'				: 14,
	'cpd_article_foot_bg_color'		: 15,
	'cpd_article_foot_color'		: 16,
	'cpd_sidebar_bg_color'			: 17,
	'cpd_intro_color'				: 18,
	'cpd_advisory_bg_color'			: 19,
	'cpd_advisory_color'			: 20,
	'cpd_footer_bg_color'			: 21,
	'cpd_footer_bottom_bg_color'	: 22,
	'cpd_footer_color'				: 23,
	'cpd_table_head_bg_color'		: 24,
	'cpd_table_head_color'			: 25,
	'cpd_table_row_bg_color'		: 26,
	'cpd_table_row_color'			: 27,
	'cpd_table_row_link_color'		: 28
};

// NOTHING SOUTH OF HERE NEEDS TOUCHING
(function(api) {
	// I've set colorSettings to be the same as colorSchemeKeys as there was no need for
	// two completely identical arrays as per the Twenty Fifteen code!
	var cssTemplate = wp.template('cpd-color-scheme'),
		colorSchemeKeys = _.keys(colorOptions),
		colorSettings   = _.keys(colorOptions);

	api.controlConstructor.select = api.Control.extend( {
		ready: function() {
			if ('color_scheme' === this.id) {
				this.setting.bind( 'change', function(value) {
					// Loop through our colorOptions array to save on repetition
					_.each(colorOptions, function(number, key) {
						// Update the DB with the new colour value
						api(key).set(colorSchemeCPD[value].colors[number]);
						// If the control is present, update it live in the customizer.
						// Needs this conditional otherwise customizer breaks for supervisors and participants!
						if (api.control(key)) {
							api.control(key).container.find('.color-picker-hex')
							  .data( 'data-default-color', colorSchemeCPD[value].colors[number])
							  .wpColorPicker( 'defaultColor', colorSchemeCPD[value].colors[number]);
						}
					});
				});
			}
		}
	});

	// Generate the CSS for the current Color Scheme.
	function updateCSS() {
		var scheme = api('color_scheme')(), css,
			colors = _.object(colorSchemeKeys, colorSchemeCPD[ scheme ].colors);

		// Merge in color scheme overrides.
		_.each(colorSettings, function(setting) {
			colors[ setting ] = api(setting)();
		});

		css = cssTemplate(colors);

		api.previewer.send('update-color-scheme-css', css);
	}

	// Update the CSS whenever a color setting is changed.
	_.each(colorSettings, function(setting) {
		api(setting, function( setting) {
			setting.bind(updateCSS);
		});
	});
})(wp.customize);
