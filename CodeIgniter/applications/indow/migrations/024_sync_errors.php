<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Migration_sync_errors extends CI_Migration
    {
        public function up()
        {
            $this->db->query("CREATE TABLE IF NOT EXISTS `sync_errors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `errors` text NOT NULL,
  `jobsites` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");
            $this->db->query("ALTER TABLE  `items` ADD  `measured` TINYINT NOT NULL DEFAULT  '0'");
        }
    }
