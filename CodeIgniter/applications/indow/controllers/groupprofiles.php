<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    class GroupProfiles extends MM_Controller
    {

        protected $_message;
        protected $_group;
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

            if($this->_user->in_admin_group){
                redirect('groups/edit/1');
            }

            if(!$this->permissionslibrary->hasUserAccessToFeature( 3 , 6))
            {
                redirect();
            }

        }

        /**
         * List view
         */
        public function index_get()
        {

            $group = $this->Group_model->getProfile($this->_user->group_ids[0]);

            $data = array(
                'content'           => 'modules/profiles/group-profile',
                'title'             => 'Group',
                'nav'               => 'Group',
                'subtitle'          => 'Group',
                'manager'           => 'Group',
                'section'           => 'Group',
                'path'              => 'group/profile',
                'group'             => $group
            );

            if (!$this->_message == false)
            {
                $data['message'] = $this->_message;
            }

            $this->load->view($this->config->item('theme_home'), $data);

        }

        /**
         * List view
         */
        public function index_post()
        {

            $this->form_validation->set_rules('name', 'Group Name', 'required');
            $group_dup_name = $this->groupEditCheck($this->input->post('name'));
            $this->_post = $this->input->post();

            if($this->form_validation->run() == true AND $group_dup_name == true)
            {
                // update group
                $this->Group_model->update($this->_user->group_ids[0],array('name' => $this->input->post('name') ));
                // update group addresses
                $this->updateGroupAddresses();
                $this->session->set_flashdata('message', 'Group Profile updated.');
                redirect('group/profile');

            }
            else
            {

                $data = array(
                    'content'           => 'modules/profiles/group-profile',
                    'title'             => 'Group',
                    'nav'               => 'Group',
                    'subtitle'          => 'Group',
                    'manager'           => 'Group',
                    'section'           => 'Group',
                    'path'              => 'group/profile',
                    'group'             => $this->setPostData(),
                    'message'           => validation_errors() . ( $group_dup_name == true ? '' : $group_dup_name)
                );

                $this->load->view($this->config->item('theme_home'), $data);

            }


        }

        public function updateGroupAddresses()
        {

            $results = array();
            $group_address_one = $this->group_address_model->get_by( array( 'addressnum' => 1 , 'group_id' => $this->_user->group_ids[0] ) );
            $group_address_two = $this->group_address_model->get_by( array( 'addressnum' => 2 , 'group_id' => $this->_user->group_ids[0]) );

            $results[] = $this->group_address_model->update( $group_address_one->id , array(
                'address' => (isset($this->_post['address_1']) ? $this->_post['address_1'] : ''),
                'address_ext' => (isset($this->_post['address_1_ext']) ? $this->_post['address_1_ext'] : ''),
                'city' => (isset($this->_post['city_1']) ? $this->_post['city_1'] : ''),
                'state' => (isset($this->_post['state_1']) ? $this->_post['state_1'] : ''),
                'zipcode' => (isset($this->_post['zipcode_1']) ? $this->_post['zipcode_1'] : ''),
            ));

            $results[] = $this->group_address_model->update( $group_address_two->id , array(
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
            $group                    = new stdClass();
            $group->name        = (isset($this->_post['name']) ? $this->_post['name'] : '');
            $group->phone_1           = (isset($this->_post['phone_1']) ? $this->_post['phone_1'] : '');
            $group->phone_type_1      = (isset($this->_post['phone_type_1']) ? $this->_post['phone_type_1'] : 0);
            $group->phone_2           = (isset($this->_post['phone_2']) ? $this->_post['phone_2'] : '');
            $group->phone_type_2      = (isset($this->_post['phone_type_2']) ? $this->_post['phone_type_2'] : 0);
            $group->address_1         = (isset($this->_post['address_1']) ? $this->_post['address_1'] : '');
            $group->address_1_ext     = (isset($this->_post['address_1_ext']) ? $this->_post['address_1_ext'] : '');
            $group->address_2         = (isset($this->_post['address_2']) ? $this->_post['address_2'] : '');
            $group->address_2_ext     = (isset($this->_post['address_2_ext']) ? $this->_post['address_2_ext'] : '');
            $group->city_1            = (isset($this->_post['city_1']) ? $this->_post['city_1'] : '');
            $group->city_2            = (isset($this->_post['city_2']) ? $this->_post['city_2'] : '');
            $group->state_1           = (isset($this->_post['state_1']) ? $this->_post['state_1'] : '');
            $group->state_2           = (isset($this->_post['state_2']) ? $this->_post['state_2'] : '');
            $group->zipcode_1         = (isset($this->_post['zipcode_1']) ? $this->_post['zipcode_1'] : '');
            $group->zipcode_2         = (isset($this->_post['zipcode_2']) ? $this->_post['zipcode_2'] : '');
            return $group;
        }


        public function groupEditCheck($name)
        {

            $results = $this->Group_model->get_by(array( 'name' => $name ));

            if (empty($results) OR $results->id == $this->_user->group_ids[0])
            {
                return true;
            }
            else
            {
                return "<p>The group name entered is already taken.</p>";
            }

        }

        public function files_json_get($group_id) {
            $results = $this->Group_model->getGroupUploads($group_id);
            $dataTables = array("draw" => 1,
                "recordsTotal"         => count($results),
                "recordsFiltered"      => count($results),
                "data"                 => $results);
            $this->response($dataTables, 200);
        }


    }