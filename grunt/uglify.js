module.exports = {

    options: {
        banner: '/*! <%= package.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n',
        mangle: false
    },
    header: {
        files: {
            '<%= wpInfo.wp_content %>/themes/<%= wpInfo.wp_theme_name %>/js/header.min.js': ['<%= concat.header.dest %>']
        }
    },
    footer: {
        files: {
            '<%= wpInfo.wp_content %>/themes/<%= wpInfo.wp_theme_name %>/js/footer.min.js': ['<%= concat.footer.dest %>']
        }
    },
    customise_control: {
        files: {
            '<%= wpInfo.wp_content %>/themes/<%= wpInfo.wp_theme_name %>/js/color-scheme-control.min.js': ['assets/js/color-scheme-control.js']
        }
    },
    customise_preview: {
        files: {
            '<%= wpInfo.wp_content %>/themes/<%= wpInfo.wp_theme_name %>/js/customize-preview.min.js': ['assets/js/customize-preview.js']
        }
    }
};