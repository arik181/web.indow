<script src="<?php echo base_url('assets/theme/default/js/jquery.json.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/shared.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/jquery.dataTables.columnFilter.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/fulfillment_manufacturing.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/jquery.sticky.js');?>"></script>
<div id="clonecont">
    <div class="items_table_cont">
        <h4 style="margin: 5px;"><span style="margin-right: 5px">&#8627;</span> Order List View</h4>
        <table class="items_table row display table table-hover" cellspacing="0" style="width: 950px; margin-left: -7px;">
            <thead>
                <tr>
                    <th></th>
                    <th>Item&nbsp;#</th>
                    <th>Room Name</th>
                    <th>Location</th>
                    <th>Product</th>
                    <th>Product Type</th>
                    <th><span title="Special Geometry" class="special-geo">&#x22BE;</span></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
        </table>
    </div>
    <?= form_dropdown('status', $status_options, '', 'class="changedata status_code_options input-sm form-control" style="width: 135px; padding: 4px"') ?>
</div>
<script>
function fix_header() {
    $('#mfgheadertable').html($('#mfgthead').clone(true).attr('id', null).show());
    $('#mfgthead').hide();
    $('#mfgheadertable #check_all').click(function (e) {
        e.stopPropagation();
    });
    var head = $('#manufacturingTable tbody tr');
    if (head.length) {
        head = $(head[0]);
        $('#mfgheadertable th').each(function () {
            var index = $(this).index();
            var width = $(head.children()[index]).width();
            $(this).css('width', width);
        });
    }
}
</script>
<?php 
initializeDataTable($selector       = "#manufacturingTable", 
                    $ajaxEndPoint   = "/fulfillment/manufacturing_json",
                    $columns        = array("created","id", 
                                            "order_number", 
                                            "created",
                                            "commit_date",
                                            "sq_ft",
                                            "created",
                                            "created",
                                            "created",
                                            "created",
                                            "created",
                                            "created",
                                            "created",
                                            "status",
                                            "created_c",
                                            "commit_date_c",
                                            
                                        ),
                    $primaryKey     = "id",
                    $actionButtons  = array(array('class' => 'icon',
                                                  'title' => 'View',
                                                  'href'  => '/orders/edit/',
                                                  'innerHtml'  => '<i class="sprite-icons view"></i>'
                                                 ),
                                            array('class' => 'icon show-items',
                                                'title' => 'Items',
                                                'href' => '#',
                                                'innerHtml' => '<i class="fa fa-list"></i>'
                                            )
                                           ),
                    $actionColumn   = 1,
                    $emptyString    = "There are no records available.",
                    $extraCreationJS = "manufacturing_row(data, row); ",
                    $dom ='\'<"top"if<"clear">>r<"bottom"<"clear">>\'',
                    $extra = 'manufacturing'
                    
                    );
?>
<?php if (isset($message)): ?>
<div class="well" id="flash_data">
    <?php echo $message; ?>
</div>
<?php endif; ?>
<style>
    .expedite-row, .expedite-row:hover, .expedite-row td, .expedite-row td:hover {
        background-color: #FFD5D5 !important;
    }
    #schedulingTable {
        position: relative;
        top: -25px;
    }
    #schedulingTable_wrapper .top {
        position: relative;
        top: -58px;
    }
    #manufacturingTable_wrapper .top, #manufacturingTable_wrapper .bottom {
        position: relative;
        top: -28px;
    }
    #manufacturingTable_wrapper .bottom {
        position: relative;
        top: -70px;
    }
    #manufacturingTable {
        position: relative;
        top: -90px;
    }
    #manufacturingTable {
        table-layout: fixed;
    }
    .items_table_cont table {
        margin-bottom: 0px;
    }
    #manufacturingTable > thead > tr > th:nth-child(n+8),
    #manufacturingTable > tbody > tr > td:nth-child(n+8)) {
        width: 80px;
    }
    #manufacturingTable table > thead > tr > th:nth-child(n+8),
    #manufacturingTable table > tbody > tr > td:nth-child(n+8) {
        width: 55px;
    }

    #manufacturingTable table > tbody > tr > td:last-child, #manufacturingTable table > thead > tr > th:last-child {
        width: 60px;
    }
    .items_table_cont tbody tr {
        background-color: #efefef !important;
    }
    #mfgheader-sticky-wrapper {
        height: 50px !important;
        position:relative;
        z-index:9000;
    }
    
    #manufacturingTable_wrapper {
        position: relative;
        top:-60px;
    }
	#manufacturingTable_filter input {
		width: 120px !important;
	}
	#manufacturingTable_wrapper .top, #manufacturingTable_info {
		display: inline;
		position: relative;
		top: -30px;
	}
	#manufacturingTable td:nth-child(14), #mfgheadertable th:nth-child(14), #manufacturingTable td:nth-child(15), #mfgheadertable th:nth-child(15), #manufacturingTable td:nth-child(16), #mfgheadertable th:nth-child(16) {
		display: none;
	}
	#ui-datepicker-div {
		z-index: 10000 !important;
	}
    
