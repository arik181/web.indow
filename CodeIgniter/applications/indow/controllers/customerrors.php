<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . 'third_party' . DIRECTORY_SEPARATOR . 'PasswordHash.php';

class CustomErrors extends MM_Controller
{
    function __construct()
    {
        parent::__construct();

        // $this->output->enable_profiler(TRUE);
    }
    public function index_get($errno = "404")
    {
        $errorpages = array("403" => "Permission Denied","404" => "Page Not Found");
        if (!isset($errorpages[$errno]))
            $errno = "404";
        if ($errno == 404 && empty($this->data['user'])) {
            redirect();
        }
        $data = array(
            'content' => 'modules/errors/' . $errno,
        );

        $data['nav']            = "dashboard";
        $data['title']          = 'Error';
        $data['subtitle']       = $errorpages[$errno];
        $data['manager']        = 'Dashboard';

        if (!$this->session->flashdata('message') == FALSE) 
        {
            $data['message'] = $this->session->flashdata('message');
        }

        $this->load->view($this->config->item('theme_list'), $data);
    }
}