<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_order_totals extends CI_Migration
{
    public function up()
    {
        $this->db->trans_start();

        $this->db->query("ALTER TABLE  `orders` ADD  `subtotal` FLOAT NOT NULL DEFAULT  '0',
ADD  `discounts` FLOAT NOT NULL DEFAULT  '0',
ADD  `fees` FLOAT NOT NULL DEFAULT  '0',
ADD  `total` FLOAT NOT NULL DEFAULT  '0',
ADD  `updated` DATETIME NULL AFTER  `created`,
ADD  `deleted` INTEGER NOT NULL DEFAULT  '0'");

        $this->db->trans_complete();
    }
}

