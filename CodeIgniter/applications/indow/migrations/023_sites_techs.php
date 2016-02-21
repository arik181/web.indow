<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Migration_sites_techs extends CI_Migration
    {
        public function up()
        {
            $this->db->query("CREATE TABLE IF NOT EXISTS `sites_techs` (
  `site_id` int(11) NOT NULL,
  `tech_id` int(11) NOT NULL,
  PRIMARY KEY (`site_id`,`tech_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
            $this->db->query("ALTER TABLE `sites` DROP `tech_id`");
            $this->db->query("ALTER TABLE  `items` ADD  `extension` TINYINT NOT NULL DEFAULT  '0'");
            $this->db->query("ALTER TABLE  `items` CHANGE  `window_shape_id`  `window_shape_id` INT( 11 ) NOT NULL DEFAULT  '1'");
        }
    }