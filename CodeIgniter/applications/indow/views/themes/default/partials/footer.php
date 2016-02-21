

<div class="container">
    <?php $this->load->view('themes/default/blocks/footer-nav');?>
</div>
<script src="<?php echo base_url('assets/theme/default/js/carousel.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/sidebar.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/main.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/notes.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/associated_customers.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/tabbar.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/contactinfo.js');?>"></script>
<?php

    /**
     * Used for module specific JS data )
     */

    if(isset($this->data['phpToJavaScript']))
    {
        if(!is_array($this->data['phpToJavaScript'])){
            echo $this->data['phpToJavaScript'];
        }
    }

    if(!empty($this->data['js_views'])){
        foreach($this->data['js_views'] as $value){
            $this->load->view($value);
        }
    }

?>
<!-- js in footer -->
