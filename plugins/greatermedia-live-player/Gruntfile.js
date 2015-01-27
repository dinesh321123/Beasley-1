module.exports = function( grunt ) {

	// Project configuration
	grunt.initConfig( {
		pkg:    grunt.file.readJSON( 'package.json' ),
		concat: {
			options: {
				stripBanners: true
			},
			greater_media_live_player: {
				src: [
					'assets/js/src/greater_media_live_player.js'
				],
				dest: 'assets/js/greater_media_live_player.js'
			},
			tdplayer: {
				src: [
					'assets/js/src/tdplayer.js'
				],
				dest: 'assets/js/tdplayer.js'
			}
		},
		jshint: {
			all: [
				'Gruntfile.js',
				'assets/js/src/**/*.js',
				'assets/js/test/**/*.js',
			],
			options: {
				curly:   true,
				eqeqeq:  false,
				immed:   true,
				latedef: false,
				newcap:  true,
				noarg:   true,
				sub:     true,
				undef:   true,
				boss:    false,
				eqnull:  true,
				devel:   true,
				browser: true,
				globals: {
					exports: true,
					module:  false,
					'is_gigya_user_logged_in': true,
					'gmr': true,
					'Cookies': true,
					'gmlp': true,
					'jQuery': true,
					'$': true,
					'window': true,
					'bowser': true,
					'require': true,
					'TdPlayerApi': true,
					'_': false,
					'Modernizr': true
				}
			}		
		},
		uglify: {
			all: {
				files: {
					'assets/js/greater_media_live_player.min.js': ['assets/js/greater_media_live_player.js'],
					'assets/js/tdplayer.min.js': ['assets/js/tdplayer.js']
				},
				options: {
					mangle: {
						except: ['jQuery']
					}
				}
			}
		},
		test:   {
			files: ['assets/js/test/**/*.js']
		},

		watch:  {
			
			scripts: {
				files: ['assets/js/admin/**/*.js', 'assets/js/src/**/*.js', 'assets/js/vendor/**/*.js'],
				tasks: ['jshint', 'concat', 'uglify'],
				options: {
					debounceDelay: 500
				}
			}
		},
		clean: {
			main: ['release/<%= pkg.version %>']
		},
		copy: {
			// Copy the plugin to a versioned release directory
			main: {
				src:  [
					'**',
					'!node_modules/**',
					'!release/**',
					'!.git/**',
					'!.sass-cache/**',
					'!css/src/**',
					'!js/src/**',
					'!img/src/**',
					'!Gruntfile.js',
					'!package.json',
					'!.gitignore',
					'!.gitmodules'
				],
				dest: 'release/<%= pkg.version %>/'
			}		
		},
		compress: {
			main: {
				options: {
					mode: 'zip',
					archive: './release/greater_media_live_player.<%= pkg.version %>.zip'
				},
				expand: true,
				cwd: 'release/<%= pkg.version %>/',
				src: ['**/*'],
				dest: 'greater_media_live_player/'
			}		
		}
	} );
	
	// Load other tasks
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	
	grunt.loadNpmTasks('grunt-contrib-sass');
	
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks( 'grunt-contrib-clean' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-contrib-compress' );
	
	// Default task.
	
	grunt.registerTask( 'default', ['jshint', 'concat', 'uglify'] );
	
	
	grunt.registerTask( 'build', ['default', 'clean', 'copy', 'compress'] );

	grunt.util.linefeed = '\n';
};