<?php

$apiKey = get_option('airtable_api_key');

?>

<div class="admin-wrap">

	<h1>API Settings</h1>

	<form
		action="<?php echo esc_url( admin_url( 'admin-post.php' )); ?>"
		id="admin_form"
		method="POST">

		<input type="hidden" name="action" value="airtable_connect_api">

		<div class="form-field">
			<label>AirTable API Key</label>
			<input id="api_key" name="api_key" type="password" value="<?php print $apiKey; ?>" />
		</div>

		<div class="form-field">
			<input type="submit" value="Save" />
		</div>

	</form>
	
</div>
