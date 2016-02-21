<?php defined('BASEPATH') OR exit('No direct script access allowed');


    class sites_seeder extends MM_Controller
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function index_get(){

            for($i = 0; $i < 300; $i++){

                //--------------------------------------------------------------------
                //  STEP :1 Create Job Site
                //--------------------------------------------------------------------

                // Create Random Date

                $time = rand(1409529600,time());
                $created = date("Y-m-d H:i:s" , $time );
                $address = getRandomRandomAddress();
                $sql_created_by = "SELECT users.id,users_groups.group_id FROM users JOIN users_groups ON users_groups.user_id = users.id WHERE users_groups.group_id > 1 ORDER BY RAND()LIMIT 1;";
                $sql_created_by_query = $this->db->query($sql_created_by);
                $created_by_user = $sql_created_by_query->row();

                $new_job_sites =

                    array(
                        'address'        => $address['address'],
                        'address_ext'    => $address['address_ext'],
                        'city'           => $address['city'],
                        'state'          => getRandomState(),
                        'zipcode'        => $address['zipcode'],
                        'address_type'   => rand(1,2),
                        'deleted'        => '0',
                        'created'        => $created,
                        'created_by'     => $created_by_user->id,
                        'opportunity_id' => rand(0,1)
                    );

                $this->db->insert('sites',$new_job_sites);

                $new_site_id = $this->db->insert_id();

                //--------------------------------------------------------------------
                //  STEP :2 Assign job site to random customer
                //--------------------------------------------------------------------

                $sql_customer = "SELECT user_id FROM customers ORDER BY RAND() LIMIT 1;";
                $get_random_customer_query = $this->db->query($sql_customer);
                $customer = $get_random_customer_query->row();

                //--------------------------------------------------------------------
                //  STEP :3 Insert record to tie a customer to the new site
                //--------------------------------------------------------------------

                $this->db->insert('site_customers',array( 'site_id' => $new_site_id , 'customer_id' => $customer->user_id ) );
                $this->db->insert('users_sites',array( 'site_id' => $new_site_id , 'user_id' => $customer->user_id ) );

                //--------------------------------------------------------------------
                //  STEP :4 INTERNAL SITE NOTE
                //--------------------------------------------------------------------

                $this->db->insert( 'notes', array( 'text' => 'This is an internal site note.' , 'created' => $created ));
                $internal_note_id = $this->db->insert_id();
                $this->db->insert( 'site_internal_notes' , array( 'site_id' => $new_site_id , 'note_id' => $internal_note_id ) );

                //--------------------------------------------------------------------
                //  STEP :5 SITE CUSTOMER NOTE
                //--------------------------------------------------------------------

                $this->db->insert( 'notes' , array( 'text' => 'This is a customer site note.' , 'user_id' =>  $customer->user_id , 'created' => $created ));
                $customer_site_note_id = $this->db->insert_id();
                $this->db->insert( 'site_customer_notes' , array( 'site_id' => $new_site_id , 'customer_id' => $customer->user_id, 'note_id' => $customer_site_note_id) );

                //--------------------------------------------------------------------
                //  STEP :6 ADD ROOM
                //--------------------------------------------------------------------

                $rooms = array('Office','Kitchen','Bedroom','Library','Study','Mail','Room','Hallway','Backroom','Nursery','Parlor','Dinning Room');
                $random_key = array_rand($rooms);

                $this->db->insert( 'rooms', array( 'name' => $rooms[$random_key]));
                $new_room_id = $this->db->insert_id();

                //--------------------------------------------------------------------
                //  STEP :7 TIE ROOM TO SITE
                //--------------------------------------------------------------------
                $this->db->insert( 'sites_rooms', array( 'room_id' => $new_room_id , 'site_id' => $new_site_id));
                //--------------------------------------------------------------------
                //  STEP :8 CREATE ITEM
                //--------------------------------------------------------------------

                for($counter = 0; $counter <= 10; $counter++){

                        $new_item =
                        array(
                            'manufacturing_status'    =>  1,
                            'floor'                   =>  rand(1,4),
                            'room'                    =>  $rooms[$random_key],
                            'site_id'                 =>  $new_site_id,
                            'quality_control_id'      => '1',
                            'price'                   =>  80, // default min price
                            'width'                   =>  40, // default width needed to equal the min require price
                            'height'                  =>  40, // default height needed to equal the min require price
                            'edging_id'               =>  1,
                            'special_geom'            =>  0,
                            'deleted'                 =>  0,
                            'location'                => '',
                            'product_types_id'        =>  1,
                            'acrylic_panel_size'      => null,
                            'acrylic_panel_sq_ft'     => null,
                            'acrylic_panel_linear_ft' => null,
                            'acrylic_panel_thickness' => null,
                            'top_spine'               => null,
                            'side_spines'             => null,
                            'frame_step'              => null,
                            'frame_depth_id'          => 1,
                            'notes'                   => '',
                            'drafty'                  => '0',
                            'window_shape_id'         => 1,
                            'unit_num'                => null
                        );

                        $this->db->insert('items',$new_item);
                        $new_item_id = $this->db->insert_id();


                        //--------------------------------------------------------------------
                        //  STEP :9 TIE ITEM TO SITE
                        //--------------------------------------------------------------------
                        $this->db->insert('site_has_items',array('site_id' => $new_site_id , 'item_id' => $new_item_id));
                        //--------------------------------------------------------------------
                        //  STEP :10 TIE ITEM TO ROOM
                        //--------------------------------------------------------------------
                        $this->db->insert('rooms_items',array('room_id' => $new_room_id , 'item_id' => $new_item_id));
                        //--------------------------------------------------------------------
                        //  STEP :11 CREATE ITEM MEASUREMENTS RECORD
                        //--------------------------------------------------------------------
                        $measurements = array(

                            array('valid' => '1','measurement_key' => 'A','measurement_value' => '1','data_set' => NULL),
                            array('valid' => '1','measurement_key' => 'B','measurement_value' => '1','data_set' => NULL),
                            array('valid' => '1','measurement_key' => 'C','measurement_value' => '1','data_set' => NULL),
                            array('valid' => '1','measurement_key' => 'D','measurement_value' => '1','data_set' => NULL),
                            array('valid' => '1','measurement_key' => 'E','measurement_value' => '1','data_set' => NULL),
                            array('valid' => '1','measurement_key' => 'G','measurement_value' => '1','data_set' => NULL),
                            array('valid' => '1','measurement_key' => 'F','measurement_value' => '1','data_set' => NULL),
                            array('valid' => '1','measurement_key' => 'J','measurement_value' => '1','data_set' => NULL)

                        );

                        $measurement_ids = array();

                        foreach($measurements as $row){
                           $this->db->insert('measurements',$row);
                            $measurement_ids[] = $this->db->insert_id();
                        }

                        //--------------------------------------------------------------------
                        //  STEP :11 TIE ITEM TO ITEM MEASUREMENTS
                        //--------------------------------------------------------------------

                        foreach($measurement_ids as $id){
                            $this->db->insert('items_measurements',array('measurement_id' => $id , 'item_id' => $new_item_id));
                    }




                }





            }

        }


    }