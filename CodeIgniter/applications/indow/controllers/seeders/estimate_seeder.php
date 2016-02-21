<?php defined('BASEPATH') OR exit('No direct script access allowed');


    class estimate_seeder extends MM_Controller
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function index_get()
        {


            for($i = 0; $i < 300; $i++){

                //--------------------------------------------------------------------
                //  STEP :1 Create an Estimate
                //--------------------------------------------------------------------

                // GET A RANDOM JOB SITE

                $sql_get_job_site = "SELECT * FROM sites ORDER BY RAND() LIMIT 1;";
                $sql_job_site_query = $this->db->query($sql_get_job_site);
                $job_site = $sql_job_site_query->row();

                // GET CUSTOMER ID FOR THE JOB SITE

                $sql_site_customer_id = "SELECT customer_id FROM site_customers WHERE site_id = ? LIMIT 1";
                $sql_site_customer_query = $this->db->query($sql_site_customer_id,$job_site->id);
                $job_site_customer = $sql_site_customer_query->row();

                // GET ITEMS ATTACHED TO THE JOB SITE

                $sql_site_items         = "SELECT items.id FROM items WHERE items.site_id = ?";
                $sql_site_items_query   = $this->db->query($sql_site_items,$job_site->id);
                $job_site_items         = $sql_site_items_query->result();

                // Create Random Date

                $time = rand(strtotime($job_site->created),time());
                $created = date("Y-m-d H:i:s" , $time );

                // CREATE HISTORY ID

                $this->db->select('id');
                $this->db->order_by("id", "desc");
                $this->db->limit(1);
                $history_record = $this->db->get('history');
                $history = $history_record->result_array();
                $new_history_id = (int) $history[0]['id'] + 1;
                $this->db->set('id',$new_history_id);
                $this->db->insert('history');

                $new_estimate =

                    array(
                        'history_id' => $new_history_id,
                        'created' => $created,
                        'created_by_id' => $job_site->created_by,
                        'closed' => '0',
                        'converted' => '0000-00-00',
                        'dealer_id' => $job_site->created_by,
                        'processed' => '0',
                        'total_square_feet' => 800,
                        'estimate_total' => 800,
                        'quote_id' => NULL,
                        'quote_created_by' => NULL,
                        'order_id' => NULL,
                        'order_created_by' => NULL,
                        'parent_group_id' => '1',
                        'customer_id' => $job_site_customer->customer_id,
                        'site_id' => $job_site->id,
                        'tech_id' => NULL,
                        'tech_assigned' => NULL,
                        'name' => NULL,
                        'parent_estimate_id' => '0',
                        'deleted' => '0',
                        'followup' => rand(0,1)
                    );

                $this->db->insert('estimates',$new_estimate);
                $new_estimate_id = $this->db->insert_id();

                // ADD ITEMS TO ESTIMATES_HAS_ITEM

                foreach($job_site_items as $item)
                {
                    $this->db->insert('estimates_has_item',array('estimate_id' => $new_estimate_id , 'item_id' => $item->id));
                }

                // ADD ESTIMATES_HAS_CUSTOMERS

                $this->db->insert('estimates_has_customers',array('estimate_id' => $new_estimate_id , 'customer_id' => $job_site_customer->customer_id , 'primary' => 1));

                // ADD ESTIMATE NOTE

                $this->db->insert( 'notes', array( 'text' => 'Estimate note' , 'created' => $created ));
                $note_id = $this->db->insert_id();
                $this->db->insert( 'estimates_notes' , array( 'estimate_id' => $new_estimate_id , 'note_id' => $note_id ) );

                }

            redirect('estimates');

        }


    }