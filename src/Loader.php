<?php

namespace AirtableConnect;

class Loader {

	public function includeFiles() {

		require_once( AIRTABLE_CONNECT_PATH . '/src/Admin.php' );
		require_once( AIRTABLE_CONNECT_PATH . '/src/AirtableApi.php' );
		require_once( AIRTABLE_CONNECT_PATH . '/src/Log.php' );
		require_once( AIRTABLE_CONNECT_PATH . '/src/LogEntry.php' );
		require_once( AIRTABLE_CONNECT_PATH . '/src/Template.php' );
		require_once( AIRTABLE_CONNECT_PATH . '/integrations/events-manager/BookingRegistration.php' );
		require_once( AIRTABLE_CONNECT_PATH . '/integrations/learndash/QuizResults.php' );

	}

}
