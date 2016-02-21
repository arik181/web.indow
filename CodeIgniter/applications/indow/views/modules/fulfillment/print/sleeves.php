<div class="page_cont">
<?php 
initializeDataTable($selector       = "#sleeveTable_" . $order->id,
                    $ajaxEndPoint   = '',
                    $columns        = array("id", 
                                            "room",
                                            "location", 
                                            "width", 
                                            "height",
                                            "checkbox"
                                            ),
                    $primaryKey     = "id",
                    $actionButtons  = array(),
                    $actionColumn   = 0,
                    $emptyString    = "There are no records available.",
                    $extraCreationJS = "",
                    $dom ='""',
                    $extra = 'nopaging'
                    );
?>
<script>
    $(function () {
        var table = $('#sleeveTable_<?= $order->id ?>').DataTable();
        var items = <?= json_encode($items) ?>;
        $.each(items, function (i, item) {
            item.checkbox = '';
            item.width = item.measurements.B ? item.measurements.B : 0;
            item.height = item.measurements.D ? item.measurements.D : 0;
            item.id += "_" + item.sleevecount;
            var j;
            for(j=0; j < item.sleevecount; j++) {
                table.row.add(item).draw();
            }
        });
    });
</script>
<div class="row">
    <div class="col-xs-12">
        <div class="pull-right">
            <div class="order_num_box"><?= $order->id ?></div>
        </div>
        <h3 class="text-center">SLEEVE CUT LIST</h3>
    </div>
</div>
<div class="row">
    <div class="col-xs-10"><br><br>
        <table id="sleeveTable_<?= $order->id ?>" class="tubing_table" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Item #</th>
                    <th>Room</th>
                    <th>Location</th>
                    <th>Width</th>
                    <th>Height</th>
                    <th><i class="fa fa-check"></i></th>
                </tr>
            </thead>
        </table><br><br><br>
        <div id="special_instructions">
            <div style="padding: 4px;" id="special_instructions_heading">Special Instructions</div>
            <div id="special_instructions_content">
                <?= @$order->special_instructions ?>
            </div>
        </div>
    </div>
</div>

</div>