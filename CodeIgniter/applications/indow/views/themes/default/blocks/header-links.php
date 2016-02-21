<nav id="top_link_nav">
    <ul class="list-inline">
        <? $user = $this->data['user']; ?>
        <span style="margin-right: 12px;">Logged in as <?= $user->first_name . ' ' . $user->last_name; ?></span>
        <li><a href="/logout">Logout</a></li>
        <li><a href="/profile">Profile</a></li>
        <?php if($this->permissionslibrary->has_view_permission(4)):?>
        <li><a href="/groups/profile">Group Profile</a></li>
        <?php endif;?>
    </ul>
</nav>
<div class="pull-right">
    <? if ($this->permissionslibrary->has_view_permission(10) && empty($this->data['hide_mapp_button'])) { ?>
        <a href="<?= $this->config->item('mapp_url') ?>" target="_blank" class="btn btn-blue btn-header btn-sm">Launch MAPP</a>
    <? } ?>
    <a target="_blank" href="http://www.indowlearningcenter.com" class="btn btn-blue btn-header btn-sm">Learning Center</a>
    <a target="_blank" href="http://www.indowwindows.com/dealer-resources-page/" class="btn btn-blue btn-header btn-sm">Resources</a>
</div>
