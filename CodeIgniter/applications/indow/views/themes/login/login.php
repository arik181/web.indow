<div id="login_form_cont">
    <div class="down20 row">
        <div class="col-md-5">
            <form method="post">
                <div class="row nomargin">
                    <div class="col-md-5"><label class="indow-blue" for="identity">Username:</label></div>
                    <div class="col-md-7"><input id="identity" name="identity" class="form-control input-sm"></div>
                </div>
                <div class="row nomargin">
                    <div class="col-md-5"><label class="indow-blue" for="password">Password:</label></div>
                    <div class="col-md-7"><input class="input-sm form-control" type="password" name="password" id="password"></div>
                </div>
                <div class="row nomargin">
                    <div class="col-md-7 col-md-offset-5"><input class="btn-yellow input-sm form-control" type="submit" value="LOGIN"></div>
                </div>
                <div class="row nomargin">
                    <div class="col-md-7 col-md-offset-5 text-center">
                        <a id="forgetpassword" href="#">Forgot your password?</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-5 col-md-offset-1">
            <div class="indow_message">
                <div class="bulletin_caret"></div>
                <? if (empty($customer)) { ?>
                <div style="font-size: 1.4em; line-height: 24px; color: #454545">
		Thank you for your patience, Lead times on new orders are currently 3-4 weeks from the date the order has been approved by Indow. Please communicate with your customers accordingly.
		</div>
                <? } else { ?>
                <div class="indow_message_header">CONGRATULATIONS!</div>
                <div>

                <p>Take the username and password we emailed you and plug them into the boxes on the left.
                Once inside you can enter your measurement values in preparation for your order. Our software
                will detect almost all measurement errors, so you can proceed with confidence.  You can
                either record your measurements directly into the Measure portal or onto a printed worksheet.
                Once your dimensions validate and you confirm your order, then "voila!" We'll start making
                your inserts and you’ll be on your way to comfort and energy efficiency.</p>
                </div>
                <? } ?>
            </div>
        </div>
    </div>
    <div class="row" style="display: none;" id="forget_pass_cont">
        <div>
            Reset your password and have credentials emailed to you.<br>
            Enter the email address associated with your account: <br>
        </div>
        <form id="forget_pass_form">
            <div class="col-md-3"><input id="forget_pass_email" class="form-control input-sm" name="email"></div>
            <div class="col-md-2" style="margin-left: 5px;"><input id="forget_pass_submit" class="form-control input-sm btn-yellow" type="submit" value="Reset Password"></div>
        </form>
    </div>
</div>
