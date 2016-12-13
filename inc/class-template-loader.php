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
		 * Constructor
		 *
		 * Allows a module to choose to locate its templates within itself,
		 *   rather than in the base plugin
		 *
		 * @since 0.1.0
		 *
		 * @param null|string $module_path Relative path for a module's templates
		 */
		public function __construct( $args ) {
			$a = wp_parse_args( $args, array(
				'filter_prefix' => 'gamajo',
				'plugin_directory' => plugin_dir_path(__FILE__),
				'plugin_template_directory' => 'templates',
				'theme_template_directory' => 'templates/plugin',
			) );

			$this->filter_prefix = $a['filter_prefix'];
			$this->plugin_directory = $a['plugin_directory'];
			$this->plugin_template_directory = $a['plugin_template_directory'];
			$this->theme_template_directory = $a['theme_template_directory'];
		}
	} # End class.
}
