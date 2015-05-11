/* global colorScheme, Color */
/**
 * Add a listener to the Color Scheme control to update other color controls to new values/defaults.
 * Also trigger an update of the Color Scheme CSS when a color is changed.
 */

( function( api ) {
	// JUST ADD THE CONTROL NAME TO THE KEYS AND SETTINGS ARRAYS HERE
	var cssTemplate = wp.template( 'cpd-color-scheme' ),
		colorSchemeKeys = [
			'cpd_widget_bg_color'
		],
		colorSettings = [
			'cpd_widget_bg_color'
		];

	api.controlConstructor.select = api.Control.extend( {
		ready: function() {
			if ( 'color_scheme' === this.id ) {
				this.setting.bind( 'change', function( value ) {
					// THEN ADD A BLOCK FOR EVERY ADDITIONAL COLOR OPTION HERE
					// Update Background Color.
					api( 'cpd_widget_bg_color' ).set( colorSchemeCPD[value].colors[6] );
					api.control( 'cpd_widget_bg_color' ).container.find( '.color-picker-hex' )
						.data( 'data-default-color', colorSchemeCPD[value].colors[6] )
						.wpColorPicker( 'defaultColor', colorSchemeCPD[value].colors[6] );
				} );
			}
		}
	} );

	// NOTHING SOUTH OF HERE NEEDS TOUCHING
	// Generate the CSS for the current Color Scheme.
	function updateCSS() {
		var scheme = api( 'color_scheme' )(), css,
			colors = _.object( colorSchemeKeys, colorSchemeCPD[ scheme ].colors );

		// Merge in color scheme overrides.
		_.each( colorSettings, function( setting ) {
			colors[ setting ] = api( setting )();
		});

		css = cssTemplate( colors );

		api.previewer.send( 'update-color-scheme-css', css );
	}

	// Update the CSS whenever a color setting is changed.
	_.each( colorSettings, function( setting ) {
		api( setting, function( setting ) {
			setting.bind( updateCSS );
		} );
	} );
} )( wp.customize );
