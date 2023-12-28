<?php
//e335cb473ab90265a531a14120ef424c
$apikey = get_option('newsplugin_save_api_key');
// adding js in footer for simplicity and to avoid loading it on the whole site
//add_action( 'admin_footer', 'newsplugin_api_javascript' ); 

//function newsplugin_api_javascript() { ?>
	 <?php
//}//newsplugin_api_javascript

?> 
<h1>News Plugin</h1>
<p>Go to <a href="https://gnews.io/">https://gnews.io/</a> to Sign up and generate API Key</p>
<form action="#" method="post">
<input id="apikey" type="text" name="apikey" value="<?php echo $apikey ?>">
<input id="form-button-submit" type="submit" value="Save API">
</form>
<p id="newsplugin_submit_message"></p>
<h2>Generate Posts</h2>
<form action="#" method="post">
<label>Enter Your Search Term:</label><br>
<input id="news-search-input" type="text"><br>
<label>Max News To Get:</label><br>
<input id="news-max-input" type="number" value="10"><br>
<label for="language">Select a language:</label><br>
  <select name="language" id="news-language-input">
    <option value="ar">Arabic</option>
    <option value="zh">Chinese</option>
    <option value="en" selected>English</option>
    <option value="hi">Hindi</option>
  </select><br>
  <label for="countries">Select a Country:</label><br>
  <select name="countries" id="news-countries-input">
    <option value="cn">China</option>
    <option value="pk">Pakistan</option>
    <option value="us" selected>United States</option>
    <option value="in">India</option>
  </select>
<input id="form-search-button-submit" type="submit" value="Generate News">
</form>
<p id="newsplugin_search_submit_message"></p>
<div class="loader"></div>
<p>Use do_shortcode('[generatenews]') to include search form</p>
<style>
	form{
		padding:20px 50px;
	}
	#newsplugin_search_submit_message{
    color:#dd5f56;
     font-size:14px;
    font-weight:bold;
    font-style:italic;
}
	input[type=text],  select,input[type=number]{
  width: 100%;
  padding: 12px 20px;
  border: none;
  border-left:5px solid #080e7b;
  margin: 8px 0;
  color:white;
  background-color:rgba( 8, 14, 123  , 0.6);
  box-sizing: border-box;
  resize: none;
  transition:background-color 2s;
}
input[type=submit] {
 width: 100%;
 background-color: #080e7b;
 color: white;
 padding: 14px 20px;
 margin: 8px 0;
 border: none;
 border-radius: 4px;
 cursor: pointer;
 font-weight: bold;
  outline: none;
}
input[type=submit]:hover {
 background-color: #464d5f;
}
input[type=text]:focus, input[type=number]:focus{
  background-color: #080e7b;
  color: white;
  outline: none;
  
}
.loading {
  border: 6px solid #f3f3f3;
  border-radius: 50%;
  border-top: 6px solid #FF4337;
  width: 40px;
  height: 40px;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;
}

/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
::-webkit-input-placeholder { /* Edge */
  color: white;
}

:-ms-input-placeholder { /* IE 10-11 */
  color: white;
}

::placeholder {
  color: white;
}
</style>