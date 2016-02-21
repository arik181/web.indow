<?php $curr_uid = $this->ion_auth->get_user_id(); ?>
<script src="/assets/theme/default/js/spin.min.js"></script>
<script src="/assets/theme/default/js/shared.js"></script>
<?php
$this->data['delayedload'] = true;
initializeDataTable($selector       = "#sitesTable", 
                    $ajaxEndPoint   = "/sites/list_json/".$curr_uid,
                    $columns        = array("id", 
                                            "created", 
                                            "name", 
                                            "address", 
                                            "company",
                                            "creator_name",
                                            "orders_count",
                                            "estimate_count",
                                            "quotes_count",
                                            "address_type",
                                            "check_user"),
                    $primaryKey     = "id",
                    $actionButtons  = array(array('class' => 'icon',
                                                  'title' => 'View',
                                                  'href'  => '/sites/edit/',
                                                  'innerHtml'  => '<i class="sprite-icons view"></i>'
                                                 )
                                           ),
                    $actionColumn   = 0,
                    $emptyString    = "There are no sites available.",
                    $extraCreationJS = "
                                        //$('td:eq(6)', row).html('<a class=\"btn btn-sm btn-blue\" href=\"sites/edit/'  + data['id'] + '#orders-anchor\"' + ( data['orders_count'] == 'none' ? ' disabled=\"disabled\"' : '' ) + '>View</a>' );
                                        //$('td:eq(7)', row).html('<a class=\"btn btn-sm btn-blue\" href=\"sites/edit/'  + data['id'] + '#estimates-anchor\"' + ( data['estimate_count'] == 'none' ? ' disabled=\"disabled\"' : '' ) + '>View</a>' );
                                        //$('td:eq(8)', row).html('<a class=\"btn btn-sm btn-blue\" href=\"sites/edit/'  + data['id'] + '#quotes-anchor\"' + ( data['quotes_count'] == 'none' ? ' disabled=\"disabled\"' : '' ) + '>View</a>' );
                                        ",
                    $dom ='\'<"top"iflp<"clear">>rt<"bottom"p<"clear">>\'',
                    $extra = 'sites'
                    );
?>

<script type="text/javascript">
//Add Filter for Display TODO: Needs implementation during users phase
    $(document).ready(function() {
        $('#sitesTable').on('xhr.dt', function ( e, settings, response ) {
            if (!response.data.length) {
                $('#spin_cont').hide();
            }
        });

         var span = $('<span class="filter">Type </span>')
            .prependTo($('.top .dataTables_filter'));
        var select = $('<select><option value="">Select</option><option value="0">Residential</option><option value="1">Commercial</option></select>')
            .appendTo(span)
            .on('change', function () {
                var dataTable = $("#sitesTable").DataTable();
                var filterColumn = 8; // status column
                var val = $(this).val();
                if (val)
                {
                    val = '^'+$(this).val()+'$';
                }
                dataTable.column( filterColumn )
                    .search( val , true, false )
                    .draw();
            } );
               // $('#sites_cont').addspinner();
    });
</script>
<?= my_own_filter('#sitesTable', FALSE); ?>
<?php if(!empty($sites)):?>
<div id="sites_cont">
    <style>
        td:nth-child(10), td:nth-child(11) {
            display: none;
        }
    </style>
    <table id="sitesTable" class="display table table-hover" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>Actions</th>
            <th>Created</th>
            <th>Customer</th>
            <th>Address</th>
            <th>Company</th>
            <th>Creator</th>
            <th>Orders</th>
            <th>Estimates</th>
            <th>Quotes</th>
            <th style="display:none;">Quotes</th>
            <th style="display:none;">User Check</th>
        </tr>
        </thead>

    </table>
</div>
<?php else:?>
    <div id="list_empty_message">No sites have been added.</div>
<?php endif;?>
