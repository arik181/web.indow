<div id="wrapper">
<h2>Administration login</h2>
<? if(!empty($message)) { ?>
<div id="infoMessage"><div class="alert"><?php echo $message;?></div></div>
<? }?>
<?php echo form_open("login",array('autocomplete'=>'off'));?>
  <p>
    <?php echo form_label('Email:', 'identity');?>
    <?php echo form_input($identity);?>
  </p>
  <p>
    <?php echo form_label('Password: ', 'password');?>
    <?php echo form_input($password);?>
  </p>
  <!--<p id="rememberP">
    <?php echo form_checkbox('remember', '1', FALSE, 'id="remember"');?>
    <?php echo lang('login_remember_label', 'remember');?>
  </p>-->
  <p><?php echo form_submit(array('name'=>'submit'), lang('login_submit_btn'));?></p>
<?php echo form_close();?>
</div>
