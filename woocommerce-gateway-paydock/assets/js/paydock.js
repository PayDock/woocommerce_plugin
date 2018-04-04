jQuery(document).ready(function () {
    var body = jQuery('body');

    body.on('click', '#place_order', function (e) {
        if (jQuery('.woocommerce-checkout').find('#payment_method_paydock').attr('checked') === 'checked') {
            e.preventDefault();

            jQuery('html, body').animate({
                scrollTop: jQuery(".paydock-tab-wrap").offset().top
            }, 500);

            var gateway = jQuery(".paydock-tab:checked").data('gateway');

            switch (gateway) {
                case 'credit_card':
                    paydock_cc.trigger('submit_form');
                    break;
                case 'direct_debit':
                    paydock_dd.trigger('submit_form');
                    break;
                case 'paypal_express':
                    jQuery('#paydock-paypal-express').trigger('click');
                    break;
                case 'zip_money':
                    jQuery('#zip-money-button').trigger('click');
                    break;
                case 'afterpay':
                    jQuery('#afterpay-button').trigger('click');
                    break;
                default:
                    return '';
            }
        }
    });

    body.on('click', '.zip-money-tab', function (e) {
        jQuery(this).find('#zip-money-button').get(0).click();
    });

    body.on('click', '.paypal-express-tab', function (e) {
        jQuery(this).find('#paydock-paypal-express').get(0).click();
    });

    body.on('click', '.afterpay-tab', function (e) {
        jQuery(this).find('#afterpay-button').get(0).click();
    });

    createPaydockCreditCard(paydock_object);
    createPaydockDirectDebit(paydock_object);

    body.on('updated_checkout', function () {
        createPaydockCreditCard(paydock_object);
        createPaydockDirectDebit(paydock_object);
    });
});

var createPaydockCreditCard = function (paydock_object) {
    if (paydock_object.gateways.creditCard === 'yes') {
        // Paydock Credit Card gateway
        var paydock_cc = new paydock.HtmlWidget('#paydock_cc', paydock_object.publicKey, paydock_object.creditGatewayId);

        if (paydock_object.sandbox === true) {
            paydock_cc.setEnv('sandbox');
        }

        if (paydock_object.cc_email === true) {
            paydock_cc.setFormFields(['email']);
        }

        paydock_cc.interceptSubmitForm('#paydock_cc');

        paydock_cc.onFinishInsert('input[name="payment_source"]', 'payment_source');

        paydock_cc.setStyles({
            font_size: '12px',
            background_color: 'rgb(255, 255, 255)'
        });

        paydock_cc.on('finish', function (data) {
            jQuery('input[name="paydock_gateway"]').val('credit_card');
            jQuery('input[name=woocommerce_checkout_place_order]').submit();
            console.log('on:finish', data);
        });

        paydock_cc.load();
    }
};

var createPaydockDirectDebit = function (paydock_object) {
    if (paydock_object.gateways.directDebit === 'yes') {
        // Paydock Direct Debit gateway
        var paydock_dd = new paydock.HtmlWidget('#paydock_dd', paydock_object.publicKey, paydock_object.debitGatewayId, 'bank_account');
        if (paydock_object.sandbox === true) {
            paydock_dd.setEnv('sandbox');
        }

        paydock_dd.setStyles({
            font_size: '12px',
            background_color: 'rgb(255, 255, 255)'
        });

        paydock_dd.setFormFields(['account_bsb']);

        paydock_dd.interceptSubmitForm('#paydock_dd');

        paydock_dd.onFinishInsert('input[name="payment_source"]', 'payment_source');

        paydock_dd.on('finish', function (data) {
            jQuery('input[name="paydock_gateway"]').val('direct_debit');
            jQuery('input[name=woocommerce_checkout_place_order]').submit();
            console.log('on:finish', data);
        });

        paydock_dd.load();
    }
};