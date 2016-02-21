<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    class group_permission_model extends MM_Model
    {
        protected  $_table = 'group_permissions';
        protected  $_key = 'id';


        public function __construct(){
            parent::__construct();
        }


    }