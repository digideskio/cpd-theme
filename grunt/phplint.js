module.exports =  {

    plugins: ['<%= wpPlugins %>'],
    theme: ['<%= wpInfo.wp_content %>/themes/<%= wpInfo.wp_theme_name %>/**/*.php']
};