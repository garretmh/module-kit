<?php
require_once 'class-template-loader.php';
require_once 'class-module-options.php';

if ( ! class_exists( 'MK_Module', false ) ) {
	/**
	 * MK Module abstract
	 *
	 * Template module for building actual Module Kit modules
	 *
	 * @author   Garret McGraw-Hanson
	 * @version  0.9.0
	 *
	 * @property string             $key                   Module underscored key (ie mk_example)
	 * @property string             $path                  Path to this class from plugin root
	 * @property string             $title                 Module display title (ie MK Example)
	 * @property MK_Module_Options  $options_page          Module's WP options page ID
	 *
	 * @abstract void               run()                  What to do when the module runs (ie extends the initiate fn)
	 * @abstract void               options_page_metabox() Register the module's options page metabox
	 *
	 * @static   class_object       initiate()             Creates/returns instance(s) of the MK_Module object
	 *
	 * @method   mixed              get_option( string )                        Get CMB2 options from Module's options page
	 * @method   MK_Template_Loader get_template_loader()                       Get/create an instance of the MK Template Loader for this module
	 * @method   string             get_template_part( string, string, bool)    Retrieve a template part
	 * @method   bool               is_enabled()                                Check whether this module is active
	 * @method   string             locate_template( string|array, bool, bool ) Retrieve the name of the highest priority template file that exists
	 * @method   void               register_options_page( array )              Create/Get a module options page
	 *
	 */
	abstract class MK_Module {

		/**
		 * Module key, and module page slug
		 * @var string
		 */
		protected $key = '';

		/**
		 * Module title
		 * @var string
		 */
		protected $title = '';

		/**
		 * Module dir path relative to Plugin
		 * @var string
		 */
		protected $path = '';

		/**
		 * Module dir path relative to Plugin
		 * @var MK_Module_Options
		 */
		protected $options_page = null;

		/**
		 * Single instance of the MK_Module object
		 *
		 * @var MK_Module
		 */
		private static $instances = array();

		/**
		 * Run the module
		 */
		abstract protected function run();

		/**
		 * Register a metabox for the Module's options page
		 *
		 * @see  MK_Module::register_options
		 * @uses CMB2
		 */
		abstract public function options_page_metabox();

		/**
		 * Creates/returns instance(s) of the MK_Module object
		 *
		 * @return MK_Module Single instance object
		 */
		public static function initiate() {
			$class = get_called_class();
			if ( __CLASS__ == $class ) {
				return self::$instances;
			}

			if(!isset(self::$instances[$class])) {
				self::$instances[$class] = new static();
				self::$instances[$class]->run();
			}
			return self::$instances[$class];
		}

		/**
		 * Start the Module
		 *
		 * Sets up properties and begins initial run function
		 */
		protected function __construct() {
			$class = get_called_class();

			// Set our title
			if ( empty( $this->title ) ) {
				$this->title = str_replace( array( '__', '_' ), array( ' - ', ' ' ), trim( $class, '_' ) );
			}
			$this->title = __( $this->title, 'MK' );

			// Set our key
			if ( empty( $this->key ) ) {
				$this->key = $class;
			}
			$this->key = sanitize_key($this->key);

			// Set our path
			if ( empty( $this->path ) ) {
				$plugin_dir = plugin_dir_path(dirname(__FILE__));
				$module_dir = plugin_dir_path((new ReflectionClass(static::class))->getFileName());
				if ( !empty($plugin_dir) && 0 === strpos( $module_dir, $plugin_dir ) ) {
					$this->path = substr( $module_dir, strlen($plugin_dir) ).'';
				}
			}
			# Initialization Complete.
		}

		/**
		 * Wrapper function around cmb2_get_option
		 *
		 * @param  string  $key Options array key
		 * @return mixed        Option value
		 */
		public function get_option( $key = '' ) {
			return cmb2_get_option( $this->key, $key );
		}

		/**
		 * Retrieve a template loader for the module
		 *
		 * @uses MK_Template_Loader
		 *
		 * @return MK_Template_Loader [description]
		 */
		public function get_template_loader() {
			if ( ! isset($this->template_loader) ) {
				$this->template_loader = new MK_Template_Loader($this->$path);
			}
			return $this->template_loader;
		}

		/**
		 * Retrieve a template part.
		 *
		 * @uses MK_Template_Loader
		 *
		 * @param string $slug Template slug.
		 * @param string $name Optional. Template variation name. Default null.
		 * @param bool   $load Optional. Whether to load template. Default true.
		 *
		 * @return string
		 */
		public function get_template_part( $slug, $name = null, $load = true ) {
			return $this->get_template_loader()->get_template_part( $slug, $name, $load );
		}

		/**
		 * Check if module is enabled
		 *
		 * @return bool True if the module is enabled, otherwise false
		 */
		public function is_enabled() {
			return (bool) mk_get_option($this->key);
		}

		/**
		 * Retrieve the name of the highest priority template file that exists.
		 *
		 * Searches in the STYLESHEETPATH before TEMPLATEPATH so that themes which
		 * inherit from a parent theme can just overload one file. If the template is
		 * not found in either of those, it looks in the theme-compat folder last.
		 *
		 * @uses MK_Template_Loader
		 *
		 * @param string|array $template_names Template file(s) to search for, in order.
		 * @param bool         $load           If true the template file will be loaded if it is found.
		 * @param bool         $require_once   Whether to require_once or require. Default true.
		 *                                     Has no effect if $load is false.
		 *
		 * @return string The template filename if one is located.
		 */
		public function locate_template( $template_names, $load = false, $require_once = true ) {
			return $this->get_template_loader()->locate_template( $template_names, $load, $require_once );
		}

		/**
		 * Create/Get a module options page
		 *
		 * Note: enabled_cb defaults to module's is_enabled fn
		 *
		 * @param  array  $args             Arguments
		 *
		 * @return MK_Module_Options
		 */
		public function register_options_page( $args = array() ) {
			if ( ! is_null( $this->options_page ) )
				return $this->options_page;

			// Default options page args
			$args = wp_parse_args( $args, array(
				'title' => $this->title,
				'key' => "{$this->key}_options",
				'metabox_id' => "{$this->key}_options_metabox",
				'metabox_cb' => array( $this, 'options_page_metabox' ),
				'enabled_cb' => array( $this, 'is_enabled' ),
			) );

			return $this->options_page = mk_mod_options( get_class($this), $args );
		}

		/**
		 * Public getter method for retrieving protected/private variables
		 * @param  string  $field Field to retrieve
		 * @return mixed          Field value or exception is thrown
		 */
		public function __get( $field ) {
			// Allowed fields to retrieve
			if ( in_array( $field, array( 'key', 'title', 'options_page', 'path' ), true ) ) {
				return $this->{$field};
			}

			throw new Exception( 'Invalid property: ' . $field );
		}
	}
}

function get_mk_modules() {
	return MK_Module::initiate();
}
