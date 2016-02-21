<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_indexes extends CI_Migration
{
    public function up()
    {
        $this->db->trans_start();
        $this->db->query("ALTER TABLE `estimates` ADD INDEX(`site_id`);");
        $this->db->query("ALTER TABLE `quotes` ADD INDEX(`site_id`);");
        $this->db->query("ALTER TABLE `orders` ADD INDEX(`site_id`);");
        $this->db->trans_complete();
    }
}

