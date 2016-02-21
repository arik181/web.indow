<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function cmp($a, $b) {
    return strnatcmp($a['location'], $b['location']);
}
class Order_model extends MM_Model
{
    protected  $_table      = 'orders';
    protected  $soft_delete = TRUE;
    protected  $_key        = 'id';

    public $id;
    public $order_number;
    public $order_number_type;
    public $order_number_type_sequence;
    public $history_id;
    public $created;
    public $created_by;
    public $closed;
    public $status_code;
    public $signed_purchase_order;
    public $down_payment_received;
    public $final_payment_received;
    public $order_confirmation_sent;
    public $credit_hold;
    public $expedite_date;
    public $sales_modifier_id;
    public $shipment_id;
    public $quote_id;
    public $quote_created_by;
    public $customer_id;
    public $customer_user_id;
    public $order_id;
    public $order_created_by;
    public $dealer_id;
    public $dealer_indow_rep_id;
    public $dealer_user_id;

    public function __construct($id = null)
    {
        parent::__construct();

        if ($id === null)
            return;

        $this->load->library('form_validation');
        $this->load->helper('functions');

        $sql = '
            SELECT      id,
                        order_number,
                        order_number_type,
                        order_number_type_sequence,
                        history_id,
                        created,
                        created_by,
                        closed,
                        status_code,
                        signed_purchase_order,
                        down_payment_received,
                        final_payment_received,
                        order_confirmation_sent,
                        credit_hold,
                        expedite_date,
                        sales_modifier_id,
                        shipment_id,
                        quote_id,
                        quote_created_by,
                        customer_id,
                        customer_user_id,
                        order_id,
                        order_created_by,
                        dealer_id,
                        dealer_indow_rep_id,
                        dealer_user_id
            FROM        orders
            WHERE       id = ?
            ;
        ';

        $order = $this->db->query($sql, $id)->row("order_model");
        foreach ($order as $key => $value)
        {
            $this->$key = $value;
        }
    }

