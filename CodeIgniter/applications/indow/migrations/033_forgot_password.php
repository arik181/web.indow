<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Migration_forgot_password extends CI_Migration
    {
        public function up()
        {
            $this->db->query("CREATE TABLE IF NOT EXISTS `password_reset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `reset_code` varchar(64) NOT NULL,
`created` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `reset_code` (`reset_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");
        }
    }
