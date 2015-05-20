module.exports = function(grunt) {

    // Without grunt-concurrent
    grunt.registerTask('default', [
        "scsslint",
        "sass",
        "autoprefixer",
        "cssmin",
        "jshint:before",
        // "modernizr",
        "concat",
        "uglify",
        "jshint:after",
        "imagemin",
        "phplint",
        "clean",
        "notify:build"
    ]);
};
