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
	   $(".loader").addClass("loading");
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
			$(".loader").removeClass("loading");
		});
       
    });//preventdefault
	 /*
	Submitting Search Form Data
	*/
	$('#shortcode-form-search-button-submit').on('click', function(e){
		e.preventDefault();
		var shortcodesearchinput =$("#shortcode-news-search-input").val();
	  
		 var data = {
			 'action': 'shortcode_news',
			 'shortcodesearchterm': shortcodesearchinput	 
		 };
	 
		 // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		 jQuery.post(ajaxurl, data, function(response) {
			 document.getElementById('shortcode-newsplugin_search_submit_message').innerHTML=response;
		 });
		
	 });//preventdefault
	});
