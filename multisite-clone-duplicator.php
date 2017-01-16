<?php
/**
 * Plugin Name:         MultiSite Clone Duplicator
 * Plugin URI:          http://wordpress.org/plugins/multisite-clone-duplicator/
 * Description:         Clones an existing site into a new one in a multisite installation : copies all the posts, settings and files
 * Author:              Julien OGER, Pierre DARGHAM, David DAUGREILH, GLOBALIS media systems
 * Author URI:          https://github.com/pierre-dargham/multisite-clone-duplicator
 *
 * Version:             1.3.3
 * Requires at least:   4.0.0
 * Tested up to:        4.7.0
 */

// Block direct requests
if ( !defined('ABSPATH') )
    die('-1');

if( !class_exists( 'MUCD' ) ) {

    // Load configuration
    require_once realpath( dirname( __FILE__ ) ) . '/include/config.php';

    // Plugin options
    require_once MUCD_COMPLETE_PATH . '/include/option.php';

    // Load textdomain
    load_plugin_textdomain( MUCD_DOMAIN, NULL, MUCD_PATH . '/language/' );

    // Load language
    require_once MUCD_COMPLETE_PATH . '/include/lang.php';

    // Load Functions
    require_once MUCD_COMPLETE_PATH . '/lib/functions.php';

    if( is_admin() ) {
        require_once MUCD_COMPLETE_PATH . '/include/admin.php';
        MUCD_Admin::hooks();
    }

    if ( defined('WP_CLI') && WP_CLI ) {
        require_once MUCD_COMPLETE_PATH . '/lib/duplicate.php';
        MUCD_Functions::set_locale_to_en_US();
        require_once MUCD_COMPLETE_PATH . '/wp-cli/wp-cli-site-duplicate-subcommand.php';
    }    

    /**
     * Main class of the plugin
     */
    class MUCD {
        /**
        * Plugin's version number
        */
        const VERSION = '1.4.0';
        
        /**
         * Register hooks used by the plugin
         */
        public static function hooks() {
            // Register (de)activation hook
            register_activation_hook( __FILE__, array( __CLASS__, 'activate' ) );

            add_action( 'init', array( __CLASS__, 'init' ) );
            add_action( 'admin_init', array( __CLASS__, 'check_if_multisite' ) );
        }


        /**
         * Deactivate the plugin if we are not on a multisite installation
         * @since 0.2.0
         */
        public static function check_if_multisite() {
            if (!function_exists('is_multisite') || !is_multisite()) {
                deactivate_plugins( plugin_basename( __FILE__ ) );
                wp_die('multisite-clone-duplicator works only for multisite installation');
            }
        }

        /**
         * Deactivate the plugin if we are not on the network admin
         * @since 1.4.0
         */
        public static function check_if_network_admin() {
            if (!is_network_admin() ) {
                deactivate_plugins( plugin_basename( __FILE__ ) );
                wp_die('multisite-clone-duplicator works only as multisite network-wide plugin');
            }
        }

        /**
         * What to do on plugin activation
         */
        public static function activate() {
            MUCD::check_if_multisite();
            MUCD::check_if_network_admin();
            MUCD_Option::init_options();
        }

        /**
         * Plugin init
         */
        public static function init() {
             // Nothing for now.
        }   
    }
    MUCD::hooks();
}
