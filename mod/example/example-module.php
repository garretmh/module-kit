<?php
if ( ! class_exists( 'MK_Example', false ) ) {
	/**
	 * Example MK module
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
	class MK_Example extends MK_Module {

		/**
		 * The module begins here
		 * @internal REQUIRED function
		 */
		protected function run() {
			// Register our options page
			$this->register_options_page();

			// Set up hooks
			add_action( 'cmb2_init', [$this, 'when_enabled'] );
		}

		/**
		 * An example function to run code if the module is enabled
		 * @example if the module is enabled, this will fetch the template
		 *          'example-example' or, if it's unavailable, 'example' from the
		 *          theme's templates/mod/example folder or, if it doesn't find one,
		 *          from the module's templates folder and set $template to it.
		 */
		public function when_enabled() {
			if ( $this->is_enabled() )
			return;

			// code here.
			$template = $this->get_template_part( 'example', 'example', false );
		}


		/**
		 * Register a metabox for the Module's options page
		 * @internal REQUIRED function, only runs if register_options_page is used.
		 *
		 * @see  MK_Module::register_options_page()
		 * @uses CMB2
		 */
		public function options_page_metabox() {
			$metabox = $this->options_page;

			// Hook in our save notices
			add_action( "cmb2_save_options-page_fields_{$metabox->metabox_id}", array( $metabox, 'settings_notices' ), 10, 2 );

			// Create our CMB2 box
			$cmb = new_cmb2_box( array(
				'id'         => $metabox->metabox_id,
				'hookup'     => false,
				'cmb_styles' => false,
				'show_on'    => array(
					// These are important, don't remove
					'key'   => 'options-page',
					'value' => array( $metabox->key, )
				),
			) );

			// Set our CMB2 fields
			// $cmb->add_field( array(
			// 	'name' => __( 'Test Text', 'myprefix' ),
			// 	'desc' => __( 'field description (optional)', 'myprefix' ),
			// 	'id'   => 'test_text',
			// 	'type' => 'text',
			// 	'default' => 'Default Text',
			// ) );
			//
			// $cmb->add_field( array(
			//   'name'    => __( 'Test Color Picker', 'myprefix' ),
			//   'desc'    => __( 'field description (optional)', 'myprefix' ),
			//   'id'      => 'test_colorpicker',
			//   'type'    => 'colorpicker',
			//   'default' => '#bada55',
			// ) );
		}
	}

	MK_Example::initiate();
}
