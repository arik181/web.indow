<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Discounts extends MM_Controller {

    function __construct() {
        parent::__construct();
        $this->_feature = 13;
        $this->_user = $this->data['user'];
        $this->load->model(array('permission_preset_model', 'sales_modifiers_model', 'group_model'));
        $this->load->library(array('email'));
        $this->load->helper(array('language', 'ulist', 'boxlist', 'totallist', 'notes', 'info', 'tabbar', 'associationlist', 'site_info', 'functions'));
        $this->load->factory('DiscountsFactory');
        $this->configure_pagination('/discounts/list', 'sales_modifiers');
        // Create an easy alias for the model I'm using
        $this->modifier = $this->sales_modifiers_model;
        // First argument is the page, second argument is the table
        // $this->configure_pagination('/discounts/list','sales_modifiers');
    }

    public function index_get($start = 0, $limit = 25) {
        $this->permissionslibrary->require_view_perm($this->_feature);

        $data = array(
            'content' => 'modules/discounts/list',
        );

        $this->pagination->per_page = $limit;

        $data["discounts"] = $this->modifier->fetch_discounts($limit, $start);

        $data["links"] = $this->pagination->create_links($start);

        if (($this->pagination->cur_page) === 0) {
            $data["first_row"] = 1;
        } else {
            $data["first_row"] = (($this->pagination->cur_page - 1) * $this->pagination->per_page) + 1;
        }

        $data["per_page"] = $this->pagination->per_page;
        $data["status_filters"] = $this->pagination->per_page;
        $data["total_rows"] = $this->pagination->total_rows;

        $data["last_row"] = $data["first_row"] + $this->pagination->per_page - 1;

        if ($data["total_rows"] < $data["last_row"]) {
            $data["last_row"] = $data["total_rows"];
        }

        $data['nav'] = "discounts";
        $data['title'] = 'Discounts & Fees';
        $data['subtitle'] = 'Discounts & Fees List';
        $data['edit_path'] = '/discounts/edit';
        if ($this->permissionslibrary->has_edit_permission($this->_feature)) {
            $data['add_path'] = '/discounts/add';
            $data['add_button'] = 'Add New Discount/Fee';
        }
        $data['form'] = '/discounts/list';
        $data['delete_path'] = '/discounts/delete';
        $data['manager'] = 'Discounts';
        $data['section'] = 'Discounts List';

        if (!$this->session->flashdata('message') == FALSE) {
            $data['message'] = $this->session->flashdata('message');
        }

        $this->load->view($this->config->item('theme_list'), $data);
    }

    public function list_json_get() 
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $results = $this->DiscountsFactory->getList();
        $dataTables = array("draw" => 1,
            "recordsTotal" => count($results),
            "recordsFiltered" => count($results),
            "data" => $results);
        $this->response($dataTables, 200);
    }

    // This is what will happen if someone tries to use the pagination functions
    // IE big bunch of junk I don't want to mess with
    /* Currently no use may be need to modify */
    public function index_post() {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $start = 0;

        switch ($this->post('show')) {
            case 1:
                $limit = 50;
                break;
            case 2:
                $limit = 100;
                break;
            case 0:
            default:
                $limit = 25;
                break;
        }

        if (!$this->data['auth']) {
            redirect();
        }

        $data = array(
            'content' => 'modules/discounts/list',
        );

        $this->pagination->per_page = $limit;
        // Discounts data
        $data['modifiers'] = $this->modifier->getModifier($limit, $start);

        $data["links"] = $this->pagination->create_links($start);

        if (($this->pagination->cur_page) === 0) {
            $data["first_row"] = 1;
        } else {
            $data["first_row"] = (($this->pagination->cur_page - 1) * $this->pagination->per_page) + 1;
        }

        $data["per_page"] = $this->pagination->per_page;
        $data["status_filters"] = $this->pagination->per_page;
        $data["total_rows"] = $this->pagination->total_rows;

        $data["last_row"] = $data["first_row"] + $this->pagination->per_page - 1;

        if ($data["total_rows"] < $data["last_row"]) {
            $data["last_row"] = $data["total_rows"];
        }

        $data['nav'] = "discounts";
        $data['title'] = 'Fees and Discounts';
        $data['subtitle'] = 'Fees and Discounts List';
        $data['edit_path'] = '/discounts/edit';
        $data['add_path'] = '/discounts/add';
        $data['form'] = '/discounts/list';
        $data['delete_path'] = '/discounts/delete';

        if (!$this->session->flashdata('message') == FALSE) {
            $data['message'] = $this->session->flashdata('message');
        }

        $this->load->view($this->config->item('theme_list'), $data);
    }

    // This is where we go when we click on edit 
    public function edit_get($id = NULL) {
        $this->permissionslibrary->require_view_perm($this->_feature, $id);
        // If an id is in the address bar, work
        if (isset($id)) {
           
            $modifier = $this->modifier->get_discount($id);
            if ($modifier->modifier_type === 'wholesale') {
                $this->permissionslibrary->require_admin();
            }

            $data = array(
                'content' => 'modules/discounts/edit',
                'modifier' => $modifier,
                'mode' => 'edit'
            );
            if ($this->session->flashdata('message')) {
                $data['message'] = $this->session->flashdata('message');
            }

            $groups = $this->group_model->getGroupList($this->_user->in_admin_group ? null : $this->_user->allGroupIds);
            $group_options = array();
            foreach($groups as $group){ /* obj to array */
                $group_options[$group->id] = $group->name;
            }
            $data['group_options'] = $group_options;

            $data['nav'] = "discounts";
            $data['title'] = 'Fees and Discounts';
            // Sets up the dynamic sub-title, ucfirst capitalizes the first letter
            $data['subtitle'] = 'Edit ' . ucfirst($modifier->modifier_type) . ': ' . $modifier->code;
            $data['edit_path'] = '/discounts/edit';
            $data['add_path'] = '/discounts/add';
            $data['form'] = '/discounts/list';
            $data['delete_path'] = '/discounts/delete';
            // Load our view
            $this->load->view($this->config->item('theme_home'), $data);
        } else {
            // if an ID isn't in the address bar, break
            $this->session->set_flashdata('message', 'Unable to find the user. Please try again.');
            redirect('discounts/');
        }
    }

    public function single_msrp($group, $edit_id=null) {
        $id = $edit_id ? $edit_id : 0;
        $row = $this->db
                ->join('sales_modifiers_groups', 'sales_modifiers.id=sales_modifiers_id')
                ->where('modifier_type', 'msrp')->where('deleted', 0)->where('groups_id', $group)->where('sales_modifiers.id <>', $id)
                ->get('sales_modifiers')->row();
        $this->form_validation->set_message('single_msrp', 'Only one MSRP fee may be added per group.');
        return !$row;
    }

    // This is where we go when we actually submit updated information
    public function edit_post($id) {
        if ($this->input->post('modifier_type') === 'msrp' && !$this->single_msrp($this->input->post('groups_id'), $id)) {
            $this->session->set_flashdata('message', 'Only one msrp fee may be active per group.');
            redirect('/discounts/edit/' . $id);
        }
        $this->edit_discount_id = $id;
        $this->permissionslibrary->require_edit_perm($this->_feature, $id);
        $post = $this->post();
        if ($post['modifier_type'] === 'wholesale') {
            $this->permissionslibrary->require_admin();
        }

        $this->modifier->updateModifier($id, $post);
        // Redirect to the list
        redirect('discounts/list');
    }

    // This is just to display the form to enter a new discount or fee
    public function add_get() {
        $this->permissionslibrary->require_edit_perm($this->_feature);
        if (!$this->data['auth']) {
            redirect();
        }

        $this->db->cache_on();

        $data = array(
            'content' => 'modules/discounts/edit',
            'mode' => 'add'
        );
        if ($this->session->flashdata('message')) {
            $data['message'] = $this->session->flashdata('message');
        }
        $groups = $this->group_model->getGroupList($this->_user->in_admin_group ? null : $this->_user->allGroupIds);
        $group_options = array();
        foreach($groups as $group){ /* obj to array */
            $group_options[$group->id] = $group->name;
        }
        $data['group_options'] = $group_options;

        $data['nav'] = "discounts";
        $data['title'] = 'Fees and Discounts';
        $data['subtitle'] = 'Add new Fee or Discount';
        $data['edit_path'] = '/discounts/edit';
        $data['add_path'] = '/discounts/add';
        $data['form'] = '/discounts/list';
        $data['delete_path'] = '/discounts/delete';
        // load our view
        $this->load->view($this->config->item('theme_home'), $data);
    }
    
    // Actually create a new fee or discount
    
     public function add_post(){
        if ($this->input->post('modifier_type') === 'msrp' && !$this->single_msrp($this->input->post('groups_id'), null)) {
            $this->session->set_flashdata('message', 'Only one msrp fee may be active per group.');
            redirect('/discounts/add');
        }
        $this->permissionslibrary->require_edit_perm($this->_feature);
        $data = $this->post();
        if ($data['modifier_type'] === 'wholesale') {
            $this->permissionslibrary->require_admin();
        }
        $data['created_by'] = $this->_user->id;
        $result = $this->modifier->add_discounts($this->post());

        $this->session->set_flashdata('message', $result['message']);
        
        if ( $result['success'] === true )
        {
            redirect('discounts/list');
        }
       
    }
   // This is where we get when someone hits delete
    public function delete_get($id) {
        $this->permissionslibrary->require_edit_perm($this->_feature, $id);
        if (!$this->data['auth']) 
        {
            redirect();
        }

        if ( ! isset($id)) 
        {
            redirect('discounts/');

        }

        if($this->modifier->deleteModifier($id)){
            $this->session->set_flashdata('message', 'The discount/fee has been deleted.');
            redirect('discounts/');
        }
      
        
    }

}
