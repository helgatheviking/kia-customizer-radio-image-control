# WordPress Customizer Radio Image Control

A radio control using images for the WordPress Customizer

![Animation. Two icons. One is 3 stacked rectangles representing a list. The other 6 boxes in two rowns of 3 representing a grid.](https://user-images.githubusercontent.com/507025/136675766-d1370616-a9e4-4c94-b0b6-154e5d773086.gif)

## Installation

```
composer require helgatheviking/kia-customizer-radio-image-control
```

The class is autoloaded through Composer's classmap, so there is nothing to `require`. If your plugin or theme doesn't load Composer's autoloader, `require` `vendor/helgatheviking/kia-customizer-radio-image-control/class-kia-customizer-radio-image-control.php` inside your `customize_register` callback.

## Adding the control

```php
/**
 * Add the control to the Customizer.
 *
 * @param WP_Customize_Manager $wp_customize
 */
function kia_customizer( $wp_customize ) {

    // Register the control type that we're using as a JavaScript control.
    $wp_customize->register_control_type( 'KIA_Customizer_Radio_Image_Control' );

    $wp_customize->add_setting(
        'my_setting',
        array(
            'default'           => 'tabular',
            'type'              => 'option',
            'capability'        => 'edit_themes',
            'sanitize_callback' => array( 'KIA_Customizer_Radio_Image_Control', 'sanitize' ),
        )
    );

    $wp_customize->add_control(
        new KIA_Customizer_Radio_Image_Control(
            $wp_customize,
            'my_setting',
            array(
                'label'    => __( 'Layout', 'your-textdomain' ),
                'section'  => 'my_section',
                'settings' => 'my_setting',
                'choices'  => array(
                    'tabular' => array(
                        'label' => esc_html__( 'List', 'your-textdomain' ),
                        'image' => 'assets/images/th-list.svg', // URL to image.
                    ),
                    'grid'    => array(
                        'label' => esc_html__( 'Grid', 'your-textdomain' ),
                        'image' => 'assets/images/th-grid.svg', // URL to image.
                    ),
                ),
            )
        )
    );
}
add_action( 'customize_register', 'kia_customizer' );
```

## Bundling it in a plugin or theme

The control type, script and style handle, CSS scope and JS constructor all come from the PHP class name: `KIA_Customizer_Radio_Image_Control` uses the type `kia-customizer-radio-image-control`. If you prefix the class, your copy gets its own of each, so another plugin bundling a different version can't replace yours.

With [Strauss](https://github.com/BrianHenryIE/strauss) and a `classmap_prefix` of `My_Plugin_`, the class becomes `My_Plugin_KIA_Customizer_Radio_Image_Control` with the type `my-plugin-kia-customizer-radio-image-control`. Use the prefixed class name in `register_control_type()`, `new …()` and the sanitize callbacks.

The JS and CSS URLs are worked out from the file path, so the package has to live inside the WordPress install (under `ABSPATH`).

## Upgrading from 1.x

* The package is a Composer `library` and no longer needs `composer/installers` or `installer-paths`. Remove the `installer-paths` entry and the `require_once` for the class.
* The control type is now `kia-customizer-radio-image-control` (or your prefixed equivalent) instead of `kia-radio-image`. Drop any `'type' => 'kia-radio-image'` argument: it's ignored. If you styled or scripted the control by its old type, ex: `.customize-control-kia-radio-image`, update those selectors.
* The script and style handle is the control type, and the styles are printed inline.

## Credits

Huge props to Rich Tabor's [Login Designer](https://github.com/thatplugincompany/login-designer).