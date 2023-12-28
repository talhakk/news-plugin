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
 * Register Scripts
 */
function newsplugin_enqueue_script()
{   
    //wp_enqueue_script("jquery");
    wp_enqueue_script( 'newsplugin_jquery', plugin_dir_url( __FILE__ ) . 'newsplugin.js', array('jquery'), '1.1',true );
    
}
add_action('admin_enqueue_scripts', 'newsplugin_enqueue_script');



/**
 * Add Shortcode
 */
add_shortcode('generatenews', 'newsplugin_shortcode');
function newsplugin_shortcode( ) {
    // do something to $content
    // always return
   echo'<form action="#" method="post">'; 
   echo'<input id="shortcode-news-search-input" type="text">';
   /*
   echo'<input id="news-max-input" type="number" value="10">';
   echo'<label for="language">Select a language:</label>';
   echo'<select name="language" id="news-language-input">';
   echo'<option value="ar">Arabic</option>';
   echo'<option value="zh">Chinese</option>';
   echo'<option value="en" selected>English</option>';
   echo'<option value="hi">Hindi</option>';
   echo'</select>';
   echo'<label for="countries">Select a Country:</label>';
   echo'<select name="countries" id="news-countries-input">';
   echo'<option value="cn">China</option>';
   echo'<option value="pk">Pakistan</option>';
   echo'<option value="us" selected>United States</option>';
   echo'<option value="in">India</option>';
   echo'</select>';
   */
   echo'<input id="shortcode-form-search-button-submit" type="submit" value="Generate News">';
   echo'</form>';
   echo'<p id="shortcode-newsplugin_search_submit_message"></p>';
    return ;
}

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
    $searchLang =  $_POST['searchlang'];
    $searchCountry =  $_POST['searchregion'];
    $searchCount =  $_POST['maxnews'];
    //retriving api key
    $apikey = get_option('newsplugin_save_api_key');
    //adding double quotes to output search terms with questions or special characters ( need to be implemented)
    //$url = "https://gnews.io/api/v4/search?q="."\"$searchTerm\""."&lang=$searchLang&country=$searchRegion&max=$searchMax&apikey=$apikey";
    $url = "https://gnews.io/api/v4/search?q=$searchTerm&lang=$searchLang&country=$searchCountry&max=$searchCount&apikey=$apikey";
    //Handling spaces in search term
    $url=str_replace(' ','%20',$url);
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

    $title=wp_strip_all_tags( $articles[$i]['title'] );
    if (!get_page_by_title($title, OBJECT, 'news')) :
    $new_post = array(
        'post_type' => 'news',
        'post_title'    => $title,
        'post_content'  => $articles[$i]['content'],
        'post_excerpt'  => $articles[$i]['description'],
        'post_status'   => 'publish',
        'post_author'   => 1
        );
        
        // Insert post into the database
        // Use $wp_error set to true for error handling
    $post_check = wp_insert_post($new_post, true); 
    
    // add taxonomy data, but it will add country again each time searched 
    
    
    // Check if there was an error during post insertion
    if (is_wp_error($post_check)) {
        // Error occurred while inserting the post
        echo "Error: " . $post_check->get_error_message();
    } else {
        // The post was successfully inserted, and $post_id contains the post ID
        echo nl2br("Post inserted successfully. New Post ID: " . $post_check.". \n");
    }
else:
    echo nl2br("Returned News Already exist. \n");
endif;
    }//for loop
}//if(!$errors==null){


    //returing the response
	wp_die(); // this is required to terminate immediately and return a proper response
}

/*
* Getting shortcode Form Data 
* and 
* Generating the Custom News Posts
*/
add_action( 'wp_ajax_shortcode_news', 'shortcode_news' );

function shortcode_news() {
	$shortcodesearchTerm =  $_POST['shortcodesearchterm'];
    //retriving api key
    $apikey = get_option('newsplugin_save_api_key');
    //adding double quotes to output search terms with questions or special characters ( need to be implemented)
    //$url = "https://gnews.io/api/v4/search?q="."\"$searchTerm\""."&lang=$searchLang&country=$searchRegion&max=$searchMax&apikey=$apikey";
    $url = "https://gnews.io/api/v4/search?q=$shortcodesearchTerm&lang=en&country=us&max=10&apikey=$apikey";
    //Handling spaces in search term
    $url=str_replace(' ','%20',$url);
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

    $title=wp_strip_all_tags( $articles[$i]['title'] );
    if (!get_page_by_title($title, OBJECT, 'news')) :
    $new_post = array(
        'post_type' => 'news',
        'post_title'    => $title,
        'post_content'  => $articles[$i]['content'],
        'post_excerpt'  => $articles[$i]['description'],
        'post_status'   => 'publish',
        'post_author'   => 1
        );
        
        // Insert post into the database
        // Use $wp_error set to true for error handling
    $post_check = wp_insert_post($new_post, true); 
    
    // add taxonomy data, but it will add country again each time searched 
    
    
    // Check if there was an error during post insertion
    if (is_wp_error($post_check)) {
        // Error occurred while inserting the post
        echo "Error: " . $post_check->get_error_message();
    } else {
        // The post was successfully inserted, and $post_id contains the post ID
        echo nl2br("Post inserted successfully. New Post ID: " . $post_check.". \n");
    }
else:
    echo nl2br("Returned News Already exist. \n");
endif;
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
