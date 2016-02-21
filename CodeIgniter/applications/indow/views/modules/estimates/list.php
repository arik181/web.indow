<?php $curr_uid = $this->ion_auth->get_user_id(); ?>
<script>
    //quick estimates info
    var estimates_product_options = '<?= addslashes(str_replace("\n", '', form_dropdown('',$product_options,'','class="form-control input-sm product_options"'))) ?>';
    var estimates_product_type_options = '<?= addslashes(str_replace("\n", '', form_dropdown('product_types_id',$product_type_options,'','class="form-control input-sm product_type_options"'))) ?>';
    var estimates_edging_options = '<?= addslashes(str_replace("\n", '', form_dropdown('edging_id',$edging_options,'','class="form-control input-sm edging_options"'))) ?>';
    var products_info = <?= json_encode($product_info) ?>;
</script>
<style>
    .popover-content td, .popover-content tr, .popover-content td {
        width: 10px !important;
    }
</style>
<script src="/assets/theme/default/js/bootbox.min.js"></script>
<script src="/assets/theme/default/js/spin.min.js"></script>
<script src="/assets/theme/default/js/jquery.json.js"></script>
<!--script src="/assets/theme/default/js/shared.js"></script-->
<script src="/assets/theme/default/js/shared.js"></script>
<script src="/assets/theme/default/js/quick_estimate.js"></script>
<? 
initializeDataTable($selector       = "#estimatesTable",
                    $ajaxEndPoint   = "/estimates/list_json/".$curr_uid . '/' . $order_id ,
                    $columns        = array("id",
                                            "customer_name",
                                            "jobsite",
                                            "status",
                                            "item_count",
                                            "price",
                                            "created",
                                            "created_by",
                                            "dealer",
                                            "check_user"),
                    $primaryKey     = "id",
                    $actionButtons  = array(array('class' => 'icon',
                                                  'title' => 'View',
                                                  'href'  => '/estimates/edit/',
                                                  'innerHtml'  => '<i class="sprite-icons view"></i>'
                                                 )
                                           ),
                    $actionColumn   = 0,
                    $emptyString    = "There are no estimates available.",
                    $extraCreationJS = "
                        $('td:eq(5)', row).html('$' + parseFloat(data.price).toFixed(2));
                        $('td:eq(9)', row).html(data['check_user']).css('display','none'); $('#spin_cont').hide();",
                    $dom ='\'<"top"iflp<"clear">>rt<"bottom"p<"clear">>\'', $extra = 0
                    );
?>
<script type="text/javascript">
//Add Filter for Status
    $(document).ready(function() {
        $('#estimatesTable').on('xhr.dt', function ( e, settings, response ) {
            if (!response.data.length) {
                $('#spin_cont').hide();
            }
        });
        var span = $('<span class="filter">Filter By Status </span>')
            .prependTo($('.top .dataTables_filter'));
        var select = $('<select><option value="">Select</option><option value="Open">Open</option><option value="Closed">Closed</option></select>')
            .appendTo(span)
            .on('change', function () {
                var dataTable = $("#estimatesTable").DataTable();
                var filterColumn = 3; // status column
                var val = $(this).val();
                if (val)
                {
                    val = '^'+$(this).val()+'$';
                }
                dataTable.column( filterColumn )
                    .search( val , true, false )
                    .draw();
            } );
        $('#estimates_cont').addspinner();
    });
</script>

<?= my_own_filter('#estimatesTable', 'users'); ?>

<div id="estimates_cont">
    <table id="estimatesTable" class="display table table-hover" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Actions</th>
                <th>Customer</th>
                <th>Job Site</th>
                <th>Status</th>
                <th>Windows</th>
                <th>Cost</th>
                <th>Created</th>
                <th>Creator</th>
                <th>Dealer</th>
                <th style="display:none;">User Check</th>
            </tr>
        </thead>
    </table>
</div>
<?php $this->data['js_views'][] = 'modules/estimates/quick_estimates_js'; ?>
<div id="clonecont">
    <div id="customer_manager_cont" style="margin: 10px;">
        <?= $this->load->view('modules/customers/customer_manager'); ?>
    </div>
</div>
