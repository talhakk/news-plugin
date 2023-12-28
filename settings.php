<?php
//e335cb473ab90265a531a14120ef424c
$apikey = get_option('newsplugin_save_api_key');
// adding js in footer for simplicity and to avoid loading it on the whole site
add_action( 'admin_footer', 'newsplugin_api_javascript' ); 

//function newsplugin_api_javascript() { ?>
	 <?php
//}//newsplugin_api_javascript

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