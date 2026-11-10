<?php
/**
 * Customizer Radio Image Control.
 *
 * @version 2.0.0
 * @package Kia_Customizer_Radio_Image_Control
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Exit if WP_Customize_Control does not exsist.
if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return null;
}

/**
 * KIA_Customizer_Radio_Image_Control class.
 */
class KIA_Customizer_Radio_Image_Control extends \WP_Customize_Control {

	/**
	 * Control version.
	 *
	 * @var string
	 */
	const VERSION = '2.0.0';

	/**
	 * Control type. Always set from the class name, see get_control_type().
	 *
	 * @var string
	 */
	public $type = '';

	/**
	 * Constructor.
	 *
	 * The control type comes from the class name, so a copy prefixed by a tool like Strauss
	 * gets its own control type, script, styles and JS constructor.
	 *
	 * @param \WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string                $id      Control ID.
	 * @param array                 $args    Optional. Arguments to override class property defaults.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		$this->type = static::get_control_type();
	}

	/**
	 * Get the control type from the class name.
	 *
	 * @return string ex: kia-customizer-radio-image-control
	 */
	public static function get_control_type() {
		return strtolower( str_replace( array( '\\', '_' ), '-', static::class ) );
	}

	/**
	 * Enqueue scripts/styles.
	 *
	 * Registered once per control type. The script stores its constructor on
	 * wp.customize.kiaRadioImageControl and the inline script after it registers that constructor
	 * for this type before another copy of the control can replace it.
	 */
	public function enqueue() {
		$handle = $this->type;

		if ( ! wp_script_is( $handle, 'registered' ) ) {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_script( $handle, $this->abs_path_to_url( __DIR__ . '/js/script' . $suffix . '.js' ), array( 'jquery', 'customize-controls' ), self::VERSION, true );
			wp_add_inline_script( $handle, sprintf( 'wp.customize.controlConstructor[ %s ] = wp.customize.kiaRadioImageControl;', wp_json_encode( $handle ) ) );

			wp_register_style( $handle, false, array(), self::VERSION );
			wp_add_inline_style( $handle, $this->get_styles() );
		}

		wp_enqueue_script( $handle );
		wp_enqueue_style( $handle );
	}

	/**
	 * Get the control's styles, scoped to this control type.
	 *
	 * @return string
	 */
	protected function get_styles() {
		$css = file_get_contents( __DIR__ . '/css/style.css' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local file.
		$css = preg_replace( '/^\xEF\xBB\xBF/', '', (string) $css ); // Strip a byte order mark.
		return $css ? str_replace( 'customize-control-kia-customizer-radio-image-control', 'customize-control-' . $this->type, $css ) : '';
	}

	/**
	 * Add custom JSON parameters to use in the JS template.
	 */
	public function to_json() {
		parent::to_json();

		// We need to make sure we have the correct image URL.
		foreach ( $this->choices as $value => $args ) {
			$this->choices[ $value ]['image'] = esc_url( $args['image'] );
		}

		$this->json['choices'] = $this->choices;
		$this->json['link']    = $this->get_link();
		$this->json['value']   = $this->value();
		$this->json['id']      = $this->id;
	}

	/**
	 * Underscore JS template to handle the control's output.
	 */
	public function content_template() {
		?>

		<# if ( ! data.choices ) {
			return;
		} #>

		<fieldset class="customize-control-kia-radio-images">

			<# if ( data.label ) { #>
				<legend class="customize-control-title">{{ data.label }}</legend>
			<# } #>

			<# if ( data.description ) { #>
				<p class="description customize-control-description">{{{ data.description }}}</p>
			<# } #>

			<div class="radio-image__options-wrapper">
		
				<# for ( key in data.choices ) { #>

					<input type="radio" value="{{ key }}" name="_customize-{{ data.type }}-{{ data.id }}" id="{{ data.id }}-{{ key }}" {{{ data.link }}} <# if ( key === data.value ) { #> checked="checked" <# } #> />

					<label for="{{ data.id }}-{{ key }}" class="{{ key }}">
						<# if ( data.choices[ key ]['image'] ) { #>
							<img src="{{ data.choices[ key ]['image'] }}" alt="{{ data.choices[ key ]['label'] }}" />
						<# } #>
						<span class="description">{{ data.choices[ key ]['label'] }}</span>
					</label>

				<# } #>

			</div>

		</fieldset><!-- .customize-control-kia-radio-images -->
		<?php
	}

	/**
	 * Sanitize the image radios.
	 *
	 * @param string                 $value   Setting value.
	 * @param \WP_Customize_Setting  $setting Setting instance.
	 * @return string
	 */
	public static function sanitize( $value, $setting ) {
		// Input must be a slug: lowercase alphanumeric characters, dashes and underscores are allowed only.
		$value = sanitize_key( $value );

		// Get the list of possible radio box options.
		$choices = $setting->manager->get_control( $setting->id )->choices;

		// Return value if valid or return default option.
		return ( array_key_exists( $value, $choices ) ? $value : $setting->default );
	}

	/**
	 * Plugin / theme agnostic path to URL.
	 *
	 * @see https://wordpress.stackexchange.com/a/264870/14546
	 * @param string $path File path.
	 * @return string      URL.
	 */
	private function abs_path_to_url( $path = '' ) {
		$url = str_replace(
			wp_normalize_path( untrailingslashit( ABSPATH ) ),
			site_url(),
			wp_normalize_path( $path )
		);
		return esc_url_raw( $url );
	}
}
