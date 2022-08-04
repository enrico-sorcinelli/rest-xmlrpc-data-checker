# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [1.4.0] - 2022-08-04
### Added
 - Tested up to WordPress 6.0.1
 - Development Grunt task to check _CHANGELOG_ version.
 - `--phpcs-bin` Grunt option.
 - Used [jQuery Flex Tree](https://github.com/enrico-sorcinelli/jquery-flex-tree) (1.2.2) library for checkbox three-state selection.
 - Allow to set PHPUnit Polyfills path library from `WP_TESTS_PHPUNIT_POLYFILLS_PATH` environment variable (test units).

### Changed
 - Multisite support for network superadmin plugin's capabilities.
 - Documentation improvements.

### Fixed
 - Coding standards.
 - XML-RPC tests since from WP 5.7.3 'xmlrpc_enabled' filter has been moved to `wp_xmlrpc_server()` constructor.

## [1.3.2] - 2020-08-14
### Added
 - Tested up to WordPress 5.5.
 - Minified assets.
 - Added Node.js and Grunt for development and build. 

### Fixed
 - Improved PHP, JavaScript and CSS coding standard.

## [1.3.1] - 2019-05-28
### Added
 - Tested up to WordPress 5.2.1.
 - New XML-RPC option allowing to apply WordPress rendering to `post_content` (this will prevent to leave blocks comments).
 - New XML-RPC option allowing to restore original `post_status` value.
 - Allows to use PHP single line comments (with `//` or `#`) in trusted network option.

### Changed
 - Updated translations and screenshots.
 - Documentation improvements.

### Fixed
 - PHPCS standars.
 
## [1.3.0] - 2019-03-09
### Added
 - New option allowing to disable evaluation of originating IP in HTTP headers added by proxy or load balancer for trusted network check.

### Changed
 - Updated translations and screenshots.
 - Documentation improvements.

## [1.2.2] - 2018-12-12
### Fixed
 - _User's grants_ setting saving.
 - Check for including XML-RPC related libraries.

## [1.2.1] - 2018-12-03
### Fixed
 - Typo in users column hook.

## [1.2.0] - 2018-12-03
### Added
 - REST JSON and XML-RPC users grants status column.
 - Tested up to WordPress 5.0.0.

### Changed
 - Updated translations and screenshots.
 - Documentation improvements.

## [1.1.0] - 2018-11-25
### Added
 - Allows to remove REST API Really Simple Discovery (RSD) endpoint.
 - Allows to remove `<link>` to the Really Simple Discovery service endpoint on front-end pages.
 - Allows to remove `X-Pingback` HTTP Header on front-end pages.

### Changed
 - Updated translations and screenshots.
 - Documentation improvements.

## [1.0.1] - 2018-10-18
### Added
 - Allows to remove REST and Discovery oEmbed `<link>` tags on front-end pages.
 - Allows to remove REST HTTP Header `Link`.
 - Contextual help menu in plugin admin setting screen.
 - Remove REST/XML-RPC user's capabilities added by plugin on plugin removal.

### Fixed
 - First plugin options save.
 - Maintains last opened tab after plugin options saving.
 - PHPCS.

### Changed
 - Documentation improvements.

## [1.0.0] - 2018-10-10
### Added
 - First public release.
