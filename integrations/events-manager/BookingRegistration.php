<?php

namespace AirtableConnect;

class BookingRegistration {

	public function hookBookingSaved() {

		add_filter('em_booking_save', array( '\AirtableConnect\BookingRegistration', 'bookingRegistrationSync' ), 10, 2 );

	}

	public static function bookingRegistrationSync( $result, $booking ) {

		$airtableApi = new \AirtableConnect\AirtableApi;

		// see ss EM_DateTime
		$startTime = $booking->event->start();
		$start = $startTime->format('Y-m-d');

		$args = array(
			'fields' => array(
				'Last Name' => $booking->booking_meta['registration']['last_name'],
				'First Name' => $booking->booking_meta['registration']['first_name'],
				'Event' => $booking->event->event_name,
				'Date of Workshop' => $start,
				'Name of School or Organization' => $booking->booking_meta['booking']['name_of_school_or_organization'],
				'E-mail' => $booking->booking_meta['registration']['user_email'],
				'I would like to sign up to receive: (you can unsubscribe at anytime)' => $booking->booking_meta['booking']['i_would_like_to_sign_up_to_receive_a__you_can_unsubscribe_at_anytime_'],
				'Phone' => $booking->booking_meta['registration']['dbem_phone'],
				'Title or Position' => $booking->booking_meta['booking']['title_or_position'],
				'Grade Level' => $booking->booking_meta['booking']['grade_level'],
				'How did you hear about our workshop?' => $booking->booking_meta['booking']['how_did_you_hear_about_our_workshop_f'],
				'Photo/Video Release' => true,
				'Comments' => $booking->booking_meta['booking']['comments'],
				'Payment Method' => $booking->booking_meta['booking']['payment_method'],
				'Purchase Order # (if applicable)' => $booking->booking_meta['booking']['purchase_order__']
			)
		);

		$response = $airtableApi->call( '/app51JFsOUKKqiw6Y/CONS%20WS%20Participants', 'post', $args );
		return $result;

	}


}
