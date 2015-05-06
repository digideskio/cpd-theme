module.exports = {

    // CHECKING AFTER CONCAT/UGLIFY MAY LEAD TO GREY HAIR
    options: {
        globals: {
            jQuery: true,
            console: true,
            module: true
        },
        reporter: require('jshint-stylish'),
    },
    before: {
        options: {
            force: true,
            reporterOutput: 'reports/jshint-report-before.txt'
        },
        src: ['assets/js/**/*.js', '!js/modernizr-custom.js']
    },
    after: {
        options: {
            force: true,
            reporterOutput: 'reports/jshint-report-after.txt'
        },
        src: ['<%= wpInfo.wp_content %>/themes/<%= wpInfo.wp_theme_name %>/js/**/*.js']
    },
};