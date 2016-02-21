
<script type="text/template" id="associated-permission-list">
    <thead>
        <tr>
            <th>Actions</th>
            <th>Tool Name</th>
            <th>Permission Level</th>
            <th style="display: none;"><button type="button" id="addpermreal" class="btn btn-default">Add</button></th>
        </tr>
    </thead>
</script>

<script type="text/template" id="associated-permissions-rows">
        <td><i class="delperm icon fa fa-times"></i></td>
        <td>
            <select class="permoption form-control input-sm" style="width: 200px;">
                  <option value="1" <% if (feature_id == 1) { %>selected<% } %>>Customers</option>
                  <option value="2" <% if (feature_id == 2) { %>selected<% } %>>Estimates</option>
                  <option value="3" <% if (feature_id == 3) { %>selected<% } %>>Quotes</option>
                  <option value="4" <% if (feature_id == 4) { %>selected<% } %>>Groups</option>
                  <option value="5" <% if (feature_id == 5) { %>selected<% } %>>Job Sites</option>
                  <option value="6" <% if (feature_id == 6) { %>selected<% } %>>Orders</option>
                  <option value="7" <% if (feature_id == 7) { %>selected<% } %>>Products</option>
                  <option value="8" <% if (feature_id == 8) { %>selected<% } %>>Users</option>
                  <option value="9" <% if (feature_id == 9) { %>selected<% } %>>Fulfillment</option>
                  <option value="10" <% if (feature_id == 10) { %>selected<% } %>>MAPP</option>
                  <option value="11" <% if (feature_id == 11) { %>selected<% } %>>Reports</option>
                  <option value="12" <% if (feature_id == 12) { %>selected<% } %>>SalesForce</option>
                  <option value="13" <% if (feature_id == 13) { %>selected<% } %>>Discounts</option>
                  <option value="14" <% if (feature_id == 14) { %>selected<% } %>>Self Measure</option>
            </select>
        </td>

        <td>
            <select class="permvalue form-control input-sm" style="width: 200px;">
                <option value="1" <% if (permission_level_id == 1) { %>selected<% } %>>None</option>
                <option value="2" <% if (permission_level_id == 2) { %>selected<% } %>>View Own</option>
                <option value="3" <% if (permission_level_id == 3) { %>selected<% } %>>Edit Own</option>
                <option value="4" <% if (permission_level_id == 4) { %>selected<% } %>>View Company</option>
                <option value="6" <% if (permission_level_id == 6) { %>selected<% } %>>View Company and Edit Own</option>
                <option value="5" <% if (permission_level_id == 5) { %>selected<% } %>>Edit Company</option>
            </select>
        </td>

</script>

<script type="text/template" id="associated-permission-inputs">
    <input type="hidden" value="<%- id %>" name="assoc_perms_<%- id %>[]" multiple>
    <input type="hidden" value="<%- permission_level_id %>" name="assoc_perms_<%- id %>[]" multiple>
    <input type="hidden" value="<%- feature_id %>" name="assoc_perms_<%- id %>[]" multiple>
    <input type="hidden" value="<%- permission_preset_id %>" name="assoc_perms_<%- id %>[]" multiple>
</script>
