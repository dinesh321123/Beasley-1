module.exports = function (grunt) {
	var config = {
		pkg: grunt.file.readJSON('package.json'),
		concat: {
			options: {
				stripBanners: true
			}
		},
		uglify: {
			options: {
				mangle: {
					reserved: ['jQuery']
				}
			}
		},
		sass: {
			options: {
				require: 'sass-globbing',
				sourceMap: true,
				precision: 5
			}
		},
		cssmin: {
		}
	};

	var fs = require('fs');
	var path = require('path');
	var dirs = function(dir) {
		var theme = grunt.option('theme');

		return fs
			.readdirSync(dir)
			.filter(function(name) {
				if (theme && name !== theme) {
					return false;
				}

				return name !== 'node_modules' && fs.statSync(path.join(dir, name)).isDirectory();
			});
	};

	var tasks = [];
	dirs(__dirname).forEach(function(dir) {
		var json = path.join(__dirname, dir, 'Gruntfile.json');

		if (fs.existsSync(json)) {
			var theme = grunt.file.readJSON(json);
			['concat', 'uglify', 'sass', 'cssmin'].forEach(function(key) {
				if (theme[key]) {
					config[key][dir] = theme[key];
					if (tasks.indexOf(key) === -1) {
						tasks.push(key);
					}
				}
			});
		}
	});

	grunt.initConfig(config);
	grunt.util.linefeed = '\n';

	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-sass');

	grunt.registerTask('default', tasks);
};
