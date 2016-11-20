<?php
require_once 'Gamajo-Template-Loader/class-gamajo-template-loader.php';

if ( ! class_exists( 'MK_Template_Loader' ) ) {
	/**
	 * Template loader.
	 *
	 * Originally based on functions in Easy Digital Downloads (thanks Pippin!).
	 *
	 * @version 0.9.0
	 * @uses Gamajo_Template_Loader
	 *
	 * @param   string     $module_path              Relative path for a module's templates
	 *
	 * @method  string     get_template_part()       Retrieve a template part.
	 * @method  void       set_template_data()       Make custom data available to template.
	 * @method  void       unset_template_data()     Remove access to custom data in template.
	 * @method  array      get_template_file_names() Given a slug and optional name, create the file names of templates.
	 * @method  string     locate_template()         Retrieve the name of the highest priority template file that exists.
	 * @method  mixed|void get_template_paths()      Return a list of paths to check for template locations.
	 * @method  string     get_templates_dir()       Return the path to the templates directory in this plugin.
	 *
	 */
	class MK_Template_Loader extends Gamajo_Template_Loader {
		/**
		 * Prefix for filter names.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		protected $filter_prefix = 'mk';

		/**
		 * Directory name where custom templates for this plugin should be found in the theme.
		 *
		 * For example: 'your-plugin-templates'.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		protected $theme_template_directory = 'templates/mod';

		/**
		 * Reference to the root directory path of this plugin.
		 *
		 * Can either be a defined constant, or a relative reference from where the subclass lives.
		 *
		 * e.g. YOUR_PLUGIN_TEMPLATE or plugin_dir_path( dirname( __FILE__ ) ); etc.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		protected $plugin_directory = Module_Kit_DIR;

		/**
		 * Constructor
		 *
		 * Allows a module to choose to locate its templates within itself,
		 *   rather than in the base plugin
		 *
		 * @since 0.1.0
		 *
		 * @param null|string $module_path Relative path for a module's templates
		 */
		public function __construct( $module_path = null ) {
			if ( ! is_string($module_path) )
				return;

			// Set up our module path
			$module_path = trim( $module_path, '\/' );
			$this->theme_template_directory = trim( "templates/{$module_path}" );
			$this->plugin_template_directory = trim( "{$module_path}/templates" );
		}
	}
}
