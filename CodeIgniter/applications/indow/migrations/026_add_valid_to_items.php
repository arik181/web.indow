<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Migration_add_valid_to_items extends CI_Migration
    {
        public function up()
        {
            $this->db->query("ALTER TABLE  `items` ADD  `valid` TINYINT NOT NULL DEFAULT  '0'");
        }
    }
