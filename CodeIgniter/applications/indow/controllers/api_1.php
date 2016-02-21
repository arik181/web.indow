<?php 

class Api_1 extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->output->enable_profiler(TRUE);
        //$this->load->model(array('user_model', 'group_model', 'customer_model', 'permission_preset_model'));
        $this->load->model(array('user_model', 'customer_model', 'site_model'));

        $this->user     = $this->user_model;
        $this->customer = $this->customer_model;
        $this->site     = $this->site_model;
    }

    public function update_contact_info_post()
    {
        $customer_id  = $this->post('customer_id');
        $contact_info = $this->post();
        unset($contact_info['customer_id']);
        $data = $this->customer_model->update_contact_info($customer_id, $contact_info);
        $response = array('code' => 200, 'data' => $data);
        $this->response($response, 200);
    }

    public function fetch_updated_contact_info_post()
    {
        $customer_id = $this->post('customer_id');
        $response    = $this->customer_model->fetch_customer($customer_id);
        $this->response($response, 200);
    }
    
    public function update_site_info_post()
    {
      $site_id = $this->post('site_id');
      $site_info = array(
        'address' => $this->post('site_address'),
        'address_ext' => $this->post('site_address_ext'),
        'address_type' => $this->post('address_type')
      );
      $data = $this->site_model->update_site($site_id, $site_info);
      $response = array('code' => 200, 'data' => $data);
      $this->response($response, 200);
    }
    
    public function fetch_updated_site_info_post()
    {
      $site_id = $this->post('site_id');
      $response = $this->site_model->fetch_site_info($site_id);
      $this->response($response, 200);
    }

    // Functionality added for SalesForce Integration
    // Use this call if there isn't enough info to create a job-site
    public function new_customer_post()
    {
      // array('first_name', 'last_name', 'organization_name', 'email_1', 'email_type_1', 'email_2', 'email_type_2', 'phone_1', 'phone_type_1', 'phone_2', 'phone_type_2', 'phone_3', 'phone_type_3', 'company_id', 'customer_preferred_contact');
      $data = $this->post();
      $response = $this->customer->add_customer($data);
      $this->response($response, 200);
    }

    // If there's enough info to create a jobsite, use this call, it will automatically add the customer
    public function add_site_post()
    {
        // $user_keys = array('first_name', 'last_name', 'organization_name', 'email_1', 'email_type_1', 'email_2', 'email_type_2', 'phone_1', 'phone_type_1', 'phone_2', 'phone_type_2', 'phone_3', 'phone_type_3', 'company_id', 'customer_preferred_contact');
        // $user_address_keys = array('address_type', 'address', 'address_ext', 'country', 'city', 'state', 'zipcode');
        // $site_keys = array('address', 'address_ext', 'city', 'state', 'zipcode', 'address_type');      

      $data = $this->post();
      $response = $this->site->add_site($data);
      $this->response($response, 200);
    }
}
