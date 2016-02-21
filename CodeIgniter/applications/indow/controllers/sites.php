<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Sites extends MM_Controller
    {
        protected $_model;
        protected $_user;
        protected $_feature = 5; // user for permission checks - see features table in database
        protected $_class; // user for permission checks - see features table in database
        protected $_method; // user for permission checks - see features table in database

        /**
         *  1 = does the current user have view access for this route/feature
         *  2 = does the current user have View/Edit access for this route/feature
         *  (A check for the GET or POST types to allow users with view only access to see form pages they can not edit.)
         *  @var array
         */
        protected $_routes = array(
            'index'                             => 1,
            'index_post'                        => 1,
            'add'                               => 2,
            'edit'                              => 2,
            'delete'                            => 2,
            'edit_json'                         => 2,
            'window_json'                       => 1,
            'customer_history'                  => 1,
            'estimates_json'                    => 1,
            'list_json'                         => 1,
            'quotes_json'                       => 1,
            'existing_customer_json'            => 1,
            'orders_json'                       => 1,
            'update_list'                       => 2,
            'users'                             => 2,
            'search_user'                       => 2,
            'update_primary'                    => 2,
            'delete_associated'                 => 2,
            'add_associated'                    => 2,
            'user_info'                         => 2,
            'user_add_exist'                    => 2,
            'save_notes'                        => 2,
        );

        protected function is_legacy($order) {
            $time = empty($order) ? time() : strtotime($order->created);
            return date('Y-m-d H:i:s', $time) < $this->config->item('legacy_date');
        }
        public function __construct()
        {
            parent::__construct();

            $this->load->model(array());
            $this->load->helper(array('language', 'ulist', 'boxlist', 'totallist', 'notes', 'info', 'tabbar', 'associationlist', 'site_info', 'ship_to_helper', 'functions', 'widget'));
            $this->load->factory(array('SitesFactory', 'ItemFactory'));
            $this->site = $this->site_model;
            $this->configure_pagination('/sites/list', 'sites');
            $this->_model = $this->site_model;
            $this->_user  = $this->data['user'];
            $this->_class = $this->router->class;
            $this->_method = $this->router->method;
        }

        public function populate_sites() {
            exit;
            for ($i = 0; $i<3000; ++$i) {
                $this->db->insert('sites', array(
                    'address' => '2925 Monument Blvd',
                    'address_ext' => '#165',
                    'city' => 'Concord',
                    'state' => 'CA',
                    'zipcode' => 94520,
                    'created_by' => 9000
                ));
            }
        }

        public function index_get($start = 0, $limit = 25)
        {
            $this->permissionslibrary->require_view_perm($this->_feature);

            $data = array(
                'content' => 'modules/sites/list',
            );

            $this->pagination->per_page = $limit;

            $data["sites"] = $this->site->fetch_sites($limit, $start);

            // $data["links"] = $this->pagination->create_links($start);

            if (($this->pagination->cur_page) === 0) {
                $data["first_row"] = 1;
            } else {
                $data["first_row"] = (($this->pagination->cur_page - 1) * $this->pagination->per_page) + 1;
            }

            $data["per_page"]       = $this->pagination->per_page;
            $data["status_filters"] = $this->pagination->per_page;
            $data["total_rows"]     = $this->pagination->total_rows;

            $data["last_row"] = $data["first_row"] + $this->pagination->per_page - 1;

            if ($data["total_rows"] < $data["last_row"]) {
                $data["last_row"] = $data["total_rows"];
            }

            $data['nav']         = "sites";
            $data['title']       = 'Job Sites';
            $data['subtitle']    = 'Job Sites List';
            $data['edit_path']   = '/sites/edit';
            $data['add_path']    = '/sites/add';
            $data['form']        = '/sites/list';
            $data['delete_path'] = '/sites/delete';
            $data['add_button']  = 'Add Job Site';
            $data['manager']     = 'Sites';
            $data['section']     = 'Site List';

            if (!$this->session->flashdata('message') == false) {
                $data['message'] = $this->session->flashdata('message');
            }

            $this->load->view($this->config->item('theme_list'), $data);
        }

        public function edit_json_post($id)
        {
            $this->permissionslibrary->require_edit_perm($this->_feature, $id);

            $result = $this->site_model->update_site($id, $this->input->post());
            $result['Bool'] = true;
            $result['Message'] = 'Site updated.';
            $result['Data'] = $this->input->post();
            $result['Data']['address_type'] = $result['Data']['address_type'] ? 'Business' : 'Residential';
            $this->response($result, 200);
            exit; // no idea what all the crap below this line is, or what happened to this function as it existed a few days ago.

            $add_customer_tab_data      = array( array('name' => 'New Customer',
                                                       'id'   => 'new_customer_tab'),
                                                 array('name' => 'Existing Customer',
                                                       'id'   => 'existing_customer_tab')
                                                   );
            $tabbar_css_id              = "customer";
            $active_tab_index           = 1;
            $data['customer_tabbar']    = generate_tabbar( $tabbar_css_id, $add_customer_tab_data, $active_tab_index );

            $existing_customer_data     = array(
                        'form'          => 'add_existing_customer_form',
                        'add_path'      => '',
                        'add_button'    => '',
                        'empty_message' => 'No Associated Customers',
                        'has_actions'   => true,
                        'has_details'   => true,
                        'has_add'      => false,
                        );
            $existing_customers         = $this->_model->fetch_existing_customers_new($id);
            $keys                       = array( 'name' => 'Name' );

            // Create windows totallist
            $assoc_window_data = array(
                'title'         => 'Window Measurements',
                'form'          => 'assoc_windows_form',
                'empty_message' => "There are no windows associated with this site",
                'button_1_path' => 'button_1_path',
                'button_2_path' => 'button_2_path',
                'button_1'      => 'Launch in MAPP',
                'button_2'      => 'Sort',
            );

            $data['user_name'] = $this->user_model->get_user_name($this->ion_auth->get_user_id());

            $hide_submit = ($modeType == 'add') ? true : false;

            $customer_notes_data    = array(
                'title'         => 'Customer Notes',
                'form'          => "sites/edit/$site_id",
                'form_name'     => "save_customer_notes",
                'form_value'    => "Save Customer Notes",
                'empty_message' => "There are no customer notes associated with this site",
                'hidesubmit'    => $hide_submit,
                'fieldvalue'    => ''
            );
            $customer_notes         = $this->_model->fetch_customer_notes_history($site_id);
            $data['customer_notes'] = generate_notes($site_id, $customer_notes_data, $customer_notes);

            // Create internal notes
            $internal_notes_data    = array(
                'title'         => 'Internal Notes',
                'form'          => "sites/edit/$site_id",
                'form_name'     => "save_internal_notes",
                'form_value'    => "Save Internal Notes",
                'empty_message' => "There are no internal notes associated with this site",
                'hidesubmit'    => $hide_submit,
                'fieldvalue'    => ''
            );
            $internal_notes         = $this->_model->fetch_internal_notes_history($site_id);
            $data['internal_notes'] = generate_notes($site_id, $internal_notes_data, $internal_notes);

            // Create  estimates boxlist
            /*$assoc_quotes_estimates_data = array(
                         'title' => 'Quotes &amp; Estimates',
                         'empty_message'=> "There are no estimates associated with this site",
                         ); */
            $estimate_data = array(
                'title'         => 'Estimates',
                'empty_message' => "There are no estimates associated with this site",
            );
            /*$quotes_estimates           = $this->_model->fetch_quotes_estimates_boxlist($site_id);*/
            $estimate_model_data = $this->_model->fetch_estimates_sites($site_id);

            $keys = array('name' => array('th' => 'Name', 'a' => "/users/view/$site_id"));
            /*$data['quotes_estimates']   = generate_boxlist($site_id, $assoc_quotes_estimates_data, $quotes_estimates, $keys);*/
            $data['e_estimates'] = generate_boxlist($site_id, $estimate_data, $estimate_model_data, $keys);

            // Create  Quotes boxlist
            /*$assoc_quotes_estimates_data = array(
                         'title' => 'Quotes &amp; Estimates',
                         'empty_message'=> "There are no estimates associated with this site",
                         ); */
            $quotes_data = array(
                'title'         => 'Quotes',
                'empty_message' => "There are no quotes associated with this site",
            );
            /*$quotes_estimates           = $this->_model->fetch_quotes_estimates_boxlist($site_id);*/
            $quotes_model_data = $this->_model->fetch_quotes_sites($site_id);

            $keys = array('name' => array('th' => 'Name', 'a' => "/users/view/$site_id"));
            /*$data['quotes_estimates']   = generate_boxlist($site_id, $assoc_quotes_estimates_data, $quotes_estimates, $keys);*/
            $data['q_quotes'] = generate_boxlist($site_id, $quotes_data, $quotes_model_data, $keys);


            // Create users boxlist
            $assoc_users_data = array(
                'title'         => 'Users',
                'empty_message' => "There are no users associated with this site",
            );
            $users            = $this->_model->fetch_users_boxlist($site_id);
            $keys             = array('name' => array('th' => 'Name', 'a' => "/users/view/$site_id"));
            $data['users']    = generate_boxlist($site_id, $assoc_users_data, $users, $keys);

            // Create users association list
            $assoc_user_data     = array(
                'title'         => 'Associated Users',
                'form'          => 'assoc_users_form',
                'empty_message' => "There are no users associated with this site",
                'add_path'      => 'add_path',
                'add_button'    => 'Add',
            );
            $users               = $this->_model->fetch_users_association_list($site_id);
            $keys                = array('name' => array('th' => 'Name', 'a' => "/users/view/$site_id"));
            $data['assoc_users'] = $users; // generate_association_list($site_id, $assoc_user_data, $users, $keys);

            // Create orders boxlist
            $assoc_orders_data = array(
                'title'         => 'Orders',
                'empty_message' => "There are no orders associated with this site",
            );
            $orders            = $this->_model->fetch_orders_boxlist($site_id);
            $keys              = array('ordernumber' => array('th' => 'Order Number', 'a' => "/users/view/$site_id"));
            $data['orders']    = generate_boxlist($site_id, $assoc_orders_data, $orders, $keys);

            // Create customer/dealer history boxlist
            $assoc_history_data = array(
                'title'         => 'Customer &amp; Dealer History',
                'empty_message' => "There are no orders associated with this site",
            );

            /*
           $orders                     = $this->_model->fetch_history_boxlist($site_id);
           $keys                       = array( 'name' => array( 'th' => 'Name', 'a'  => "/users/view/$site_id" ));
           $data['history']            = generate_boxlist($site_id, $assoc_history_data, $orders, $keys);
           */
            $data = array_merge($data, $this->get_window_options());


            $this->load->view($this->config->item('theme_home'), $data);

        }

        public function window_json_get($site_id)
        {
            $this->permissionslibrary->require_view_perm($this->_feature, $site_id);
            $results    = $this->ItemFactory->getSiteList($site_id);
            $dataTables = array("draw"            => 1,
                                "recordsTotal"    => count($results),
                                "recordsFiltered" => count($results),
                                "data"            => $results);
            $this->response($dataTables, 200);

        }

        public function customer_history_get($site_id)
        {
            $this->permissionslibrary->require_view_perm($this->_feature, $site_id);

            $results      = $this->site_model->get_customer_history($site_id);
            $all_cust_ids = array();
            foreach ($results as $result) {
                $all_cust_ids[] = $result->id;
            }
            list($cust_ids, $primary) = $this->customer_model->get_site_customers($site_id);
            $all_cust_ids = array_merge($all_cust_ids, $cust_ids);

            $dealers    = $this->site_model->get_dealer_history($all_cust_ids);
            $results    = array_merge($results, $dealers);
            $dataTables = array("draw"            => 1,
                                "recordsTotal"    => count($results),
                                "recordsFiltered" => count($results),
                                "data"            => $results);
            $this->response($dataTables, 200);

        }

        public function estimates_json_get($site_id)
        {
            $this->permissionslibrary->require_view_perm($this->_feature, $site_id);
            if ($site_id) {
                $results    = $this->SitesFactory->getEstimates($site_id);
                $dataTables = array("draw"            => 1,
                                    "recordsTotal"    => count($results),
                                    "recordsFiltered" => count($results),
                                    "data"            => $results);
                $this->response($dataTables, 200);
            }
        }

        public function site_list_json_get($customer_ids) {
            $site_options      = $this->site_model->get_site_options(explode('_', $customer_ids));
            $this->response($site_options, 200);
        }

        public function quotes_json_get($site_id)
        {
            $this->permissionslibrary->require_view_perm($this->_feature, $site_id);
            if ($site_id) {
                $results    = $this->SitesFactory->getQuotes($site_id);
                $dataTables = array("draw"            => 1,
                                    "recordsTotal"    => count($results),
                                    "recordsFiltered" => count($results),
                                    "data"            => $results);
                $this->response($dataTables, 200);

            }
        }

        public function list_json_get($cid)
        {
            $this->permissionslibrary->require_view_perm($this->_feature);
            $results = $this->SitesFactory->getList($cid);

            $dataTables = array("draw"            => 1,
                                "recordsTotal"    => count($results),
                                "recordsFiltered" => count($results),
                                "data"            => $results
            );

            $this->response($dataTables, 200);
        }

        public function existing_customer_json_get($siteid)
        {
            $this->permissionslibrary->require_view_perm($this->_feature, $siteid);
            $results    = $this->SitesFactory->getExisitngUserList($siteid);
            $dataTables = array("draw"            => 1,
                                "recordsTotal"    => count($results),
                                "recordsFiltered" => count($results),
                                "data"            => $results);
            $this->response($dataTables, 200);

        }

        public function index_post()
        {
            exit; //dont think this is used. sorry if im wrong.
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
                'content' => 'modules/sites/list',
            );

            $this->pagination->per_page = $limit;
            $this->pagination->cur_page = 1;

            $data["sites"] = $this->site->fetch_sites($limit, $start);

            $data["links"] = $this->pagination->create_links($start);

            $data["first_row"]      = (($this->pagination->cur_page - 1) * $this->pagination->per_page) + 1;
            $data["per_page"]       = $this->pagination->per_page;
            $data["status_filters"] = $this->pagination->per_page;
            $data["total_rows"]     = $this->pagination->total_rows;

            $data["last_row"] = $data["first_row"] + $this->pagination->per_page - 1;

            if ($data["total_rows"] < $data["last_row"]) {
                $data["last_row"] = $data["total_rows"];
            }

            $data['nav']         = "sites";
            $data['title']       = 'Job Sites';
            $data['subtitle']    = 'Job Sites List';
            $data['add_path']    = '/sites/add';
            $data['edit_path']   = 'sites/edit';
            $data['form']        = '/sites/list';
            $data['delete_path'] = '/sites/delete';
            $data['add_button']  = 'Add Job Site';
            $data['manager']     = 'Sites';
            $data['section']     = 'Site List';

            $data['selected'] = $this->post('show');

            if (!$this->session->flashdata('message') == false) {
                $data['message'] = $this->session->flashdata('message');
            }

            $this->load->view($this->config->item('theme_list'), $data);
        }

        protected function get_window_options($dealer_id = null, $date=null)
        {
            return array(
                'product_type_options' => $this->site_model->id_name_array('product_types', 'description'),
                'product_options'      => $this->site_model->id_name_array('products', 'product'),
                'product_info'         => $this->product_model->get_product_info($dealer_id, $date),
                'frame_step_options'   => array(
                    '' => '',
                    1  => 1,
                    2  => 2,
                    3  => 3,
                    4  => 4,
                    5  => 5,
                    6  => 6,
                    7  => 7,
                    8  => 8,
                    9  => 9,
                    10 => 10
                ),
                'window_shapes'        => $this->site_model->id_name_array('window_shapes', 'name', 'id'),
                'edging_options'       => $this->site_model->id_name_array('edging', 'name', 'id', true),
                'frame_depth_options'  => $this->site_model->id_name_array('frame_depth', 'name', 'id', true),
            );
        }

        public function edit_get($site_id, &$result = "")
        {
            $this->permissionslibrary->require_view_perm($this->_feature, $site_id);

            $site = $this->site_model->getProfile($site_id);
            if (isset($site_id)) {
                $modeType = ($site_id > 0) ? 'edit' : 'add';
                $data     = array(
                    'content' => 'modules/sites/edit',
                    'site'    => $site,
                    'mode'    => $modeType,
                );

                if (!empty($result)) {
                    $data['message'] = $result['message'];
                    $data['post']    = $this->build_empty_site();
                }
                $data['nav']         = "sites";
                $data['title']       = 'Job Sites';
                $data['subtitle']    = 'Add/Edit Site';
                $data['add_path']    = '/sites/add';
                $data['form']        = '/sites/list';
                $data['delete_path'] = '/sites/delete';
                $data['manager']     = 'Sites';
                $data['section']     = 'Site Edit';
                $data['siteid']      = $site_id;
                $data['site_id']      = $site_id;
                $data['is_admin'] = $this->_user->in_admin_group;
				$data['tech'] = $this->site_model->get_tech($site_id);
				$data['change_tech_users'] = $this->user_model->simple_list();
                $data['order_access'] = $this->permissionslibrary->has_edit_permission(6);
                $data['measurements_editable'] = $this->site_model->measurements_editable($site_id);
                $data['legacy'] = $this->is_legacy($site);
                // Job site info
                $job_site_data = array(
                    'title'         => 'Job Site Info',
                    'empty_message' => 'No Info Available',
                    'edit_link'     => "#site_info_popup",
                );
                $job_site      = $site_id > 0 ? $this->db->where('id', $site_id)->get('sites')->row() : false;
                $job_sitep     = $this->_model->fetch_site_info_popup($site_id);
                $keys          = array('address', 'address_ext', 'city', 'state', 'zipcode', 'address_type');
                if (!empty($job_site->created_by)) {
                    $dealer_id = $this->user_model->get_group_id($job_site->created_by);
                } else {
                    $dealer_id  = $this->user_model->get_group_id($this->ion_auth->get_user_id());
                }

                if ($this->_user->in_admin_group) {
                    $data['site_orders'] = $this->site_model->get_site_orders($site_id);
                    $data['change_tech_users'] = $this->user_model->simple_list();
                } else {
                    $data['change_tech_users'] = array('' => '') + $this->user_model->associated_rep_list($dealer_id);
                }



                /* Existing Customer Data */
                list($cust_ids, $primary) = $this->customer_model->get_site_customers($site_id);
                $data['primary'] = $primary;

                $data['start_customers'] = $this->customer_model->customer_manager_get_customer($cust_ids, $primary);


                if ($data['order_access']) {
                    $data['product_info_msrp'] = $this->product_model->get_product_info(null, $site ? date('Y-m-d', strtotime($site->created)) : date('Y-m-d'));
                    $data['review_wholesale_discount'] = $this->group_model->get_wholesale_discount($dealer_id);
                    $data['dealer_id'] = $dealer_id;
                    
                    // Ship To
                    $ship_data = array(
                        'title'         => 'Ship To',
                        'empty_message' => 'No Info Available',
                        'edit_link'     => "#ship_to_popup",
                        'order_review'  => true,
                        'dealer_id' => $dealer_id,
                    );

                    /*
                    if (!empty($order->shipping_address_id)) {
                        $ship = $this->db->where('id', $order->shipping_address_id)->get('user_addresses')->result_array();
                        $ship = $ship[0];
                    } else {
                        $ship = false;
                    }*/
                    if ($dealer_id != 1) {
                        $ship = $this->group_model->get_shipping_address($dealer_id, true);
                        if ($ship) {
                            $ship['id'] = 'd' . $ship['id'];
                        }
                    } else {
                        $ship = $this->customer_model->get_shipping_address($primary, true);
                    }
                    $data['ship_to'] = generate_ship_to(null, $ship_data, $ship, $keys, array(), true, $data['mode']);
                }


                $data['job_site'] = generate_info($site_id, $job_site_data, $job_sitep, $keys, "primary");


                // Create customer addition tabbar
                // Customer Info
                $this->load->model('customer_model');
                $primary_customer = $this->_model->fetch_primary_customer($site_id);

                if (empty($primary_customer))
                    $customer_id = 0;
                else
                    $customer_id = $primary_customer->user_id;
                // $customer = $this->customer_model->getProfile($customer_id);

                $customer_data = array(
                    'title'         => 'Customer Info',
                    'empty_message' => 'No Primary Customer',
                    'edit_link'     => "#contact_info_popup",
                );


                $customer                      = $this->customer_model->fetch_customer_info($customer_id);
                $keys                          = array('name', 'company_name', 'phone_1_type', 'phone_1', 'phone_2_type', 'phone_2', 'email_1_type', 'email_1', 'email_2_type', 'email_2');
                $data['primary_customer_info'] = generate_contact_info_rename($customer_id, $customer_data, $customer, $keys, $mode = "primary");

                $data['user_name'] = $this->user_model->get_user_name($this->ion_auth->get_user_id());

                $hide_submit = ($modeType == 'add') ? true : false;

                // Create customer notes
                $post = $this->post();
                if (isset($post['customer_data'])) {
                    $cdata  = json_decode($post['customer_data'], 1);
                    $cnotes = $cdata['notes_customer'];
                    $inotes = $cdata['notes_internal'];
                } else {
                    $cnotes = '';
                    $inotes = '';
                }
                $customer_notes_data    = array(
                    'title'         => 'Customer Notes',
                    'form'          => "sites/edit/$site_id",
                    'form_name'     => "save_customer_notes",
                    'form_value'    => "Save Customer Notes",
                    'empty_message' => "There are no customer notes associated with this site",
                    'hidesubmit'    => $hide_submit,
                    'fieldvalue'    => $cnotes
                );
                $customer_notes         = $this->_model->fetch_customer_notes_history($site_id);
                $data['customer_notes'] = generate_notes($site_id, $customer_notes_data, $customer_notes);

                // Create internal notes
                $internal_notes_data    = array(
                    'title'         => 'Internal Notes',
                    'form'          => "sites/edit/$site_id",
                    'form_name'     => "save_internal_notes",
                    'form_value'    => "Save Internal Notes",
                    'empty_message' => "There are no internal notes associated with this site",
                    'hidesubmit'    => $hide_submit,
                    'fieldvalue'    => $inotes
                );
                $internal_notes         = $this->_model->fetch_internal_notes_history($site_id);
                $data['internal_notes'] = generate_notes($site_id, $internal_notes_data, $internal_notes);

                // Create  estimates boxlist
                /*$assoc_quotes_estimates_data = array(
                             'title' => 'Quotes &amp; Estimates',
                             'empty_message'=> "There are no estimates associated with this site",
                             ); */
                //$estimate_data = array(
                //    'title'         => 'Estimates',
                //    'empty_message' => "There are no estimates associated with this site",
                //);
                /*$quotes_estimates           = $this->_model->fetch_quotes_estimates_boxlist($site_id);*/
                //$estimate_model_data = $this->_model->fetch_estimates_sites($site_id);

                //$keys = array('name' => array('th' => 'Name', 'a' => "/users/view/$site_id"));
                /*$data['quotes_estimates']   = generate_boxlist($site_id, $assoc_quotes_estimates_data, $quotes_estimates, $keys);*/
                //$data['e_estimates'] = generate_boxlist($site_id, $estimate_data, $estimate_model_data, $keys);

                // Create  Quotes boxlist
                /*$assoc_quotes_estimates_data = array(
                             'title' => 'Quotes &amp; Estimates',
                             'empty_message'=> "There are no estimates associated with this site",
                             ); */
                             /*
                $quotes_data = array(
                    'title'         => 'Quotes',
                    'empty_message' => "There are no quotes associated with this site",
                );*/
                /*$quotes_estimates           = $this->_model->fetch_quotes_estimates_boxlist($site_id);*/
                //$quotes_model_data = $this->_model->fetch_quotes_sites($site_id);

                //$keys = array('name' => array('th' => 'Name', 'a' => "/users/view/$site_id"));
                /*$data['quotes_estimates']   = generate_boxlist($site_id, $assoc_quotes_estimates_data, $quotes_estimates, $keys);*/
                //$data['q_quotes'] = generate_boxlist($site_id, $quotes_data, $quotes_model_data, $keys);


                // Create users boxlist
                $assoc_users_data = array(
                    'title'         => 'Users',
                    'empty_message' => "There are no users associated with this site",
                );
                $users            = $this->_model->fetch_users_boxlist($site_id);
                $keys             = array('name' => array('th' => 'Name', 'a' => "/users/view/$site_id"));
                $data['users']    = generate_boxlist($site_id, $assoc_users_data, $users, $keys);

                // Create users association list
                $assoc_user_data     = array(
                    'title'         => 'Associated Users',
                    'form'          => 'assoc_users_form',
                    'empty_message' => "There are no users associated with this site",
                    'add_path'      => 'add_path',
                    'add_button'    => 'Add',
                );
                $users               = $this->_model->fetch_users_association_list($site_id);
                $keys                = array('name' => array('th' => 'Name', 'a' => "/users/view/$site_id"));
                $data['assoc_users'] = $users; // generate_association_list($site_id, $assoc_user_data, $users, $keys);

                // Create orders boxlist
                $assoc_orders_data = array(
                    'title'         => 'Orders',
                    'empty_message' => "There are no orders associated with this site",
                );
                $orders            = $this->_model->fetch_orders_boxlist($site_id);
                $keys              = array('ordernumber' => array('th' => 'Order Number', 'a' => "/users/view/$site_id"));
                $data['orders']    = generate_boxlist($site_id, $assoc_orders_data, $orders, $keys);

                $data['dealer_address']   = $this->Group_model->get_address($dealer_id);
                $data['shipping_address'] = $this->Group_model->get_shippingAddress($dealer_id);

                // Create customer/dealer history boxlist
                $assoc_history_data = array(
                    'title'         => 'Customer &amp; Dealer History',
                    'empty_message' => "There are no orders associated with this site",
                );

                $data['extension_state'] = true;//$this->item_model->get_extension_state($order_id);
                /*
                $orders                     = $this->_model->fetch_history_boxlist($site_id);
                $keys                       = array( 'name' => array( 'th' => 'Name', 'a'  => "/users/view/$site_id" ));
                $data['history']            = generate_boxlist($site_id, $assoc_history_data, $orders, $keys);
                */
                $data = array_merge($data, $this->get_window_options($dealer_id, $site ? date('Y-m-d', strtotime($site->created)) : date('Y-m-d')));


                $this->load->view($this->config->item('theme_home'), $data);

            } else {

                $this->session->set_flashdata('message', 'Unable to find the site. Please try again.');
                redirect('sites/list');
            }
        }

        public function orders_json_get($site_id)
        {
            $this->permissionslibrary->require_view_perm($this->_feature, $site_id);
            $results    = $this->SitesFactory->getOrders($site_id);
            $dataTables = array("draw"            => 1,
                                "recordsTotal"    => count($results),
                                "recordsFiltered" => count($results),
                                "data"            => $results);

            $this->response($dataTables, 200);
        }

        protected function build_empty_site()
        {
            $site = new stdClass();

            $site->address      = $this->input->post('site_address');
            $site->address_ext  = $this->input->post('site_address_ext');
            $site->city         = $this->input->post('site_city');
            $site->state        = $this->input->post('site_state');
            $site->zipcode      = $this->input->post('site_zipcode');
            $site->address_type = $this->input->post('site_address_type');

            return $site;
        }

        public function site_ajax_post()
        {
            $post    = $this->post();
            $user_id = $this->ion_auth->get_user_id();
            $site    = array(
                'address'      => $post['site_address'],
                'address_ext'  => $post['site_address_ext'],
                'city'         => $post['site_city'],
                'state'        => $post['site_state'],
                'zipcode'      => $post['site_zipcode'],
                'address_type' => $post['site_address_type'],
            );
            if (!empty($post['id'])) {
                $site['id'] = $post['id'];
            }
            if (empty($site['id'])) {
                $site['created'] = date('Y-m-d H:i:s');
                $site['created_by'] = $user_id;
                if ($this->permissionslibrary->has_edit_permission($this->_feature)) {
                    $result = $this->site_model->create($site, json_decode($post['customers'], true), true);
                } else {
                    $this->response(array('message' => 'You do not have permission to create job sites'), 200);
                }
            } else {
                $_POST = array_merge($_POST, $site); //make form validation happy
                if ($this->permissionslibrary->has_edit_permission($this->_feature, $site['id'])) {
                    $result = $this->site_model->update_site($site['id'], $site);
                } else {
                    $result['message'] = 'You do not have permission to edit that site.';
                }
                $result['id'] = $site['id'];
            }
            $result = array_merge($result,array('site'=>$site));
            $this->response($result, 200);
        }

        public function edit_post($id)
        {
            $this->permissionslibrary->require_edit_perm($this->_feature, $id);
            $post         = $this->post();
            $post_site_id = $id;

            $data   = json_decode($post['customer_data'], true);
            $result = "";

            if ($id === 0) { //new Site

                $user_id = $this->ion_auth->get_user_id();
                $site    = array(
                    'address'      => $post['site_address'],
                    'address_ext'  => $post['site_address_ext'],
                    'city'         => $post['site_city'],
                    'state'        => $post['site_state'],
                    'zipcode'      => $post['site_zipcode'],
                    'address_type' => $post['site_address_type'],
                    'created'      => date('Y-m-d H:i:s'),
                    'created_by'   => $user_id,
                    'tech_notes'   => $data['tech_notes']
                );
                $result  = $this->site_model->create($site);

                $id = isset($result['id']) ? $result['id'] : 0;
                if (empty($result['success'])) {
                    $message = "no";
                    $this->edit_get('0', $message);
                }

            }

            if (!$this->data['auth']) {
                redirect();
            }
            if ($this->input->is_ajax_request()) {
                $result = $this->site_model->update_site_form($id, $this->post());
                echo ($result['success'] === true) ? json_encode($result) : "fail";
                exit;
            }

            if ($data['save']) {
                $this->db->where('id', $id)->update('sites', array('updated' => date('Y-m-d H:i:s'), 'tech_notes' => $data['tech_notes']));
                if (isset($data['customers'])) {
                    $this->site_model->set_site_customers($id, $data['customers']);
                }

                $this->item_model->delete_site_items($data['delete_items'], $id);
                $this->item_model->save_site_items($data['items'], $id, !$post_site_id);

                if (isset($data['users'])) {
                    $this->site_model->set_site_users($id, $data['users']);
                }
                if (isset($data['notes_customer']) && $data['notes_customer']) {
                    $this->site_model->add_note($id, $data['notes_customer']);
                }
                if (isset($data['notes_internal']) && $data['notes_internal']) {
                    $this->site_model->add_note($id, $data['notes_internal'], $internal = true);
                }
                $result['success'] = true;
            }
            if ($data['createnew'] !== false) {
                foreach ($data['items'] as $k => $item) {
                    if (empty($data['items'][$k]['checked'])) {
                        unset($data['items'][$k]);
                    } elseif (!empty($data['items'][$k]['subproducts'])) {
                        foreach ($data['items'][$k]['subproducts'] as $sk => $subitem) {
                            if (empty($subitem['checked'])) {
                                unset($data['items'][$k]['subproducts'][$sk]);
                            }
                        }
                    } else {
                        $data['items'][$k]['site_item_id'] = $item['id'] === 'new' ? null : $item['id'];
                    }
                }
                if ($data['createnew'] === 'quote') {
                    $quote_id = $this->quote_model->create_from_site($id);
                    $this->item_model->save_quote_items($data['items'], $quote_id, true, 'qsite');
                    $this->session->set_flashdata('open_quote', $quote_id);
                    redirect('/sites/edit/' . $id);
                } elseif ($data['createnew'] === 'order') {
                    $order_id = $this->order_model->create_from('jobsite', $id);
                    $this->item_model->save_order_items($data['items'], $order_id, true, 'osite');
                    $this->order_model->save_review_info($order_id, $data['review_info']);
                    redirect('/orders/edit/' . $order_id);
                }
            }

            if ($result['success'] === true) {

                $this->session->set_flashdata('message', 'Job site saved.');
                redirect('/sites/edit/' . $id);
            } else {
                $this->edit_get(0, $result);
            }
        }

        public function add_get()
        {
            $this->edit_get(0);
        }

        public function add_post()
        {
            $this->edit_post(0);
        }

        public function delete_get($id)
        {
            $this->permissionslibrary->require_edit_perm($this->_feature, $id);
            if (!isset($id)) {
                redirect('/sites');
            }

            $check_status = $this->site->site_delete($id);
            if ($check_status) {
                $this->session->set_flashdata('message', 'The site has been deleted.');
            } else {
                $this->session->set_flashdata('message', 'Its unable to delete the site.');
            }

            redirect('/sites');

        }

        public function update_list_post()
        {
            exit; //dont think this is used, sorry if im wrong
            $start = 0;
            $limit = (int)$this->input->post('entries');

            $this->pagination->per_page = $limit;
            $this->pagination->cur_page = 1;

            $sites      = $this->site->fetch_sites($limit, $start);
            $links      = $this->pagination->create_links($start);
            $first_row  = ((($this->pagination->cur_page - 1) * $this->pagination->per_page) + 1);
            $total_rows = $this->pagination->total_rows;
            $last_row   = $first_row + $this->pagination->per_page - 1;
            $per_page   = $this->pagination->per_page;

            if ($total_rows < $last_row) {
                $last_row = $total_rows;
            }


            $data = array(
                "per_page"   => $per_page,
                "first_row"  => $first_row,
                "last_row"   => $last_row,
                "total_rows" => $total_rows,
                "links"      => $links,
                "sites"      => $sites,
            );

            echo json_encode($data);
        }

        public function save_notes_post($id, $cid)
        {
			if ($cid === 'null') {
				$cid = null;
			}
            $this->permissionslibrary->require_edit_perm($this->_feature, $id);
            $data     = $this->post();
            $note     = $data['note'];
            $internal = ($data['save'] === "Save Internal Notes") ? true : false;

            $this->site_model->add_note($id, $note, $internal, $cid ? $cid : null);
            $this->response(array(
                'success' => true,
                'date'    => date("M j g:ia")
            ), 200);
        }

        public function save_tech_notes_post($site_id) {
            $this->permissionslibrary->require_edit_perm($this->_feature, $site_id);
            $notes = $this->input->post('notes');
            $this->db->where('id', $site_id)->update('sites', array('tech_notes' => $notes));
            $this->response(array('message' => 'Note saved.'), 200);
        }

		public function reassign_tech_post($site_id) {
			$this->permissionslibrary->require_edit_perm($this->_feature, $site_id);
			$this->site_model->reassign_tech($site_id, $this->input->post('tech_id'));
			$this->session->set_flashdata('message', 'Tech successfully re-assigned.');
			redirect('/sites/edit/' . $site_id);
		}
    }


