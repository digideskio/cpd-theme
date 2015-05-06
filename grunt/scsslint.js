module.exports = {

    options: {
      bundleExec: false, // should be true once bundler for is being used for installing gems as part the workflow
      compact: true,
      force: true,
      reporterOutput: 'reports/scsslint-report.xml',
      colorizeOutput: true,
      config: null
    },
    src: [
      'assets/scss/**/*.scss',
    ]
};