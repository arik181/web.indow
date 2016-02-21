<?
$this->data['delayedload'] = true;
initializeDataTable($selector       = "#customersTable", 
                    $ajaxEndPoint   = "/customers/list_json",
                    $columns        = array("id", 
                                            "name", 
                                            "address", 
                                            "phone", 
                                            "email",
                                            "company"),
                    $primaryKey     = "id",
                    $actionButtons  = array(array('class' => 'icon',
                                                  'title' => 'View',
                                                  'href'  => '/customers/edit/',
                                                  'innerHtml'  => '<i class="sprite-icons view"></i>'
                                                 )
                                           ),
                    $actionColumn   = 0,
                    $emptyString    = "There are no customers available."
                    );
?>
<?php if(!empty($customers)):?>
<table id="customersTable" class="display table table-hover" cellspacing="0" width="100%">
    <thead>
    <tr>
        <th>Actions</th>
        <th>Customer</th>
        <th>Job Site</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Company</th>
    </tr>
    </thead>
</table>
<?php else:?>
    <div id="list_empty_message">No customers have been added.</div>
<?php endif;?>
