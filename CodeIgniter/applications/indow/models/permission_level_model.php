<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    class permission_level_model extends MM_Model
    {
        protected  $_table = 'permissionLevels';
        protected  $_key = 'id';


        public function __construct(){
            parent::__construct();
        }


    }