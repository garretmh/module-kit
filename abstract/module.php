<?php
if ( ! class_exists( 'Plugin_Suite_Module', false ) ) {

	abstract class Plugin_Suite_Module extends Plugin_Suite_Abstract {

		private function __construct() {
			parent::__construct();
			add_action( 'init', array( $this, "begin" ) );
		}

	}

}
