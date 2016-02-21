<script src="<?php echo base_url('assets/theme/default/js/delete_confirmer.js'); ?>"></script>
<?php if (isset($message)): ?>
<div class="well" id="flash_data">
    <?php echo $message; ?>
</div>
<?php endif; ?>

<?= form_open('', array('id'=>'add_edit_product_form','class'=>'add_edit_product')); ?>
 
    <div class="row">
        <div class="col-xs-5">
            <div class="form-group">
                <?= form_label('Product', 'product_id'); ?>
                <?= form_dropdown('product_id',$product_types,@$edit_contents['product_id'],'class="form-control input-sm"');?>
            </div>
            <div class="form-group">
                <?= form_label('Product Type', 'product_type'); ?>
                <?= form_input('product_type',@$edit_contents['product_type'],'class="form-control input-sm"');?>
            </div>
            <div class="form-group">
                <?= form_label('Product Code', 'abbrev'); ?>
                <?= form_input('abbrev',@$edit_contents['abbrev'],'class="form-control input-sm"');?>
            </div>
            <div class="form-group">
                <?= form_label('Description', 'description'); ?>
                <?= form_textarea(array('name'=>'description', 'rows'=>'3'),@$edit_contents['description'],'class="form-control input-sm"');?>
            </div>

            <!--
            <div class="form-group">
                <?= form_label('Custom Add-on','custom_add_on'); ?>
                <?= form_dropdown('custom_add_on',array('1'=>'Yes','0'=>'No'),'class="form-control input-sm"'); ?>
            </div>
            -->
        </div>
        <div class="col-xs-offset-2 col-xs-5">
            <div class="form-group form-inline">
                <div class="form-group form-group-sm-push">
                    <?= form_label('Opening Specific', 'opening_status'); ?> &nbsp; &nbsp;
                    <input type="radio" name="opening_status" value="1" <?php echo set_radio('opening_status', '0',@$Yes); ?> /> Yes  &nbsp;
                    <input type="radio" name="opening_status" value="2" <?php echo set_radio('opening_status', '1',@$No); ?> /> No  &nbsp;
                    <input type="radio" name="opening_status" value="3" <?php echo set_radio('opening_status', '2',@$Both); ?> /> Both
                </div>
            </div>

            <div class="form-horizontal">
                <div class="form-group">
                    <?= form_label('Unit Price', 'price', array('class'=>'col-xs-12')); ?>
                    <div class="col-xs-9">
                        <?= form_input('unit_price',@$edit_contents['unit_price'],'class="form-control input-sm"');?>
                    </div>
                    <div class="col-xs-3">
                        <? // $units=array('sq ft'=>'Sq ft','unit'=>'Unit','sf'=>'SF','lf'=>'LF','each'=>'Each','pack'=>'Pack','kit'=>'Kit','pair'=>'Pair','set'=>'Set'); ?>
                        <? $units=array('sq ft'=>'Sq ft','unit'=>'Unit'); ?>
                        <?= form_dropdown('unit_price_type',$units,@$edit_contents['unit_price_type'],'class="form-control input-sm"');?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <?= form_label('Min Price', 'min_price'); ?>
                <?= form_input('min_price',@$edit_contents['min_price'], 'class="form-control input-sm"');?>
            </div>

            <div class="form-group-sm-push">Max Size</div>
            <div class="row">
                <div class="col-xs-5">
                    <div class="form-group">
                        <?= form_label('Width (in)', 'max_width'); ?>
                        <?= form_input('max_width',@$edit_contents['max_width'],'class="form-control input-sm"');?>
                    </div>
                </div>
                <div class="col-xs-2">
                    <div class="form-group text-center form-group-sm-push">
                        X
                    </div>
                </div>
                <div class="col-xs-5">
                    <div class="form-group">
                        <?= form_label('Height (in)', 'max_height'); ?>
                        <?= form_input('max_height',@$edit_contents['max_height'],'class="form-control input-sm"');?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?= form_label('Length (in)', 'size'); ?>
                <?= form_input('size',@$edit_contents['size'],'class="form-control input-sm"');?>
            </div>
            <div class="form-group">
                <label for="discontinued_date">Discontinued Date</label>
                <?= form_input('discontinued_date',@$edit_contents['discontinued_date'],'class="form-control input-sm" id="discontinued_date"');?>
                <script>
                $(function () {
                    $('#discontinued_date').datepicker({dateFormat: 'yy-mm-dd'}).change(function() {
                        var val = $(this).val();
                        if (val !== '' && val < '<?= date('Y-m-d')?>') {
                            alert('Setting this date to the past will cause problems in orders between that date and now if the product has been used.');
                        }
                    });
                });
                </script>
            </div>
            <div class="form-group">
                <label for="cut_image_offset">Cut Image Offset (Default 1.375 if left blank)<br>
                1.375=3/16" compression, 1.50=1/8" compression</label>
                <?= form_input('cut_image_offset',@$edit_contents['cut_image_offset'],'class="form-control input-sm" id="cut_image_offset"');?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <hr />
            <div class="inline">
                <input type="submit" value="Save" name="submit" class="btn btn-blue btn-sm pull-right" />
            </div>

            <?php if ($check_page=="edit"): ?>
				<a href="/products/delete/<?= $edit_contents['id'] ?>" class="btn btn-gray btn-sm delete pull-left" style="margin-right: 20px;">Delete Product</a>
            <?php endif; ?>
			<a href="/products" class="btn btn-gray btn-sm pull-left">Cancel</a>
        </div>
    </div>

<?= form_close(); ?>



