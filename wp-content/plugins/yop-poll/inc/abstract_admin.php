<?php
	abstract class YOP_POLL_Abstract_Admin {
		protected $twig	= NULL;
		protected $type	= NULL;

		protected function __construct( $type ) {
			$this->type	= $type . '_';
			$this->loadTwig();
		}

		private function loadTwig() {
			$loader		= new Twig_Loader_Filesystem( YOP_POLL_PATH . 'templates/');
			$this->twig	= new Twig_Environment( $loader, array() );
			$this->twig->addExtension(new Twig_Extension_YopPoll() );
		}

		protected function make_order_array( $fields = array(), $default_order = 'asc', $orderby = NULL, $order = 'asc' ) {
			$return_array	= array();
			if ( is_foreach_array( $fields ) ) {
				foreach( $fields as $field ) {
					if ( $field == $orderby )
						$return_array[ $field ]	= $order;
					else
						$return_array[ $field ]	= $default_order;
				}
			}
			return $return_array;
		}

		protected function render( $template, $data ) {
			return $this->twig->render( $this->type . $template, $data );
		}

		protected function display( $template, $data ) {
			$this->twig->display( $this->type . $template, $data );
		}

		public function get_new_answer_template( $args = array() ) {

			$question_id		= ( isset( $args['question_id'] ) && $args['question_id'] != '' ) ? $args['question_id'] : uniqid( 'q_' );
			$answer_id			= ( isset( $args['answer_id'] ) && $args['answer_id'] != '' ) ? $args['answer_id'] : uniqid( 'a_' );

			$questionObj	= new YOP_POLL_Question_Model( $question_id );
			$answerObj		= new YOP_POLL_Answer_Model( $answer_id );
			if ( ! isset( $questionObj->ID ) ) {
				$questionObj->ID	= $question_id;
			}

			$data['question'] = $questionObj;

			if ( ! isset( $answerObj->ID ) ) {
				$answerObj->ID	= $answer_id;
			}

			$data['answer']		= $answerObj;
            if($_POST['type']=='text')
			    $this->display( 'answer_template.html', $data );
			   else
			     $this->display( 'answer_media_template.html', $data );
		}

		public function get_new_question_template( $args = array() ) {

			$question_id		= ( isset( $args['question_id'] ) && $args['question_id'] != '' ) ? $args['question_id'] : uniqid( 'q_' );

			$questionObj	= new YOP_POLL_Question_Model( $question_id );
			if ( ! isset( $questionObj->ID ) ) {
				$questionObj->ID	= $question_id;
			}

			$data['question'] = $questionObj;

           if($_POST['type']=="text")
		    	$this->display( 'question_template.html', $data );
            else
                $this->display('question_template_media.html',$data);

        }
}