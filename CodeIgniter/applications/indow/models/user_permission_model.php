<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    class user_permission_model extends MM_Model
    {
        protected  $_table = 'user_feature_permissions';
        protected  $_key = 'id';


        public function __construct(){
            parent::__construct();
        }


    }