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
			frontend_contest: {
				src: [
					'js/vendor/waypoint.js',
					'js/vendor/modernizr.js',
					'js/vendor/debouncedresize.js',
					'js/vendor/imagesloaded.js',
					'js/src/grid.js',
					'js/src/contests.js',
					'js/src/datepicker.js'
				],
				dest: 'js/contests.js'
			},
			backend_contest: {
				src: ['js/src/contests-admin.js'],
				dest: 'js/contests-admin.js'
			},
			backend_moderation: {
				src: ['js/src/ugc-moderation.js'],
				dest: 'js/ugc-moderation.js'
			},
			frontend_survey: {
				src: [
					'js/src/surveys.js',
					'js/src/datepicker.js'
				],
				dest: 'js/surveys.js'
			},
			backend_survey: {
				src: ['js/src/surveys_admin.js'],
				dest: 'js/surveys_admin.js'
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
					gridModernizr: false,
					Grid: true,
					document: false,
					window: false,
					console: false,
					setTimeout: false,
					confirm: false,
					GreaterMediaContests: false,
					GreaterMediaContestsForm: false,
					GreaterMediaAdminNotifier: false,
					GreaterMediaSurveys: false,
					GreaterMediaUGC: false,
					Formbuilder: false,
					FormData: false
				}
			}
		},
		uglify: {
			all: {
				files: {
					'js/surveys.min.js': ['js/surveys.js'],
					'js/surveys_admin.min.js': ['js/surveys_admin.js'],
					'js/ugc-moderation.min.js': ['js/ugc-moderation.js'],
					'js/contests-admin.min.js': ['js/contests-admin.js'],
					'js/contests.min.js': ['js/contests.js']
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
					'css/greatermedia-ugc-moderation.css': 'css/src/greatermedia-ugc-moderation.scss',
					'css/greatermedia-contests.css': 'css/src/greatermedia-contests.scss',
					'css/greatermedia-contests-admin.css': 'css/src/greatermedia-contests-admin.scss'
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
