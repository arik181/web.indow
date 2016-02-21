<?php
include ('header.php');

if(!isset($message))
{
	$message = '<div class="message"></div>';
}
else 
{
	$message = '<div class="message">' . $message . '</div>';
}
?>
<script>
    $(function () {
        $('#forgetpassword').click(function (e) {
            e.preventDefault();
            $('#forget_pass_cont').slideToggle();
        });
        $('#forget_pass_form').submit(function (e) {
            e.preventDefault();
            var submit = $('#forget_pass_submit').prop('disabled', 1);
            $.post('/reset_pass', {email: $('#forget_pass_email').val()}, function (response) {
                $('#forget_pass_email').val('');
                submit.prop('disabled', 0);
                alert(response.message);
            });
        });
    });
</script>
<div class="login_pad"></div>
<? if (empty($customer)) { ?>
<div class="logo_box"><div class="logo_subtitle">by Indow<sup> &reg;</sup></div></div>
<? } else { ?>
<div class="logo_box customer"></div>
<? } ?>
<div class="login_cont">
    <div class="row">
        <div class="login_box row" style="padding: 20px;">
            <? if (empty($customer)) { ?>
            <div class="top_caret"></div>
            <? } ?>
            <div class="row">
                <div class="text-center">
                    <? if (empty($customer)) { ?>
                    <h2 class="indow-blue">Welcome to MODI, the Indow Dealer Portal</h2>
                    <? } else { ?>
                    <h2 style="color: white">Welcome to <span class="indow-blue">MEASURE</span> <span style="font-size: .6em;">by Indow</span></h2>
                    <? } ?>
                </div>
            </div>
            <? if (!empty($fmessage)) { ?>
                <div class="well" id="flash_data"><?= $fmessage ?></div>
            <? } ?>
            <? if (!empty($content_view)) {
                echo $this->load->view($content_view);
            } elseif (!empty($content_message)) {
                echo $content_message;
            } ?>
        </div>
    </div>
    <div class="row text-center address_footer">
        <? if (empty($customer)) { ?>
        <img src="/assets/theme/default/img/indow-grey.png">
        <? } ?>
        2267 N Interstate Ave., Portland, OR 97227&nbsp;&nbsp;&nbsp;&nbsp;W: indowwindows.com&nbsp;&nbsp;&nbsp;&nbsp;T: 503.284.2260
    </div>
</div>

<? include('footer.php'); ?>
