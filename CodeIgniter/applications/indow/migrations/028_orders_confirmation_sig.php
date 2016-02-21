<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Migration_orders_confirmation_sig extends CI_Migration
    {
        public function up()
        {
            $this->db->query("ALTER TABLE orders ADD COLUMN confirmation_sig VARCHAR(100) NULL DEFAULT NULL AFTER deleted");
        }
    }
