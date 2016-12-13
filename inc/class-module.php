<?php
if ( ! class_exists( 'Module_Kit', false ) ) {
	/**
	 * MK Module Kit
	 *
	 * Template module for building actual Module Kit modules
	 *
	 * @author   Garret McGraw-Hanson
	 * @version  0.10.0
	 *
	 * @property bool   $is_root                      overrideable  Whether the module is a Root Module or Submodule
	 * @property string $key                                        Identification key of the module (ie mk_example)
	 * @property object $options_page                               Module_Kit_Options object
	 * @property string $path                                       Path to this class from plugin root
	 * @property string $prefix                       overrideable  Module naming prefix
	 * @property string $title                        overrideable  Module display title (ie MK Example)
	 *
	 * @static   object get_instance()                              Returns instance(s) of Module_Kit objects
	 * @static   object initiate()                                  Creates/returns an instance of the particular Module_Kit object
	 *
	 * @method   void   activate()                    overrideable  What to do when the module runs (ie extends initiate())
	 * @method   bool   add_action()                                Hooks a function on to a specific action if the module is enabled.
	 * @method   bool   add_filter()                                Hook a function or method to a specific filter action if the module is enabled.
	 * @method   void   add_options_fields( object )  overrideable  Register the module's options page metabox
	 * @method   void   add_options_page( array )                   Create/Get a module options page
	 * @method   mixed  get_option( string )                        Get CMB2 options from Module's options page
	 * @method   object get_parent()                                Retrieve the parent module object if one exists.
	 * @method   object get_root()                                  Retrieve the root module object.
	 * @method   object get_template_loader()                       Get/create a template loader for the module
	 * @method   string get_template_part( string, string, bool )   Retrieve a template part
	 * @method   bool   is_enabled()                                Check whether this module is active
	 * @method   void   load_submodules()                           Load any submodules in the mod directory once.
	 * @method   string locate_template( mixed, bool, bool )        Retrieve the name of the highest priority template file that exists
	 * @method   object parent()                                    Alias for the get_parent method
	 */
	abstract class Module_Kit {

		/**
		 * Module naming prefix
		 * @internal Overrideable
		 *           Default: 'mk'
		 * @var string
		 */
		protected $prefix = 'mk';

		/**
		 * Module title
		 * @internal Overrideable
		 *           Default: ''
		 *           If not defined, defaults to the class name
		 * @var string
		 */
		protected $title = '';

		/**
		 * Single instances of Module_Kit child objects
		 * @internal Internal use only
		 * @var Module_Kit[]
		 */
		static private $instances = array();

		/**
		 * WordPress actions/filters that only run when module is active
		 * @var array
		 */
		private $filter = array();

		/**
		 * Whether the module is a Root Module or Submodule
		 * @internal Overrideable
		 *           Default: null
		 *           If true, module will initiate as a root module
		 * @var boolean
		 */
		private $is_root = null;

		/**
		 * Identification key of the module (ie mk_example)
		 * @var string
		 */
		protected $key = '';

		/**
		 * Submodules of our module
		 * @var array
		 */
		protected $modules = array();

		/**
		 * Options Page object
		 * @var MK_Module_Kit_Options object
		 */
		protected $options_page = null;

		/**
		 * Module dir path relative to Plugin
		 * @var string
		 */
		protected $path = '';

		/**
		 * Directory of our Root Module
		 * @var string
		 */
		protected $root_dir = '';

		/**
		 * Returns instance(s) of Module_Kit objects
		 *
		 * @return Module_Kit Single instance object
		 */
		static public function get_instance( $module = null ) {
			$instances = self::$instances;
			if ( is_array($module) ) {
				return array_intersect_key( $instances, array_flip( $module ) );
			}
			if ( ! $module ) $module = strtolower(static::class);
			return isset( $instances[$module]) ? $instances[$module] : false;
		}

		/**
		 * Creates/returns an instance of the particular Module_Kit object
		 *
		 * @return Module_Kit Single instance object
		 */
		static public function initiate() {
			$instance =& self::$instances[ strtolower(static::class) ];
			if(!isset($instance)) {
				$instance = new static();
				$instance->activate();
			}
			return $instance;
		}

		/**
		 * Start the Module
		 *
		 * Sets up properties and begins initial run function
		 */
		protected function __construct() {
			$class = static::class;
			$reflection = new ReflectionClass($class);
			$module_dir = plugin_dir_path($reflection->getFileName());
			$is_root = (property_exists( $class, 'is_root' )) ? $this->is_root : false;

			// Set our key
			$this->key = strtolower($class);

			// Set our title
			if ( empty( $this->title ) ) {
				$prefix = $this->prefix . '_';
				$title = (0 === stripos( $class, $prefix )) ? substr($class, strlen($prefix)) : $class;
				$this->title = str_replace( array( '__', '_' ), array( ' - ', ' ' ), trim( $title, '_' ) );
			}
			$this->title = __( $this->title, $this->prefix );

			// Register with our Parent
			if (
				! $is_root
				&& ($parent_id = strtolower(get_parent_class($class)))
				&& ($parent = static::get_instance($parent_id))
			) {
				$parent->add_submodule($this);
				$this->parent = $parent_id;
			} else {
				$this->parent = false;
			}

			// Set our root dir
			if ( $this->parent ) {
				$this->root_dir = $this->parent()->root_dir;
			} else {
				$this->root_dir = $module_dir;
			}

			// Set our path
			if ( empty( $this->path ) ) {
				$plugin_dir = $this->root_dir;
				if ( !empty($plugin_dir) && 0 === strpos( $module_dir, $plugin_dir ) ) {
					$this->path = substr( $module_dir, strlen($plugin_dir) ).'';
				}
			}
			// Initialization Complete.
		}

		/**
		 * Run the module
		 *
		 * Add hooks, register an options page, load submodules
		 * @internal Overrideable
		 */
		protected function activate() {
			if ( ! $this->parent ) {
				$this->add_options_page();
				$this->load_submodules();
			}
		}

		/**
		 * Hooks a function on to a specific action if the module is enabled.
		 *
		 * @param string   $tag             The name of the action to which the $function_to_add is hooked.
		 * @param callable $function_to_add The name of the function you wish to be called.
		 * @param int      $priority        Optional. Used to specify the order in which the functions
		 *                                  associated with a particular action are executed. Default 10.
		 *                                  Lower numbers correspond with earlier execution,
		 *                                  and functions with the same priority are executed
		 *                                  in the order in which they were added to the action.
		 * @param int      $accepted_args   Optional. The number of arguments the function accepts. Default 1.
		 * @return true Will always return true.
		 */
		public function add_action( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
			return $this->add_filter( $tag, $function_to_add, $priority, $accepted_args );
		}

		/**
		 * Hook a function or method to a specific filter action if the module is enabled.
		 *
		 * @param string   $tag             The name of the action to which the $function_to_add is hooked.
		 * @param callable $function_to_add The name of the function you wish to be called.
		 * @param int      $priority        Optional. Used to specify the order in which the functions
		 *                                  associated with a particular action are executed. Default 10.
		 *                                  Lower numbers correspond with earlier execution,
		 *                                  and functions with the same priority are executed
		 *                                  in the order in which they were added to the action.
		 * @param int      $accepted_args   Optional. The number of arguments the function accepts. Default 1.
		 * @return true
		 */
		public function add_filter( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
			if ( ! isset( $this->filter[$tag] ) ) {
				add_action( $tag, [$this, 'apply_filters'], -1, 1 );
			}
			$this->filter[$tag][] = [ $tag, $function_to_add, $priority, $accepted_args ];
			return true;
		}

		/**
		 * Register a metabox for the Module's options page
		 *
		 * @see  Module_Kit::register_options
		 * @uses CMB2
		 */
		public function add_options_fields( $cmb ) {
			// Create a checkbox for each submodule
			$modules = static::get_instance( $this->modules );
			foreach( $modules as $module ) {
				$cmb->add_field([
					'name' => __( $module->title, $this->prefix ),
					'id'   =>     $module->key,
					'type' => 'checkbox',
				]);
			}
		}

		/**
		 * Create/Get a module options page
		 *
		 * Note: enabled_cb defaults to module's is_enabled fn
		 * @param  array  $args             Arguments
		 * @return Module_Kit_Options
		 */
		public function add_options_page( $args = array() ) {
			if ( ! is_null( $this->options_page ) || ! class_exists('MK_Options') )
				return $this->options_page;

			// Default options page args
			$args = wp_parse_args( $args, array(
				'key' => $this->key,
				'title' => $this->title,
				'metabox_cb' => [$this, 'add_options_fields'],
				'metabox_id' => "{$this->key}_options_metabox",
			));
			// Default options subpage args
			if ( $this->parent && ! isset( $args['parent' ]) ) {
				$args = wp_parse_args( $args, array(
					'enabled_cb' => [$this, 'is_enabled'],
					'parent' => $this->get_root()->options_page,
				));
			}
			return $this->options_page = new MK_Options( $args['key'], $args['title'], $args['metabox_cb'], $args );
		}

		/**
		 * Register a submodule
		 * @param  Module_Kit object  $module The module to register
		 * @return string                        The module key registered
		 */
		public function add_submodule($submodule) {
			if ( $submodule instanceof static && ! in_array( $submodule->key, $this->modules ) ) {
				return $this->modules[] = $submodule->key;
			}
		}

		/**
		 * Apply module filters if the module is enabled.
		 *
		 * @param  Mixed $return In the case of a fitler, the value being filtered
		 * @return Mixed         Returns any filtered item that may be passed to it.
		 */
		public function apply_filters( $return = null ) {
			$filters = $this->filter[ current_filter() ];
			if ( $this->is_enabled() && isset( $filters ) ) {
				foreach ( $filters as $filter ) {
					add_filter( ...$filter );
				}
			}
			return $return;
		}

		/**
		 * Wrapper function around cmb2_get_option
		 *
		 * @uses CMB2
		 *
		 * @param  string  $key Options array key
		 * @return mixed        Option value
		 */
		public function get_option( $key = '' ) {
			if ( $options = $this->options_page ) {
				return cmb2_get_option( $options->key, $key );
			}
		}

		/**
		 * Retrieve the parent module object if one exists.
		 * @return Module_Kit object
		 */
		public function get_parent() {
			if ( $parent = $this->parent ) {
				return static::get_instance( $parent );
			}
			return $parent;
		}

		/**
		 * Retrieve the root module object.
		 * @return Module_Kit object
		 */
		public function get_root() {
			static $root;
			if ( is_null( $root ) ) {
				$root = $this->key;
				if ( $parent = $this->parent() ) {
					$root = $parent->get_root()->key;
				}
			}
			return static::get_instance($root);
		}

		/**
		 * Get/create a template loader for the module
		 *
		 * @uses MK_Template_Loader
		 *
		 * @return MK_Template_Loader [description]
		 */
		public function get_template_loader() {
			if ( ! isset($this->template_loader) ) {
				if ( class_exists('MK_Template_Loader') ) {
					$path = trim( $this->path, '/' );
					$theme_path = "templates-{$this->prefix}";
					//$theme_path .= str_replace( 'mod/', '', "/$path" ); // Break modules' theme template directories into sub-folders
					$args = array(
						'filter_prefix' => $this->prefix,
						'plugin_directory' => $this->root_dir,
						'theme_template_directory' => $theme_path,
						'plugin_template_directory' => "$path/templates",
					);
					$this->template_loader = new MK_Template_Loader($args);
				} else {
					$this->template_loader = false;
				}
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
			if ( $loader = $this->get_template_loader() ) {
				return $loader->get_template_part( $slug, $name, $load );
			}
			return $loader;
		}

		/**
		 * Check if module is enabled
		 *
		 * @return bool True if the module is enabled, otherwise false
		 */
		public function is_enabled() {
			static $is_enabled;
			if ( is_null( $is_enabled ) ) {
				$is_enabled = true;
				if ( ($parent = $this->parent()) && $parent->options_page ) {
					$is_enabled = $parent->is_enabled() && $parent->get_option( $this->key );
				}
			}
			return $is_enabled;
		}

		/**
		 * Load any submodules in the mod directory once.
		 *
		 * Submodules must be setup according to our folder layout.
		 */
		protected function load_submodules() {
			$submodule_path = $this->root_dir . $this->path . 'mod/*/module.php';
			foreach( glob( $submodule_path ) as $submodule ) {
				include_once $submodule;
			}
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
			if ( $loader = $this->get_template_loader() ) {
				return $loader->locate_template( $template_names, $load, $require_once );
			}
			return $loader;
		}

		/**
		 * Retrieve the parent module object if one exists.
		 * @return Module_Kit object
		 */
		public function parent() {
			return $this->get_parent();
		}

		/**
		 * Public getter method for retrieving protected/private variables
		 * @since  0.1.0
		 * @param  string  $field Field to retrieve
		 * @return mixed          Field value or exception is thrown
		 */
		public function __get( $field ) {
			// Allowed fields to retrieve
			if ( in_array( $field, array( 'key', 'options_page', 'path', 'title' ), true ) ) {
				return $this->{$field};
			}

			throw new Exception( 'Invalid property: ' . $field );
		}
	} # End class.
}

//Module_Kit::get_instance( false, 'root' );
