<?php
/*
Plugin Name: PayDock for WooCommerce
Plugin URI: https://github.com/PayDockDev/woocommerce_plugin
Description: PayDock for WooCommerce
Author: Mark Cardamis
Text Domain: paydock-for-woocommerce
Version: 1.5.3
Author URI: 
*/

// Exit if executed directl
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Functions used by plugins
 */
if ( ! class_exists( 'WC_Dependencies' ) )
    require_once 'class-wc-dependencies.php';

/**
 * WC Detection
 */
if ( ! function_exists( 'is_woocommerce_active' ) ) {
    function is_woocommerce_active() {
        return WC_Dependencies::woocommerce_active_check();
    }
}

if ( is_woocommerce_active() ) {

    //current plugin version
    define( 'WOOPAYDOCK_VER', '1.5.3' );

    if ( !class_exists( 'WOOPAYDOCK' ) ) {

        class WOOPAYDOCK {

            var $plugin_dir;
            var $plugin_url;

            public function __clone() {
                _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '2.1' );
            }

            public function __wakeup() {
                _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '2.1' );
            }

            /**
             * Main constructor
             **/
            function __construct() {


                //setup proper directories
                $this->plugin_dir = dirname( __FILE__ ) . '/';
                $this->plugin_url = str_replace( array( 'http:', 'https:' ), '', plugins_url( '', __FILE__ ) ) . '/';


                register_activation_hook( __FILE__, array( &$this, 'activation' ) );

                load_plugin_textdomain( 'paydock-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

                add_filter( 'woocommerce_payment_gateways',  array( $this, 'add_gateways' ) );
            }

            /**
             * Run Activated funtions
             */
            function activation() {
                add_option( 'woopaydock_ver', WOOPAYDOCK_VER );
            }

            /**
             * Add gateways to WC
             *
             * @param  array $methods
             * @return array of methods
             */
            public function add_gateways( $methods ) {
                include_once( $this->plugin_dir . 'class-paydock.php' );

                $methods[] = 'WCPayDockGateway';

                return $methods;
            }

            //end class
        }

    }

    /**
     * function to initiate plugin
     */
    function init_woopaydock() {

        //checking for version required
        if ( ! version_compare( paydock_get_wc_version(), '2.6.0', '>=' ) ) {
            add_action( 'admin_notices', 'woopaydock_rec_ver_notice', 5 );
            function woopaydock_rec_ver_notice() {
                if ( current_user_can( 'install_plugins' ) )
                    echo '<div class="error fade"><p>Sorry, but for this version of <b>Woocommerce Gateway PayDock</b> is required version of the <b>WooCommerce</b> not lower than <b>2.6.0</b>. <br />Please update <b>WooCommerce</b> to latest version.</span></p></div>';
            }

        } else {
            $GLOBALS['woopaydock'] = new WOOPAYDOCK();
        }
    }

    function paydock_get_wc_version() {
        if ( defined( 'WC_VERSION' ) && WC_VERSION )
            return WC_VERSION;
        if ( defined( 'WOOCOMMERCE_VERSION' ) && WOOCOMMERCE_VERSION )
            return WOOCOMMERCE_VERSION;
        return null;
    }

    add_action( 'plugins_loaded', 'init_woopaydock' );
}