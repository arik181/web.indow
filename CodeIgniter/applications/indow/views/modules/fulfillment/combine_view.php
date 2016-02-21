<?php 
if($mode == "get"){
    initializeDataTable($selector       = "#batchOrders", 
                    $ajaxEndPoint   = "/fulfillment/batch_order_json/".$batch_id,
                    $columns        = array("combine_id",
                                            "commit_date",
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
                                        $('td:eq(0)', row).html('<input class=\"check-box\"type=\"checkbox\" name=\"'+data['combine_id']+'\">');
                                        $('#hidden-dealer-id').val(data['group_id']);",
                    $dom ='\'<"top"iflp<"clear">>rt<"bottom"p<"clear">>\'',
                    $extra = 2,
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


<div style="margin-top: -50px;" class="association-form tab-pane" id="manufacturing_tab">
    <a class="pull-right btn btn-blue btn-header btn-sm back-btn" href="/logistics">Back to Main Shipping List</a><br>
                
           
    <form method="post" action="" id="remove-combine-order">
            <table id="batchOrders" class="display table table-hover" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="check_all" name="check-all"/></th>
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
        <div>
         <input style="margin-left: 0px" type="submit" value="Remove from Combined List" class="btn btn-blue btn-header back-btn remove-combine"/>
         <!-- <a class="btn btn-blue btn-header btn-sm back-btn" href="javascript:void(0);">Remove from Combined List</a>  --> 
        </div>
    </form>
            <div>
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
                        <td align="left">Largest Item Dimensions.</td>
                        <td align="right"><span class="largest_dimension">N/A</span></td>
                    </tr>
               
                    <!--tr>
                        <td align="right"><input type="submit" class="btn btn-blue btn-header btn-sm back-btn" value="Save"/></td>
                        <td align="left"><a class="btn btn-blue btn-header btn-sm back-btn" href="/shipping">Cancel</a></td>
                    </tr-->
                </table>
                
                <input type="hidden" name="dealer_id" value="" id="hidden-dealer-id"/>
            </div>

            <?= $combine_order_data ?>

</div>


<script src="<?php echo base_url('assets/theme/default/js/delete_confirmer.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/main.js');?>"></script>
<script>
$(function(){
    $('#batchOrders').add_order_totals_function();
    $('#remove-combine-order').on('submit', function(){
      if (confirm("Are you sure want to delete?")){
         return true;
      }
       return false; 
    });
    $('.check_all').click(function () {
        $(this).closest('table').find('.check-box').prop('checked', $(this).prop('checked'));
    });
});
</script>
