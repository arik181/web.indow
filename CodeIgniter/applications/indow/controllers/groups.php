<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Groups extends MM_Controller
    {

        public $_user;
        protected $_feature = 4;

        public function __construct()
        {
            parent::__construct();
            $this->_user = $this->data['user'];
            $this->load->factory("GroupFactory");
        }

        /**
         *  List view for groups
         */
        public function index_get()
        {
            $this->permissionslibrary->require_admin();

            $data    = array(
                'content'    => 'modules/groups/list',
                'title'      => 'Groups',
                'nav'        => 'groups',
                'subtitle'   => 'Groups',
                'manager'    => 'Groups',
                'section'    => 'Groups',
                'add_path'   => '/groups/add',
                'add_button' => 'Add Group',
            );

            $message = $this->session->flashdata('message');

            if (!empty($message)) {
                $data['message'] = $message;
            }

            $this->load->view($this->config->item('theme_list'), $data);

        }

        /**
         * @param $id
         */
        public function edit_get($id)
        {
            $this->permissionslibrary->require_view_perm($this->_feature, $id);

            $group            = $this->Group_model->getProfile($id);
            $group->users     = $this->user_model->simple_group_users($id);
            $group->subGroups = $this->Group_model->get_subgroups($id);

            if (isset($id)) {
                $data = array(
                    'content' => 'modules/groups/edit',
                    'group'   => $group,
                    'mode'    => 'edit',
                    'discount' => $this->Group_model->get_wholesale_discount_obj($id)
                );

                $data['groups']      = $this->Group_model->get_groups_list();
                $data['users']       = $this->user_model->get_rep_users_list();
                $data['permoptions'] = $this->permission_preset_model->fetch_permissions_list($includeblank = true);

                $data['nav']         = "groups";
                $data['title']       = 'Groups';
                $data['subtitle']    = 'Add/Edit Group';
                $data['add_path']    = '/groups/add';
                $data['form']        = '/groups/list';
                $data['delete_path'] = '/groups/delete';
                $data['manager']     = 'Groups';
                $data['section']     = 'Group Edit';
                $message             = $this->session->flashdata('message');

                if (!empty($message))
                {
                    $data['message'] = $message;
                }

                $this->data['phpToJavaScript'] = $this->phptojavascript->phpToJavaScript(array(
                    'groups' => array (
                        'group' => $group,
                        'groups' => $this->Group_model->getGroupList()
                    )
                ));

                $this->load->view($this->config->item('theme_home'), $data);

            }
            else
            {
                $this->session->set_flashdata('message', 'Unable to find the group. Please try again.');
                redirect('groups/list');
            }

        }

        public function files_json_get($group_id) {
            $this->permissionslibrary->require_view_perm($this->_feature, $group_id, true);
            $results = $this->Group_model->getGroupUploads($group_id);
            $dataTables = array("draw"    => 1,
                        "recordsTotal"    => count($results),
                        "recordsFiltered" => count($results),
                        "data"            => $results);
            $this->response($dataTables, 200);
        }

        public function profile_get() {
            if (!count($this->_user->groups)) {
                redirect();
            }
            $this->edit_get($this->_user->groups[0]->group_id);
        }

        public function edit_post($id)
        {
            $this->permissionslibrary->require_edit_perm($this->_feature, $id);

            $post = $this->input->post();
            $post['credit_hold'] = empty($post['credit_hold']) ? 0 : 1;
            unset($post['username']);
            $group = $post;
            $result = $this->Group_model->update_group($id,$group);

            $this->load->library('form_validation');

            $this->form_validation->set_rules('phone_1', 'Phone Number', 'required|callback_verify_phone');
            $this->form_validation->set_rules('phone_2', 'Phone Number', 'callback_verify_phone');
            $this->form_validation->set_rules('phone_3', 'Phone Number', 'callback_verify_phone');

            if ($this->form_validation->run() == true)
            {
                if ($result['success'] === true)
                {
                    if (!empty($group['parent_group_id'])) {
                        $row = $this->db->where('group_id', $group['parent_group_id'])->where('sub_group_id', $id)->get('groupsSubGroups')->row();
                        if (!$row) {
                            $this->db->insert('groupsSubGroups', array('group_id' => $group['parent_group_id'], 'sub_group_id' => $id));
                        }
                    }
                    $this->session->set_flashdata('message', $result['message']);
                    redirect('/groups/edit/' . $id);
                }
                else
                {

                    $group     = new stdClass();
                    $group->id = $id;
                    $data      = $this->input->post();

                    foreach ($data as $key => $value) {
                        if (!empty($key)) {
                            $group->{$key} = $value;
                        }
                    }

                    $group->users = $this->user_model->get_simple_users($this->input->post('userid'));
                    //$group->subGroups = $this->group_sub_group_model->getSubGroups($sub_groups);

                    $data                = array(
                        'content' => 'modules/groups/edit',
                        'path'    => 'groups/edit',
                        'group'   => $group,
                    );

                    $data['nav']         = "groups";
                    $data['title']       = 'Groups';
                    $data['subtitle']    = 'Add/Edit Group';
                    $data['add_path']    = '/groups/add';
                    $data['form']        = '/groups/list';
                    $data['delete_path'] = '/groups/delete';
                    $data['manager']     = 'Groups';
                    $data['section']     = 'Group Edit';
                    $data['permoptions'] = $this->permission_preset_model->fetch_permissions_list($includeblank = true);
                    $data['groups']      = $this->Group_model->get_groups_list();
                    $data['users']       = $this->user_model->get_users_list();
                    $data['message']     = $result['message'];
                    $this->load->view($this->config->item('theme_home'), $data);

                }
            }
            else
            {
                $this->session->set_flashdata('message',validation_errors());
                redirect('/groups/edit/' . $id);
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
         *  Add new group form
         */
        public function add_get()
        {
            $this->permissionslibrary->require_admin();
            if(!$this->_user->in_admin_group)
            {
                redirect();
            }

            $data                = array(
                'content' => 'modules/groups/edit',
                'mode'    => 'add'
            );

            $data['groups']      = $this->Group_model->get_groups_list();
            $data['users']       = $this->user_model->get_users_list();
            $data['nav']         = "groups";
            $data['title']       = 'Groups';
            $data['subtitle']    = 'Add/Edit Group';
            $data['add_path']    = '/groups/add';
            $data['form']        = '/groups/list';
            $data['delete_path'] = '/groups/delete';
            $data['manager']     = 'Groups';
            $data['section']     = 'Group Edit';
            $data['permoptions'] = $this->permission_preset_model->fetch_permissions_list($includeblank = true);


            $this->data['phpToJavaScript'] = $this->phptojavascript->phpToJavaScript(array(
                'groups' => array (
                    'group' => array(),
                    'groups' => $this->Group_model->getGroupList()
                )
            ));

            $this->load->view($this->config->item('theme_home'), $data);

        }

        public function upload_file_get($group_id) {
            $this->permissionslibrary->require_edit_perm($this->_feature, $group_id);

            $data                = array(
                'content' => 'modules/groups/upload_file',
            );

            $data['nav']         = "groups";
            $data['title']       = 'Groups';
            $data['subtitle']    = 'Upload File';
            $data['manager']     = 'Groups';
            $data['section']     = 'File Upload';
            $data['group_id']    = $group_id;

            $this->load->view($this->config->item('theme_home'), $data);

        }

        function do_upload($group_id, $type) 
        {
            if (!file_exists('./uploads/' . $group_id . '/')) {
                mkdir('./uploads/' . $group_id . '/', 0777, true);
            }
            $config['upload_path'] = './uploads/' . $group_id . '/';
            if ($type === 'logo') {
                $config['allowed_types'] = 'gif|jpg|png';
            } else {
                $config['allowed_types'] = 'pdf';
            }
            $config['max_size']	= '5000';
            $config['remove_spaces'] = true;
            //$config['max_width']  = '1024';
            //$config['max_height']  = '768';

            $this->load->library('upload', $config);

            if ( ! $this->upload->do_upload())
            {
                $error = array('error' => $this->upload->display_errors());
                return $error;
            }
            else
            {
                $data           = $this->upload->data();
                return $data;
            }
        }

        public function upload_file_post($group_id) 
        {
            $this->permissionslibrary->require_edit_perm($this->_feature, $group_id);
            $type = $this->input->post('file_type');
            if ($type === 'logo') {
                $row = $this->db->where('group_id', $group_id)->where('type', 'logo')->where('deleted', 0)->get('file_uploads')->row();
                if ($row) {
                    $error = 'A logo already exists.  Please remove it before uploading another.';
                }
            }
            $resp = $this->do_upload((integer) $group_id, $type);
            if (!isset($resp['file_name'])) {
                $error = implode('<br>', $resp);
            }

            if (!isset($error)) {
                $file = array(
                    'group_id' => $group_id,
                    'type' => $type,
                    'uploaded' => date('Y-m-d H:i:s'),
                    'filename' => $resp['file_name']
                );

                if ($type === 'logo')
                {
                    $path_parts = pathinfo($resp['file_name']);
                    $filetype = $path_parts['extension'];
                    $file['filename'] = 'logo.' . $filetype;

                    rename( $resp['file_path'] . $resp['file_name'], $resp['file_path'] . $file['filename'] );

                } else {

                    $file['filename'] = $resp['file_name'];
                }

                $this->db->insert('file_uploads', $file);

                $this->session->set_flashdata('message', 'File uploaded.');

                redirect('/groups/edit/' . (integer) $group_id);

            } else {
                $data                = array(
                    'content' => 'modules/groups/upload_file',
                );
                $data['message']     = $error;
                $data['nav']         = "groups";
                $data['title']       = 'Groups';
                $data['subtitle']    = 'Upload File';
                $data['manager']     = 'Groups';
                $data['section']     = 'File Upload';
                $data['group_id']    = $group_id;
                $data['type']        = $type;

                $this->load->view($this->config->item('theme_home'), $data);
            }
        }

        public function delete_file_get($group_id, $file_id) {
            $this->db->where('group_id', $group_id)->where('id', $file_id)->update('file_uploads', array('deleted' => 1));
            $this->session->set_flashdata('message', 'File Deleted.');
            redirect('/groups/edit/' . (integer) $group_id);
        }

        /**
         * Add new group post
         */
        public function add_post()
        {
            $this->permissionslibrary->require_admin();
            if(!$this->_user->in_admin_group)
            {
                redirect();
            }
            $post = $this->input->post();
            unset($post['username']);
            $group = $post;
            $result = $this->Group_model->add_group($group);

            if ($result['success'] == true)
            {
                $this->group_sub_group_model->updateSubGroupsByParentGroupId($sub_groups,$result['id']);
                $this->session->set_flashdata('message', $result['message']);
                redirect('/groups/edit/' . $result['id']);
            }
            else
            {

                $group                  = new stdClass();
                $group->name            = $this->input->post('name');
                $group->email_1         = $this->input->post('email_1');
                $group->email_type_1    = $this->input->post('email_type_1');
                $group->email_2         = $this->input->post('email_2');
                $group->email_type_2    = $this->input->post('email_type_2');
                $group->phone_1         = $this->input->post('phone_1');
                $group->phone_type_1    = $this->input->post('phone_type_1');
                $group->phone_2         = $this->input->post('phone_2');
                $group->phone_type_2    = $this->input->post('phone_type_2');
                $group->state_id        = $this->input->post('state_id');
                $group->address_1_type  = $this->input->post('address_1_type');
                $group->address_1       = $this->input->post('address_1');
                $group->address_1_ext   = $this->input->post('address_1_ext');
                $group->address_2_type  = $this->input->post('address_2_type');
                $group->address_2       = $this->input->post('address_2');
                $group->address_2_ext   = $this->input->post('address_2_ext');
                $group->city_1          = $this->input->post('city_1');
                $group->city_2          = $this->input->post('city_2');
                $group->state_1         = $this->input->post('state_1');
                $group->state_2         = $this->input->post('state_2');
                $group->zipcode_1       = $this->input->post('zipcode_1');
                $group->zipcode_2       = $this->input->post('zipcode_2');
                $group->company_id      = $this->input->post('company_id');
                $group->credit          = $this->input->post('credit');
                $group->permissions_id  = $this->input->post('permissions_id');
                $group->rep_id          = $this->input->post('rep_id');
                $group->parent_group_id = $this->input->post('parent_group_id');
                $group->users           = $this->user_model->get_simple_users($this->input->post('userid'));
                //$group->subGroups       = $this->group_sub_group_model->getSubGroups($sub_groups);

                $data['group']   = $group;
                $data['content'] = 'modules/groups/edit';
                $data['mode']    = 'add';

                $data['nav']         = "groups";
                $data['title']       = 'Groups';
                $data['subtitle']    = 'Add/Edit Group';
                $data['type']        = 'admin';
                $data['path']        = '/groups/edit';
                $data['add_path']    = '/groups/add';
                $data['form']        = '/groups/list';
                $data['delete_path'] = '/groups/delete';
                $data['manager']     = 'Groups';
                $data['section']     = 'Add Admin';
                $data['groups']      = $this->Group_model->get_groups_list();
                $data['users']       = $this->user_model->get_users_list();
                $data['permoptions'] = $this->permission_preset_model->fetch_permissions_list($includeblank = true);

                $data['message'] = $result['message'];

                $this->load->view($this->config->item('theme_home'), $data);
            }
        }


        public function delete_get($id)
        {
            $this->permissionslibrary->require_admin();

            if ($this->Group_model->verifyNoSubGroupsOrUsersByGroupId($id))
            {
                $this->group_sub_group_model->delete_many_by(array('group_id' => $id ));
                $this->user_group_model->delete_many_by(array('group_id' => $id ));
                $this->Group_model->delete($id);
                $this->session->set_flashdata('message', 'The group has been deleted.');
                redirect('/groups');
            }
            else
            {
                $this->session->set_flashdata('message', 'All users and sub groups must be removed prior to deleting a group.');
                $url = '/groups/edit/' . $id;
                redirect($url);
            }


        }


        //-----------------------------------------------------
        //  Ajax Calls
        //-----------------------------------------------------

        /**
         * used for datatables
         */
        public function list_json_get()
        {
            $this->permissionslibrary->require_admin();
            $results    = $this->GroupFactory->getList();
            $dataTables = array("draw"            => 1,
                                "recordsTotal"    => count($results),
                                "recordsFiltered" => count($results),
                                "data"            => $results);
            $this->response($dataTables, 200);
        }

        /**
         * used for datatables
         *
         * @param $cid
         */
        public function list_groups_json_get($cid)
        {
            $this->permissionslibrary->require_admin();
            $results    = $this->GroupFactory->getGroupList($cid);
            $dataTables = array("draw"            => 1,
                                "recordsTotal"    => count($results),
                                "recordsFiltered" => count($results),
                                "data"            => $results);
            $this->response($dataTables, 200);
        }

        /**
         * @param $searchstring
         */
        public function ajax_search_get($searchstring)
        {
            $this->permissionslibrary->require_admin();
            if (empty($this->_user->group_ids)) {
                return false;
            } else {
                $group_id = $this->_user->group_ids[0];
            }
            $admin = $this->_user->in_admin_group;
            $searchstring = urldecode($searchstring);
            $this->response($this->Group_model->group_search($searchstring, $group_id, $admin), 200);
        }

        /**
         * @param $searchstring
         *
         * @todo - Add filter the database call to all groups avaliable based on permission.
         * Currently it is based on the currently logged in users group.
         * EXAMPLE: A user creating a user would only see groups with their own group. This does not work for Indow Admin Users.
         */
        public function ajax_permissions_search_get($searchstring)
        {
            $this->permissionslibrary->require_admin();
            $searchstring = urldecode($searchstring);
            $this->response($this->Group_model->group_permissions_search($this->session->userdata['user_id'], $searchstring), 200);
        }

        /**
         * @param null $name
         */
        public function simple_group_get($name = null)
        {
            $this->permissionslibrary->require_admin();

            if ($name) {
                $name = urldecode($name);
                $ret = $this->Group_model->get_simple_group($name);
                if ($ret === false) {
                    $this->response(array('exists' => false), 200);
                } else {
                    $ret['exists'] = true;
                    $this->response($ret, 200);
                }
            }

        }
    }
