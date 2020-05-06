<?php

namespace AirtableConnect;

class QuizResults {

	public function hookLearnDash() {

		add_action('learndash_quiz_completed', array( '\AirtableConnect\QuizResults', 'hookLearnDashQuizCompleted'), 10, 2 );
		add_action('learndash_essay_all_quiz_data_updated', array( '\AirtableConnect\QuizResults', 'hookLearnDashEssayGraded'), 10, 4 );


	}

	public function hookLearnDashEssayGraded( $quiz_id, $question_id, $updated_scoring_data, $essay_post ) {

		$points = $updated_scoring_data['updated_question_score'];
		$essayId = $essay_post->ID;
		if( $points ) {
			$correct = true;
		}

		$airtableRecordId = get_post_meta( $essayId, 'airtable_record_id', true );

		$airtableApi = new \AirtableConnect\AirtableApi;
		$args = array(
			'records' => array(
				array(
					'id' => $airtableRecordId,
					'fields' => array(
						'Points' => $points,
						'Graded' => true,
						'Correct' => $correct
					)
				)
			)
		);

		$response = $airtableApi->call( '/apphyZq0vvoujFIQv/Question%20Stats', 'patch', $args );
		return $response;

	}

	// send quiz stat to airtable
	public function quizStatSend( $postTitle, $username, $date, $statRefId ) {

		$airtableApi = new \AirtableConnect\AirtableApi;
		$args = array(
			'fields' => array(
				'Quiz' => $postTitle,
				'User' => $username,
				'Date' => $date,
				'Stat Ref ID' => $statRefId
			)
		);
		$response = $airtableApi->call( '/apphyZq0vvoujFIQv/Quiz%20Stats', 'post', $args );
		return $response;

	}

	// send stat to airtable
	public function questionStatSend( $obj ) {

		$airtableApi = new \AirtableConnect\AirtableApi;
		$args = array(
			'fields' => array(
				'ID' => $obj->id,
				'Question' => $obj->question,
				'Answer' => $obj->answer,
				'Question ID' => $obj->questionId,
				'Correct' => $obj->correct,
				'Points' => $obj->points,
				'Graded' => $obj->graded,
				'Essay ID' => $obj->essayId,
				'Essay Edit' => $obj->essayEdit,
				'Quiz Stat' => [$obj->quizStatRecordId]
			)
		);
		$response = $airtableApi->call( '/apphyZq0vvoujFIQv/Question%20Stats', 'post', $args );

		return $response;

	}

	public function getStatRef( $statRefId ) {

		global $wpdb;
		$mapper = new \WpProQuiz_Model_Mapper;
		$results = $wpdb->get_results(
			$wpdb->prepare('
			SELECT * FROM
				'. $wpdb->prefix . 'wp_pro_quiz_statistic_ref' .' AS sf
			WHERE
				sf.statistic_ref_id = %d
			', $statRefId )
		, ARRAY_A);
		if( empty( $results )) {
			return false;
		}
		return new \WpProQuiz_Model_StatisticRefModel( $results[0] );

	}

	public function formatTimeForAirtable( $timestamp ) {
		return date('Y-m-dTH:i:s.uZ');
	}

	public static function hookLearnDashQuizCompleted( $quizdata, $current_user ) {

		$quizResults = new \AirtableConnect\QuizResults;

		// add learn dash quiz data
		$airtableConnectorData = new \stdClass;
		$airtableConnectorData->learnDashQuizData = $quizdata;

		// add stat ref
		$statRefId = $quizdata["statistic_ref_id"];
		$airtableConnectorData->statRef = $quizResults->getStatRef( $statRefId );

		// add stats
		$statMapper = new \WpProQuiz_Model_StatisticMapper();
		$airtableConnectorData->stats = $statMapper->fetchAllByRef( $statRefId );

		$postTitle = $airtableConnectorData->learnDashQuizData['quiz']->post_title;

		// get user name
		$user = get_user_by( 'id', $airtableConnectorData->statRef->getUserId() );

		// create time
		$createdTime = $airtableConnectorData->statRef->getCreateTime();
		$formattedDate = $quizResults->formatTimeForAirtable( $createdTime );

		// send quiz stat to airtable
		$quizResponse = $quizResults->quizStatSend(
			$postTitle,
			$user->data->user_login,
			$formattedDate,
			$statRefId
		);

		// send question stats to airtable
		$questionResponses = [];
		$questionNumber = 1;

		foreach( $airtableConnectorData->stats as $questionStat ) {

			$obj = new \stdClass;

			// record ID from Quiz Stats table for link field
			$obj->quizStatRecordId = $quizResponse->data->id;

			$obj->id = 'Question #' . $questionNumber . ', Stat #' . $questionStat->getStatisticRefId();

			$obj->correct = false;
			if( $questionStat->getCorrectCount() ) {
				$obj->correct = true;
			}

			$obj->points = $questionStat->getPoints();

			// load question object
			$obj->questionId = $questionStat->getQuestionId();
			$questionMapper = new \WpProQuiz_Model_QuestionMapper;
			$question = $questionMapper->fetch( $obj->questionId );

			$answerData = $questionStat->getAnswerData();
			$hasEssay = $quizResults->questionStatHasEssay( $answerData );

			if( $hasEssay ) {

				$obj->graded = false;
				$obj->essayId = $quizResults->questionStatExtractEssayId( $answerData );
				$obj->essayEdit = get_site_url() . '/wp-admin/post.php?post=' . $obj->essayId . '&action=edit';
				$obj->answer = '';

			} else {

				$obj->graded = true;
				$obj->essayId = null;
				$obj->essayEdit = '';

				// there was an answer selection
				$questionAnswerData = $question->getAnswerData();
				$obj->answer = $quizResults->questionStatExtractAnswer( $questionAnswerData, $answerData );

			}

			$obj->question = $question->getQuestion();
			$response = $quizResults->questionStatSend( $obj );

			if( $hasEssay ) {
				update_post_meta( $obj->essayId, 'airtable_record_id', $response->data->id );
			}

			$questionResponses[] = $response;
			$questionNumber++;

		}

	}

	/*
	 * $questionAnswerData (array AnswerTypes)
	 * $answerData [0,1,0]
	 */
	public function questionStatExtractAnswer( $questionAnswerData, $answerData ) {

		// identify from json $answerData which answer user chose
		$userAnswerArray = json_decode( $answerData );
		$userAnswerSelectionKey = array_search( 1, $userAnswerArray );
		$answerChosen = $questionAnswerData[ $userAnswerSelectionKey ];
		$answer = $answerChosen->getAnswer();

		return $answer;


	}

	public function questionStatExtractEssayId( $answerData ) {
		$json = json_decode( $answerData );
		return $json->graded_id;
	}

	public function questionStatHasEssay( $answerData ) {

		$match = strpos( $answerData, 'graded_id' );
		if( $match !== false ) {
			return true;
		}
		return false;

	}

}
