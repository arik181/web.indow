<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Quote_model extends MM_Model
{
    protected  $_table      = 'quotes';
    protected  $soft_delete = TRUE;
    protected  $_key        = 'id';

    public $id;
    public $history_id;
    public $created;
    public $created_by_id;
    public $closed;
    public $status_code;
    public $measurement_date;
    public $commit_date;
    public $order_id;
    public $order_created_by;
    public $estimate_id;
    public $estimate_created_by;
    public $parent_group_id;
    public $quote_total;
    public $dealer_id;
    public $customer_id;
    public $site_id;

    public function __construct()
    {
        parent::__construct();
        $key = $this->_key;
        if ($this->$key)
            return;
        $this->load->model('History_model');
        $this->created = date("Y-m-d H:i:s");
        $this->created_by_id = $this->ion_auth->get_user_id();
        $this->closed = 0;
        $this->status_code = 100;
    }

    public function save()
    {
        $sql = "";
        if ($this->id > 0) // update operation
        {
            $sql = "UPDATE      $this->_table
                    SET         history_id = ?,
                                created = ?,
                                created_by_id = ?,
                                closed = ?,
                                status_code = ?,
                                measurement_date = ?,
                                commit_date = ?,
                                order_id = ?,
                                order_created_by = ?,
                                estimate_id = ?,
                                estimate_created_by = ?,
                                parent_group_id = ?,
                                quote_total = ?,
                                dealer_id = ?,
                                customer_id = ?,
                                site_id = ?
                    WHERE       id = ?";
        }
        else //insert operation
        {
            $history = new History_model();
            $history->save();
            $this->history_id = $history->id;
            $sql = "INSERT INTO $this->_table
                    SET         history_id = ?,
                                created = ?,
                                created_by_id = ?,
                                closed = ?,
                                status_code = ?,
                                measurement_date = ?,
                                commit_date = ?,
                                order_id = ?,
                                order_created_by = ?,
                                estimate_id = ?,
                                estimate_created_by = ?,
                                parent_group_id = ?,
                                quote_total = ?,
                                dealer_id = ?,
                                customer_id = ?,
                                site_id = ?";
        }

        $this->db->query($sql, array($this->history_id,
                                     $this->created,
                                     $this->created_by_id,
                                     $this->closed,
                                     $this->status_code,
                                     $this->measurement_date,
                                     $this->commit_date,
                                     $this->order_id,
                                     $this->order_created_by,
                                     $this->estimate_id,
                                     $this->estimate_created_by,
                                     $this->parent_group_id,
                                     $this->quote_total,
                                     $this->dealer_id,
                                     $this->customer_id,
                                     $this->site_id,
                                     $this->id));
        if ($this->id == 0)
            $this->id = $this->db->insert_id();

        return array('success' => true, 'message' => 'Updated quote successfully.');
    }

    public function fetch_quotes($limit, $start)
    {
        $sql = "SELECT *
                FROM quotes
            ";

        $start = (int) $start;
        $limit = (int) $limit;
        $query = $this->db->query($sql, array($start, $limit));

        if ( ! empty($query)  && $query->num_rows() > 0) 
        {
            foreach ($query->result() as $row) 
            {
                $data[] = $row;
            }
            return $data;
        }

        return false;
    }

    public function get_counts ($id)
    {
        $sql = "SELECT COUNT(`id`) AS `count` FROM `quotes`
                    WHERE `created_by` = ?
                    AND DATE_FORMAT(`created`, '%Y%m') = DATE_FORMAT(NOW(), '%Y%m');";

        $query = $this->db->query($sql, $id);
        $result = $query->result();
        return $result[0];
    }

    public function get_followup ($id)
    {
        $user_id = $this->ion_auth->get_user_id();
        $sql = "
            SELECT
                concat('/quotes/edit/', e.id) AS `edit`,
                'Quote' as `type`,
                CONCAT(customer.first_name,' ', customer.last_name) as customer,
                concat('<input type=checkbox id=quote/', e.id, ' onClick=\"done(this);\">') AS done
            FROM `quotes` AS `e`
            LEFT JOIN quotes_has_customers ON quotes_has_customers.quote_id=e.id AND quotes_has_customers.primary=1
            LEFT JOIN users AS customer ON customer.id = quotes_has_customers.customer_id
            WHERE e.followup = 1 AND dealer_id = ? ";

        $query = $this->db->query($sql, $id);
        return $query->result();
    }

    public function get_admin_followup ()
    {
        $sql = "
            SELECT
                concat('/quotes/edit/', e.id) AS `edit`,
                'Quote' as `type`,
                CONCAT(customer.first_name,' ', customer.last_name) as customer,
                concat('<input type=checkbox id=quote/', e.id, ' onClick=\"done(this);\">') AS done
            FROM `quotes` AS `e`
            LEFT JOIN quotes_has_customers ON quotes_has_customers.quote_id=e.id AND quotes_has_customers.primary=1
            LEFT JOIN users AS customer ON customer.id = quotes_has_customers.customer_id
            WHERE e.followup = 1";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function followup ($id)
    {
        $data = array('followup' => 0);
        $this->db->where('id', $id);
        $this->db->update($this->_table, $data); 
    }

    public function create_new($quote = array())
    {
        $user_id = $this->ion_auth->get_user_id();
        $defaults = array(
            'created'       => date('Y-m-d H:i:s'),
            'created_by'    => $user_id,
            'status_code'   => 1
        );
        $new_quote = array_merge($defaults, $quote);
        if (empty($new_quote['dealer_id'])) {
            $new_quote['dealer_id'] = $this->user_model->get_dealer_id($user_id);
        }
        
        $this->db->insert('quotes', $new_quote);
        $quote_id = $this->db->insert_id();
        return $quote_id;
    }

    public function create_from_estimate($id, $tech_id=null)
    {
        $this->load->model(array('item_model'));
        $copy_fields = array(
            'dealer_id'     => 'dealer_id',
            'site_id'       => 'site_id',
            'created_by_id' => 'estimate_created_by_id',
            'id'            => 'estimate_id'
        );
        $old = $this->db->where('id', $id)->get('estimates')->row();

        $quote = array();
        foreach ($copy_fields as $from => $to) {
            $quote[$to] = $old->$from;
        }
        $quote['tech_id'] = $tech_id;
        $quote_id = $this->create_new($quote);
        $this->item_model->copy_items('estimate', $id, 'quote', $quote_id);
        $this->customer_model->copy_customers('estimate', $id, 'quote', $quote_id);
        $fees = $this->sales_modifiers_model->get_estimate_fee_ids($id);
        $this->sales_modifiers_model->set_quote_modifiers($fees, $quote_id);
        return $quote_id;
    }

    public function create_from_site($id) {
        $old = $this->db->where('sites.id', $id)->get('sites')->row();
        $dealer_id = $this->site_model->get_dealer_id($id);
        $old->dealer_id = $dealer_id;
        if (!$dealer_id) {
            $this->session->set_flashdata('message', 'The creator of this site has no associated group.');
            redirect('/sites/edit/' . $id);
        }
        $quote = array(
            'site_id'   => $id,
            'dealer_id' => $old->dealer_id
        );
        $quote_id = $this->create_new($quote);
        $this->customer_model->copy_customers('jobsite', $id, 'quote', $quote_id);
        return $quote_id;
    }

    public function delete($id)
    {
        $this->db->trans_start();
        $sql = "UPDATE $this->_table 
                SET    deleted = 1
                WHERE  id = ?
        ";
        $query = $this->db->query($sql,$id);
        $this->db->trans_complete();

        return $query;
    }


    public function getByGroupId($group_id)
    {
        return $this->get_many_by(array( 'parent_group_id' => $group_id ));
    }


    public function getQuotesForCurrentMonth($user_id = 0)
    {

        if($user_id == 0)
        {
            $this->db->where('created >', date("Y-m-01 00:00:00"));
            return $this->db->get('quotes')->result();
        }
        else
        {

            $this->db->where('created >', date("Y-m-01 00:00:00"));
            $this->db->where('created_by', $user_id);
            return $this->db->get('quotes')->result();

        }

    }

}
