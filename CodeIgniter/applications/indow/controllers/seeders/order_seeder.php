<?php defined('BASEPATH') OR exit('No direct script access allowed');


    class order_seeder extends MM_Controller
    {

        public function __construct()
        {
            parent::__construct();
            $this->load->helper('string');
        }

        public function index_get()
        {


            for($i = 0; $i < 300; $i++){

                //--------------------------------------------------------------------
                //  STEP :1 Create an Order
                //--------------------------------------------------------------------

                // GET A RANDOM QUOTE

                $sql_get_quote = "SELECT * FROM quotes ORDER BY RAND() LIMIT 1;";
                $sql_get_quote_query = $this->db->query($sql_get_quote);
                $quote = $sql_get_quote_query->row();

                // GET CUSTOMER ID FOR THE QUOTE

                $sql_quote_customer_id = "SELECT customer_id FROM quotes_has_customers WHERE quote_id = ? LIMIT 1";
                $sql_quote_customer_query = $this->db->query($sql_quote_customer_id, $quote->id);
                $quote_customer = $sql_quote_customer_query->row();

                // Create Random Date

                $time = rand(strtotime($quote->created),time());
                $created = date("Y-m-d H:i:s" , $time );

                // get group id

                $sql_get_created_by_group        = "SELECT groups.id,rep_id FROM groups JOIN users_groups ON users_groups.group_id = groups.id WHERE users_groups.user_id = ? LIMIT 1;";
                $sql_get_created_by_group_query  = $this->db->query($sql_get_created_by_group,$quote->created_by);
                $estimate_group                  = $sql_get_created_by_group_query->result_array();



                $order_total_records = $this->db->count_all('orders');


                $new_order =

                    array(
                        'order_number'               => 10000 + $order_total_records,
                        'order_number_type'          => 1,
                        'order_number_type_sequence' => 1,
                        'created'                    => $created,
                        'updated'                    => $created,
                        'created_by'                 => $quote->created_by,
                        'closed'                     => 0,
                        'status_code'                => 1,
                        'signed_purchase_order'      => 0,
                        'down_payment_received'      => 0,
                        'final_payment_received'     => 0,
                        'order_confirmation_sent'    => 0,
                        'credit_hold'                => 0,
                        'quote_id'                   => $quote->id,
                        'quote_created_by'           => $quote->created_by,
                        'customer_user_id'           => $quote_customer->customer_id,
                        'estimate_id'                => $quote->estimate_id,
                        'estimate_created_by'        => $quote->created_by,
                        'dealer_id'                  => $quote->created_by,
                        'dealer_indow_rep_id'        => 1,
                        'dealer_user_id'             => $quote->created_by,
                        'site_id'                    => $quote->site_id,
                        'followup'                   => rand(0,1),
                        'group_id'                   => $estimate_group[0]['id'],
                        'po_num'                     => '#' . strtoupper(random_string('alnum', 10)),
                        'subtotal'                   => $quote->quote_total,
                        'total'                      => $quote->quote_total,
                    );

                $this->db->insert('orders',$new_order);

                $new_order_id = $this->db->insert_id();


                // ADD ORDERS_STATUS_CODES RECORD
                $this->db->insert('orders_status_codes',array('order_id' => $new_order_id, 'order_status_code_id' => 1, 'user_id' => $quote->created_by));

                // UPDATE QUOTE RECORD TO REFLECT A NEW ORDER ID
                $this->db->set('order_id',$new_order_id);
                $this->db->set('order_created_by',$quote->created_by);
                $this->db->where('id', $quote->id);
                $this->db->update('quotes');

                // GET ITEMS ATTACHED TO THE JOB SITE
                $sql_quote_items            = "SELECT quotes_has_item.item_id FROM quotes_has_item WHERE quotes_has_item.quote_id = ?";
                $sql_quote_items_query      = $this->db->query($sql_quote_items,$quote->id);
                $quote_items             = $sql_quote_items_query->result();

                // ADD ITEMS TO ORDERS_HAS_ITEM
                foreach($quote_items as $item)
                {
                    $this->db->insert('orders_has_item',array('order_id' => $new_order_id , 'item_id' => $item->item_id));
                }

                // ADD ORDERS_HAS_CUSTOMERS
                $this->db->insert('orders_has_customers',array('order_id' => $new_order_id , 'customer_id' =>  $quote_customer->customer_id , 'primary' => 1));

                // ADD ORDER NOTE
                $this->db->insert( 'notes', array( 'text' => 'Order note' , 'created' => $created ));
                $note_id = $this->db->insert_id();
                $this->db->insert( 'orders_notes' , array( 'order_id' => $new_order_id , 'note_id' => $note_id ) );

                // ADD INTERNAL ORDER NOTE
                $this->db->insert( 'notes', array( 'text' => 'Internal Order note' , 'created' => $created ));
                $internal_note_id = $this->db->insert_id();
                $this->db->insert( 'order_internal_notes' , array( 'order_id' => $new_order_id , 'note_id' => $internal_note_id ) );

            }

            redirect('orders');

        }


    }