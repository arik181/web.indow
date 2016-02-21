<?
class Payment_model extends MM_Model 
{
    public function fetch_order_payments($order_id) 
    {
        return $this->db->where('order_id', $order_id)->where('deleted', 0)->get('payments')->result();
    }

    public function delete_payments($order_id, $payment_ids) 
    {
        if (count($payment_ids)) 
        {
            $this->db->where('order_id', $order_id)->where_in('id', $payment_ids);
            $this->db->update('payments', array('deleted' => 1));
        }
    }

    public function add_payment($order_id, $payment) 
    {
        $payment['order_id'] = $order_id;
        $payment['payment_made_by_id'] = $this->ion_auth->get_user_id();
        $this->db->insert('payments', $payment);
    }

    public function add_payments($order_id, $payments) 
    {
        foreach ($payments as $payment) {
            $this->add_payment($order_id, $payment);
        }
    }
}
