<?php if (isset($message)): ?>
<div class="well" id="flash_data">
    <?php echo $message; ?>
</div>
<?php endif; ?>

<script type="text/javascript">
  $(document).ready(function(){
    var calcSquareFt = function(){
      var pricePerSqft = 25 //TODO, use a non made-up number here
      width = $(".width_input[data-row=" + $(this).data('row') + "]");
      height = $(".height_input[data-row=" + $(this).data('row') + "]");
      sqft = $(".squareFeet[data-row=" + $(this).data('row') + "]");
      price = $(".price[data-row=" + $(this).data('row') + "]");
      sqft_val = parseFloat(width.val()/12 * height.val() /12).toFixed(2);
      price_val = parseFloat(sqft_val * pricePerSqft).toFixed(2);
      sqft.html(sqft_val);
      price.html("$" + price_val);
    }
    $('.width_input').keyup(calcSquareFt);
    $('.height_input').keyup(calcSquareFt);

  });
</script>
<div class="content-short-row row">
  <div class="col-md-12">
    <h3 class="pull-left"><?=$subtitle?></h3>
    <a href="/export" class="btn btn-default btn-header pull-right">Export Estimate Package</a>
    <a href="/discounts" class="btn btn-default btn-header pull-right">Fees &amp; Discounts</a>
    <a href="/mapp" class="btn btn-default btn-header pull-right">Assign for Measurement</a>
  </div>
</div>


<div class="content-row">

<h3>Customer Info</h3>
<input type="text" name="customer_name" placeholder="Search by Customer Email/Name to Add" size="30"> &nbsp;&nbsp;
OR 
<a href="/estimates" class="btn btn-default btn-content">New Customer</a> 

</div>

<div class="content-row">

<table id="itemsTable" class="table table-hover">
    <thead>
    <tr>
        <th><input type="checkbox" name="all"></th>
        <th>Room</th>
        <th>Location</th>
        <th>Width</th>
        <th>Height</th>
        <th>Product</th>
        <th>Product Type</th>
        <th>Tubing</th>
        <th>Special Geom</th>
        <th>Retail</th>
        <th>Sq. Ft</th>
        <th>Assc.Prd</th>
    </tr>
    </thead>
    <tbody>
  
        <tr id="prime">
            <td content="item_id"><input type="checkbox" name="items[]" value="" /></td>
            <td content="room"><input type="text" name="room" /></td>
            <td content="location"><input type="text" name="location[]" /></td>
            <td content="width"><input type="text" class="width_input small_input" name="width[]" data-row="0" size=4 /></td>
            <td content="height"><input type="text" class="height_input small_input" name="height[]" data-row="0" size=4 /></td>
            <td content="product">
             <select name='product[]'>
                <?php
                  foreach($products as $product)
                  {
                    ?>
                      <option value='<?=$product->id?>'><?=$product->name?></option>
                    <?
                  }
                ?>
               </select>  
            </td>
            <td content="product_type">
            	<select name='product_type[]' class='product_type'>
                <?php
                  unset($product);
                  foreach($product_types as $product)
                  {
                    ?>
                      <option value='<?=$product->id?>' data-cost='<?=$product->cost?>'><?=$product->name?></option>
                    <?
                  }
                ?>
              </select>        
            </td>
            <td content="edging">
            	<select name='edging[]'>
                <?php
                  unset($product);
                  foreach($edging as $product)
                  {
                    ?>
                      <option value='<?=$product->id?>'><?=$product->name?></option>
                    <?
                  }
                ?>
               </select>   
            </td>
            <td content="geometry"><input type="checkbox" name="spec_geo"></td>
            <td content="retail" class="price" data-row="0">$0</td>  
            <td content="sqft" class="squareFeet" data-row="0">0</td>
            <td content="product"><input type="button" name="submit" value="Add" class="btn btn-default btn-content"/></td>                      
        </tr>

    </tbody>
</table>

<div class="content-row">
<div id="total_box" class="pull-right">
	<h4 class="inline">Subtotal</h4><span id="subtotal">$00.00</span>
	<br>
	<h4 class="inline">Fees</h4><span id="fees">$00.00</span>
	<br>
	<h3 class="inline">Total</h3><span id="total">$00.00</span>	
</div>
<div class="content-row">
	<input type="button" name="submit" value="Add Window" class="btn btn-default btn-content"/>
</div>
	<div class="content-row">
	<h5>With Selected</h5>
		<input type="button" name="submit" value="Delete" class="btn btn-default btn-content"/>
		<input type="button" name="submit" value="Create New Estimate" class="btn btn-default btn-content"/>
	</div>
	<div class="content-row">
		<h5>Mass Editing</h5>
		<select name='product[]'>
                <?php
                  foreach($products as $product)
                  {
                    ?>
                      <option value='<?=$product->id?>'><?=$product->name?></option>
                    <?
                  }
                ?>
   </select>  
   <select name='product_type[]' class='product_type'>
                <?php
                  unset($product);
                  foreach($product_types as $product)
                  {
                    ?>
                      <option value='<?=$product->id?>' data-cost='<?=$product->cost?>'><?=$product->name?></option>
                    <?
                  }
                ?>
   </select>   
   <select name='edging[]'>
                <?php
                  unset($product);
                  foreach($edging as $product)
                  {
                    ?>
                      <option value='<?=$product->id?>'><?=$product->name?></option>
                    <?
                  }
                ?>
   </select> 
	</div>
</div>



</div>





<div>

   <div class="content-row macrolist_clear">
   <hr>
       <input type="submit" name="submit" value="Save" class="btn btn-default btn-content pull-left"/>
       <a href="/estimates" class="btn btn-default btn-content pull-right">Cancel</a>       
   </div>
</div>

<script src="<?php echo base_url('assets/theme/default/js/delete_confirmer.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/main.js');?>"></script>
