<?php
/**
 * WCPayDockGateway
 */
if ( ! class_exists( 'WCPayDockGateway' ) ) {

	class WCPayDockGateway extends WC_Payment_Gateway_CC {

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->currency_list = array( 'AUD', 'USD', 'GBP', 'EUR', 'JPY', 'CAD', 'CHF', 'NZD' );
			$this->js_ver        = '1.0.2';
			$this->method_title  = 'PayDock';
			$this->id            = 'paydock';
			$this->has_fields    = true;
			$this->icon          = WP_PLUGIN_URL . '/woocommerce-gateway-paydock/assets/images/logo.png';

			// Load the form fields.
			$this->init_form_fields();

			// Load the settings.
			$this->init_settings();

			// Define user set variables
			$this->enabled = $this->settings['enabled'];
			$this->title   = trim( $this->settings['title'] );
			$this->mode    = $this->settings['sandbox'] == 'yes' ? 'sandbox' : 'production';

			$this->secret_key = trim( $this->settings['paydock_secret_key'] );
			$this->public_key = trim( $this->settings['paydock_public_key'] );

			if ( 'sandbox' == $this->mode ) {
				$this->api_endpoint = "https://api-sandbox.paydock.com/";
			} else {
				$this->api_endpoint = "https://api.paydock.com/";
			}

			$this->credit_card            = trim( $this->settings['credit_card'] );
			$this->credit_card_gateway_id = trim( $this->settings['credit_card_gateway_id'] );
			$this->credit_card_email      = trim( $this->settings['credit_card_email'] );

			$this->direct_debit            = trim( $this->settings['direct_debit'] );
			$this->direct_debit_gateway_id = trim( $this->settings['direct_debit_gateway_id'] );

			$this->paypal_express            = trim( $this->settings['paypal_express'] );
			$this->paypal_express_gateway_id = trim( $this->settings['paypal_express_gateway_id'] );

			$this->zip_money              = trim( $this->settings['zip_money'] );
			$this->zip_money_gateway_id   = trim( $this->settings['zip_money_gateway_id'] );
			$this->zip_money_tokenization = $this->settings['zip_money_tokenization'];

			if ( $this->credit_card != 'no' ) {
				$this->gateways['credit_card'] = true;
			}

			if ( $this->direct_debit != 'no' ) {
				$this->gateways['direct_debit'] = true;
			}

			if ( $this->paypal_express != 'no' ) {
				$this->gateways['paypal_express'] = true;
			}

			if ( $this->zip_money != 'no' ) {
				$this->gateways['zip_money'] = true;
			}

			if ( $this->enabled ) {
				$this->order_button_text = __( 'Place order with PayDock', WOOPAYDOCKTEXTDOMAIN );
			}

			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array(
				$this,
				'process_admin_options'
			) );

			add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_paydock_scripts' ) );
		}

		/**
		 * admin_paydock_scripts function.
		 *
		 * Outputs scripts used for admin side of WooCommerce panel
		 */
		public function admin_paydock_scripts( $hook ) {
			if ( 'woocommerce_page_wc-settings' != $hook ) {
				return;
			}

			wp_enqueue_script( 'admin-paydock-js', WP_PLUGIN_URL . '/woocommerce-gateway-paydock/assets/js/admin-paydock.js', array( 'jquery' ), WOOPAYDOCK_VER );
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

			wp_enqueue_style( 'paydock-tabs', WP_PLUGIN_URL . '/woocommerce-gateway-paydock/assets/css/tabs.css', array(), WOOPAYDOCK_VER );

			if ( 'sandbox' == $this->mode ) {
				wp_enqueue_script( 'paydock-api', 'https://app-sandbox.paydock.com/v1/widget.umd.js', array(), $this->js_ver, true );
			} else {
				wp_enqueue_script( 'paydock-api', 'https://app.paydock.com/v1/widget.umd.min.js', array(), $this->js_ver, true );
			}

			wp_enqueue_script( 'paydock-js', WP_PLUGIN_URL . '/woocommerce-gateway-paydock/assets/js/paydock.js', array( 'paydock-api' ), time(), true );
			wp_localize_script( 'paydock-js', 'paydock_object', array(
				'gateways'        => array(
					'creditCard'  => $this->credit_card,
					'directDebit' => $this->direct_debit,
				),
				'publicKey'       => $this->public_key,
				'creditGatewayId' => $this->credit_card_gateway_id,
				'debitGatewayId'  => $this->direct_debit_gateway_id,
				'paypalGatewayId' => $this->paypal_express_gateway_id,
				'sandbox'         => 'sandbox' == $this->mode ? true : false,
				'cc_email'        => 'no' == $this->credit_card_email ? 'no' : true,
			) );

			return '';
		}

		/**
		 * init_form_fields function.
		 *
		 * @access public
		 * @return void
		 */
		function init_form_fields() {
			$this->form_fields = include( 'includes/settings-paydock.php' );
		}

		/**
		 * Check If The Gateway Is Available For Use
		 *
		 * @access public
		 * @return bool
		 */
		function is_available() {
			if ( 'yes' == $this->enabled && in_array( strtoupper( get_woocommerce_currency() ), $this->currency_list ) && ! empty( $this->secret_key ) && ! empty( $this->public_key ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Check if Zip Money can be enable with this customer country and WooCommerce currency
		 */
		function is_zipmoney_available( $country = 'AU' ) {
			if ( get_woocommerce_currency() == 'AUD' && $country == 'AU' ) {
				return true;
			}

			return false;
		}

		/**
		 * Get zipmoney tokenization
         */
		function get_zip_money_tokenization() {
		    if ( $this->zip_money_tokenization == 'no' ) {
		        return false;
            } else {
		        return true;
            }
        }

		/**
		 * Print gateway tabs in Checkout page
		 */
		public function tabs() {
			?>
            <div class="paydock">
                <div class="paydock-tab-wrap">
                    <!-- Script for checkout widgets -->
					<?php if ( 'sandbox' == $this->mode ) : ?>
                        <script src="https://app-sandbox.paydock.com/v1/widget.umd.js"></script>
					<?php else : ?>
                        <script src="https://app.paydock.com/v1/widget.umd.min.js"></script>
					<?php endif; ?>

                    <!-- active paydock-tab on page load gets checked attribute -->
					<?php if ( ! empty( $this->gateways['credit_card'] ) ) : ?>
                        <input type="radio" data-gateway="credit_card" id="paydock-tab1" name="paydock-tabGroup1"
                               class="paydock-tab" checked>
                        <label for="paydock-tab1"><?php _e( 'Credit Card', WOOPAYDOCKTEXTDOMAIN ); ?></label>
					<?php endif; ?>

					<?php if ( ! empty( $this->gateways['direct_debit'] ) ) : ?>
                        <input type="radio" data-gateway="direct_debit" id="paydock-tab2" name="paydock-tabGroup1"
                               class="paydock-tab">
                        <label for="paydock-tab2"><?php _e( 'Direct Debit', WOOPAYDOCKTEXTDOMAIN ); ?></label>
					<?php endif; ?>

					<?php if ( ! empty( $this->gateways['paypal_express'] ) ) : ?>
                        <input type="radio" data-gateway="paypal_express" id="paydock-tab3" name="paydock-tabGroup1"
                               class="paydock-tab">
                        <label for="paydock-tab3"><?php $this->paypal_express_button(); ?></label>
					<?php endif; ?>

					<?php if ( ! empty( $this->gateways['zip_money'] ) && $this->is_zipmoney_available( WC()->cart->get_customer()->get_shipping_country() ) ) : ?>
                        <input type="radio" data-gateway="zip_money" id="paydock-tab4" name="paydock-tabGroup1"
                               class="paydock-tab">
                        <label for="paydock-tab4"><?php $this->zip_money_express_button(); ?></label>
					<?php endif; ?>

                    <!-- Tabs content -->
					<?php if ( ! empty( $this->gateways['credit_card'] ) ) : ?>
                        <div class="paydock-tab__content">
							<?php $this->credit_card_form( $args = array(), $fields = array() ); ?>
                        </div>
					<?php endif; ?>

					<?php if ( ! empty( $this->gateways['direct_debit'] ) ) : ?>
                        <div class="paydock-tab__content">
							<?php $this->direct_debit_form(); ?>
                        </div>
					<?php endif; ?>

					<?php if ( ! empty( $this->gateways['paypal_express'] ) ) : ?>
                        <div class="paydock-tab__content">
                            <ol>
                                <li><?php _e( 'Click to tab button', WOOPAYDOCKTEXTDOMAIN ); ?></li>
                                <li><?php _e( 'Pay order in modal window', WOOPAYDOCKTEXTDOMAIN ); ?></li>
                                <li><?php _e( 'If payment was successful you will redirect', WOOPAYDOCKTEXTDOMAIN ); ?></li>
                            </ol>
                        </div>
					<?php endif; ?>

					<?php if ( ! empty( $this->gateways['zip_money'] ) ) : ?>
                        <div class="paydock-tab__content">
                            <ol>
                                <li><?php _e( 'Click to tab button', WOOPAYDOCKTEXTDOMAIN ); ?></li>
                                <li><?php _e( 'Pay order in modal window', WOOPAYDOCKTEXTDOMAIN ); ?></li>
                                <li><?php _e( 'If payment was successful you will redirect', WOOPAYDOCKTEXTDOMAIN ); ?></li>
                            </ol>
                        </div>
					<?php endif; ?>

                    <input type="hidden" name="payment_source">

                    <input type="hidden" name="paydock_gateway">
                </div>
            </div>
			<?php
		}

		/**
		 * ZipMoney Checkout Button for ZipMoney tab
		 */
		public function zip_money_express_button() {
		    $tokenize = $this->get_zip_money_tokenization();
            ?>
            <button type="button" id="zip-money-button">
                <img src="<?php echo plugins_url( 'woocommerce-gateway-paydock/assets/images/zipmoney.png' ); ?>"
                     align="left" style="margin-right:7px;">
            </button>
            <script>
                // woocommerce checkout form fields
                var billing_first_name = '#billing_first_name',
                    billing_last_name = '#billing_last_name',
                    billing_email = '#billing_email',
                    billing_address_1 = '#billing_address_1',
                    billing_address_2 = '#billing_address_2',
                    billing_country = '#billing_country',
                    billing_postcode = '#billing_postcode',
                    billing_city = '#billing_city',
                    billing_state = '#billing_state';

                var paydock_zipmoney = new paydock.ZipmoneyCheckoutButton('#zip-money-button', '<?php echo $this->public_key; ?>', '<?php echo $this->zip_money_gateway_id; ?>');

                paydock_zipmoney.on('click', function(){
                    jQuery("#paydock-tab4").trigger("click");

                    var zipmoney_meta = {
                        "tokenize":<?php echo ( $tokenize == false ? 'false' : 'true' ); ?>,
                        "first_name":jQuery(billing_first_name).val(),
                        "last_name":jQuery(billing_last_name).val(),
                        "email":jQuery(billing_email).val(),
                        "charge": {
                            "amount": "<?php echo WC()->cart->get_cart_contents_total(); ?>",
                            "currency": "<?php echo get_woocommerce_currency(); ?>",
                            "shipping_address":{
                                "first_name":jQuery(billing_first_name).val(),
                                "last_name":jQuery(billing_last_name).val(),
                                "line1":jQuery(billing_address_1).val(),
                                "line2":jQuery(billing_address_2).val(),
                                "country":jQuery(billing_country).val(),
                                "postcode":jQuery(billing_postcode).val(),
                                "city":jQuery(billing_city).val(),
                                "state":jQuery(billing_state).val()
                            },
                            "billing_address":{
                                "first_name":jQuery(billing_first_name).val(),
                                "last_name":jQuery(billing_last_name).val(),
                                "line1":jQuery(billing_address_1).val(),
                                "line2":jQuery(billing_address_2).val(),
                                "country":jQuery(billing_country).val(),
                                "postcode":jQuery(billing_postcode).val(),
                                "city":jQuery(billing_city).val(),
                                "state":jQuery(billing_state).val()
                            }
                        }
                    };

                    // check if some meta is empty
                    jQuery.each(zipmoney_meta, function(k, v) {
                        if(v===""){
                            jQuery('input[name=woocommerce_checkout_place_order]').submit();
                            throw new Error("Validation error!");
                        } else {
                            // zipmoney_meta - json object
                            paydock_zipmoney.setMeta(zipmoney_meta);
                        }
                    });
                });

                paydock_zipmoney.on('error', function (data) {
                    alert('Error! Something went wrong');
                    // work with troubles
                    jQuery('.checkout-overlay.display').remove();
                });

                paydock_zipmoney.onFinishInsert('input[name="payment_source"]', 'payment_source_token');

                paydock_zipmoney.on('finish', function (data) {
                    jQuery('input[name="paydock_gateway"]').val('zip_money');
                    jQuery('input[name=woocommerce_checkout_place_order]').submit();
                });
            </script>
			<?php
		}

		/**
		 * PayPal Checkout Button for Paypal Express tab
		 */
		public function paypal_express_button() {
			?>
            <button type="button" id="paydock-paypal-express">
                <img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="left"
                     style="margin-right:7px;">
            </button>

            <script>
                var paydock_paypal = new paydock.CheckoutButton('#paydock-paypal-express', '<?php echo $this->public_key; ?>', '<?php echo $this->paypal_express_gateway_id ?>');
                paydock_paypal.onFinishInsert('input[name="payment_source"]', 'payment_source_token');

                paydock_paypal.on('click', function () {
                   jQuery('#paydock-tab3').trigger('click');
                });

                paydock_paypal.on('finish', function (data) {
                    jQuery('input[name="paydock_gateway"]').val('paypal_express');
                    jQuery('input[name=woocommerce_checkout_place_order]').submit();
                });
            </script>
			<?php
		}

		/**
		 * Credit Card Form in Credit Card tabs
		 */
		public function credit_card_form( $args = array(), $fields = array() ) {
			?>
            <style>
                iframe {
                    border: 0;
                    width: 100%;
                    height: 300px;
                    z-index: 1;
                }
            </style>
            <div id="paydock_cc"></div>
			<?php
		}

		/**
		 * Direct Debit Form in Direct Debit tabs
		 */
		public function direct_debit_form() {
			?>
            <style>
                iframe {
                    border: 0;
                    width: 100%;
                    height: 300px;
                    z-index: 1;
                }
            </style>
            <div id="paydock_dd"></div>
			<?php
		}

		/**
		 * Payment form on checkout page
		 */
		public function payment_fields() {
			if ( $this->has_fields ) {
				$this->supports[] = 'tokenization';
				$this->tabs();
			}
		}

		/**
		 * Admin Panel Options
		 * - Options for bits like 'title' and availability on a country-by-country basis
		 */
		public function admin_options() {

			if ( 'yes' == $this->enabled && 'sandbox' == $this->mode ) { ?>

                <div class="updated woocommerce-message">
                    <div class="squeezer">
                        <h4><?php _e( 'Note: Now PayDock working in Sandbox mode.', WOOPAYDOCKTEXTDOMAIN ); ?></h4>
                    </div>
                </div>

				<?php
			}

			if ( $this->zip_money != 'no' ) { ?>
                <div class="updated woocommerce-message">
                    <div class="squeezer">
                        <h4><?php _e( 'Note: ZipMoney works only with "AUD" currency and for Australian customers.', WOOPAYDOCKTEXTDOMAIN ); ?></h4>
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

		public function process_payment( $order_id, $retry = true, $force_customer = false ) {
			$order = wc_get_order( $order_id );

			$item_name = sprintf( __( 'Order %s from %s.', WOOPAYDOCKTEXTDOMAIN ), $order->get_order_number(), urlencode( remove_accents( wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ) ) );

			try {
				if ( isset( $_POST['payment_source'] ) ) {
					$token = $_POST['payment_source'];
				} else {
					$token = '';
				}

				switch ( $_POST['paydock_gateway'] ) {
					case 'credit_card':
						$paydock_gateway = __( '(Credit Card)', WOOPAYDOCKTEXTDOMAIN );
						break;
					case 'direct_debit':
						$paydock_gateway = __( '(Direct Debit)', WOOPAYDOCKTEXTDOMAIN );
						break;
					case 'paypal_express':
						$paydock_gateway = __( '(Paypal Express)', WOOPAYDOCKTEXTDOMAIN );
						break;
					case 'zip_money':
						$paydock_gateway = __( '(Zip Money)', WOOPAYDOCKTEXTDOMAIN );
						break;
					default:
						$paydock_gateway = '';
						break;
				}

				//make sure token is set at this point
				if ( empty( $token ) ) {
					throw new Exception( __( 'The PayDock Token was not generated correctly. Please go back and try again.', WOOPAYDOCKTEXTDOMAIN ) );
				}

				$postfields = json_encode( array(
					'amount'      => (float) $order->get_total(),
					'currency'    => strtoupper( get_woocommerce_currency() ),
					'token'       => $token,
					'reference'   => $item_name,
					'description' => $item_name,
					'customer'    => array(
						'first_name' => $order->get_billing_first_name(),
						'last_name'  => $order->get_billing_last_name(),
						'email'      => $order->get_billing_email(),
						'phone'      => $order->get_billing_phone(),
					),
				) );

				$args   = array(
					'method'      => 'POST',
					'timeout'     => 45,
					'httpversion' => '1.0',
					'blocking'    => true,
					'sslverify'   => false,
					'body'        => $postfields,
					'headers'     => array(
						'Content-Type'      => 'application/json',
						'x-user-secret-key' => $this->secret_key,
					),
				);
				$result = wp_remote_post( $this->api_endpoint . 'v1/charges', $args );

				if ( ! empty( $result['body'] ) ) {

					$res = json_decode( $result['body'], true );

					if ( ! empty( $res['resource']['type'] ) && 'charge' == $res['resource']['type'] ) {

						$correct_status = ( 'direct_debit' == $_POST['paydock_gateway'] ) ? 'requested' : 'complete';

						if ( ! empty( $res['resource']['data']['status'] ) && $correct_status == $res['resource']['data']['status'] ) {
							$order->set_payment_method_title( sprintf( __( '%s Payment %s', WOOPAYDOCKTEXTDOMAIN ), $this->method_title, $paydock_gateway ) );
							$order->payment_complete( $res['resource']['data']['_id'] );
							// Remove cart
							WC()->cart->empty_cart();

							return array(
								'result'   => 'success',
								'redirect' => $this->get_return_url( $order )
							);

						}

					} elseif ( ! empty( $res['error']['message'] ) ) {

						throw new Exception( $res['error']['message'] );
					}
				}

				throw new Exception( __( 'Unknown error', WOOPAYDOCKTEXTDOMAIN ) );

			} catch ( Exception $e ) {

				wc_add_notice( __( 'Error:', WOOPAYDOCKTEXTDOMAIN ) . ' ' . $e->getMessage(), 'error' );
			}

			return '';
		}

		//class end
	}
}