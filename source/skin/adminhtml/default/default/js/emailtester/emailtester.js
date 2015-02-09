$j(function() {

    $j("#customer_select").change(function() {
        $j('#customer_search').val('');
        $j('#customer_id').val($j('#customer_select').val());
    });

    $j("#customer_search").autocomplete({
        source: emailtester_customer_ajax_url,
        minLength: 1,
        select: function( event, ui ) {
            $j('#customer_id').val(ui.item.id);
            $j('#customer_select').val('');
        }
    });

    $j("#order_select").change(function() {
        $j('#order_search').val('');
        $j('#order_id').val($j('#order_select').val());
    });

    $j("#order_search").autocomplete({
        source: emailtester_order_ajax_url,
        minLength: 1,
        select: function( event, ui ) {
            $j('#order_id').val(ui.item.id);
            $j('#order_select').val('');
        }
    });

    $j("#product_select").change(function() {
        $j('#product_search').val('');
        $j('#product_id').val($j('#product_select').val());
    });

    $j("#product_search").autocomplete({
        source: emailtester_product_ajax_url,
        minLength: 1,
        select: function( event, ui ) {
            $j('#product_id').val(ui.item.id);
            $j('#product_select').val('');
        }
    });
});
