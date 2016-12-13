<?php
if ( ! class_exists( 'MK_Options' ) ) {
	/**
	 * Module Kit Options Page
	 *
	 * @version 0.9.0
	 * @see WebDevStudios/CMB2-Snippet-Library  CMB2_Theme_Options
	 */
	class MK_Options {

		/**
		 * Option key, and option page slug
		 * @var string
		 */
		protected $key = '';

		/**
		 * Options page metabox id
		 * @var string
		 */
		protected $metabox_id = null;

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

		protected $metabox_cb = null;
		protected $display_cb = null;
		protected $notices_cb = null;
		protected $enabled_cb = null;
		protected $parent = null;

		/**
		 * Constructor
		 * @since 0.1.0
		 */
		public function __construct( $key, $title, $metabox_cb, $args = array() ) {
			// Set our title
			$this->key = $key;
			$this->title = $title;
			$this->metabox_cb = $metabox_cb;

			$a = wp_parse_args( $args, array(
				'metabox_id' => "{$key}_metabox",
				'display_cb' => [$this, 'admin_page_display'],
				'notices_cb' => [$this, 'settings_notices'],
				'enabled_cb' => null,
				'parent' => false,
			) );
			$this->metabox_id = $a['metabox_id'];
			$this->display_cb = $a['display_cb'];
			$this->notices_cb = $a['notices_cb'];
			$this->enabled_cb = $a['enabled_cb'];
			$this->parent = $a['parent'];

			if ( ! is_a( $this->parent, __CLASS__ ) ) {
				$this->parent = false;
			}

			// Initiate our hooks
			$this->hooks();
		}

		/**
		 * Initiate our hooks
		 * @since 0.1.0
		 */
		public function hooks() {
			if ( ! $this->parent ) {
				add_action( 'admin_init', array( $this, 'init' ) );
			}
			add_action( 'admin_menu', array( $this, 'add_options_page' ) );
			add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );
		}


		/**
		 * Register our setting to WP
		 * @since  0.1.0
		 */
		public function init() {
			register_setting( $this->key, $this->key );
		}

		/**
		 * Add menu options page
		 * @since 0.1.0
		 */
		public function add_options_page() {
			if ( is_callable($this->enabled_cb) && ! call_user_func($this->enabled_cb) ) {
				return;
			}

			$parent = $this->parent;
			if ( ! $parent ) {
				$this->options_page = add_menu_page( $this->title, $this->title, 'manage_options', $this->key, $this->display_cb );
			} else {
				$this->options_page = add_submenu_page( $parent->key, "{$parent->title}: {$this->title}", $this->title, 'manage_options', $this->key, $this->display_cb );
			}

			// Include CMB CSS in the head to avoid FOUC
			add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
		}

		/**
		 * Admin page markup. Mostly handled by CMB2
		 * @since  0.1.0
		 */
		public function admin_page_display() {
			?>
			<div class="wrap cmb2-options-page <?php echo $this->key; ?>">
				<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
				<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
			</div>
			<?php
		}

		/**
		 * Add the options metabox to the array of metaboxes
		 * @since  0.1.0
		 */
		function add_options_page_metabox() {

			// hook in our save notices
			add_action( "cmb2_save_options-page_fields_{$this->metabox_id}", array( $this, 'settings_notices' ), 10, 2 );

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
			call_user_func( $this->metabox_cb, $cmb );
		}

		/**
		 * Register settings notices for display
		 *
		 * @since  0.1.0
		 * @param  int   $object_id Option key
		 * @param  array $updated   Array of updated fields
		 * @return void
		 */
		public function settings_notices( $object_id, $updated ) {
			if ( $object_id !== $this->key || empty( $updated ) ) {
				return;
			}

			add_settings_error( $this->key . '-notices', '', __( 'Settings updated.', 'mk' ), 'updated' );
			settings_errors( $this->key . '-notices' );
		}

		/**
		 * Public getter method for retrieving protected/private variables
		 * @since  0.1.0
		 * @param  string  $field Field to retrieve
		 * @return mixed          Field value or exception is thrown
		 */
		public function __get( $field ) {
			// Allowed fields to retrieve
			if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
				return $this->{$field};
			}

			throw new Exception( 'Invalid property: ' . $field );
		}
	} # End class.
}
