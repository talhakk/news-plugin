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
	register_post_type( 'news',
        array(
            'labels' => array(
                'name' => __( 'News' ),
                'singular_name' => __( 'News' )
            ),
            'public' => true,
            'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'has_archive' => true,
        'rewrite'   => array( 'slug' => 'google-news' ),
            'menu_position' => 5,
        'menu_icon' => 'dashicons-align-pull-left',
        )
    );
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

/*
Make dashboard page for settings
*/
add_action( 'admin_menu', 'newsplugin_settings_page' );
function newsplugin_settings_page() {
    add_menu_page(
        'Google News Api Settings',
        'News Plugin',
        'manage_options',
        plugin_dir_path(__FILE__) . 'settings2.php',
        null,
        '',
        20
    );
}
/*
Getting News Api Key and Saving it to options
*/
add_action( 'wp_ajax_save_api_key', 'save_api_key' );

function save_api_key() {
	global $wpdb; // accessing database

	$newsapikey =  $_POST['newsapikey'];
//saving api key and checking if it already exist, in such a case updating it
    if(!get_option('newsplugin_save_api_key')){
        add_option('newsplugin_save_api_key', $newsapikey);
    }else{
        update_option('newsplugin_save_api_key', $newsapikey);
    }
    echo $newsapikey;
	wp_die(); // this is required to terminate immediately and return a proper response
}
/*
Getting Search Form Data and Generating the Custom News Posts
*/
add_action( 'wp_ajax_search_news', 'search_news' );

function search_news() {
	$searchTerm =  $_POST['searchterm'];
    //retriving api key
    $apikey = get_option('newsplugin_save_api_key');
    $url = "https://gnews.io/api/v4/search?q=$searchTerm&lang=en&country=us&max=10&apikey=$apikey";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$data = json_decode(curl_exec($ch), true);
curl_close($ch);
$articles = $data['articles'];

for ($i = 0; $i < count($articles); $i++) {
   /* echo "Title: " . $articles[$i]['title'] . "\n";
    echo "Description: " . $articles[$i]['description'] . "\n";
    echo "Description: " . $articles[$i]['content'] . "\n";
*/
    // Create post object
$new_post = array(
    'post_type' => 'news',
    'post_title'    => wp_strip_all_tags( $articles[$i]['title'] ),
    'post_content'  => $articles[$i]['content'],
    'post_excerpt'  => $articles[$i]['description'],
    'post_status'   => 'publish',
    'post_author'   => 1
    );
    
    // Insert post into the database
    // Use $wp_error set to true for error handling
$post_check = wp_insert_post($new_post, true); 

// Check if there was an error during post insertion
if (is_wp_error($post_check)) {
    // Error occurred while inserting the post
    echo "Error: " . $post_check->get_error_message();
} else {
    // The post was successfully inserted, and $post_id contains the post ID
    echo "Post inserted successfully. New Post ID: " . $post_check;
}
}//for loop
    
    //returing the response
    echo $searchTerm;
	wp_die(); // this is required to terminate immediately and return a proper response
}
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
