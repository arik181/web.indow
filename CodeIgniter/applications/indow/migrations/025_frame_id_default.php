<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Migration_frame_id_default extends CI_Migration
    {
        public function up()
        {
            $this->db->query("ALTER TABLE  `items` MODIFY  `frame_depth_id` INT(11) NOT NULL DEFAULT  '1'");
        }
    }
