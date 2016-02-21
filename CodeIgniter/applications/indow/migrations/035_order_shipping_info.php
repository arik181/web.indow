<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_order_shipping_info extends CI_Migration
{
    public function up()
    {
        $this->db->trans_start();
        $this->db->query("ALTER TABLE  `orders` ADD  `ship_method` VARCHAR( 30 ) NULL ,
ADD  `carrier` VARCHAR( 30 ) NULL ,
ADD  `tracking_num` VARCHAR( 100 ) NULL");
        $this->db->trans_complete();
    }
}

