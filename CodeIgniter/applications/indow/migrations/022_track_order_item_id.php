<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Migration_track_order_item_id extends CI_Migration
    {
        public function up()
        {
            $this->db->query("ALTER TABLE items DROP INDEX manufacturing_status_2");
            $this->db->query("ALTER TABLE  `items` ADD  `site_item_id` INT NULL DEFAULT NULL ,
ADD  `order_id` INT NULL DEFAULT NULL ,
ADD  `order_item_id` INT NULL DEFAULT NULL ,
ADD INDEX (  `site_item_id` ,  `order_id` ,  `order_item_id` )");
            $this->db->query("ALTER TABLE  `sites` ADD  `updated` DATETIME NULL AFTER  `created`");
            $this->db->query("ALTER TABLE  `sites` ADD  `tech_id` INT NULL DEFAULT NULL , ADD INDEX (  `tech_id` )");
        }
    }