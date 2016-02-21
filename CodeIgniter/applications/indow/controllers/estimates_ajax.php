<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . 'third_party' . DIRECTORY_SEPARATOR . 'PasswordHash.php';

class Estimates_ajax extends MM_Controller
{
    protected $_model;
    
    function __construct()
    {
        parent::__construct();

        $this->load->model(array('user_model', 'estimate'));
        $this->load->helper(array('language', 'macrolist', 'totallist', 'info'));

        $this->estimate = $this->estimate;
        $this->configure_pagination('/estimates/list','estimates');

        $this->_model = $this->estimate;
    }

    public function index_get($start = 0, $limit = 25)
    {
        if(!$this->data['auth'])
        {
            redirect();
        }

        $data = array( 'content' => 'modules/estimates/list',);

        $this->pagination->per_page = $limit;

        $data["estimates"] = $this->estimate->fetch_estimates($limit, $start);

        $data["links"] = $this->pagination->create_links($start);

        if (($this->pagination->cur_page) === 0)
        {
            $data["first_row"] = 1;

        } else {

            $data["first_row"] = (($this->pagination->cur_page - 1) * $this->pagination->per_page) + 1;
        }

        $data["per_page"]       = $this->pagination->per_page;
        $data["status_filters"] = $this->pagination->per_page;
        $data["total_rows"]     = $this->pagination->total_rows;

        $data["last_row"]       = $data["first_row"] + $this->pagination->per_page - 1;

        if ($data["total_rows"] < $data["last_row"])
        {
            $data["last_row"]   = $data["total_rows"];
        }

        $data['nav']            = "estimates";
        $data['title']          = 'Estimates';
        $data['subtitle']       = 'Estimate List';
        $data['edit_path']      = '/estimates/edit';
        $data['add_path']       = '/estimates/add';
        $data['form']           = '/estimates/list';
        $data['form']           = '/estimates/list';
        $data['delete_path']    = '/estimates/delete';
        $data['add_button']     = 'Add Estimate';
        $data['manager']        = 'Estimates';
        $data['section']        = 'Estimate List';

        if (!$this->session->flashdata('message') == FALSE) 
        {
            $data['message'] = $this->session->flashdata('message');
        }

        $this->load->view($this->config->item('theme_list'), $data);
    }

    public function index_post($start = 0, $limit = 25)
    {
    }

    public function edit_get($estimate_id)
    {
        if (!$this->data['auth']) 
        {
            redirect();
        }

        $estimate = $this->estimate->getProfile($estimate_id);

        if (isset($estimate_id))
        {
            $data = array(
                'content'  => 'modules/estimates/edit',
                'estimate' => $estimate,
                'mode'     => 'edit'
            );

            $data['nav']         = "estimates";
            $data['title']       = 'Estimates';
            $data['subtitle']    = 'Add/Edit Estimate';
            $data['add_path']    = '/estimates/add';
            $data['form']        = '/estimates/list';
            $data['delete_path'] = '/estimates/delete';
            $data['manager']     = 'Estimates';
            $data['section']     = 'Edit Estimate';

            // Create job sites estimates sitelist

            $this->load->view($this->config->item('theme_home'), $data);

        } else {

            $this->session->set_flashdata('message', 'Unable to find the estimate. Please try again.');
            redirect('estimates/list');
        }
    }

    public function edit_post($estimate_id)
    {
        // TODO
    }

    public function add_get()
    {
        if (!$this->data['auth']) 
        {
            redirect();
        }

        $this->db->cache_on();

        $data = array(
            'content'  => 'modules/estimates/edit',
            'estimate' => $this->estimate->defaultSettings(),
            'mode'     => 'add'
        );

        $data['nav']            = "estimates";
        $data['title']          = 'Estimates';
        $data['subtitle']       = 'Add/Edit Estimate';
        $data['add_path']       = '/estimates/add';
        $data['form']           = '/estimates/list';
        $data['delete_path']    = '/estimates/delete';
        $data['manager']        = 'Estimates';
        $data['section']        = 'Estimate Edit';

        $this->load->view($this->config->item('theme_home'), $data);
    }

    public function add_post()
    {
        if (!$this->data['auth']) 
        {
            redirect();
        }

        $result = $this->estimate->add_estimate($this->post());

        $this->session->set_flashdata('message', $result['message']);


        if ( $result['success'] === true )
        {
            redirect('/estimates/list');

        } else {

            $estimate = $this->build_empty_estimate();

            $this->db->cache_on();
            $data['estimate']    = $estimate;
            $data['content']     = 'modules/estimates/edit';
            $data['mode']        = 'add';
            $this->db->cache_off();

            $data['nav']         = "estimates";
            $data['title']       = 'Job Estimates';
            $data['subtitle']    = 'Add/Edit Estimate';
            $data['type']        = 'admin';
            $data['path']        = '/estimates/edit';
            $data['add_path']    = '/estimates/add';
            $data['form']        = '/estimates/list';
            $data['delete_path'] = '/estimates/delete';
            $data['manager']     = 'Estimates';
            $data['section']     = 'Add Admin';

            $data['message'] = $result['message'];

            $this->load->view($this->config->item('theme_home'), $data);
        }
    }
}
