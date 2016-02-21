<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_tech_to_quotes extends CI_Migration
{
    public function up()
    {
        $this->db->trans_start();

        $this->db->query("ALTER TABLE  `quotes` ADD  `tech_id` INT NULL AFTER  `site_id` , ADD INDEX (  `tech_id` )");
        $this->db->query("ALTER TABLE  `estimates` ADD  `tech_id` INT NULL AFTER  `site_id` , ADD INDEX (  `tech_id` )");
        $this->db->query("ALTER TABLE  `estimates` ADD  `tech_assigned` DATETIME NULL AFTER  `tech_id`");

        $this->db->trans_complete();
    }
}

