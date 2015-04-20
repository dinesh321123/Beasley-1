module.exports = function (grunt) {
	'use strict';

	// Load all grunt tasks
	require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

	// Project configuration
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		concat: {
			options: {
				stripBanners: true
			},
			greater_media_load_more: {
				src: [
					'assets/js/src/greater_media_load_more.js'
				],
				dest: 'assets/js/greater_media_load_more.js'
			},
			greater_media: {
				src: [
					'assets/js/vendor/jquery.fitvids.js',
					'assets/js/src/mobile-sub-menus.js',
					'assets/js/src/profile.js',
					'assets/js/src/show-schedule.js',
					'assets/js/src/social_share.js',
					'assets/js/src/greater_media_pjax.js',
					'assets/js/src/greater_media.js',
					'assets/js/src/live_player.js',
					'assets/js/src/menus.js',
					'assets/js/src/search.js'
				],
				dest: 'assets/js/greater_media.js'
			},
			greater_media_admin: {
				src: [
					'assets/js/src/greater_media_admin.js'
				],
				dest: 'assets/js/greater_media_admin.js'
			}
		},
		jshint: {
			browser: {
				all: [
					'assets/js/src/**/*.js',
					'assets/js/test/**/*.js'
				],
				options: {
					jshintrc: '.jshintrc',
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
					'assets/js/greater_media.min.js': ['assets/js/greater_media.js'],
					'assets/js/greater_media_load_more.min.js': ['assets/js/greater_media_load_more.js'],
					'assets/js/greater_media_admin.min.js': ['assets/js/greater_media_admin.js']
				},
				options: {
					mangle: {
						except: ['jQuery']
					}
				}
			}
		},
		sass: {
			options: {
				require: 'sass-globbing',
				sourceMap: true,
				precision: 5
			},
			all: {
				files: {
					'assets/css/greater_media.css': 'assets/css/sass/greater_media.scss',
					'assets/css/greater_media_admin.css': 'assets/css/sass/greater_media_admin.scss',
					'assets/css/gm_admin.css': 'assets/css/sass/gm_admin.scss',
					'assets/css/gm_tinymce.css': 'assets/css/sass/gm_tinymce.scss',
					'assets/css/ie9.css': 'assets/css/sass/ie9.scss'
				}
			}
		},
		cssmin: {
			minify: {
				expand: true,
				cwd: 'assets/css/',
				src: ['greater_media.css', 'greater_media_admin.css', 'gm_admin.css', 'gm_tinymce.css'],
				dest: 'assets/css/',
				ext: '.min.css'
			}
		},
		watch:  {

			livereload: {
				files  : ['assets/css/**/*.css'],
				options: {
					livereload: true
				}
			},

			sass: {
				files: ['assets/css/sass/**/*.scss'],
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
		},
		parker: {
			options: {
				metrics: ['TotalStylesheets', 'TotalStylesheetSize', 'TotalRules', 'TotalSelectors', 'TotalIdentifiers', 'TotalDeclarations', 'SelectorsPerRule', 'IdentifiersPerSelector', 'SpecificityPerSelector', 'TopSelectorSpecificity', 'TopSelectorSpecificitySelector', 'TotalIdSelectors', 'TotalUniqueColours', 'TotalImportantKeywords', 'TotalMediaQueries'],
				file: 'assets/css/style.css.md',
				usePackage: true
			},
			src: ['assets/css/*.css']
		}
	});

	// Default tasks
	grunt.registerTask('css', ['sass', 'cssmin', 'parker']);
	grunt.registerTask('js', ['jshint', 'concat', 'uglify']);
	grunt.registerTask('default', ['js', 'css']);

	grunt.util.linefeed = '\n';
};