    public function fetch_orders($limit, $start)
    {
        $sql = "SELECT *
                FROM orders
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

    public function fetch_order_notes_history($id)
    {
        $sql = "SELECT          notes.*, CONCAT(users.first_name,  ' ', users.last_name) AS name
                FROM            orders
                INNER JOIN      orders_notes ON orders.id = orders_notes.order_id
                INNER JOIN      notes ON notes.id = orders_notes.note_id
                LEFT JOIN users ON users.id = notes.user_id
                WHERE orders.id = ?
                ORDER BY notes.id DESC
                ;
                ";

        return $this->db->query($sql, $id)->result();
    }

    public function fetch_internal_notes_history($id)
    {
        $sql = "SELECT          notes.*, CONCAT(users.first_name,  ' ', users.last_name) AS name
                FROM            orders
                INNER JOIN      order_internal_notes
                ON orders.id    = order_internal_notes.order_id
                INNER JOIN      notes
                ON notes.id     = order_internal_notes.note_id
                LEFT JOIN users ON users.id = notes.user_id
                WHERE orders.id = ?
                ORDER BY notes.id DESC
                ;
                ";

        return $this->db->query($sql, $id)->result();
    }

    public function fetch_instructions($order_id)
    {
        $sql = "
            SELECT special_instructions
            FROM orders
            WHERE orders.id = ?
            ;
        ";

        $query = $this->db->query($sql, $order_id);
        $row = $query->row();

        if ($row)
            $result = $row->special_instructions;
        else
            $result = '';

        return $result;
    }

    public function add_order_note($order_id, $note_text)
    {
        if (!empty($note_text))
        {
            $note = array(
                'text' => $note_text,
                'created' => date('Y-m-d H:i:s'),
                'user_id' => $this->ion_auth->get_user_id()
            );
            $this->db->insert('notes', $note);
            $note_id = $this->db->insert_id();
            $this->db->insert('orders_notes', array('order_id' => $order_id, 'note_id' => $note_id));
        }
    }

    public function add_internal_note($order_id, $note_text)
    {
        if (!empty($note_text))
        {
            $note = array(
                'text' => $note_text,
                'created' => date('Y-m-d H:i:s'),
                'user_id' => $this->ion_auth->get_user_id()
            );
            $this->db->insert('notes', $note);
            $note_id = $this->db->insert_id();
            $this->db->insert('order_internal_notes', array('order_id' => $order_id, 'note_id' => $note_id));
        }
    }

    public function get_bundle($order_id) {
        if (!$order_id) {
            return array();
        }
        $bundle_rows = $this->db->where('order_id', $order_id)->get('order_bundles')->result();
        $bundle_ids = array();
        foreach ($bundle_rows as $row) {
            $bundle_ids[] = $row->bundle_target;
        }
        return $bundle_ids;        
    }

    public function set_bundle($order_id, $bundle_ids) {
        $this->db->where('order_id', $order_id)->delete('order_bundles');
        foreach ($bundle_ids as $id) {
            $this->db->insert('order_bundles', array(
                'order_id' => $order_id,
                'bundle_target' => $id
            ));
        }
    }

    public function save_review_info($order_id, $review_info) {
        $order_data = array(
            'po_num'                => $review_info['po_num'],
            'expedite_date'           => $review_info['commit_date'] ? $review_info['commit_date'] : null
        );
        if (!empty($review_info['shipping_address'])) {
            if (substr($review_info['shipping_address'], 0, 1) === 'd') {
                $order_data['dealer_shipping_address_id'] = substr($review_info['shipping_address'], 1, 10);
                $order_data['shipping_address_id'] = null;
            } else {
                $order_data['dealer_shipping_address_id'] = null;
                $order_data['shipping_address_id'] = $review_info['shipping_address'];
            }
        }
        $this->db->where('id', $order_id)->update('orders', $order_data);
        $this->set_bundle($order_id, $review_info['bundle']);
        $this->add_order_note($order_id, $review_info['notes']);
    }

    public function get_chart_data()
    {
        // TODO Proof of concept. Implement for actual codes.
        $sql = '
            SELECT      COUNT(IF(status_code>100 AND status_code<200,1,null)) as on_hold, 
                        COUNT(IF(status_code>200 AND status_code<300,1,null)) as ready_to_ship,
                        COUNT(IF(status_code>300 AND status_code<400,1,null)) as ready_to_ship,
            FROM        orders
            ;
        ';

        $result = $$this->db->query($sql)->row();

        return $result;
    }

    public function get_counts ($id)
    {
        $sql = "SELECT COUNT(`id`) AS `count` FROM `orders`
                    WHERE `created_by` = ?
                    AND DATE_FORMAT(`created`, '%Y%m') = DATE_FORMAT(NOW(), '%Y%m');";

        $query = $this->db->query($sql, $id);
        $result = $query->result();
        return $result[0];
    }

    public function get_followup ($id)
    {
       $sql = "SELECT concat('/orders/edit/', e.id) AS `edit`
                , 'Order' as `type`
                ,  CONCAT(users.first_name, ' ', users.last_name) AS `customer`
                , concat('<input type=checkbox id=order/', e.id, ' onClick=\"done(this);\">') AS `done`
            FROM `orders` AS `e`
            LEFT JOIN orders_has_customers ON (orders_has_customers.order_id=e.id AND orders_has_customers.primary=1)
            LEFT JOIN users ON users.id=orders_has_customers.customer_id
            WHERE `dealer_id` = ?
            AND `followup` = 1";
        $query = $this->db->query($sql, $id);
        return $query->result();
    }

    public function get_admin_followup ()
    {
        $sql = "SELECT concat('/orders/edit/', e.id) AS `edit`
                , 'Order' as `type`
                ,  CONCAT(users.first_name,' ', users.last_name) AS `customer`
                , concat('<input type=checkbox id=order/', e.id, ' onClick=\"done(this);\">') AS `done`
            FROM `orders` AS `e`
            LEFT JOIN orders_has_customers ON (orders_has_customers.order_id=e.id AND orders_has_customers.primary=1)
            LEFT JOIN users ON users.id=orders_has_customers.customer_id
            WHERE `followup` = 1";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function followup ($id, $followup = 0)
    {
        $data = array('followup' => $followup);
        $this->db->where('id', $id);
        $this->db->update($this->_table, $data); 
    }

    public function graph_data ($id)
    {
        $sql = "select count(orders.id) `data`,  status_codes.description `label` from orders, status_codes
                where orders.status_code = status_codes.id
                and orders.created_by = ?
                group by description";

        $query = $this->db->query($sql, $id);
        return $query->result();
    }

    public function get_status($order_id, $use_id = false) {
        $row = $this->db->select('status_codes.id, status_codes.code')->from('orders')->join('status_codes', 'orders.status_code=status_codes.id')->where('orders.id', $order_id)->get()->row();
        if (!$row) {
            return null;
        }
        if ($use_id) {
            return $row->id;
        } else {
            return $row->code;
        }
    }

    protected function natksort(&$array) {
        $keys = array_keys($array);
        natcasesort($keys);

        foreach ($keys as $k) {
            $new_array[$k] = $array[$k];
        }

        $array = $new_array;
        return true;
    }
    public function set_item_numbers2(&$items) {
        $rooms = array();
        foreach ($items as &$item) {
            $rooms[$item['room']][] = &$item;
        }
        $this->natksort($rooms);
        foreach ($rooms as $k => $room) {
            usort($rooms[$k], 'cmp');
        }
        $num = 1;
        foreach ($rooms as &$room) {
            foreach ($room as &$i) {
                if ($i['manufacturing_status'] == 1) {
                    $i['unit_num'] = $num;
                    $num++;
                }
            }
        }
    }
    public function set_item_numbers($order_id) {
        $this->db->query("SET @rownum=0");
        $this->db->query("
        UPDATE
        items JOIN
        (
            SELECT @rownum:=@rownum+1 auto_num, items.id AS item_id
            FROM items
            JOIN orders_has_item ON orders_has_item.item_id = items.id
            WHERE orders_has_item.order_id= ? AND orders_has_item.deleted=0
        ) AS item_num ON item_num.item_id=items.id
        SET items.unit_num = item_num.auto_num
", $order_id);
    }

    public function update_status($order_id, $status, $oldstatus=null, $use_code=false) {
        if ($use_code) {
            $newstatuscode = $status;
            $status = $this->db->where('code', $status)->get('status_codes')->row()->id;
        }
        if ($oldstatus === null) {
            $oldstatus = $this->db->where('id', $order_id)->get('orders')->row()->status_code;
        }
        if ($oldstatus != $status) {
            $status_change = array(
                'status_changed' => date('Y-m-d H:i:s'),
                'order_id' => $order_id,
                'order_status_code_id' => $status,
                'user_id' => $this->ion_auth->get_user_id()
            );
            $this->db->insert('orders_status_codes', $status_change);
        }
        $this->db->where('id', $order_id)->update('orders', array('status_code' => $status));

        if ($oldstatus != $status) {
            if ($oldstatus) {
                $oldstatuscode = $this->db->where('id', $oldstatus)->get('status_codes')->row()->code;
            } else {
                $oldstatuscode = null;
            }
            if (!isset($newstatuscode)) {
                $newstatuscode = $this->db->where('id', $status)->get('status_codes')->row()->code;
            }
            if ($oldstatuscode < 650 && $newstatuscode >= 650) {
                $site_id = $this->db->where('id', $order_id)->get('orders')->row()->site_id;
                if ($site_id) {
                    $min_status = $this->db->query("SELECT MIN( code ) AS min_status
                            FROM orders
                            JOIN status_codes ON status_codes.id = status_code
                            WHERE site_id = (SELECT site_id FROM orders WHERE id = ?)", $order_id)->row()->min_status;
                    if ($min_status >= 650) {
                        $this->db->where('site_id', $site_id)->delete('sites_techs');
                    }
                }
            }
        }
        return $status;
    }

    public function update_data($data, $order_id) {
        if (!empty($data['status_code'])) {
            $this->update_status($order_id, $data['status_code']);
        }
        $this->db->where('id', $order_id);
        $this->db->update('orders', $data);
    }

    public function get_status_history($order_id) {
        $this->db->select("code, orders_status_codes.*, CONCAT(users.first_name, ' ', users.last_name) as name, description", false);
        $this->db->from('orders_status_codes');
        $this->db->join('users', 'users.id=user_id');
        $this->db->join('status_codes', 'order_status_code_id=status_codes.id');
        $this->db->where('order_id', $order_id);
        return $this->db->get()->result();
    }

    public function create_new($order = array(), $freebird=false) {
        $user_id = $this->ion_auth->get_user_id();
        $defaults = array(
            'created'       => date('Y-m-d H:i:s'),
            'created_by' => $user_id,
            'status_code' => $this->db->where('code', 300)->get('status_codes')->row()->id
        );
        $new_order = array_merge($defaults, $order);

        if (empty($new_order['dealer_id']) && !$freebird) {
            $this->load->model('user_model');
            $new_order['dealer_id'] = $this->user_model->get_dealer_id($user_id);
        }
        
        $this->db->insert('orders', $new_order);
        $order_id = $this->db->insert_id();
        $this->update_status($order_id, $new_order['status_code'], 0);
        $order_num = strtoupper(dechex($order_id));
        $this->db->where('id', $order_id)->update('orders', array(
            'order_number'                  => $order_id,
            'order_number_type'             => $order_num,
            'order_number_type_sequence'    => $order_num,
        ));
        return $order_id;
    }

    public function get_payments_total($order_id) {
        return $this->db->select('SUM(payment_amount) as total', false)
                    ->where('deleted', 0)->where('order_id', $order_id)
                    ->get('payments')->row()->total;
    }

    public function update_order_totals($order_id) {
        $totals = $this->get_totals($order_id);
        $order = array(
            'subtotal' => $totals['subtotal'],
            //'fees' => $totals['fees'],
            //'discounts' => $totals['discounts'],
            'total' => $totals['total'],
        );
        $this->db->where('id', $order_id)->update('orders', $order);
    }

    public function get_status_date($order_id, $status) {
        $row = $this->db->select('status_changed')
            ->from('orders_status_codes')
            ->join('status_codes', 'status_codes.id=order_status_code_id')
            ->where('orders_status_codes.order_id', $order_id)
            ->where('code', $status)
            ->get()->row();
        if ($row) {
            return $row->status_changed;
        } else {
            return false;
        }
    }

    public function get_totals($order_id, $items=null) {
        if ($items === null) {
            $items = $this->ItemFactory->getOrderConfList($order_id);
        }
        $subtotal = 0;
        $special_geom = 0;
        $special_geom_count = 0;
        foreach ($items as $item) {
            $price = $item->price_override !== null ? $item->price_override : $item->price;
            if ($item->special_geom && $price) {
                $special_geom_count++;
                $special_geom += max($price * .2, 50);
            }
            $subtotal += $price;
            if (count($item->subproducts)) {
                foreach ($item->subproducts as $p) {
                    $subtotal += $p->price_override !== null ? $p->price_override : $p->price;
                }
            }
        }
        $order = $this->db->where('id', $order_id)->get('orders')->row();

        $modifiers = $this->sales_modifiers_model->get_order_fees($order_id, 'all');
        $mods = array();
        foreach ($modifiers as $mod) {
            $mods[] = array('name' => $mod->description, 'amount' => $this->sales_modifiers_model->get_modifier_amount($mod, $subtotal));
        }
        //$mod_totals = $this->sales_modifiers_model->get_modifier_totals($modifiers);
        $totals = array();
        $totals['wholesale_discount'] = 0;
        $wholesale_discount = $this->group_model->get_wholesale_discount($this->db->where('id', $order_id)->get('orders')->row()->dealer_id);
        if ($wholesale_discount) {
            if ($wholesale_discount['type'] === 'percent') {
                $totals['wholesale_discount'] = $subtotal * $wholesale_discount['amount'] / -100;
            } else if ($wholesale_discount['type'] === 'dollar') {
                $totals['wholesale_discount'] = $wholesale_discount['amount'] * -1;
            }
        }
        $modifiers_total = 0;
        foreach ($mods as $mod) {
            $modifiers_total += $mod['amount'];
        }
        $totals['subtotal'] = $subtotal;
        //$totals['fees'] = $subtotal * $mod_totals['fees_percent'] / 100 + $mod_totals['fees_dollars'];
        //$totals['discounts'] = ($subtotal + $totals['fees']) * $mod_totals['discounts_percent'] / 100 + $mod_totals['discounts_dollars'];
        //$totals['taxes'] = ($subtotal + $totals['fees'] + $special_geom - $totals['discounts']) * $mod_totals['taxes_percent'] / 100 + $mod_totals['taxes_dollars'];
        //$totals['total'] = $subtotal + $special_geom + $totals['wholesale_discount'] + $totals['fees'] - $totals['discounts'] + $totals['taxes'];
        $totals['mods'] = $mods;
        $totals['total'] = $subtotal + $special_geom + $totals['wholesale_discount'] + $modifiers_total;
        $totals['special_geom'] = $special_geom;
        $totals['special_geom_count'] = $special_geom_count;

        $totals['payments'] = $this->get_payments_total($order_id);
        $totals['due'] = $totals['total'] - $totals['payments'];
        return $totals;
    }

    public function get_items($order_id) {
        return $this->db
                ->select('items.*, product_id')
                ->from('items')
                ->join('orders_has_item', 'orders_has_item.item_id=items.id')
                ->join('product_types', 'product_types.id=product_types_id')
                ->where('orders_has_item.order_id', $order_id)
                ->get()->result();
    }

    public function create_from($type, $id) {
        $this->load->model(array('item_model', 'site_model', 'sales_modifiers_model'));
        $copy_fields = array(
            'estimate'  => array(
                'dealer_id'                 => 'dealer_id',
                'site_id'                   => 'site_id',
                'created_by_id'             => 'estimate_created_by',
                'id'                        => 'estimate_id',
            ),
            'quote'     => array(
                'dealer_id'                 => 'dealer_id',
                'site_id'                   => 'site_id',
                'created_by'                => 'quote_created_by',
                'estimate_created_by_id'    => 'estimate_created_by',
                'estimate_id'               => 'estimate_id',
                'id'                        => 'quote_id'
            ),
            'jobsite'   => array(
                'id'                        => 'site_id',
                'dealer_id'                 => 'dealer_id'
            )
        );
        if ($type === 'quote') {
            $old = $this->db->where('id', $id)->get('quotes')->row();
            $fees = array(); //$this->sales_modifiers_model->get_quote_fee_ids($id, true);
        } elseif ($type === 'estimate') {
            $old = $this->db->where('id', $id)->get('estimates')->row();
            $fees = $this->sales_modifiers_model->get_estimate_fee_ids($id, true);
        } elseif ($type === 'jobsite') {
            $fees = array();
            $old = $this->db->where('sites.id', $id)->get('sites')->row();
            $dealer_id = $this->site_model->get_dealer_id($id);
            $old->dealer_id = $dealer_id;
            if (!$dealer_id) {
                $this->session->set_flashdata('message', 'A customer is required to create an order.');
                redirect('/sites/edit/' . $id);
            }
        }

        $fields = $copy_fields[$type];
        $order = array();
        foreach ($fields as $from => $to) {
            $order[$to] = $old->$from;
        }
        $order_id = $this->create_new($order);
        if ($type !== 'jobsite') {
            $this->item_model->copy_items($type, $id, 'order', $order_id);
        }
        $this->customer_model->copy_customers($type, $id, 'order', $order_id);
        $this->sales_modifiers_model->set_order_modifiers($fees, $order_id);
        $this->set_item_numbers($order_id);
        $items = $this->get_items($order_id);
        $this->item_model->calc_item_thickness($items);
        return $order_id;
    }

    public function get_primary_customer($order_id, $include_addr=false) {
        $this->db->select('customers.*, users.*')->from('orders_has_customers');
        $this->db->join('users', 'users.id=orders_has_customers.customer_id');
        $this->db->join('customers', 'customers.user_id=orders_has_customers.customer_id');
        $this->db->where('primary', 1)->where('order_id', $order_id);
        $user = $this->db->get()->row();
        if (!$user || !$include_addr) {
            return $user;
        }
        $user->addr = $this->db->where('user_id', $user->user_id)->get('user_addresses')->row();
        return $user;
    }

    public function get_primary_customer_id($order_id, $include_addr=false) {
        $this->db->select('users.id')->from('orders_has_customers');
        $this->db->join('users', 'users.id=orders_has_customers.customer_id');
        $this->db->join('customers', 'customers.user_id=orders_has_customers.customer_id');
        $this->db->where('primary', 1)->where('order_id', $order_id);
        $user = $this->db->get()->row();

        if (isset($user->id))
            return $user->id;
        else
            return 0;
    }

    public function get_address($address_id) {
        return $this->db->where('id', $address_id)->get('user_addresses')->row();
    }

    public function delete_order($order_id) {
        $this->db->where('id', $order_id)->update('orders', array('deleted' => 1));
    }

    public function getOrdersForCurrentMonth($user_id = 0)
    {


        if($user_id == 0)
        {

            $this->db->where('created >', date("Y-m-01 00:00:00"));
            return $this->db->get('orders')->result();

        }
        else
        {
            $this->db->where('created_by', $user_id);
            $this->db->where('created >', date("Y-m-01 00:00:00"));
            return $this->db->get('orders')->result();

        }

    }

    public function get_shipping_address($order, $array=false) {
        if (is_numeric($order)) {
            $order = $this->get($order);
        }
        if (!empty($order->dealer_shipping_address_id)) {
            $ship = $this->db->where('id', $order->dealer_shipping_address_id)->get('group_addresses');
            if ($array) {
                $ship = $ship->row_array();
                if (!$ship) {
                    return false;
                }
                $ship['id'] = 'd' . $ship['id'];
            } else {
                $ship = $ship->row();
                if (!$ship) {
                    return false;
                }
                $ship->id = 'd' . $ship->id;
            }
        } elseif (!empty($order->shipping_address_id)) {
            $ship = $this->db->where('id', $order->shipping_address_id)->get('user_addresses');
            $ship = $array ? $ship->row_array() : $ship->row();
        } else {
            $ship = false;
        }
        return $ship;
    }

	public function generate_csv_header(&$output, &$order_array) {
		fputcsv($output, array_keys($order_array));
	}
	
	public function generate_csv_order_data(&$order_array, $order) {
		$order_array['order_num'] = $order->order_num;
		$order_array['order_date'] = $order->order_date;
		$order_array['customer_first_name'] = $order->customer_first_name;
		$order_array['customer_last_name'] = $order->customer_last_name;
		$order_array['job_site_address'] = $order->site_address;
		$order_array['job_site_address_ext'] = $order->site_address_ext;
		$order_array['customer_phone_1'] = $order->customer_phone_1;
		$order_array['customer_phone_2'] = $order->customer_phone_2;
		$order_array['customer_email_1'] = $order->customer_email_1;
		$order_array['customer_email_2'] = $order->customer_email_2;
		$order_array['job_site_ctiy'] = $order->site_city;
		$order_array['job_site_state'] = $order->site_state;
		$order_array['job_site_zipcode'] = $order->site_zipcode;
		$order_array['group_name'] = $order->group_name;
		$order_array['group_address'] = $order->group_address;
		$order_array['group_address_ext'] = $order->group_address_ext;
		$order_array['group_city'] = $order->group_city;
		$order_array['group_state'] = $order->group_state;
		$order_array['group_zipcode'] = $order->group_zipcode;
		$order_array['creator_first_name'] = $order->creator_first_name;
		$order_array['creator_last_name'] = $order->creator_last_name;
		$order_array['creator_email'] = $order->creator_email;
		$order_array['creator_phone'] = $order->creator_phone;
		$order_array['dealer_po_num'] = $order->po_num;
	}
	
	public function generate_csv_item_row(&$output, &$order_array, $item) {
		$order_array['product_serial_num'] = $item->id;
		$order_array['room_name'] = $item->room;
		$order_array['location'] = $item->location;
		$order_array['width_top'] = $item->measurements['A'];
		$order_array['width_bottom'] = $item->measurements['B'];
		$order_array['height_left'] = $item->measurements['C'];
		$order_array['height_right'] = $item->measurements['D'];
		$order_array['diagonal_left'] = $item->measurements['E'];
		$order_array['diagonal_right'] = $item->measurements['F'];
		$order_array['product_type'] = $item->product_type;
		$order_array['panel_thickness'] = $item->acrylic_panel_thickness;
		$order_array['drafty_window'] = $item->drafty ? '1' : '0';
		$order_array['tubing'] = $item->tubing;
		$order_array['frame_depth'] = $item->frame_depth;
		$order_array['product'] = $item->product;
		$order_array['window_notes'] = $item->notes;
        $width = max($item->measurements['A'], $item->measurements['B']);
        $height = max($item->measurements['C'], $item->measurements['D']);
        $sqft_panel = $width * $height / 144;
		$order_array['sqft_panel'] = round($sqft_panel, 2);
        $order_array['linear_ft_panel'] = round(($item->measurements['A'] + $item->measurements['B'] + $item->measurements['C'] + $item->measurements['D']) / 12, 2);
		$order_array['top_spine'] = $item->top_spine ? '1' : '0';
		$order_array['side_spines'] = $item->side_spines ? '1' : '0';
		$order_array['item_num'] = $item->unit_num;
		$order_array['unit_msrp'] = $item->price;
		fputcsv($output, $order_array);
	}
	
	public function generate_csv_subitem_row(&$output, &$order_array, $subitem) {
		$order_array['product_type'] = $subitem->product_type;
		$order_array['product'] = $subitem->product;
		$order_array['unit_msrp'] = $subitem->unit_price;
		fputcsv($output, $order_array);
	}
	
	public function generate_csv($order_ids) {
		$orders = $this->db
			->select("
				orders.id,
				orders.po_num,
				creator.first_name as creator_first_name,
				creator.last_name as creator_last_name,
				creator.email_1 as creator_email,
				creator.phone_1 as creator_phone,
				sites.address as site_address,
				sites.address_ext as site_address_ext,
				sites.city as site_city,
				sites.state as site_state,
				sites.zipcode as site_zipcode,
				users.first_name as customer_first_name,
				users.last_name as customer_last_name,
				users.phone_1 as customer_phone_1,
				users.phone_2 as customer_phone_2,
				users.email_1 as customer_email_1,
				users.email_2 as customer_email_2,
				groups.name as group_name,
				group_addresses.address as group_address,
				group_addresses.address_ext as group_address_ext,
				group_addresses.city as group_city,
				group_addresses.state as group_state,
				group_addresses.zipcode as group_zipcode,
				CONCAT(order_number, '-', order_number_type_sequence) as order_num,
				(SELECT status_changed FROM orders_status_codes JOIN status_codes ON status_codes.id=order_status_code_id WHERE status_codes.code=350 AND order_id=orders.id ORDER BY orders_status_codes.id DESC LIMIT 1) as order_date
				
			", false)
			->from('orders')
			->join('orders_has_customers', 'orders_has_customers.order_id=orders.id AND orders_has_customers.primary=1')
			->join('users', 'orders_has_customers.customer_id=users.id')
			->join('users creator', 'orders.created_by=creator.id')
			->join('sites', 'orders.site_id=sites.id')
			->join('groups', 'orders.dealer_id=groups.id', 'left')
			->join('group_addresses', 'group_addresses.group_id=groups.id AND group_addresses.addressnum=1')
			->where_in('orders.id', $order_ids)
			->get()->result();
			
		
		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=order_" . @$order_ids[0] . ".csv");
		// Disable caching
		header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
		header("Pragma: no-cache"); // HTTP 1.0
		header("Expires: 0"); // Proxies
		
		
		$output = fopen("php://output", "w");
		
		// Default Order and Values
		$order_array = array(
			'product_serial_num' => '?',
			'order_num' => '?',
			'item_num' => '?',
			'room_name' => '?',
			'location' => '?',
			'window_notes' => '?',
			'product' => '?',
			'product_type' => '?',
			'tubing' => '?',
			'width_top' => '?',
			'width_bottom' => '?',
			'height_left' => '?',
			'height_right' => '?',
			'diagonal_left' => '?',
			'diagonal_right' => '?',
			'panel_thickness' => '?',
			'order_date' => '?',
			'sqft_panel' => '?',
			'linear_ft_panel' => '?',
			'top_spine' => '?',
			'side_spines' => '?',
			'frame_depth' => '?',
			'drafty_window' => '?',
			'customer_first_name' => '?',
			'customer_last_name' => '?',
			'job_site_address' => '?',
			'job_site_address_ext' => '?',
			'customer_phone_1' => '?',
			'customer_phone_2' => '?',
			'customer_email_1' => '?',
			'customer_email_2' => '?',
			'job_site_ctiy' => '?',
			'job_site_state' => '?',
			'job_site_zipcode' => '?',
			'group_name' => '?',
			'group_address' => '?',
			'group_address_ext' => '?',
			'group_city' => '?',
			'group_state' => '?',
			'group_zipcode' => '?',
			'creator_first_name' => '?',
			'creator_last_name' => '?',
			'creator_email' => '?',
			'creator_phone' => '?',
			'creator_phone' => '?',
			'dealer_po_num' => '?',
			'dim_w' => '?',
			'dim_h' => '?',
			'unit_msrp' => '?');
		
		// Header creation	
		$this->generate_csv_header($output, $order_array);
		
		foreach($orders as $order) {
			$this->generate_csv_order_data($order_array, $order);
			$items = $this->db->from('items')
				->select("
					items.*,
					product_types.product_type as product_type,
					products.product as product,
					edging.name as tubing,
					frame_depth.name as frame_depth
					")
				->join('product_types', 'items.product_types_id=product_types.id')
				->join('products', 'product_types.product_id=products.id')
				->join('edging', 'items.edging_id=edging.id')
				->join('frame_depth', 'items.frame_depth_id=frame_depth.id')
				->join('orders_has_item', 'orders_has_item.item_id=items.id AND orders_has_item.deleted=0')
				->where('orders_has_item.order_id', $order->id)
				->get()->result();

			$item_ids = array();
			foreach ($items as $item) {
				$item_ids[] = $item->id;
			}

			// Get data we need for subitems
			$subitems = $this->db
				->select("*")
				->join('product_types', 'subitems.product_type_id=product_types.id')
				->join('products', 'product_types.product_id=products.id')
				->where_in('item_id', $item_ids)->where('deleted', 0)->get('subitems')->result();
			$subitems_sorted = array();
			
			foreach ($subitems as $sub) {
				$subitems_sorted[$sub->item_id][] = $sub;
			}
		
			// Getting measurements
			$measurements = $this->db->from('measurements')
				->join('items_measurements', 'items_measurements.measurement_id=measurements.id')
				->where_in('item_id', $item_ids)
				->get()->result();
			$measurements_sorted = array();
			
			foreach ($measurements as $m) {
				$measurement_sorted[$m->item_id][] = $m;
			}
			
			foreach ($items as $item) {
				$item->measurements = array(
					'A' => '',
					'B' => '',
					'C' => '',
					'D' => '',
					'E' => '',
					'F' => ''
				);
				if (!empty($measurement_sorted[$item->id])) {
					foreach ($measurement_sorted[$item->id] as $measurement) {
						$item->measurements[$measurement->measurement_key] = $measurement->measurement_value;
					}
				} else {
				}
			}
			
			// Go through all items and subitems
			foreach ($items as $item) {
				$this->generate_csv_item_row($output, $order_array, $item);
				if (!empty($subitems_sorted[$item->id])) {
					foreach ($subitems_sorted[$item->id] as $subitem) {
						for($i=0; $i<$subitem->quantity; $i++) {
							$this->generate_csv_subitem_row($output, $order_array, $subitem);
						}
					}
				}
			}
		}
		fclose($output);
	}

    public function queue_cut_scripts($order_id) {
        $this->db->where('order_id', $order_id)->update('cut_calculations', array('deleted' => 1));
        $items = $this->db->where('order_id', $order_id)->where('deleted', 0)->get('orders_has_item')->result();

        $insert_items = array();
        foreach ($items as $item) {
            $insert_items[] = array(
                'item_id' => $item->item_id,
                'order_id' => $order_id
            );
        }
        if (count($insert_items)) {
            $this->db->insert_batch('cut_calculations', $insert_items);
        }
    }

    public function get_total_sqft($order_id) {
        $items = $this->db->select('item_id as id')->where('orders_has_item.order_id', $order_id)->where('deleted', 0)->get('orders_has_item')->result();
        $this->item_model->attach_measurements($items);
        $total = 0;
        foreach ($items as $item) {
            $a = !empty($item->measurements['A']) ? $item->measurements['A'] : 0;
            $b = !empty($item->measurements['B']) ? $item->measurements['B'] : 0;
            $c = !empty($item->measurements['C']) ? $item->measurements['C'] : 0;
            $d = !empty($item->measurements['D']) ? $item->measurements['D'] : 0;
            $total += max($a, $b) * max($c, $d);
        }
        return round($total / 144, 2);
    }

    public function get_orders_with_items($order_ids) {
        $orders = $this->db
            ->where_in('orders.id', $order_ids)
            ->where('orders.deleted', 0)
            ->get('orders')->result();
        $orders_assoc = $orders;
        foreach ($orders as $order) {
            $order->items = array();
            $orders_assoc[$order->id] = $order;
        }
        $items = $this->db
                ->select('items.*, orders.id as r_order_id')
                ->join('items', 'items.id=orders_has_item.item_id')
                ->where_in('orders_has_item.order_id', $order_ids)
                ->where('orders_has_item.deleted', 0)
                ->get('orders_has_item')->result();
        foreach ($items as $item) {
            $orders_assoc[$item->r_order_id]->items[] = $item;
        }

        return $orders;
    }

    public function get_shipping_fee($order_id) {
        $fees = $this->db
                ->where('order_id', $order_id)
                ->where('modifier_type', 'fee')
                ->like('description', 'shipping')
                ->join('sales_modifiers', 'sales_modifiers.id=sales_modifier_id AND sales_modifiers.deleted=0')
                ->join('orders', 'orders.id=orders_has_fees.order_id')
                ->get('orders_has_fees')->result();
        if (!count($fees)) {
            return 0;
        }
        $subtotal = $fees[0]->subtotal;
        $fees_total = 0;
        foreach ($fees as $fee) {
            if ($fee->modifier === 'dollar') {
                $fees_total += $fee->amount * $fee->quantity;
            } elseif ($fee->modifier === 'percent') {
                $fees_total += ($subtotal * $fee->amount / 100) * $fee->quantity;
            }
        }

        return money_format($fees_total, 2);
    }

    public function update_combined_order($batch_id, $values) {
        $orders = $this->db->where('batch_id', $batch_id)->get('orders_combined')->result();
        $order_ids = array();
        foreach ($orders as $order) {
            $order_ids[] = $order->order_id;
            if (!empty($values['status'])) {
                $this->update_status($order->order_id, $values['status'], $oldstatus=null, $use_code=true);
            } elseif (!empty($values['status_code'])) {
                $this->update_status($order->order_id, $values['status_code']);
            }
        }
        unset($values['status_code']);
        unset($values['status']);
        if (count($values)) {
            $this->db->where_in('id', $order_ids)->update('orders', $values);
        }
    }

    public function get_combined_ids($batch_id) {
        $order_ids = array();
        $orders = $this->db->where('batch_id', $batch_id)->get('orders_combined')->result();
        foreach ($orders as $order) {
            $order_ids[] = $order->order_id;
        }
        return $order_ids;
    }

    public function update_order($id, $values) {
        if (!empty($values['status'])) {
            $this->update_status($id, $values['status'], $oldstatus=null, $use_code=true);
            unset($values['status']);
        } elseif (!empty($values['status_code'])) {
            $this->update_status($id, $values['status_code']);
            unset($values['status_code']);
        }
        
        if (count($values)) {
            $this->db->where('id', $id)->update('orders', $values);
        }
    }

    public function get_combined_order($order_id) {
        $combined = $this->db->where('order_id', $order_id)->get('orders_combined')->row();
        return $combined ? $combined->batch_id : false;
    }

    public function freebird_redirect() {
        $user = $this->data['user'];
        $order = $this->db
                ->select('orders.id')
                ->where('customer_id', $user->id)
                ->where('primary', 1)
                ->where('orders.deleted', 0)
                ->where('code', '200')
                ->join('orders', 'order_id=orders.id')
                ->join('status_codes', 'status_code=status_codes.id')
                ->order_by('orders.id DESC')
                ->get('orders_has_customers')->row();
        if ($order) {
            redirect('/orders/measure/' . $order->id);
        } else {
            $data = array(
                'title'   => 'Measure Order',
                'content' => 'themes/fullwidth/message_screen',
                'message' => 'No orders are available for measurement at this time.'
            );
            $this->load->view('themes/fullwidth/main', $data);
            return;
        }
    }

    public function attach_sqft($orders, $join_table = 'orders_has_item', $id_col='order_id') {
        $this->load->factory('itemfactory');
        if (!count($orders)) {
            return;
        }
        $orders_assoc = array();
        $order_ids = array();
        foreach ($orders as $order) {
            $order->sqft = 0;
            $order->windows = 0;
            $order->items = array();
            $orders_assoc[$order->id] = $order;
            $order_ids[] = $order->id;
            $order->isubtotal = 0;
        }
        $items = $this->db
                ->select("items.id, $join_table.$id_col, product_id, unit_price_type, min_price, unit_price, width, height")
                ->where("$join_table.deleted", 0)
                ->where_in("$join_table.$id_col", $order_ids)
                ->join('items', "items.id=$join_table.item_id")
                ->join('product_types', 'product_types_id=product_types.id')
                ->get($join_table)->result();
        foreach ($items as $item) {
            $orders_assoc[$item->$id_col]->items[] = $item;
        }
        $this->item_model->attach_measurements($items);
        $this->itemfactory->attach_subproducts($items, true);
        foreach ($items as $item) {
            if ($item->product_id != 3) {
                if ($join_table === 'estimates_has_item') {
                    $item->sqft = $item->width * $item->height / 144;
                } else {
                    $item->sqft = max(@$item->measurements['A'], @$item->measurements['B']) * max(@$item->measurements['C'], @$item->measurements['D']) / 144;
                }
            } else {
                $item->sqft = 0;
            }
        }

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                if ($item->product_id != 3) {
                    $order->sqft += $item->sqft;
                    $order->windows += 1;
                }
                if ($item->unit_price_type == 'unit') {
                    $item->price = $item->unit_price;
                } else {
                    if ($item->price = $item->unit_price * $item->sqft);
                }
                $item->price = ceil(max($item->price, $item->min_price));  
                foreach ($item->subproducts as $p) {
                    $item->price += $p->price;
                }
                $order->isubtotal += $item->price;
            }
            $order->sqft = round($order->sqft, 2);
            $order->isubtotal = round($order->isubtotal, 2);
            $order->windows += 1;
        }
    }

	
    public function change_owner($order_id, $owner_id) {
        $order = $this->db->where('id', $order_id)->get('orders')->row();
        if (!$order) {
            return;
        }
        $owner = $this->db->where('user_id', $owner_id)->get('users_groups')->row();
        $group = $owner ? $owner->group_id : null;
        $this->db->where('id', $order_id)->update('orders', array('created_by' => $owner_id, 'dealer_id' => $group));

        if ($order->site_id) {
            $site_id = $order->site_id;
            $this->db->where('id', $site_id)->update('sites', array('created_by' => $owner_id));
            $this->db->where('site_id', $site_id)->update('estimates', array('created_by_id' => $owner_id, 'dealer_id' => $group));
            $this->db->where('site_id', $site_id)->update('quotes', array('created_by' => $owner_id, 'dealer_id' => $group));
            $this->db->query("
                UPDATE orders JOIN status_codes ON status_codes.id=orders.status_code SET created_by=$owner_id, dealer_id=$group WHERE code <> 700 AND site_id=$site_id;
            ");

            $customers = $this->db->where('order_id', $order_id)->get('orders_has_customers')->result();
            $customer_ids = array();
            foreach ($customers as $customer) {
                $customer_ids[] = $customer->customer_id;
            }
            $this->db->where_in('id', $customer_ids)->update('users', array('company_id' => $group));
            $this->db->where_in('user_id', $customer_ids)->update('customers', array('customer_referred_by' => $owner_id));
        }
    }

    public function add_items($order_id, $item_ids) {
        $this->load->factory('itemfactory');
        $items = $this->db->where_in('id', $item_ids)->get('items')->result();
        $this->ItemFactory->attach_subproducts($items);
        $this->item_model->attach_measurements($items);
        foreach ($items as $item) {
            $item->id = 'new';
            if ($item->subproducts) {
                foreach ($item->subproducts as $sp) {
                    $sp->id = 'new';
                    unset($sp->item_id);
                }
            }
        }
        $items = json_decode(json_encode($items), true);
        $this->item_model->save_order_items($items, $order_id);
        $this->db->where_in('id', $item_ids)->update('items', array('order_id' => $order_id));
    }

    function get_tech($order_id) {
        $tech = $this->db
                ->select("CONCAT(first_name, ' ', last_name) AS name", false)
                ->join('sites_techs', 'sites_techs.site_id=orders.site_id')
                ->join('users', 'users.id=sites_techs.tech_id')
                ->where('orders.id', $order_id)
                ->get('orders')->row();
        return $tech ? $tech->name : null;
    }
}
