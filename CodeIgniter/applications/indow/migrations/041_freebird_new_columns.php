<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_freebird_new_columns extends CI_Migration
{
    public function up()
    {
        $this->db->trans_start();
        $this->db->query("ALTER TABLE  `sites` ADD  `created_from_order` INT NULL DEFAULT NULL");
        $this->db->query("ALTER TABLE  `items` ADD  `freebird_extension` TINYINT NOT NULL DEFAULT  '0' AFTER  `extension`");
        $this->db->query("ALTER TABLE  `items` ADD  `freebird_laser` TINYINT NOT NULL DEFAULT  '0' AFTER  `freebird_extension`");
        $this->db->trans_complete();
    }
}

