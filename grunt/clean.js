module.exports = {
    scripts: [
        '<%= wpInfo.wp_content %>/themes/<%= wpInfo.wp_theme_name %>/js/*.js',
        '!<%= wpInfo.wp_content %>/themes/<%= wpInfo.wp_theme_name %>/js/*.min.js'
    ]
};