<div class="totallist_view">
    <h4 class="inline totallist_header">Window Measurements</h4>
    <? if (!$new && $this->permissionslibrary->has_view_permission(10)) { ?>
    <? } ?>
            
    <table id="windowTable" class="default_item_table display table table-hover condensed" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th><input type="checkbox" id="checkall"></th>
                <th>Status</th>
                <th>Room<div class="room_req">*required</div></th>
                <th>Location</th>
                <th>Width</th>
                <th>Height</th>
                <th>Product</th>
                <th>Product Type</th>
                <th>Tubing</th>
                <th width="24px"><span title="Special Geometry" class="special-geo">&#x22BE;</span></th>
                <th>Retail</th>
                <th>Details</th>
                <th>Assoc. Product</th>
            </tr>
        </thead>
    </table>
</div>