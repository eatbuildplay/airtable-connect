<?php

/*
 * Logging system

Example:

\AirtableConnect\Log::entry(
  'Logging Test',
  'It is just a test, do not freak out bitch.',
  ['frutas', 'non-frutas']
);

 */

namespace AirtableConnect;

class Log {

  public $entries   = [];
  public $optionKey = '_airtable_connect_log';

  public static function entry( $action, $code, $message, $data = [] ) {

    $log = new \AirtableConnect\Log;
    $result = $log->write( $action, $code, $message, $data );

  }

  public function write( $action, $code, $message, $data = [] ) {

    $entry = new LogEntry();
    $timestamp = time();
    $entry->create( $timestamp, $code, $action, $message, $data );

    $log = $this->read();
		if( !$log || !is_array( $log )) {
			$log = [];
		}
		$log[] = $entry;
    return update_option( $this->optionKey, $log );

  }

  public function read() {
    return get_option( $this->optionKey, false );
  }

  public function reset() {
    update_option( $this->optionKey, false );
  }

}
