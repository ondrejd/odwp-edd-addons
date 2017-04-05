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
defined( 'ODWPEDDA_SLUG' )    || define( 'ODWPEDDA_SLUG', 'odwp-edd_addons' );
defined( 'ODWPEDDA_FILE' )    || define( 'ODWPEDDA_FILE', __FILE__ );
defined( 'ODWPEDDA_VERSION' ) || define( 'ODWPEDDA_VERSION', '1.0.0' );
defined( 'ODWPEDDA_OPTSKEY' ) || define( 'ODWPEDDA_OPTSKEY', 'odwpedda_settings' );



if ( ! function_exists( 'odwpedda_init' ) ) :
    /**
     * Initializes plugin.
     */
    function odwpedda_init() {
        // Ensure that plugin's options are initialized
        odwpedda_get_options();
    }
endif;



if ( !function_exists( 'odwpedda_get_default_options' ) ) :
    /**
     * Returns plugin's default options.
     * @return array
     */
    function odwpedda_get_default_options() {
        return [
            'odwpedda_settings_field_0' => 'CZ,SK',
            'odwpedda_settings_field_1' => 'address,address2,city,zip,state',
        ];
    }
endif;



if ( ! function_exists( 'odwpedda_get_options' ) ) :
    /**
     * Get plugin's options.
     */
    function odwpedda_get_options() {
		$options = get_option( ODWPEDDA_OPTSKEY );
		$need_update = false;

		if ( !is_array( $options) ) {
			$need_update = true;
			$options = array();
		}

		foreach (odwpedda_get_default_options() as $key => $value ) {
			if ( !array_key_exists( $key, $options ) ) {
				$options[$key] = $value;
				$need_update = true;
			}
		}

		if ( !array_key_exists( 'latest_used_version', $options ) ) {
			$options['latest_used_version'] = ODWPEDDA_VERSION;
			$need_update = true;
		}

		if ( $need_update === true ) {
			update_option( ODWPEDDA_OPTSKEY, $options );
		}

		return $options;
    }
endif;



