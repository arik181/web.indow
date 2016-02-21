<!DOCTYPE html> 
<html>
    <head>
        <title>Indow</title>
        <?= $this->load->view('/themes/default/partials/header'); ?>
        <script src="/assets/theme/default/js/spin.min.js"></script>
        <script src="/assets/theme/default/js/shared.js"></script>
        <style>
            body {
                background: none !important;
            }
        </style>
        <script>
            $(function () {
                $('#main-modal').modal('show');
                $('#run_image_calcs').click(function (e) {
                    e.preventDefault();
                    $('#spinner').addcontentspinner();
                    $('#spinner').show();
                    $('#error_modal_title').html('Running cut calculations...');
                    $('#error_modal_message').html('The cut calculations are being run and the image will appear within several minutes.  Please do not navigate away from this page during this time.');
                    $.get('/fulfillment/force_cut_script/' + $(this).data('image_id'), function () {
                        window.location.reload();
                    });
                });
            });
        </script>
    </head>
    <body>
    
<div id="main-modal" class="modal fade" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <!--button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button-->
        <h4 id="error_modal_title" class="modal-title">Error</h4>
      </div>
      <div id="error_modal_body" class="modal-body">
        <p id="error_modal_message"><?= $message ?></p>
        <div id="spinner" style="display: none; position: relative; height: 150px;"></div>
      </div>
      <!--div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div-->
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
    </body>
</html>