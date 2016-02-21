<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_eula_accepted extends CI_Migration
{
    public function up()
    {
        $this->db->trans_start();
        $this->db->query("ALTER TABLE  `users` ADD  `eula_accepted` TINYINT NOT NULL DEFAULT  '0'");
        $this->db->query("ALTER TABLE  `items` CHANGE  `acrylic_panel_thickness`  `acrylic_panel_thickness` VARCHAR( 10 ) NULL DEFAULT NULL");
        $this->db->trans_complete();
    }
}

