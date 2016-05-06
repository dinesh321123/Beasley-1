module.exports = function (grunt) {

	// Project configuration
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		concat: {
			options: {
				stripBanners: true,
				banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
						' * <%= pkg.homepage %>\n' +
						' * Copyright (c) <%= grunt.template.today("yyyy") %>;\n' +
						' * Licensed GPLv2+\n' +
						' */\n'
			},
			backend_countdown_clock: {
				src: ['js/src/countdown-clock-admin.js'],
				dest: 'js/countdown-clock-admin.js'
			},
			frontend_countdown_clock: {
				src: ['js/vendor/flipclock.min.js','js/src/countdown-clock.js'],
				dest: 'js/countdown-clock.js'
			}
		},
		jshint: {
			all: ['js/src/**/*.js'],
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
					twttr: false,
					jQuery: false,
					Modernizr: false,
					Waypoint: false,
					Grid: true,
					document: false,
					window: false,
					console: false,
					setTimeout: false,
					confirm: false,
					GreaterMediaContestsForm: false,
					GreaterMediaAdminNotifier: false,
					GreaterMediaUGC: false,
					Formbuilder: false,
					FormData: false,
					alert: false
				}
			}
		},
		uglify: {
			all: {
				files: {
					'js/countdown-clock-admin.min.js': ['js/countdown-clock-admin.js'],
					'js/countdown-clock.min.js': ['js/countdown-clock.js']
				},
				options: {
					banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
							' * <%= pkg.homepage %>\n' +
							' * Copyright (c) <%= grunt.template.today("yyyy") %>;\n' +
							' * Licensed GPLv2+\n' +
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
					sourcemap: 'auto',
					style: 'compressed'
				},
				files: {
					'css/greatermedia-countdown-clock-admin.css': 'css/src/greatermedia-countdown-clock-admin.scss',
					'css/greatermedia-countdown-clock.css': 'css/src/greatermedia-countdown-clock.scss'
				}
			}
		},
		watch: {
			sass: {
				files: ['css/src/*.scss'],
				tasks: ['css'],
				options: {
					debounceDelay: 500
				}
			},
			scripts: {
				files: ['js/src/**/*.js'],
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
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Custom tasks
	grunt.registerTask('js', ['jshint', 'concat', 'uglify']);
	grunt.registerTask('css', ['sass']);

	// Default task
	grunt.registerTask('default', ['js', 'css']);

	grunt.util.linefeed = '\n';
};
