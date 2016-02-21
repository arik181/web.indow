<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_frame_depth_values extends CI_Migration
{
    public function up()
    {
        $this->db->trans_start();
        $this->db->query("ALTER TABLE  `frame_depth` ADD `validation_value` FLOAT NOT NULL DEFAULT '0'");
        $this->db->query("INSERT INTO `frame_depth` (`id`, `name`, `validation_value`) VALUES (1, 'Less than 5/8 in.', 0.5), (2, 'Narrow (5/8in.-1in.)', 0.75), (3, 'Med (1in.-2in.)', 1.5), (4, 'Deep (2+ in.)', 2.1)");
        $this->db->trans_complete();
    }
}

