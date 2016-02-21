<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_estimate_package_html extends CI_Migration
{
    public function up()
    {
        $this->db->trans_start();
        $this->db->query("ALTER TABLE  `groups` ADD  `estimate_package_html` TEXT NOT NULL");
        $this->db->trans_complete();
    }
}

