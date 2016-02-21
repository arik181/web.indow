<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_bulk_ship_to_orders extends CI_Migration
{
    public function up()
    {
        $this->db->trans_start();

        $this->db->query("ALTER TABLE  `orders` ADD  `bundle` TINYINT NOT NULL DEFAULT  '0'");

        $this->db->trans_complete();
    }
}

