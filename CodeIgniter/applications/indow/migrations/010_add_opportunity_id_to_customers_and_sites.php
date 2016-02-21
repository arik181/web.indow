<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_opportunity_id_to_customers_and_sites extends CI_Migration
{
    public function up()
    {
        $this->db->trans_start();

        $this->db->query("ALTER TABLE `sites` ADD COLUMN `opportunity_id` VARCHAR(20) NULL DEFAULT 0 COMMENT 'Object ID for contact, coming from SalesForce' AFTER `created_by`");
        $this->db->query("ALTER TABLE `customers` ADD COLUMN `opportunity_id` VARCHAR(20) NULL DEFAULT 0 COMMENT 'Object ID for contact, coming from SalesForce' AFTER `customer_referred_by`");

        $this->db->trans_complete();
    }
}

