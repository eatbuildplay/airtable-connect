(function($) {

	$( document ).on('click', '#airtable_cancel_connection_button', function(e) {
		e.preventDefault()
		$('#airtable_add_connection_button').show()
		$('#admin_form_connections').hide()
	})

	// add connection
	$( document ).on('click', '#airtable_add_connection_button', function(e) {
		e.preventDefault()
		$(this).hide()
		$('#admin_form_connections').show()
		$('#airtable_cancel_connection_button').show()
		$('#airtable_save_connection_button').show()
		$('.canvas-type').html( $('#select_connection_type').html() )
	})

	// select type of connection
	$( document ).on('change', '.connection-type-select', function() {

		var connectionType = $(this).val()
		if( connectionType == 0 ) {
			return;
		}

		var baseIdEntry = $('#base_id_entry')
		$('.canvas-base').html( baseIdEntry.html() )

		if( connectionType == 'learndash') {
			var learndashTableFields = $('#learndash_table_fields')
			$('.canvas-table').html( learndashTableFields.html() )
		}

		if( connectionType == 'events_manager_pro') {
			var eventsManagerTableFields = $('#events_manager_table_fields')
			$('.canvas-table').html( eventsManagerTableFields.html() )
		}

	})

	/*
	 * Delete connection handler
	 */
	$( document ).on('click', '.delete-connection button', function() {
		var row = $(this).closest('tr')
		var connectionId = row.data('connection-id')

		row.after( $('#delete_connection').html() )

	})

	// cancel delete connection
	$(document).on('click', '.delete-connection-cancel', function() {
		$(this).closest('tr').remove()
	})

	// confirm delete connection
	$(document).on('click', '.delete-connection-confirm', function() {

		var row = $(this).closest('tr').prev()
		var connectionIndex = row.data('connection-index')

		row.remove()
		$(this).closest('tr').remove()

		data = {
			action: 'airtable_connect_delete',
			index: connectionIndex
		}
		$.post( ajaxurl, data, function( response ) {
			if ( response.status == 'success' ) {

			} else {

			}
		});
	})

	/*
	 * Test API Key
	 */
	$( document ).on('click', '#airtable-api-test-key', function() {

		data = {
			action: 'airtable_connect_api_test'
		}
		$.post( ajaxurl, data, function( response ) {
			if ( response.status == 'success' ) {

			} else {

			}
		});

	})

})( jQuery );
