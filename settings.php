<?php
echo 'The News Plugin Settings Page';


// add a new option to save API key
add_option('newsplugin_save_api_key', 'e335cb473ab90265a531a14120ef424c');
// get an option

$apikey = get_option('newsplugin_save_api_key');


//$apikey = 'e335cb473ab90265a531a14120ef424c';
$url = "https://gnews.io/api/v4/search?q=google&lang=en&country=us&max=10&apikey=$apikey";

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
}

