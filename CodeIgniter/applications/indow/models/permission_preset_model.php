<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Permission_preset_model extends MM_Model
{
    protected  $_table = 'permission_presets';
    protected  $soft_delete = TRUE;
    protected  $_key = 'permission_presets.id';

    public function __construct(){
        parent::__construct();
    }

    public function get_perm_options() {
        $sql = "SELECT * FROM features";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function update_permission($id, $data)
    {
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('email_1', 'Email', 'required|valid_email|callback_emailEdit_check');

        if ($this->form_validation->run() == TRUE) 
        {
            foreach ( $data as $key => $value )
            {
                if ( $key   !== 'submit'
                &&   $value !== '' )
                {
                    $permission[$key] = $value;
                }
            }

            if (!isset($permission['disabled']))
                $permission['disabled'] = 0;
            if (!isset($permission['certified']))
                $permission['certified'] = 0;

            $this->update($id, $permission);

        } else {
            return array('success' => false, 'message' => validation_errors());
        }

        return array('success' => true, 'message' => 'Updated permission successfully.');
    }

    public function getProfile($id)
    {
        $sql = '
            SELECT  *
            FROM    permission_presets
            WHERE   permission_presets.id = ?;
            ';

        $query = $this->db->query($sql, $id);

        if ( ! empty($query)  && $query->num_rows() > 0)
        {
            $perm = $query->row();
            $perm->perms = $this->db->get_where('permissions', array('permissions_id' => $id))->result();
            return $perm;
        }
        else
        {
            return false;
        }

    }

    public function defaultSettings()
    {
        $permission = new stdClass();
        $permission->ip_address        = '';
        $permission->first_name        = '';
        $permission->last_name         = '';
        $permission->permissionname          = '';
        $permission->password          = '';
        $permission->active            = 1;
        $permission->organization_name = '';
        $permission->bio               = '';
        $permission->email_1           = '';
        $permission->email_type_1      = '';
        $permission->email_2           = '';
        $permission->email_type_2      = '';
        $permission->phone_1           = '';
        $permission->phone_type_1      = '';
        $permission->phone_2           = '';
        $permission->phone_type_2      = '';
        $permission->address_1         = '';
        $permission->address_1_ext     = '';
        $permission->address_2         = '';
        $permission->address_2_ext     = '';
        $permission->city_1            = '';
        $permission->city_2            = '';
        $permission->state_1           = '';
        $permission->state_2           = '';
        $permission->zipcode_1         = '';
        $permission->zipcode_2         = '';
        $permission->company_id        = 1;
        $permission->certified         = 0;
        $permission->deleted           = 0;
        $permission->disabled          = 0;

        return $permission;
    }

    public function fetch_permissions($limit, $start)
    {
        $sql = 'SELECT  permission_presets.*,
                (SELECT count(*) FROM group_permissions WHERE permission_preset_id = permission_presets.id) AS groupcount,
                (SELECT count(*) FROM groups
                    LEFT JOIN users_groups ON users_groups.group_id=groups.id
                    JOIN users ON users.id=users_groups.user_id
                    WHERE groups.permissions_id=permission_presets.id AND user_id IS NOT NULL AND users.deleted != 1) as usercount
                FROM   `permission_presets`
                WHERE   permission_presets.deleted = 0
                LIMIT   ?, ?';
        //WHERE   permissions.deleted = 0 AND permission_permissions.permission_id = 1

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
    
    public function fetch_permissions_list($includeblank = false) {
        $permso = $this->fetch_permissions(999, 0);
        if ($includeblank) {
            $perms = array('' => '');
        } else {
            $perms = array();
        }
        foreach($permso as $perm) {
            if($perm->id != 2){
                $perms[$perm->id] = $perm->name;
            }
        }
        return $perms;
    }

    public function add_permission($data, $id=null)
    {
        $this->form_validation->set_rules('name', 'Permission Name', 'required');

        if ($this->form_validation->run() == TRUE) 
        {
            $permission = array('name' => $data['name'], 'deleted' => 0);

            if (!$id) {
                $new = true;
                $this->db->insert('permission_presets', $permission);
                $id = $this->db->insert_id();
                $successmessage = 'Added permission successfully.';
            } else {
                $new = false;
                $this->db->where('id', $id);
                $this->db->update('permission_presets', $permission);
                $successmessage = 'Updated permission successfully.';
            }

            $count = 0;
            if (!$new)
            {
                //all permissions are reinserted so delete first or duplicates happen
                $this->db->where('permissions_id', $id);
                $this->db->delete('permissions'); 
            }


            while (isset($data['option' . $count])) {
                $perm = array(
                    'permissions_id'    => $id,
                    'feature_id'        => $data['option' . $count],
                    'level'             => $data['value' . $count],
                );
                $this->db->insert('permissions', $perm);
                $count++;

            }

        }
        else
        {
            return array('success' => false, 'message' => validation_errors());
        }

        return array('success' => true, 'message' => $successmessage);
    }

    /**
     * @param $id
     * @param $name
     * @return bool
     */
    public function is_uniqueUpdate($name,$id)
    {
        $results = $this->get_by(array( 'name' => $name ));

        if (empty($results) OR $results->id == $id)
        {
            return true;
        }
        else
        {
            return false;
        }
    }


}
