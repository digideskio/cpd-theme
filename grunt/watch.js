module.exports = {
    options: {
      livereload: true,
    },
    code: {
      files: ['<%= wpInfo.wp_content %>/themes/<%= wpInfo.wp_theme_name %>/**/*.php'],
      tasks: ['newer:phplint', 'notify:code'],
      options: {
        spawn: false,
      }
    },
    scripts: {
      files: ['assets/js/**/*.js', '!js/modernizr-custom.js'],
      tasks: ['newer:jshint:before', 'modernizr', 'newer:concat', 'newer:uglify', 'newer:jshint:after','clean', 'notify:scripts'],
      options: {
        spawn: false,
      }
    },
    // We'll silently (no notification) run the scripts alias to ensure Modernizr build is aware of any new checks in the CSS
    styles: {
      files: ['assets/scss/**/*.scss'],
      tasks: ['scsslint', 'sass', 'autoprefixer', 'cssmin', 'spriteGenerator', 'notify:styles', 'scripts'],
      options: {
        spawn: false,
      }
    },
    images: {
      files: ['assets/img/**/*.{png,jpg,svg,gif}'],
      tasks: ['newer:imagemin', 'notify:images'],
      options: {
        spawn: false,
      }
    }
};