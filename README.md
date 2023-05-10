# Awesome Motive API Plugin Challenge Block

== Description ==

Application submission for the position of Awesome Motive WordPress Developer

As requested, this plugin provides the following functionality:

* A REST endpoint that provides the data retrieved from your provided external API (caching results for an hour)
* A Gutenberg block, called "Israel Curtis API Challenge" which displays the retrieved data in a table, with settings to toggle columns
* A WP CLI command, `wp curtis force_refresh` to delete the API cache and ensure fresh data upon next call of the REST endpoint
* An admin page which displays the table, and provides a button to manually refresh the data

== Usage ==

1. Upload the plugin files to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Insert the "Israel Curtis API Challenge" block on any page or post
4. Customize display of individual table columns as desired (for that particular block placement)
5. Visit the Curtis Challenge admin settings page to view the current table data and manually force a refresh!


== Changelog ==

= 1.0.0 =
* Release
