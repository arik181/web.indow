<?php if(empty($data)){?>
<tr class="new-row-<?php echo $orderid;?>"><td colspan="12"><center>No Records Found</center></td></tr>

<?php }else{?>


<tr class="new-row-<?php echo $orderid;?>"><td colspan="12">Order List View</td></tr>
<!--<table class="order-list-tbl"> -->
    <tr class="new-row-<?php echo $orderid;?>">
        <td class="t1"><input type="checkbox" name="check-all"/></td>
        <td class="t2">Unit</td>
        <td class="t3">Room Name</td>
        <td class="t4">Location</td>
        <td class="t5">Product</td>
        <td class="t6">Product Type</td>
        <td class="t7"></td>
        <td class="t8"></td>
        <td class="t9"></td>
        <td class="t10"></td>
        <td class="t11"></td>
        <td class="t12"></td>
    
        
    </tr>
    
    <?php $i = 0 ;foreach($data as $key => $value){?>
    <tr class="new-row-<?php echo $orderid;?>">
        <td class="t1"><input type="checkbox" name="check"/></td>
        <td class="t2"><?php echo $i;?></td>
        <td class="t3"><span><?php echo $value->room;?></span></td>
        <td class="t4"><?php echo $value->address;?></td>
        <td class="t5"><?php echo $value->product;?></td>
        <td class="t6"><?php echo ucfirst($value->product_type);?></td>
        <td class="t7"><input type="checkbox" name="tubing-sub"/></td>
        <td class="t8"><input type="checkbox" name="laser-sub"/></td>
        <td class="t9"><input type="checkbox" name="traveler-sub"/></td>
        <td class="t10"><input type="checkbox" name="pro-sub"/></td>
        <td class="t11"><input type="checkbox" name="slevees-sub"/></td>
        <td class="t12"><input type="checkbox" name="slevees-label-sub"/></td>
        
    </tr>
    <?php }?>
    
<?php }?>
<!--    
</table>
-->