module.exports = {
    phase1: [
        'scsslint',
        'jshint:before'
    ],
    phase2: [
        'modernizr',
        'svg2png'
    ],
    phase3: [
        'sass',
        'concat'
    ],
    phase4: [
        'prefix',
        'uglify'
    ],
    phase5: [
        'sprite',
        'jshint:after',
    ],
    phase6: [
        'cssmin',
        'imagemin',
    ],
    phase7: [
        'responsive:all',
        'phplint',
    ]
};