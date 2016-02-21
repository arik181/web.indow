<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    class user_address_model extends MM_Model
    {
        protected  $_table = 'user_addresses';
        protected  $soft_delete = false;
        protected  $_key = 'id';
    }