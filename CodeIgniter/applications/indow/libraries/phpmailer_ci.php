<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/phpmailer/class.phpmailer.php';

class Phpmailer_ci extends PHPMailer
{
    function __construct()
    {
        parent::__construct();
    }
}

?>