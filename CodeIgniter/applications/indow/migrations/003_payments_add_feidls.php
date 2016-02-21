<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_payments_add_feidls extends CI_Migration
{
    public function up()
    {
        $this->db->trans_start();

        $this->db->query("ALTER TABLE  `payments` ADD  `deleted` INT NOT NULL DEFAULT  '0';");

        $this->db->query("CREATE TABLE payments_new LIKE payments;");
        $this->db->query("ALTER TABLE `payments_new` DROP `order_created_by`;");
        $this->db->query("DROP TABLE payments;");
        $this->db->query("RENAME TABLE payments_new TO payments;");

        $this->db->query("ALTER TABLE orders DROP FOREIGN KEY fk_orders_sales_modifiers1;");
        $this->db->query("ALTER TABLE `orders` DROP `sales_modifier_id`;");

        $this->db->query("ALTER TABLE orders DROP FOREIGN KEY fk_orders_customers1;");
        $this->db->query("ALTER TABLE `orders` DROP `customer_id`;");

        $this->db->query("ALTER TABLE orders DROP FOREIGN KEY `fk_orders_history1`;");
        $this->db->query("ALTER TABLE `orders` DROP `history_id`;");

        $this->db->query("ALTER TABLE  `orders` CHANGE  `dealer_id`  `dealer_id` INT( 10 ) UNSIGNED NULL;");

        $this->db->query("ALTER TABLE orders DROP FOREIGN KEY fk_orders_dealers1;");

        $this->db->query("ALTER TABLE orders DROP FOREIGN KEY constraint_orders_groups1;");

        $this->db->query("ALTER TABLE  `orders` CHANGE  `order_number_type_sequence`  `order_number_type_sequence` VARCHAR( 10 ) NULL DEFAULT NULL;");

        $this->db->trans_complete();
    }
}
