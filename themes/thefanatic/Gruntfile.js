module.exports = function( grunt ) {
	'use strict';

	// Load all grunt tasks
	require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

	// Project configuration
	grunt.initConfig( {
		pkg:    grunt.file.readJSON( 'package.json' ),
		concat: {
			options: {
				stripBanners: true
			},
			thefanatic: {
				src: [
					'assets/js/src/thefanatic.js'
				],
				dest: 'assets/js/thefanatic.js'
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
					'assets/js/thefanatic.min.js': ['assets/js/thefanatic.js']
				},
				options: {
					mangle: {
						except: ['jQuery']
					}
				}
			}
		},
		sass:   {
			options: {
				require: 'sass-globbing',
				sourceMap: true,
				precision: 5
			},
			all: {
				files: {
					'assets/css/thefanatic.css': 'assets/css/sass/thefanatic.scss'
				}
			}
		},

		cssmin: {
			minify: {
				expand: true,

				cwd: 'assets/css/',
				src: ['thefanatic.css'],

				dest: 'assets/css/',
				ext: '.min.css'
			}
		},
		watch:  {

			sass: {
				files: ['assets/css/sass/**/*.scss'],
				tasks: ['sass', 'cssmin'],
				options: {
					debounceDelay: 500
				}
			},

			scripts: {
				files: ['assets/js/src/**/*.js', 'assets/js/vendor/**/*.js'],
				tasks: ['jshint', 'concat', 'uglify'],
				options: {
					debounceDelay: 500
				}
			}
		}
	} );

	// Default task.

	grunt.registerTask( 'default', ['jshint', 'concat', 'uglify', 'sass', 'cssmin'] );


	grunt.util.linefeed = '\n';
};