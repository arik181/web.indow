<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    class PermissionsLibrary {

        protected $ci;
        public $_user;
        protected $_feature_models = array(
            1  => 'Customer_model',
            2  => 'Estimate_model',
            3  => 'quote_model',
            4  => 'Group_model',
            5  => 'site_model',
            6  => 'Order_model',
            7  => 'Product_model',
            8  => 'User_model',
            9  => 'fulfillment_model', //fulfillment
            10  => 'Mapp_model',
            11 => 'report_model',
            12 => 'salesforce_model',
            13 => 'sales_modifiers_model',
            14 => 'Order_model',
        );

        protected $_feature_redirects = array(
            1  => array('redirect' => '/customers', 'term' => 'customer'),
            2  => array('redirect' => '/estimates', 'term' => 'estimate'),
            3  => array('redirect' => '/sites', 'term' => 'quote'),
            4  => array('redirect' => '/groups', 'term' => 'group'),
            5  => array('redirect' => '/sites', 'term' => 'job site', 'table'=>'sites'),
            6  => array('redirect' => '/orders', 'term' => 'order'),
            7  => array('redirect' => '/products', 'term' => 'product'),
            8  => array('redirect' => '/users', 'term' => 'user'),
            9  => array('redirect' => '/fulfillment', 'term' => 'feature'),
            10  => array('redirect' => '/', 'term' => 'feature'),
            11 => array('redirect' => '/', 'term' => 'report'),
            12 => array('redirect' => '/', 'term' => 'feature'),
            13 => array('redirect' => '/discounts', 'term' => 'discount'),
            14 => array('redirect' => '/orders', 'term' => 'order'),
        );

        public function __construct()
        {
            $this->ci =& get_instance();
        }

        public function getCurrentUserProfile()
        {
            $user = $this->ci->user_model->getProfile($this->ci->ion_auth->get_user_id(),true,false);
            $user->groups = $this->ci->user_group_model->get_many_by(array('user_id' => $user->id));

            $user->group_ids = $this->filterIds($user->groups,'group_id');
            $user->subGroups_ids = $this->get_all_subgroups($user->group_ids);
            $user->allGroupIds = array_merge($user->group_ids, $user->subGroups_ids);
            $user->in_admin_group = in_array(1, $user->group_ids);
            $user->in_admin_rep_group = in_array($this->ci->config->item('rep_group'), $user->group_ids);

            if($user->is_customer == 1) {
                $user_permissions = array();
            } else {
                $user->permissions = $this->get_permissions($user->id, $user->group_ids, $user->permission_set);
            }
            //prd($user->permissions);
            $this->_user = $user;
            //prd($user);
            return $user;
        }

        public function get_all_subgroups($group_ids) {
            if (!count($group_ids)) {
                return array();
            }
            $subgroups = array();
            $added = true;
            while ($added) {
                $where_groups = array_merge($group_ids, $subgroups);
                $added = false;
                $newgroups = $this->ci->db->where_in('parent_group_id', $where_groups)->get('groups')->result();
                foreach ($newgroups as $group) {
                    if (!in_array($group->id, $where_groups)) { //prevent infinite loops if parented group is also child - this would probably break elsewhere though
                        $added = true;
                        $subgroups[] = $group->id;
                    }
                }
            }
            return $subgroups;
        }

        public function get_permissions($user_id, $group_ids=array(), $permission_set=null) {
            $user_permissions = $this->ci->user_permission_model->get_many_by(array('user_id' => $user_id));
            $group_permissions = $this->getGroupPresetPermissions($group_ids);

            $permissions = array( //defaults are set here, then overridden by group permissions, then overridden by user permissions
                4 => 2, //read own status for your group
                8 => 3, //edit own permission for users
            );

            foreach ($group_permissions as $perm) {
                $permissions[$perm->feature_id] = $perm->permission_level_id;
            }

            if ($permission_set) {
                foreach ($this->getPresetPermissions($permission_set) as $perm) {
                    $permissions[$perm->feature_id] = $perm->permission_level_id;
                }
            }

            foreach ($user_permissions as $perm) {
                $permissions[$perm->feature_id] = $perm->permission_level_id;
            }
            //prd($permissions);
            return $permissions;
        }

        public function getGroupPresetPermissions($group_ids) {
            if (empty($group_ids)) {
                return array();
            }
            return $this->ci->db
                    ->select('permissionPresetPermissions.*')
                    ->from('permissionPresetPermissions')
                    ->join('permission_presets', 'permission_preset_id = permission_presets.id')
                    ->join('groups', 'permissions_id = permission_presets.id')
                    ->where_in('groups.id', $group_ids)
                    ->get()->result();
        }

        public function getPresetPermissions($perm_set_id) {
            return $this->ci->db->where('permission_preset_id', $perm_set_id)->get('permissionPresetPermissions')->result();
        }

        public function filterIds($data , $key = 'id') {

            $ids = array();

            if(!empty($data))
            {
                foreach($data as $row){
                    //$ids[] = $row->group_id;
                    $ids[] = $row->$key;
                }
            }
            return $ids;
        }

        public function getEntityRecord($feature_id, $entity_id)
        {
            $model_name = $this->_feature_models[$feature_id];
            return $this->ci->$model_name->get($entity_id);
        }

        public function has_permission($feature_id, $entity_id=null, $mode = 'view', $override_perm=null) {
            //see permissionLevels table for reference to the $perm_level values used in this function
            if (empty($this->_user)) {
                return false;
            }
            if ($this->_user->in_admin_group) {
                return true;
            }
            $perm_level = empty($this->_user->permissions[$feature_id]) ? 1 : $this->_user->permissions[$feature_id];
            if ($override_perm) {
                $perm_level = $override_perm;
            }
            if ($perm_level <= 1 || $perm_level > 6) { //no permissions set, permissions explicitly denied, or bad permission level
                return false;
            }
            if ($mode === 'edit' && (!in_array($perm_level, array(3, 5, 6)))) { //edit permission required and user only has view permissions
                return false;
            }
            if ($entity_id === null || $entity_id == '0') { //list view or add new
                return true;
            }

            $entity = $this->getEntityRecord($feature_id , $entity_id);
            if (!$entity) { //record does not exist
                return false;
            }
            if (in_array($perm_level, array(2,3,4,5,6)) && ($this->_user->in_admin_rep_group || $this->get_entity_creator($entity) == $this->_user->id)) { //view or edit own
                return true;
            }
            if ($perm_level == 4 || $perm_level == 5 || $perm_level == 6) { //view or edit company
                if ($perm_level == 6 && $mode === 'edit') { //trying to edit company record with only view access
                    return false;
                }
                return $this->_user->in_admin_rep_group || in_array($this->get_entity_group($entity), $this->_user->allGroupIds);
            } else {
                return false;
            }
        }

        public function has_edit_permission($feature_id, $entity_id=null) {
            return $this->has_permission($feature_id, $entity_id, 'edit');
        }

        public function has_view_permission($feature_id, $entity_id=null) {
            return $this->has_permission($feature_id, $entity_id, 'view');
        }

        public function require_edit_perm($feature_id, $entity_id=null, $ajax=false, $redirect=null, $redirect_message = null) {
            $this->require_perm($feature_id, $entity_id, 'edit', $ajax, $redirect, $redirect_message);
        }

        public function require_view_perm($feature_id, $entity_id=null, $ajax=false, $redirect=null, $redirect_message = null) {
            $this->require_perm($feature_id, $entity_id, 'view', $ajax, $redirect, $redirect_message);
        }

        public function require_perm($feature_id, $entity_id=null, $mode='view', $ajax=false, $redirect=null, $redirect_message = null, $override_perm=null) {
            $this->ci->load->helper('url');
            if (!$this->has_permission($feature_id, $entity_id, $mode, $override_perm)) {
                if ($ajax) {
                    $this->ci->response('Access Denied', 403);
                }
                $redirect_info = $this->_feature_redirects[$feature_id];
                if ($redirect_message === null) {
                    if ($entity_id === null || $entity_id == 0) {
                        if ($this->has_view_permission($feature_id)) {
                            $redirect = $redirect_info['redirect'];
                        } else {
                            $redirect = '/';
                        }
                        $redirect_message = 'You do not have permission to access that feature.';
                    } else {
                        if ($mode === 'edit' && !$redirect && $this->has_permission($feature_id, $entity_id, 'view', $override_perm) && $_SERVER['REQUEST_METHOD'] !== 'GET') {
                            $redirect = uri_string();
                            if ($this->ci->uri->segment(2) === 'delete') {
                                $redirect = str_replace('delete', 'edit', $redirect);
                                $mode = 'delete';
                            }
                        }
                        $redirect_message = 'You do not have permission to ' . $mode . ' that ' . $redirect_info['term'] . '.';
                    }
                }
                if ($redirect === null) {
                    $redirect = $redirect_info['redirect'];
                }
                $this->ci->session->set_flashdata('message', $redirect_message);
                redirect($redirect);
            }
        }

        public function eula_accepted() {
            return (bool) $this->_user->eula_accepted;
        }

        public function is_customer() {
            return (bool) $this->_user->is_customer;
        }

        public function get_where_string($feature_id, $created_by_field='created_by', $group_field='dealer_id') { // get the where string for list views
            if ($this->_user->in_admin_group || $this->_user->in_admin_rep_group) {
                return 1;
            }
            $perm_level = empty($this->_user->permissions[$feature_id]) ? 1 : $this->_user->permissions[$feature_id];
            if ($perm_level === 1) {
                return 0;
            }
            $where = '(' . $created_by_field . '=' . $this->_user->id;
            if (in_array($perm_level, array(4, 5, 6)) && count($this->_user->allGroupIds) && $group_field) { //group permissions (view/edit)
                $where .= ' OR ' . $group_field . ' IN (' . implode(',', $this->_user->allGroupIds) . ')';
            }
            $where .= ')';
            return $where;
        }

        public function require_admin() {
            if (!$this->_user->in_admin_group) {
                redirect();
            }
        }
        public function require_admin_rep() {
            if (!$this->_user->in_admin_group && !$this->_user->in_admin_rep_group) {
                redirect();
            }
        }

        public function get_entity_creator($entity) {
            if (!empty($entity->created_by)) {
                return $entity->created_by;
            }
            if (!empty($entity->created_by_id)) {
                return $entity->created_by_id;
            }
            if (isset($entity->password)) { //if is user, return user id, so they can edit their own
                return $entity->id;
            }
            if (isset($entity->customer_referred_by)) { //if is user, return user id, so they can edit their own
                return $entity->customer_referred_by;
            }
            if (isset($entity->parent_group_id) && in_array($entity->id, $this->_user->group_ids)) {
                return $this->_user->id; //ugly hack to make group permissions work so you can view "your own" group
            }
            return null;
        }

        public function get_entity_group($entity) {
            $this->ci->load->model('user_model');
            if (!empty($entity->dealer_id)) {
                return $entity->dealer_id;
            }
            if (!empty($entity->group_id)) {
                return $entity->group_id;
            }
            if (isset($entity->modifier_type)) {
                $row = $this->ci->db->where('sales_modifiers_id', $entity->id)->get('sales_modifiers_groups')->row();
                if ($row) {
                    return $row->groups_id;
                }
            }
            if (!empty($entity->created_by)) {
                return $this->ci->user_model->get_group_id($entity->created_by);
            }
            if (!empty($entity->created_by_id)) {
                return $this->ci->user_model->get_group_id($entity->created_by_id);
            }
            if (isset($entity->password)) { //users table
                return $this->ci->user_model->get_group_id($entity->id);
            }
            if (isset($entity->parent_group_id)) {
                return $entity->id;
            }
            if (isset($entity->customer_referred_by)) {
                return $this->ci->db->where('users.id', $entity->user_id)->get('users')->row()->company_id;
            }
            return null;
        }

        public function get_group_name() {
            if (!count($this->_user->group_ids)) {
                return false;
            } else {
                return $this->ci->db->where('id', $this->_user->group_ids[0])->get('groups')->row()->name;
            }
        }
    }
