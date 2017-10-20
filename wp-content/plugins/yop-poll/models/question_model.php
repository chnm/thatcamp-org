<?php

	Class YOP_POLL_Question_Model{

		protected $data = NULL;

		protected $options = NULL;

		protected $answers = NULL;

		protected $ID = NULL;

		protected $custom_fields = NULL;

		private $default_fields = array( 'ID', 'poll_id', 'question','type', 'question_author', 'question_date', 'question_status', 'question_modified', 'poll_order' );


		function __construct( $id = 0, $answer_sort = "question_order", $answer_sort_rule = "ASC" ) {

			if ( $id instanceof YOP_POLL_Question_Model ){

				$this->init( $id->data, $answer_sort, $answer_sort_rule );

				return;

			}
			elseif ( is_object( $id ) ) {

				$this->init( $id, $answer_sort, $answer_sort_rule );

				return;

			}


			if ( !empty( $id ) && !is_numeric( $id ) ){

				$id = 0;

			}


			$data = self::get_data_by( 'id', $id );

			if ( $data ){
				$this->init( $data, $answer_sort, $answer_sort_rule );
			}

			else {
				$this->default_init();
			}

		}

		function init( $data, $answer_sort, $answer_sort_rule ) {
			$this->data = $data;

			$this->ID = (int)$data->ID;

			$this->init_options();

			$this->answers = array();

			$answers_ids = $this->load_answers_ids( $answer_sort, $answer_sort_rule);

			if ( $answers_ids && count( $answers_ids ) > 0 ){
				foreach ( $answers_ids as $answer_id ) {
					$new_answer = new YOP_POLL_Answer_Model( $answer_id );
					if ( $new_answer ){
						$this->answers[] = $new_answer;
					}
				}
			}

			$custom_fields_ids = $this->load_custom_fields_ids();

			if ( $custom_fields_ids && count( $custom_fields_ids ) > 0 ){
				foreach ( $custom_fields_ids as $custom_fields_id ) {
					$new_custom_field = new YOP_POLL_Custom_Field_Model( $custom_fields_id );
					if ( $new_custom_field ){
						$this->custom_fields[] = $new_custom_field;
					}
				}
			}
		}

		function default_init() {
			$this->ID = NULL;
            $data = new stdClass();

            $data->ID=NULL;
            $data->question=NULL;
            $this->data=$data;

            $this->init_options();

			$this->answers = array();

			$this->custom_fields = array();
		}

		function init_options() {

			$this->options = array();

			$default_options = get_option( 'yop_poll_options' );

			if ( $default_options && count( $default_options ) > 0 ){

				foreach ( $default_options as $option_name => $option_value ) {

					$this->options[$option_name] = $this->get_option( $option_name );

				}

			}

		}

		function _unset() {

			$this->data = NULL;

			$this->ID = NULL;

			$this->answers = NULL;

		}

		static function get_data_by( $field, $value ) {

			if ( 'id' == $field ){

				if ( !is_numeric( $value ) ){
					return false;
				}

				$value = intval( $value );

				if ( $value < 1 ){
					return false;
				}

			}

			else {

				$value = trim( $value );

			}


			if ( !$value ){
				return false;
			}


			switch ( $field ) {

				case 'id':

					$question_id = $value;

					$db_field = 'ID';

					break;

				default:

					return false;

			}


			if ( false !== $question_id ){

				if ( $question = wp_cache_get( $question_id, 'yop_poll_question' ) ){
					return $question;
				}

			}


			if ( !$question = $GLOBALS['wpdb']->get_row( $GLOBALS['wpdb']->prepare(

						"SELECT * FROM {$GLOBALS['wpdb']->yop_poll_questions} WHERE $db_field = %s", $value

					) )
			){
				return false;
			}
			$question->question = stripslashes( $question->question );


			wp_cache_add( $question->ID, $question, 'yop_poll_question' );


			return $question;

		}

		function __isset( $key ) {

			if ( 'id' == strtolower( $key ) ){

				$key = 'ID';

			}


			if ( isset( $this->$key ) ){
				return true;
			}


			if ( isset( $this->data->$key ) ){
				return true;
			}


			//return metadata_exists( 'yop_poll_question', $this->ID, $key );

			return $this->isset_option( $key );

		}

		function isset_option( $key ) {


			if ( in_array( $key, $this->default_fields ) ) //this is not an option

			{
				return false;
			}


			if ( isset( $this->options[$key] ) ){
				return true;
			}


			$question_options = get_yop_poll_question_meta( $this->id, 'options', true );

			if ( isset( $question_options[$key] ) ){
				return true;
			}


			$poll_options = get_yop_poll_meta( $this->poll_id, 'options', true );

			if ( isset( $poll_options[$key] ) ){
				return true;
			}


			$default_options = get_option( 'yop_poll_options' );

			if ( isset( $default_options[$key] ) ){
				return true;
			}

			return false;

		}

		function __get( $key ) {
			$value = NULL;
			if ( 'id' == strtolower( $key ) ){
				return $this->ID;
			}

			if ( 'answers' == $key ){
				return $this->answers;
			}

			if ( 'custom_fields' == $key ){
				return $this->custom_fields;
			}

			if ( isset( $this->data->$key ) ){
				$value = $this->data->$key;
			}
			else {
				//$value = get_yop_poll_question_meta( $this->ID, $key, true );
				$value = $this->get_option( $key );
			}

			return $value;

		}

		function get_option( $key ) {

			if ( in_array( $key, $this->default_fields ) ) //this is not an option

			{
				return false;
			}

			if ( isset( $this->options[$key] ) ){
				return $this->options[$key];
			}

			$question_options = get_yop_poll_question_meta( $this->id, 'options', true );

			if ( isset( $question_options[$key] ) ){
				return $question_options[$key];
			}

			$poll_options = get_yop_poll_meta( $this->poll_id, 'options', true );

			if ( isset( $poll_options[$key] ) ){
				return $poll_options[$key];
			}

			$default_options = get_option( 'yop_poll_options' );

			if ( isset( $default_options[$key] ) ){
				return $default_options[$key];
			}

			return false;

		}

		function __set( $key, $value ) {

			if ( 'id' == strtolower( $key ) ){
				$this->ID       = $value;
				$this->data->ID = $value;
				return;
			}

			if ( 'answers' == $key ){
				$this->answers = $value;
				return;
			}

			if ( 'custom_fields' == $key ){
				$this->custom_fields = $value;
				return;
			}


			if ( in_array( $key, $this->default_fields ) ) //this is not an option
			{
				$this->data->$key = $value;
			}
			else {
				$this->_set_option( $key, $value );
			}
		}

		function _set_option( $key, $value ) {

			$this->options[$key] = $value;

		}

		function exists() {

			return !empty( $this->ID );

		}

		function get( $key ) {
			return $this->__get( $key );
		}

		function has_prop( $key ) {

			return $this->__isset( $key );

		}

		function to_array() {

			return get_object_vars( $this->data );

		}

		function load_answers_ids( $orderby = "question_order", $orderRule = "ASC" ) {
			if ( !$answers = $GLOBALS['wpdb']->get_col(
					$GLOBALS['wpdb']->prepare(
						"SELECT
						ID
						FROM
						{$GLOBALS['wpdb']->yop_poll_answers}
						WHERE
						question_id = %s
						ORDER BY " . $orderby . " " . $orderRule,
						$this->id
					) )
			){
				return false;
			}

			return $answers;

		}

		function load_custom_fields_ids() {
			if ( !$custom_fields = $GLOBALS['wpdb']->get_col(
					$GLOBALS['wpdb']->prepare(
						"SELECT
						ID
						FROM
						{$GLOBALS['wpdb']->yop_poll_custom_fields}
						WHERE
						question_id = %s",
						$this->id
					) )
			){
				return false;
			}
			return $custom_fields;
		}

		function save() {
			if ( $this->exists() ){ // update
				$this->update();
			}
			else { //insert
				$this->insert();
			}

		}

		private function save_options() {

			$question_options = get_yop_poll_question_meta( $this->ID, 'options', true );

			if ( $this->options && count( $this->options ) > 0 ){

				foreach ( $this->options as $option_name => $option_value ) {

					$question_options[$option_name] = $option_value;

				}

			}


			update_yop_poll_question_meta( $this->id, 'options', $question_options );

		}

		private function insert() {
			$this->question = sanitize_text_field( $this->question );
			$GLOBALS['wpdb']->query( $GLOBALS['wpdb']->prepare( "
					INSERT INTO " . $GLOBALS['wpdb']->yop_poll_questions . "
					SET
					poll_id					= %d,
					question				= %s,
					type                    =%s,
					question_author			= %d,
					question_date			= %s,
					question_status			= %s,
					question_modified		= %s,
					poll_order				= %s
					",
					$this->poll_id,
					$this->question,
					$this->type,
					$this->question_author,
					$this->question_date,
					$this->question_status,
					$this->question_modified,
					$this->poll_order
				)
			);

			$this->id = $GLOBALS['wpdb']->insert_id;

			$this->save_options();

			if ( intval( $this->id ) > 0 ){
				$this->save_answers();
				$this->save_custom_fields();
				wp_cache_delete( $this->id, 'yop_poll_question' );
				return $this->id;
			}

			return false;

		}

		private function update() {
			$this->question = sanitize_text_field( $this->question );
			$GLOBALS['wpdb']->query(
				$GLOBALS['wpdb']->prepare( "
					UPDATE " . $GLOBALS['wpdb']->yop_poll_questions . "
					SET
					question				= %s,
					question_status			= %s,
					question_modified		= %s,
					poll_order				= %s
					WHERE
					ID						= %d
					",
					$this->question,
					$this->question_status,
					$this->question_modified,
					$this->poll_order,
					$this->id
				)
			);

			$this->save_options();

			$this->save_answers();

			$this->save_custom_fields();

			wp_cache_delete( $this->id, 'yop_poll_question' );

			return $this->id;

		}

		function delete() {


			if ( count( $this->answers ) > 0 ){

				foreach ( $this->answers as $answer ) {
					$answer->delete();
				}

			}


			delete_yop_poll_question_meta( $this->id, 'options' );


			$GLOBALS['wpdb']->query(

				$GLOBALS['wpdb']->prepare(

					"

					DELETE FROM " . $GLOBALS['wpdb']->yop_poll_questions . "

					WHERE ID = %d

					",

					$this->id

				)

			);

			wp_cache_delete( $this->id, 'yop_poll_question' );

			$this->_unset();

		}

		function save_answers() {
			$current_answers_ids = array();
			if ( is_array( $this->answers ) && count( $this->answers ) > 0 ){
				foreach ( $this->answers as $answer ) {
					$answer->poll_id     = $this->poll_id;
					$answer->question_id = $this->id;
					$answer->save();
					$current_answers_ids[] = $answer->id;
				}

			}
			$this->remove_deleted_answers( $current_answers_ids );
		}

		function save_custom_fields() {
			$current_custom_fields_ids = array();
			if ( is_array( $this->custom_fields ) && count( $this->custom_fields ) > 0 ){
				foreach ( $this->custom_fields as $custom_field ) {
					$custom_field->poll_id     = $this->poll_id;
					$custom_field->question_id = $this->id;
					$custom_field->save();
					$current_custom_fields_ids[] = $custom_field->id;
				}
			}
			$this->remove_deleted_custom_fields( $current_custom_fields_ids );
		}

		function remove_deleted_answers( $current_answers_ids = array() ) {
			if ( count( $current_answers_ids ) == 0 ){
				$current_answers_ids[] = 0;
			}
			$answers_for_delete = $GLOBALS['wpdb']->get_col(
                $GLOBALS['wpdb']->prepare( " SELECT ID FROM " . $GLOBALS['wpdb']->yop_poll_answers . " WHERE ID NOT IN ( " . implode( ',', $current_answers_ids ) . " ) AND question_id = %d  AND type != %s", $this->id,"other" )
			);
			if ( $answers_for_delete ){
				foreach ( $answers_for_delete as $answer_id ) {
					$answer = new YOP_POLL_Answer_Model( $answer_id );
					$answer->delete();
				}
			}
		}

		function remove_deleted_custom_fields( $current_custom_fields_ids = array() ) {
			if ( count( $current_custom_fields_ids ) == 0 ){
				$current_custom_fields_ids[] = 0;
			}
			$custom_fields_for_delete = $GLOBALS['wpdb']->get_col(
				$GLOBALS['wpdb']->prepare( " SELECT ID FROM " . $GLOBALS['wpdb']->yop_poll_custom_fields . " WHERE ID NOT IN ( " . implode( ',', $current_custom_fields_ids ) . " ) AND question_id = %d", $this->id )
			);
			if ( $custom_fields_for_delete ){
				foreach ( $custom_fields_for_delete as $custom_field_id ) {
					$custom_field = new YOP_POLL_Custom_Field_Model( $custom_field_id );
					$custom_field->delete();
				}
			}
		}

		function addAnswer( &$answer ) {
			$this->answers[] = $answer;
		}

		function countanswers( $types = array( 'text', 'other' ), $include_others = true ) {
			$ans_no = 0;
			$other_answers_no = 0;
			foreach( $this->answers as $answer ) {
				if( in_array( $answer->type, $types ) ) {
					if( $answer->type == 'other' ) {
						if( $include_others ) { $other_answers_no++; }
					}
					else {
						$ans_no++;
					}
				}
			}
			return $ans_no + $other_answers_no;
		}

		function countcustomfields() {
			return count( $this->custom_fields );
		}

		function &getAnswerById( $id ) {
			$ret = false;
			foreach( $this->answers as $answer ) {
				if( $answer->ID == $id ) {
					return $answer;
				}
			}
			return $ret;
		}

		function &getCustomFieldById( $id ) {
			$ret = false;
			foreach( $this->custom_fields as $custom_field ) {
				if( $custom_field->ID == $id )
					return $custom_field;
			}
			return $ret;
		}

		function sortAnswers( $rule = "question_order", $direction = "asc" ) {
			usort( $this->answers, function( $a1, $a2 ) use ($rule, $direction){
					switch( strtolower($rule) ) {
						case 'question_order' : {
							return ( intval($a1->$rule) - intval( $a2->$rule ) );
							break;
						}
						case 'alphabetical' : {
							if( 'asc' == strtolower( $direction ) ) {
								return strcmp( $a1->answer, $a2->answer );
							}
							else {
								return strcmp( $a2->answer, $a1->answer );
							}
							break;
						}
						case 'votes' :
						default :
						{
							if( 'asc' == strtolower( $direction ) ) {
								return ( intval($a1->$rule) - intval( $a2->$rule ) );
							}
							else {
								return ( intval($a2->$rule) - intval( $a1->$rule ) );
							}
							break;
						}
					}
			});
		}
}
