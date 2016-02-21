<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Migration_add_reports_table extends CI_Migration
{
    public function up()
    {
        $this->db->trans_start();

        $this->db->query("CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `display_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 for my own 1 for all',
  `from` date NOT NULL,
  `to` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1");

        $this->db->trans_complete();
    }
}

