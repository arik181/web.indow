<?php defined('BASEPATH') OR exit('No direct script access allowed');


    class quote_seeder extends MM_Controller
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function index_get()
        {


            for($i = 0; $i < 300; $i++){

                //--------------------------------------------------------------------
                //  STEP :1 Create an Quote
                //--------------------------------------------------------------------

                // GET A RANDOM ESTIMATE

                $sql_get_estimate = "SELECT * FROM estimates ORDER BY RAND() LIMIT 1;";
                $sql_get_estimate_query = $this->db->query($sql_get_estimate);
                $estimate = $sql_get_estimate_query->row();

                // GET CUSTOMER ID FOR THE JOB SITE

                $sql_estimate_customer_id = "SELECT customer_id FROM estimates_has_customers WHERE estimate_id = ? LIMIT 1";
                $sql_estimate_customer_query = $this->db->query($sql_estimate_customer_id, $estimate->id);
                $estimate_customer = $sql_estimate_customer_query->row();

                // GET ITEMS ATTACHED TO THE ESTIMATE

                $sql_estimate_items         = "SELECT estimates_has_item.item_id FROM estimates_has_item WHERE estimates_has_item.estimate_id = ?";
                $sql_estimate_items_query   = $this->db->query($sql_estimate_items,$estimate->id);
                $estimate_items             = $sql_estimate_items_query->result();

                // Create Random Date

                $time = rand(strtotime($estimate->created),time());
                $created = date("Y-m-d H:i:s" , $time );

                // get group id

                $sql_get_created_by_group        = "SELECT groups.id FROM groups JOIN users_groups ON users_groups.group_id = groups.id WHERE users_groups.user_id = ? LIMIT 1;";
                $sql_get_created_by_group_query  = $this->db->query($sql_get_created_by_group,$estimate->created_by_id);
                $estimate_group                  = $sql_get_created_by_group_query->result_array();

                $new_quote =

                    array(
                        'history_id'             => $estimate->history_id,
                        'created'                => $created,
                        'created_by'             => $estimate->created_by_id,
                        'closed'                 => 0,
                        'status_code'            => 1,
                        'measurement_date'       => '0000-00-00',
                        'commit_date'            => '0000-00-00',
                        'order_id'               => '',
                        'order_created_by'       => $estimate->created_by_id,
                        'estimate_id'            => $estimate->id,
                        'estimate_created_by_id' => $estimate->created_by_id,
                        'parent_group_id'        => $estimate_group[0]['id'],
                        'quote_total'            => $estimate->estimate_total,
                        'dealer_id'              => $estimate->created_by_id,
                        'customer_id'            => $estimate_customer->customer_id,
                        'site_id'                => $estimate->site_id,
                        'followup'               => rand(0,1)
                    );

                $this->db->insert('quotes',$new_quote);
                $new_quote_id = $this->db->insert_id();

                // UPDATE ESTIMATE RECORD TO REFLECT A NEW QUOTE ID
                $this->db->set('quote_id',$new_quote_id);
                $this->db->set('quote_created_by',$estimate->created_by_id);
                $this->db->where('id', $estimate->id);
                $this->db->update('estimates');

                // GET ITEMS ATTACHED TO THE JOB SITE
                $sql_estimate_items         = "SELECT estimates_has_item.item_id FROM estimates_has_item WHERE estimates_has_item.estimate_id = ?";
                $sql_estimate_items_query   = $this->db->query($sql_estimate_items,$estimate->id);
                $estimate_items             = $sql_estimate_items_query->result();

                // ADD ITEMS TO QUOTES_HAS_ITEM
                foreach($estimate_items as $item)
                {
                    $this->db->insert('quotes_has_item',array('quote_id' => $new_quote_id , 'item_id' => $item->item_id));
                }

                // ADD QUOTES_HAS_CUSTOMERS
                $this->db->insert('quotes_has_customers',array('quote_id' => $new_quote_id , 'customer_id' =>  $estimate_customer->customer_id , 'primary' => 1));

                // ADD QUOTE NOTE
                $this->db->insert( 'notes', array( 'text' => 'Quote note' , 'created' => $created ));
                $note_id = $this->db->insert_id();
                $this->db->insert( 'quotes_notes' , array( 'quote_id' => $new_quote_id , 'note_id' => $note_id ) );

            }

            redirect('quotes');

        }


    }