<?php
/**
 * @package      WordPress Plugin Suite
 * @author       Garret McGraw-Hanson
 * @link         http://github.com/garretmh/plugin-suite
 * @license      GPL-2.0+
 *
 * Plugin Name:  Plugin Suite
 * Plugin URI:   https://github.com/garretmh/plugin-suite
 * Description:  Template for building plugins
 * Author:       Garret McGraw-Hanson
 * Author URI:   https://github.com/garretmh
 * Contributors: WebDevStudios (@webdevstudios / webdevstudios.com)
 *               Justin Sternberg (@jtsternberg / dsgnwrks.pro)
 *               Jared Atchison (@jaredatch / jaredatchison.com)
 *               Bill Erickson (@billerickson / billerickson.net)
 *               Andrew Norcross (@norcross / andrewnorcross.com)
 *
 * Version:      0.9
 *
 * Text Domain:  plugin-suite
 * Domain Path:  languages
 *
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

/************************************************************************
                  You should not edit the code below
                  (or any code in the included files)
                  or things might explode!
*************************************************************************/

if ( ! class_exists( $class, false ) ) {

	require_once plugin_dir_path( __FILE__ ) . 'abstract/abstract.php';

	class Plugin_Suite extends Plugin_Suite_Abstract {

		/**
		 * Name of Plugin_Suite
		 *
		 * @var name
		 */
		public static $name = 'Plugin Suite';

		/**
		 * Initiate Plugin_Suite
		 */
		private function __construct() {
			parent::__construct();
			# Record the plugin directory
			if ( ! defined( 'PLUGIN_SUITE_DIR' ) ) {
				define( 'PLUGIN_SUITE_DIR', plugin_dir_path( __FILE__ ) );
			}
			# Begin functions on init (NOTE: is this right?)
			add_action( 'init', array( $this, "begin" ) );
		}

		public function begin() {
			/**
			 * TODO:
			 * Setup options page
			 * Load modules
			 * do_action( "{$this->id}_init");
			 */
		}

	}

	// Make it so...
	Plugin_Suite::initiate();

}
