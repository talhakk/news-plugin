<?php
/*
Plugin Name: News Plugin
Description: Google news api plugin 
Version: 1
Author URI: https://wordpressflash.com/
*/

/**
 * Register the "news" custom post type
 */
function newsplugin_setup_post_type() {
	register_post_type( 'news', ['public' => true ] ); 
} 
add_action( 'init', 'newsplugin_setup_post_type' );


/**
 * Activate the plugin.
 */
function newsplugin_activate() { 
	// Trigger the function to register the custom post type plugin.
	newsplugin_setup_post_type(); 
	// Clear the permalinks after the post type has been registered.
	flush_rewrite_rules(); 
}
register_activation_hook( __FILE__, 'newsplugin_activate' );



/**
 * Deactivation hook.
 */
function newsplugin_deactivate() {
	// Unregister the post type, so the rules are no longer in memory.
	unregister_post_type( 'book' );
	// Clear the permalinks to remove our post type's rules from the database.
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'newsplugin_deactivate' );