jQuery(document).ready(function(){
    var woocommerce_save_button = jQuery('.woocommerce-save-button'),
        submit_block            = jQuery('p.submit');

    function limit_checked_gateways() {
        var woocommerce_paydock_credit_card     = jQuery('#woocommerce_paydock_credit_card').is(':checked'),
            woocommerce_paydock_direct_debit    = jQuery('#woocommerce_paydock_direct_debit').is(':checked'),
            woocommerce_paydock_paypal_express  = jQuery('#woocommerce_paydock_paypal_express').is(':checked'),
            woocommerce_paydock_zip_money       = jQuery('#woocommerce_paydock_zip_money').is(':checked');

        if(woocommerce_paydock_credit_card && woocommerce_paydock_direct_debit && woocommerce_paydock_paypal_express && woocommerce_paydock_zip_money && woocommerce_paydock_zip_money) {
            return true;
        }
    }

    jQuery('input[type=checkbox]').change(function(){
        if(limit_checked_gateways()) {
            woocommerce_save_button.prop('disabled', true);
            submit_block.append('<b style="color:#ff0000;" class="warning">Sorry, you can select maximum 3 payment methods at the same time.</b>')
        } else {
            woocommerce_save_button.prop('disabled', false);
            jQuery('.warning').remove();
        }
    });
});