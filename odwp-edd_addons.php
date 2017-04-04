<?php
/**
 * Plugin Name: Úpravy pro plugin Easy Digital Downloads
 * Plugin URI: https://github.com/ondrejd/odwp-edd_addons
 * Description: Úpravy pro WordPress plugin Easy Digital Downloads.
 * Version: 1.0.0
 * Author: ondrejd
 * Author URI: http://ondrejd.com
 * License: GPLv3
 *
 * Text Domain: odwp-edd_addons
 * Domain Path: /languages
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @link https://github.com/ondrejd/odwp-edd_addons for the canonical source repository
 * @package odwp-edd-addon
 *
 * @todo Add dependency on Easy Digital Downloads plugin!
 */

// For more security...
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

// Plugin constants
defined( 'ODWPEDDA_SLUG' ) || define( 'ODWPEDDA_SLUG', 'odwp-edd_addons' );
defined( 'ODWPEDDA_FILE' ) || define( 'ODWPEDDA_FILE', __FILE__ );



if ( ! function_exists( 'odwpedda_countries' ) ) :
    /**
     * Serves `edd_countries` filter.
     * @param array $countries
     * @return $countries
     * @todo Load enabled countries from settings of the plugin.
     */
    function odwpedda_countries( $countries ) {
        return [
            'CZ' => __( 'Česká republika', ODWPEDDA_SLUG ),
            'SK' => __( 'Slovenská republika', ODWPEDDA_SLUG ),
        ];
    }
endif;



if ( ! function_exists( 'odwpedda_cc_billing_bottom' ) ) :
    /**
     * Hides picked up elements from the checkout form.
     * @todo Add settings to pick up hidden elements.
     */
    function odwpedda_cc_billing_bottom() {
?>
<script type="text/javascript">
jQuery( document ).ready( function() {
    jQuery( "#edd-card-state-wrap" ).hide();
    jQuery( "#card_state" ).val( "přeskočeno" );
    jQuery( "#edd-card-zip-wrap" ).hide();
    jQuery( "#card_zip" ).val( "12345" );
    jQuery( "#edd-card-city-wrap" ).hide();
    jQuery( "#card_city" ).val( "přeskočeno" );
    jQuery( "#edd-card-address-2-wrap" ).hide();
    jQuery( "#edd-card-address-wrap" ).hide();
} );
</script>
<?php
    }
endif;



if ( !function_exists( 'odwpedda_admin_init' ) ) :
    /**
     * Initializes all what is needed for this plugin in WP administration.
     */
    function odwpedda_admin_init() {
        register_setting( ODWPEDDA_SLUG, 'odwpedda_settings' );

        add_settings_section(
            'odwpedda_settings_section',
            __( 'Your section description', ODWPEDDA_SLUG ),
            'odwpedda_settings_section_callback',
            ODWPEDDA_SLUG
        );

        add_settings_field(
            'odwpedda_settings_field_0',
            __( 'Omezení výběru zemí', ODWPEDDA_SLUG ),
            'odwpedda_settings_field_0_render',
            ODWPEDDA_SLUG,
            'odwpedda_settings_section'
        );

        add_settings_field(
            'odwpedda_settings_field_1',
            __( 'Skrytí checkout polí', ODWPEDDA_SLUG ),
            'odwpedda_settings_field_1_render',
            ODWPEDDA_SLUG,
            'odwpedda_settings_section'
        );
    }
endif;



if ( ! function_exists( 'odwpedda_settings_section_callback' ) ) :
    /**
     * ...
     */
    function odwpedda_settings_section_callback() {
        echo __( 'This section description', 'odwp-edd_addons' );
    }
endif;



if ( ! function_exists( 'odwpedda_settings_field_0_render' ) ) :
    /**
     * ...
     */
    function odwpedda_settings_field_0_render() {
        $options = get_option( 'odwpedda_settings' );
        $selected = array();

        // Get selected options
        if ( array_key_exists( 'odwpedda_settings_field_0', $options ) ) {
            $selected = explode( ',', $options['odwpedda_settings_field_0'] );
        }
?>
    <select id="odwpedda_select_field_0" name="odwpedda_settings[odwpedda_select_field_0]" multiple size="10">
        <?php foreach ( edd_get_country_list() as $val => $label ) : ?>
        <option value="<?= $val ?>" <?php selected( in_array( $val, $selected ) ) ?>><?= $label ?></option>
        <?php endforeach ?>
	</select>
<?php
    }
endif;



if ( ! function_exists( 'odwpedda_settings_field_1_render' ) ) :
    /**
     * ...
     */
    function odwpedda_settings_field_1_render() {
        $options = get_option( 'odwpedda_settings' );
        $available = array(
            'address' => __( 'Adresa', ODWPEDDA_SLUG ),
            'address2' => __( 'Adresa (2. řádek)', ODWPEDDA_SLUG ),
            'city' => __( 'Město', ODWPEDDA_SLUG ),
            'zip' => __( 'PSČ', ODWPEDDA_SLUG ),
            'state' => __( 'Část země (kraj, provincie, podstát)', ODWPEDDA_SLUG ),
        );
        $selected = array();

        // Get selected options
        if ( array_key_exists( 'odwpedda_settings_field_1', $options ) ) {
            $selected = explode( ',', $options['odwpedda_settings_field_1'] );
        }
?>
    <select id="odwpedda_select_field_1" name="odwpedda_settings[odwpedda_select_field_1]" multiple size="10">
        <?php foreach ( $available as $val => $label ) : ?>
        <option value="<?= $val ?>" <?php selected( in_array( $val, $selected ) ) ?>><?= $label ?></option>
        <?php endforeach ?>
	</select>
<?php
    }
endif;



if ( !function_exists( 'odwpedda_admin_menu' ) ) :
    /**
     * Plugin's administration menu.
     */
    function odwpedda_admin_menu() {
        add_options_page(
                __( 'Úpravy pro plugin Easy Digital Downloads', ODWPEDDA_SLUG ),
                __( 'Úpravy pro EDD', ODWPEDDA_SLUG ),
                'manage_options',
                ODWPEDDA_FILE,
                'odwpedda_options_page'
        );
    }
endif;



if ( ! function_exists( 'odwpedda_options_page' ) ) :
    /**
     * Plugin's options page.
     */
    function odwpedda_options_page() {
?>
	<form action='options.php' method='post'>
		<h2>Úpravy pro EDD</h2>
		<?php
		settings_fields( ODWPEDDA_SLUG );
		do_settings_sections( ODWPEDDA_SLUG );
		submit_button();
		?>
	</form>
<?php
    }
endif;



// Register action/filter hooks
add_filter( 'edd_countries', 'odwpedda_countries' );
add_action( 'edd_cc_billing_bottom', 'odwpedda_cc_billing_bottom' );

if ( is_admin() ) {
    add_action( 'admin_menu', 'odwpedda_admin_init' );
    add_action( 'admin_menu', 'odwpedda_admin_menu' );
}