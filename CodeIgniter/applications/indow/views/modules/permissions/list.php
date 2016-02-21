<script>
$(document).ready(function() {
    var dataTable = $('#usersTable').dataTable({
        "ajax":     "/permissions/list_json",
        "dom":      '<"top"iflp<"clear">>rt<"bottom"p<"clear">>',
        "pagingType": "full_numbers",
        "language": {
            "paginate":{
                "previous":"&laquo;",
                "next":"&raquo;",
            },
            "emptyTable": "There are no permissions available.",
        },
        "columns": [
{'data':'id'},{'data':'name'},{'data':'groupcount'},{'data':'usercount'}],
                    "createdRow": function( row, data, index ) {
                            $('td:eq(0)', row).html('<a title="Edit" class="icon" href="/permissions/edit/' + data['id'] + '"><i class="sprite-icons view"></i></a>');
                            return row;
        }
                } );
});
</script>
<table id="usersTable" class="display table table-hover dataTable no-footer">
    <thead>
        <tr>
            <th>Actions</th>
            <th>Preset Name</th>
            <th>Attached Groups</th>
            <th>Attached Users</th>
        </tr>
    </thead>
</table>