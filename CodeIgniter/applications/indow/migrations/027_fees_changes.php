<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Migration_fees_changes extends CI_Migration
    {
        public function up()
        {
            $this->db->query("ALTER TABLE  `sales_modifiers` ADD  `single_use` TINYINT NOT NULL DEFAULT  '0' COMMENT  'is a oneoff fee added from a particular estimate/quote/order'");
            $this->db->query("ALTER TABLE  `sales_modifiers` ADD  `quantity` INT NOT NULL DEFAULT  '1' COMMENT  'single use fees only'");
            $this->db->query("ALTER TABLE  `sales_modifiers` CHANGE  `modifier_type`  `modifier_type` ENUM(  'discount',  'fee',  'tax',  'msrp',  'wholesale' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT  'discount'");
        }
    }
