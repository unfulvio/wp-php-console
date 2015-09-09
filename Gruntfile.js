module.exports = function( grunt ) {

	var pkg = grunt.file.readJSON( 'package.json' ),
		distFiles =  [
			'**',
			'!assets/**',
			'!node_modules/**',
			'!svn/**',
			'!tests/**',
			'!**/.*',
			'!bootstrap.php',
			'!ChangeLog.md',
			'!Gruntfile.js',
			'!package.json',
			'!phpunit.xml',
			'!phpunit.xml.dist',
			'!README.md'
		];

	// Project configuration
	grunt.initConfig( {

		pkg: pkg,

		clean: {
			main: [ 'build' ]
		},

		// Copy the plugin to build directory
		copy: {
			main: {
				expand: true,
				src: distFiles,
				dest: 'build/wp-php-console'
			}
		},

		compress: {
			main: {
				options: {
					mode: 'zip',
					archive: './build/wp-php-console.<%= pkg.version %>.zip'
				},
				expand: true,
				src: distFiles,
				dest: '/wp-php-console/'
			}
		},

		wp_deploy: {
			deploy: {
				options: {
					plugin_slug: 'wp-php-console',
					build_dir: 'build/wp-php-console',
					assets_dir: 'assets',
					svn_url: 'https://plugins.svn.wordpress.org/wp-php-console'
				}
			}
		}

	} );

	// Load tasks
	require('load-grunt-tasks')(grunt);

	// Register tasks

	grunt.registerTask( 'build',  ['clean', 'copy', 'compress'] );
	grunt.registerTask( 'deploy', ['build', 'wp_deploy'] );

	grunt.util.linefeed = '\n';
};
