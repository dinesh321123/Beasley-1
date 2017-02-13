module.exports = function (grunt) {
	// Project configuration
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		concat: {
			options: {
				stripBanners: true
			},
			gmr_gallery: {
				src: [
					'assets/js/vendor/fotorama.js',
					'assets/js/vendor/fotorama-wp.js'
				],
				dest: 'assets/js/gmr_gallery.js'
			},
			gmr_admin: {
				src: ['assets/js/src/gmr_admin.js'],
				dest: 'assets/js/gmr_admin.js'
			}
		},
		jshint: {
			all: [
				'Gruntfile.js',
				'assets/js/src/**/*.js',
				'assets/js/test/**/*.js'
			],
			options: {
				browser: true,
				curly:   true,
				eqeqeq:  true,
				immed:   true,
				latedef: true,
				newcap:  true,
				noarg:   true,
				sub:     true,
				undef:   true,
				boss:    true,
				eqnull:  true,
				jquery:  true,
				globals: {
					exports: true,
					module: false,
					jQuery: false,
					window: false,
					document: false,
					console: false,
					wp: false,
					ga: false,
					_gaq: false,
					tinymce: false
				}
			}
		},
		uglify: {
			all: {
				files: {
					'assets/js/gmr_gallery.min.js': ['assets/js/gmr_gallery.js'],
					'assets/js/gmr_admin.min.js': ['assets/js/gmr_admin.js']
				},
				options: {
					mangle: {
						except: ['jQuery']
					}
				}
			}
		},
		sass: {
			all: {
				files: {
					'assets/css/gmr_gallery_admin.css': 'assets/css/sass/gmr_gallery_admin.scss'
				}
			}
		},
		cssmin: {
			combine: {
				files: {
					'assets/css/gmr_gallery.css': ['assets/css/vendor/fotorama.css', 'assets/css/vendor/fotorama-wp.css']
				}
			},
			minify: {
				expand: true,
				cwd: 'assets/css/',
				src: ['gmr_gallery.css', 'gmr_gallery_admin.css'],
				dest: 'assets/css/',
				ext: '.min.css'
			}
		},
		watch: {
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
		}
	});

	// Load other tasks
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Default tasks
	grunt.registerTask('js', ['jshint', 'concat', 'uglify']);
	grunt.registerTask('css', ['sass', 'cssmin']);
	grunt.registerTask('default', ['js', 'css']);

	grunt.util.linefeed = '\n';
};
