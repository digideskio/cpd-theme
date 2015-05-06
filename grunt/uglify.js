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
    }
};