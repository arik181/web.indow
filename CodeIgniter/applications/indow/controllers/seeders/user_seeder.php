<?php defined('BASEPATH') OR exit('No direct script access allowed');


    class user_seeder extends MM_Controller
    {


        public function __construct()
        {
            parent::__construct();
        }


        public function index_get()
        {

            $users = getUsersArray();
            $adminUser = $users[0];

            //-----------------------------------------------------
            // Create Admin User
            //-----------------------------------------------------

            $user_name = clean_string($adminUser['first_name'] . $adminUser['last_name']);
            $new_user = array(
                'first_name' => $adminUser['first_name'],
                'last_name' => $adminUser['last_name'],
                'username' => $user_name,
                'password' => $this->Ion_auth_model->hash_password('testing1234'),
                'created_on' => time(),
                'active' => 1,
                'organization_name' => 'IndowAdmin',
                'email_1' => $adminUser['first_name'].'@indow.com',
                'email_type_1' => rand(0,1),
                'email_2' => $user_name . random_string('numeric', 3) .'@work' . 'IndowAdmin' . '.com',
                'email_type_2' => rand(0,1),
                'phone_1' => random_string('numeric', 3) . '-' . random_string('numeric', 3) . '-' . random_string('numeric', 4),
                'phone_type_1' => rand(0,1),
                'phone_2' => random_string('numeric', 3) . '-' . random_string('numeric', 3) . '-' . random_string('numeric', 4),
                'phone_type_2' => rand(0,1),
                'company_id' => 1,
                'certified' => 1,
                'deleted' => 0,
                'disabled' => 0,
                'email' => rand(0,1),
                'mapp_key' => ''
            );

            $this->db->insert('users',$new_user);
            $user_id = $this->db->insert_id();

            //-----------------------------------------------------
            //  Insert New User Address
            //-----------------------------------------------------

            $state = getRandomState();

            // home address
            $address_1 = getRandomRandomAddress();
            $admin_addresses[] = array(
                'user_id' => $user_id,
                'address_type' => 1,
                'address' => $address_1['address'],
                'address_ext' => $address_1['address_ext'],
                'city' => $address_1['city'],
                'state' => $state,
                'zipcode' => $address_1['zipcode']
            );

            // work address
            $address_2 = getRandomRandomAddress();
            $admin_addresses[] = array(
                'user_id' => $user_id,
                'address_type' => 2,
                'address' => $address_2['address'],
                'address_ext' => $address_2['address_ext'],
                'city' => $address_2['city'],
                'state' => $state,
                'zipcode' => $address_2['zipcode']
            );

            $this->db->insert_batch('user_addresses', $admin_addresses);


            //-----------------------------------------------------
            //  Insert New User Group Relation
            //-----------------------------------------------------

            $this->db->insert('users_groups',array('user_id' => $user_id , 'group_id' => 1));

            //-----------------------------------------------------
            //  Insert user feature permission
            //-----------------------------------------------------

            $this->db->insert('user_feature_permissions',array( 'user_id' => $user_id , 'permission_level_id' => 1, 'feature_id' => 1));

            //-----------------------------------------------------
            //  Insert user_permissions
            //-----------------------------------------------------

            $this->db->insert('user_preset_permissions', array( 'user_id' => $user_id , 'permission_preset_id' => 1  ) );

            unset($users[0]);

            foreach($users as $user){

                //-----------------------------------------------------
                //  Insert New User
                //-----------------------------------------------------
                $user_name = clean_string($user['first_name'] . $user['last_name']);
                $group = getRandomGroup();
                $new_user = array(
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'username' => $user_name,
                    'password' => $this->Ion_auth_model->hash_password('testing1234'),
                    'created_on' => time(),
                    'active' => 1,
                    'organization_name' => $group['name'],
                    'email_1' => $user_name . random_string('numeric', 3) .'@home' . $user_name . '.com',
                    'email_type_1' => rand(0,1),
                    'email_2' => $user_name . random_string('numeric', 3) .'@work' . $group['name'] . '.com',
                    'email_type_2' => rand(0,1),
                    'phone_1' => random_string('numeric', 3) . '-' . random_string('numeric', 3) . '-' . random_string('numeric', 4),
                    'phone_type_1' => rand(0,1),
                    'phone_2' => random_string('numeric', 3) . '-' . random_string('numeric', 3) . '-' . random_string('numeric', 4),
                    'phone_type_2' => rand(0,1),
                    'company_id' => $group['id'],
                    'certified' => 1,
                    'deleted' => 0,
                    'disabled' => 0,
                    'email' => rand(0,1),
                    'mapp_key' => ''
                );

                $this->db->insert('users',$new_user);
                $user_id = $this->db->insert_id();

                //-----------------------------------------------------
                //  Insert New User Address
                //-----------------------------------------------------

                $state = getRandomState();

                // home address
                $address_1 = getRandomRandomAddress();
                $addresses[] = array(
                    'user_id' => $user_id,
                    'address_type' => 1,
                    'address' => $address_1['address'],
                    'address_ext' => $address_1['address_ext'],
                    'city' => $address_1['city'],
                    'state' => $state,
                    'zipcode' => $address_1['zipcode']
                );

                // work address
                $address_2 = getRandomRandomAddress();
                $addresses[] = array(
                    'user_id' => $user_id,
                    'address_type' => 2,
                    'address' => $address_2['address'],
                    'address_ext' => $address_2['address_ext'],
                    'city' => $address_2['city'],
                    'state' => $state,
                    'zipcode' => $address_2['zipcode']
                );

                $this->db->insert_batch('user_addresses', $addresses);
                unset($addresses);


                //-----------------------------------------------------
                //  Insert New User Group Relation
                //-----------------------------------------------------

                $this->db->insert('users_groups',array('user_id' => $user_id , 'group_id' => $group['id']));


                //-----------------------------------------------------
                //  Insert user feature permission
                //-----------------------------------------------------

                $this->db->insert('user_feature_permissions',array( 'user_id' => $user_id , 'permission_level_id' => 1, 'feature_id' => rand(1,7)));

                //-----------------------------------------------------
                //  Insert user_permissions
                //-----------------------------------------------------

                $this->db->insert('user_preset_permissions', array( 'user_id' => $user_id , 'permission_preset_id' => rand(2,5)  ) );


            }

        }







    }