jQuery(document).ready(function () {
    var woocommerce_save_button = jQuery('.woocommerce-save-button'),
        submit_block = jQuery('p.submit');

    function limit_checked_gateways() {
        var checked_gateways = jQuery('.gateway-checkbox:checked').size();

        if (checked_gateways > 3) {
            return true;
        }
    }

    jQuery('.gateway-checkbox').change(function () {
        if (limit_checked_gateways()) {
            woocommerce_save_button.prop('disabled', true);
            submit_block.append('<b style="color:#ff0000;" class="warning">Sorry, you can select maximum 3 payment methods at the same time.</b>')
        } else {
            woocommerce_save_button.prop('disabled', false);
            jQuery('.warning').remove();
        }
    });
});