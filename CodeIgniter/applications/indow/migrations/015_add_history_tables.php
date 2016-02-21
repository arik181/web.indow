<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_add_history_tables extends CI_Migration 
{
    public function up()
    {
        $this->db->trans_start();

        $this->db->query("CREATE TABLE IF NOT EXISTS `site_customer_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `removed` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`,`customer_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;");

        $this->db->trans_complete();
	}
}
