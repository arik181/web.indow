<?php defined('BASEPATH') OR exit('No direct script access allowed');


    /**
     * Class Seeder
     *
     *
     */


    class Seeder extends CI_Controller{


        protected $_faker;
        protected $_user_ids;
        protected $_site_ids;
        protected $_history_id;
        protected $_square_foot_price = 1.00;
        protected $_screen_price = 1.00;
        protected $_kit_price = 1.00;


        public function __construct()
        {
            parent::__construct();
            $this->load->model(array('user_model', 'customer_model', 'estimate_model','history_model','estimate_has_item_model','item_model','product_model'));

//            $this->_faker = Faker\Factory::create();
            $this->output->enable_profiler(TRUE);
        }

        public function index(){

            $this->getSiteIds();
            $this->getUserIds();
            $this->estimates();
            //redirect('estimates');
            echo "you made it";

        }

        public function createHistory(){

            $this->_history_id = $this->history_model->insert(array('id' => null));

        }



        public function estimates(){

            $i = 0;
            while($i < 10){
               // set random created time for estimate
               $time = rand(1262370689,time());
               $created = date("Y-m-d H:i:s" , $time );
               // create history
               $this->createHistory();
               // get random user
               $customer_dealer_ids =  array_rand ( $this->_user_ids , 2 );
               // get random site id
               $site_id = array_rand ( $this->_site_ids , 1 );
               // create estimate

                $data = array(
                    'history_id' => $this->_history_id,
                    'created' => $created,
                    'created_by_id' => $customer_dealer_ids[0],
                    'customer_id' => $customer_dealer_ids[1],
                    'site_id' => $site_id,
                    'name' => 'Estimate Name' ,
                );

                $estimate_id = $this->estimate_model->insert($data);

                $this->createItems($this->estimate_model->get($estimate_id));

                $i++;

            }


        }

        private function createItems($estimate){



            $i = 0;

            while($i < 10){

                // set random created time for estimate
                $data = array(
                    'floor' => 1,
                    'room' => 'Living Room',
                    'product_id' => 1,
                    'site_id' => $estimate->site_id,
                    'price' => ((10 * 10) / 12 ) * $this->_square_foot_price,
                    'width' => 10,
                    'height' => 10,
                    'edging_id' => 1,
                    'parent_item_id' => 0,
                    'special_geom' => 0,
                );

                $item_id = $this->item_model->insert($data);


                $this->item_model->insert(array('product_id' => 2,
                                                'price' => $this->_screen_price,
                                                'site_id' => $estimate->site_id,
                                                'parent_item_id' => $item_id
                ));

                $this->item_model->insert(array('product_id' => 3,
                                                'price' => $this->_kit_price,
                                                'site_id' => $estimate->site_id,
                                                'parent_item_id' => $item_id
                ));

                $i++;

            }

        }



        private function getUserIds(){

            $sql = "SELECT id FROM users";
            $this->_user_ids = $this->db->query($sql)->result_array();

        }

        private function getSiteIds(){

            $sql = "SELECT id FROM sites";
            $this->_site_ids = $this->db->query($sql)->result_array();

        }


    }