module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		watch: {
			scripts: {
				files: ['js/**/*.js'],
				tasks: ['build']
			}
		},

		uglify: {
			dist: {
				files: [{
					expand: true,
					src: ['js/**/*.js', '!js/**/*.min.js'],
					ext: '.min.js'
				}]
			}
		},

		cssmin: {
			dist: {
				files: [{
					expand: true,
					src: ['css/**/*.css', 'js/**/*.css', '!css/**/*.min.css', '!js/**/*.min.css'],
					ext: '.min.css'
				}]
			}
		},

	});

	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');

	grunt.registerTask('dist', ['uglify:dist', 'cssmin:dist']);
	grunt.registerTask('default', ['dist']);
};
