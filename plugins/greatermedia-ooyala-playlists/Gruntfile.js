module.exports = function( grunt ) {

	// Project configuration
	grunt.initConfig( {
		pkg:    grunt.file.readJSON( 'package.json' ),
		concat: {
			options: {
				stripBanners: true,
				banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
					' * <%= pkg.homepage %>\n' +
					' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
					' * Licensed GPLv2+' +
					' */\n'
			},
			main: {
				src: [
					'assets/js/src/greater-media-ooyala-playlists.js'
				],
				dest: 'assets/js/greater-media-ooyala-playlists.js'
			}
		},
		jshint: {
			all: [
				'Gruntfile.js',
				'assets/js/src/**/*.js',
				'assets/js/test/**/*.js'
			]
		},
		uglify: {
			all: {
				files: {
					'assets/js/greater-media-ooyala-playlists.min.js': ['assets/js/greater-media-ooyala-playlists.js']
				},
				options: {
					banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
						' * <%= pkg.homepage %>\n' +
						' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
						' * Licensed GPLv2+' +
						' */\n',
					mangle: {
						except: ['jQuery']
					}
				}
			}
		},


		cssmin: {
			options: {
				banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
					' * <%=pkg.homepage %>\n' +
					' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
					' * Licensed GPLv2+' +
					' */\n',
				processImport: false
			},
			minify: {
				expand: true,

				cwd: 'assets/css/',
				src: ['greater-media-ooyala-playlists.css'],

				dest: 'assets/css/',
				ext: '.min.css'
			}
		},
		watch:  {
			livereload: {
				files: ['assets/css/*.css'],
				options: {
					livereload: true
				}
			},
			styles: {
				files: ['assets/css/*.css', '!assets/css/*.min.css'],
				tasks: ['cssmin'],
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
		},
		clean: {
			main: ['release/<%= pkg.version %>']
		},
		copy: {
			// Copy the plugin to a versioned release directory
			main: {
				src:  [
					'**',
					'!**/.*',
					'!**/readme.md',
					'!node_modules/**',
					'!vendor/**',
					'!tests/**',
					'!release/**',
					'!assets/css/sass/**',
					'!assets/css/src/**',
					'!assets/js/src/**',
					'!images/src/**',
					'!bootstrap.php',
					'!bower.json',
					'!composer.json',
					'!composer.lock',
					'!Gruntfile.js',
					'!package.json',
					'!phpunit.xml',
					'!phpunit.xml.dist'
				],
				dest: 'release/<%= pkg.version %>/'
			}
		},
		compress: {
			main: {
				options: {
					mode: 'zip',
					archive: './release/gmr_ooyala_playlists.<%= pkg.version %>.zip'
				},
				expand: true,
				cwd: 'release/<%= pkg.version %>/',
				src: ['**/*'],
				dest: 'gmr_ooyala_playlists/'
			}
		},
		wp_readme_to_markdown: {
			readme: {
				files: {
					'readme.md': 'readme.txt'
				}
			}
		}
	} );

	// Load tasks
	require('load-grunt-tasks')(grunt);

	// Register tasks

	grunt.registerTask( 'css', ['cssmin'] );


	grunt.registerTask( 'js', ['jshint', 'concat', 'uglify'] );

	grunt.registerTask( 'default', ['css', 'js', 'wp_readme_to_markdown' ] );

	grunt.registerTask( 'build', ['default', 'clean', 'copy', 'compress'] );

	grunt.util.linefeed = '\n';
};
