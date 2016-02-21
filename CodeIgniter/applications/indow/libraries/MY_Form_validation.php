<?php

class MY_Form_validation extends CI_Form_validation
{
    public function __construct()
    {
        parent::__construct();
        $this->ci = & get_instance();
        $this->ci->load->database();
    }

    function alpha_dash_space($str)
    {
        return ( ! preg_match("/^([-a-z_ ])+$/i", $str)) ? FALSE : TRUE;
    }

}