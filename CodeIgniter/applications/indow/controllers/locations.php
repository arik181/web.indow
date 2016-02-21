<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Locations extends MM_Controller
{
    var $data;
    function __construct()
    {
        parent::__construct();
        $this->load->model('location_model');
        $this->data['nav'] = "locations";
    }

    public function index($sort_column="modified",$sort_direction="desc",$page = 1)
    {
        $this->load->helper('pagination');
        $this->data['title'] = "Locations";
        $this->data['page_number'] = $page;
        
        // Populate the data
        list($sort_column, $sort_direction, $page) = sanitizeURIforPagination($sort_column,$sort_direction,$page,"modified","desc",1);
        $page_info = new PageInfo();
        $page_info->sort=$sort_column;
        $page_info->direction=$sort_direction;
        $page_info->page=$page;
        $page_info->controller="locations";
        $this->data['page_info'] = $page_info;
        $this->data['locations']=$this->location_model->paginate($page,$sort_column,$sort_direction);

        // Let CodeIgniter Handle the link generation
        $this->pagination->initialize($this->location_model->paginateMeta($sort_column,$sort_direction)); 

        $this->load->template('locations/list',$this->data);
    }

    public function list_locations($sort_column="name",$sort_direction="asc",$page = 1)
    {
        $this->load->helper('pagination');
        $this->data['title'] = "locations";
        $this->data['page_number'] = $page;
        
        // Populate the data
        list($sort_column, $sort_direction, $page) = sanitizeURIforPagination($sort_column,$sort_direction,$page,"claim_date","desc",1);
        $page_info = new PageInfo();
        $page_info->sort=$sort_column;
        $page_info->direction=$sort_direction;
        $page_info->page=$page;
        $page_info->controller="locations";
        $this->data['page_info'] = $page_info;
        $this->data['locations']=$this->location_model->paginate($page,$sort_column,$sort_direction);

        // Let CodeIgniter Handle the link generation
        $this->pagination->initialize($this->location_model->paginateMeta($sort_column,$sort_direction)); 

        $this->load->template('locations/list',$this->data);
    }

    function verify_phone($str)
    {
        $this->form_validation->set_message('verify_phone', 'The %s field is invalid');
        $match1 = preg_match("/^1?-?\ *\(?[0-9]*\)?-?\ *[0-9]+-?[0-9]+$/", $str);
        $match2 = preg_match("/^1?-?\ *\(?[0-9]*\)?-?\ *[0-9]+-?[0-9]+\ +[Ee]xt.?\ +[0-9][0-9][0-9][0-9]?$/", $str);
        $match3 = preg_match("/^1?-?\ *\(?[0-9]*\)?-?\ *[0-9]+-?[0-9]+\ +[Xx]\ *[0-9][0-9][0-9][0-9]?$/", $str);

        if ($match1 || $match2 || $match3)
        {
            return true;
        }

        return false;
    }

    public function create()
    {
        $form = $this->input->post();

        $this->load->library('form_validation');

        $this->form_validation->set_rules('name',           'Location Name','required');
        $this->form_validation->set_rules('latitude',       'Latitude',     'required|numeric');
        $this->form_validation->set_rules('longitude',      'Longitude',    'required|numeric');
        $this->form_validation->set_rules('phone_number',   'Phone Number', 'required|callback_verify_phone');
        $this->form_validation->set_rules('address',        'Address',      'required');
        $this->form_validation->set_rules('hours',          'Hours',        'required');
        $this->form_validation->set_rules('city',           'City',         'required');
        $this->form_validation->set_rules('state',          'State',        'required');
        $this->form_validation->set_rules('zip',            'Zip',          'required|numeric|min_length[5]|max_length[5]');

        if ($this->form_validation->run() == true)
        {
            $this->location_model->create($form);
            $this->session->set_flashdata('flash_data', 'Location was successfully created.');
            $this->session->set_flashdata('alert_type','alert-success');
        }
        else
        {
            $this->session->set_flashdata('validation_errors',validation_errors());
            $this->session->set_flashdata('form',$form);
        }
        $this->load->library('user_agent');
        
        if($this->agent->is_referral()) {
            redirect($this->agent->referrer());
        } else {
            redirect("/locations");
        }    
    }
    
    public function edit($id)
    {
        $this->load->library('user_agent');
        $this->load->library('form_validation');
        $this->data['title'] = 'Edit Location';
        if($this->input->post())
        {
            $form    = $this->input->post();

            $this->form_validation->set_rules('name',           'Location Name','required');
            $this->form_validation->set_rules('latitude',       'Latitude',     'required|numeric');
            $this->form_validation->set_rules('longitude',      'Longitude',    'required|numeric');
            $this->form_validation->set_rules('phone_number',   'Phone Number', 'required|callback_verify_phone');
            $this->form_validation->set_rules('address',        'Address',      'required');
            $this->form_validation->set_rules('hours',          'Hours',        'required');
            $this->form_validation->set_rules('city',           'City',         'required');
            $this->form_validation->set_rules('state',          'State',        'required');
            $this->form_validation->set_rules('zip',            'Zip',          'required|numeric|min_length[5]|max_length[5]');

            if ($this->form_validation->run() == true)
            {
                $this->location_model->update_location($id,$form);
                $this->session->set_flashdata('flash_data','The location was successfully saved.');
                $this->session->set_flashdata('alert_type','alert-success');
            }
            else
            {
                $this->session->set_flashdata('validation_errors',validation_errors());
            }
            if($this->agent->is_referral())
                redirect($this->agent->referrer());
            else
                redirect("/locations");
        }
        if(!isset($this->data['form'])) //if we haven't already processed a form submission, fill from the database instead
            $this->data['form'] = (array)$this->location_model->get($id); //cast array to match POST format
        $this->load->template('locations/edit',$this->data);
    }

    public function delete($id)
    {
        $this->load->library('user_agent');
        if($this->location_model->delete($id))
        {
            $this->session->set_flashdata('flash_data','The location was successfully deleted.');
            $this->session->set_flashdata('alert_type','alert-success');
        }
        else
        {
            $this->session->set_flashdata('flash_data','That location no longer exists.');
            $this->session->set_flashdata('alert_type','alert-danger');
        }  
        if($this->agent->is_referral())
            redirect($this->agent->referrer());
        else
            redirect("/locations");
    }
}

class PageInfo {
    public $sort;
    public $direction;
    public $page;
    public $controller;
}
?>
