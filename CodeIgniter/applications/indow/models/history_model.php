<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


    class history_model extends MM_Model
    {
    	//MM Model Protocol
        protected  $_table = "history";
        protected  $_key = "id";
        protected  $_soft_delete = true;

        //Class Properties
        public $id;
    }