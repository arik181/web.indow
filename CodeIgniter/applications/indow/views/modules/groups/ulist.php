<? if(!empty($users)):?>
    <div class="col-md-12">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>Actions</th>
                <th>Name</th>
                <th>Permission Level</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($users as $user):?>
                <tr>
                    <td id="actions"><a class="btn btn-sm btn-info" href="<?php echo base_url('users/edit') . '/' . $user->id;?>"> <i class="sprite-icons view"></i></a></td>
                    <td id="group_name"><? //$user->group_name; ?></td>
                    <td id="first_name"><?= $user->first_name; ?></td>
                    <td id="last_name"><?= $user->last_name; ?></td>
                    <td id="zipcode_1"><?= $user->zipcode_1; ?></td>
                    <td id="username"><?= $user->username; ?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
<div class="row">
<div class="col-md-6 pull-left">
<? if (isset($remove_button)): ?>
    <a href="<?= $remove_path ?>" class="btn btn-default btn-remove pull-right"><?= $remove_button ?></a>
<? endif; ?>
</div>
<? if (isset($links)): ?>
    <div class="row">
        <div class="col-md-6 pull-right">
            <?php echo $links; ?>
        </div>
    </div>
<? endif; ?>
</div>
<?php else:?>
    <p class="message">No users have been added.</p>
<?php endif;?>
