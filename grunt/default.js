module.exports = function(grunt) {

    // Without grunt-concurrent
    grunt.registerTask('default', [
        "scsslint",
        "sass",
        "autoprefixer",
        "cssmin",
        // "sprite",
        "jshint:before",
        "modernizr",
        "concat",
        "uglify",
        "jshint:after",
        "imagemin",
        "phplint",
        "clean",
        "notify:build"
    ]);

    // With grunt-concurrent
    // grunt.registerTask('default', [
    //     "concurrent:phase1",
    //     "concurrent:phase2",
    //     "concurrent:phase3",
    //     "concurrent:phase4",
    //     "concurrent:phase5",
    //     "concurrent:phase6",
    //     "concurrent:phase7",
    //     "clean",
    //     "notify:build"
    // ]);
};