<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Group_model extends MM_Model 
{
    protected  $_table = 'groups';
    protected  $soft_delete = TRUE;
    protected  $_key = 'id';

    public $id;
    public $name;
    public $parent_group_id;
    public $email_1;
    public $email_type_1;
    public $email_2;
    public $email_type_2;
    public $phone_1;
    public $phone_type_1;
    public $phone_2;
    public $phone_type_2;
    public $company_id;
    public $deleted;

    public function __construct(){
        parent::__construct();
        $key = $this->_key;
        if ($this->$key)
            return;
    }

    public function save()
    {
        $sql = "";
        if ($this->id > 0) // update operation
        {
            $sql = "UPDATE      $this->_table
                    SET         name = ?,
                                parent_group_id = ?,
                                email_1 = ?,
                                email_type_1 = ?,
                                email_2 = ?,
                                email_type_2 = ?,
                                phone_1 = ?,
                                phone_type_1 = ?,
                                phone_2 = ?,
                                phone_type_2 = ?,
                                company_id = ?,
                                deleted = ?,
                    WHERE       id = ?
                   ;
                   ";
        }
        else //insert operation
        {
            $history = new History_model();
            $history->save();
            $this->history_id = $history->id;
            $sql = "INSERT INTO $this->_table
                    SET         name = ?,
                                parent_group_id = ?,
                                email_1 = ?,
                                email_type_1 = ?,
                                email_2 = ?,
                                email_type_2 = ?,
                                phone_1 = ?,
                                phone_type_1 = ?,
                                phone_2 = ?,
                                phone_type_2 = ?,
                                company_id = ?,
                                deleted = ?
                   ;
                   ";
        }

        $this->db->query($sql, array($this->name,
                                     $this->parent_group_id,
                                     $this->email_1,
                                     $this->email_type_1,
                                     $this->email_2,
                                     $this->email_type_2,
                                     $this->phone_1,
                                     $this->phone_type_1,
                                     $this->phone_2,
                                     $this->phone_type_2,
                                     $this->company_id,
                                     $this->deleted,
                                     $this->id));
        if ($this->id == 0)
            $this->id = $this->db->insert_id();

        return array('success' => true, 'message' => 'Updated user successfully.');
    }

    public function build_group_list_from_ids($ids) {
        if (!count($ids)) {
            return array();
        }
        return $this->db
                ->select('groups.id, permission_presets.name as permname, groups.name')
                ->from('groups')
                ->join('permission_presets', 'permission_presets.id=permissions_id', 'left')
                ->where_in('groups.id', $ids)
                ->get()->result();
    }

    public function add_user_to_group($user_id, $group_id)
    {

        $insert = array(
                  'user_id' => $user_id
                , 'group_id' => $group_id
            );

        $this->db->insert('users_groups', $insert);

    }

    public function get_estimate_package($dealer_id) 
    {
        return $this->db->where('group_id', $dealer_id)->where('deleted', 0)->where('type', 'addendum')->get('file_uploads')->result();
    }

    public function get_order_package($dealer_id) 
    {
        return $this->db->where('group_id', $dealer_id)->where('deleted', 0)->where('type', 'addendum')->get('file_uploads')->result();
    }

    public function get_msrp($dealer_id) {
        if ($dealer_id) {
            $row = $this->db
                    ->from('sales_modifiers_groups')
                    ->join('sales_modifiers', 'sales_modifiers.id=sales_modifiers_id')
                    ->where('groups_id', $dealer_id)->where('modifier_type', 'msrp')
                    ->where('deleted', 0)
                    ->get()->row();
            if ($row) {
                $msrp = 1 + ($row->amount / 100);
            } else {
                $msrp = 1;
            }
        } else {
            $msrp = 1;
        }
        return $msrp;
    }


    public function get_wholesale_discount($dealer_id) {
        if ($dealer_id) {
            $row = $this->db
                    ->from('sales_modifiers_groups')
                    ->join('sales_modifiers', 'sales_modifiers.id=sales_modifiers_id')
                    ->where('groups_id', $dealer_id)->where('modifier_type', 'wholesale')
                    ->where('sales_modifiers.deleted', 0)
                    ->get()->row();
            if ($row) {
                return array('type' => $row->modifier, 'amount' => $row->amount);
            }
        }
        return false;
    }

    public function get_wholesale_discount_obj($dealer_id) {
        if ($dealer_id) {
            return $this->db
                    ->select('sales_modifiers.*')
                    ->from('sales_modifiers_groups')
                    ->join('sales_modifiers', 'sales_modifiers.id=sales_modifiers_id')
                    ->where('groups_id', $dealer_id)->where('modifier_type', 'wholesale')
                    ->where('sales_modifiers.deleted', 0)
                    ->get()->row();
        }
        return false;
    }

    public function get_subgroups($group_id) {
        return $this->db->where('parent_group_id', $group_id)->get('groups')->result();
    }

    public function update_group($id, $data)
    {
        $this->form_validation->set_rules('name', 'Group Name', 'required');
        $this->form_validation->set_rules('email_1', 'Email', 'valid_email');

        $email_check = $this->groupEditCheck($id,$data['name']);

        if ($this->form_validation->run() == TRUE && $email_check === TRUE)
        {

            $address1fields = array(
                'address_1' => 'address',
                'address_1_ext' => 'address_ext',
                'city_1' => 'city',
                'state_1' => 'state',
                'zipcode_1' => 'zipcode',
                'address_1_type' => 'address_type',
            );

            $address2fields = array(
                'address_2' => 'address',
                'address_2_ext' => 'address_ext',
                'city_2' => 'city',
                'state_2' => 'state',
                'zipcode_2' => 'zipcode',
                'address_2_type' => 'address_type',
            );

            $address3fields = array(
                'address_3' => 'address',
                'address_3_ext' => 'address_ext',
                'city_3' => 'city',
                'state_3' => 'state',
                'zipcode_3' => 'zipcode',
                'address_3_type' => 'address_type',
            );

            $address1update = false;
            $address2update = false;
            $address3update = false;
            $address1 = array('addressnum' => '1');
            $address2 = array('addressnum' => '2');
            $address3 = array('addressnum' => '3');
            $group = array();
            foreach ( $data as $key => $value )
            {
                if (isset($address1fields[$key])) {
                    $address1update = true;
                    $address1[$address1fields[$key]] = $value;                    
                } elseif (isset($address2fields[$key])) {
                    $address2update = true;
                    $address2[$address2fields[$key]] = $value; 
                } elseif (isset($address3fields[$key])) {
                    $address3update = true;
                    $address3[$address3fields[$key]] = $value; 
                } else {
                    if ($key !== 'submit' && $key !== 'userid')
                    {
                        $group[$key] = $value;
                    }
                }
            }
            if (!$this->_user->in_admin_group) {
                foreach (array('permissions_id', 'rep_id', 'signed_agreement_name', 'rep_id', 'credit_hold', 'credit') as $key) {
                    unset($group[$key]);
                }
            }
            $this->update($id, $group);
            $address1['group_id'] = $id;
            $address2['group_id'] = $id;
            $address3['group_id'] = $id;

            $addressq = $this->db->from('group_addresses')->where('group_id', $id)->get();
            $types = array();
            foreach ($addressq->result() as $row) {
                $types[$row->addressnum] = true;
            }

            if ($address1update) {
                if (isset($types['1'])) {
                    $this->db->where('group_id', $id)->where('addressnum', '1');
                    $res = $this->db->update('group_addresses', $address1);
                } else {
                    $this->db->insert('group_addresses', $address1);
                }
            }
            if ($address2update) {
                if (isset($types['2'])) {
                    $this->db->where('group_id', $id)->where('addressnum', '2');
                    $this->db->update('group_addresses', $address2);
                } else {
                    $this->db->insert('group_addresses', $address2);
                }
            }
            if ($address3update) {
                if (isset($types['3'])) {
                    $this->db->where('group_id', $id)->where('addressnum', '3');
                    $this->db->update('group_addresses', $address3);
                } else {
                    $this->db->insert('group_addresses', $address3);
                }
            }
            /*
            $this->db->where('group_id', $id);
            $this->db->delete('users_groups');
            if (isset($data['userid']) && count($data['userid'])) {
                foreach($data['userid'] as $userid) {
                    $user_group = array(
                        'user_id' => $userid,
                        'group_id' => $id,
                    );
                    $this->db->insert('users_groups', $user_group);
                }
            }*/

        }
        else
        {
            return array('success' => false, 'message' => validation_errors() . $email_check);
        }

        return array('success' => true, 'message' => 'Updated group successfully.');
    }

    public function getProfile($id)
    {
        $sql = '
            SELECT  *
            FROM    groups
            WHERE   groups.id = ?;
            ';

        $query = $this->db->query($sql, $id);

        if ( ! empty($query)  && $query->num_rows() > 0) 
        {
            $group = $query->row();
            $addressq = $this->db->get_where('group_addresses', array('group_id' => $id));
            foreach ($addressq->result() as $row) {
                if ($row->addressnum == '1') {
                    $group->address_1_type = $row->address_type;
                    $group->address_1 = $row->address;
                    $group->address_1_ext = $row->address_ext;
                    $group->city_1 = $row->city;
                    $group->state_1 = $row->state;
                    $group->zipcode_1 = $row->zipcode;
                } elseif ($row->addressnum == '2') {
                    $group->address_2_type = $row->address_type;
                    $group->address_2 = $row->address;
                    $group->address_2_ext = $row->address_ext;
                    $group->city_2 = $row->city;
                    $group->state_2 = $row->state;
                    $group->zipcode_2 = $row->zipcode;
                } elseif ($row->addressnum == '3') {
                    $group->address_3_type = $row->address_type;
                    $group->address_3 = $row->address;
                    $group->address_3_ext = $row->address_ext;
                    $group->city_3 = $row->city;
                    $group->state_3 = $row->state;
                    $group->zipcode_3 = $row->zipcode;
                }
            }
            return $group;
        }
        else{
            return false;
        }

    }

    public function get_logo_url($group_id) {
        $row = $this->db->where('group_id', $group_id)->where('type', 'logo')->where('deleted', 0)->get('file_uploads')->row();
        return $row ? '/uploads/' . $group_id . '/' . $row->filename : false;
    }

    public function defaultSettings()
    {
        $group = new stdClass();
        $group->ip_address        = '';
        $group->first_name        = '';
        $group->last_name         = '';
        $group->groupname          = '';
        $group->password          = '';
        $group->active            = 1;
        $group->organization_name = '';
        $group->bio               = '';
        $group->email_1           = '';
        $group->email_type_1      = '';
        $group->email_2           = '';
        $group->email_type_2      = '';
        $group->phone_1           = '';
        $group->phone_type_1      = '';
        $group->phone_2           = '';
        $group->phone_type_2      = '';
        $group->address_1         = '';
        $group->address_1_ext     = '';
        $group->address_2         = '';
        $group->address_2_ext     = '';
        $group->city_1            = '';
        $group->city_2            = '';
        $group->state_1           = '';
        $group->state_2           = '';
        $group->zipcode_1         = '';
        $group->zipcode_2         = '';
        $group->company_id        = 1;
        $group->certified         = 0;
        $group->deleted           = 0;
        $group->disabled          = 0;

        return $group;
    }

    public function getEmail($id){
        $this->db->select('email');
        $this->db->where('id',$id);
        $query = $this->db->get($this->_table)->result();
        return $query[0]->email;
    }

    public function fetch_groups($limit, $start)
    {
        $sql = 'SELECT  *
                FROM   `groups`
                WHERE   groups.deleted = 0
                LIMIT   ?, ?';
        //WHERE   groups.deleted = 0 AND group_permissions.permission_id = 1

        $start = (int) $start;
        $limit = (int) $limit;
        $query = $this->db->query($sql, array($start, $limit));

        if ( ! empty($query)  && $query->num_rows() > 0) 
        {
            foreach ($query->result() as $row) 
            {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function group_search($string, $group_id, $admin=false) {
        $this->db->select('id, name AS group_name');
        $this->db->from('groups');
        $this->db->like('name', $string);
        $this->db->where('deleted', 0);
        if (!$admin) {
            $this->db->where("
                id=$group_id 
                OR id IN (SELECT sub_group_id FROM groupsSubGroups WHERE group_id=$group_id)
                OR id IN (SELECT group_id FROM groupsSubGroups WHERE sub_group_id=$group_id)
            ");
        }
        $this->db->limit(10);
        $query = $query = $this->db->get();
        return $query->result();
    }

    public function group_permissions_search($cid,$string)
    {
        $sql = "SELECT          users.id, COALESCE(groups.name,'Not Assigned') AS group_name
                FROM            users
                LEFT JOIN       user_addresses
                ON              user_addresses.user_id = users.id AND user_addresses.address_type = 'address1'
                LEFT JOIN       users_groups
                ON              users_groups.user_id = users.id
                LEFT JOIN       groups
                ON              groups.id = users_groups.group_id
                WHERE           groups.name LIKE ? AND users.deleted = 0  AND users_groups.group_id IN (SELECT group_id FROM users_groups Where users_groups.user_id =  ?)
                GROUP BY        users_groups.group_id
                ;
                ";

        $results = $this->db->query($sql,array("%".$string."%", $cid))->result();

        return $results;
    }

    public function get_primary_address($dealer_id, $array=false) {
        $addr = $this->db
                    ->where('addressnum', 1)
                    ->where('group_id', $dealer_id)
                    ->where('address <>', '')
                    ->get('group_addresses');
        return $array ? $addr->row_array() : $addr->row();
    }

    public function get_shipping_address($dealer_id, $array=false) {
        $sql = "SELECT * FROM group_addresses WHERE address <> '' AND group_id=?
                    ORDER BY address_type='Shipping' DESC, addressnum=1 DESC"; //codeigniter doesnt support an order_by clause like this til 3.0. Fantastic...
        $addr = $this->db->query($sql, $dealer_id);
        return $array ? $addr->row_array() : $addr->row();
    }

    public function simple_list($ids=null) {
        if ($ids) {
            $this->db->where_in('id', $ids);
        }
        $groups = $this->db->where('deleted', 0)->get('groups')->result();
        $groupsa = array();
        foreach ($groups as $group) {
            $groupsa[$group->id] = $group->name;
        }
        return $groupsa;
    }

    public function get_groups_list() {
        $groups = $this->fetch_groups(999, 0);
        $groupsa = array('' => '');
        foreach ($groups as $group) {
            $groupsa[$group->id] = $group->name;
        }
        return $groupsa;
    }

    public function delete($id)
    {
        $this->db->trans_start();
        $sql = "DELETE FROM group_permissions WHERE group_id = ? ;";
        $query = $this->db->query($sql,$id);
        $sql = "DELETE FROM " . $this->_table . " WHERE  " . $this->_key . " = ? ;";
        $query = $this->db->query($sql,$id);
        $this->db->trans_complete();

        return $query;
    }

    public function add_group($data)
    {
        $this->form_validation->set_rules('name', 'Group Name', 'required|is_unique[groups.name]');

        if ($this->form_validation->run() == TRUE) 
        {
            $address1fields = array(
                'address_1' => 'address',
                'address_1_ext' => 'address_ext',
                'city_1' => 'city',
                'state_1' => 'state',
                'zipcode_1' => 'zipcode',
                'address_1_type' => 'address_type',
            );
            $address2fields = array(
                'address_2' => 'address',
                'address_2_ext' => 'address_ext',
                'city_2' => 'city',
                'state_2' => 'state',
                'zipcode_2' => 'zipcode',
                'address_2_type' => 'address_type',
            );
            $address3fields = array(
                'address_3' => 'address',
                'address_3_ext' => 'address_ext',
                'city_3' => 'city',
                'state_3' => 'state',
                'zipcode_3' => 'zipcode',
                'address_3_type' => 'address_type',
            );
            $address1update = false;
            $address2update = false;
            $address3update = false;
            $address1 = array('addressnum' => '1');
            $address2 = array('addressnum' => '2');
            $address3 = array('addressnum' => '3');
            $group = array();
            foreach ( $data as $key => $value )
            {
                if (isset($address1fields[$key])) {
                    $address1update = true;
                    $address1[$address1fields[$key]] = $value;                    
                } elseif (isset($address2fields[$key])) {
                    $address2update = true;
                    $address2[$address2fields[$key]] = $value; 
                } elseif (isset($address3fields[$key])) {
                    $address3update = true;
                    $address3[$address3fields[$key]] = $value; 
                } else {
                    if ($key !== 'submit' && $key !== 'userid')
                    {
                        $group[$key] = $value;
                    }
                }
            }
            $id = $this->insert($group);
            $address1['group_id'] = $id;
            $address2['group_id'] = $id;
            $address3['group_id'] = $id;
            if ($address1update) {
                $this->db->insert('group_addresses', $address1);
            }
            if ($address2update) {
                $this->db->insert('group_addresses', $address2);
            }
            if ($address3update) {
                $this->db->insert('group_addresses', $address3);
            }
            if (isset($data['userid']) && count($data['userid'])) {
                foreach($data['userid'] as $userid) {
                    $user_group = array(
                        'user_id' => $userid,
                        'group_id' => $id,
                    );
                    $this->db->insert('users_groups', $user_group);
                }
            }

            return array('success' => true, 'message' => 'Added group successfully.' ,'id' => $id);

        }
        else
        {
            return array('success' => false, 'message' => validation_errors());
        }

    }
    
    public function get_simple_group($name) {
        $this->db->select('groups.id, groups.name, permission_presets.name as permname');
        $this->db->where('groups.name', $name);
        $this->db->join('permission_presets', 'permission_presets.id=groups.permissions_id', 'left');
        $query = $this->db->get('groups');
        if ($query->num_rows()) {
            return $query->row_array();
        } else {
            return false;
        }
    }
    public function get_users_groups($userid) {
        $this->db->select('groups.id, groups.name, permission_presets.name as permname');
        $this->db->from('users_groups');
        $this->db->join('groups', 'group_id=groups.id');
        $this->db->join('permission_presets', 'permissions_id=permission_presets.id', 'left');
        $this->db->where('user_id', $userid);
        return $this->db->get()->result();
    }
    public function groups_by_ids($ids) {
        $this->db->select('groups.id, groups.name, permission_presets.name as permname');
        $this->db->from('groups');
        $this->db->join('permission_presets', 'permissions_id=permission_presets.id', 'left');
        $this->db->where_in('groups.id', $ids);
        return $this->db->get()->result();
    }

    public function get_address($group_id) {
        return $this->db
                ->select("  group_addresses.*,
                            groups.name,
                            groups.credit,
                            CONCAT(users.first_name,' ',users.last_name) AS rep_name,
                            users.phone_1,
                            users.phone_2,
                            users.phone_3
                         ", false)
                ->from('groups')
                ->join('group_addresses', 'groups.id=group_addresses.group_id AND addressnum=1', 'left')
                ->join('users', 'groups.rep_id=users.id', 'left')
                ->where('groups.id', $group_id)
                ->get()->row();
    }
    public function get_shippingAddress($id) {
        return $this->db
            ->select("*")
            ->from('group_addresses')
            ->where('group_addresses.group_id', $id)
            ->where('group_addresses.address_type', 'Shipping')
            ->get()->row();
    }

    function get_members($group, $use_name=false) {
        $user_assoc = array();
        $this->db
                ->select("users.id, CONCAT(first_name,' ',last_name) as name", false);
        if ($use_name) {
            $this->db->from('groups')
                ->join('users_groups', 'group_id=groups.id')
                ->join('users', 'users.id=user_id');
        } else {
            $this->db->from('users_groups')
                ->join('users', 'users.id=user_id');
        }
        
        if ($use_name) {
            $users = $this->db->where('groups.name', $group)->get()->result();
        } else {
            $users = $this->db->where('group_id', $group)->get()->result();
        }
        foreach ($users as $user) {
            $user_assoc[$user->id] = $user->name;
        }
        //prd($this->db->last_query());
        return $user_assoc;
    }
    
    function get_tech_members($group) {
        $group_techs = $this->get_members_with_feature($group, 10);
        if ($group == 1) {
            return $group_techs;
        }
        $indow_techs = $this->get_members_with_feature(1, 10);
        return $group_techs + $indow_techs;
    }

    
    function get_members_with_feature($group_id, $feature_id) {
        $group_level_row = $this->db
                ->from('permissionPresetPermissions')
                ->join('groups', 'permissions_id=permission_preset_id')
                ->where('groups.id', $group_id)
                ->where('feature_id', $feature_id) //mapp
                ->get()->row();
        $group_level = $group_level_row ? $group_level_row->permission_level_id : 1;

        $feature_id = $this->db->escape($feature_id);
        $users = $this->db
                ->select("
                    users.id,
                    CONCAT(first_name,' ',last_name) as name,
                    (SELECT permission_level_id FROM permissionPresetPermissions WHERE permission_preset_id=users.permission_set AND feature_id=$feature_id) as user_preset_level,
                    (SELECT permission_level_id FROM user_feature_permissions WHERE user_id=users.id AND feature_id=$feature_id) as user_level
                ", false)
                ->from('users_groups')
                ->join('users', 'users.id=user_id')
                ->where('group_id', $group_id)
                ->where('users.active', 1)
                ->get()->result();
        $user_assoc = array();
        foreach ($users as $user) {
            $perm_level = 1;
            if ($user->user_level) {
                $perm_level = $user->user_level;
            } elseif ($user->user_preset_level) {
                $perm_level = $user->user_preset_level;
            } else {
                $perm_level = $group_level;
            }
            if ($perm_level != 1) {
                $user_assoc[$user->id] = $user->name;
            }
        }
        return $user_assoc;
    }
    
    //--------------------------------------------------------------------
    // Form Validation Callbacks
    //--------------------------------------------------------------------

    public function groupEditCheck($id,$name)
    {

        $results = $this->get_by(array( 'name' => $name ));

        if (empty($results) OR $results->id == $id)
        {
            return true;
        }
        else
        {
            return "<p>The group name entered is already taken.</p>";
        }

    }

    /**
     * used for the admin group edit and add form. Do not alter.
     */
    public function getGroupList($ids=null)
    {
        if ($ids) {
            $this->db->where_in('id', $ids);
        }
        return $this->db->select('id,name')->order_by('name')->get($this->_table)->result();

    }

    public function verifyNoSubGroupsOrUsersByGroupId($id)
    {

        $sub_groups  = $this->group_sub_group_model->getSubGroupsByGroupId($id);
        $group_users = $this->user_group_model->getUsersByGroupId($id);

        if(empty($sub_groups) AND  empty($group_users)){
            return true;
        }
        else
        {
            return false;
        }
    }

    public function getGroupUploads($group_id){
        return $this->db->select("id, filename, IF(type='logo', 'Logo', 'Estimate Addendum') as type", false)->where('group_id', $group_id)->where('deleted', 0)->get('file_uploads')->result();

    }

    public function get_package_html($group_id) {
        $group = $this->db->select('estimate_package_html')->where('id', $group_id)->get('groups')->row();
        return $group ? $group->estimate_package_html : null;
    }

    public function get_groups_array($extra_vals = array()) {
        $groups = array();
        $dbg = $this->db->select('id, name')->where('deleted', 0)->order_by('name')->get('groups')->result();
        foreach ($dbg as $g) {
            $groups[$g->id] = $g->name;
        }
        return $extra_vals + $groups;
    }
}

