<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    /**
     * Class permission_preset_permission_model
     * Used as a replacement for a user type.
     */
    class permission_preset_permission_model extends MM_Model
    {
        protected  $_table = 'permissionPresetPermissions';
        protected  $_key = 'id';


        public function __construct(){
            parent::__construct();
        }

        /**
         * Updates Permissions for PermissionPresets. Permission Presets are a substitute for user types.
         * @param     $permissions
         * @param     $id
         *
         * @return bool
         */
        public function updatePermissionPresetPermissions($permissions,$id)
        {

            if(!empty($permissions))
            {

                $this->delete_many_by(array('permission_preset_id' => $id ));

                foreach($permissions as $row)
                {
                    $duplicate_check = $this->get_many_by(array('permission_preset_id' => $id ,"feature_id" => $row->feature_id,"permission_level_id" => $row->permission_level_id));

                    if(empty($duplicate_check)){
                        $this->insert(array(
                            "permission_preset_id" => $id,
                            "feature_id" => $row->feature_id,
                            "permission_level_id" => $row->permission_level_id,
                        ));
                    }

                }

                return true;

            }
            else
            {
                $this->delete_many_by(array('permission_preset_id' => $id ));
                return false;
            }


        }


    }