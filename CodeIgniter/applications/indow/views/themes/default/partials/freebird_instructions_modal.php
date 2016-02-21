<?
$groupname = $this->permissionslibrary->get_group_name();
if (!$groupname) {
    $groupname = 'you';
}
?>
<script>
    $(function () {
        $('.show_freebird_instructions').click(function (e) {
            e.preventDefault();
            $('#freebird_instructions_modal').modal('show');
        });
    });
</script>
<style>
    .header-bold {
        font-weight: bold;
    }
</style>
<div id="freebird_instructions_modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-body">
                <!--button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button-->
                <iframe src="http://docs.google.com/gview?url=http://modi.indowwindows.com/assets/instructions.pdf&embedded=true" style="width:100%; height:80%;" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>