<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Migration_Fix_site_customer_notes extends CI_Migration
{
    public function up()
    {
        $this->db->trans_start();

        $this->db->query("ALTER TABLE site_customer_notes DROP INDEX note_id;");
        $this->db->query("ALTER TABLE site_customer_notes DROP INDEX note_idx_1;");
        $this->db->query("ALTER TABLE  `site_customer_notes` CHANGE  `customer_id`  `customer_id` INT( 10 ) UNSIGNED NULL;");

        $this->db->trans_complete();
    }
}

