<?php  if (! defined('BASEPATH')) exit('No direct script access allowed');

class CLI_Controller extends REST_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->data['title'] = "";   //overwrite this if you want additional title information
        $this->data["nav"] = "home"; //overwrite this with a key unique to this navigation item (handles selected states for navigation)
        $this->data['auth'] = false;
        $this->data['phpToJavaScript'] = array();
        $this->data['js_header'] = array();
        $this->data['js_footer'] = array();
        $this->data['js_views'] = array();
        date_default_timezone_set('America/Los_Angeles');
    }
}
