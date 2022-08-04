# REST XML-RPC Data Checker #
**Contributors:** enrico.sorcinelli  
**Tags:** json, rest, xmlrpc, api, security, admin, theme  
**Requires at least:** 4.4  
**Requires PHP:** 5.2.4  
**Tested up to:** 6.0  
**Stable tag:** 1.4.0  
**License:** GPLv2 or later  

REST XML-RPC Data Checker allow to check JSON REST and XML-RPC API requests and grant access permissions.

## Description ##

JSON REST API and XML-RPC API are powerful ways to remotely interact with WordPress.

If you don't have external applications that need to communicate with your WordPress instance using JSON REST API or XML-RPC API you should disable access to them for external requests.

In the standard WordPress installation JSON REST API and XML-RPC API are enabled by default.
In particular the REST API is turned on also for unlogged users. This means that your WordPress instance is potentially leaking data, for example anyone could be able to:

* copy easily your published contents natively with the REST API (and not with a web crawler);
* get the list of all users (with their ID, nickname and name);
* retrieve other information that you didn't want to be public (such as an unlisted published page or a saved media not yet used).

Even if you could do the stuff by writing your own code using native filters, this plugin aims to help you to control JSON REST API and XML-RPC API accesses from the administration panel or programmatically by a simple API filter.

== Basic Features

* **Disable REST API** interface for unlogged users.
* **Disable JSONP support** on REST API.
* **Add Basic Authentication** to REST API.
* **Remove** REST `<link>` tags, REST `Link` HTTP header and REST Really Simple Discovery (RSD) informations.
* **Setup trusted users, IP/Networks and endpoints** for unlogged users REST requests.
* **Change REST endpoint prefix**.
* **Disable XML-RPC API** interface.
* **Remove** `<link>` to the Really Simple Discovery (RDS) informations.
* **Remove** `X-Pingback` HTTP header.
* **Setup trusted users, IP/Networks and methods** for XML-RPC requests.
* **Show user's access informations** in users list administration screen.

## Installation ##

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/rest-xmlrpc-data-checker` directory, or install the plugin through the WordPress _Plugins_ screen directly.
1. Activate the plugin through the _Plugins_ screen in WordPress.

## Usage ##

Once the plugin is installed you can control settings in the following ways:

* Using the _Settings->REST XML-RPC Data Checker_ administration screen.
* Programmatically, by using `rest_xmlrpc_data_checker_settings` filter (see below).

## API ##

### Hooks ###

**`rest_xmlrpc_data_checker_settings`**

Filters plugin settings values.

`apply_filters( 'rest_xmlrpc_data_checker_settings', array $settings )`

**`rest_xmlrpc_data_checker_admin_settings`**

Filter allowing to display or not the plugin settings page in the administration.

`apply_filters( 'rest_xmlrpc_data_checker_admin_settings', boolean $display )`

**`rest_xmlrpc_data_checker_rest_error`**

Filter JSON REST authentication error after plugin checks.

`apply_filters( 'rest_xmlrpc_data_checker_rest_error', WP_Error|boolean $result )`

**`xmlrpc_before_insert_post`**

Filter XML-RPC post data to be inserted via XML-RPC before to insert post into database.

`apply_filters( 'xmlrpc_before_insert_post', array|IXR_Error $content_struct, WP_User $user )`

## Frequently Asked Questions ##

### Does it work with Gutenberg? ###

Yes

### Does it work on Multisite? ###

Yes

### How do I make REST requests using Basic Authentication?

In the _REST_ tab of plugin settings page you have to:

* check **Disable REST API interface for unlogged users** option
* select **Use Basic Authentication** in the _Authentication_ section
* select users whom you want to grant REST access
* save changes

This way, in HTTP REST external requests, users have to add `Authorization` HTTP header.

In order to generate the `Authorization` HTTP header to use with Basic Authentication you simply have to base64 encode the username and password separated by a colon.

Here is an example in PHP:
###
`$header = 'Authorization: Basic ' . base64_encode( 'my-user:my-password' );`

[Here you can see several examples](https://gist.github.com/enrico-sorcinelli/d33b6889888e95f710bc50a2090a25cf) in a variety of language.

Note that the Basic Authentication requires sending your username and password with every request, and should only be used over SSL-secured connections or for local development and testing.
Without SSL you are strongly encouraged to to turn off Basic Authentication in production environments.

## Screenshots ##

### 1. The JSON REST settings section. ###
![The JSON REST settings section.](https://raw.githubusercontent.com/enrico-sorcinelli/rest-xmlrpc-data-checker/master/assets-wp/screenshot-1.png)

### 2. The XML-RPC settings section. ###
![The XML-RPC settings section.](https://raw.githubusercontent.com/enrico-sorcinelli/rest-xmlrpc-data-checker/master/assets-wp/screenshot-2.png)

### 3. The Options settings section. ###
![The Options settings section.](https://raw.githubusercontent.com/enrico-sorcinelli/rest-xmlrpc-data-checker/master/assets-wp/screenshot-3.png)

### 4. Enable XML-RPC and REST interfaces on user profile/user edit pages (available only for users with `edit_users` capability). ###
![Enable XML-RPC and REST interfaces on user profile/user edit pages (available only for users with `edit_users` capability).](https://raw.githubusercontent.com/enrico-sorcinelli/rest-xmlrpc-data-checker/master/assets-wp/screenshot-4.png)

### 5. User list administration screen. ###
![User list administration screen.](https://raw.githubusercontent.com/enrico-sorcinelli/rest-xmlrpc-data-checker/master/assets-wp/screenshot-5.png)


## Changelog ##

For REST XML-RPC Data Checker changelog, please see [the Releases page on GitHub](https://github.com/enrico-sorcinelli/rest-xmlrpc-data-checker/releases).

## Upgrade Notice ##

### 1.4.0 ###

* Multisite support improvement for superadmin plugin's caps.
* Tested to the latest WordPress release.

### 1.3.1 ###

* Allows to use PHP single line comments in trusted network option; allows to prevent to leave blocks comments in `post_content` via XML-RPC.

### 1.3.0 ###

* Trusted networks check over IP address found in HTTP headers added by proxy or load balancer is now disabled by default. It can be enabled on plugin settings page.
