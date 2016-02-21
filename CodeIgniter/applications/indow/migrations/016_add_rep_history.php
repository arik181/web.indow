<?php defined('BASEPATH') OR exit('No direct script access allowed');


    /**
     * Class Migration_add_orders
     */
    class Migration_add_rep_history extends CI_Migration
    {
        public function up(){
            $this->db->query("CREATE TABLE IF NOT EXISTS `rep_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `rep_id` int(11) NOT NULL,
  `changed` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`,`rep_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
    $this->db->query("ALTER TABLE  `items` CHANGE  `floor`  `floor` VARCHAR( 20 ) NOT NULL");
        }
    }