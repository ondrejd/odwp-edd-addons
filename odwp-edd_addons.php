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

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}



defined( 'ODWPEDDA_SLUG' ) || define( 'ODWPEDDA_SLUG', 'odwp-edd_addons' );



if ( !function_exists( 'odwpedda_countries' ) ) :
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



add_filter( 'edd_countries', 'odwpedda_countries' );

