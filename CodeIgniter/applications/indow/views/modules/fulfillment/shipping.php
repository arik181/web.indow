<?php if (isset($message)): ?>
<div class="well" id="flash_data">
    <?php echo $message; ?>
</div>
<?php endif; ?>

<div class="content-short-row row">
    <h3 class="pull-left"><?=$subtitle?></h3>
    <a href="/export" class="btn btn-default btn-header pull-right">Export Estimate Package</a>
    <a href="/discounts" class="btn btn-default btn-header pull-right">Fees &amp; Discounts</a>
    <a href="/mapp" class="btn btn-default btn-header pull-right">Assign for Measurement</a>
</div>

<div class="content-short-row row">
<select class="pull-right" onChange="location = this.options[this.selectedIndex].value;">
	<option selected disabled>Saved Estimates</option>
	<option value="/saved/<?=@$estimate->id?>/new">Create New Saved Estimate</option>
<?php
	if(!empty($se_dropdown))
	{
		?>
		<option disabled></option>
		<?
		foreach($se_dropdown as $option)
		{
			?>
				<option value="/saved/<?=$option->id?>"><?=$option->name?></option>
			<?
		}
	}
?>
</select>
</div>

<div class="content-row row">
    <div class="inline-block col-xs-4">
            <?= @$contact_info ?>
    </div>
    <div class="inline-block col-xs-4">
            <?= @$site_info ?>
    </div>
</div>

<?  initializeDataTable($selector       = "#itemsTable", 
                        $ajaxEndPoint   = "/estimates/item_list_json",
                        $columns        = array("id", 
                                                "room", 
                                                "location",  // from site
                                                "width",
                                                "height", 
                                                "product", 
                                                "product_type",  // from product
                                                "edging",  // from edging
                                                "special_geom", 
                                                "retail", 
                                                "total_square_feet",
                                                "id"
                                                ), 
                        $primaryKey     = "id",
                        $actionButtons  = array(
                                            array('class' => 'icon',
                                                  'title' => 'View',
                                                  'href'  => '/item/edit/',
                                                  'innerHtml'  => '<i class="sprite-icons view"></i>'
                                                 )
                                               ),
                       $actionColumn   = 0,
                       $extraCreationJS = "
                       $('td:eq(1)', row).html('<input type=\"text\" value=\"' + data['room'] + '\" class=\"\" data-row=\"' + data['id'] + '\"></input>');
                       $('td:eq(2)', row).html('<input type=\"text\" value=\"' + data['location'] + '\" class=\"\" data-row=\"' + data['id'] + '\"></input>');
                       $('td:eq(3)', row).html('<input type=\"text\" value=\"' + data['width'] + '\" class=\"input_small width_input\"  data-row=\"' + data['id'] + '\"></input>');
                       $('td:eq(4)', row).html('<input type=\"text\" value=\"' + data['height'] + '\" class=\"input_small height_input\" data-row=\"' + data['id'] + '\"></input>');
                       $('td:eq(11)', row).html('<a class=\"btn btn-sm btn-default btn-info\" href=\"estimates/edit/'  + data['id'] + '/#add\">Add</a>');
                       ",
                       $dom = '"<rt>"'


                   ); ?>

<div class="content-row row">
<?php if(!empty($items)):?>
<table id="itemsTable" class="display table table-hover" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Actions</th>
            <th>Room</th>
            <th>Location</th>
            <th>Width</th>
            <th>Height</th>
            <th>Product</th>
            <th>Product Type</th>
            <th>Edging</th>
            <th>Special Geom</th>
            <th>Retail</th>
            <th>Sq. Ft</th>
            <th>Product</th>
        </tr>
    </thead>
</table>
<?php else:?>
    <div id="list_empty_message">No estimates have been added.</div>
<?php endif;?>
<?= form_open('new_user_form', array('id'=>'new_user_form','class'=>'new_user_form')); ?>
<?= form_close(); ?>
</div>

<div>
   <div class="content-row">
       <input type="submit" name="submit" value="Save" class="btn btn-default btn-content pull-left"/>
       <?php if (isset($estimate->id)): ?>
       <a href="/estimates/delete/<?= $estimate->id ?>" class="btn btn-default btn-content delete pull-right">Delete Estimate</a>
       <?php endif; ?>
   </div>
</div>

<script src="<?php echo base_url('assets/theme/default/js/delete_confirmer.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/main.js');?>"></script>

