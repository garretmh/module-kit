<?php
/*
Plugin Name: Module Kit
Description: A WordPress Plugin for projects to combine disparate functions
Version: 0.9.0
Author: Garret McGraw-Hanson
Author URI: http://garretmh.github.com
*/

if ( ! class_exists( 'Module_Kit', false ) ) {
	/**
	 * Handles checking for and loading the Module Kit and its dependencies
	 */
	class Module_Kit {
		/**
		 * The name of this class
		 *
		 * @var string
		 */
		public static $name = __CLASS__;

		/**
		 * Single instance of the Module_Kit object
		 *
		 * @var Module_Kit
		 */
		public static $single_instance = null;

		/**
		 * Creates/returns the single instance Module_Kit object
		 *
		 * @return Module_Kit Single instance object
		 */
		public static function initiate() {
			if ( null === self::$single_instance ) {
				self::$single_instance = new self();
			}
			return self::$single_instance;
		}

		/**
		 * Start the plugin
		 *
		 * Sets up globals and plugin hooks
		 */
		private function __construct() {
			# Globally define the plugin directory path
			$dirname = __CLASS__ . '_DIR';
			if ( ! defined( $dirname ) ) {
				define( $dirname, trailingslashit( dirname( __FILE__ ) ) );
			}

			# Setup hooks & shortcodes
			add_action( 'init', [__CLASS__, 'bootstrap'], 20 );

			# Initilize
			# Initialization Complete.
		}

		/**
		 * Module Kit bootstrap proccess
		 */
		public static function bootstrap() {
			if ( is_admin() ) {
				/**
				 * Fires on the admin side when the Module Kit is included/loaded.
				 */
				do_action( 'mk_admin_init' );
			}

			/**
			 * Fires when the Module Kit is included/loaded
			 *
			 * Can be used to add modules
			 */
			do_action( 'mk_init' );

			/**
			 * Fires after the Module Kit initiation process has been completed
			 */
			do_action( 'mk_after_init' );
		}
	}
}

Module_Kit::initiate();
