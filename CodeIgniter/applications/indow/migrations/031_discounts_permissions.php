<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Migration_discounts_permissions extends CI_Migration
    {
        public function up()
        {
            $this->db->query("ALTER TABLE  `sales_modifiers_groups` ADD PRIMARY KEY (  `sales_modifiers_id` ,  `groups_id` ) ;");
            $this->db->query("ALTER TABLE  `sales_modifiers` ADD  `created_by` INT NOT NULL ,
ADD INDEX (  `created_by` )");
        }
    }
