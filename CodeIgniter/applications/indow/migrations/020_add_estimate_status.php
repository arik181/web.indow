<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Migration_add_estimate_status extends CI_Migration
    {
        public function up()
        {
            $this->db->query("ALTER TABLE  `estimates` ADD  `reason_for_closing` VARCHAR( 50 ) NOT NULL AFTER  `parent_estimate_id`");
        }
    }