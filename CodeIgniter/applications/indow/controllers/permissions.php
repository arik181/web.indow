<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    /**
     * Class Permissions
     * This is the controller for the managing the permission_presets table. The permission presets are a replacement for user types.
     */
    class Permissions extends MM_Controller
    {

        protected $_message;
        protected $_user;

        public function __construct()
        {

            parent::__construct();
            $this->_user = $this->data['user'];
            if(!$this->_user->in_admin_group)
            {
                redirect();
            }

            $this->configure_pagination('/permissions/list', 'permission_presets');

            if (@!$this->session->flashdata('message') == false)
            {
                $this->_message = $this->session->flashdata('message');
            }
            else
            {
                $this->_message = false;
            }

        }

        /**
         * List view of permission pre sets.
         */
        public function index_get()
        {

            $data['content']     = "modules/permissions/list";
            $data['nav']         = "permissions";
            $data['title']       = 'Permissions';
            $data['subtitle']    = 'Permission Presets List';
            $data['edit_path']   = '/permissions/edit';
            $data['add_path']    = '/permissions/add';
            $data['form']        = '/permissions/list';
            $data['form']        = '/permissions/list';
            $data['delete_path'] = '/permissions/delete';
            $data['add_button']  = 'Add Permission';
            $data['manager']     = 'Permissions';
            $data['section']     = 'Permission List';

            if (!$this->_message == false) {
                $data['message'] = $this->_message;
            }

            $this->load->view($this->config->item('theme_list'), $data);

        }

        /**
         * Edit permission
         *
         * @param $id
         */
        public function edit_get($id)
        {

            $permission = $this->permission_preset_model->get($id);

            if (!is_array($permission)) {

                $permission->permissions = $this->db->get_where( 'permissionPresetPermissions', array('permission_preset_id' => $id))->result();;

                $data = array(
                    'content'     => 'modules/permissions/edit',
                    'path'        => 'permissions/edit/' . $permission->id,
                    'permission'  => $permission,
                    'mode'        => 'edit',
                    'nav'         => 'permissions',
                    'title'       => 'Permissions',
                    'subtitle'    => 'Add/Edit Permission',
                    'add_path'    => '/permissions/add',
                    'form'        => '/permissions/list',
                    'delete_path' => '/permissions/delete',
                    'manager'     => 'Permissions',
                    'section'     => 'Permissions',
                    'btntext'     => 'Save'
                );

                if (!$this->_message == false) {
                    $data['message'] = $this->_message;
                }

                $this->data['phpToJavaScript'] = $this->phptojavascript->phpToJavaScript(array(
                    'permission' => array(
                        'permission' => $permission,
                        'permissionLevels' => $this->permission_level_model->get_all()
                    )
                ));


                $this->load->view($this->config->item('theme_home'), $data);

            }
            else
            {
                $this->session->set_flashdata('message', 'Unable to find the permission. Please try again.');
                redirect('permissions/list');
            }

        }

        /**
         * Edit permission
         *
         * @param $id
         */
        public function edit_post($id)
        {

            $post = $this->input->post();

            $this->form_validation->set_rules('name', 'Name', 'required|callback_permissionEdit_check');

            if($this->form_validation->run() == TRUE)
            {
                if($this->permission_preset_model->update($post['permission_id'],array('name'=>$post['name'])))
                {
                    $this->permission_preset_permission_model->updatePermissionPresetPermissions($this->formatAssociatedPermissions($post),$post['permission_id']);
                    $this->session->set_flashdata('message', 'Updated permission successfully');
                    $url = 'permissions/edit/' . $id;
                    redirect($url);
                }
                else
                {
                    $this->session->set_flashdata('message', 'Unable to update the permission requested. Please try again.');
                    redirect('permissions/list');
                }
            }
            else
            {

                $permission              = new stdClass();
                $permission->id          = $post['permission_id'];
                $permission->name        = $post['name'];
                $permission->permissions = $this->formatAssociatedPermissions($post);
                $this->data['phpToJavaScript'] = $this->phptojavascript->phpToJavaScript(array(
                    'permission' => array('permission' => $permission)
                ));

                $data = array(
                    'content'     => 'modules/permissions/edit',
                    'path'        => 'permissions/edit/' . $permission->id,
                    'permission'  => $permission,
                    'mode'        => 'edit',
                    'nav'         => 'permissions',
                    'title'       => 'Permissions',
                    'subtitle'    => 'Add/Edit Permission',
                    'add_path'    => '/permissions/add',
                    'form'        => '/permissions/list',
                    'delete_path' => '/permissions/delete',
                    'manager'     => 'Permissions',
                    'section'     => 'Permissions',
                    'btntext'     => 'Save',
                    'message'     => validation_errors()
                );

                $this->load->view($this->config->item('theme_home'), $data);

            }
        }

        /**
         * Add Permission
         */
        public function add_get()
        {

            $permission              = new stdClass();
            $permission->name        = '';
            $permission->permissions = array();
            $this->data['phpToJavaScript'] = $this->phptojavascript->phpToJavaScript(array(
                'permission' => array('permission' => $permission)
            ));

            $data = array(
                'content'     => 'modules/permissions/edit',
                'path'        => 'permissions/add',
                'permission'  => $permission,
                'mode'        => 'add',
                'nav'         => 'permissions',
                'title'       => 'Add New Permission',
                'subtitle'    => 'Add/Edit Permission',
                'add_path'    => '/permissions/add',
                'form'        => '/permissions/list',
                'delete_path' => '/permissions/delete',
                'manager'     => 'Permissions',
                'section'     => 'Permission Edit',
                'btntext'     => 'Save'
            );

            $this->load->view($this->config->item('theme_home'), $data);

        }

        /**
         * Add Permission
         */
        public function add_post()
        {

            $post = $this->input->post();

            $this->form_validation->set_rules('name', 'Name', 'required|alpha|is_unique[permission_presets.name]');

            if($this->form_validation->run() == TRUE)
            {

                $permission_id = $this->permission_preset_model->insert(array('name'=>$post['name']));

                if($permission_id)
                {
                    $this->permission_preset_permission_model->updatePermissionPresetPermissions($this->formatAssociatedPermissions($post),$permission_id);
                    $this->session->set_flashdata('message', 'Added permission successfully.');
                    $url = 'permissions/edit/' . $permission_id;
                    redirect($url);
                }
                else
                {
                    $this->session->set_flashdata('message', 'Unable to update the permission requested. Please try again.');
                    redirect('permissions/list');
                }
            }
            else
            {

                $permission              = new stdClass();
                $permission->name        = $post['name'];
                $permission->permissions = $this->formatAssociatedPermissions($post);
                $this->data['phpToJavaScript'] = $this->phptojavascript->phpToJavaScript(array(
                    'permission' => array('permission' => $permission)
                ));

                $data = array(
                    'content'     => 'modules/permissions/edit',
                    'path'        => 'permissions/add',
                    'permission'  => $permission,
                    'mode'        => 'add',
                    'nav'         => 'permissions',
                    'title'       => 'Add New Permission',
                    'subtitle'    => 'Add/Edit Permission',
                    'add_path'    => '/permissions/add',
                    'form'        => '/permissions/list',
                    'delete_path' => '/permissions/delete',
                    'manager'     => 'Permissions',
                    'section'     => 'Permission Edit',
                    'btntext'     => 'Save',
                    'message'     => validation_errors()
                );

                $this->load->view($this->config->item('theme_home'), $data);

            }

        }

        /**
         * Delete the permission_presets record + Delete the Associated Permissions.
         * @param $id
         */
        public function delete_get($id)
        {

            $protectedPresetPermissions = array(1,2,3,4,5,6,7);

            if(in_array($id,$protectedPresetPermissions)){
                $this->session->set_flashdata('message', 'This permission is protected and can not be deleted. Contact your web administrator for questions about protected permissions.');
                redirect('/permissions');
            }
            else
            {
                // delete user preset permission records
                $this->user_preset_permission_model->delete_many_by(array('permission_preset_id' => $id ));
                // delete group preset permission records
                $this->group_permission_model->delete_many_by(array('permission_preset_id' => $id ));
                // delete preset permission permissions records
                $this->permission_preset_permission_model->delete_many_by(array('permission_preset_id' => $id ));
                // Delete preset permission
                $this->permission_preset_model->delete($id);
                $this->session->set_flashdata('message', 'The permission has been deleted.');
                redirect('/permissions');
            }

        }

        /**
         *used for ajax call
         */
        public function permission_presets_get()
        {
            $this->response($this->permission_preset_model->get_all(), 200);
        }

        /**
         *used for ajax call
         */
        public function list_json_get()
        {
            $results    = $this->permission_preset_model->fetch_permissions(999, 0);
            $dataTables = array("draw"            => 1,
                                "recordsTotal"    => count($results),
                                "recordsFiltered" => count($results),
                                "data"            => $results);
            $this->response($dataTables, 200);
        }

        /**
         * @todo find out if this route is used
         */
        public function update_list_post()
        {

            $start = 0;
            $limit = (int)$this->input->post('entries');

            $this->pagination->per_page = $limit;
            $this->pagination->cur_page = 1;

            $permissions = $this->permission_preset_model->fetch_permissions($limit, $start);
            $links       = $this->pagination->create_links($start);
            $first_row   = ((($this->pagination->cur_page - 1) * $this->pagination->per_page) + 1);
            $total_rows  = $this->pagination->total_rows;
            $last_row    = $first_row + $this->pagination->per_page - 1;
            $per_page    = $this->pagination->per_page;

            if ($total_rows < $last_row) {
                $last_row = $total_rows;
            }


            $data = array(
                "per_page"    => $per_page,
                "first_row"   => $first_row,
                "last_row"    => $last_row,
                "total_rows"  => $total_rows,
                "links"       => $links,
                "permissions" => $permissions,
            );

            echo json_encode($data);
        }

        /**
         * Format associated permissions sent in the post value.
         * @param $post
         *
         * @return array
         */
        public function formatAssociatedPermissions($post)
        {

            $associatedPermissions = array();
            $data = array();

            foreach($post as $key => $value)
            {
                $pos = strpos($key,'assoc_perms_');
                 if($pos !== false){
                    $associatedPermissions[] = $value;
                }
            }

            if(!empty($associatedPermissions))
            {

                foreach($associatedPermissions as $row)
                {
                    $permission                                 = new stdClass();
                    $permission->id                             = $row[0];
                    $permission->feature_id                     = $row[2];
                    $permission->permission_level_id            = $row[1];
                    $permission->permission_preset_id           = $row[3];
                    $data[] = $permission;
                }

            }


            return $data;

        }

        //--------------------------------------------------------------------
        // Form Validation Callbacks
        //--------------------------------------------------------------------

        /**
         * verify the name of the permission is unique.
         * @return bool
         */
        public function permissionEdit_check()
        {

            if($this->permission_preset_model->is_uniqueUpdate($this->input->post('name'),$this->input->post('permission_id')))
            {
                return TRUE;
            }
            else
            {
                $this->form_validation->set_message('permissionEdit_check', 'The permission name entered is already taken.');
                return FALSE;
            }

        }

    }

