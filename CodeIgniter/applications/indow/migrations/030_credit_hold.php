<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Migration_credit_hold extends CI_Migration
    {
        public function up()
        {
            $this->db->query("ALTER TABLE  `groups` ADD  `credit_hold` TINYINT NOT NULL DEFAULT  '0' AFTER  `credit`");
        }
    }
