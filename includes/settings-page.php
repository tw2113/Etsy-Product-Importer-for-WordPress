<?php
/**
 * Include and setup options page.
 *
 * @category Etsy Importer
 * @package  Options
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/WebDevStudios/Custom-Metaboxes-and-Fields-for-WordPress
 */

/**
 * Set up our options page.
 */
class Etsy_Options_Admin {

	/**
	 * Option key, and option page slug.
	 *
	 * @var string
	 */
	private $key = 'etsy_options';

	/**
	 * Array of metaboxes/fields.
	 *
	 * @var array
	 */
	protected $option_metabox = array();

	/**
	 * Options Page title.
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * Options Page hook.
	 *
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param string $post_type Post type to use regarding our settings page.
	 */
	public function __construct( $post_type ) {
		$this->post_type = $post_type;

		// Set our title.
		$this->title = __( 'Etsy Importer Settings', 'etsy_importer' );

		// Set our CMB2 fields.
		$this->fields = array(
			array(
				'name' => __( 'API Key', 'etsy_importer' ),
				'desc' => $this->api_key_description(),
				'id'   => 'etsy_importer_api_key',
				'type' => 'text',
			),
			array(
				'name' => __( 'Shop name or ID', 'etsy_importer' ),
				'desc' => __( 'Enter your Shop name or ID above.', 'etsy_importer' ),
				'id'   => 'etsy_importer_store_id',
				'type' => 'text',
			),
			array(
				'name' => __( 'Disable updating of post status on product import', 'etsy_importer' ),
				'desc' => __( 'When left unchecked, inactive products will be set to draft status and active products will be set to published. If you\'ve changed a product\'s status for already imported items, their published status will update appropriately.', 'etsy_importer' ),
				'id'   => 'etsy_importer_status_checkbox',
				'type' => 'checkbox',
			),
		);
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
	}

	/**
	 * Register our setting to WP.
	 *
	 * @since 0.1.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page.
	 *
	 * @since 0.1.0
	 */
	public function add_options_page() {
		$this->options_page = add_submenu_page( 'edit.php?post_type='. $this->post_type, $this->title, $this->title, 'manage_options', $this->key, array( $this, 'admin_page_display' ) );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2.
	 *
	 * @since 0.1.0
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb2_options_page <?php echo $this->key; // WPCS: XSS ok. ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb2_metabox_form( $this->option_metabox(), $this->key, $this->option_metabox_overrides() ); ?>
		</div>
		<?php
	}

	/**
	 * Defines the theme option metabox and field configuration.
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	public function option_metabox() {
		return array(
			'id'         => 'option_metabox',
			'show_on'    => array( 'key' => 'options-page', 'value' => array( $this->key ) ),
			'show_names' => true,
			'fields'     => $this->fields,
		);
	}

	/**
	 * Defines theme option metabox overrides.
	 *
	 * @since 1.4.0
	 *
	 * @return array
	 */
	public function option_metabox_overrides() {
		return (array) apply_filters( 'etsy_importer_option_metabox_overrides', array(
			'save_button' => __( 'Save &amp; Import', 'etsy_importer' ),
		) );
	}

	/**
	 * Retrieve the existing API Key from preivously registered settings.
	 *
	 * @return mixed Description text.
	 */
	public function api_key_description() {
		$template = '%s<p class="cmb2-metabox-description">%s</p><div id="etsy-api-instructions" style="display:none;width:800px;"><p>%s<br/>%s</p><p>%s</p></div>';

		$output = sprintf(
			$template,
			__( 'Enter your API Key above.', 'etsy_importer' ),
			sprintf(
				__( 'Need help? <a href="%s" class="thickbox">Click here</a> for a walkthrough on how to setup your Etsy Application.', 'etsy_importer' ),
				'#TB_inline?&height=600&width=800&inlineId=etsy-api-instructions'
			),
			sprintf(
				__( 'In order to import your products, you first need to register an application with Etsy.  <a href="%s" target="_blank">Click here</a> to begin registering your application.  You should see a screen similar to the image below:', 'etsy_importer' ),
				'https://www.etsy.com/developers/register'
			),
			'<img src="' . PLUGIN_BASE_DIR . 'screenshot-1.jpg" /></p>',
			__( 'Once you have created your app, click "Apps You\'ve Made" in the sidebar and select your new app.  On the app detail page, copy the value in the Keystring input field.  This is your API Key.', 'etsy_importer' ) . '<br /><img src="' . PLUGIN_BASE_DIR . 'screenshot-2.jpg" />'
		);

		return $output;
	}

	/**
	 * Public getter method for retrieving protected/private variables.
	 *
	 * @since 0.1.0
	 *
	 * @throws Exception Invalid property provided.
	 *
	 * @param string $field Field to retrieve.
	 * @return mixed Field value or exception is thrown.
	 */
	public function __get( $field ) {

		// Allowed fields to retrieve.
		if ( in_array( $field, array( 'key', 'fields', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}
		if ( 'option_metabox' === $field ) {
			return $this->option_metabox();
		}

		throw new Exception( 'Invalid property: ' . $field );
	}
}

/**
 * Wrapper function around cmb2_get_option.
 *
 * @since 0.1.0
 *
 * @param string $key Options array key.
 * @return mixed Option value
 */
function etsy_options_get_option( $key = '' ) {
	return cmb2_get_option( Etsy_Importer::engage()->admin->key, $key );
}
