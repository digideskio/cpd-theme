module.exports = {

    sass: {
        options: {
            sourcemap: true,
            style: 'compressed'
        },
        files: [{
            expand: true,
            cwd: 'assets/scss',
            src: ['*.scss'],
            dest: 'assets/css',
            ext: '.css'
        }]
    }
};