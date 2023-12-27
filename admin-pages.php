<?php
echo 'The News Plugin Settings Page';
$apikey = 'e335cb473ab90265a531a14120ef424c';
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
$my_post = array(
    'post_type' => 'news',
    'post_title'    => wp_strip_all_tags( $articles[$i]['title'] ),
    'post_content'  => $articles[$i]['content'],
    'post_excerpt'  => $articles[$i]['description'],
    'post_status'   => 'publish',
    'post_author'   => 1
    );
    
    // Insert the post into the database
    wp_insert_post( $my_post );
}