</style>
<script>
    $(document).ready(function(){
       $('.fulfillment_child').show();
       manufacturing_table = $('#manufacturingTable').DataTable(); //set global variable used in fulfillment_production.js. do not remove.
    });
    function draw_row(data, row) {
        var input = $('<input style="width: 90px;" name="commit_date" class="changedata input-sm form-control" type="text">');
        input.val(data.commit_date);
        input.datepicker({dateFormat: 'yy-mm-dd'});

        var build_date = $('<input style="width: 90px;" name="build_date" class="changedata input-sm form-control" type="text">');
        build_date.val(data.build_date);
        build_date.datepicker({dateFormat: 'yy-mm-dd'});
        // var tooltip = $('<i data-toggle=\"tooltip\" data-placement=\"right\" class=\"icon fa fa-info-circle\"></i>').attr('title',data.status_name).tooltip();
        $('td:eq(0)', row).html('<a title="View" class="icon" href="/orders/edit/' + data.id + '"><i class="sprite-icons view"></i></a>');
        $('td:eq(3)', row).html($('#clonecont .status_code_options').clone().val(data.code));
        $('td:eq(7)', row).html(input);
        $('td:eq(8)', row).html(build_date);
        $('td:eq(9)', row).html('<input type=\"checkbox\" class=\"download\" value=\"'+ data['id'] +'\"  />');
        var input = $('<input style="width: 90px;" name="materials_ordered" class="changedata input-sm form-control" type="text">');
        input.val(data.materials_ordered);
        input.datepicker({dateFormat: 'yy-mm-dd'});
       $('td:eq(10)', row).html(input);
       if (parseInt(data.expedite, 10)) {
        $(row).addClass('expedite-row');
       }
    }
</script>
     <?php 
 initializeDataTable($selector       = "#schedulingTable", 
                    $ajaxEndPoint   = "/fulfillment/production_planning_json",
                    $columns        = array("id", 
                                             "number",
                                            "company",
                                            "code", 
                                            "statuschanged",
                                            "panel",
                                            "sqftpanel",
                                            "commit_date",
                                            "build_date",
                                            "id",
                                            "materials_ordered"                                           
                                            ),
                    $primaryKey     = "id",
                    $actionButtons  = array(array('class' => 'icon',
                                                  'title' => 'View',
                                                  'href'  => '/orders/edit/',
                                                  'innerHtml'  => '<i class="sprite-icons view"></i>'
                                                 )
                                           ),
                    $actionColumn   = 0,
                    $emptyString    = "There are no records available.",
                    $extraCreationJS= "
                                        draw_row(data, row);
                                        ",
                    $dom ='\'<"top"il<"clear">>rt<"bottom"p<"clear">>\''
                    );
