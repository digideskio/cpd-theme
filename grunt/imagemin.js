module.exports = {
    images: {
      files: [{
        expand: true,
        cwd: 'assets/img/',
        src: ['**/*.{png,jpg,svg,gif}'],
        dest: '<%= wpInfo.wp_content %>/themes/<%= wpInfo.wp_theme_name %>/img/'
      }]
    }
};