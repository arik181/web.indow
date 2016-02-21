<?php // echo $links; ?>
<?php
initializeDataTable($selector       = "#discountsTable", 
                    $ajaxEndPoint   = "/discounts/list_json",
                    $columns        = array("id", 
                                            "modifier_type", 
                                            "description", 
                                            "group", 
                                            "code", 
                                            "amount",
                                            "start_date",
                                            "end_date",
                                            ),
                    $primaryKey     = "id",
                    $actionButtons  = array(array('class' => 'icon',
                                                  'title' => 'View',
                                                  'href'  => '/discounts/edit/',
                                                  'innerHtml'  => '<i class="sprite-icons view"></i>'
                                                 )
                                           ),
                    $actionColumn   = 0,
                    $emptyString    = "There are no discounts/fees available.",
                    $extraCreationJS = "",
                    $dom ='\'<"top"iflp<"clear">>rt<"bottom"p<"clear">>\'',
                    $extra = 2
                    );
?>


<?php if(!empty($discounts)):?>
<table id="discountsTable" class="display table table-hover" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Actions</th>
            <th>Type</th>
            <th>Description</th>
            <th>Group</th>
            <th>Code</th>
            <th>Amount</th>
            <th>Effective</th>
            <th>Expires</th>
        </tr>
    </thead>

</table>
<?php // echo $links; ?>
<?php else:?>
    <div id="list_empty_message">No fees or discounts have been added.</div>
<?php endif;?>