?>

    
     <script type="text/javascript">
         $(document).ready(function() {
        
            $('#schedulingstatus').on('change', function () {
                var dataTable = $("#schedulingTable").DataTable();
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
			$('#mfgstatus').on('change', function () {
                var dataTable = $("#manufacturingTable").DataTable();
                var filterColumn = 13; // status column
                var val = $(this).val();
                if (val)
                {
                    val = '^'+$(this).val()+'$';
                }
                dataTable.column( filterColumn )
                    .search( val , true, false )
                    .draw();
            } );
			$('#planning_filter_field').change(function () {
				if ($(this).val() == 1) {
					$('#filter_from, #filter_to').val('');
					$('#date_filter').hide();
					$('#non_date_filter').show();
				} else {
					$('#filter_eq').val('');
					$('#non_date_filter').hide();
					$('#date_filter').show();
				}
			});
			$('#manufacturing_filter_field').change(function () {
				if ($(this).val() == 2) {
					$('#filter_from_m, #filter_to_m').val('');
					$('#date_filter_m').hide();
					$('#non_date_filter_m').show();
				} else {
					$('#filter_eq_m').val('');
					$('#non_date_filter_m').hide();
					$('#date_filter_m').show();
				}
			});
            $.fn.dataTable.ext.search.push(
                function( settings, data, dataIndex ) {
                    if (!$('#scheduling_tab_tab').hasClass('active')) {
						var index = $('#manufacturing_filter_field').val();
						var min = $('#filter_from_m').val();
						var max = $('#filter_to_m').val();
						var eq = $('#filter_eq_m').val();
						var commit = data[index];
						console.log(commit, min, max);
				 
						if ( ( index != 2 && ((!min && !max) || (!min && commit <= max) || (min <= commit && !max) || (min <= commit && commit <= max))) ||
							(index == 2 &&  (!eq || commit.indexOf(eq) != -1)	))
						{
							return true;
						}
						return false;
                    }
					
					var index = $('#planning_filter_field').val();
                    var min = $('#filter_from').val();
                    var max = $('#filter_to').val();
					var eq = $('#filter_eq').val();
                    var commit = data[index]; // use data for the age column
             
                    if ( ( index != 1 && ((!min && !max) || (!min && commit <= max) || (min <= commit && !max) || (min <= commit && commit <= max))) ||
						(index == 1 &&  (!eq || commit.indexOf(eq) != -1)	))
                    {
                        return true;
                    }
                    return false;
                }
            );
            var planning_table = $('#schedulingTable').DataTable();
            var mfg_table = $('#manufacturingTable').DataTable();
            $('.filter_date').change( function() {
				if (!$('#scheduling_tab_tab').hasClass('active')) {
					mfg_table.draw();
				} else {
					planning_table.draw();
				}
            });
			$('#filter_eq, #filter_eq_m').keyup( function() {
				if (!$('#scheduling_tab_tab').hasClass('active')) {
					mfg_table.draw();
				} else {
					planning_table.draw();
				}
            });
            $('[name="schedulingTable_length"], [name="manufacturingTable_length"]').addClass('input-sm form-control').css('width', '50px').css('display', 'inline-block').css('padding-left', '2px').css('padding-right', '2px');
            $('#manufacturingTable_filter input').addClass('input-sm form-control inline').css('width', '170px');
			$('#mfg_search').html($('#manufacturingTable_filter'));
        });
</script>   

<div style="margin-top: -50px"></div>
<?= $production_tabbar ?>
<div class="tab-content">
    <div class="association-form tab-pane active content-row" id="scheduling_tab">
    <div id="planning_filter" class="form-inline">
        <div class="col-xs-3 col-xs-offset-3">
            Filter By Status&nbsp;&nbsp;

            <select style="width: 100px" class="input-sm form-control" id="schedulingstatus">
                <option value="">Select</option>
                <option value="300">(300)&nbsp;&nbsp;&nbsp;Order Submitted</option>
                <option value="320">(320)&nbsp;&nbsp;&nbsp;Pending Confirmation</option>
                <option value="330">(330)&nbsp;&nbsp;&nbsp;Final Review</option>
                <option value="350">(350)&nbsp;&nbsp;&nbsp;Order Approved</option>
                <option value="380">(380)&nbsp;&nbsp;&nbsp;Hold - Order Approved</option>
                <option value="400">(400)&nbsp;&nbsp;&nbsp;Order Processing</option>
                <option value="480">(480)&nbsp;&nbsp;&nbsp;Hold - Order Processing</option>
                <option value="500">(500)&nbsp;&nbsp;&nbsp;Manufacturing</option>
                <option value="580">(580)&nbsp;&nbsp;&nbsp;Hold - Manufacturing</option>
                <option value="600">(600)&nbsp;&nbsp;&nbsp;Packaging</option>
                <option value="650">(650)&nbsp;&nbsp;&nbsp;Complete</option>
                <option value="680">(680)&nbsp;&nbsp;&nbsp;Hold - Shipping</option>
            </select>
        </div>
        <div class="pull-right">
            Filter by 
            <select id="planning_filter_field" class="filter_date form-control input-sm">
                <option value="1">Order Num</option>
                <option value="7">Commit Date</option>
                <option value="8">Build Date</option>
            </select>&nbsp;&nbsp;
			<span id="date_filter" style="display: none">
				<input id="filter_from" style="width: 110px" class="filter_date input-sm form-control">&nbsp;&nbsp;to&nbsp;&nbsp;
				<input id="filter_to" style="width: 110px" class="filter_date input-sm form-control">
			</span>
			<span id="non_date_filter">
				<input id="filter_eq" style="width: 110px" class="input-sm form-control">
			</span>
        </div>
    </div>

        <table id="schedulingTable" class="display table table-hover" cellspacing="0" width="100%">
            
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Order</th>
                    <th>Company</th>
                    <th>Status</th>
                    <th>Status Date</th>
                    <th>Panels</th>
                    <th>Sq ft</th>
                    <th>Commit Date</th>
                    <th>Build Date</th>
                    <th>Download</th>
                    <th>Acrylic Delivery</th>
                </tr>
            </thead>
        </table>
        <iframe style="display: none;" id="csv_export_frame"></iframe>
        <iframe style="display: none;" id="dxf_frame"></iframe>
        <button id="planning_download" class="btn btn-default btn-info pull-right">Download</button>
    </div>
    <div class="association-form tab-pane content-row" id="manufacturing_tab">
        <div id="manufacturing_filter" class="form-inline">
            <div class="col-xs-3">
                Filter By Status&nbsp;&nbsp;

                <select style="width: 100px" class="input-sm form-control" id="mfgstatus">
                    <option value="">Select</option>
                    <option value="300">(300)&nbsp;&nbsp;&nbsp;Order Submitted</option>
                    <option value="320">(320)&nbsp;&nbsp;&nbsp;Pending Confirmation</option>
                    <option value="330">(330)&nbsp;&nbsp;&nbsp;Final Review</option>
                    <option value="350">(350)&nbsp;&nbsp;&nbsp;Order Approved</option>
                    <option value="380">(380)&nbsp;&nbsp;&nbsp;Hold - Order Approved</option>
                    <option value="400">(400)&nbsp;&nbsp;&nbsp;Order Processing</option>
                    <option value="480">(480)&nbsp;&nbsp;&nbsp;Hold - Order Processing</option>
                    <option value="500">(500)&nbsp;&nbsp;&nbsp;Manufacturing</option>
                    <option value="580">(580)&nbsp;&nbsp;&nbsp;Hold - Manufacturing</option>
                    <option value="600">(600)&nbsp;&nbsp;&nbsp;Packaging</option>
                    <option value="650">(650)&nbsp;&nbsp;&nbsp;Complete</option>
                    <option value="680">(680)&nbsp;&nbsp;&nbsp;Hold - Shipping</option>
                </select>
            </div>
			<div class="col-xs-3" id="mfg_search"></div>
            <div class="pull-right">
                Filter by 
                <select id="manufacturing_filter_field" class="filter_date form-control input-sm">
                    <option value="2">Order Num</option>
                    <option value="15">Commit Date</option>
                    <option value="14">Build Date</option>
                </select>&nbsp;&nbsp;
        		<span id="date_filter_m" style="display: none">
        			<input id="filter_from_m" style="width: 110px" class="filter_date input-sm form-control">&nbsp;&nbsp;to&nbsp;&nbsp;
        			<input id="filter_to_m" style="width: 110px" class="filter_date input-sm form-control">
        		</span>
        		<span id="non_date_filter_m">
        			<input id="filter_eq_m" style="width: 110px" class="input-sm form-control">
        		</span>
            </div>
        </div>
        <div id="mfgheader" style="z-index:9000; margin-top: 50px;">
            <table style="background-color: white" id="mfgheadertable" class="display table table-hover dataTable no-footer"></table>
        </div>
        <table id="manufacturingTable" class="display table table-hover" cellspacing="0" style="width: 100%; margin-top: 64px;">
            <thead id="mfgthead">
                <tr>
                    <th><input id="check_all" type="checkbox"></th>
                    <th>Actions</th>
                    <th>Order</th>
                    <th>Build Date</th>
                    <th>Commit</th>
                    <th>Sq Ft</th>
                    <th>Tubing</th>
                    <th>Laser</th>
                    <th>Traveler</th>
                    <th>Product Labels</th>
                    <th>Sleeves</th>
                    <th>Sleeve Labels</th>
                    <th>DXF</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Commit</th>

                </tr>
            </thead>

        </table>
        <div class="pull-right action-btn">
            <p>With Selected</p>
            <button id="manufacturing_print" class="btn btn-default btn-info">Print</button>
            <button id="manufacturing_export" class="btn btn-default btn-info">Export</button>
        </div>

        <?= $manufacturing_data ?>

    </div>
</div>
<form id="print_form" style="display: none;" method="post" action="/fulfillment/print_items" target="_blank">
    <input type="hidden" name="data" id="print_data">
</form>

<script src="<?php echo base_url('assets/theme/default/js/delete_confirmer.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/main.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/fulfillment_production.js');?>"></script>
<script>
$(function(){
    $("#mfgheader").sticky({topSpacing:0});
    $('#mfgheadertable').click(function () {
        fix_header();
    });
    $('#manufacturing_tab_tab').click(function () {
        setTimeout(function () {
            fix_header();
        }, 100);
    });
    fix_header();
    $('#manufacturingTable').on('draw.dt', function () {
        fix_header();
    });
});
</script>
