<style>
#livesearch {
    position: absolute;
    z-index: 9000;
    background-color: #ffffff;
    padding: 5px;
    border: 1px solid #606c74;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    display: none;
}
#est-terms td:last-child {
    min-width: 60px;
}
</style>

<?php 
if($mode == "post"){
    initializeDataTable($selector       = "#combineOrders", 
                    $ajaxEndPoint   = "/fulfillment/search_order_json?q=".urlencode($query_string),
                    $columns        = array("id","commit_date",
                                            "build_date", 
                                            "status", 
                                            "address",
                                            "name",
                                            "id",
                                            "panel",
                                            "total_weight",
                                            "dimension"
                                            
                                        ),
                    $primaryKey     = "id",
                    $actionButtons  = array(
                                            array('class' => 'icon',
                                                  'title' => 'View',
                                                  'href'  => '/orders/edit/',
                                                  'innerHtml'  => ''
                                                 ),
                                            
                                           ),
                    $actionColumn   = 6,
                    $emptyString    = "There are no records available.",
                    $extraCreationJS = "$('td:eq(6)', row).html('<a class=\"ulist_cell info-link\" href=\"/orders/edit/'  + data['id'] + '\" >' + data['id'] + '</a>');
                                        $('td:eq(8)', row).html(data.total_weight + ' lbs');
                                        $('td:eq(0)', row).html('<input class=\"check-box\"type=\"checkbox\" name=\"'+data['id']+'\">');
                                        $('#hidden-dealer-id').val(data['group_id']);",
                    $dom ='\'<"top"p<"clear">>rt<"bottom"p<"clear">>\'',
                    $extra = 'nopaging',
                    $omitscript = false,
                    $filter = true
                    
                    );
}
?>
<?php if (isset($message)): ?>
<div class="well" id="flash_data">
    <?php echo $message; ?>
</div>
<?php endif; ?>


<div class="association-form tab-pane" id="manufacturing_tab">
                <?= form_open();?>
                <div class="row">
                    <div class="col-md-3">
                    <?= form_label('Filter By Dealer', 'filterbydealer'); ?>
                    <?= form_input(array('name' => 'searchText', 'id' => 'filter-by-dealer', 'class' => 'input-sm form-control', 'value' => @$query_string, 'autocomplete' => 'off')); ?>
                    <div id="livesearch"></div>
                    </div>
                    <div class="col-md-5">
                    <input type="submit" value="Search" class="btn btn-blue btn-header btn-sm"/>
                    </div>
                    
                    <div class="col-md-4">
                            <a class="pull-right btn btn-blue btn-header btn-sm back-btn" href="/logistics">Back to Main Shipping List</a>
                    </div>
                </div><br>
                
                <?= form_close();?>
                
                
            <?php if($mode == "post"){?>
            <div class="col-md-4">
                <div class="results">Results for <strong><?php echo $query_string;?></strong></div>
            </div>
    <form method="post" action="/fulfillment/add_combine_order" >
            <table id="combineOrders" class="display table table-hover" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th><input id="check_all" type="checkbox" name="check-all"/></th>
                        <th>Commit</th>
                        <th>Build</th>
                        <th>Status</th>
                        <th>Ship to Address</th>
                        <th>Customer</th>
                        <th>Order</th>
                        <th>Panels</th>
                        <th>Weight</th>
                        <th>Dimensions</th>
                        
                    </tr>
                </thead>

            </table>
    
            <div class="row">
                <div class="col-xs-12">
                    <table cellpadding="5" cellspacing="5" align="right" valign="top" id="est-terms" >
                        <tr style="border-bottom: 1px solid lightgray;">
                            <td align="left">Est. Total Sq Ft.</td>
                            <td align="right"><span class="total_sqft">N/A</span></td>
                        </tr>
                        <tr style="border-bottom: 1px solid lightgray;">
                            <td align="left">Est. Total Weight (lbs).</td>
                            <td align="right"><span class="total_weight">N/A</span></td>
                        </tr>
                        <tr style="border-bottom: 1px solid lightgray;">
                            <td align="left">Est. Total Items.</td>
                            <td align="right"><span class="total_panels">N/A</span></td>
                        </tr>
                        <tr style="border-bottom: 1px solid lightgray;">
                            <td align="left" style="width: 180px;">Largest Item Dimensions.</td>
                            <td align="right"><span class="largest_dimension">N/A</span></td>
                        </tr>
                    </table>
                </div>
                
                <input type="hidden" name="dealer_id" value="" id="hidden-dealer-id"/>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <a style="margin-left: 0px" class="btn btn-gray btn-header back-btn" href="/logistics">Cancel</a>
                    <div class="pull-right">
                        <input type="submit" class="btn btn-blue btn-header back-btn" value="Combine">
                    </div>
                </div>
            </div>
           </form> 

            <?php }?> 

            <?= $combine_order_data ?>

</div>


<script src="<?php echo base_url('assets/theme/default/js/delete_confirmer.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/main.js');?>"></script>
<script>
$(function(){
    $('#check_all').click(function (e) {
        e.stopPropagation();
        $(this).closest('table').find('.check-box').prop('checked', $(this).prop('checked')).change();
    });
    total_checked_rows('#combineOrders');
    //$('#combineOrders').add_order_totals_function();
    $('#combineOrders').on('change', '.check-box', function () {
        total_checked_rows('#combineOrders');
    });
    $('#filter-by-dealer').on('input', function() {
            if ($('#filter-by-dealer').val() == "") {
                $('#livesearch').html("").hide();
                return false;
            }
            $.ajax({
                url: '/fulfillment/dealer/' + $('#filter-by-dealer').val(),
                data: "POST",
                dataType: "html",
                success: function(data) {
                    $('#livesearch').html("");
                    $('#livesearch').append(data).show();
                }

            });

    }).blur(function() {
        setTimeout(function() {
            $('#livesearch').fadeOut();
        }, 500);
    });
        
    $('body').on('click', '.select-users', function(event){
        var inp_val = $(this).text();
        $('#filter-by-dealer').val(inp_val);
        $('#livesearch').html("").hide();
    });
    $('form').on('submit',function(){
        if($('#filter-by-dealer').val() == ""){
           
            return false;
            
        }

        
    });
    
    

});
</script>
