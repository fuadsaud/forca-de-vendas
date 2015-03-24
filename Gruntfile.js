module.exports = function(grunt) {
  require('jit-grunt')(grunt);
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    bower: {
      install: {
        options: {
          verbose: true,
          cleanTargetDir: true,
          layout: 'byComponent',
          // copy: false,
          targetDir: 'public/vendor/',
        }
      }
    },
  });

  grunt.loadNpmTasks('grunt-bower-task');
  //grunt.loadNpmTasks('grunt-contrib-concat');
  // grunt.loadNpmTasks('grunt-preen');
  // grunt.registerTask('default', ['bower:install', 'preen', 'copy:bower']);
  grunt.registerTask('default', ['bower:install']);
};
