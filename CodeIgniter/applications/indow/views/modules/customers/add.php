<?php if (isset($message)): ?>
    <div class="well" id="flash_data">
        <?php echo $message; ?>
    </div>
<?php endif; ?>
<div>
    <?= $this->load->view('modules/customers/addform'); ?>
</div>