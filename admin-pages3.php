<?php
$apikey = get_option('newsplugin_save_api_key');
add_action( 'admin_footer', 'newsplugin_api_javascript' ); // Write our JS below here

function newsplugin_api_javascript() { ?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
        $('#form-button-submit').on('click', function(e){
    e.preventDefault();
    var key =$("#apikey").val();
    


		var data = {
			'action': 'save_api_key',
			'newsapikey': key
		};
    
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			alert('Got this from the server: ' + response);
            $("#newsplugin_submit_message").val("API Key is stored successfully");
		});
       
    });//myadin
	});
	</script> <?php
}

?> 
<form action="#" method="post">
<input id="apikey" type="text" name="apikey" value="<?php echo $apikey ?>">
<input id="form-button-submit" type="submit">
</form>
<p id="newsplugin_submit_message"></p>

