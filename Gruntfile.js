/* jshint node:true */
module.exports = function( grunt ) {
	'use strict';

	var options = {
		phpcs_bin: grunt.option( 'phpcs-bin' ) || 'phpcs'
	};

	grunt.initConfig({

		// Load package data.
		pkg: grunt.file.readJSON( 'package.json' ),

		// Set folder vars.
		dirs: {
			css: 'assets/css',
			js: 'assets/js',
			languages: 'languages',
			build: 'build'
		},

		// Javascript linting with jshint.
		jshint: {
			options: {
				jshintrc: '.jshintrc',
                reporterOutput: ''
			},
			all: [
				'Gruntfile.js',
				'<%= dirs.js %>/*.js',
				'!**/*.min.js'
			]
		},

		// Javascript linting with eslint.
		eslint: {
			options: {
				configFile: '.eslintrc.json'
			},
			target: [
				'Gruntfile.js',
				'<%= dirs.js %>/**/*.js',
				'!**/*.min.js'
			]
		},

		// Minify .js files.
		uglify: {
			options: {

				// preserveComments: 'some',
				banner: '/* <%= pkg.title %> */\n'
			},
			admin: {
				files: [ {
					expand: true,
					cwd: '<%= dirs.js %>/',
					src: [
						'*.js',
						'!*.min.js'
					],
					dest: '<%= dirs.js %>/',
					ext: '.min.js'
				} ]
			}
		},

		// CSS linting with stylelint.
		stylelint: {
			options: {
				configFile: '.stylelintrc.json'
			},
			all: [
				'<%= dirs.css %>/**/*.css',
				'!**/*.min.css'
			]
		},

		// Minify .css files.
		cssmin: {
			minify: {
				options: {
					banner: '/* <%= pkg.title %> */'
				},
				files: [ {
					expand: true,
					cwd: '<%= dirs.css %>/',
					src: [
						'*.css',
						'!*.min.css'
					],
					dest: '<%= dirs.css %>/',
					ext: '.min.css'
				} ]
			}
		},

		// PHP Code Sniffer.
		phpcs: {
			application: {
				src: [
					'**/*.php',
					'!node_modules/**',
					'!<%= dirs.build %>/**'
				]
			},
			options: {
				bin: options.phpcs_bin + ' --exclude=Generic.Files.LineEndings',
				standard: 'WordPress-Extra',
				verbose: true
			}
		},

		// Watch changes for assets.
		watch: {
			js: {
				files: [ '<%= dirs.js %>/*.js' ],
				tasks: [ 'uglify' ],
				options: {
					spawn: false
				}
			},
			css: {
				files: [ '<%= dirs.css %>/*.css' ],
				tasks: [ 'cssmin' ],
				options: {
					spawn: false
				}
			},
			readme: {
				files: [ 'readme.txt' ],
				tasks: [ 'wp_readme_to_markdown' ],
				options: {
					spawn: false
				}
			}
		},

		// Clean build dir.
		clean: {
			main: [ '<%= dirs.build %>/<%= pkg.name %>' ]
		},

		// Copy the plugin to a versioned release directory.
		copy: {
			main: {
				src: [
					'**',
					'!.git/**',
					'!.gitignore',
					'!.gitmodules',
					'!.jshintrc',
					'!.scrutinizer.yml',
					'!node_modules/**',
					'!<%= dirs.build %>/**',
					'!Gruntfile.js',
					'!package.json',
					'!package-lock.json',
					'!composer.json',
					'!LICENSE',
					'!README.md',
					'!CONTRIBUTING.md',
					'!CHANGELOG.md',
					'!TODO.md',
					'!nbproject/**',
					'!**/*.LCK',
					'!**/_notes/**',
					'!tmp/**',
					'!assets-wp/**',
					'!bin/**',
					'!tests/**',
					'!Makefile',
					'!phpcs.xml',
					'!phpunit.xml'
				],
				dest: '<%= dirs.build %>/<%= pkg.name %>/'
			}
		},

		// Convert line endings to LF.
		lineending: {
			build: {
				options: {
					eol: 'lf',
					overwrite: true
				},
				files: [ {
					expand: true,
					cwd: '<%= dirs.build %>/<%= pkg.name %>/',
					src: [ '**/*.{php,css,js,po,txt}' ]
				} ]
			}
		},

		// Create zip package.
		compress: {
			main: {
				options: {
					mode: 'zip',
					archive: './<%= dirs.build %>/<%= pkg.name %>-<%= pkg.version %>.zip'
				},
				expand: true,
				cwd: '<%= dirs.build %>/<%= pkg.name %>/',
				src: [ '**/*' ],
				dest: '<%= pkg.name %>/'
			}
		},

		// Generate .pot file.
		makepot: {
			target: {
				options: {
					domainPath: '/languages',
					exclude: [ '<%= dirs.build %>/.*', 'tmp/.*' ],
					potFilename: 'rest-xmlrpc-data-checker.pot',
					processPot: function( pot ) {
						var translation, deleteTranslation,
							excludedStrings = [ 'Title:', 'Visual', 'HTML', 'Cheatin&#8217; uh?', 'Automatically add paragraphs' ],
							excludedMeta = [ 'Plugin Name of the plugin/theme', 'Plugin URI of the plugin/theme', 'Author of the plugin/theme', 'Author URI of the plugin/theme' ];
						pot.headers['report-msgid-bugs-to'] = 'https://github.com/enrico-sorcinelli/rest-xmlrpc-data-checker/issues\n';
						pot.headers['plural-forms'] = 'nplurals=2; plural=n != 1;';
						pot.headers['last-translator'] = 'Fname Lname <email@address>\n';
						pot.headers['language-team'] = 'Fname Lname <email@address>\n';
						pot.headers['x-poedit-basepath'] = '.\n';
						pot.headers['x-poedit-language'] = 'English\n';
						pot.headers['x-poedit-country'] = 'United States\n';
						pot.headers['x-poedit-sourcecharset'] = 'utf-8\n';
						pot.headers['x-poedit-keywordslist'] = '__;_e;__ngettext:1,2;_n:1,2;__ngettext_noop:1,2;_n_noop:1,2;_c,_nc:4c,1,2;_x:1,2c;_ex:1,2c;_nx:4c,1,2;_nx_noop:4c,1,2;esc_html__;esc_html_e;esc_html_x;esc_attr__;esc_attr_e;esc_attr_x;\n';
						pot.headers['x-poedit-bookmarks'] = '\n';
						pot.headers['x-poedit-searchpath-0'] = '.\n';
						pot.headers['x-textdomain-support'] = 'yes\n';
						for ( translation in pot.translations['']) {
							deleteTranslation = false;
							if ( 0 <= excludedStrings.indexOf( translation ) ) {
								deleteTranslation = true;
								console.log( 'Excluded string: ' + translation );
							}
							if ( 'undefined' !== typeof pot.translations[''][translation].comments.extracted ) {
								if ( 0 <= excludedMeta.indexOf( pot.translations[''][translation].comments.extracted ) ) {
									deleteTranslation = true;
									console.log( 'Excluded meta: ' + pot.translations[''][translation].comments.extracted );
								}
							}
							if ( deleteTranslation ) {
								delete pot.translations[''][translation];
							}
						}
						return pot;
					},
					type: 'wp-plugin',
					updateTimestamp: true
				}
			}
		},

		// Check plugin text domain.
		checktextdomain: {
			options: {
				text_domain: 'rest-xmlrpc-data-checker', // eslint-disable-line camelcase
				keywords: [
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
					'_nx_noop:1,2,3c,4d'
				],
				report_missing: false // eslint-disable-line camelcase
			},
			files: {
				src: [
					'**/*.php',
					'!node_modules/**',
					'!<%= dirs.build %>/**'
				],
				expand: true
			}
		},

		// Generate .mo files from .po files.
		potomo: {
			dist: {
				options: {
					poDel: false
				},
				files: [ {
					expand: true,
					cwd: '<%= dirs.languages %>',
					src: [ '*.po' ],
					dest: '<%= dirs.languages %>',
					ext: '.mo',
					nonull: true
				} ]
			}
		},

		// Generate README.md from readme.txt.
		wp_readme_to_markdown: { // eslint-disable-line camelcase
			readme: {
				files: {
					'README.md': 'readme.txt'
				},
				options: {
					screenshot_url: 'https://raw.githubusercontent.com/enrico-sorcinelli/<%= pkg.name %>/master/assets-wp/{screenshot}.png' // eslint-disable-line camelcase
				}
			}
		},

		// Check version.
		checkwpversion: {
			options: {
				readme: 'readme.txt',
				plugin: 'rest-xmlrpc-data-checker.php'
			},
			pluginVsReadme: { //Check plugin header version against stable tag in readme
				version1: 'plugin',
				version2: 'readme',
				compare: '=='
			},
			pluginVsGrunt: { //Check plugin header version against package.json version
				version1: 'plugin',
				version2: '<%= pkg.version %>',
				compare: '=='
			},
			pluginVsInternal: { //Check plugin header version against internal defined version
				version1: 'plugin',
				version2: grunt.file.read( 'rest-xmlrpc-data-checker.php' ).match( /VERSION', '(.*)'/ )[1],
				compare: '=='
			},
			pluginVsCHANGELOG: {
				version1: 'plugin',
				version2: grunt.file.read( 'CHANGELOG.md' ).match( /## \[(.*)\]/ )[1],
				compare: '=='
			}
		}

	});

	// Load NPM tasks to be used here.
	require( 'load-grunt-tasks' )( grunt );

	// Register tasks.
	grunt.registerTask( 'default', [
		'cssmin',
		'uglify'
	]);

	grunt.registerTask( 'check', [
		'phpcs',
		'jshint',
		'eslint',
		'stylelint',
		'checktextdomain',
		'checkwpversion'
	]);

	grunt.registerTask( 'languages', [
		'checktextdomain',
		'makepot',
		'potomo'
	]);

	grunt.registerTask( 'readme', [
		'wp_readme_to_markdown'
	]);

	grunt.registerTask( 'build', [
		'check',
		'default',
		'readme',
		'clean',
		'copy',
		'lineending',
		'compress'
	]);

};
