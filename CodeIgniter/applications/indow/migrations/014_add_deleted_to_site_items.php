<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Migration_add_deleted_to_site_items extends CI_Migration
{
    public function up()
    {
        $this->db->trans_start();

        $this->db->query("ALTER TABLE  `site_has_items` ADD  `deleted` TINYINT NOT NULL DEFAULT  '0', ADD INDEX (  `deleted` )");

        $this->db->trans_complete();
    }
}

