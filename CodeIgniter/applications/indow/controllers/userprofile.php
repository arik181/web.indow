<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    class UserProfile extends MM_Controller
    {

        protected $_message;
        protected $_user;
        protected $_post;


        public function __construct()
        {

            parent::__construct();

            $this->_user = $this->data['user'];


            if (@!$this->session->flashdata('message') == false)
            {
                $this->_message = $this->session->flashdata('message');
            }
            else
            {
                $this->_message = false;
            }


        }

        /**
         * List view
         */
        public function index_get()
        {

            $data = array(
                'content'           => 'modules/profiles/profile',
                'title'             => 'Profile',
                'nav'               => 'Profile',
                'subtitle'          => 'Profile',
                'manager'           => 'Profile',
                'section'           => 'Profile',
                'path'              => 'profile',
                'user'              => $this->data['user']
            );

            if (!$this->_message == false) {
                $data['message'] = $this->_message;
            }

            $this->load->view($this->config->item('theme_home'), $data);

        }

        /**
         * List view
         */
        public function index_post()
        {

            $this->form_validation->set_rules('first_name', 'First Name', 'required');
            $this->form_validation->set_rules('last_name', 'Last Name', 'required');
            $this->form_validation->set_rules('email_1', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'matches[confirm_password]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password');
            $update_password = false;
            $this->_post = $this->input->post();

            if($this->form_validation->run())
            {

                // update user
                $this->updateUserRecord($update_password);
                // update user addresses
                $this->updateUserAddresses();
                $this->session->set_flashdata('message', 'Profile updated.');
                redirect('profile');

            }
            else
            {

                $data = array(
                    'content'           => 'modules/profiles/profile',
                    'title'             => 'Profile',
                    'nav'               => 'Profile',
                    'subtitle'          => 'Profile',
                    'manager'           => 'Profile',
                    'section'           => 'Profile',
                    'path'              => 'profile',
                    'user'              => $this->setPostData(),
                    'message'           => validation_errors()
                );

                $this->load->view($this->config->item('theme_home'), $data);

            }


        }

        public function updateUserRecord($update_password = false)
        {

            $data = array(
                'first_name'   => (isset($this->_post['first_name']) ? $this->_post['first_name'] : ''),
                'last_name'    => (isset($this->_post['last_name']) ? $this->_post['last_name'] : ''),
                'email_1'      => (isset($this->_post['email_1']) ? $this->_post['email_1'] : ''),
                'email_type_1' => (isset($this->_post['email_type_1']) ? $this->_post['email_type_1'] : 0),
                'email_2'      => (isset($this->_post['email_2']) ? $this->_post['email_2'] : 0),
                'email_type_2' => (isset($this->_post['email_type_2']) ? $this->_post['email_type_2'] : 0),
                'phone_1'      => (isset($this->_post['phone_1']) ? $this->_post['phone_1'] : 0),
                'phone_type_1' => (isset($this->_post['phone_type_1']) ? $this->_post['phone_type_1'] : 0),
                'phone_2'      => (isset($this->_post['phone_2']) ? $this->_post['phone_2'] : 0),
                'phone_type_2' => (isset($this->_post['phone_type_2']) ? $this->_post['phone_type_2'] : 0),
            );

            if($update_password)
            {
                $data['password'] = $this->ion_auth_model->hash_password($this->_post['password']);
            }

            return $this->user_model->update( $this->data['user']->id , $data );

        }

        public function updateUserAddresses()
        {

            $results = array();
            $user_address_one = $this->user_address_model->get_by( array( 'address_type' => 1 , 'user_id' => $this->data['user']->id ) );
            $user_address_two = $this->user_address_model->get_by( array( 'address_type' => 2 , 'user_id' => $this->data['user']->id ) );

            $results[] = $this->user_address_model->update( $user_address_one->id , array(
                                                                    'address' => (isset($this->_post['address_1']) ? $this->_post['address_1'] : ''),
                                                                    'address_ext' => (isset($this->_post['address_1_ext']) ? $this->_post['address_1_ext'] : ''),
                                                                    'city' => (isset($this->_post['city_1']) ? $this->_post['city_1'] : ''),
                                                                    'state' => (isset($this->_post['state_1']) ? $this->_post['state_1'] : ''),
                                                                    'zipcode' => (isset($this->_post['zipcode_1']) ? $this->_post['zipcode_1'] : ''),
                                                                ));

            $results[] = $this->user_address_model->update( $user_address_two->id , array(
                                                                    'address' => (isset($this->_post['address_2']) ? $this->_post['address_2'] : ''),
                                                                    'address_ext' => (isset($this->_post['address_2_ext']) ? $this->_post['address_2_ext'] : ''),
                                                                    'city' => (isset($this->_post['city_2']) ? $this->_post['city_2'] : ''),
                                                                    'state' => (isset($this->_post['state_2']) ? $this->_post['state_2'] : ''),
                                                                    'zipcode' => (isset($this->_post['zipcode_2']) ? $this->_post['zipcode_2'] : ''),
                                                                ) );

            return $results;


        }

        public function setPostData()
        {
            $user                    = new stdClass();
            $user->first_name        = (isset($this->_post['first_name']) ? $this->_post['first_name'] : '');
            $user->last_name         = (isset($this->_post['last_name']) ? $this->_post['last_name'] : '');
            $user->email_1           = (isset($this->_post['email_1']) ? $this->_post['email_1'] : '');
            $user->email_type_1      = (isset($this->_post['email_type_1']) ? $this->_post['email_type_1'] : 0);
            $user->email_2           = (isset($this->_post['email_2']) ? $this->_post['email_2'] : 0);
            $user->email_type_2      = (isset($this->_post['email_type_2']) ? $this->_post['email_type_2'] : 0);
            $user->phone_1           = (isset($this->_post['phone_1']) ? $this->_post['phone_1'] : '');
            $user->phone_type_1      = (isset($this->_post['phone_type_1']) ? $this->_post['phone_type_1'] : 0);
            $user->phone_2           = (isset($this->_post['phone_2']) ? $this->_post['phone_2'] : '');
            $user->phone_type_2      = (isset($this->_post['phone_type_2']) ? $this->_post['phone_type_2'] : 0);
            $user->address_1         = (isset($this->_post['address_1']) ? $this->_post['address_1'] : '');
            $user->address_1_ext     = (isset($this->_post['address_1_ext']) ? $this->_post['address_1_ext'] : '');
            $user->address_2         = (isset($this->_post['address_2']) ? $this->_post['address_2'] : '');
            $user->address_2_ext     = (isset($this->_post['address_2_ext']) ? $this->_post['address_2_ext'] : '');
            $user->city_1            = (isset($this->_post['city_1']) ? $this->_post['city_1'] : '');
            $user->city_2            = (isset($this->_post['city_2']) ? $this->_post['city_2'] : '');
            $user->state_1           = (isset($this->_post['state_1']) ? $this->_post['state_1'] : '');
            $user->state_2           = (isset($this->_post['state_2']) ? $this->_post['state_2'] : '');
            $user->zipcode_1         = (isset($this->_post['zipcode_1']) ? $this->_post['zipcode_1'] : '');
            $user->zipcode_2         = (isset($this->_post['zipcode_2']) ? $this->_post['zipcode_2'] : '');
            return $user;
        }





    }