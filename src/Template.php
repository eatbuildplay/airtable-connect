<?php

namespace AirtableConnect;

class Template {

	public function render( $templateName ) {


		require( AIRTABLE_CONNECT_PATH . '/templates/' . $templateName . '.php' );

	}


}
