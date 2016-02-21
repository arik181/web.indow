<?php defined('BASEPATH') OR exit('No direct script access allowed');


    class Migration_add_customers extends CI_Migration
    {

        public function __construct(){
            parent::__construct();
        }


        public function up()
        {

            // Get 20 users who do not belong to indow admin or indow rep group

            $sql = "SELECT users.id, users.first_name, users.last_name, users.organization_name, groups.name AS group_name, groups.id AS group_id
                    FROM users
                    JOIN users_groups ON users_groups.user_id = users.id
                    JOIN groups ON groups.id = users_groups.group_id
                    WHERE group_id NOT
                    IN ( 1, 2 )
                    AND users.deleted =0
                    GROUP BY users.id LIMIT 20";

            $new_customers = $this->db->query($sql)->result_array();

            foreach( $new_customers as $row ){

                $this->db->where('id', $row['id']);
                $this->db->update('users', array('is_customer' => 1));
                $this->db->where('user_id', $row['id']);
                $this->db->delete('user_preset_permissions');

                $this->db->insert('customers',array(
                    'customer_company_name' => $row['organization_name'] ,
                    'sales_modifier_id' => rand(1,4),
                    'user_id' => $row['id'],
                    'customer_referred_by' => rand(3,89),
                    'customer_preferred_contact' => rand(3,89)
                ));
            }

        }

        public function down(){
            
        }

    }