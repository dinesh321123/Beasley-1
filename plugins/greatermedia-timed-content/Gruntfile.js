module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		uglify: {
			options: {
			},
			build: {
				// Don't include 'js/gm-timed-content-tinymce.js' as WP's tinymce integration handles loading it for us
				src: ['js/greatermedia-timed-content-admin.js', 'bower_components/date.format/date.format.js', 'js/vendor/date.format/date-toisostring.js', 'bower_components/datetimepicker/jquery.datetimepicker.js'],
				dest: 'js/dist/<%= pkg.name %>.min.js'
			}
		}
	});

	// Load the plugin that provides the "uglify" task.
	grunt.loadNpmTasks('grunt-contrib-uglify');

	// Default task(s).
	grunt.registerTask('default', ['uglify']);

};