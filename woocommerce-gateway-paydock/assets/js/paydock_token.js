// this identifies your website in the createToken call below
Paydock.setPublicKey( paydock.publicKey );
Paydock.setSandbox( paydock.sandbox );

function paydockFormHandler() {

    var $form = jQuery( 'form.checkout, form#order_review' );
    var $ccForm = jQuery( '#wc-paydock-cc-form' );

    if ( jQuery( '#payment_method_paydock' ).is( ':checked' ) ) {

        if ( 0 === jQuery( 'input.paydockToken' ).size() ) {

            $form.block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });

            var paydock_card_expiry = jQuery( '#paydock-card-expiry' ).val().split( '/' );

            // create token with credit card
            Paydock.createToken({
                    gateway_id: paydock.gatewayId,
                    type: "card",
                    card_name: jQuery( '#billing_first_name' ).val() + ' ' + jQuery( '#billing_last_name' ).val(),
                    card_number: jQuery( '#paydock-card-number' ).val().replace(/\s/g, ''),
                    expire_month: parseInt( paydock_card_expiry[0] ),
                    expire_year: parseInt( paydock_card_expiry[1] ),
                    card_ccv: jQuery( '#paydock-card-cvc' ).val(),
                    first_name: jQuery( '#billing_first_name' ).val(),
                    last_name: jQuery( '#billing_last_name' ).val(),
                    details: 'order 123',
                    email: jQuery( '#billing_email' ).val()
                },
                function ( token, status ) { // success callback

                     //Insert the token into the form so it gets submitted to the server
                    $ccForm.append( '<input type="hidden" class="paydockToken" name="paydockToken" value="' + token + '"/>' );
                    $form.submit();

                    //console.log( 'token', token);
                },
                function ( res, status ) { // error callback

                    // Show the errors on the form
                    jQuery( '.woocommerce-error, .paydockToken', $ccForm ).remove();
                    $form.unblock();

                    $ccForm.prepend( '<ul class="woocommerce-error"><li>' + res.message + '</li></ul>' );

                    //console.log('errors', res);
                }
             );

            // Prevent the form from submitting
            return false;
        }
    }
    return true;

}

jQuery( function () {

    /* Checkout Form */
    jQuery( 'form.checkout' ).on( 'checkout_place_order_paydock', function () {
        return paydockFormHandler();
    });

    /* Pay Page Form */
    jQuery( 'form#order_review' ).on( 'submit', function () {
        return paydockFormHandler();
    });

    /* Both Forms */
    jQuery( 'form.checkout, form#order_review' ).on( 'change', '#wc-paydock-cc-form input', function() {
        jQuery( '.paydockToken' ).remove();
    });

    jQuery( 'form.checkout, form#order_review' ).on( 'blur', '#wc-paydock-cc-form input', function() {
        jQuery( '.paydockToken' ).remove();
    });

});