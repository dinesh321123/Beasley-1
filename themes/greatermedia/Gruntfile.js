module.exports = function (grunt) {
	'use strict';

	// Load all grunt tasks
	require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

	// Project configuration
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		concat: {
			options: {
				stripBanners: true
			},
			greater_media: {
				src: [
					'assets/js/src/greater_media.js'
				],
				dest: 'assets/js/greater_media.js'
			},
			greater_media_admin: {
				src: [
					'assets/js/src/greater_media_admin.js'
				],
				dest: 'assets/js/greater_media_admin.js'
			}
		},
		jshint: {
			browser: {
				all: [
					'assets/js/src/**/*.js',
					'assets/js/test/**/*.js'
				],
				options: {
					jshintrc: '.jshintrc'
				}
			},
			grunt: {
				all: [
					'Gruntfile.js'
				],
				options: {
					jshintrc: '.gruntjshintrc'
				}
			}
		},
		uglify: {
			all: {
				files: {
					'assets/js/greater_media.min.js': ['assets/js/greater_media.js'],
					'assets/js/greater_media_admin.min.js': ['assets/js/greater_media_admin.js']
				},
				options: {
					mangle: {
						except: ['jQuery']
					}
				}
			}
		},
		sass: {
			options: {
				require: 'sass-globbing'
			},
			all: {
				files: {
					'assets/css/greater_media.css': 'assets/css/sass/greater_media.scss',
					'assets/css/greater_media_admin.css': 'assets/css/sass/greater_media_admin.scss',
					'assets/css/gm_admin.css': 'assets/css/sass/gm_admin.scss',
					'assets/css/gm_tinymce.css': 'assets/css/sass/gm_tinymce.scss'
				}
			}
		},
		cssmin: {
			minify: {
				expand: true,
				cwd: 'assets/css/',
				src: ['greater_media.css', 'greater_media_admin.css', 'gm_admin.css', 'gm_tinymce.css'],
				dest: 'assets/css/',
				ext: '.min.css'
			}
		},
		watch: {
			sass: {
				files: ['assets/css/sass/**/*.scss'],
				tasks: ['css'],
				options: {
					debounceDelay: 500
				}
			},
			scripts: {
				files: ['assets/js/src/**/*.js', 'assets/js/vendor/**/*.js'],
				tasks: ['js'],
				options: {
					debounceDelay: 500
				}
			}
		}
	});

	// Default tasks
	grunt.registerTask('css', ['sass', 'cssmin']);
	grunt.registerTask('js', ['jshint', 'concat', 'uglify']);
	grunt.registerTask('default', ['js', 'css']);

	grunt.util.linefeed = '\n';
};