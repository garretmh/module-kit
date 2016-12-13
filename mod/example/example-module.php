<?php
if ( ! class_exists( 'MK_Example', false ) ) {
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
	 * @property object $options_page                               MK_Module_Kit_Options object
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
	class MK_Example extends MK_Module {

		/**
		 * Module title
		 * @internal Overrideable
		 *           Default: ''
		 *           Defaults to the class name
		 * @var string
		 */
		protected $title = '';

		/**
		 * Module naming prefix
		 * @internal Overrideable
		 *           Default: 'mk'
		 * @var string
		 */
		protected $prefix = 'mk';

		/**
		 * Whether the module is a Root Module or Submodule
		 * @internal Overrideable
		 *           Default: null
		 *           If true, module will initiate as a root module
		 * @var boolean
		 */
		private $is_root = null;

		/**
		 * Run the module
		 *
		 * Add hooks, register an options page, load submodules
		 * @internal Overrideable
		 */
		protected function activate() {
			# Register our options page
			//$this->add_options_page();

			# Setup up our hooks to always run
			//add_action( 'init', [$this, 'init'] );
			//add_filter( 'the_content', [$this, 'the_content'] );

			# Set up our hooks to run when enabled
			//$this->add_action( 'cmb2_admin_init', [$this, 'admin_init'] );
			//$this->add_filter( 'the_content', [$this, 'the_content'] );

			# Load our submodules
			//$this->load_submodules();

			# Set up our submodule-dependant hooks
			// if ( static::class == self::class ) {
			// 	# Setup up our mk_example related hooks
			// 	if ( $submodule = self::get_instance('mk_example') ) {
			// 		$submodule->add_filter( 'the_content', [$this, 'the_content'] );
			// 	}
			// }
		}

		/**
		 * Register a metabox for the Module's options page
		 * @internal Overrideable
		 * @see  Module_Kit::add_options_page
		 * @uses CMB2
		 */
		public function add_options_fields( $cmb ) {
			# Create a checkbox for each submodule
			//Module_Kit::add_options_fields($cmb);

			# Set our CMB2 fields
			// $cmb->add_field([
			// 	'name' => __( 'Test Text', 'myprefix' ),
			// 	'desc' => __( 'field description (optional)', 'myprefix' ),
			// 	'id'   => 'test_text',
			// 	'type' => 'text',
			// 	'default' => 'Default Text',
			// ]);
			//
			// $cmb->add_field([
			//   'name'    => __( 'Test Color Picker', 'myprefix' ),
			//   'desc'    => __( 'field description (optional)', 'myprefix' ),
			//   'id'      => 'test_colorpicker',
			//   'type'    => 'colorpicker',
			//   'default' => '#bada55',
			// ]);
		}
	} # End class.

	MK_Example::initiate();
}
