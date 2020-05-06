<?php

$log = new \AirtableConnect\Log;
$entries = $log->read();

?>

<div class="admin-page-wrap">

	<h1>AirTable Connect Logs</h1>

	<table id="airtable_connect_log_table">
		<thead>
			<tr>
				<th>Time</th>
				<th>Action</th>
				<th>Code</th>
				<th>Message</th>
				<th>Data</th>
			</tr>
		</thead>
		<tbody>
			<?php
				if( !empty( $entries )) :
				foreach( $entries as $entry ) :
			?>
				<tr>
					<td><?php print $entry->timestamp; ?></td>
					<td><?php print $entry->action; ?></td>
					<td><?php print $entry->code; ?></td>
					<td><?php print $entry->message; ?></td>
					<td>
						<pre>
							<?php var_dump( $entry->data ); ?>
						</pre>
					</td>
				</tr>
			<?php endforeach; endif; ?>
		</tbody>
	</table>

	<!-- clear log form -->
	<form
		action="<?php echo esc_url( admin_url( 'admin-post.php' )); ?>"
		id="admin_form"
		method="POST">
		<input type="hidden" name="action" value="airtable_connect_log_clear">
		<input type="hidden" name="reset_log" value="1" />
		<input type="submit" value="Clear Logs" />
	</form>

</div>

<style>

</style>
