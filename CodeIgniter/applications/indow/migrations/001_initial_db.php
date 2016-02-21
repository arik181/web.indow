<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Initial_Db extends CI_Migration 
{
    public function up()
    {
        $this->db->trans_start();

        $this->db->query("CREATE TABLE IF NOT EXISTS `ci_sessions` (
          `session_id` varchar(40) NOT NULL DEFAULT '0',
          `ip_address` varchar(45) NOT NULL DEFAULT '0',
          `user_agent` varchar(120) NOT NULL,
          `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
          `user_data` text NOT NULL,
          PRIMARY KEY (`session_id`),
          KEY `last_activity_idx` (`last_activity`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `contractors` (
          `certified` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
          `disabled` tinyint(1) DEFAULT '0',
          `eula` tinyint(1) NOT NULL DEFAULT '0',
          `eula_signed` datetime DEFAULT NULL,
          `secondary_acct_type` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
          `secondary_co` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
          `user_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`user_id`),
          KEY `fk_contractors_users1_idx` (`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `customers` (
          `customer_company_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
          `customer_preferred_contact` int(10) unsigned NOT NULL,
          `sales_modifier_id` int(10) unsigned NOT NULL,
          `user_id` int(10) unsigned NOT NULL,
          `deleted` tinyint(1) NOT NULL DEFAULT '0',
          `customer_referred_by` int(10) unsigned NOT NULL,
          PRIMARY KEY (`user_id`),
          KEY `fk_customers_users1_idx` (`customer_referred_by`),
          KEY `fk_customers_users2_idx` (`customer_preferred_contact`),
          KEY `fk_customers_sales_modifiers1_idx` (`sales_modifier_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `dealers` (
          `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
          `indow_rep_id` int(10) unsigned NOT NULL,
          `user_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`user_id`),
          KEY `fk_dealers_users1_idx` (`indow_rep_id`),
          KEY `fk_dealers_users2_idx` (`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `edging` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `estimates` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `history_id` int(10) unsigned NOT NULL,
          `created` datetime NOT NULL,
          `created_by_id` int(10) unsigned NOT NULL,
          `closed` tinyint(1) unsigned NOT NULL DEFAULT '0',
          `converted` date DEFAULT NULL,
          `dealer_id` int(10) unsigned DEFAULT NULL,
          `processed` tinyint(1) NOT NULL DEFAULT '0',
          `total_square_feet` double DEFAULT '0',
          `estimate_total` double DEFAULT '0',
          `quote_id` int(10) unsigned DEFAULT NULL,
          `quote_created_by` int(10) unsigned DEFAULT NULL,
          `order_id` int(10) unsigned DEFAULT NULL,
          `order_created_by` int(10) unsigned DEFAULT NULL,
          `parent_group_id` int(10) unsigned NOT NULL,
          `customer_id` int(11) NOT NULL,
          `site_id` int(11) NOT NULL,
          `name` varchar(90) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Display name of Estimate',
          `parent_estimate_id` int(10) DEFAULT '0' COMMENT 'ID of parent estimate, 0 for no parent',
          `deleted` tinyint(1) DEFAULT '0' COMMENT '1 = deleted',
          `followup` tinyint(1) DEFAULT '0' COMMENT 'Follow Up Flag, automatically set to 0',
          PRIMARY KEY (`id`),
          KEY `fk_estimates_users1_idx` (`created_by_id`),
          KEY `fk_estimates_quotes1_idx` (`quote_id`),
          KEY `fk_estimates_orders1_idx` (`order_id`),
          KEY `fk_estimates_history1_idx` (`history_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `estimates_estimate_status_codes` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `status_changed` datetime NOT NULL,
          `estimate_id` int(10) unsigned NOT NULL,
          `estimate_status_code_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`id`),
          KEY `fk_orders_status_codes_copy1_estimates1_idx` (`estimate_id`),
          KEY `fk_orders_status_codes_copy1_status_codes_copy11_idx` (`estimate_status_code_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `estimates_has_customers` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `estimate_id` int(11) NOT NULL,
          `customer_id` int(11) NOT NULL,
          `primary` tinyint(4) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `estimate_id` (`estimate_id`),
          KEY `customer_id` (`customer_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `estimates_has_fees` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `estimate_id` int(11) NOT NULL,
          `sales_modifier_id` int(11) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `estimate_id` (`estimate_id`),
          KEY `sales_modifier_id` (`sales_modifier_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `estimates_has_item` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `estimate_id` int(10) unsigned NOT NULL,
          `item_id` int(10) unsigned NOT NULL,
          `deleted` tinyint(1) DEFAULT '0',
          PRIMARY KEY (`id`),
          KEY `fk_estimates_has_item_item1_idx` (`item_id`),
          KEY `fk_estimates_has_item_estimates1_idx` (`estimate_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `estimates_notes` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `note_id` int(10) unsigned NOT NULL,
          `estimate_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`id`,`note_id`,`estimate_id`),
          KEY `fk_estimates_notes_notes1_idx` (`note_id`),
          KEY `fk_estimates_notes_estimates1_idx` (`estimate_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `estimate_status_codes` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `code` int(10) unsigned NOT NULL,
          `text` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
          PRIMARY KEY (`id`,`code`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `features` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `feature_key` varchar(45) DEFAULT NULL,
          `feature_display_name` varchar(45) DEFAULT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `feature_key_UNIQUE` (`feature_key`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `frame_depth` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(20) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `groups` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
          `parent_group_id` int(10) unsigned NOT NULL,
          `email_1` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `email_type_1` int(11) NOT NULL DEFAULT '1',
          `email_2` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
          `email_type_2` int(11) DEFAULT NULL,
          `phone_1` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
          `phone_type_1` int(11) NOT NULL DEFAULT '1',
          `phone_2` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
          `phone_type_2` int(11) DEFAULT NULL,
          `company_id` int(11) unsigned NOT NULL,
          `deleted` tinyint(1) NOT NULL DEFAULT '0',
          `signed_agreement_name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
          `rep_id` int(11) NOT NULL,
          `credit` float NOT NULL,
          `permissions_id` int(11) NOT NULL,
          `group_type_id` int(11),
          PRIMARY KEY (`id`,`parent_group_id`),
          KEY `fk_groups_group_types1_idx` (`parent_group_id`),
          KEY `permissions_id` (`permissions_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `group_addresses` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `group_id` int(11) NOT NULL,
          `addressnum` int(11) NOT NULL,
          `address_type` enum('Home','Work','Billing','Shipping') NOT NULL DEFAULT 'Home',
          `address` varchar(45) NOT NULL DEFAULT '',
          `address_ext` varchar(45) NOT NULL DEFAULT '',
          `city` varchar(45) NOT NULL DEFAULT '',
          `state` varchar(2) NOT NULL DEFAULT '',
          `zipcode` varchar(100) NOT NULL DEFAULT '',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `group_types` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `history` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `items` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `manufacturing_status` int(11) NOT NULL DEFAULT '1',
          `floor` int(11) NOT NULL,
          `room` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
          `site_id` int(10) unsigned NOT NULL,
          `product_attributes_id` int(10) unsigned NOT NULL COMMENT 'window_attributes, mullion_attributes, etc., depends on product_type',
          `quality_control_id` int(10) unsigned NOT NULL,
          `price` double NOT NULL,
          `image_url` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
          `width` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
          `height` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
          `edging_id` int(10) NOT NULL,
          `special_geom` tinyint(1) NOT NULL,
          `deleted` tinyint(1) NOT NULL,
          `location` varchar(45) COLLATE utf8_unicode_ci DEFAULT '',
          `product_types_id` int(11) NOT NULL,
          `acrylic_panel_size` int(10) DEFAULT NULL,
          `acrylic_panel_sq_ft` int(10) DEFAULT NULL,
          `acrylic_panel_linear_ft` int(10) DEFAULT NULL,
          `acrylic_panel_thickness` int(10) DEFAULT NULL,
          `top_spine` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
          `side_spines` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
          `frame_step` int(10) DEFAULT NULL,
          `frame_depth_id` int(11) NOT NULL,
          `notes` text COLLATE utf8_unicode_ci NOT NULL,
          `drafty` tinyint(4) NOT NULL DEFAULT '0',
          `window_shape_id` int(11) NOT NULL,
          `unit_num` int(11) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `fk_item_quality_control1_idx` (`quality_control_id`),
          KEY `product_types_id` (`product_types_id`),
          KEY `frame_depth_id` (`frame_depth_id`),
          KEY `window_shape_id` (`window_shape_id`),
          KEY `manufacturing_status` (`manufacturing_status`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `items_measurements` (
          `id` int(10) NOT NULL AUTO_INCREMENT,
          `measurement_id` int(10) unsigned NOT NULL,
          `item_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`id`),
          KEY `measurement_id` (`measurement_id`),
          KEY `item_id` (`item_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `item_has_notes` (
          `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `notes_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`item_id`,`notes_id`),
          KEY `fk_item_has_notes_notes1_idx` (`notes_id`),
          KEY `fk_item_has_notes_item1_idx` (`item_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `manufacturing_status` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `measurements` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `valid` tinyint(1) NOT NULL DEFAULT '0',
          `measurement_key` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
          `measurement_value` double NOT NULL,
          `data_set` enum('WL','WB','HL','HR') COLLATE utf8_unicode_ci DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `mullion` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `notes` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `text` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
          `user_id` int(11) NOT NULL,
          `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `orders` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `order_number` int(10) unsigned NOT NULL COMMENT 'Order numbers are user facing, and are not unique. Order numbers have an integer component, as well as a varchar type component.\n\ne.g.: 1201-bo, 1201-rm1',
          `order_number_type` enum('','rm','bo','incl','hold') COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `order_number_type_sequence` int(10) unsigned DEFAULT NULL,
          `history_id` int(10) unsigned NOT NULL,
          `created` datetime NOT NULL,
          `created_by` int(10) unsigned NOT NULL,
          `closed` tinyint(1) NOT NULL DEFAULT '0',
          `status_code` int(10) unsigned NOT NULL,
          `signed_purchase_order` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'We have a signed PO if this box is checked.',
          `down_payment_received` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Downpayment received.',
          `final_payment_received` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Final payment received.',
          `order_confirmation_sent` tinyint(1) NOT NULL DEFAULT '0',
          `credit_hold` tinyint(1) NOT NULL DEFAULT '0',
          `expedite_date` datetime DEFAULT NULL,
          `commit_date` datetime DEFAULT NULL,
          `sales_modifier_id` int(10) unsigned NOT NULL,
          `shipment_id` int(10) unsigned DEFAULT NULL,
          `quote_id` int(10) unsigned DEFAULT NULL,
          `quote_created_by` int(10) unsigned DEFAULT NULL,
          `customer_id` int(10) unsigned NOT NULL,
          `customer_user_id` int(10) unsigned NOT NULL,
          `estimate_id` int(10) DEFAULT NULL,
          `estimate_created_by` int(10) DEFAULT NULL,
          `dealer_id` int(10) unsigned NOT NULL,
          `dealer_indow_rep_id` int(10) unsigned NOT NULL,
          `dealer_user_id` int(10) unsigned NOT NULL,
          `site_id` int(11) NOT NULL DEFAULT '0',
          `manufacturing_date` datetime DEFAULT NULL,
          `materials_ordered` tinyint(1) DEFAULT NULL,
          `expedite` tinyint(1) DEFAULT NULL,
          `build_date` datetime DEFAULT NULL,
          `followup` tinyint(1) DEFAULT '0' COMMENT 'Follow Up Flag, automatically set to 0',
          `group_id` int(10) unsigned NOT NULL,
          `po_num` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
          `shipping_address_id` int(11) NOT NULL COMMENT 'references user_addresses table',
          PRIMARY KEY (`id`),
          KEY `fk_orders_users1_idx` (`created_by`),
          KEY `fk_orders_sales_modifiers1_idx` (`sales_modifier_id`),
          KEY `fk_orders_shipments1_idx` (`shipment_id`),
          KEY `fk_orders_quotes1_idx` (`quote_id`),
          KEY `fk_orders_customers1_idx` (`customer_id`),
          KEY `fk_orders_history1_idx` (`history_id`),
          KEY `fk_orders_orders1_idx` (`estimate_id`),
          KEY `fk_orders_dealers1_idx` (`dealer_id`),
          KEY `group_id` (`group_id`),
          KEY `shipping_address_id` (`shipping_address_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `orders_combined` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `order_id` int(10) unsigned NOT NULL,
          `group_id` int(10) unsigned NOT NULL,
          `batch_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`id`),
          KEY `order_id` (`order_id`),
          KEY `group_id` (`group_id`),
          KEY `batch_id` (`batch_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `orders_combined_batch` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `created` datetime NOT NULL,
          `updated` datetime NOT NULL,
          `status` int(10) NOT NULL,
          `build_date` datetime NOT NULL,
          `commit_date` datetime NOT NULL,
          `ship_method` enum('Will Call','Local Delivery','Ground','Air','') NOT NULL,
          `carrier` enum('UPS','DHL','USPS') NOT NULL,
          `total_panels` int(10) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `orders_has_customers` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `order_id` int(11) NOT NULL,
          `customer_id` int(11) NOT NULL,
          `primary` tinyint(4) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `order_id` (`order_id`),
          KEY `customer_id` (`customer_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `orders_has_fees` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `order_id` int(11) NOT NULL,
          `sales_modifier_id` int(11) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `order_id` (`order_id`),
          KEY `sales_modifier_id` (`sales_modifier_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `orders_has_item` (
          `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `order_order_number_id` int(10) unsigned NOT NULL,
          `order_created_by` int(10) unsigned NOT NULL,
          `item_id` int(10) unsigned NOT NULL,
          `item_quality_control_id` int(10) unsigned NOT NULL,
          `deleted` tinyint(4) NOT NULL DEFAULT '0',
          PRIMARY KEY (`order_id`,`item_id`),
          KEY `fk_orders_has_item_item1_idx` (`item_id`),
          KEY `fk_orders_has_item_orders1_idx` (`order_id`),
          KEY `deleted` (`deleted`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `orders_notes` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `note_id` int(10) unsigned NOT NULL,
          `order_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`id`,`note_id`,`order_id`),
          KEY `fk_orders_notes_notes1_idx` (`note_id`),
          KEY `fk_orders_notes_orders1_idx` (`order_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `orders_sales_modifiers` (
          `orders_id` int(10) NOT NULL COMMENT 'ID in the orders table',
          `sales_modifiers_id` int(10) NOT NULL COMMENT 'ID in the sales_modifiers table',
          KEY `osm_orders_id_idx` (`orders_id`),
          KEY `osm_sales_modifiers_id_ix` (`sales_modifiers_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Join table to join sales modifiers to orders, can have multiple modifiers per order, and multiple orders per modifier';");

        $this->db->query("CREATE TABLE IF NOT EXISTS `orders_status_codes` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `status_changed` datetime NOT NULL,
          `order_id` int(10) unsigned NOT NULL,
          `order_status_code_id` int(10) unsigned NOT NULL,
          `user_id` int(11) NOT NULL,
          PRIMARY KEY (`id`,`order_id`,`order_status_code_id`),
          KEY `fk_orders_status_codes_orders1_idx` (`order_id`),
          KEY `fk_orders_order_status_codes_order_status_codes1_idx` (`order_status_code_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `order_internal_notes` (
          `note_id` int(10) unsigned NOT NULL,
          `order_id` int(10) unsigned NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `payments` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `payment_amount` double NOT NULL,
          `payment_received` datetime DEFAULT NULL,
          `payment_cleared` datetime DEFAULT NULL,
          `payment_type_id` int(10) unsigned NOT NULL,
          `order_id` int(10) unsigned NOT NULL,
          `order_created_by` int(10) unsigned NOT NULL,
          `payment_made_by_id` int(10) unsigned NOT NULL,
          `archived` tinyint(1) NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`),
          KEY `fk_payments_payment_types1_idx` (`payment_type_id`),
          KEY `fk_payments_orders1_idx` (`order_id`),
          KEY `fk_payments_users1_idx` (`order_created_by`),
          KEY `fk_payments_users2_idx` (`payment_made_by_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `payment_types` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");



        $this->db->query("CREATE TABLE IF NOT EXISTS `permission_presets` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `name` varchar(20) NOT NULL,
          `description` varchar(100) NOT NULL,
          `deleted` int(1) NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `products` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `product` varchar(255) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `product_types` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `product_type` varchar(255) NOT NULL,
          `abbrev` varchar(255) NOT NULL,
          `size` int(11) NOT NULL,
          `description` varchar(255) NOT NULL,
          `custom_add_on` tinyint(1) NOT NULL,
          `opening_specific` tinyint(1) NOT NULL DEFAULT '0',
          `not_opening_specific` tinyint(1) NOT NULL DEFAULT '0',
          `unit_price` double NOT NULL,
          `unit_price_type` enum('sq','unit','','') NOT NULL,
          `min_price` double NOT NULL,
          `max_width` double NOT NULL,
          `max_height` double NOT NULL,
          `product_id` int(11) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `promotion_info` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `promotion_start_date` datetime NOT NULL,
          `promotion_end_date` datetime NOT NULL,
          `event_stamp` datetime NOT NULL,
          `rebate_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
          `percent_for_estimate` double NOT NULL,
          `percent_for_invoice` double NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `quality_control` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `cut_by_id` int(10) unsigned NOT NULL,
          `checked_by_id` int(10) unsigned NOT NULL,
          `cut_checked_by_id` int(10) unsigned NOT NULL,
          `gasket_cut_by_id` int(10) unsigned NOT NULL,
          `gasket_checked_by_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`id`),
          KEY `fk_quality_control_users1_idx` (`cut_by_id`),
          KEY `fk_quality_control_users2_idx` (`cut_checked_by_id`),
          KEY `fk_quality_control_users3_idx` (`gasket_cut_by_id`),
          KEY `fk_quality_control_users4_idx` (`gasket_checked_by_id`),
          KEY `fk_quality_control_users5_idx` (`checked_by_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `quotes` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `history_id` int(10) unsigned DEFAULT NULL,
          `created` datetime NOT NULL,
          `created_by` int(10) unsigned NOT NULL,
          `closed` tinyint(1) NOT NULL DEFAULT '0',
          `status_code` int(10) unsigned NOT NULL,
          `measurement_date` datetime DEFAULT NULL,
          `commit_date` datetime DEFAULT NULL,
          `order_id` int(10) unsigned DEFAULT NULL,
          `order_created_by` int(10) unsigned NOT NULL,
          `estimate_id` int(10) unsigned DEFAULT NULL,
          `estimate_created_by_id` int(10) unsigned NOT NULL,
          `parent_group_id` int(10) unsigned NOT NULL,
          `quote_total` int(11) NOT NULL,
          `dealer_id` int(11) NOT NULL,
          `customer_id` int(11) NOT NULL,
          `site_id` int(11) NOT NULL,
          `followup` tinyint(1) DEFAULT '0' COMMENT 'Follow Up Flag, automatically set to 0',
          PRIMARY KEY (`id`),
          KEY `fk_quotes_users1_idx` (`created_by`),
          KEY `fk_quotes_orders1_idx` (`order_id`),
          KEY `fk_quotes_estimates1_idx` (`estimate_id`),
          KEY `fk_quotes_history1_idx` (`history_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `quotes_has_customers` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `quote_id` int(11) NOT NULL,
          `customer_id` int(11) NOT NULL,
          `primary` tinyint(4) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `quote_id` (`quote_id`),
          KEY `customer_id` (`customer_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `quotes_has_fees` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `quote_id` int(11) NOT NULL,
          `sales_modifier_id` int(11) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `quote_id` (`quote_id`,`sales_modifier_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `quotes_has_item` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `quote_id` int(10) unsigned NOT NULL,
          `item_id` int(10) unsigned NOT NULL,
          `deleted` tinyint(4) NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`),
          KEY `fk_quotes_has_item_item1_idx` (`item_id`),
          KEY `fk_quotes_has_item_quotes1_idx` (`quote_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `quotes_notes` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `note_id` int(10) unsigned NOT NULL,
          `quote_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`id`,`note_id`,`quote_id`),
          KEY `fk_quotes_notes_notes1_idx` (`note_id`),
          KEY `fk_quotes_notes_quotes1_idx` (`quote_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `rooms` (
          `id` int(10) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `rooms_items` (
          `id` int(10) NOT NULL AUTO_INCREMENT,
          `room_id` int(10) NOT NULL,
          `item_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`id`),
          KEY `room_id` (`room_id`),
          KEY `item_id` (`item_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `sales_modifiers` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `modifier_type` enum('discount','fee','tax') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'discount' COMMENT 'Determines whether the sales modified is a discount, or a fee - values are ''discount'' or ''fee''',
          `code` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
          `description` varchar(90) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Description of sales modifier',
          `amount` double NOT NULL,
          `modifier` enum('dollar','percent') COLLATE utf8_unicode_ci DEFAULT 'percent' COMMENT 'Used to store and determine whether a sales modifier is a whole dollar amount, or a percentage - valid entries are dollar and percent',
          `start_date` datetime DEFAULT NULL COMMENT 'Discount Start Date',
          `end_date` datetime DEFAULT NULL COMMENT 'Discount End Date',
          `deleted` tinyint(1) DEFAULT '0',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `sales_modifiers_groups` (
          `sales_modifiers_id` int(10) NOT NULL COMMENT 'matched to id in sales_modifiers',
          `groups_id` int(10) NOT NULL COMMENT 'matched to group_id in users_groups and id in groups'
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='pair sales modifiers to groups';");

        $this->db->query("CREATE TABLE IF NOT EXISTS `shipments` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `ship_date` datetime NOT NULL,
          `ship_method` enum('Will Call','Local Delivery','Ground','Air','') COLLATE utf8_unicode_ci NOT NULL,
          `carrier` enum('UPS','DHL','USPS') COLLATE utf8_unicode_ci NOT NULL,
          `labels` tinyint(1) DEFAULT NULL,
          `lists` tinyint(1) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `sites` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `address` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
          `address_ext` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `city` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
          `state` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
          `zipcode` varchar(9) COLLATE utf8_unicode_ci NOT NULL,
          `address_type` tinyint(1) DEFAULT '0',
          `deleted` tinyint(1) DEFAULT '0',
          `created` datetime NOT NULL,
          `created_by` int(10) unsigned NOT NULL,
          PRIMARY KEY (`id`),
          KEY `fk_created_by_1` (`created_by`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `sites_rooms` (
          `id` int(10) NOT NULL AUTO_INCREMENT,
          `room_id` int(10) NOT NULL,
          `site_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`id`),
          KEY `room_id` (`room_id`),
          KEY `site_id` (`site_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `site_customers` (
          `customer_id` int(10) unsigned NOT NULL,
          `site_id` int(10) unsigned NOT NULL,
          `primary` tinyint(1) NOT NULL DEFAULT '0',
          KEY `fk_sites_has_customers_customers1_idx` (`customer_id`),
          KEY `fk_sites_customers_sites1_idx` (`site_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `site_customer_notes` (
          `site_id` int(10) unsigned NOT NULL,
          `customer_id` int(10) unsigned NOT NULL,
          `note_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`note_id`),
          UNIQUE KEY `note_id` (`note_id`),
          KEY `note_idx_1` (`note_id`),
          KEY `site_customer_notes_site_id1` (`site_id`),
          KEY `site_customer_notes_customer_id1` (`customer_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `site_has_items` (
          `site_id` int(10) unsigned NOT NULL,
          `item_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`site_id`,`item_id`),
          KEY `fk_site_has_items_item1_idx` (`item_id`),
          KEY `fk_site_has_items_site1_idx` (`site_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `site_internal_notes` (
          `site_id` int(10) unsigned NOT NULL,
          `note_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`note_id`),
          UNIQUE KEY `note_id` (`note_id`),
          KEY `note_idx_2` (`note_id`),
          KEY `site_id` (`site_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `status_codes` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `code` int(10) unsigned NOT NULL,
          `description` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `subitems` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `item_id` int(11) NOT NULL,
          `product_type_id` int(11) NOT NULL,
          `quantity` int(11) NOT NULL,
          `deleted` tinyint(4) NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`),
          KEY `item_id` (`item_id`),
          KEY `product_type_id` (`product_type_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `users` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `ip_address` int(10) unsigned NOT NULL DEFAULT '1',
          `first_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
          `last_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
          `username` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
          `password` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
          `salt` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
          `activation_code` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
          `forgotten_password_code` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
          `forgotten_password_time` int(11) unsigned DEFAULT NULL,
          `remember_code` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
          `created_on` int(11) unsigned NOT NULL,
          `last_login` int(11) unsigned DEFAULT NULL,
          `active` tinyint(1) unsigned DEFAULT NULL,
          `organization_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
          `bio` text COLLATE utf8_unicode_ci,
          `ios_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
          `android_token` int(11) DEFAULT NULL,
          `email_1` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `email_type_1` int(11) NOT NULL DEFAULT '1',
          `email_2` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
          `email_type_2` int(11) DEFAULT NULL,
          `phone_1` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
          `phone_type_1` int(11) NOT NULL DEFAULT '1',
          `phone_2` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
          `phone_type_2` int(11) DEFAULT NULL,
          `phone_3` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
          `phone_type_3` int(11) NOT NULL,
          `company_id` int(11) unsigned NOT NULL,
          `certified` tinyint(1) unsigned NOT NULL DEFAULT '0',
          `deleted` tinyint(1) DEFAULT '0',
          `disabled` tinyint(1) DEFAULT '0',
          `email` tinyint(1) DEFAULT NULL,
          `is_customer` tinyint(1) DEFAULT 0,
          `mapp_key` varchar(100) DEFAULT '',
          PRIMARY KEY (`id`),
          KEY `first_name` (`first_name`),
          KEY `last_name` (`last_name`),
          KEY `email_1` (`email_1`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `users_groups` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(10) unsigned NOT NULL,
          `group_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`id`,`user_id`,`group_id`),
          KEY `fk_users_groups_users_idx` (`user_id`),
          KEY `fk_users_groups_groups1_idx` (`group_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `users_sites` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `site_id` int(10) unsigned NOT NULL,
          `user_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`id`,`user_id`,`site_id`),
          KEY `fk_users_sites_sites1_idx` (`site_id`),
          KEY `fk_users_sites_users1_idx` (`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `user_addresses` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(11) NOT NULL,
          `address_type` tinyint(1) DEFAULT '1',
          `address` varchar(45) NOT NULL DEFAULT '',
          `address_ext` varchar(45) NOT NULL DEFAULT '',
          `city` varchar(45) NOT NULL DEFAULT '',
          `state` varchar(2) NOT NULL DEFAULT '',
          `zipcode` varchar(100) NOT NULL DEFAULT '',
          `address_type_other` varchar(45) NOT NULL,
          `country` varchar(45) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `windows` (
          `id` int(36) unsigned NOT NULL AUTO_INCREMENT,
          `treatment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          `gasket_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          `shade_grade` double NOT NULL,
          `pane_thickness` double NOT NULL,
          `linear_feet` double NOT NULL,
          `gasket_size` double NOT NULL,
          `top_spine` tinyint(1) NOT NULL DEFAULT '0',
          `side_spines` tinyint(1) NOT NULL DEFAULT '0',
          `safety_hardware` tinyint(1) NOT NULL DEFAULT '0',
          `frame_depth_top` double NOT NULL DEFAULT '0',
          `frame_depth_left` double NOT NULL DEFAULT '0',
          `frame_depth_right` double NOT NULL DEFAULT '0',
          `frame_depth_bottom` double NOT NULL DEFAULT '0',
          `air_filter` tinyint(1) NOT NULL DEFAULT '0',
          `frame_step` tinyint(1) NOT NULL DEFAULT '0',
          `plus_four` tinyint(1) NOT NULL DEFAULT '0',
          `cut_dimensions` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
          `coordinates` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
          `MAPP_width` double NOT NULL,
          `MAPP_width_bottom` double NOT NULL,
          `MAPP_width_top` double NOT NULL,
          `MAPP_height` double NOT NULL,
          `MAPP_height_left` double NOT NULL,
          `MAPP_height_right` double NOT NULL,
          `MAPP_diagonal_left` double NOT NULL,
          `MAPP_diagonal_right` double NOT NULL,
          `cut_script_url` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `window_attributes` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `max_width` double NOT NULL,
          `max_height` double NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `window_shapes` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(30) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;");


        $this->db->query("ALTER TABLE `contractors`
          ADD CONSTRAINT `fk_contractors_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->db->query("ALTER TABLE `customers`
          ADD CONSTRAINT `fk_customers_sales_modifiers1` FOREIGN KEY (`sales_modifier_id`) REFERENCES `sales_modifiers` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
          ADD CONSTRAINT `fk_customers_users1` FOREIGN KEY (`customer_referred_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
          ADD CONSTRAINT `fk_customers_users2` FOREIGN KEY (`customer_preferred_contact`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;");

        $this->db->query("ALTER TABLE `dealers`
          ADD CONSTRAINT `fk_dealers_users1` FOREIGN KEY (`indow_rep_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_dealers_users2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->db->query("ALTER TABLE `estimates`
          ADD CONSTRAINT `fk_estimates_history1` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_estimates_orders1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_estimates_quotes1` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_estimates_users1` FOREIGN KEY (`created_by_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->db->query("ALTER TABLE `estimates_estimate_status_codes`
          ADD CONSTRAINT `fk_orders_status_codes_copy1_estimates1` FOREIGN KEY (`estimate_id`) REFERENCES `estimates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_orders_status_codes_copy1_status_codes_copy11` FOREIGN KEY (`estimate_status_code_id`) REFERENCES `estimate_status_codes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->db->query("ALTER TABLE `estimates_has_item`
          ADD CONSTRAINT `fk_estimates_has_item_estimates1` FOREIGN KEY (`estimate_id`) REFERENCES `estimates` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
          ADD CONSTRAINT `fk_estimates_has_item_item1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");

        $this->db->query("ALTER TABLE `estimates_notes`
          ADD CONSTRAINT `fk_estimates_notes_estimates1` FOREIGN KEY (`estimate_id`) REFERENCES `estimates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_estimates_notes_notes1` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");



        $this->db->query("ALTER TABLE `items_measurements`
          ADD CONSTRAINT `constraint_items_measurements1` FOREIGN KEY (`measurement_id`) REFERENCES `measurements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `constraint_items_measurements` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->db->query("ALTER TABLE `item_has_notes`
          ADD CONSTRAINT `fk_item_has_notes_item1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_item_has_notes_notes1` FOREIGN KEY (`notes_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->db->query("ALTER TABLE `orders`
          ADD CONSTRAINT `constraint_orders_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_orders_customers1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_orders_dealers1` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_orders_history1` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_orders_quotes1` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_orders_sales_modifiers1` FOREIGN KEY (`sales_modifier_id`) REFERENCES `sales_modifiers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_orders_shipments1` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_orders_users1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->db->query("ALTER TABLE `orders_combined`
          ADD CONSTRAINT `orders_combined_ibfk_3` FOREIGN KEY (`batch_id`) REFERENCES `orders_combined_batch` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `orders_combined_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `orders_combined_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->db->query("ALTER TABLE `orders_has_item`
          ADD CONSTRAINT `fk_orders_has_item_item1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_orders_has_item_orders1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->db->query("ALTER TABLE `orders_notes`
          ADD CONSTRAINT `fk_orders_notes_notes1` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_orders_notes_orders1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");

        $this->db->query("ALTER TABLE `orders_status_codes`
          ADD CONSTRAINT `fk_orders_order_status_codes_order_status_codes1` FOREIGN KEY (`order_status_code_id`) REFERENCES `status_codes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_orders_status_codes_orders1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->db->query("ALTER TABLE `payments`
          ADD CONSTRAINT `fk_payments_payment_types1` FOREIGN KEY (`payment_type_id`) REFERENCES `payment_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_payments_users1` FOREIGN KEY (`order_created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_payments_users2` FOREIGN KEY (`payment_made_by_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->db->query("ALTER TABLE `quality_control`
          ADD CONSTRAINT `fk_quality_control_users1` FOREIGN KEY (`cut_by_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_quality_control_users2` FOREIGN KEY (`cut_checked_by_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_quality_control_users3` FOREIGN KEY (`gasket_cut_by_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_quality_control_users4` FOREIGN KEY (`gasket_checked_by_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_quality_control_users5` FOREIGN KEY (`checked_by_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->db->query("ALTER TABLE `quotes`
          ADD CONSTRAINT `fk_quotes_estimates1` FOREIGN KEY (`estimate_id`) REFERENCES `estimates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_quotes_history1` FOREIGN KEY (`history_id`) REFERENCES `history` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_quotes_users1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->db->query("ALTER TABLE `quotes_has_item`
          ADD CONSTRAINT `fk_quotes_has_item_item1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_quotes_has_item_quotes1` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->db->query("ALTER TABLE `quotes_notes`
          ADD CONSTRAINT `fk_quotes_notes_notes1` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_quotes_notes_quotes1` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->db->query("ALTER TABLE `rooms_items`
          ADD CONSTRAINT `constraint_rooms_rooms` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `constraint_rooms_items` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->db->query("ALTER TABLE `sites`
          ADD CONSTRAINT `sites_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->db->query("ALTER TABLE `sites_rooms`
          ADD CONSTRAINT `constraint_rooms_rooms11` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `constraint_sites_rooms1` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->db->query("ALTER TABLE `site_customers`
          ADD CONSTRAINT `fk_sites_customers_sites1` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_sites_has_customers_customers1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->db->query("ALTER TABLE `site_customer_notes`
          ADD CONSTRAINT `site_customer_notes_customer_id1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
          ADD CONSTRAINT `site_customer_notes_note_id1` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
          ADD CONSTRAINT `site_customer_notes_site_id1` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");

        $this->db->query("ALTER TABLE `site_has_items`
          ADD CONSTRAINT `site_has_items_ibfk_1` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
          ADD CONSTRAINT `site_has_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;");

        $this->db->query("ALTER TABLE `site_internal_notes`
          ADD CONSTRAINT `site_internal_notes_ibfk_1` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
          ADD CONSTRAINT `site_internal_notes_ibfk_2` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;");

        $this->db->query("ALTER TABLE `users_groups`
          ADD CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_users_groups_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->db->query("ALTER TABLE `users_sites`
          ADD CONSTRAINT `fk_users_sites_sites1` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `fk_users_sites_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");



        $this->db->query("ALTER TABLE `user_addresses` CHANGE `address_type` `address_type` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Home';");

        $this->db->trans_complete();
    }
}
