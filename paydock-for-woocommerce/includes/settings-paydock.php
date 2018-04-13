<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$settings = array(
    'enabled' => array(
        'title'       => __( 'Enable/Disable', 'paydock-for-woocommerce' ),
        'label'       => __( 'Enable PayDock', 'paydock-for-woocommerce' ),
        'type'        => 'checkbox',
        'description' => '',
        'default'     => 'no'
    ),
    'title' => array(
        'title'       => __( 'Title', 'paydock-for-woocommerce' ),
        'type'        => 'text',
        'description' => __( 'Payment method title that the customer will see on your website.', 'paydock-for-woocommerce' ),
        'default'     => __( 'PayDock', 'paydock-for-woocommerce' ),
        'desc_tip'    => true
    ),
    'sandbox' => array(
        'title'       => __( 'Use Sandbox', 'paydock-for-woocommerce' ),
        'label'       => __( 'Enable sandbox - live payments will not be taken if enabled.', 'paydock-for-woocommerce' ),
        'type'        => 'checkbox',
        'description' => '',
        'default'     => 'no'
    ),
    'paydock_public_key' => array(
        'title'       => __( 'PayDock Public Key', 'paydock-for-woocommerce' ),
        'type'        => 'password',
        'description' => __( 'Obtained from your PayDock account. You can set this key by logging into PayDock.', 'paydock-for-woocommerce' ),
        'default'     => '',
        'desc_tip'    => true
    ),
    'paydock_secret_key' => array(
        'title'       => __( 'PayDock Secret Key', 'paydock-for-woocommerce' ),
        'type'        => 'password',
        'description' => __( 'Obtained from your PayDock account. You can set this key by logging into PayDock.', 'paydock-for-woocommerce' ),
        'default'     => '',
        'desc_tip'    => true
    ),
    'credit_card' => array(
        'title'       => __( 'Credit Card', 'paydock-for-woocommerce' ),
        'label'       => __( 'Enable', 'paydock-for-woocommerce' ),
        'type'        => 'checkbox',
        'class'       => 'gateway-checkbox',
        'description' => '',
        'default'     => 'no'
    ),
    'credit_card_gateway_id' => array(
        'title'       => __( 'Credit Card Gateway ID', 'paydock-for-woocommerce' ),
        'type'        => 'text',
        'default'     => '',
        'desc_tip'    => true
    ),
    'credit_card_email' => array(
        'title'       => __( 'Credit Card Email', 'paydock-for-woocommerce' ),
        'label'       => __( 'Enable', 'paydock-for-woocommerce' ),
        'type'        => 'checkbox',
        'description' => '',
        'default'     => 'no'
    ),
    'direct_debit' => array(
        'title'       => __( 'Direct Debit', 'paydock-for-woocommerce' ),
        'label'       => __( 'Enable', 'paydock-for-woocommerce' ),
        'type'        => 'checkbox',
        'class'       => 'gateway-checkbox',
        'description' => '',
        'default'     => 'no'
    ),
    'direct_debit_gateway_id' => array(
        'title'       => __( 'Direct Debit Gateway ID', 'paydock-for-woocommerce' ),
        'type'        => 'text',
        'default'     => '',
        'desc_tip'    => true
    ),
    'paypal_express' => array(
        'title'       => __( 'PayPal Express', 'paydock-for-woocommerce' ),
        'label'       => __( 'Enable', 'paydock-for-woocommerce' ),
        'type'        => 'checkbox',
        'class'       => 'gateway-checkbox',
        'description' => '',
        'default'     => 'no'
    ),
    'paypal_express_gateway_id' => array(
        'title'       => __( 'PayPal Express Gateway ID', 'paydock-for-woocommerce' ),
        'type'        => 'text',
        'default'     => '',
        'desc_tip'    => true
    ),
    'zip_money' => array(
        'title'       => __( 'Zip Money', 'paydock-for-woocommerce' ),
        'label'       => __( 'Enable', 'paydock-for-woocommerce' ),
        'type'        => 'checkbox',
        'class'       => 'gateway-checkbox',
        'description' => '',
        'default'     => 'no'
    ),
    'zip_money_gateway_id' => array(
        'title'       => __( 'Zip Money Gateway ID', 'paydock-for-woocommerce' ),
        'type'        => 'text',
        'default'     => '',
        'desc_tip'    => true
    ),
    'zip_money_tokenization' => array(
        'title'       => __( 'Zip Money Tokenization', 'paydock-for-woocommerce' ),
        'label'       => __( 'Enable', 'paydock-for-woocommerce' ),
        'type'        => 'checkbox',
        'description' => '',
        'default'     => 'no'
    ),
    'afterpay' => array(
	    'title'       => __( 'AfterPay', 'paydock-for-woocommerce' ),
	    'label'       => __( 'Enable', 'paydock-for-woocommerce' ),
	    'type'        => 'checkbox',
	    'class'       => 'gateway-checkbox',
	    'description' => '',
	    'default'     => 'no'
    ),
    'afterpay_gateway_id' => array(
	    'title'       => __( 'AfterPay Gateway ID', 'paydock-for-woocommerce' ),
	    'type'        => 'text',
	    'default'     => '',
	    'desc_tip'    => true
    ),

);

return $settings;