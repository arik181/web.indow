<script type="text/template" id="js-subgroup-select-list-item">
    <%- name %>
</script>
<script type="text/template" id="js-subgroup-select-list">
</script>
<script type="text/template" id="js-subgroup-list">
    <button style="display: none;"  id="addnewsubgroup" class="">Add</button>
    <button style="display: none;"  id="sortsubgroups">sort</button>
</script>
<script type="text/template" id="js-subgroup-items">
    <i class="delperm icon fa fa-times"></i> <%- name %> &nbsp;&nbsp; <i class="icon fa fa-bars pull-right">
</script>
<script type="text/template" id="js-subgroup-inputs">
    <input type="hidden" value="<%- id %>" name="assoc_sub_groups_<%- id %>[]" multiple>
    <input type="hidden" value="<%- rank %>" name="assoc_sub_groups_<%- id %>[]" multiple>
</script>