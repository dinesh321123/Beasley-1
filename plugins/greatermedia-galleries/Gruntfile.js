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
					'assets/js/vendor/cycle2/jquery.cycle2.min.js',
					'assets/js/vendor/cycle2/jquery.cycle2.center.min.js',
					'assets/js/vendor/cycle2/jquery.cycle2.swipe.min.js',
					'assets/js/vendor/cycle2/jquery.cycle2.carousel.min.js',
					'assets/js/src/gmr_gallery.js'
				],
				dest: 'assets/js/gmr_gallery.js'
			}
		},
		jshint: {
			all: [
				'Gruntfile.js',
				'assets/js/src/**/*.js',
				'assets/js/test/**/*.js'
			],
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
					window: false,
					document: false,
					console: false
				}
			}
		},
		uglify: {
			all: {
				files: {
					'assets/js/gmr_gallery.min.js': ['assets/js/gmr_gallery.js']
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
					'assets/css/gmr_gallery.css': 'assets/css/sass/gmr_gallery.scss',
					'assets/css/gmr_gallery_admin.css': 'assets/css/sass/gmr_gallery_admin.scss'
				}
			}
		},
		cssmin: {
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