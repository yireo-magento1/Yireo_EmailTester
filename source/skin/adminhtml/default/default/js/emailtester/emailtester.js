/**
 * EmailTester extension for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright (c) 2016 Yireo.com
 * @license     Open Software License
 */

/**
 * Initialze EmailTester form
 */
jQuery(function () {

    jQuery("#customer_select").change(function () {
        jQuery('#customer_search').val('');
        jQuery('#customer_id').val(jQuery('#customer_select').val());
    });

    jQuery("#customer_search").autocomplete({
        source: emailtester_customer_ajax_url,
        minLength: 1,
        select: function (event, ui) {
            jQuery('#customer_id').val(ui.item.id);
            jQuery('#customer_select').val('');
        }
    });

    jQuery("#order_select").change(function () {
        jQuery('#order_search').val('');
        jQuery('#order_id').val(jQuery('#order_select').val());
    });

    jQuery("#order_search").autocomplete({
        source: emailtester_order_ajax_url,
        minLength: 1,
        select: function (event, ui) {
            jQuery('#order_id').val(ui.item.id);
            jQuery('#order_select').val('');
        }
    });

    jQuery("#product_select").change(function () {
        jQuery('#product_search').val('');
        jQuery('#product_id').val(jQuery('#product_select').val());
    });

    jQuery("#product_search").autocomplete({
        source: emailtester_product_ajax_url,
        minLength: 1,
        select: function (event, ui) {
            jQuery('#product_id').val(ui.item.id);
            jQuery('#product_select').val('');
        }
    });
});

function emailtesterPrepareForm() {
    if ($$('[name="store"]')[0].value < 1) {
        $$('[name="store"]')[0].value = emailtester_default_store_id;
    }
    return true;
}

function emailtesterEmail() {
    if (emailtesterPrepareForm() == true) {
        $('emailtester_form').writeAttribute('target', '_self');
        emailtesterForm.submit(emailtester_send_url);
        return true;
    }
    return false;
}

function emailtesterPrint() {
    if (emailtesterPrepareForm() == true) {
        $('emailtester_form').writeAttribute('target', '_blank');
        emailtesterForm.submit(emailtester_output_url);
        return true;
    }
    return false;
}

function clearInput(id1, id2, id3) {
    jQuery('#' + id1).val('');
    jQuery('#' + id2).val('');
    jQuery('#' + id3).val('');
    return false;
}