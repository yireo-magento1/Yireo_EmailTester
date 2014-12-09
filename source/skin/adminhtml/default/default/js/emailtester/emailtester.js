$j(function() {

    $j("#customer_search").autocomplete({
        source: emailtester_customer_ajax_url,
        minLength: 1,
        select: function( event, ui ) {
            $j('#customer_id').val(ui.item.id);
        }
    });

    $j("#order_search").autocomplete({
        source: emailtester_order_ajax_url,
        minLength: 1,
        select: function( event, ui ) {
            $j('#order_id').val(ui.item.id);
        }
    });

    $j("#product_search").autocomplete({
        source: emailtester_product_ajax_url,
        minLength: 1,
        select: function( event, ui ) {
            $j('#product_id').val(ui.item.id);
        }
    });
});
