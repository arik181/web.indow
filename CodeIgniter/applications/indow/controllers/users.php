<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Users extends MM_Controller
    {

        protected $_message;
        protected $_user;


        public function __construct()
        {

            parent::__construct();
            $this->_user = $this->data['user'];

            $this->load->factory("UserFactory");
            $this->load->model('group_model');

            if (@!$this->session->flashdata('message') == false) {
                $this->_message = $this->session->flashdata('message');
            }
            else
            {
                $this->_message = false;
            }

        }

        /**
         * List view
         */
        public function index_get()
        {
            $this->permissionslibrary->require_admin();

            $data = array(
                'content'           => 'modules/users/list',
                'title'             => 'Users',
                'nav'               => 'users',
                'subtitle'          => 'Users',
                'manager'           => 'Users',
                'section'           => 'Users',
                'associated_orders' => null,
                'add_path'          => '/users/add',
                'add_button'        => 'Add User',
            );

            $this->data['phpToJavaScript'] = $this->phptojavascript->phpToJavaScript(array(
                'users' =>
                    array(
                        'list' => true,
                        'mode' => 'list',
                    )
            ));

            if (!$this->_message == false) {
                $data['message'] = $this->_message;
            }

            $this->load->view($this->config->item('theme_list'), $data);

        }

        public function profile_get() {
            $this->edit_get($this->_user->id);
        }


        /**
         * @param $id
         */
        public function edit_get($id)
        {
            $this->permissionslibrary->require_view_perm(8, $id);
            $user = $this->user_model->getProfile($id);

            if ($user != false)
            {

                $user->groups = $this->Group_model->get_users_groups($id);

                $data['content']      = "modules/users/edit";
                $data['user']         = $user;
                $data['path']         = 'users/edit/' . $id;
                $data['mode']         = 'edit';
                $data['nav']          = "users";
                $data['title']        = 'Users';
                $data['subtitle']     = 'Add/Edit User';
                $data['add_path']     = '/users/add';
                $data['form']         = '/users/list';
                $data['delete_path']  = '/users/delete';
                $data['manager']      = 'Users';
                $data['section']      = 'User Edit';
                $data['perm_options'] = $this->Features_model->get_all();
                $data['perm_options_flat'] = array();
                $data['perm_set_options'] = $this->user_model->id_name_array('permission_presets', 'name', 'id', true);
                $data['_user'] = $this->_user;
                foreach ($data['perm_options'] as $perm) {
                    $data['perm_options_flat'][$perm->id] = $perm->feature_display_name;
                }

                if (!$this->_message == false) {
                    $data['message'] = $this->_message;
                }

                $this->data['phpToJavaScript'] = $this->phptojavascript->phpToJavaScript(array(
                    'users' =>
                        array(
                            'mode'  => 'edit',
                            'user' => $user,
                            'features' => $this->Features_model->get_all(),
                            'permissionLevels' => $this->permission_level_model->get_all()
                        )
                ));
                $data['pre_perms'] = $user->perms;
                $data['pre_groups'] = $user->groups;
                $this->load->view($this->config->item('theme_home'), $data);

            }
            else
            {

                $this->session->set_flashdata('message', 'Unable to find the user. Please try again.');
                redirect('users/list');

            }

        }

        public function password_check($str) {
           if (preg_match('#[0-9]#', $str) && preg_match('#[A-Z]#', $str) && preg_match('#[a-z]#', $str)) {
             return true;
           }
           return false;
        }

        public function unique_username($username) {
            $id = empty($this->edit_user_id) ? 0 : $this->edit_user_id;
            $row = $this->db->where('username', $username)->where('id <>', $id)->get('users')->row();
            $this->form_validation->set_message('unique_username', 'The Username must be unique.');
            return !$row;
        }

        /**
         * @param $id
         */
        public function edit_post($id)
        {
            $this->edit_user_id = $id;
            $this->permissionslibrary->require_edit_perm(8, $id);
            $data   = $this->post();
            $groups = !empty($data['groupid']) ? $data['groupid'] : array();

            $this->load->library('form_validation');

            $this->form_validation->set_rules('phone_1', 'Phone Number', 'required|callback_verify_phone');
            $this->form_validation->set_rules('phone_2', 'Phone Number', 'callback_verify_phone');

            if ($this->form_validation->run() == true)
            {

                $result = $this->user_model->update_user($id, $data, $this->_user->in_admin_group);


                if ($result['success'] == true)
                {
                    $this->session->set_flashdata('message', $result['message']);
                    redirect('/users/edit/' . $id);
                }
                else
                {

                    $user = $this->build_empty_user();
                    $user->id = $id;
                    $user->perms = $this->buildUserFeaturePermissions($data,$id);
                    $data        = array(
                        'title'    => 'Users',
                        'subtitle' => 'Add/Edit User',
                        'content'  => 'modules/users/edit',
                        'path'     => 'users/edit/' . $id,
                        'user'     => $user,
                        'pre_perms' => $user->perms,
                        'pre_groups' => $this->group_model->build_group_list_from_ids($groups)
                    );

                    $data['nav'] = "users";
                    $data['message']      = $result['message'];
                    $data['perm_options'] = $this->permission_preset_model->get_perm_options();
                    $data['perm_set_options'] = $this->user_model->id_name_array('permission_presets', 'name', 'id', true);
                    $data['_user'] = $this->_user;
                    $data['perm_options_flat'] = array();
                    foreach ($data['perm_options'] as $perm) {
                        $data['perm_options_flat'][$perm->id] = $perm->feature_display_name;
                    }

                    $this->data['phpToJavaScript'] = $this->phptojavascript->phpToJavaScript(array(
                        'users' =>
                        array(
                            'mode'  => 'edit',
                            'user' => $user,
                            'features' => $this->Features_model->get_all(),
                            'permissionLevels' => $this->permission_level_model->get_all()
                        )
                    ));

                }
                $this->load->view($this->config->item('theme_home'), $data);

            }
            else
            {
                $this->session->set_flashdata('message',validation_errors());
                redirect('/users/edit/' . $id);
            }

        }

        public function add_get()
        {
            $this->permissionslibrary->require_admin();
            $user = $this->user_model->defaultSettings();
            $user->perms = array();


            $data = array(
                'content' => 'modules/users/edit',
                'user'    => $user,
                'mode'    => 'add',
                'path'    => 'users/add',
            );
            $data['_user']        = $this->_user;
            $data['nav']          = "users";
            $data['perm_options_flat'] = array();
            $data['title']        = 'Add New User';
            $data['subtitle']     = 'Add/Edit User';
            $data['add_path']     = '/users/add';
            $data['form']         = '/users/list';
            $data['delete_path']  = '/users/delete';
            $data['manager']      = 'Users';
            $data['perm_set_options'] = $this->user_model->id_name_array('permission_presets', 'name', 'id', true);
            $data['section']      = 'User Edit';
            $data['perm_options'] = $this->permission_preset_model->get_perm_options();

            $this->data['phpToJavaScript'] = $this->phptojavascript->phpToJavaScript(array(
                'users' =>
                    array(
                        'mode'  => 'edit',
                        'user' => $user,
                        'features' => $this->Features_model->get_all(),
                        'permissionLevels' => $this->permission_level_model->get_all()
                    )
            ));

            $this->load->view($this->config->item('theme_home'), $data);
        }

        public function add_post()
        {
            $this->permissionslibrary->require_admin();
            $result = $this->user_model->add_user($this->post());

            //$this->session->set_flashdata('message', $result['message']);

            if ($result['success'] === true)
            {
                redirect('/users/list');

            }else
            {

                $user = $this->build_empty_user();
                $user->id = 0;
                $user->perms = $this->buildUserFeaturePermissions($this->input->post(),0);

                $data['user']    = $user;
                $data['content'] = 'modules/users/edit';
                $data['mode']    = 'add';
                $data['nav']          = "users";
                $data['path']         = '/users/add';
                $data['type']         = 'admin';
                $data['add_path']     = '/users/add';
                $data['form']         = '/users/list';
                $data['perm_set_options'] = $this->user_model->id_name_array('permission_presets', 'name', 'id', true);
                $data['delete_path']  = '/users/delete';
                $data['title']        = 'Add/Edit User';
                $data['manager']      = 'Users';
                $data['section']      = 'Add Admin';
                $data['perm_options_flat'] = array();
                $data['perm_options'] = $this->permission_preset_model->get_perm_options();
                $data['message'] = $result['message'];
                $data['_user'] = $this->_user;
                $data['pre_perms'] = $user->perms;
                $data['pre_groups'] = $this->group_model->build_group_list_from_ids($this->input->post('groupid'));

                $this->data['phpToJavaScript'] = $this->phptojavascript->phpToJavaScript(array(
                    'users' =>
                        array(
                            'mode'  => 'edit',
                            'user' => $user,
                            'features' => $this->Features_model->get_all(),
                            'permissionLevels' => $this->permission_level_model->get_all()
                        )
                ));

                $this->load->view($this->config->item('theme_home'), $data);
            }
        }

        function verify_phone($str)
        {
            $match1 = preg_match("/^1?-?\ *\(?[0-9]*\)?-?\ *[0-9]+-?[0-9]+$/", $str);
            $match2 = preg_match("/^1?-?\ *\(?[0-9]*\)?-?\ *[0-9]+-?[0-9]+\ +[Ee]xt.?\ +[0-9][0-9][0-9][0-9]?$/", $str);
            $match3 = preg_match("/^1?-?\ *\(?[0-9]*\)?-?\ *[0-9]+-?[0-9]+\ +[Xx]\ *[0-9][0-9][0-9][0-9]?$/", $str);
            $match4 = (!isset($str) || ($str === '')) ? true : false;

            if ($match1 || $match2 || $match3 || $match4)
            {
                return true;
            }

            $this->form_validation->set_message('verify_phone', 'The %s field is invalid');
            return false;
        }

        /**
         * @param $id
         * @todo verify that the currently logged in user can delete users
         */
        public function delete_get($id)
        {
            $this->permissionslibrary->require_admin();
            $this->user_model->delete($id);
            $this->session->set_flashdata('message', 'The user has been deleted.');

            redirect('/users');
        }


        /**
         * used for datatables ajax call
         * @param $user_id
         */
        public function list_json_get($user_id)
        {
            $this->permissionslibrary->require_admin();
            $results = $this->UserFactory->getAdminList($user_id);

            //$results = $this->UserFactory->getList($user_id);

            $dataTables = array("draw"            => 1,
                                "recordsTotal"    => count($results),
                                "recordsFiltered" => count($results),
                                "data"            => $results);

            $this->response($dataTables, 200);

        }

        /**
         * used for an ajax call
         * @param null $username
         */
        public function user_json_get($username = null)
        {

            if ($username) {
                $query = $this->db->get_where('users', array('username' => $username, 'deleted' => 0));
                if ($query->num_rows()) {
                    $user   = $query->row();
                    $simple = array(
                        'id'          => $user->id,
                        'username'    => $user->username,
                        'full_name'   => $user->first_name . ' ' . $user->last_name,
                        'user_exists' => true,
                    );
                } else {
                    $simple = array(
                        'user_exists' => false,
                    );
                }
                $this->response($simple, 200);
            }

        }

        /**
         * @todo find out if this is used.
         */
        public function update_list_post()
        {
            $this->permissionslibrary->require_admin();
            $start = 0;
            $limit = (int)$this->input->post('entries');

            $this->pagination->per_page = $limit;
            $this->pagination->cur_page = 1;

            $users      = $this->user->fetch_users($limit, $start);
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
                "users"      => $users,
            );

            echo json_encode($data);
        }

        protected function build_empty_user()
        {
            $user                = new stdClass();
            $user->first_name    = $this->input->post('first_name');
            $user->last_name     = $this->input->post('last_name');
            $user->username      = $this->input->post('username');
            $user->active        = $this->input->post('active');
            $user->email_1       = $this->input->post('email_1');
            $user->email_type_1  = $this->input->post('email_type_1');
            $user->email_2       = $this->input->post('email_2');
            $user->email_type_2  = $this->input->post('email_type_2');
            $user->phone_1       = $this->input->post('phone_1');
            $user->phone_type_1  = $this->input->post('phone_type_1');
            $user->phone_2       = $this->input->post('phone_2');
            $user->phone_type_2  = $this->input->post('phone_type_2');
            $user->address_1     = $this->input->post('address_1');
            $user->address_1_ext = $this->input->post('address_1_ext');
            $user->address_2     = $this->input->post('address_2');
            $user->address_2_ext = $this->input->post('address_2_ext');
            $user->city_1        = $this->input->post('city_1');
            $user->city_2        = $this->input->post('city_2');
            $user->state_1       = $this->input->post('state_1');
            $user->state_2       = $this->input->post('state_2');
            $user->zipcode_1     = $this->input->post('zipcode_1');
            $user->zipcode_2     = $this->input->post('zipcode_2');
            $user->company_id    = $this->input->post('company_id');
            $user->certified     = $this->input->post('certified');
            $user->deleted       = $this->input->post('deleted');


            $post = $this->post();

            $user->perms = array();
            $count       = 1;
            while (isset($post['toolname_' . $count])) {
                $user->perms[] = array('module' => $post['toolname_' . $count], 'level' => $post['permlevel_' . $count]);
                $count++;
            }

            $ids = array();
            if (isset($post['groupid'])) {
                foreach ($post['groupid'] as $id) {
                    $ids[] = $id;
                }
            }

            if (count($ids)) {
                $user->groups = $this->Group_model->groups_by_ids($ids);
            } else {
                $user->groups = array();
            }

            return $user;



        }

        /**
         * @param $searchstring
         * @todo find out if it is used
         */
        public function ajax_search_get($searchstring, $require_customer=1)
        {
            $searchstring = urldecode($searchstring);
            $this->response($this->user_model->name_search($searchstring, $require_customer), 200);
        }

        /**
         * @param $id
         * @todo find out if it is used
         */
        public function user_info_post($id)
        {
            $this->permissionslibrary->require_admin();
            $result = $this->user_model->get_user_info($id, true, false);
            echo json_encode($result);
            exit;
        }

        public function user_manager_get_user_get($id) {
            $this->permissionslibrary->require_admin();
            if (!$this->data['auth']) {
                redirect();
            }
            $customers = $this->user_model->user_manager_get_user(array($id));
            if (count($customers)) {
                $customer = $customers[0];
            } else {
                $customer = array('error' => 'No such customer');
            }
            $this->response($customer, 200);
        }


        protected function buildUserFeaturePermissions($post,$id)
        {
            $associatedFeaturesPermissions = array();
            foreach($post as $key => $value)
            {
                $pos = strpos($key,'toolname_');

                if($pos !== false){
                    $count = substr($key, 9);

                    $perm_key = 'permlevel_' . $count;
                    
                    $permission                                 = new stdClass();
                    //$permission->id                             = 0;
                    $permission->feature_id                     = $post[$key];
                    $permission->feature_name                   = $this->db->where('id', $permission->feature_id)->get('features')->row()->feature_display_name;
                    $permission->permission_level_id            = $post[$perm_key];
                    $permission->user_id                        = $id;
                    $associatedFeaturesPermissions[]            = $permission;
                }
            }

            return $associatedFeaturesPermissions;

        }

        public function accept_eula_post() {
            $this->db->where('id', $this->_user->id)->update('users', array('eula_accepted' => 1));
            $this->response(array('message' => 'Eula Accepted'), 200);
        }

        public function login_contents_get($customer = false) {
            echo file_get_contents('http://www.indowwindows.com/modi-customer-login-window/');
        }
    }
