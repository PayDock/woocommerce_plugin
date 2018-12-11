jQuery(document).ready(function () {
    var body = jQuery('body');

    //maping data
    let requiredFilds = {
        'billing_first_name' : 'first_name',
        'billing_last_name'  : 'last_name',
        'billing_state'  : 'address_state',
        'billing_city': 'address_city',
        'billing_postcode': 'address_postcode',
        'billing_email': 'email',
        'billing_country': 'address_country'
    };

    if (paydock_object.gateways.creditCard === 'yes') {
        // Paydock Credit Card gateway
        var paydock_cc = new paydock.HtmlWidget('#paydock_cc', paydock_object.publicKey, paydock_object.creditGatewayId);

        if (paydock_object.sandbox == true) {
            paydock_cc.setEnv('sandbox');
        } else {
            paydock_cc.setEnv('production');
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
            jQuery('#place_order').submit();
        });

        paydock_cc.load();


        //set value "requiredFilds" in widget
        Object.keys(requiredFilds).forEach(function(key) {
            let _this = jQuery("#" + key);
            let data = [];

            data[requiredFilds[key]] =  _this.val();
            paydock_cc.setFormValues(data);

            switch (_this.prop("tagName"))  {
                case 'INPUT':
                    _this.keyup( function(){
                        let data = [];

                        data[requiredFilds[key]] =  jQuery(this).val() ;
                        paydock_cc.setFormValues(data);
                    });
                    break;
                case 'SELECT':
                    _this.change( function(){
                        let data = [];
                        data[requiredFilds[key]] =  jQuery(this).val() ;
                        paydock_cc.setFormValues(data);
                    });
                    break;
            }
        });
    }

    if (paydock_object.gateways.directDebit === 'yes') {
        // Paydock Direct Debit gateway
        var paydock_dd = new paydock.HtmlWidget('#paydock_dd', paydock_object.publicKey, paydock_object.debitGatewayId, 'bank_account');
        if (paydock_object.sandbox == true) {
            paydock_dd.setEnv('sandbox');
        } else {
            paydock_dd.setEnv('production');
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
            jQuery('#place_order').submit();
        });

        paydock_dd.load();
    }

    body.on('updated_checkout', function () {
        if (paydock_object.gateways.creditCard === 'yes') {
            paydock_cc.reload();
        }

        if (paydock_object.gateways.directDebit === 'yes') {
            paydock_dd.reload();
        }
    });

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
});