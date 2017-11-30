<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$settings = array(
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
        'class'       => 'gateway-checkbox',
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
        'class'       => 'gateway-checkbox',
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
        'class'       => 'gateway-checkbox',
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
        'class'       => 'gateway-checkbox',
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

return $settings;