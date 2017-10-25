<?php
/**
 * WCPayDockGateway
 */
if ( !class_exists( 'WCPayDockGateway' ) ) {

    class WCPayDockGateway extends WC_Payment_Gateway_CC {

        /**
         * Constructor
         */
        public function __construct() {

            $this->currency_list = array( 'AUD', 'USD', 'GBP', 'EUR', 'JPY', 'CAD', 'CHF', 'NZD' );
            $this->js_ver = '1.0.2';
            $this->method_title = 'PayDock';
            $this->id           = 'paydock';
            $this->has_fields   = true;
            $this->icon         = WP_PLUGIN_URL . '/woocommerce-gateway-paydock/assets/images/logo.png';

            // Load the form fields.
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();

            // Define user set variables
            $this->enabled                  = $this->settings['enabled'];
            $this->title                    = trim( $this->settings['title'] );
            $this->mode                     = $this->settings['sandbox'] == 'yes' ? 'sandbox' : 'production';

            $this->secret_key    = trim( $this->settings['paydock_secret_key'] );
            $this->public_key    = trim( $this->settings['paydock_public_key'] );

            if ( 'sandbox' == $this->mode )    {
                $this->api_endpoint = "https://api-sandbox.paydock.com/";
            } else {
                $this->api_endpoint = "https://api.paydock.com/";
            }

            $this->credit_card              = trim( $this->settings['credit_card'] );
            $this->credit_card_gateway_id   = trim( $this->settings['credit_card_gateway_id'] );
            $this->credit_card_email        = trim( $this->settings['credit_card_email'] );

            $this->direct_debit             = trim( $this->settings['direct_debit'] );
            $this->direct_debit_gateway_id  = trim( $this->settings['direct_debit_gateway_id'] );

            $this->paypal_express               = trim( $this->settings['paypal_express'] );
            $this->paypal_express_gateway_id    = trim( $this->settings['paypal_express_gateway_id'] );

            $this->zip_money                = trim( $this->settings['zip_money'] );
            $this->zip_money_gateway_id     = trim( $this->settings['zip_money_gateway_id'] );
            $this->zip_money_tokenization   = trim( $this->settings['zip_money_tokenization'] );

            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

            add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
        }


        /**
         * init_form_fields function.
         *
         * @access public
         * @return void
         */
        function init_form_fields() {

            $this->form_fields = array(
                'enabled' => array(
                    'title'       => __( 'Enable/Disable', WOOPAYDOCKTEXTDOMAIN ),
                    'label'       => __( 'Enable PayDock', WOOPAYDOCKTEXTDOMAIN ),
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'title' => array(
                    'title'       => __( 'Title', WOOPAYDOCKTEXTDOMAIN ),
                    'type'        => 'text',
                    'description' => __( 'Payment method title that the customer will see on your website.', WOOPAYDOCKTEXTDOMAIN ),
                    'default'     => __( 'PayDock', WOOPAYDOCKTEXTDOMAIN ),
                    'desc_tip'    => true
                ),
                'sandbox' => array(
                    'title'       => __( 'Use Sandbox', WOOPAYDOCKTEXTDOMAIN ),
                    'label'       => __( 'Enable sandbox - live payments will not be taken if enabled.', WOOPAYDOCKTEXTDOMAIN ),
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'paydock_public_key' => array(
                    'title'       => __( 'PayDock Public Key', WOOPAYDOCKTEXTDOMAIN ),
                    'type'        => 'password',
                    'description' => __( 'Obtained from your PayDock account. You can set this key by logging into PayDock.', WOOPAYDOCKTEXTDOMAIN ),
                    'default'     => '',
                    'desc_tip'    => true
                ),
                'paydock_secret_key' => array(
                    'title'       => __( 'PayDock Secret Key', WOOPAYDOCKTEXTDOMAIN ),
                    'type'        => 'password',
                    'description' => __( 'Obtained from your PayDock account. You can set this key by logging into PayDock.', WOOPAYDOCKTEXTDOMAIN ),
                    'default'     => '',
                    'desc_tip'    => true
                ),
                'credit_card' => array(
                    'title'       => __( 'Credit Card', WOOPAYDOCKTEXTDOMAIN ),
                    'label'       => __( 'Enable', WOOPAYDOCKTEXTDOMAIN ),
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'credit_card_gateway_id' => array(
                    'title'       => __( 'Credit Card Gateway ID', WOOPAYDOCKTEXTDOMAIN ),
                    'type'        => 'text',
                    'default'     => '',
                    'desc_tip'    => true
                ),
                'credit_card_email' => array(
                    'title'       => __( 'Credit Card Email', WOOPAYDOCKTEXTDOMAIN ),
                    'label'       => __( 'Enable', WOOPAYDOCKTEXTDOMAIN ),
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'direct_debit' => array(
                    'title'       => __( 'Direct Debit', WOOPAYDOCKTEXTDOMAIN ),
                    'label'       => __( 'Enable', WOOPAYDOCKTEXTDOMAIN ),
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'direct_debit_gateway_id' => array(
                    'title'       => __( 'Direct Debit Gateway ID', WOOPAYDOCKTEXTDOMAIN ),
                    'type'        => 'text',
                    'default'     => '',
                    'desc_tip'    => true
                ),
                'paypal_express' => array(
                    'title'       => __( 'PayPal Express', WOOPAYDOCKTEXTDOMAIN ),
                    'label'       => __( 'Enable', WOOPAYDOCKTEXTDOMAIN ),
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'paypal_express_gateway_id' => array(
                    'title'       => __( 'PayPal Express Gateway ID', WOOPAYDOCKTEXTDOMAIN ),
                    'type'        => 'text',
                    'default'     => '',
                    'desc_tip'    => true
                ),
                'zip_money' => array(
                    'title'       => __( 'Zip Money', WOOPAYDOCKTEXTDOMAIN ),
                    'label'       => __( 'Enable', WOOPAYDOCKTEXTDOMAIN ),
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'zip_money_gateway_id' => array(
                    'title'       => __( 'Zip Money Gateway ID', WOOPAYDOCKTEXTDOMAIN ),
                    'type'        => 'text',
                    'default'     => '',
                    'desc_tip'    => true
                ),
                'zip_money_tokenization' => array(
                    'title'       => __( 'Zip Money Tokenization', WOOPAYDOCKTEXTDOMAIN ),
                    'label'       => __( 'Enable', WOOPAYDOCKTEXTDOMAIN ),
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
            );
        }


        /**
         * Check If The Gateway Is Available For Use
         *
         * @access public
         * @return bool
         */
        function is_available() {

            if ( 'yes' == $this->enabled && in_array( strtoupper( get_woocommerce_currency() ), $this->currency_list )
                && !empty( $this->secret_key ) && !empty( $this->public_key ) && !empty( $this->gateway_id ) ) {
                return true;
            }

            return false;
        }


        /**
         * Payment form on checkout page
         */
        public function payment_fields() {
            if ( $this->has_fields ) {

                if ( $description = $this->get_description() ) {
                    echo wpautop( wptexturize( $description ) );
                }

                $this->supports[] = 'tokenization';

                $this->form();
            }
        }


        /**
         * payment_scripts function.
         *
         * Outputs scripts used for simplify payment
         */
        public function payment_scripts() {
            if ( ! is_checkout() || ! $this->is_available() ) {
                return '';
            }

            wp_enqueue_script( 'js-paydock', 'https://app.paydock.com/v1/paydock.min.js', array(), $this->js_ver, true );
            wp_enqueue_script( 'paydock-token', WP_PLUGIN_URL . '/woocommerce-gateway-paydock/assets/js/paydock_token.js', array('js-paydock'), time(), true );
            wp_localize_script( 'paydock-token', 'paydock', array(
                'publicKey' => $this->public_key,
                'gatewayId' => $this->gateway_id,
                'sandbox'   => 'sandbox' == $this->mode ? true : false,
            ) );

            return '';
        }


        /**
         * Admin Panel Options
         * - Options for bits like 'title' and availability on a country-by-country basis
         */
        function admin_options() {

            if ( 'yes' == $this->enabled && 'sandbox' == $this->mode ) { ?>

                <div class="updated woocommerce-message">
                    <div class="squeezer">
                        <h4><?php _e( 'Note: Now PayDock working in Sandbox mode.', WOOPAYDOCKTEXTDOMAIN ); ?></h4>
                    </div>
                </div>

                <?php
            }

            if ( ! in_array( strtoupper( get_woocommerce_currency() ), $this->currency_list ) ) { ?>

                <div class="error woocommerce-message">
                    <div class="squeezer">
                        <h4>
                            <?php echo __( 'Note: PayDock support only next currencies:', WOOPAYDOCKTEXTDOMAIN ) . ' ' . implode( ', ', $this->currency_list ) ?>
                        </h4>
                    </div>
                </div>

                <?php
            }
            ?>

            <table class="form-table">
                <?php $this->generate_settings_html(); ?>
            </table><!--/.form-table-->
            <?php
        }

        /**
         * Process the payment and return the result.
         *
         * @since 1.0.0
         */
        function process_payment( $order_id ) {

            $order = wc_get_order( $order_id );

            $item_name = sprintf( __( 'Order %s from %s.', WOOPAYDOCKTEXTDOMAIN ), $order->get_order_number(), urlencode( remove_accents( wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ) ) );

            try {

                //make sure token is set at this point
                if ( !isset( $_POST['paydockToken'] ) || empty( $_POST['paydockToken'] ) ) {
                    throw new Exception( __( 'The PayDoc Token was not generated correctly. Please go back and try again.', WOOPAYDOCKTEXTDOMAIN ) );
                }

                $postfields = json_encode( array(
                    'amount'        => (float)$order->get_total(),
                    'currency'      => strtoupper( get_woocommerce_currency() ),
                    'token'         => $_POST['paydockToken'],
                    'reference'     => $item_name,
                    'description'   => $item_name,
                ));

                $args = array(
                    'method'        => 'POST',
                    'timeout'       => 45,
                    'httpversion'   => '1.0',
                    'blocking'      => true,
                    'sslverify'     => false,
                    'body'          => $postfields,
                    'headers'       => array(
                        'Content-Type'      => 'application/json',
                        'x-user-secret-key' => $this->secret_key,
                    ),
                );
                $result = wp_remote_post( $this->api_endpoint . 'v1/charges', $args );

                if ( !empty( $result['body'] ) ) {

                    $res= json_decode( $result['body'], true );

                    if ( !empty( $res['resource']['type'] ) && 'charge' == $res['resource']['type'] ) {
                        if ( !empty( $res['resource']['data']['status'] ) && 'complete' == $res['resource']['data']['status'] ) {

                            $order->payment_complete( $res['resource']['data']['_id'] );

                            // Remove cart
                            WC()->cart->empty_cart();

                            return array(
                                'result'   => 'success',
                                'redirect' => $this->get_return_url( $order )
                            );

                        }

                    } elseif ( !empty( $res['error']['message'] ) ) {

                        throw new Exception( $res['error']['message'] );
                    }
                }

                throw new Exception( __( 'Unknown error', WOOPAYDOCKTEXTDOMAIN ) );

            } catch( Exception $e ) {

                wc_add_notice( __( 'Error:', WOOPAYDOCKTEXTDOMAIN ) . ' ' . $e->getMessage(), 'error' );
            }

            return '';
        }

        //class end
    }
}