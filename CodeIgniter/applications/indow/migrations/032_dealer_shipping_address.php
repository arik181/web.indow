<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Migration_dealer_shipping_address extends CI_Migration
    {
        public function up()
        {
            $this->db->query("ALTER TABLE  `orders` ADD  `dealer_shipping_address_id` INT NOT NULL ,
ADD INDEX (  `dealer_shipping_address_id` )");
            $this->db->query("ALTER TABLE  `orders` CHANGE  `dealer_shipping_address_id`  `dealer_shipping_address_id` INT( 11 ) NULL");
            $this->db->query("ALTER TABLE  `orders` CHANGE  `shipping_address_id`  `shipping_address_id` INT( 11 ) NULL COMMENT  'references user_addresses table'");
        }
    }
