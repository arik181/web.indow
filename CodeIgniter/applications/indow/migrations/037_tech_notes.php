<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_tech_notes extends CI_Migration
{
    public function up()
    {
        $this->db->trans_start();
        $this->db->query("ALTER TABLE  `sites` ADD  `tech_notes` TEXT NOT NULL");
        $this->db->trans_complete();
    }
}

