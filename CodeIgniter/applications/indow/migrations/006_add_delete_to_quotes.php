<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Migration_add_delete_to_quotes extends CI_Migration
{
    public function up()
    {
        $this->db->trans_start();

        $this->db->query("ALTER TABLE `quotes` ADD `deleted` TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT 'deleted = 1 means Record Deleted'");

        $this->db->trans_complete();
    }
}

