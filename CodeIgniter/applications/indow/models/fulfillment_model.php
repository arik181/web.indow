<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class fulfillment_model extends MM_Model
{
    protected  $_table      = 'fulfillment';
    protected  $soft_delete = TRUE;
    protected  $_key        = 'fulfillment.id';

    public $id;
    public $history_id;
    public $created;
    public $created_by;
    public $closed;
    public $status_code;
    public $measurement_date;
    public $commit_date;
    public $order_id;
    public $order_created_by;
    public $estimate_id;
    public $estimate_created_by_id;
    public $parent_group_id;
    public $quote_total;
    public $dealer_id;
    public $customer_id;
    public $site_id;

    public function __construct($id = null)
    {
        parent::__construct();

        if ($id === null)
            return;

        $this->load->library('form_validation');
        $this->load->helper('functions');

        $sql = '
            SELECT      id,
                        history_id,
                        created,
                        created_by,
                        closed,
                        status_code,
                        measurement_date,
                        commit_date,
                        order_id,
                        order_created_by,
                        estimate_id,
                        estimate_created_by_id,
                        parent_group_id,
                        quote_total,
                        dealer_id,
                        customer_id,
                        site_id
            FROM        quotes
            WHERE       id = ? 
            ;
        ';

        $quote = $this->db->query($sql, $id)->row("quote_model");

        foreach ($quote as $key => $value)
        {
            $this->$key = $value;
        }
    }
}
