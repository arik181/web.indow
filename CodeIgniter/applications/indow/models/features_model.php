<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


    class Features_model extends MM_Model
    {
        protected  $_table = 'features';
        protected  $soft_delete = false;
        protected  $_key = 'features.id';
    }