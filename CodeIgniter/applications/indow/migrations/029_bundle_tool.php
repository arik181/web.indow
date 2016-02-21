<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Migration_bundle_tool extends CI_Migration
    {
        public function up()
        {
            $this->db->query("CREATE TABLE IF NOT EXISTS `order_bundles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `bundle_target` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`,`bundle_target`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
            $this->db->query("ALTER TABLE `orders` DROP `bundle`");
        }
    }
