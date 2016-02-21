<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//this file is named helper, but should be named hinderer :p

function initializeDataTable($selector, $ajaxEndPoint, $columns, $primaryKey, $actionButtons, $actionColumn, $emptyString, $extraCreationJS = null, $dom ='\'<"top"iflp<"clear">>rt<"bottom"p<"clear">>\'', $extra = 0, $omitscript = false, $filter = false, $pagination = false, $extraCode = NULL)
{
    $ci =& get_instance();
    $delayedload = !empty($ci->data['delayedload']) ? true : false;
?>
    <?php if (!$omitscript) { ?>
    <script type="text/javascript">
    var selector = '<?= $selector?>';
    $(document).ready(function() {
        
    <?php } ?>
        
        var dataTable = $('<?= $selector?>').DataTable( {
            "initComplete": function () {
                       table_parse();
             },
            <?php if($extra == 1){?>
                    "aaSorting": [[ 1, 'asc' ]],
                    "aoColumnDefs": [
                                { 'bSortable': false, 'aTargets': [ 0 ] },
								{
                                    "aTargets": [ 2 ], // Column to target
                                    "mRender": function ( data, type, full ) {
                                        if(full['user_id'] != null){
                                            return '<a href="/customers/edit/' + full['user_id'] + '" target="_blank">' + data + '</a>';
                                        } else {
                                            return '';
                                        }
                                    }
     							}
                            ],
					
            <?php }
            if ($extra === 'orderlist') { ?>
                "aaSorting": [[ 1, 'desc' ]],
            <? }
            if($extra == 2 || $extra === 'manufacturing'){ ?>
                "aaSorting": [[ 1, 'asc' ]],
                "aoColumnDefs": [
                                { 'bSortable': false, 'aTargets': [ 0 ] }
                                                              
                    ],
                
            <?php }?>
            <?php  if($extra === 'sites'){ ?>
				"aaSorting": [[ 0, 'desc' ]],
                "aoColumnDefs": [
                    {
                        "render": function ( data, type, row ) {
							if (type == "sort" || type == 'type')
								return data;
                            return '<a class="btn btn-sm btn-blue" href="sites/edit/' + row.id + '#orders-anchor"' + ( data == 'none' ? ' disabled="disabled"' : '' ) + '>View</a>';
                        },
                        "targets": 6
                    },
                                        {
                        "render": function ( data, type, row ) {
							if (type == "sort" || type == 'type')
								return data;
                            return '<a class="btn btn-sm btn-blue" href="sites/edit/' + row.id + '#estimates-anchor"' + ( data == 'none' ? ' disabled="disabled"' : '' ) + '>View</a>';
                        },
                        "targets": 7
                    },
                                        {
                        "render": function ( data, type, row ) {
							if (type == "sort" || type == 'type')
								return data;
                            return '<a class="btn btn-sm btn-blue" href="sites/edit/' + row.id + '#quotes-anchor"' + ( data == 'none' ? ' disabled="disabled"' : '' ) + '>View</a>';
                        },
                        "targets": 8
                    }
                                                              
                    ],
                
            <?php }?>
            <?php if( $extra === 'manufacturing' || $extra === 'nopaging'){ ?>
                "iDisplayLength": 9000,
            <? } ?>
            <?php if ($extra == 3) { ?>
                        "columnDefs": [ {
                  "targets": 0,
                  "orderable": false
                } ],
                "iDisplayLength": 50,
            <?php } ?>
            <?php if ($extra == 5) { ?>
                       "order": [[ 1, "desc" ]],
                       "aaSorting": [[ 1, 'asc' ]],
                        "aoColumnDefs": [
                                { 'bSortable': false, 'aTargets': [ 0 ] }
                                                              
                    ],
            <?php } ?>
            <?php 
            if($filter){?>
               "bFilter": false,
               "bPaginate": false,
                
            <?php }?>
            <?php 
            if ( $pagination ) { ?>
                "paging" : true,
                "pagingType" : "full_numbers",
                "displayStart": 0,
            <? } ?>
             <?php if ($delayedload) { ?>
                ajax: function (data, callback, settings, a, b, c, d) {
                    var itable = this;
                    function delayed_load() {
                        var start = window.item_count;
                        var end = Math.min(start + 100, indow_table_data.length);
                        var i;
                        for (i=start; i < end; i++) {
                            itable_api.row.add(indow_table_data[i]);
                        }
                        item_count = end;
                        itable_api.draw();
                        if (end != indow_table_data.length) {
                            setTimeout(delayed_load, 0);
                        }
                    }
                    $.get('<?= $ajaxEndPoint ?>', function(data) {
                        window.indow_table_data = data.data;
                        window.item_count = 0;
                        window.itable_api = itable.api();
                        var i;
                        setTimeout(delayed_load, 0);
                        //callback(data);
                    });
                },
             <? } else { ?>
            "ajax": "<?= $ajaxEndPoint ?>",
            <? } ?>
            "dom": <?= $dom ?>,
            //"paging" : true,
            "pagingType": "full_numbers",
            "displayStart": 0,
            "language":{
                "paginate":{
                    "previous":"&laquo;",
                    "next":"&raquo;",
                },
                "emptyTable": "<?= $emptyString ?>",
            },
            "columns": [
<?
    foreach ($columns as $column)
    {
        echo "{'data':'$column'},";
    }
?>
            ],
            <?
            if (count($actionButtons))
            {
            ?>
            "createdRow": function( row, data, index ) {
                <?
                $html = array();
                foreach ($actionButtons as $button)
                {
                    $html[]= "<a title=\"" . $button['title'] . "\" class=\"" . $button['class'] . "\" href=\"" . $button['href'] . "' + data['" . $primaryKey . "'] + '\">" . $button['innerHtml'] . "</a>";
                }
                $htmlStr = implode(' ', $html);
                ?>
                $('td:eq(<?= $actionColumn?>)', row).html('<?= $htmlStr;?>');
                <?php 
                //TODO: Refactor if time allows. There's a better way to handle this
                if($extraCreationJS) echo $extraCreationJS; 
                ?>
                return row;
            }
            <?
            } elseif ($extraCreationJS) {
                ?>
                    "createdRow": function( row, data, index ) {
                        <?= $extraCreationJS ?>
                    }
                <?
            }
            ?>
        } );
    <?php if (!$omitscript) { ?>
     <?php if ($extra == 4) { ?>
    dataTable.columnFilter({ 	sPlaceHolder: "head:before",
					aoColumns: [ 	null,null,null,null,null,null,null,
				    	 		{ type: "date-range" },null,null,null,null
                                    			
						]

		});
                 $.datepicker.regional[""].dateFormat = 'mm/dd/yy';
    $.datepicker.setDefaults($.datepicker.regional['']);
     <?php } ?>
    <?php if(!is_null($extraCode)){ ?>
        <?= $extraCode ?>
    <?php }?>
    } );
    
    
   
   
    function table_parse(){
        return;
        if((selector == "#combineOrders") || (selector == "#batchOrders")){
            
            var rows = $(selector+" tr:gt(0)"); // skip the header row
            var total_panels = 0;
            var total_sqft = 0;
            var total_weight = 0.00;
            var largest_dimension;
            var subtotal = 0,sqft = 0;
            var max = 0;
            var arr = Array();
            $(selector+" tbody tr").each(function(){

                var panel = parseInt($(this).find('td:eq(7)').html());
                var weight = parseFloat($(this).find('td:eq(8)').html());
                total_panels = (total_panels + panel);
                total_weight = (total_weight + weight);
                var dimension = $(this).find('td:eq(9)').html();
                arr = dimension.split('x');
                sqft = (parseInt(arr[0])*parseInt(arr[1]));
                if(sqft > max){
                    largest_dimension = dimension;
                    max = sqft;
                }
                total_sqft = (total_sqft + sqft);

            });
               // $('#est-terms .total_sqft').text(total_sqft);
                $('#est-terms .total_weight').text(total_weight);
                $('#est-terms .total_panels').text(total_panels);
               // $('#est-terms .largest_dimension').text(largest_dimension);

            
        }
    }
    
    </script>
    <?php } ?>
<?php
}

function my_own_filter($tableselector, $set_val=false) {
    ob_start();
    ?>
    <script type="text/javascript">
//Add Filter for Display TODO: Needs implementation during users phase
    $(document).ready(function() {
       var span = $('<span class="filter">Display </span>')
            .prependTo($('.top .dataTables_filter'));
        var select = $('<select id="my_own_select"><option value="">Select</option><option value="users">My Own</option><option value="">All</option></select>')
            .appendTo(span)
            .on('change', function () {
                var dataTable = $("<?= $tableselector ?>").DataTable();
                var filterColumn = 9; // status column
                var val = $(this).val();
                if (val)
                {
                    val = '^'+$(this).val()+'$';
                }
                dataTable.column( filterColumn )
                    .search( val , true, false )
                    .draw();
            } );
            <?php if ($set_val) { ?>
                select.val('<?= $set_val ?>').change();
            <?php } ?>
    });
</script>
    <?php
    return ob_get_clean();
}
