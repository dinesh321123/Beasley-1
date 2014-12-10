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
			shows_widget: {
				src: ['assets/js/src/blogroll-widget.js'],
				dest: 'assets/js/blogroll-widget.js'
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
					console: false,
					gmr_blogroll_widget: false
				}
			}
		},
		uglify: {
			all: {
				files: {
					'assets/js/blogroll-widget.min.js': ['assets/js/blogroll-widget.js']
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
		watch: {
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
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Custom tasks
	grunt.registerTask('js', ['jshint', 'concat', 'uglify']);
	// Default task
	grunt.registerTask('default', ['js']);

	grunt.util.linefeed = '\n';
};
