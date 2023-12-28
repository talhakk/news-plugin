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


/**
 * Register Taxonomy
 */
function create_news_taxonomy() {
    register_taxonomy('country','news',array(
        'hierarchical' => false,
        'labels' => array(
            'name' => _x( 'Countries', 'taxonomy general name' ),
            'singular_name' => _x( 'Country', 'taxonomy singular name' ),
            'menu_name' => __( 'Country' ),
            'all_items' => __( 'Countries' ),
            'edit_item' => __( 'Edit Country' ), 
            'update_item' => __( 'Update Country' ),
            'add_new_item' => __( 'Add Country' ),
            'new_item_name' => __( 'New Country' ),
        ),
    'show_ui' => true,
    'show_in_rest' => true,
    'show_admin_column' => true,
    ));
    register_taxonomy('Language','news',array(
        'hierarchical' => false,
        'labels' => array(
            'name' => _x( 'Languages', 'taxonomy general name' ),
            'singular_name' => _x( 'Language', 'taxonomy singular name' ),
            'menu_name' => __( 'Languages' ),
            'all_items' => __( 'Languages' ),
            'edit_item' => __( 'Edit Language' ), 
            'update_item' => __( 'Update Language' ),
            'add_new_item' => __( 'Add Language' ),
            'new_item_name' => __( 'New Language' ),
        ),
    'show_ui' => true,
    'show_in_rest' => true,
    'show_admin_column' => true,
    ));
}
add_action( 'init', 'create_news_taxonomy', 0 );
/*
Make dashboard page for settings
*/
add_action( 'admin_menu', 'newsplugin_settings_page' );
function newsplugin_settings_page() {
    add_menu_page(
        'Google News Api Settings',
        'News Plugin',
        'manage_options',
        plugin_dir_path(__FILE__) . 'settings.php',
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
    $searchMax =  $_POST['maxnews'];
    $searchLang =  $_POST['searchlang'];
    $searchRegion =  $_POST['searchregion'];
    //retriving api key
    $apikey = get_option('newsplugin_save_api_key');
    //adding double quotes to output search terms with spaces
    $url = "https://gnews.io/api/v4/search?q="."\"$searchTerm\""."&lang=$searchLang&country=$searchRegion&max=$searchMax&apikey=$apikey";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$data = json_decode(curl_exec($ch), true);
curl_close($ch);
$articles = $data['articles'];

$errors = $data['errors'];
if(!$errors==null){
    for ($i = 0; $i < count($errors); $i++) {
        //echo "Error: " . $errors[$i][0] . "\n";
       echo $errors[$i];
     
    }
}else{
    for ($i = 0; $i < count($articles); $i++) {
        // Create post object
       // $create_taxonomy=wp_insert_term($searchRegion, 'country');
    /*
    if ( ! is_wp_error( $create_taxonomy ) )
    {
        // Get term_id, set default as 0 if not set
        $cat_id = isset( $create_taxonomy['term_id'] ) ? $create_taxonomy['term_id'] : 0;
        // ... etc ...
    }
    else
    {
         // Trouble in Paradise:
         echo $create_taxonomy->get_error_message();
    }*/
    
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
    // add taxonomy data, but it will add country again each time searched 
    
    /* problem here is maybe no taxonomy at this time
    $taxonomy = 'country';
    $termObj  = get_term_by( 'id', 7, $taxonomy);
    wp_set_object_terms($post_check, $termObj, $taxonomy);
    */
    
    // Check if there was an error during post insertion
    if (is_wp_error($post_check)) {
        // Error occurred while inserting the post
        echo "Error: " . $post_check->get_error_message();
    } else {
        // The post was successfully inserted, and $post_id contains the post ID
        echo "Post inserted successfully. New Post ID: " . $post_check;
    }
    }//for loop
}//if(!$errors==null){


    //returing the response
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
