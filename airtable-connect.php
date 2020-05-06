<?php

/**
 * Plugin Name:			Airtable Connect
 * Plugin URI:			http://eatbuildplay.com
 * Description:			Connect your WP data by exporting to Airtable.
 * Version:					1.0.1
 * Author:					Casey Milne, Eat/Build/Play
 * Author URI:			http://eatbuildplay.com
 *
 * Text Domain: airtable-connect
 * Domain Path: /languages/
 *
 */

namespace AirtableConnect;

define('AIRTABLE_CONNECT_PATH', plugin_dir_path( __FILE__ ));
define('AIRTABLE_CONNECT_URL', plugin_dir_url( __FILE__ ));
define('AIRTABLE_CONNECT_VERSION', '1.0.0');

class AirtableConnectPlugin {

	public function __construct() {

		require_once( AIRTABLE_CONNECT_PATH . '/src/Loader.php' );
		$loader = new Loader;
		$loader->includeFiles();

		add_action('admin_init', array($this, 'adminInit'), 10);
		add_action('admin_menu', array($this, 'adminSetupPages'), 10);

		$bookingRegistration = new \AirtableConnect\BookingRegistration;
		$bookingRegistration->hookBookingSaved();

		$quizResults = new QuizResults;
		$quizResults->hookLearnDash();

  }

	public function adminInit() {
		$admin = new \AirtableConnect\Admin;
		$admin->init();
	}

	public function adminSetupPages() {
		$admin = new \AirtableConnect\Admin;
		$admin->addPages();
	}

}

new AirtableConnectPlugin();
