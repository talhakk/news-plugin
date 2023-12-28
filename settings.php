<?php
//e335cb473ab90265a531a14120ef424c
$apikey = get_option('newsplugin_save_api_key');
// adding js in footer for simplicity and to avoid loading it on the whole site
add_action( 'admin_footer', 'newsplugin_api_javascript' ); 

function newsplugin_api_javascript() { ?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
/*
	Submitting API Key via Ajax
*/
    $('#form-button-submit').on('click', function(e){
       e.preventDefault();
       var key =$("#apikey").val();
		var data = {
			'action': 'save_api_key',
			'newsapikey': key
		};  
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			document.getElementById('newsplugin_submit_message').innerHTML='Your API Key: '+response+ '\n has been saved successfully';         
		});
       
    });//preventdefault
    /*
	Submitting Search Form Data
	*/
	$('#form-search-button-submit').on('click', function(e){
       e.preventDefault();
       var searchinput =$("#news-search-input").val();
       var searchmax=$("#news-max-input").val();
	   var searchlang=$("#news-language-input").val();
	   var searchcountry=$("#news-countries-inpu").val();
	 
		var data = {
			'action': 'search_news',
			'searchterm': searchinput,
			'maxnews': searchmax,
			'searchlang':searchlang,
			'searchregion':searchcountry
			
		};
    
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			document.getElementById('newsplugin_search_submit_message').innerHTML=response;
		});
       
    });//preventdefault
	});
	</script> <?php
}//newsplugin_api_javascript

?> 
<form action="#" method="post">
<input id="apikey" type="text" name="apikey" value="<?php echo $apikey ?>">
<input id="form-button-submit" type="submit" value="Save API">
</form>
<p id="newsplugin_submit_message"></p>

<form action="#" method="post">
<input id="news-search-input" type="text">
<input id="news-max-input" type="number" value="10">
<label for="language">Select a language:</label>
  <select name="language" id="news-language-input">
    <option value="ar">Arabic</option>
    <option value="zh">Chinese</option>
    <option value="en" selected>English</option>
    <option value="hi">Hindi</option>
  </select>
  <label for="countries">Select a Country:</label>
  <select name="countries" id="news-countries-input">
    <option value="cn">China</option>
    <option value="pk">Pakistan</option>
    <option value="us" selected>United States</option>
    <option value="in">India</option>
  </select>
<input id="form-search-button-submit" type="submit" value="Generate News">
</form>
<p id="newsplugin_search_submit_message"></p>