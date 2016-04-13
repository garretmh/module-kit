<?php
/**
 * Abstract Class for Plugin_Suite
 *
 * stuff
 */
if ( ! class_exists( 'Plugin_Suite_Abstract', false ) ) {

	abstract class Plugin_Suite_Abstract {

		/**
		 * Name of Plugin_Suite_Abstract
		 *
		 * @var Name (requred)
		 */
		public static $name = '';

		/**
		 *
		 */
		public static $id = NULL;
		public static $slug = NULL;

		/**
		 * Single instance of the Plugin_Suite_Abstract object
		 *
		 * @var Plugin_Suite_Abstract
		 */
		public static $single_instance = null;

		/**
		 * Creates/returns the single instance Plugin_Suite_Abstract object
		 *
		 * @since  2.0.0
		 * @return Plugin_Suite_Abstract Single instance object
		 */
		public static function initiate() {
			if ( null === static::$single_instance ) {
				static::$single_instance = new static();
			}
			return static::$single_instance;
		}

		/**
		 * Create Plugin_Suite_Abstract_LOADED definition for early detection by other scripts
		 */
		private function __construct() {
			# Assign class varialbes
			if ( null === static::$slug ) {
				static::$slug = sanitize_title( static::$name );
			}
			if ( null === static::$id ) {
				static::$id = str_replace( '-', '_', static::$slug );
			}
			if ( null === static::$plugin_dir ) {
				static::$id = plugin_dir_path( __FILE__ ); // NOTE need to test how this works
			}
			# Create definition for early detection by other scripts
			$loaded_global = strtoupper( static::$id ) . '_LOADED';
			if ( ! defined( $loaded_global ) ) {
				define( $loaded_global, true );
			}
		}

	}

}