if ( ! function_exists( 'odwpedda_countries' ) ) :
    /**
     * Serves `edd_countries` filter.
     * @param array $countries
     * @return $countries
     * @todo Load enabled countries from settings of the plugin.
     */
    function odwpedda_countries( $countries ) {
        if ( function_exists( 'get_current_screen' ) ) {
            $screen = get_current_screen();

            // In plugin's options page we need to print ALL countries in the selectbox
            if ( ( $screen instanceof WP_Screen ) ) {
                if ( $screen->base == 'settings_page_odwp-edd_addons/odwp-edd_addons' ) {
                    // Remove the first item - is just empty row
                    array_shift($countries);

                    return $countries;
                }
            }
        }

        // 1. get plugin's options
        // 2. get `enabled_countries` option
        // 3. return enabled countries
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



if ( ! function_exists( 'odwpedda_sanitize_settings' ) ) :
    /**
     * @todo Finish this!
     */
    function odwpedda_sanitize_settings() {
        var_dump( $_POST );exit();
    }
endif;


if ( !function_exists( 'odwpedda_admin_init' ) ) :
    /**
     * Initializes all what is needed for this plugin in WP administration.
     */
    function odwpedda_admin_init() {
        register_setting( ODWPEDDA_SLUG, ODWPEDDA_OPTSKEY, 'odwpedda_sanitize_settings' );

        add_settings_section(
                'odwpedda_settings_section_0',
                __( 'Stránka pokladna', ODWPEDDA_SLUG ),
                'odwpedda_settings_section_0_callback',
                ODWPEDDA_SLUG
        );

        add_settings_section(
                'odwpedda_settings_section_1',
                __( 'Číselník dokladů', ODWPEDDA_SLUG ),
                'odwpedda_settings_section_1_callback',
                ODWPEDDA_SLUG
        );

        add_settings_section(
                'odwpedda_settings_section_2',
                __( 'Generování PDF', ODWPEDDA_SLUG ),
                'odwpedda_settings_section_2_callback',
                ODWPEDDA_SLUG
        );

        add_settings_field(
                'odwpedda_settings_field_0',
                __( 'Omezení výběru zemí', ODWPEDDA_SLUG ),
                'odwpedda_settings_field_0_render',
                ODWPEDDA_SLUG,
                'odwpedda_settings_section_0'
        );

        add_settings_field(
                'odwpedda_settings_field_1',
                __( 'Skrytí checkout polí', ODWPEDDA_SLUG ),
                'odwpedda_settings_field_1_render',
                ODWPEDDA_SLUG,
                'odwpedda_settings_section_0'
        );

        add_settings_field(
                'odwpedda_settings_field_2',
                __( 'Začít číselník od', ODWPEDDA_SLUG ),
                'odwpedda_settings_field_2_render',
                ODWPEDDA_SLUG,
                'odwpedda_settings_section_1'
        );

        add_settings_field(
                'odwpedda_settings_field_3',
                __( 'Adresář pro generování', ODWPEDDA_SLUG ),
                'odwpedda_settings_field_3_render',
                ODWPEDDA_SLUG,
                'odwpedda_settings_section_2'
        );
    }
endif;



if ( ! function_exists( 'odwpedda_settings_section_0_callback' ) ) :
    /**
     * Prints the first settings section ("Nastavení stránky pokladna").
     */
    function odwpedda_settings_section_0_callback() {
        echo __( 'Níže můžete upravit stránku pokladna (<em>checkout</em>) - v prvním případě můžete vybrat země, na které bude omezen výběr; v druhém vybrat pole, které budou pro uživatele schována. (Pozn.: Některá z těchto polí vyžadují hodnoty a proto jim v případě skrytí budou podsunuty předdefinované hodnoty - v případě textových polí je to <em>přeskočeno</em>, u PSČ je to <em>12345</em>.).', ODWPEDDA_SLUG );
    }
endif;



if ( ! function_exists( 'odwpedda_settings_section_1_callback' ) ) :
    /**
     * Prints the first settings section ("Číselník dokladů").
     */
    function odwpedda_settings_section_1_callback() {
        echo __( 'Nastavení automatického číselníku dokladů.', ODWPEDDA_SLUG );
    }
endif;



if ( ! function_exists( 'odwpedda_settings_section_2_callback' ) ) :
    /**
     * Prints the third settings section ("Generování PDF").
     */
    function odwpedda_settings_section_2_callback() {
        echo __( 'Nastavení pro generování dokladů do PDF souborů.', ODWPEDDA_SLUG );
    }
endif;



if ( ! function_exists( 'odwpedda_settings_field_0_render' ) ) :
    /**
     * Prints the first settings field.
     */
    function odwpedda_settings_field_0_render() {
        $options = (array) get_option( ODWPEDDA_OPTSKEY );
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
     * Prints the second settings field.
     */
    function odwpedda_settings_field_1_render() {
        $options = (array) get_option( ODWPEDDA_OPTSKEY );
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



if ( ! function_exists( 'odwpedda_settings_field_2_render' ) ) :
    /**
     * Prints the third settings field.
     */
    function odwpedda_settings_field_2_render() {
        $options = (array) get_option( ODWPEDDA_OPTSKEY );
        $value   = array_key_exists( 'odwpedda_select_field_2', $options ) ? $options['odwpedda_select_field_2'] : '1';
?>
    <input class="small-text" id="odwpedda_select_field_2" id="odwpedda_settings[odwpedda_select_field_2]" type="number">
<?php
    }
endif;



if ( ! function_exists( 'odwpedda_settings_field_3_render' ) ) :
    /**
     * Prints the third settings field.
     */
    function odwpedda_settings_field_3_render() {
        $options = (array) get_option( ODWPEDDA_OPTSKEY );
        $value   = array_key_exists( 'odwpedda_select_field_3', $options ) ? $options['odwpedda_select_field_3'] : 'edd_pdf';
?>
    <input class="regular-text" id="odwpedda_select_field_3" id="odwpedda_settings[odwpedda_select_field_3]" type="number">
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
		<h2>Úpravy pro Easy Digital Downloads plugin</h2>
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
add_action( 'init', 'odwpedda_init' );
add_action( 'edd_cc_billing_bottom', 'odwpedda_cc_billing_bottom' );
add_filter( 'edd_countries', 'odwpedda_countries' );

if ( is_admin() ) {
    add_action( 'admin_menu', 'odwpedda_admin_init' );
    add_action( 'admin_menu', 'odwpedda_admin_menu' );
}