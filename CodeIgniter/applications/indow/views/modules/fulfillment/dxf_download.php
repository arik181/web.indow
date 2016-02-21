<!doctype html>
<html>
    <head>
        <title>DXF Download</title>
        <script src="<?php echo base_url('assets/theme/default/js/jquery.js');?>"></script>
        <script src="<?php echo base_url('assets/theme/default/js/shared.js');?>"></script>
        <script src="<?php echo base_url('assets/theme/default/js/cut_image.js');?>"></script>
        <script src="<?php echo base_url('assets/theme/default/js/fulfillment_dxf.js');?>"></script>
    </head>
    <body>
        <form method="post" id="postform">
            <input type="hidden" name="data" id="data">
        </form>
        <? $this->load->view('/themes/default/partials/traveler_hidden') ?>
        <? foreach ($items as $item) { ?>
            <div style="float: left" class="cut_image_cont" data-sheet-width="<?= $item->sheet_width ?>" data-id="<?= $item->id ?>" data-sheet-height="<?= $item->sheet_height ?>" data-cuts="<?= $item->cuts ?>"></div>
        <? } ?>
    </body>
</html>