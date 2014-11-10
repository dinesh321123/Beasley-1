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
					'assets/js/src/metabox.js'
				],
				dest: 'assets/js/greatermedia_shows.js'
			},
			shows_widget: {
				src: [
					'assets/js/src/widget.js'
				],
				dest: 'assets/js/shows_widget.js'
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
					module: false
				}
			}
		},
		uglify: {
			all: {
				files: {
					'assets/js/greatermedia_shows.min.js': ['assets/js/greatermedia_shows.js'],
					'assets/js/shows_widget.min.js': ['assets/js/shows_widget.js']
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
				files: ['assets/js/src/**/*.js', 'assets/js/vendor/**/*.js'],
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
