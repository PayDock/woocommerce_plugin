if(paydock_object.gateways.creditCard == 'yes') {
    // Paydock Credit Card gateway
    var paydock_cc = new paydock.HtmlWidget('#paydock_cc', paydock_object.publicKey, paydock_object.creditGatewayId);
    paydock_cc.interceptSubmitForm('#credit_card_form');

    if(paydock_object.sandbox == true) {
        paydock_cc.setEnv('sandbox');
    } else {
        paydock_cc.setEnv('production');
    }

    if(paydock_object.cc_email == true) {
        paydock_cc.setFormFields(['email']);
    }

    paydock_cc.onFinishInsert('input[name="payment_source_token"]', 'payment_source');
    paydock_cc.load();
}

if(paydock_object.gateways.directDebit == 'yes') {
    // Paydock Direct Debit gateway
    var paydock_dd = new paydock.HtmlWidget('#paydock_dd', paydock_object.publicKey, paydock_object.debitGatewayId, 'bank_account');
    paydock_dd.interceptSubmitForm('#direct_debit_form');

    if(paydock_object.sandbox == true) {
        paydock_dd.setEnv('sandbox');
    } else {
        paydock_cc.setEnv('production');
    }

    paydock_dd.onFinishInsert('input[name="debit_source_token"]', 'payment_source');
    paydock_dd.load();
}