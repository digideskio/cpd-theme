module.exports = {

    options: {
        separator: '\r\n\r\n',
    },
    header: {
        src: ['<%= concatHead %>'],
        dest: '<%= wpInfo.wp_content %>/themes/<%= wpInfo.wp_theme_name %>/js/header.js',
        nonull: true,
    },
    footer: {
        src: ['<%= concatFoot %>'],
        dest: '<%= wpInfo.wp_content %>/themes/<%= wpInfo.wp_theme_name %>/js/footer.js',
        nonull: true,
    }
};