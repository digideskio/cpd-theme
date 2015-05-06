module.exports = {

    custom: {
        // [REQUIRED] Path to the build you're using for development.
        "devFile" : "bower_components/modernizr/modernizr.js",

        // [REQUIRED] Path to save out the built file.
        "outputFile" : "assets/js/lib/modernizr-custom.js",

        "extra" : {
            "shiv" : true,
            "printshiv" : true,
            "load" : true,
            "mq" : true,
            "cssclasses" : true
        },

        // WE'RE UGLIFYING IN THE GRUNT STACK, SO AVOID THE OVERHEAD HERE
        "uglify" : false,

        "files" : {
            "src": ['assets/**/*.scss','assets/**/*.js','!assets/js/modernizr-custom.js']
        }
    }
};