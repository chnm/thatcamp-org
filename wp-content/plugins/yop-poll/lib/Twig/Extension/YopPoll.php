<?php
	class Twig_Extension_YopPoll extends Twig_Extension {
		public function getName() {
			return YOP_POLL_DOMAIN;
		}

		public function getFunctions() {
			return array(
				new Twig_SimpleFunction( '__', '__yop_poll' ),
				new Twig_SimpleFunction( 'wp_nonce_field', 'wp_nonce_field' ),
				new Twig_SimpleFunction( 'wp_nonce_url', 'wp_nonce_url' ),
				new Twig_SimpleFunction( 'selected', 'selected' ),
				new Twig_SimpleFunction( 'checked', 'checked' ),
				new Twig_SimpleFunction( 'esc_url', 'esc_url' ),
				new Twig_SimpleFunction( 'add_query_arg', 'add_query_arg' ),
				new Twig_SimpleFunction( 'current_user_can', 'current_user_can' ),
				new Twig_SimpleFunction( 'count', 'count' ),
				new Twig_SimpleFunction( 'uniqid', 'uniqid' ),
				new Twig_SimpleFunction( 'substr', 'substr' ),
				new Twig_SimpleFunction( 'new_obj', 'yop_poll_new_obj' ),
				new Twig_SimpleFunction( 'current_time', 'current_time' ),
				new Twig_SimpleFunction( 'stripslashes', 'stripslashes'),
				new Twig_SimpleFunction( 'settings_fields', 'settings_fields'),
				new Twig_SimpleFunction( 'dump', 'yop_poll_dump'),
				new Twig_SimpleFunction( 'print', 'print'),
				new Twig_SimpleFunction( 'isset', 'isset'),
                new Twig_SimpleFunction( 'wp_editor', 'wp_editor'),
                new Twig_SimpleFunction( 'array', 'array'),
                new Twig_SimpleFunction( 'get_userdata', 'get_userdata'),
                new Twig_SimpleFunction( 'esc_html', 'esc_html'),
                new Twig_SimpleFunction( 'intval', 'intval'),
                new Twig_SimpleFunction( 'ucfirst', 'ucfirst'),
                new Twig_SimpleFunction( 'explode', 'explode'),
                new Twig_SimpleFunction( 'list', 'list'),
                new Twig_SimpleFunction( 'is_array', 'is_array'),
                new Twig_SimpleFunction( 'in_array', 'in_array'),
                new Twig_SimpleFunction( 'convert_date', 'convert_date'),
                new Twig_SimpleFunction( 'strlen', 'strlen'),
                new Twig_SimpleFunction( 'is_ssl', 'is_ssl'),
                new Twig_SimpleFunction( 'admin_url', 'admin_url'),

            );
		}

		public function getFilters() {
			return array(

			);
		}

		public function getTokenParsers() {
			return array();
		}

		public function getOperators() {
			return array(
				array(
					'!' => array('precedence' => 50, 'class' => 'Twig_Node_Expression_Unary_Not'),
				),
				array(
					'||' => array( 'precedence' => 10, 'class' => 'Twig_Node_Expression_Binary_Or', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT ),
					'&&' => array( 'precedence' => 15, 'class' => 'Twig_Node_Expression_Binary_And', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT ),
				),
			);
		}
}