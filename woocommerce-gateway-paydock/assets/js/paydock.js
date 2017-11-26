if(paydock_object.gateways.creditCard == 'yes') {
    // Paydock Credit Card gateway
    var paydock_cc = new paydock.HtmlWidget('#paydock_cc', paydock_object.publicKey, paydock_object.creditGatewayId);

    if(paydock_object.sandbox == true) {
        paydock_cc.setEnv('sandbox');
    }

    if(paydock_object.cc_email == true) {
        paydock_cc.setFormFields(['email']);
    }

    paydock_cc.onFinishInsert('input[name="payment_source"]', 'payment_source');

    paydock_cc.on('finish', function (data) {
        jQuery('input[name="paydock_gateway"]').val('credit_card');
        jQuery('input[name=woocommerce_checkout_place_order]').submit();
        console.log('on:finish', data);
    });

    paydock_cc.load();
}

if(paydock_object.gateways.directDebit == 'yes') {
    // Paydock Direct Debit gateway
    var paydock_dd = new paydock.HtmlWidget('#paydock_dd', paydock_object.publicKey, paydock_object.debitGatewayId, 'bank_account');
    if(paydock_object.sandbox == true) {
        paydock_dd.setEnv('sandbox');
    }

    paydock_dd.setFormFields(['account_bsb']);

    paydock_dd.onFinishInsert('input[name="payment_source"]', 'payment_source');

    paydock_dd.on('finish', function (data) {
        jQuery('input[name="paydock_gateway"]').val('direct_debit');
        jQuery('input[name=woocommerce_checkout_place_order]').submit();
        console.log('on:finish', data);
    });

    paydock_dd.load();
}