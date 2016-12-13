<?php
/*
Plugin Name: Module Kit
Description: A WordPress Plugin for projects to combine disparate functions
Version: 0.9.0
Author: Garret McGraw-Hanson
Author URI: http://garretmh.github.com
*/

if ( ! class_exists( 'MK_Bootstrap', false ) ) {
	/**
	 * Handles checking for and loading the Module Kit and its dependencies
	 */
	class MK_Bootstrap {
		/**
		 * The name of this class
		 *
		 * @var string
		 */
		public static $name = __CLASS__;

		/**
		 * Single instance of the MK_Bootstrap object
		 *
		 * @var MK_Bootstrap
		 */
		public static $single_instance = null;

		/**
		 * Creates/returns the single instance MK_Bootstrap object
		 *
		 * @return MK_Bootstrap Single instance object
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
			# Setup hooks & shortcodes
			add_action( 'init', [__CLASS__, 'bootstrap'], 20 );

			# Initilize
			require_once 'inc/CMB2/init.php';
			include_once 'inc/class-template-loader.php';
			include_once 'inc/class-options.php';
			require_once 'inc/class-module.php';
			require_once 'module.php';
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
	} # End class.
}

MK_Bootstrap::initiate();
