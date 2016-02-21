<?
$groupname = $this->permissionslibrary->get_group_name();
if (!$groupname) {
    $groupname = 'you';
}
?>
<script>
    $(function () {
        $('#eula_modal').modal('show');

        $('#accept_eula').click(function () {
            $.post('/users/accept_eula', function () {
                $('#eula_modal').modal('hide');
            });
        });
    });
</script>
<style>
    .header-bold {
        font-weight: bold;
    }
</style>
<div id="eula_modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-body">

<h4 class="header-bold text-center">
BEFORE USING YOUR PASSWORD, CAREFULLY READ THIS PASSWORD<br>
AGREEMENT GOVERNING YOUR USE AND ACCESS TO THE SITE
</h4>

<p style="margin-top: 10px;">This Password Agreement ("Agreement") governs your use of user names and passwords and all login
information (collectively, "Password") to access the Indow MODI website ("Site"). For the purposes of this
Agreement, "you" refers to the recipient of this Password and "Indow" refers to R Value, Inc.</p>

<h4 class="header-bold text-center">Terms of Password Use</h4>

<p>Except as expressly written below, this Agreement incorporates all the terms and conditions of the
Distribution Agreement and/or Non­Disclosure Agreement between Indow and <?= $groupname ?> 
("NDA"). The Password itself is Confidential Information of Indow (as defined in the NDA). You agree to
protect and not disclose the Password to any co­worker or other person without the express written
permission of Indow.</p>

<p>You are authorized to use the Password only for accessing the Site for the business purposes intended by
Indow. You may not copy or disclose any MODI functionality.</p>

<p>You agree to protect and keep confidential all Confidential Information received by you through the use of
this Password, including without limitation, the MODI functionality, Confidential Information read or
communicated to you through the Site and any such information that you download, transfer or print from
the Site. You shall also keep confidential this Agreement.</p>

<p>Indow may revoke your use rights to the Password and your access to the Site at any time, in its sole
discretion, without notice to you. You agree to stop all use of the Password upon termination of
employment.</p>

<p>Your activity on the Site may be monitored, stored and reviewed for compliance with the NDA. If you receive
Indow Confidential Information through any other means or other Indow sites, it is subject to the obligations
imposed in the NDA. Indow reserves the right to revoke this Agreement or revise any of its terms, in its sole
discretion, at any time without prior notice to you.</p>

<p>Nothing contained herein shall be construed as amending or modifying the terms of the NDA or any related
agreement, and such agreements shall remain in full force and effect. You understand and acknowledge that
no license under any patent, copyright, trade secret or other Indow intellectual property right is granted or
conferred upon you in this Agreement or by the transfer of any information from Indow to you as
contemplated hereunder, either expressly, by implication, inducement, estoppel or otherwise, and that any
license under such intellectual property rights must be express and in writing.</p>

<p>If you have any questions about the terms of the NDA, please contact your employer.</p>

<p>You agree that this Agreement will be a valid and binding agreement when accepted.</p>
<br>
<div class="row">
    <div class="col-xs-12">
        <a href="/logout" class="pull-left btn btn-gray">Decline</a>
        <button id="accept_eula" class="pull-right btn btn-blue">Accept Agreement</button>
    </div>
</div>

            </div>
        </div>
    </div>
</div>