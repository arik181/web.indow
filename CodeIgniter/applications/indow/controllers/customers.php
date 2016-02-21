<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Customers extends MM_Controller
{
    protected $_model;
    public $_user;
    protected $_feature = 1;

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array('user_model', 'customer_model', 'site_model', 'group_model', 'user_model','permission_model'));
        $this->load->helper(array('language', 'macrolist', 'boxlist', 'totallist', 'notes', 'info', 'contact_info', 'tabbar', 'associationlist','sitelist'));
        $this->load->factory('CustomerFactory');
        $this->configure_pagination('/customers/list','customers');
        $this->_model = $this->customer_model;
        $this->_user = $this->data['user'];

    }

    public function index_get($start = 0, $limit = 25)
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $data = array(
            'content' => 'modules/customers/list',
        );

        $data["customers"] = $this->customer_model->fetch_customers($limit, $start);

        $data['nav']            = "customers";
        $data['title']          = 'Customers';
        $data['subtitle']       = 'Customer List';
        $data['edit_path']      = '/customers/edit';
        $data['add_path']       = '/customers/add';
        $data['form']           = '/customers/list';
        $data['form']           = '/customers/list';
        $data['delete_path']    = '/customers/delete';
        $data['add_button']     = 'Add Customer';
        $data['manager']        = 'Customers';
        $data['section']        = 'Customer List';

        if (!$this->session->flashdata('message') == FALSE) 
        {
            $data['message'] = $this->session->flashdata('message');
        }

        $this->load->view($this->config->item('theme_list'), $data);
    }

    public function list_json_get()
    {
        $this->permissionslibrary->require_view_perm($this->_feature);

        $results = $this->CustomerFactory->getList();

        $dataTables = array(
            "draw" => 1,
            "recordsTotal" => count($results),
            "recordsFiltered" => count($results),
            "data" => $results
        );

        $this->response($dataTables,200);

    }

    public function edit_get($customer_id)
    {
        $this->permissionslibrary->require_view_perm($this->_feature, $customer_id);
        $customer = $this->customer_model->getProfile($customer_id);

        if (isset($customer_id))
        {
            $data = array(
                'content'  => 'modules/customers/edit',
                'customer' => $customer,
                'mode'     => 'edit'
            );
            $message = $this->session->flashdata('message');
            if (!empty($message)) {
                $data['message'] = $message;
            }
            if ($this->_user->in_admin_group) {
                $data['group_options'] = $this->group_model->simple_list();
            } else {
                $data['group_options'] = $this->group_model->simple_list($this->_user->allGroupIds);
            }

            $data['user_options'] = array('' => '') + $this->user_model->associated_rep_list($customer->company_id ? $customer->company_id : 1);

            $data['nav']         = 'customers';
            $data['title']       = 'Customers';
            $data['subtitle']    = 'Customer Record';
            $data['add_path']    = '/customers/add';
            $data['form']        = '/customers/list';
            $data['delete_path'] = '/customers/delete';
            $data['manager']     = 'Customers';
            $data['section']     = 'Edit Customer';

            // Customer Info
            $customer_data = array(
                        'title'         => 'Contact Info',
                        'empty_message' => 'No Info Available',
                        'edit_link'     => "#contact_info_popup",
                        );
            $customer                   = $this->_model->fetch_customer_info($customer_id);
            $keys                       = array( 'name', 'company_name', 'phone_1_type', 'phone_1', 'phone_2_type', 'phone_2', 'email_1_type', 'email_1', 'email_2_type', 'email_2' );
            $data['contact_info']       = generate_contact_info($customer_id, $customer_data, $customer, $keys);

            // Create addresses list
            $addresses_data = array(
                         'title'                   => 'Addresses',
                         'form'                    => 'address_form',
                         'empty_message'           => "There are no addresses associated with this customer",
                         'has_actions'             => true,
                         'action_url'              => '/customers/addaddress/' . $customer_id,
                         'has_details'             => false,
                         'has_add'                 => true,
                         'add_path'                => '/customers/addaddress/' . $customer_id,
                         'add_button'              => 'Add Address',
                         );
            $addresses           = $this->_model->fetch_address_list($customer_id);
            $keys                = array( 'address_type'    => array('th' => 'Type'),
                                          'address'         => array('th' => 'Address'));
            $data['addresses']   = generate_macrolist($customer_id, $addresses_data, $addresses, $keys);

            // Create job sites estimates sitelist
            $job_sites_data = array(
                         'title'                   => 'Job Sites',
                         'sites_empty_message'     => "There are no job sites associated with this customer",
                         );
            $job_sites           = $this->_model->fetch_sitelist($customer_id);
            $data['job_sites']   = generate_sitelist($customer_id, $job_sites_data, $job_sites);

            $this->load->view($this->config->item('theme_home'), $data);

        } else {

            $this->session->set_flashdata('message', 'Unable to find the customer. Please try again.');
            redirect('customers/list');
        }
    }
    
    public function delete_get($id=null) {
        $this->permissionslibrary->require_edit_perm($this->_feature, $id);
        if(!$this->data['auth'])
        {
            redirect();
        }
        if ($id !== null) {
            $this->customer_model->delete_customer($id);
        }
        redirect('/customers/');
        
    }

    public function deleteaddress_get($customer_id, $address_id) {
        $this->permissionslibrary->require_edit_perm($this->_feature, $customer_id);
        if(!$this->data['auth'])
        {
            redirect();
        }
        if ($address_id !== null) {
            $this->customer_model->delete_address($address_id);
        }
        redirect('/customers/edit/'.$customer_id);

    }

    public function edit_post($customer_id)
    {
        $pdata = $this->post();
        $this->permissionslibrary->require_edit_perm($this->_feature, $customer_id, isset($pdata['ajax']));
        if (!$this->data['auth']) 
        {
            redirect();
        }

        $result = $this->customer_model->update_customer($customer_id, $pdata);
        
        if (isset($pdata['ajax'])) {
            $result['customer_id'] = $customer_id;
            $this->response($result, 200);
        }

        $this->session->set_flashdata('message', $result['message']);

        if ( $result['success'] === true )
        {
            $this->db->cache_delete('customers' , 'customers');
            $this->db->cache_delete('customers' , 'customers' , 'edit');

            redirect('/customers/edit/' . $customer_id);

        } else {

            $customer = $this->build_empty_customer();

            $data = array(
                'nav'            => 'customers',
                'title'          => 'Customers',
                'subtitle'       => 'Add/Edit Customer',
                'add_path'       => '/customers/add',
                'form'           => '/customers/list',
                'delete_path'    => '/customers/delete',
                'manager'        => 'Customers',
                'section'        => 'Edit Customer',
                'subtitle'       => 'Add/Edit Customer',
                'content'        => 'modules/customers/edit',
                'path'           => '/customers/edit',
                'customer'       => $customer,
                'job_sites'      => $job_sites,
            );

            $data['message'] = $result['message'];

            $this->load->view($this->config->item('theme_home'), $data);
        }
    }

    public function build_empty_customer()
    {
            $customer = new stdClass();
            $customer->first_name         = $this->input->post('first_name');
            $customer->last_name          = $this->input->post('last_name');
            $customer->company_id         = $this->input->post('company_id');
            $customer->newsletter         = $this->input->post('newsletter');
            $customer->referred_by        = $this->input->post('referred_by');
            $customer->for_reference_only = $this->input->post('for_reference_only');
            $customer->email_1            = $this->input->post('email_1');
            $customer->email_type_1       = $this->input->post('email_type_1');
            $customer->email_2            = $this->input->post('email_2');
            $customer->email_type_2       = $this->input->post('email_type_2');
            $customer->phone_1            = $this->input->post('phone_1');
            $customer->phone_type_1       = $this->input->post('phone_type_1');
            $customer->phone_2            = $this->input->post('phone_2');
            $customer->phone_type_2       = $this->input->post('phone_type_2');
            $customer->phone_3            = $this->input->post('phone_3');
            $customer->phone_type_3       = $this->input->post('phone_type_3');
            $customer->address_1          = $this->input->post('address_1');
            $customer->address_1_ext      = $this->input->post('address_1_ext');
            $customer->address_2          = $this->input->post('address_2');
            $customer->address_2_ext      = $this->input->post('address_2_ext');
            $customer->city_1             = $this->input->post('city_1');
            $customer->city_2             = $this->input->post('city_2');
            $customer->state_1            = $this->input->post('state_1');
            $customer->state_2            = $this->input->post('state_2');
            $customer->zipcode_1          = $this->input->post('zipcode_1');
            $customer->zipcode_2          = $this->input->post('zipcode_2');
            $customer->deleted            = $this->input->post('deleted');

            return $customer;
    }

    public function add_get($customer_id=null)
    {
        $group_id = count($this->_user->group_ids) ? $this->_user->group_ids[0] : -1;
        $this->permissionslibrary->require_edit_perm($this->_feature);
        $data = array(
            'content'  => 'modules/customers/add',
            'mode'     => 'add'
        );


        if ($this->_user->in_admin_group) {
            $data['groups'] = $this->group_model->simple_list();
        } else {
            $data['groups'] = $this->group_model->simple_list($this->_user->allGroupIds);
        }
        $data['users'] = array('' => '') + $this->user_model->associated_rep_list($group_id);

        // Create addresses list
        $addresses_data = array(
            'title'                   => 'Addresses',
            'form'                    => 'address_form',
            'empty_message'           => "There are no addresses associated with this customer",
            'has_actions'             => true,
            'action_url'              => '/customers/addaddress/' . $customer_id,
            'has_details'             => false,
            'has_add'                 => true,
            'add_path'                => '/customers/addaddress/' . $customer_id,
            'add_button'              => 'Add Address',
        );
        $addresses           = $this->_model->fetch_address_list($customer_id);
        $keys                = array( 'address_type'    => array('th' => 'Type'),
            'address'         => array('th' => 'Address'));
        $data['addresses']   = generate_macrolist($customer_id, $addresses_data, $addresses, $keys);

        // Create job sites estimates sitelist
        $job_sites_data = array(
            'title'                   => 'Job Sites',
            'sites_empty_message'     => "There are no job sites associated with this customer",
        );
        $data['user'] = $this->_user;
        $job_sites           = $this->_model->fetch_sitelist($customer_id);
        $data['job_sites']   = generate_sitelist($customer_id, $job_sites_data, $job_sites);
        $data['countries']   = gen_options('countries', 'United States');
        $data['nav']            = "customers";
        $data['title']          = 'Customers';
        $data['subtitle']       = 'Add/Edit Customer';
        $data['add_path']       = '/customers/add';
        $data['form']           = '/customers/list';
        $data['delete_path']    = '/customers/delete';
        $data['manager']        = 'Customers';
        $data['section']        = 'Customer Add';

        $this->load->view($this->config->item('theme_home'), $data);
    }

    public function customer_manager_get_customer_get($id)
    {
        $this->permissionslibrary->require_view_perm($this->_feature, $id);
        $customers = $this->customer_model->customer_manager_get_customer(array($id));
        if (count($customers)) {
            $customer = $customers[0];
        } else {
            $customer = array('error' => 'No such customer');
        }
        $this->response($customer, 200);
    }

    public function getAddresses_get()
    {
        $data = $this->get();
        $ids = explode(',', $data['ids']);
        if (!count($ids)) {
            $addresses = array();
        } else {
            $dealer_id = !empty($data['dealer']) ? $data['dealer'] : null;
            $addresses = $this->customer_model->get_shipping_list($ids, $dealer_id);
        }
        $this->response($addresses, 200);
    }
    
    public function add_post()
    {
        $post = $this->post();
        $this->permissionslibrary->require_edit_perm($this->_feature, null, isset($post['ajax']));
        $address_keys = array(
            'user_id',
            'address',
            'address_ext',
            'country',
            'city',
            'state',
            'zipcode',
            'address_type',
            'address_type_other',
        );
        if ($this->_user->in_admin_group) {
            $data['groups'] = $this->group_model->simple_list();
        } else {
            $data['groups'] = $this->group_model->simple_list($this->_user->allGroupIds);
        }
        $data['users'] = $this->user_model->rep_list();
        $address = array();
        $address_info = false;
        foreach ($address_keys as $key) {
            if (isset($post[$key])) {
                $address[$key] = $post[$key];
            }

            if(!empty($post[$key])){
                $address_info = TRUE;
            }
        }

        $result = $this->customer_model->add_customer($post);

        //$customer = $this->customer_model->insert(array('user_id' => $user, 'customer_referred_by' => 1, 'customer_preferred_contact' => 1, 'sales_modifier_id' => 1));


        if (isset($post['ajax'])) {
            $result['message'] = strip_tags($result['message']);
            $this->response($result,200);
        } else {
            if ( $result['success'] === true )
            {
                $user_id = $result['userid'];

                /*
                if(isset($post['createSite']) && $post['createSite']){
                    $created_id = $this->ion_auth->get_user_id();
                    $site = array(
                        'address' => $post['address']['address'],
                        'address_ext' => $post['address']['address_ext'],
                        'city' => $post['address']['city'],
                        'state' => $post['address']['state'],
                        'zipcode' => $post['address']['zipcode'],
                        'created' => date('Y-m-d H:i:s'),
                        'created_by' => $created_id,
                    );

                    $site_id = $this->site_model->create_site($site);
                    $this->site_model->set_site_customer($site_id, $user_id);
                } */

                $this->session->set_flashdata('message', $result['message']);
                redirect('/customers/edit/' . $user_id);
            }

            $customer = $this->build_empty_customer();


            $this->db->cache_on();
            $data['customer']    = $customer;
            $data['content']     = 'modules/customers/add';
            $data['mode']        = 'add';
            $this->db->cache_off();

            $data['nav']         = "customers";
            $data['title']       = 'Job Customers';
            $data['subtitle']    = 'Add/Edit Customer';
            $data['type']        = 'admin';
            $data['path']        = '/customers/edit';
            $data['add_path']    = '/customers/add';
            $data['form']        = '/customers/list';
            $data['delete_path'] = '/customers/delete';
            $data['manager']     = 'Customers';
            $data['section']     = 'Add Admin';
            $data['user']        = $this->_user;

            $data['message'] = $result['message'];
            $data['customer'] = $post;

            $this->load->view($this->config->item('theme_home'), $data);
        }
    }

    public function addaddress_get($customerid = null, $addressid = null)
    {
        $this->permissionslibrary->require_view_perm($this->_feature, $customerid);

        $data = array(
            'content'  => 'modules/customers/addeditaddress',
            'customer' => $this->customer_model->defaultSettings(),
            'mode'     => 'add'
        );

        if($addressid)
        {
            $data['address'] = $this->customer_model->fetch_address($addressid);
        }
        else
        {
            $data['address'] = array('address_type' => array(), 'state' => '', 'country' => 'United States');
        }

        $this->db->cache_on();
        $data['customerid']     = $customerid;
        $data['nav']            = "customers";
        $data['title']          = 'Customers';
        $data['subtitle']       = 'Add/Edit Address';
        /*
        $data['add_path']       = '/customers/add';
        $data['form']           = '/customers/list';
        $data['delete_path']    = '/customers/delete';
        */
        $data['states']         = gen_options('states', $data['address']['state']);
        $data['countries']         = gen_options('countries', $data['address']['country']);
        $data['manager']        = 'Customers';
        $data['section']        = 'Customer Add';

        if($this->session->flashdata('message') !=""){
            $data['message']        = $this->session->flashdata('message');
        }

        $this->load->view($this->config->item('theme_home'), $data);
    }
    
    public function addaddress_post($customerid = null, $addressid = null) {
        $this->permissionslibrary->require_edit_perm($this->_feature, $customerid);
        $address_keys = array(
            'user_id',
            'address',
            'address_ext',
            'country',
            'city',
            'state',
            'zipcode',
            'address_type',
            'address_type_other',
        );
        $data = $this->post();
        $address = array();
        foreach ($address_keys as $key) {
            if (isset($data[$key])) {
                $address[$key] = $data[$key];
            }
        }
        if (!empty($data['id'])) {
            $address['id'] = $data['id'];
        }

        $result = $this->customer_model->save_address($address);

        if(isset($data['createSite']) && $data['createSite']){
            $user_id = $this->ion_auth->get_user_id();
            $site = array(
                'address' => $data['address'],
                'address_ext' => $data['address_ext'],
                'city' => $data['city'],
                'state' => $data['state'],
                'zipcode' => $data['zipcode'],
                'created' => date('Y-m-d H:i:s'),
                'created_by' => $user_id,
            );

            $site_id = $this->site_model->create_site($site);
            $this->site_model->set_site_customer($site_id, $data['user_id']);
        }

        if ( $result['success'] === true )
        {
            $this->session->set_flashdata('message', 'Address saved.');
            redirect('/customers/edit/' . $data['user_id']);
        } else {


        $data = array(
            'content'  => 'modules/customers/addeditaddress',
            'customer' => $this->customer_model->defaultSettings(),
            'mode'     => 'add'
        );
        $data['address'] = $this->post();
        if (!isset($data['address']['address_type'])) {
            $data['address']['address_type'] = array();
        }
        $data['message'] = $result['message'];
        $data['customerid']     = $customerid;
        $data['nav']            = "customers";
        $data['title']          = 'Customers';
        $data['subtitle']       = 'Add/Edit Address';
        /*
        $data['add_path']       = '/customers/add';
        $data['form']           = '/customers/list';
        $data['delete_path']    = '/customers/delete';
        */
        $data['states']         = gen_options('states', $data['address']['state']);
        $data['countries']         = gen_options('countries', $data['address']['country']);
        $data['manager']        = 'Customers';
        $data['section']        = 'Customer Add';
        $this->load->view($this->config->item('theme_home'), $data);
        }
    }
    
    public function contact_info_get($customer_id) {

        $customer_data = array(
            'title'         => 'Customer Info',
            'empty_message' => 'No Info Available',
            'edit_link'     => "#contact_info_popup",
            );
        $customer                   = $this->customer_model->fetch_customer_info($customer_id);
        $keys                       = array( 'name', 'company_name', 'phone_1_type', 'phone_1', 'phone_2_type', 'phone_2', 'email_1_type', 'email_1', 'email_2_type', 'email_2' );
        $info       = generate_contact_info($customer_id, $customer_data, $customer, $keys, 'primary');
        exit($info);

    }
}
