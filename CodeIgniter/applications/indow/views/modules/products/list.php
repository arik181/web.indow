
<? 
initializeDataTable($selector       = "#productsTable", 
                    $ajaxEndPoint   = "/products/list_json",
                    $columns        = array("product_id", 
                                            "product_name", 
                                            "product_type", 
                                            "abrev",
                                            "opening",
                                            "price_unit",
                                            "min_price",
                        "product_size",
                        "max_width"),
                    $primaryKey     = "id",
                    $actionButtons  = array(array('class' => 'icon',
                                                  'title' => 'View',
                                                  'href'  => '/products/edit/',
                                                  'innerHtml'  => '<i class="sprite-icons view"></i>'
                                                 )
                                           ),
                    $actionColumn   = 0,
                    $emptyString    = "There are no products available."
                    );
?>
<script type="text/javascript">

</script>


<?php if(!empty($products)):?>
<table id="productsTable" class="display table table-hover" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th></th>
            <th>Product</th>
            <th>Product Type</th>
            <th>Code</th>
            <th>Opening Specific</th>
            <th>Unit Price</th>
            <th>Min Price</th>
            <th>Length (in)</th>
            <th>Max Length (in) </th>
        </tr>
    </thead>
</table>
<?php else:?>
    <div id="list_empty_message">No products have been added.</div>
<?php endif;?>

