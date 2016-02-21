<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    class user_group_model extends MM_Model
    {
        protected  $_table = 'users_groups';
        protected  $_key = 'id';


        public function __construct(){
            parent::__construct();
        }

        public function getUsersByGroupId($id){
            return $this->get_many_by(array('group_id' => $id));
        }


    }