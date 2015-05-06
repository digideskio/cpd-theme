module.exports = function(grunt) {

  require('load-grunt-config')(grunt, {
      init: true,
      jitGrunt: {
          jitGrunt: true,
          staticMappings: {
            scsslint: 'grunt-scss-lint'
          }
      },
      data: {

        wpInfo: {
    		  wp_theme_name: 'cpd',
    		  wp_content: 'htdocs/wp-content'
        },

        wpPlugins: [
          '<%= wpInfo.wp_content %>/plugins/mkdo-admin/**/*.php'
        ],

        concatHead: [
            'assets/js/lib/modernizr-custom.js',
            'bower_components/respondJS/dest/respond.js',
            'assets/js/header.js'
        ],

        concatFoot: [
            'assets/js/footer.js'
        ]
    }
  });
  require('time-grunt')(grunt);
};