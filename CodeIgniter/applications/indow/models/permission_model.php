<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Permission_model extends MM_Model
{
    protected  $_table = 'user_feature_permissions';
    protected  $soft_delete = TRUE;
    protected  $_key = 'user_feature_permissions.id';

    protected $permissionLevels = array(3 => 'Admin', 2 =>'Write', 1=>'Read',0=>'None');

    public $debug = false;

    public function resolveLevelString($levelString)
    {
    	foreach ($this->permissionLevels as $level => $value)
    	{
    		if (strtolower($levelString) == strtolower($value))
    			return $level;
    	}
    	return -1;
    }

	/* 
	 * For a given feature (eg, user management, estimation, etc.), and a given permission level (read, write, admin),
	 * return whether or not the user should have access, in this order:
	 * 
	 * 1) See if the user has been granted or restricted access to this feature specifically (overrides everything else)
	 * 2) See if the user has been given a preset permission set that grants or restricts access to this feature
	 * 3) See if the user's group has a preset that grants or restricts access to this feature
	 * 4) Work up the heirarchy, checking every group along the way, until you find a preset that addresses this feature
	 * 5) If you make it to the top of the chain, the feature is meant to be available to everyone regardless of access level (this shouldn't happen often)
	 */

    public function hasPermission($feature_id, $level)
    {
    	//resolve feature
    	$sql = "SELECT * FROM features WHERE id = ?";
    	$feature = $this->db->query($sql,$feature_id)->row();
    	

    	//resolve permission level
    	if (!is_numeric($level))
    		$level = $this->resolveLevelString($level);

    	$this->debug("Checking " . $feature->feature_display_name ." for permission level: " . $this->permissionLevels[$level]);

	 	//1) See if the user has been granted or restricted access to this feature specifically (overrides everything else)
    	$user_id = $this->ion_auth->get_user_id();
    	$sql = "SELECT 				id, level
    			FROM 				permissions
    			WHERE 				user_id = ?
    			AND 				feature_id = ?";
    	$query = $this->db->query($sql,array($user_id,$feature->id));
    	if ($query->num_rows() > 0)
    	{
    		$row = $query->row();
	    	$user_level = $row->level; //user_id/feature_id are a unique pair
	    	if ($user_level >= $level)
	    	{
	    		$this->debug("Permission granted. Explicitly granted for this user (permission id: " . $row->id . ")");
	    		return true;
	    	}
	    	else //permission system could be less restrictive by removing this else clause, depending on client goals
	    	{
	    		$this->debug("Permission denied. Explicitly denied for this user (permission id: " . $row->id . ")");
	    		return false;
	    	}
    	}

    	// 2) See if the user has been given a preset permission set that grants or restricts access to this feature
    	$sql = "SELECT 				level,permissions.permissions_id
    		 	FROM 				user_permissions
    		 	INNER JOIN 			permissions
    		 	ON 					user_permissions.permission_id = permissions.permissions_id
    		 	WHERE 				permissions.user_id = ?
    		 	AND 				feature_id = ?";
    	$query = $this->db->query($sql,array($user_id,$feature->id));
    	if ($query->num_rows() > 0)
    	{
    		$row = $query->row();
	    	$user_level = $row->level; //user_id/feature_id are a unique pair
	    	if ($user_level >= $level)
	    	{
	    		$this->debug("Permission granted. Explicitly granted for this user permission preset (preset id: " . $row->id . ")");
	    		return true;
	    	}
	    	else //permission system could be less restrictive by removing this else clause, depending on client goals
	    	{
	    		$this->debug("Permission denied. Explicitly denied for this user permission preset (preset id: " . $row->id . ")");
	    		return false;
	    	}
    	}


    	//if the user doesn't have explicit permission to access this feature, check their group, then all parent groups until you can resolve a specific permission level.

    	return $this->hasGroupPermission($feature_id, $level, $user_id);
    }

    public function requirePermission($feature_id, $level)
    {
    	if (!$this->hasPermission($feature_id,$level))
    		redirect('/errors/403'); 
    }

    private function hasGroupPermission($feature_id, $level, $user_id)
    {
    	//find all groups the user is a direct member of
    	$sql = "SELECT * FROM users_groups WHERE user_id = ?";
    	$query = $this->db->query($sql,$user_id);
    	if ($query->num_rows() == 0)
    	{
    		$this->debug("Permission denied. User isn't a member of any groups");
    		return false; //All users should be in a group, but return false if they're not, just in case
    	}
    	$results = $query->result();
    	
    	//for each group, recursively work your way up the group heirarchy until you can resolve a permission
    	$tieBreaker = false;
    	foreach ($results as $result)
    	{
    		if ($this->checkGroupPermissions($feature_id, $level, $result->group_id))
    		{
    			$tieBreaker = true;
    			break; //No need to continue hitting the DB if we've already been granted access
    		}
    	}

    	if ($tieBreaker)
    	{
    		$this->debug("Permission granted. At least one group in the heiarchy has access");
    	}
    	else
    	{
    		$this->debug("Permission denied. No groups in the heiarchy has access");
    	}
    	return $tieBreaker;
    }

    private function checkGroupPermissions($feature_id, $level, $group_id)
    {
    	$sql = "SELECT * FROM groups WHERE id = ?";
    	$group = $this->db->query($sql,$group_id)->row();

    	$sql = "SELECT 				level
    		 	FROM 				permissions
    		 	WHERE 				permissions_id = ?
    		 	AND 				feature_id = ?";

    	$query = $this->db->query($sql,array($group->permissions_id,$feature_id));

    	if ($query->num_rows() > 0) // There's an explicit rule set for this feature, report back the result.
    	{
	    	$group_level = $query->row()->level; //user_id/feature_id are a unique pair
	    	if ($group_level >= $level)
	    	{
	    		$this->debug("Permission granted. Group preset grants access to this feature/level");
	    		return true;
	    	}
	    	else
	    	{
	    		$this->debug("Permission potentially denied. Group preset denies access to this feature/level");
	    		return false;
	    	}
    	}

    	if ($group->parent_group_id > 0)
    	{
    		return $this->checkGroupPermissions($feature_id, $level, $group->parent_group_id);
    	}
		$this->debug("Permission potentially denied. No explicit permissions found in hierarchy");
    	return false;
    }

    public function debug($string)
    {
    	if ($this->debug)
	    	pr($string);
    }

    /**
     * Updates Permissions for both Users and PermissionPresets(see the permission preset table)
     * @param     $permissions
     * @param     $id
     * @param int $user_id
     *
     * @return bool
     */
    public function updatePermissionPresetPermissions($permissions,$id,$user_id = 0)
    {

        if(!empty($permissions))
        {

            $this->delete_many_by(array('user_id' => $user_id , 'permissions_id' => $id ));

            foreach($permissions as $row)
            {
                $duplicate_check = $this->get_many_by(array('user_id' => $user_id , 'permissions_id' => $id ,"feature_id" => $row->feature_id,"level" => $row->level));

                if(empty($duplicate_check)){
                    $this->insert(array(
                        "user_id" => $user_id,
                        "permissions_id" => $id,
                        "feature_id" => $row->feature_id,
                        "level" => $row->level,
                    ));
                }

            }

            return true;

        }
        else
        {
            $this->delete_many_by(array('user_id' => $user_id , 'permissions_id' => $id ));
            return false;
        }


    }


}



