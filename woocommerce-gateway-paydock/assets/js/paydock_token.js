var paydock_cc = new paydock.HtmlWidget('#paydock_cc', paydock_object.publicKey, paydock_object.creditGatewayId);
paydock_cc.interceptSubmitForm('#credit_card_form');

if(paydock_object.sandbox == true) {
    paydock_cc.setEnv('sandbox');
}

if(paydock_object.cc_email == true) {
    paydock_cc.setFormFields(['email']);
}

paydock_cc.onFinishInsert('input[name="payment_source_token"]', 'payment_source');
paydock_cc.load();