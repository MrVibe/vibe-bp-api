<?php
/**
 * Plugin Name: Vibe BuddyPress API
 * Plugin URI:  https://vibethemes.com
 * Description: BuddyPress Rest API for REACT
 * Author:      VibeThemes
 * Author URI:  https://vibethemes.com
 * Version:     1.0
 * Text Domain: vibe-bp-api
 * Domain Path: /languages/
 * License:     GPLv2 or later (license.txt)
 */

if ( !defined( 'ABSPATH' ) ) exit;


define('VIBE_BP_API_NAMESPACE','VibeBP/v1');
/*====== BEGIN INCLUDING FILES ======*/

if(!class_exists('WPLMS_oAuth_Server')){
    include_once('includes/auth_server/class-api-apps.php');
    include_once('includes/auth_server/class-api-wp.php');
    include_once('includes/auth_server/class-apps.php');
}
include_once('includes/class.admin.php');


/* Only load the component if BuddyPress is loaded and initialized. */
function vibe_bp_api_init() {
	// Because our loader file uses BP_Component, it requires BP 1.5 or greater.
	if ( defined('BP_VERSION') && version_compare( BP_VERSION, '1.8', '>' ) )
		require( dirname( __FILE__ ) . '/includes/bp-api-loader.php' );
}
add_action( 'bp_include', 'vibe_bp_api_init' );

add_action('plugins_loaded','vibe_bp_api_module_translations');
function vibe_bp_api_module_translations(){
    $locale = apply_filters("plugin_locale", get_locale(), 'vibe-bp-api');
    $lang_dir = dirname( __FILE__ ) . '/languages/';
    $mofile        = sprintf( '%1$s-%2$s.mo', 'vibe-bp-api', $locale );
    $mofile_local  = $lang_dir . $mofile;
    $mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;

    if ( file_exists( $mofile_global ) ) {
        load_textdomain( 'vibe-bp-api', $mofile_global );
    } else {
        load_textdomain( 'vibe-bp-api', $mofile_local );
    }   
}