<?php
require_once 'class-options.php';

/**
 * Module Kit Module Options Pages
 *
 * @version 0.9.0
 * @uses MK_Options
 */
class MK_Module_Options extends MK_Options {

	/**
	 * Option key, and option page slug
	 * @var string
	 */
	protected $key = '';

	/**
	 * Options page metabox id
	 * @var string
	 */
	protected $metabox_id = '';

	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Options Page hook
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Options Metabox callback
	 * @var callback
	 */
	protected $metabox_cb = '';

	/**
	 * Options Display callback
	 * @var callback
	 */
	protected $display_cb = '';

	/**
	 * Options Notices callback
	 * @var callback
	 */
	protected $notices_cb = '';

	/**
	 * Options Is Endabled check callback
	 * @var callback
	 */
	protected $enabled_cb = '';

	/**
	 * Holds instances of the object
	 *
	 * @var MK_Module_Options[]
	 **/
	private static $instances = array();

	/**
	 * Holds instances of the object
	 *
	 * @var MK_Options
	 **/
	private static $parent = null;

	/**
	 * Constructor
	 * @since 0.1.0
	 */
	private function __construct( $id, $a ) {
		// Set our key
		$this->key = isset($a['key']) ? $a['key'] : $id;

		// Set our title
		$this->title = isset($a['title']) ? $a['title'] : $id;

		// Set our metabox_id
		$this->metabox_id = isset($a['metabox_id']) ? $a['metabox_id'] : "{$this->key}_metabox";

		// Set our metabox callback
		$this->metabox_cb = isset($a['metabox_cb']) ? $a['metabox_cb'] : array( $this, 'add_options_page_metabox' );

		// Set our display callback
		$this->display_cb = isset($a['display_cb']) ? $a['display_cb'] : array( $this, 'admin_page_display' );

		// Set our notices callback
		$this->notices_cb = isset($a['notices_cb']) ? $a['notices_cb'] : array( $this, 'settings_notices' );

		// Set our is enabled callback
		$this->enabled_cb = isset($a['enabled_cb']) ? $a['enabled_cb'] : false;
	}

	/**
	 * Returns the running object
	 *
	 * @return MK_Module_Options|MK_Module_Options[]
	 **/
	public static function get_instance( $id = null, $args = array() ) {
		if (is_null($id))
			return self::$instances;

		if ( ! isset( self::$instances[$id] ) ) {
			self::$instances[$id] = new self( $id, $args );
			self::$instances[$id]->hooks();
		}

		return self::$instances[$id];
	}

	/**
	 * Initiate our hooks
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'cmb2_admin_init', $this->metabox_cb );
	}

	/**
	 * Add menu options page
	 * @since 0.1.0
	 */
	public function add_options_page() {
		if ( is_callable($this->enabled_cb) && ! call_user_func($this->enabled_cb) ) {
			return;
		}
		$parent = parent::get_instance();
		$this->options_page = add_submenu_page( $parent->key, "{$parent->title}: {$this->title}", $this->title, 'manage_options', $this->key, $this->display_cb );

		// Include CMB CSS in the head to avoid FOUC
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	/**
	 * Add the options metabox to the array of metaboxes
	 * @since  0.1.0
	 */
	function add_options_page_metabox() {

		// hook in our save notices
		add_action( "cmb2_save_options-page_fields_{$this->metabox_id}", $this->notices_cb, 10, 2 );

		$cmb = new_cmb2_box( array(
			'id'         => $this->metabox_id,
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );

		// Set our CMB2 fields

		$cmb->add_field( array(
			'name' => __( 'Test Text', 'myprefix' ),
			'desc' => __( 'field description (optional)', 'myprefix' ),
			'id'   => 'test_text',
			'type' => 'text',
			'default' => 'Default Text',
		) );

		$cmb->add_field( array(
			'name'    => __( 'Test Color Picker', 'myprefix' ),
			'desc'    => __( 'field description (optional)', 'myprefix' ),
			'id'      => 'test_colorpicker',
			'type'    => 'colorpicker',
			'default' => '#bada55',
		) );

	}

}

/**
 * Helper function to get/return the MK_Options object
 * @since  0.1.0
 * @param  string|object
 * @return MK_Options object
 */
function mk_mod_options( $id, $args = array() ) {
	return MK_Module_Options::get_instance( $id, $args );
}
