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
			'!bootstrap.php.dist',
			'!ChangeLog.md',
			'!composer.json',
			'!composer.lock',
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
			main: [
				// Clean directories before new build
				'svn/trunk',
				'release'
			]
		},

		// Copy the plugin to a svn versioned build directory
		copy: {
			assets : {
				expand: true,
				src: 'assets/*.*',
				dest: 'svn/assets/'
			},
			tag: {
				expand: true,
				src: distFiles,
				dest: 'svn/tags/' + pkg.version + '/'
			},
			trunk: {
				expand: true,
				src: distFiles,
				dest: 'svn/trunk'
			}
		},

		compress: {
			main: {
				options: {
					mode: 'zip',
					archive: './release/wp-php-console.<%= pkg.version %>.zip'
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
					build_dir: 'svn/trunk',
					assets_dir: 'svn/assets'
				}
			}
		},

	} );

	// Load tasks
	require('load-grunt-tasks')(grunt);

	// Register tasks

	grunt.registerTask( 'release', ['clean', 'copy:assets', 'copy:trunk', 'copy:tag', 'compress'] );

	grunt.registerTask( 'deploy', ['release', 'wp_deploy'] );

	grunt.util.linefeed = '\n';
};
