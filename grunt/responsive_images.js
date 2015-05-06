// Use 'grunt responsive' for this
module.exports = {

  options: {
      engine: 'gm',
      sizes: '<%= imgSizes %>',
    },
  all: {
    files: [{
      expand: true,
      src: ['**/*.{jpg,png,gif}'],
      cwd: 'assets/img/',
      custom_dest: '<%= wpInfo.wp_content %>/themes/<%= wpInfo.wp_theme_name %>/img/{%= name %}/'
    }]
  },
  new: {
    options: {
      newFilesOnly: true
    },
    files: [{
      expand: true,
      src: ['**/*.{jpg,png,gif}'],
      cwd: 'assets/img/',
      custom_dest: '<%= wpInfo.wp_content %>/themes/<%= wpInfo.wp_theme_name %>/img/{%= name %}/'
    }]
  }
};