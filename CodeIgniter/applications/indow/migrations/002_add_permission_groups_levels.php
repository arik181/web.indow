<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Migration_add_permission_groups_levels extends CI_Migration
    {
        public function up()
        {

            /**
             * Create A Permission Table. A substitute for user types.
             */
            $this->db->query("CREATE TABLE IF NOT EXISTS `user_feature_permissions` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) NOT NULL,
          `permission_level_id` int(11) NOT NULL,
          `feature_id` int(11) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `permission_level_id` (`permission_level_id`),
          KEY `user_id` (`user_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;");


            /**
             * Create A Users Permission Table. A substitute for user types.
             */
            $this->db->query("CREATE TABLE IF NOT EXISTS `user_preset_permissions` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(11) unsigned DEFAULT NULL,
          `permission_preset_id` int(11) unsigned DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `user_id_idx` (`user_id`),
          KEY `permission_id_idx` (`permission_preset_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");

//            $this->db->query("ALTER TABLE `user_preset_permissions`
//          ADD CONSTRAINT `user_permissions_permission_id1` FOREIGN KEY (`permission_preset_id`) REFERENCES `permission_presets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
//          ADD CONSTRAINT `user_permissions_user_id1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");


            /**
             * Add Group Permissions
             */
            $this->db->query("CREATE TABLE IF NOT EXISTS `group_permissions` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `group_id` int(11) unsigned DEFAULT NULL,
          `permission_preset_id` int(11) unsigned DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `group_id_idx` (`group_id`),
          KEY `permission_id_idx` (`permission_preset_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");

//            $this->db->query("ALTER TABLE `group_permissions`
//          ADD CONSTRAINT `group_permissions_group_id1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
//          ADD CONSTRAINT `group_permissions_permission_id1` FOREIGN KEY (`permission_preset_id`) REFERENCES `permission_presets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");


            /**
             * Add Features
             */
            $this->db->query("INSERT INTO `features` (`id`, `feature_key`, `feature_display_name`) VALUES
(1, 'customers', 'Customers'),
(2, 'estimates', 'Estimates'),
(3, 'quotes', 'Quotes'),
(4, 'groups', 'Groups'),
(5, 'jobsites', 'Job Sites'),
(6, 'orders', 'Orders'),
(7, 'products', 'Products'),
(8, 'users', 'Users'),
(9, 'fulfillment', 'Fulfillment'),
(10, 'mapp', 'MAPP'),
(11, 'reports', 'Reports'),
(13, 'discounts', 'Discounts'),
(12, 'salesforce', 'SalesForce');");


            /**
             * Add basic permission groups. Since there is no simple user types.
             */
            $default_permission_groups = array(
                array('id' => '1', 'name' => 'Indow Administrators'),
                array('id' => '2', 'name' => 'Indow Reps'),
                array('id' => '3', 'name' => 'Dealers'),
                array('id' => '5', 'name' => 'Customers'),
                array('id' => '6', 'name' => 'Sales'),
                array('id' => '7', 'name' => 'Manufacturing')
            );
            $this->db->insert_batch('permission_presets', $default_permission_groups);



            /**
             * Create a GroupsSubGroups Junction Table
             */
            $fields = array(

                'id'          => array(
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true
                ),
                'group_id'        => array(
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                ),
                'sub_group_id' => array(
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                ),
                'rank' => array(
                    'type'           => 'INT',
                    'constraint'     => 11,
                ),


            );
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table('groupsSubGroups', true);


            /**
             * Create a Permission Presets Feature Permission Level Junction Table to better achieve the goals of Indow requirements.
             */
            $fields = array(

                'id'                   => array(
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true
                ),
                'permission_preset_id' => array(
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ),
                'permission_level_id'  => array(
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ),
                'feature_id'           => array(
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                )

            );
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table('permissionPresetPermissions', true);

            /**
             * Create a Permission Levels Table to better achieve the goals of Indow requirements.
             */
            $fields = array(

                'id'          => array(
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true
                ),
                'name'        => array(
                    'type'       => 'VARCHAR',
                    'constraint' => '256',
                    'null'       => true
                ),
                'description' => array(
                    'type'       => 'VARCHAR',
                    'constraint' => '256',
                    'null'       => true
                ),
                'deleted'     => array(
                    'type'       => 'INT',
                    'constraint' => 1,
                    'default'    => 0
                )

            );
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table('permissionLevels', true);

            $this->db->query("INSERT INTO `permissionLevels` (`id`, `name`, `description`, `deleted`) VALUES
(1, 'None', NULL, 0),
(2, 'View Own', NULL, 0),
(3, 'Edit Own', NULL, 0),
(4, 'View Company', NULL, 0),
(6, 'View Company and Edit Own', NULL, 0),
(5, 'Edit Company', NULL, 0);");


        }
    }
