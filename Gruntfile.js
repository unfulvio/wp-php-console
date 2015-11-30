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

		checktextdomain: {
			options: {
				text_domain   : 'wp-php-console',
				correct_domain: false,
				keywords      : [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d',
					' __ngettext:1,2,3d',
					'__ngettext_noop:1,2,3d',
					'_c:1,2d',
					'_nc:1,2,4c,5d'
				]
			},
			files  : {
				src   : [
					'includes/**/*.php',
					'wp-php-console.php',
					'uninstall.php'
				],
				expand: true
			}
		},

		makepot: {
			target: {
				options: {
					cwd            : '',
					domainPath     : '/languages',
					potFilename    : 'wp-php-console.pot',
					mainFile       : 'wp-php-console.php',
					include        : [],
					exclude        : [
						'assets/',
						'build/',
						'languages/',
						'node_modules',
						'release/',
						'svn/',
						'tests',
						'tmp',
						'vendor'
					],
					potComments    : '',
					potHeaders     : {
						poedit                 : true,
						'x-poedit-keywordslist': true,
						'language'             : 'en_US',
						'report-msgid-bugs-to' : 'https://github.com/unfulvio/wp-php-console',
						'last-translator'      : 'Fulvio Notarstefano <fulvio.notarstefano@gmail.com>',
						'language-Team'        : 'Fulvio Notarstefano <fulvio.notarstefano@gmail.com>'
					},
					type           : 'wp-plugin',
					updateTimestamp: true,
					updatePoFiles  : true,
					processPot     : null
				}
			}
		},

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
	grunt.registerTask( 'pot',    ['checktextdomain', 'makepot'] );
	grunt.registerTask( 'build',  ['composer:install:no-dev', 'composer:dump-autoload:optimize:no-dev', 'clean', 'copy', 'compress', 'composer:update', 'composer:dump-autoload:optimize'] );
	grunt.registerTask( 'deploy', ['pot', 'build', 'wp_deploy'] );

	grunt.util.linefeed = '\n';
};
