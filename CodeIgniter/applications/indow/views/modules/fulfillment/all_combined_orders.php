<?php 
    initializeDataTable($selector       = "#combineOrders", 
                    $ajaxEndPoint   = "/fulfillment/search_combine_order_json/".$group_id,
                    $columns        = array("id","commit_date",
                                            "build_date", 
                                            "status", 
                                            "address",
                                            "name",
                                            "id",
                                            "panel",
                                            "build_date",
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
                                        $('td:eq(8)', row).html('N/A');
                                        $('td:eq(0)', row).html('<input class=\"check-box\"type=\"checkbox\" name=\"'+data['id']+'\">');
                                        $('#hidden-dealer-id').val(data['group_id']);",
                    $dom ='\'<"top"iflp<"clear">>rt<"bottom"p<"clear">>\'',
                    $extra = null,
                    $omitscript = false,
                    $filter = true
                    
                    );

?>
<?php if (isset($message)): ?>
<div class="well" id="flash_data">
    <?php echo $message; ?>
</div>
<?php endif; ?>


<div class="association-form tab-pane content-row" id="manufacturing_tab">
                
                <div class="col-md-4">
                        <a class="btn btn-blue btn-header btn-sm back-btn" href="/logistics" style="float:right">Back to Main Shipping List</a>
                </div>
                
               
                
                
           
          
            <table id="combineOrders" class="display table table-hover" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th><input type="checkbox" name="check-all"/></th>
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
                <table cellpadding="5" cellspacing="5" align="right" valign="top" class="est-terms" >
                    <tr style="border-bottom: 1px solid lightgray;">
                        <td align="left">Est. Total Sq Ft.</td>
                        <td align="right">N/A</td>
                    </tr>
                    <tr style="border-bottom: 1px solid lightgray;">
                        <td align="left">Est. Total Weight (lbs).</td>
                        <td align="right">N/A</td>
                    </tr>
                    <tr style="border-bottom: 1px solid lightgray;">
                        <td align="left">Est. Total Items.</td>
                        <td align="right">N/A</td>
                    </tr>
                    <tr style="border-bottom: 1px solid lightgray;">
                        <td align="left">Largest Item Dimensions.</td>
                        <td align="right">N/A</td>
                    </tr>
               
                    <tr>
                        <td align="right"><input type="submit" class="btn btn-blue btn-header btn-sm back-btn" value="Combine"/></td>
                        <td align="left"><a class="btn btn-blue btn-header btn-sm back-btn" href="javascript:void(0);">Cancel</a></td>
                    </tr>
                </table>
                
            </div>

       

</div>


<script src="<?php echo base_url('assets/theme/default/js/delete_confirmer.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/main.js');?>"></script>
