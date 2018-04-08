Jurassic Ninja is both a site, and a WordPress plugin.

The site at https://jurassic.ninja is a WordPress installation with this plugin on top.

The general idea is that the page at `https://jurasic.ninja/create` will launch a WordPress site right away and allow you to access the wp-admin without any credentials involved the first time you reach the freshly created site.


## General Architecture

Jurassic Ninja is a WordPress plugin that allows a site to become a launcher of new WordPress Sites.

When everything is set up, `jurassic.ninja/create` will launch a new WordPress site which will autologin (as admin) the first user that visits the site.

It does this by leveraging the ServerPilot API. If you don't know what ServerPilot is, please head to their site to find out more. In brief, ServerPilot is a simple UI that allows the creating of multiple PHP environments in a single Server for rapid deployment of apps. It also makes it super easy to launch a WordPress site ready to go.

Jurassic Ninja takes advantage of ServerPilot's features and adds the ability to install plugins and a bunch of other features on the created WordPress sites.

### The companion plugin

Every launched site, will have installed a plugin called [companion](https://github.com/oskosk/companion).

This plugin provides the autologin functionality you experience when visiting a freshly created site.

It also handles the refreshing of the site. Each site launched with Jurassic Ninja will live for 7 days, unless you sign out and sign in again. In this case, the site life will be expanded by 7 more days. (The lifespan for a site is actually configurable, defaulting to 7 days).

### features

We call features to everything you want to add to a basic WordPress site. Mostly plugins, but there are also features like having WP_DEBUG_LOG set to true by default on launch.

The currently implemented features include:

* `jetpack` - Jetpack plugin is installed on demand if you wish.
* `jetpack-beta` - Jetpack Beta Tester plugin is installed on demand if you wish.
* `woocommerce` - WooCommerce plugin is installed on demand if you wish.
* `gutenberg` - Gutenberg plugin is installed on demand if you wish.
* `ssl` - SSl is provisioned to a site on demandd if you wish.
* `shortlife` - A site that will live for an hour instead of for 7 days.
* `config-constats` - Config Constants plugin is installed on demand if you wish.
* `wp-log-viewer` - WP Log Viewer plugin is installed on demand if you wish.
* `wp-rollback` - WP Rollback plugin is installed on demand if you wish.
* `wp-downgrade` - WP Downgrade plugin is installed on demand if you wish.
* `wp-debug-log` - The constants `WP_DEBUG` and `WP_DEBUG_LOG are set to true on launch.
* `wordpress-beta-tester` - WordPress Beta Tester plugin is installed on demand if you wish.
* `subdomain_multisite` - The site is configured as a subdomain-based multisite installation
* `subdir_multisite` - The site is configured as a subdir-based multisite installation

Some of these features are set up so to be configurable as defaults when launching a site. Like `woocommerce`, `jetpack`, `jetpack-beta`, `wp-rollback` or `wp-downgrade`.

Examples:

* Launch a site with the ability to test old versions of Jetpack and WordPress.
    `https://jurassic.ninja/create?jetpack&wp-downgrade&wp-rollback`
* Launch a site with jetpack woocommerce and jetpack.
    `https://jurassic.ninja/create?jetpack&woocommerce`.

_Actually, `jetpack` feature is currently set to be on for each launched site so you don't need to specify it in the URL.

### The regular life cycle of a Jurassic Ninja site

```
* User gets to the Create page
* The JS on the create page fires a request to REST API endpoint: `/wp-json/jurassicninja/create`.
* The PHP function `launch_wordpress()` gets called and the new site is launched with all the features.
* The REST request finishes with an URL for the created site.
* That URL is shown to the user.
* The user visits the site, gets auto-logged in, acquiring a WordPress cookie.
* The site is purged 7 days after that first auto login or 7 days after the user entered credentials the last time. This can happen if the user signed out and signed in again.
```

The `launch_wordpress()` function can be explained as:

```
2. Collects requested features and merges with defaults.
3. Decide if we need to do something regarding the requested Features (`jurassic_ninja_do_feature_conditions` action).
4. Generates a random subdomain.
5. Creates a user. Hookable via the  `jurassic_ninja_create_sysuser` action.
6. Launches a new environment for a PHP/MysQL app and installs WordPress. Hookable via the  `jurassic_ninja_create_app` action.
7. Add features that need to be added before enabling the autologin mechanism (`jurassic_ninja_add_features_before_auto_login` action).
8. Enable autologin.
9. Add features that need to be added after enabling the autologin mechanism (`jurassic_ninja_add_features_before_auto_login` action).
```


### Adding Features

Basically you can go an peek the `features` directory which contains a few files that make use of the available actions and filters.

But the main hooks to look are:

## Actions and filters defined by the Jurassic Ninja plugin

Jurassic Ninja defines a few actions and filters to hook features and functionality for better extensibility.

It's better if you check this list and then grep the source code to find out how they interact with everything else.

### Actions

* jurassic_ninja_init (Action).
* jurassic_ninja_admin_init (Action).
* jurassic_ninja_do_feature_conditions (Action).
* jurassic_ninja_add_features_before_auto_login (Action).
* jurassic_ninja_add_features_after_auto_login (Action).

### Filters

* jurassic_ninja_settings_options_page (Filter).
* jurassic_ninja_settings_options_page_default_plugins (Filter).
* jurassic_ninja_rest_feature_defaults (Filter).
* jurassic_ninja_rest_create_request_features (Filter ).
* jurassic_ninja_feature_command (Filter).
* jurassic_ninja_created_site_url (Filter).

### Constants

* `jn\REST_API_NAMESPACE` - The namespace used for the REST API extensions.
* `jn\COMPANION_PLUGIN_URL` - The URL from where the companion plugin will be fetched.
* `jn\JETPACK_BETA_PLUGIN_URL` - The URL from where the Jetpack Beta Plugin URL will be fetched.
* `jn\SUBDOMAIN_MULTISITE_HTACCESS_TEMPLATE_URL` - The URL from where the basic `.htaccess` file for subdomain-based multisite will be fetched.
* `jn\SUBDIR_MULTISITE_HTACCESS_TEMPLATE_URL` - The URL from where the basic `.htaccess` file for subdi-based multisite will be fetched.
