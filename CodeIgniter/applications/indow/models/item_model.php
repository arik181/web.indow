<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Item_model extends MM_Model
{

    protected  $_table = "items";
    protected  $_soft_delete = true;
    protected  $_key = "id";

    public $id;                    // integer
    public $site_id;               // integer
    public $product_attributes_id; // integer
    public $quality_control_id;    // integer
    public $price;                 // double
    public $image_url;             // varchar
    public $width;                 // double
    public $height;                // double
    public $frame_depth_id;        // integer
    public $edging_id;             // integer
    public $parent_item_id;        // integer
    public $special_geom;          // integer
    public $deleted;               // integer
    public $product_id;            // integer

    public function __construct()
    {
        parent::__construct();
        $key = $this->_key;
        if ($this->$key)
            return;
        $this->price          = 0.0;
        $this->width          = 0;
        $this->height         = 0;
        $this->edging_id      = 1;
        $this->frame_depth_id = 1;
        $this->parent_item_id = 0;
        $this->special_geom   = 0;
        $this->product_id     = 0;
        $this->deleted        = 0;
        $this->load->model('order_model');
    }

    public function save()
    {
        $sql = "";
        if ($this->id > 0) 
        {
            $sql = "UPDATE      $this->_table
                    SET         site_id = ?
                                product_attributes_id = ?
                                quality_control_id = ?
                                price = ?
                                image_url = ?
                                width = ?
                                height = ?
                                edging_id = ?
                                frame_depth_id = ?
                                parent_item_id = ?
                                special_geom = ?
                                deleted = ?
                                product_id = ?
                    WHERE       id = ? ;"
                    ;
        }
        else 
        {
            $sql = "INSERT INTO $this->_table
                    SET         site_id = ?
                                product_attributes_id = ?
                                quality_control_id = ?
                                price = ?
                                image_url = ?
                                width = ?
                                height = ?
                                edging_id = ?
                                frame_depth_id = ?
                                parent_item_id = ?
                                special_geom = ?
                                deleted = ?
                                product_id = ? ;"
                    ;
        }

        $this->db->query($sql,array($this->site_id, 
                                    $this->product_attributes_id, 
                                    $this->quality_control_id, 
                                    $this->price, 
                                    $this->image_url, 
                                    $this->width, 
                                    $this->height, 
                                    $this->edging_id, 
                                    $this->frame_depth_id, 
                                    $this->parent_item_id, 
                                    $this->special_geom, 
                                    $this->deleted, 
                                    $this->product_id));
        if ($this->id == 0)
            $this->id = $this->db->insert_id();

        return array('success' => true, 'message' => 'Updated item successfully.');
    }

    public function getProfile($id)
    {
        $sql = '
            SELECT  *
            FROM    items
            WHERE   items.id = ?;
            ';

        $query = $this->db->query($sql, $id);

        if ( ! empty($query)  && $query->num_rows() > 0) 
        {
            return $query->row();
        }
        else{
            return false;
        }
    }

    public function fetch_items($estimate_id)
    {
        $sql = "SELECT      items.id as id,
                            room,
                            location,
                            width,
                            height,
                            products.product,
                            product_types.product_type,
                            edging.name,
                            special_geom,
                            items.price as retail,
                            total_square_feet
                FROM        items
                INNER JOIN  estimates_has_item ON items.id = estimates_has_item.item_id
                INNER JOIN  estimates ON estimates.id = estimates_has_item.estimate_id
                INNER JOIN  sites ON sites.id = estimates.site_id
                INNER JOIN  product_types ON product_types.id = items.product_types_id
                INNER JOIN  products ON products.id = product_types.product_id
                INNER JOIN  edging ON edging.id = items.edging_id
                WHERE       estimates_has_item.estimate_id = ? 
                ;
               ";
        $query = $this->db->query($sql, $estimate_id);

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

    public function delete_items($allitems, $parent_id, $parent_id_name, $assoc_table) {
        $items = array();
        $subitems = array();
        foreach ($allitems as $item) {
            if ($item['type'] === 'subitem') {
                $subitems[] = $item['id'];
            } else {
                $items[] = $item['id'];
            }
        }
        //prd(array('items' => $items, 'subitems' => $subitems));
        if (count($items)) {
            $this->db->where_in('item_id', $items)->where($parent_id_name, $parent_id);
            $this->db->update($assoc_table, array('deleted' => 1));
        }
        if (count($subitems)) {
            $this->db->where_in('id', $subitems);
            $this->db->update('subitems', array('deleted' => 1));
        }
    }
    public function delete_quote_items($items, $quote_id) {
        $this->delete_items($items, $quote_id, 'quote_id', 'quotes_has_item');
    }
    public function delete_order_items($items, $order_id) {
        $this->delete_items($items, $order_id, 'order_id', 'orders_has_item');
        $this->order_model->set_item_numbers($order_id);
        
    }
    public function delete_site_items($items, $site_id) {
        $this->delete_items($items, $site_id, 'site_id', 'site_has_items');
    }
    public function saveitems($data, $parent_id, $parent_id_name, $assoc_table, $forcenew=false, $wmod=null, $skip_spines_calc=false) {
        foreach ($data as $item) {
            unset($item['checked']);
            $subproducts = false;
            if (isset($item['subproducts']) && count($item['subproducts'])) {
                $subproducts = $item['subproducts'];
            }
            $measurements = false;
            if (isset($item['measurements']) && count($item['measurements'])) {
                $measurements = $item['measurements'];
            }
            unset($item['subproducts']);
            unset($item['product_id']);
            unset($item['unknown']);
            unset($item['measurements']);

            if ($item['id'] === 'new' || $forcenew) {
                $oldid = $item['id'];
                unset($item['id']);
                unset($item['product']);
                if ($parent_id_name === 'site_id') {
                    $item['site_id'] = $parent_id;
                }
                $this->db->insert('items', $item);
                $itemid = $this->db->insert_id();
                $parent_has_item = array(
                    $parent_id_name => $parent_id,
                    'item_id' => $itemid
                );
                if ($wmod === 'osite' && $oldid !== 'new') {
                    $this->db->where('id', $oldid)->update('items', array('order_id' => $parent_id, 'order_item_id' => $itemid));
                }
                $this->db->insert($assoc_table, $parent_has_item);
                if ($parent_id_name === 'order_id') {
                    $this->queue_item($itemid, $parent_id);
                }
            } else {
                $itemid = (integer) $item['id'];
                if ($parent_id_name === 'order_id') {
                    $this->queue_item_if_changed($itemid, $parent_id, $item, $measurements);
                }
                $this->db->where('id', $item['id']);
                $this->db->update('items', $item);
            }
            if ($subproducts) {
                foreach ($subproducts as $subitem) {
                    $subitem['item_id'] = $itemid;
                    unset($subitem['checked']);
                    unset($subitem['price']);
                    if ($subitem['id'] === 'new' || $forcenew) {
                        unset($subitem['id']);
                        $this->db->insert('subitems', $subitem);
                    } else {
                        $this->db->where('id', $subitem['id']);
                        $this->db->update('subitems', $subitem);
                    }
                }
            }
            if ($measurements) {
                $this->setmeasurements($measurements, $itemid, false, $skip_spines_calc);
            }
        }
    }
    public function save_quote_items($items, $quote_id, $forcenew=false, $wmod=null) {
        $this->saveitems($items, $quote_id, 'quote_id', 'quotes_has_item', $forcenew, $wmod);
    }
    public function save_order_items($items, $order_id, $forcenew=false, $wmod=null, $skip_spines_calc = false) {
        //$this->order_model->set_item_numbers2($items);
        $this->saveitems($items, $order_id, 'order_id', 'orders_has_item', $forcenew, $wmod, $skip_spines_calc);
        $this->order_model->set_item_numbers($order_id);
    }
    public function save_site_items($items, $site_id, $forcenew=false) {
        $this->saveitems($items, $site_id, 'site_id', 'site_has_items', $forcenew);
    }
    public function setmeasurements($measurements, $item_id, $skip_delete=false, $skip_spines=false) {
        $res = $this->db->where('item_id', $item_id)->get('items_measurements')->result();

        if (!$skip_delete) {
            $measurement_ids = array();
            foreach ($res as $m) {
                $measurement_ids[] = $m->measurement_id;
            }
            if (count($measurement_ids)) {
                $this->db->where_in('id', $measurement_ids)->delete('measurements');
            }
            $this->db->where('item_id', $item_id)->delete('items_measurements');
        }

        foreach ($measurements as $k => $v) {
            if ($v !== '') {
                $measurement = array(
                    'valid' => 1,
                    'measurement_key' => $k,
                    'measurement_value' => $v
                );
                $this->db->insert('measurements', $measurement);
                $measurement_id = $this->db->insert_id();
                $item_measurement = array(
                    'measurement_id' => $measurement_id,
                    'item_id' => $item_id
                );
                $this->db->insert('items_measurements', $item_measurement);
            }
        }


        if (!$skip_spines) {
            if (isset($measurements['B']) && isset($measurements['C'])) {
                $b = $measurements['B'];
                $c = $measurements['C'];
                $top_spine = ($b >= 42 && $c >= 30) ? 1 : 0;
                $side_spines = ($c >= 60 && $b >= 36) ? 1 : 0;
				$no_spines_ids = array(9000);
                $this->db->where('id', $item_id)->where_not_in('product_types_id', $no_spines_ids)->update('items', array('top_spine' => $top_spine, 'side_spines' => $side_spines));
            }
        }

    }

    public function copy_items($from_type, $from_id, $to_type, $to_id) {
        if ($from_type === 'estimate') {
            $adj_table = 'estimates_has_item';
            $adj_key = 'estimate_id';
        } elseif ($from_type === 'quote') {
            $adj_table = 'quotes_has_item';
            $adj_key = 'quote_id';
        }

        if ($to_type === 'quote') {
            $to_adj_table = 'quotes_has_item';
            $to_adj_key = 'quote_id';
        } elseif ($to_type === 'order') {
            $to_adj_table = 'orders_has_item';
            $to_adj_key = 'order_id';
        }

        //get list of items
        $this->db->select('items.*');
        $this->db->from($adj_table)->join('items', "$adj_table.item_id=items.id");
        $this->db->where("$adj_table.$adj_key", $from_id)->where("$adj_table.deleted", 0);
        $items = $this->db->get()->result();
        //prd($this->db->last_query());
        if (!count($items)) {
            return;
        }
        $item_ids = $this->id_list($items);

        //load subitems
        $subitems_raw = $this->db->where_in('item_id', $item_ids)->where('deleted', 0)->get('subitems')->result();
        $subitems = $this->group_assoc($subitems_raw, 'item_id');

        //load measurements
        if ($from_type !== 'estimate') {
            $this->db->select('measurement_key,measurement_value,item_id');
            $measurements_raw = $this->db->from('items_measurements')->join('measurements', 'measurements.id=measurement_id')->where_in('item_id', $item_ids)->get()->result();
            $measurements = $this->group_assoc($measurements_raw, 'item_id', $unset_key=true);
        }
        
        foreach ($items as $item) {
            $isubitems = false;
            if (isset($subitems[$item->id])) {
                $isubitems = $subitems[$item->id];
            }

            $imeasurements = array();
            if ($from_type === 'estimate') {
                if ($item->height) {
                    //$imeasurements['D'] = $item->height;
                    unset($item->height);
                }
                if ($item->width) {
                    //$imeasurements['B'] = $item->width;
                    unset($item->width);
                }
            } else {
                if (isset($measurements[$item->id])) {
                    foreach($measurements[$item->id] as $m) {
                        $imeasurements[$m->measurement_key] = $m->measurement_value;
                    }
                }
            }

            unset($item->id);
            $this->db->insert('items', $item);
            $item_id = $this->db->insert_id();
            if ($item->site_item_id && $from_type === 'quote' && $to_type === 'order') { //set order_item_id and order_id on the sites items
                $this->db->where('id', $item->site_item_id)->update('items', array('order_id' => $to_id, 'order_item_id' => $item_id));
            }
            $this->db->insert($to_adj_table, array(
                'item_id'   => $item_id,
                $to_adj_key => $to_id
            ));
            if ($imeasurements) {
                $this->setmeasurements($imeasurements, $item_id, $skip_delete=true);
            }
            if ($isubitems) {
                foreach ($isubitems as $subitem) {
                    unset($subitem->id);
                    $subitem->item_id = $item_id;
                    $this->db->insert('subitems', $subitem);
                }
            }
        }
        //prd($subitems);
    }

    public function get_totals($subtotal, $fees) {
        $mod_totals = $this->sales_modifiers_model->get_modifier_totals($fees);
        $totals = array();
        $totals['subtotal'] = $subtotal;
        $totals['fees'] = $subtotal * $mod_totals['fees_percent'] / 100 + $mod_totals['fees_dollars'];
        $totals['discounts'] = ($subtotal + $totals['fees']) * $mod_totals['discounts_percent'] / 100 + $mod_totals['discounts_dollars'];
        $totals['taxes'] = ($subtotal + $totals['fees'] - $totals['discounts']) * $mod_totals['taxes_percent'] / 100 + $mod_totals['taxes_dollars'];
        $totals['total'] = $subtotal + $totals['fees'] - $totals['discounts'] + $totals['taxes'];
        return $totals;
    }

    public function get_subtotal($items) {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item->price;
            if (count($item->subproducts)) {
                foreach ($item->subproducts as $p) {
                    $subtotal += $p->price;
                }
            }
        }
        return $subtotal;
    }

    public function get_items_w_totals($type, $id) {
        $this->load->factory('itemfactory');
        if ($type === 'estimate') {
            $table = 'estimates_has_item';
            $join = 'estimates_has_item.item_id=items.id';
            $id_name = 'estimate_id';
        }
        $items = $this->db->select('items.id, price')->from($table)->join('items', $join)->where($id_name, $id)->get()->result();
        $this->ItemFactory->attach_subproducts($items, true);
        return $items;
        
    }

    public function get_thickness($ptype, $sqft, $product_id) {
        $ptype = (integer) $ptype;
        $spec_products = array(6, 7, 8, 45, 46, 47, 70, 71, 74);
        if (in_array($ptype, $spec_products)) {
            return '1/4"';
        } else {
            if ($product_id == 1) {
                if ($sqft < 20) {
                    return '1/8"';
                } else {
                    return '3/16"';
                }
            } else {
                return '1/8"';
            }
        }
    }

    public function calc_item_thickness($items) {
        $this->load->factory('itemfactory');
        $this->itemfactory->attach_measurements($items);
        foreach ($items as $item) {
            $sqft = max(@$item->measurements['A'], @$item->measurements['B']) * max(@$item->measurements['C'], @$item->measurements['D']) / 144;
            $thickness = $this->get_thickness($item->product_types_id, $sqft, $item->product_id);
            $this->db->where('id', $item->id)->update('items', array('acrylic_panel_thickness' => $thickness));
        }
    }

    public function get_estimate_totals($id) {
        $items = $this->get_items_w_totals('estimate', $id);
        $modifiers = $this->sales_modifiers_model->get_estimate_fees($id);
        $subtotal = $this->get_subtotal($items);
        return $this->get_totals($subtotal, $modifiers);
    }

    public function attach_measurements($items) {
        if (!count($items)) {
            return array();
        }
        $item_ids = array();
        foreach ($items as $item) {
            $item_ids[] = $item->id;
        }
        $measurements = $this->db->join('items_measurements', 'measurement_id=measurements.id')->where_in('item_id', $item_ids)->get('measurements')->result();
        $measurements_sorted = array();
        foreach ($measurements as $measurement) {
            $measurements_sorted[$measurement->item_id][$measurement->measurement_key] = $measurement->measurement_value;
        }
        foreach ($items as $item) {
            $item->measurements = empty($measurements_sorted[$item->id]) ? array() : $measurements_sorted[$item->id];
        }
        return $items;
    }

    public function attach_orders($items, $reverse=false) {
        $item_ids = array();
        foreach ($items as $item) {
            $item_ids[] = $item->id;
        }
        $orders = $this->db
                ->select('first_name, last_name, orders.*, groups.name as dealer_name')
                ->where_in('item_id', $item_ids)
                ->join('orders', 'orders.id=orders_has_item.order_id')
                ->join('orders_has_customers', 'orders_has_customers.order_id=orders.id AND orders_has_customers.primary=1', 'left')
                ->join('users', 'users.id=orders_has_customers.customer_id', 'left')
                ->join('groups', 'groups.id=orders.dealer_id', 'left')
                ->group_by('orders.id')
                ->get('orders_has_item')->result();
        $orders_assoc = array();
        foreach ($orders as $order) {
            $orders_assoc[$order->id] = $order;
        }
        if ($reverse) {
            foreach ($orders as $order) {
                $order->items = array();
            }
            foreach ($items as $item) {
                $orders_assoc[$item->i_order_id]->items[] = $item;
            }
            return $orders;
        } else {
            foreach ($items as $item) {
                $item->order = $orders_assoc[$item->i_order_id];
            }
            return $items;
        }
    }

    public function process_item($item_id) {
        $this->db->where('item_id', $item_id)->update('cut_calculations', array('deleted' => 1));
        $item = $this->get_item_and_measurements($item_id);
        $this->update_cut_calculations($item, true);
    }

    public function queue_item($item_id, $order_id) {
        $this->db->where('item_id', $item_id)->update('cut_calculations', array('deleted' => 1));
        $this->db->insert('cut_calculations', array('item_id' => $item_id, 'order_id' => $order_id));
    }

    public function queue_item_if_changed($item_id, $order_id, $item, $measurements) {
        $dbitem = $this->get_item_and_measurements($item_id);
        if ($dbitem->measurements !== $measurements || $item['window_shape_id'] != $dbitem->window_shape_id) {
            $this->queue_item($item_id, $order_id);
        }
    }

    public function get_cut_queue() {
        $items = $this->db
                ->select('items.*, cut_calculations.id as calc_id')
                ->join('items', 'items.id=item_id')
                ->where('items.deleted', 0)
                ->where('cut_calculations.status', 0)
                ->where('cut_calculations.deleted', 0)
                ->get('cut_calculations')->result();
        return $this->attach_measurements($items);
    }

    public function get_item_and_measurements($item_id) {
        $item = $this->db->where('id', $item_id)->get('items')->row();
        if (!$item) {
            return null;
        }
        $items = $this->attach_measurements(array($item));
        return $items[0];
    }

    public function has_measurements($item) {
        return
            isset($item->measurements['A']) &&
            isset($item->measurements['B']) &&
            isset($item->measurements['C']) &&
            isset($item->measurements['D']) &&
            isset($item->measurements['E']) &&
            isset($item->measurements['F']);
    }

    public function update_cut_calculations($item, $new=false) {
        $this->load->helper(array('cut_script_rectangle'));
        if ($this->has_measurements($item)) {
            $measurements = array(
                $item->measurements['A'],
                $item->measurements['B'],
                $item->measurements['C'],
                $item->measurements['D'],
                $item->measurements['E'],
                $item->measurements['F']
            );
            if ($item->extension) {
                $num = $item->freebird_laser ? 11 : 12;
                $measurements[4] += $num;
                $measurements[5] += $num;
            }
            $product = $this->db->where('id', $item->product_types_id)->get('product_types')->row();
            $item->product = $product;
            
            $calculations = get_calcs($measurements, $item);
            $cut_calc_update = array(
                'status' => 1,
                'sheet_width' => $calculations['sheet_dimensions'][0],
                'sheet_height' => $calculations['sheet_dimensions'][1],
                'cuts' => json_encode($calculations['cuts']),
                'error_margin' => $calculations['error_margin']
            );
            if ($new) {
                $cut_calc_update['item_id'] = $item->id;
                $order_item = $this->db->where('item_id', $item->id)->get('orders_has_item')->row();
                $cut_calc_update['order_id'] = $order_item ? $order_item->order_id : null;
                $this->db->insert('cut_calculations', $cut_calc_update);
            } else {
                $this->db->where('id', $item->calc_id)->where('deleted', 0)->update('cut_calculations', $cut_calc_update);
            }
        }
    }

    public function get_print_orders_with_items($order_ids) {
        $this->load->factory('itemfactory');
        $items = $this->db
            ->select("
                orders_has_item.order_id AS i_order_id,
                items.*,
                product,
                product_type,
                edging.name AS edging,
                window_shapes.name AS shape,
                sheet_width,
                sheet_height,
                cuts
            ")
            ->where_in('orders_has_item.order_id', $order_ids)
            ->where('items.manufacturing_status', 1)
            ->where('orders_has_item.deleted', 0)
            ->join('items', 'orders_has_item.item_id=items.id')
            ->join('product_types', 'product_types_id=product_types.id')
            ->join('products', 'products.id=product_types.product_id')
            ->join('edging', 'items.edging_id=edging.id', 'left')
            ->join('window_shapes', 'items.window_shape_id=window_shapes.id', 'left')
            ->join('cut_calculations', 'cut_calculations.item_id=items.id AND cut_calculations.status=1 AND cut_calculations.deleted=0', 'left')
            ->order_by('i_order_id, items.unit_num')
            ->get('orders_has_item')->result();
        $this->itemfactory->attach_subproducts($items, true, 1, true);
        $this->item_model->attach_measurements($items);
        $orders = $this->db
                ->select("
                    orders.*,
                    CONCAT(users.first_name, ' ', users.last_name) AS customer_name,
                    users.first_name,
                    users.last_name,
                    groups.name AS dealer_name,
                    group_addresses.address AS group_address,
                    group_addresses.address_ext AS group_address_ext,
                    group_addresses.city AS group_city,
                    group_addresses.state AS group_state,
                    group_addresses.zipcode AS group_zip,
                    user_addresses.address AS user_address,
                    user_addresses.address_ext AS user_address_ext,
                    user_addresses.city AS user_city,
                    user_addresses.state AS user_state,
                    user_addresses.zipcode AS user_zip,
                    users.phone_1 as user_phone_1,
                    users.phone_2 as user_phone_2,
                    groups.phone_1 as group_phone_1,
                    groups.phone_2 as group_phone_2,
                    (SELECT status_changed FROM orders_status_codes JOIN status_codes ON status_codes.id=order_status_code_id WHERE order_id=orders.id AND status_codes.code=350 ORDER BY orders_status_codes.id DESC LIMIT 1) AS order_date,
                    (SELECT status_changed FROM orders_status_codes JOIN status_codes ON status_codes.id=order_status_code_id WHERE order_id=orders.id AND status_codes.code=700 ORDER BY orders_status_codes.id DESC LIMIT 1) AS ship_date
                    
                ", false)
                ->join('orders_has_customers', 'orders_has_customers.order_id=orders.id AND orders_has_customers.primary=1', 'left')
                ->join('users', 'orders_has_customers.customer_id=users.id', 'left')
                ->join('groups', 'groups.id=orders.dealer_id', 'left')
                ->join('group_addresses', "group_addresses.group_id=orders.dealer_id AND group_addresses.address_type='Billing'", 'left')
                ->join('user_addresses', "user_addresses.user_id=users.id AND user_addresses.address_type LIKE '%shipping%'", 'left')
                ->where_in('orders.id', $order_ids)
                ->get('orders')->result();
        $orders_assoc = array();
        foreach ($orders as $order) {
            $order->user_phone = $order->user_phone_1 ? $order->user_phone_1 : $order->user_phone_2;
            $order->group_phone = $order->group_phone_1 ? $order->group_phone_1 : $order->group_phone_2;
            $order->items = array();
            $orders_assoc[$order->id] = $order;
        }
        foreach ($items as $item) {
            $sqft = max($item->measurements['C'], $item->measurements['D']) * max($item->measurements['A'], $item->measurements['B']) / 144;
            $height_l = !empty($item->measurements['C']) ? $item->measurements['C'] : 0;
            $height_r = !empty($item->measurements['D']) ? $item->measurements['D'] : 0;
            $height = max($height_l, $height_r);
            $item->pull_location = ceil($height * (2/3));
            if ($sqft > 8) {
                $item->bracket_location = ceil($height * (2/3)) + 1;
            } else {
                $item->bracket_location = 'N/A';
            }
            $orders_assoc[$item->i_order_id]->items[] = $item;
        }
        foreach ($orders as $order) {
            $assoc_products = array();
            $order->shipping_address = $this->order_model->get_shipping_address($order->id);
            foreach ($order->items as $item) {
                $assoc_products = array_merge($assoc_products, $item->subproducts);
            }
            $order->assoc_products = $assoc_products;
        }
        return $orders;
    }

    public function get_print_items_and_orders($item_ids, $by_order = false) {
        if (empty($item_ids)) {
            return array();
        }
        $items = $this->db
                ->select("
                    orders_has_item.order_id AS i_order_id,
                    items.*,
                    product,
                    product_type,
                    edging.name AS edging,
                    window_shapes.name AS shape,
                    sheet_width,
                    sheet_height,
                    ship_method,
                    cuts,
                    (SELECT sum(quantity) FROM subitems JOIN product_types ON product_types.id=product_type_id WHERE product_type='Storage Sleeves' AND item_id=items.id AND subitems.deleted=0) AS sleevecount
                ")
                ->where_in('orders_has_item.item_id', $item_ids)
                ->join('items', 'orders_has_item.item_id=items.id')
                ->join('product_types', 'product_types_id=product_types.id')
                ->join('products', 'products.id=product_types.product_id')
                ->join('orders', 'orders_has_item.order_id=orders.id')
                ->join('edging', 'items.edging_id=edging.id', 'left')
                ->join('window_shapes', 'items.window_shape_id=window_shapes.id', 'left')
                ->join('cut_calculations', 'cut_calculations.item_id=items.id AND cut_calculations.status=1 AND cut_calculations.deleted=0', 'left')
                ->order_by('i_order_id, items.unit_num')
                ->get('orders_has_item')->result();
        $this->attach_measurements($items);
        foreach ($items as $item) {
            $width_t = !empty($item->measurements['A']) ? $item->measurements['A'] : 0;
            $width_b = !empty($item->measurements['B']) ? $item->measurements['B'] : 0;
            $height_l = !empty($item->measurements['C']) ? $item->measurements['C'] : 0;
            $height_r = !empty($item->measurements['D']) ? $item->measurements['D'] : 0;
            $height = max($height_l, $height_r);
            $width = max($width_t, $width_b);
            $item->horizontal = $width > $height;
            $item->sqft = round($width * $height / 144, 2);
            $item->pull_location = ceil($height * (2/3));
            if ($item->sqft > 8) {
                $item->bracket_location = ceil($height * (2/3)) + 1;
            } else {
                $item->bracket_location = 'N/A';
            }
        }
        $items = $this->attach_orders($items, $by_order);
        return $items;
    }

    protected function load_accoustic_types() {
        if (empty($this->accoustic_types)) {
            $this->accoustic_types = array();
            $types = $this->db->select('id,abbrev')->get('product_types')->result();
            foreach ($types as $type) {
                if (substr($type->abbrev, 0, 2) === 'A-') {
                    $this->accoustic_types[] = $type->id;
                }
            }
        }
    }

    public function get_weight($product_type_id, $sqft) {
        $this->load_accoustic_types();
        if (in_array($product_type_id, $this->accoustic_types)) {
            return round($sqft * 2, 2);
        } else {
            return round($sqft, 2);
        }
    }
}
