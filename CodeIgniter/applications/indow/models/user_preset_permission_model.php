<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    class user_preset_permission_model extends MM_Model
    {
        protected  $_table = 'user_preset_permissions';
        protected  $_key = 'id';


        public function __construct(){
            parent::__construct();
        }


    }