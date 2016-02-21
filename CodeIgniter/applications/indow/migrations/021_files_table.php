<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Migration_files_table extends CI_Migration
    {
        public function up()
        {
            $this->db->query("CREATE TABLE IF NOT EXISTS `file_uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `group_id` int(11) NOT NULL,
  `filename` varchar(50) NOT NULL,
  `uploaded` datetime NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`,`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;");
        }
    }