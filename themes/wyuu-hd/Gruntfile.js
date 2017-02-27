module.exports = function( grunt ) {
	'use strict';

	// Load all grunt tasks
	require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

	// Project configuration
	grunt.initConfig( {
		pkg:    grunt.file.readJSON( 'package.json' ),
		
		sass:   {
			all: {
				files: {
					'assets/css/wyuu_hd.css': 'assets/css/sass/wyuu_hd_light.scss'
				}
			}
		},
		
		cssmin: {
			minify: {
				expand: true,
				cwd: 'assets/css/',
				src: ['wyuu_hd.css'],
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
		}
	} );

	// Default task.
	grunt.registerTask( 'default', ['sass', 'cssmin'] );

	grunt.util.linefeed = '\n';
};