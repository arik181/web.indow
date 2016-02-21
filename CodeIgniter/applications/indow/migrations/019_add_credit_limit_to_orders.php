<?php defined('BASEPATH') OR exit('No direct script access allowed');


    /**
     * Class Migration_add_orders
     */
    class Migration_add_credit_limit_to_orders extends CI_Migration
    {

        public function up(){
                $this->db->query("ALTER TABLE  `orders` ADD  `credit_limit` TINYINT NOT NULL DEFAULT  '0' AFTER  `total`;");
        }

    }