module.exports = function (grunt) {

	// Project configuration
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		concat: {
			options: {
				stripBanners: true,
				banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
						' * <%= pkg.homepage %>\n' +
						' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
						' * Licensed GPLv2+' +
						' */\n'
			},
			greatermedia_shows: {
				src: [
					'assets/js/src/shows.js',
					'assets/js/src/schedule.js',
					'assets/js/src/metabox.js',
					'assets/js/src/hide-metabox-if-disabled.js'
				],
				dest: 'assets/js/greatermedia_shows.js'
			},
			admin_show_selector: {
				src: ['assets/js/src/admin_show_selector.js'],
				dest: 'assets/js/admin_show_selector.js'
			}
		},
		jshint: {
			all: ['assets/js/src/**/*.js'],
			options: {
				curly: true,
				eqeqeq: true,
				immed: true,
				latedef: true,
				newcap: true,
				noarg: true,
				sub: true,
				undef: true,
				boss: true,
				eqnull: true,
				globals: {
					exports: true,
					module: false,
					jQuery: false,
					document: false,
					window: false,
					console: false
				}
			}
		},
		uglify: {
			all: {
				files: {
					'assets/js/greatermedia_shows.min.js': ['assets/js/greatermedia_shows.js'],
					'assets/js/admin_show_selector.min.js': ['assets/js/admin_show_selector.js']
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
		sass: {
			all: {
				options: {
					trace: true,
					sourcemap: 'auto'
				},
				files: {
					'assets/css/greatermedia_shows.css': 'assets/css/sass/shows.scss'
				}
			}
		},
		cssmin: {
			options: {
				banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
						' * <%= pkg.homepage %>\n' +
						' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
						' * Licensed GPLv2+' +
						' */\n'
			},
			minify: {
				expand: true,
				cwd: 'assets/css/',
				src: ['greatermedia_shows.css'],
				dest: 'assets/css/',
				ext: '.min.css'
			}
		},
		watch: {
			sass: {
				files: ['assets/css/sass/*.scss'],
				tasks: ['css'],
				options: {
					debounceDelay: 500
				}
			},
			scripts: {
				files: ['assets/js/src/**/*.js'],
				tasks: ['js'],
				options: {
					debounceDelay: 500
				}
			}
		}
	});

	// Load other tasks
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Custom tasks
	grunt.registerTask('js', ['jshint', 'concat', 'uglify']);
	grunt.registerTask('css', ['sass', 'cssmin']);

	// Default task
	grunt.registerTask('default', ['js', 'css']);

	grunt.util.linefeed = '\n';
};
