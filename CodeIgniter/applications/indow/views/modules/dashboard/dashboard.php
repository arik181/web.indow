
<? 
initializeDataTable($selector       = "#dashboardTable",
                    $ajaxEndPoint   = "/dashboard/list_json",
                    $columns        = array("edit",
                                            "type",
                                            "customer",
                                            "done"),
                    $primaryKey     = "edit",
                    $actionButtons  = array(
                                        array( 'class' => 'icon',
                                               'title' => 'View',
                                               'href'  => '',
                                               'innerHtml'  => '<i class="sprite-icons view"></i>'
                                             )
                                           ),
                    $actionColumn   = 0,
                    $emptyString    = "There are no items to follow up."
                    );
?>

<div class="row show-grid">
    <div class="col-xs-4">
        <div id="stats" class="well dashboard_statistics_image text-right">
            <? /*<span><a href="/estimates" title="estimates">view</a></span>*/ ?>
            <?= img(array('src'=>'assets/img/dashboard/est.png','class' => 'pull-left')); ?>
            <div class="text-center pull-right"><span><?=$estimate_count?></span><br/>Estimates</div>
        </div>
    </div>
    <div class="col-xs-4">
        <div id="stats" class="well dashboard_statistics_image">
             <? /*<span><a href="/quotes" title="quotes">view</a></span>*/ ?>
            <?= img(array('src'=>'assets/img/dashboard/quo.png','class' => 'pull-left')); ?>
            <div class="text-center pull-right"><span><?=$quote_count?></span><br />Quotes</div>
        </div>
    </div>
    <div class="col-xs-4">
        <div id="stats" class="well dashboard_statistics_image">
            <? /* <span><a href="/orders" title="orders">view</a></span>*/ ?>
            <?= img(array('src'=>'assets/img/dashboard/ord.png','class' => 'pull-left')); ?>
            <div class="text-center pull-right"><span><?=$order_count?></span><br />Orders</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-6">
        <h2>Needs Follow Up</h2>
        <div class="dashboard_table_data">
            <table id="dashboardTable" class="display table table-hover" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>View</th>
                        <th>Type</th>
                        <th>Customer</th>
                        <th>Done</th>
                    </tr>

                </thead>
            </table>
        </div>
    </div>
    <div class="col-xs-offset-1 col-xs-5">
        <? if (count($chart_data)) { ?>
        <h2>Orders</h2>
        <div id="flot-placeholder" style="height:170px;width:300px;" class="pull-left"></div>
        <? } ?>
    </div>
</div>
<?

// 12 == SalesForce permission
if ($this->permissionslibrary->has_edit_permission(12)) 
{
    initializeDataTable($selector       = "#dashboardTableTwo", 
                    $ajaxEndPoint   = '',
                    $columns        = array(
                                            "customer",
                                            "site",
                                            "phone_1", 
                                            "email_1", 
                                            "created",
                                        ),
                    $primaryKey     = "created",
                    $actionButtons  = array(),
                    $actionColumn   = null,
                    $emptyString    = "You have 0 new leads from Indow",
                    $extraCreationJS = "
                        $('td:eq(0)', row).html($('<a>').attr('href', '/customers/edit/' + data.customer_id).text(data.customer));
                        $('td:eq(1)', row).html($('<a>').attr('href', '/sites/edit/' + data.site_id).html(data.site));
                    ",
                    $dom ='\'<"top"iflp<"clear">>rt<"bottom"p<"clear">>\'',
                    $extra = 2,
                    $omitscript = false,
                    $pagination     = true,
                    $filter = true
                    
                    );

?>
<script>
    $(function () {
        var dtable = $('#dashboardTableTwo').DataTable();
        var leads = <?= json_encode($sales_leads) ?>;
        $.each(leads, function (i, lead) {
            dtable.row.add(lead).draw();
        });
    });
</script>
<div class="row">
    <div class="col-xs-12">
        <h2>New Sales Leads</h2>
        <div class="dashboard_table_data">
            <table id="dashboardTableTwo" class="display table table-hover" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Job Site</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Created</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<? } ?>

<script>
//     var dataSet = [
//     { label: "Ready-to-ship", data: 25, color: "#005CDE" },
//     { label: "On-hold", data: 45, color: "#00A36A" },
//     { label: "In-Production", data: 3, color: "#7D0096" },
//     { label: "Ship", data: 35, color: "red" } 
// ];

var dataSet = [
<?php
foreach($chart_data as $chart)
{
  ?>
  {label: "<?=$chart['label']?>", data: <?=$chart['data']?>, color: "<?=$chart['color']?>"},
  <?
}
?>
];

var options = {
    series: {
        pie: {
            show: true,
            innerRadius: 0.6,
        }
    }
};

$.fn.showMemo = function () {
    $(this).bind("plothover", function (event, pos, item) {
        if (!item) { return; }
 
        var html = [];
        var percent = parseFloat(item.series.percent).toFixed(2);       
 
        html.push("<div style=\"border:1px solid grey;background-color:",
             item.series.color,
             "\">",
             "<span style=\"color:white\">",
             item.series.label,
             " : ",
             $.formatNumber(item.series.data[0][1], { format: "#,###", locale: "us" }),
             " (", percent, "%)",
             "</span>",
             "</div>");
        $("#flot-memo").html(html.join(''));
    });
}


$(document).ready(function () {
    if (dataSet.length) {
        $.plot($("#flot-placeholder"), dataSet, options);
        $("#flot-placeholder").showMemo();
    }
});

function done(object)
{
  $(object).closest('tr').remove();
  $.get('/dashboard/followup/' + object.id);
}
    
</script>
