<?php

$connections = get_option('airtable_connect_connections', array());

?>

<div class="admin-wrap">

	<h1>AirTable Connections</h1>

	<div class="form-field">
		<button id="airtable_add_connection_button" class="btn">Add Connection</button>
	</div>

	<form
		action="<?php echo esc_url( admin_url( 'admin-post.php' )); ?>"
		id="admin_form_connections"
		method="POST">

		<input type="hidden" name="action" value="airtable_connect_connections">

		<div class="canvas">
			<div class="canvas-type"></div>
			<div class="canvas-base"></div>
			<div class="canvas-table"></div>
		</div>

		<div class="form-field">
			<input id="airtable_save_connection_button" type="submit" value="Save Connection" />
		</div>

    <div class="form-field">
			<button id="airtable_cancel_connection_button">Cancel Add Connection</button>
		</div>

	</form>

	<table>
		<thead>
			<tr>
				<th>Connection Type</th>
				<th>Base ID</th>
        <th>Tables</th>
        <th>&nbsp;</th>
				<th>&nbsp;</th>
        <th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php
				if( !empty( $connections )) {
				foreach( $connections as $index => $connection ) {
			?>
				<tr data-connection-index="<?php print $index; ?>">
					<td><?php print $connection['type']; ?></td>
					<td><?php print $connection['base_id']; ?></td>
          <td>
            <?php
              if( isset( $connection['tables'] ) && !empty( $connection['tables'] )) {
                foreach( $connection['tables'] as $table ) {
                  print $table . '<br />';
                }
              }
            ?>
          </td>
					<td class="test-connection">
						<button>Test Connection</button>
					</td>
          <td class="test-connection">
						<button>Edit Connection</button>
					</td>
          <td class="delete-connection">
						<button>Delete Connection</button>
					</td>
				</tr>
			<?php
				}}
			?>
		</tbody>
	</table>

</div>

<template id="delete_connection">
  <tr>
    <td colspan="5">
      <h3>Are you sure you want to permanently delete this AirTable connection?</h3>
      <button class="delete-connection-confirm">Yes, Permanently Delete</button>
      <button class="delete-connection-cancel">No, Cancel</button>
    </td>
  </tr>

</template>

<template id="select_connection_type">
	<div class="form-field">
		<select class="connection-type-select" name="connection_type">
			<option value="0">Choose a Connection Type</option>
			<option value="learndash">LearnDash</option>
			<option value="events_manager_pro">Events Manager Pro</option>
		</select>
	</div>
</template>

<template id="learndash_table_fields">

	<div class="form-field">
		<label>Quiz Statistic Table</label>
		<input type="text" name="airtable_learndash_table_quiz_stat" />
	</div>

	<div class="form-field">
		<label>Question Statistic Table</label>
		<input type="text" name="airtable_learndash_table_question_stat" />
	</div>

</template>

<template id="events_manager_table_fields">
	<div class="form-field">
		<label>Event Booking Table</label>
		<input type="text" name="airtable_event_manager_table_booking" />
	</div>
</template>

<template id="base_id_entry">
	<div class="form-field">
		<label>Base ID</label>
		<input name="base_id" type="text" />
	</div>
</template>
