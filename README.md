# REST XML-RPC Data Checker

JSON REST API and XML-RPC API are powerful ways to remotely interact with WordPress.

If you don't have external applications that need to communicate with your WordPress instance using JSON REST API or XML-RPC API you should disable access to them for external requests.

In the standard WordPress installation JSON REST API and XML-RPC API are enabled by default and even if you could do the stuff by writing your own code using native filters, this plugin aims to help you to control JSON REST API and XML-RPC API accesses from the administration panel.

# Basic Features

* **Disable REST API** interface for unlogged users.
* **Disable JSONP support** on REST API.
* **Add Basic Authentication** to REST API.
* **Remove** REST `<link>` tags and `Link` HTTP header on front-end side.
* **Setup trusted users, IP/Networks and endpoints** for unlogged users REST requests.
* **Change REST endpoint prefix**.
* **Disable XML-RPC API** interface.
* **Setup trusted users, IP/Networks and methods** for XML-RPC requests.

# Installation

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/rest-xmlrpc-data-checker` directory, or install the plugin through the WordPress _Plugins_ screen directly.
1. Activate the plugin through the _Plugins_ screen in WordPress.

# Usage

Once the plugin is installed you can configure it in the following ways:

* Using the _Settings->REST XML-RPC Data Checker_ administration screen.
* Programmatically, by using `rest_xmlrpc_data_checker_settings` filter (see below).

# API

## Hooks

### `rest_xmlrpc_data_checker_settings`

Filters plugin settings values.

```php
apply_filters( 'rest_xmlrpc_data_checker_settings', array $settings )
```

### `rest_xmlrpc_data_checker_admin_settings`

Filter allowing to display or not the plugin settings page in the administration.

```php
apply_filters( 'rest_xmlrpc_data_checker_admin_settings', boolean $display )
```

### `rest_xmlrpc_data_checker_rest_error`

Filter REST authentication error after plugin checks.

```php
apply_filters( 'rest_xmlrpc_data_checker_rest_error', WP_Error|boolean $result )
```

### `xmlrpc_before_insert_post`

Filter XML-RPC post data to be inserted via XML-RPC before to insert post into database.

```php
apply_filters( 'xmlrpc_before_insert_post', array|IXR_Error $content_struct, WP_User $user );
```

# Frequently Asked Questions

## Does it work with Gutenberg?

Yes.

# Screenshots 

### REST settings ###

The REST settings section.

![REST settings](https://raw.githubusercontent.com/enrico-sorcinelli/rest-xmlrpc-data-checker/master/assets-wp/screenshot-1.png)

### XML-RPC settings ###

The XML-RPC settings section.

![XML-RPC settings](https://raw.githubusercontent.com/enrico-sorcinelli/rest-xmlrpc-data-checker/master/assets-wp/screenshot-2.png)

### User profile ###

Enable XML-RPC and REST interfaces on user profile/user edit pages (available only for users with `edit_users` capability).

![Plugin settings](https://raw.githubusercontent.com/enrico-sorcinelli/rest-xmlrpc-data-checker/master/assets-wp/screenshot-3.png)

# License: GPLv2 #

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.