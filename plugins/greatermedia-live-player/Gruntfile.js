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
					'assets/js/vendor/bowser.js',
					'assets/js/src/tdplayer.js'
				],
				dest: 'assets/js/live-player.js'
			}
		},
		jshint: {
			all: [
				'Gruntfile.js',
				'assets/js/src/**/*.js'
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
				expr:    true,
				browser: true,
				globals: {
					exports: true,
					module:  false,
					gmr: true,
					Cookies: true,
					gmlp: true,
					jQuery: true,
					'$': true,
					window: true,
					bowser: true,
					require: true,
					TDSdk: true,
					'_': false,
					Modernizr: true,
					TdPlayerApi: false
				}
			}
		},
		uglify: {
			all: {
				files: {
					'assets/js/live-player.min.js': ['assets/js/live-player.js'],
				},
				options: {
					mangle: {
						except: ['jQuery']
					}
				}
			}
		},
		watch:  {
			scripts: {
				files: ['assets/js/admin/**/*.js', 'assets/js/src/**/*.js', 'assets/js/vendor/**/*.js'],
				tasks: ['default'],
				options: {
					debounceDelay: 500
				}
			}
		}
	} );

	// Load other tasks
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Default task
	grunt.registerTask('default', ['jshint', 'concat', 'uglify']);

	grunt.util.linefeed = '\n';
};
