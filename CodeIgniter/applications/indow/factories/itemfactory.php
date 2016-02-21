<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ItemFactory extends MM_Factory
{
	protected $_model = "Item_model";
	protected $_table = "items";
	protected $_primaryKey = "items.id";

    public function __construct()
    {
        parent::__construct();
	}

	public function getEstimateList($estimate_id)
	{
        $sql = "SELECT      items.id as id,
                            room,
                            location,
                            width,
                            height,
                            products.product,
                            products.id as product_id,
                            items.product_types_id,
                            price,
                            edging_id,
                            edging.name as edging,
                            special_geom,
                            ROUND(CEIL(width)*CEIL(height)/144, 2) as square_feet,
                            product_types.max_width,
			                product_types.max_height
                FROM        items
                INNER JOIN  estimates_has_item ON items.id = estimates_has_item.item_id
                INNER JOIN  estimates ON estimates.id = estimates_has_item.estimate_id
                INNER JOIN  product_types ON product_types.id = items.product_types_id
                INNER JOIN  products ON products.id = product_types.product_id
                INNER JOIN  edging ON edging.id = items.edging_id
                WHERE estimates_has_item.deleted = 0 AND estimate_id = ?
                ;
               ";
                            //items.price as retail,
//                INNER JOIN  sites ON sites.id = estimates.site_id

		$results = $this->db->query($sql, $estimate_id)->result();

		return $results;
	}

    public function attach_subproducts($items, $readonly = false, $msrp = 1, $always_array=false) {
        if (!count($items)) {
            return;
        }
        foreach ($items as $item) {
            $itemids[] = $item->id;
        }
        if ($readonly) {
            $msrp = (float) $msrp;
            $this->db->select('subitems.*, 
                IF(price_override IS NOT NULL, 
                    IF(min_price>price_override, min_price, price_override)*quantity*' . $msrp . ', ' .  
                   'IF(min_price>unit_price, min_price, unit_price)*quantity*' . $msrp . ')' .
                ' as price, 
                product_types.product_type as name', false);
        }
        $this->db->where_in('item_id', $itemids);
        if ($readonly) {
            $this->db->join('product_types', 'product_types.id=product_type_id');
        }
        $subproducts = $this->db->where('deleted', 0)->get('subitems')->result();
        $sub_assoc = array();
        foreach($subproducts as $sp) {
            if (isset($sub_assoc[$sp->item_id])) {
                $sub_assoc[$sp->item_id][$sp->product_type_id] = $sp;
            } else {
                $sub_assoc[$sp->item_id] = array($sp->product_type_id => $sp);
            }
        }
        foreach ($items as $item) {
            if (isset($sub_assoc[$item->id])) {
                $item->subproducts = $sub_assoc[$item->id];
            } else {
                if ($always_array) {
                    $item->subproducts = array();
                } else {
                    $item->subproducts = new stdClass();
                }
            }
        }
    }
    public function attach_measurements($items, $no_json=false) {
        if (!count($items)) {
            return;
        }
        foreach ($items as $item) {
            $itemids[] = $item->id;
        }
        $measurements = $this->db->from('items_measurements')->join('measurements', 'measurements.id=items_measurements.measurement_id')->where_in('item_id', $itemids)->get()->result();
        $m_assoc = array();
        foreach($measurements as $m) {
            if (isset($m_assoc[$m->item_id])) {
                $m_assoc[$m->item_id][$m->measurement_key] = $m->measurement_value;
            } else {
                $m_assoc[$m->item_id] = array($m->measurement_key => $m->measurement_value);
            }
        }
        foreach ($items as $item) {
            if (isset($m_assoc[$item->id])) {
                $item->measurements = $m_assoc[$item->id];
            } else {
                if ($no_json) {
                    $item->measurements = array();
                } else {
                    $item->measurements = new stdClass();
                }
            }
        }
    }
    
	public function getQuoteList($quote_id)
	{
        $sql = "SELECT      items.id as id,
                            room,
                            location,
                            width,
                            height,
                            floor,
                            product_id,
                            products.product,
                            items.product_types_id,
                            product_types.product_type,
                            unit_num,
                            edging_id,
                            special_geom,
                            extension,
                            items.price as price,
                            window_shape_id,
                            manufacturing_status,
                            drafty,
                            edging.name as edging,
                            notes,
                            frame_step,
                            frame_depth_id
                FROM        items
                INNER JOIN  quotes_has_item ON items.id = quotes_has_item.item_id
                INNER JOIN  quotes ON quotes.id = quotes_has_item.quote_id
                INNER JOIN  product_types ON product_types.id = items.product_types_id
                INNER JOIN  products ON product_id = products.id
                INNER JOIN  edging ON edging.id = edging_id
                WHERE quotes_has_item.deleted = 0 AND quote_id = ?
                ;
               ";

		$results = $this->db->query($sql, $quote_id)->result();
        $this->attach_subproducts($results);
        $this->attach_measurements($results);
		return $results;
	}

    protected function is_legacy($order) {
        return date('Y-m-d H:i:s', strtotime($order->created)) < $this->config->item('legacy_date');
    }
    
	public function getOrderList($order_id, $mfg_status = 1, $freebird = false) {
        if (gettype($mfg_status) === 'array') {
            $cleaned = array();
            foreach ($mfg_status as $status) {
                $cleaned[] = (integer) $status;
            }
            $mfg_where = ' manufacturing_status IN (' . implode(',', $cleaned) . ') ';
        } else {
            $mfg_status = (integer) $mfg_status;
            $mfg_where = " manufacturing_status = '$mfg_status' ";
        }

        $order = $this->order_model->get($order_id);
        $legacy = $this->is_legacy($order);
        $sql = "SELECT      items.id as id,
                        room,
                        location,
                        width,
                        height,
                        floor,
                        valid,
                        measured,
                        product_id,
                        unit_num,
                        items.product_types_id,
                        acrylic_panel_thickness,
                        edging_id,
                        special_geom,
                        extension,
                        items.price as price,
                        window_shape_id,
                        freebird_laser,
                        own_tools,
                        manufacturing_status,
                        drafty,
                        notes,
                        frame_step,
                        frame_depth_id,
                        top_spine,
                        side_spines,
                        price_override
            FROM        items
            INNER JOIN  orders_has_item ON items.id = orders_has_item.item_id 
            INNER JOIN  product_types ON product_types.id = items.product_types_id 
            WHERE orders_has_item.deleted = 0 AND $mfg_where AND orders_has_item.order_id = ?
            ;
           ";
          //  INNER JOIN  orders ON orders.id = orders_has_item.order_id

		$results = $this->db->query($sql, array($order_id))->result();
        $this->attach_subproducts($results);
        $this->attach_measurements($results);
        if ($freebird) {
            foreach ($results as $item) {
                if ($legacy) {
                    if ($item->freebird_laser) {
                        foreach ($item->measurements as $key => $measurement) {
                            $item->measurements[$key] -= 1;
                        }
                    }
                } else {
                    if (!$item->own_tools && gettype($item->measurements) === 'array') {
                        $item->measurements['E'] -= 2;
                        $item->measurements['F'] -= 2;
                    }
                }
            }
        }
		return $results;
	}

    public function getSiteList($site_id) {
        $sql = "SELECT      items.id as id,
                        room,
                        location,
                        floor,
                        product_id,
                        unit_num,
                        items.product_types_id,
                        edging_id,
                        special_geom,
                        extension,
                        items.price as price,
                        top_spine,
                        side_spines,
                        measured,
                        valid,
                        window_shape_id,
                        manufacturing_status,
                        drafty,
                        own_tools,
                        notes,
                        frame_step,
                        frame_depth_id
            FROM        items
            INNER JOIN  site_has_items ON items.id = site_has_items.item_id 
            INNER JOIN  product_types ON product_types.id = items.product_types_id 
            WHERE site_has_items.deleted = 0 AND items.measured = 1 AND site_has_items.site_id = ? 
            ;
           ";
          //  INNER JOIN  orders ON orders.id = orders_has_item.order_id

		$results = $this->db->query($sql, $site_id)->result();
        $this->attach_subproducts($results);
        $this->attach_measurements($results);
        foreach ($results as $result) {
            $result->height = isset($result->measurements->B) ? $result->measurements->B : '';
            $result->width = isset($result->measurements->D) ? $result->measurements->D : '';
        }
		return $results;
	}

	public function getOrderConfList($order_id, $mfg_status = 1) {
        $dealer_id = $this->db->where('id', $order_id)->get('orders')->row()->dealer_id;
        $this->load->model('group_model');
        //$msrp = $this->group_model->get_msrp($dealer_id);
        $sql = "SELECT      items.id as id,
                        room,
                        location,
                        floor,
                        unit_num,
                        product_types.product_type,
                        product,
                        edging.name as edging,
                        special_geom,
                        items.price as price,
                        window_shape_id,
                        manufacturing_status,
                        drafty,
                        notes,
                        frame_step,
                        frame_depth_id,
                        price_override
            FROM        items
            INNER JOIN  orders_has_item ON items.id = orders_has_item.item_id 
            INNER JOIN  product_types ON product_types.id = items.product_types_id 
            JOIN products ON products.id=product_id
            LEFT JOIN edging ON edging.id=edging_id
            WHERE orders_has_item.deleted = 0 AND manufacturing_status = ? AND orders_has_item.order_id = ?
            ;
           ";
          //  INNER JOIN  orders ON orders.id = orders_has_item.order_id

		$results = $this->db->query($sql, array($mfg_status, $order_id))->result();
        $this->attach_subproducts($results, true);
        $this->attach_measurements($results);

        foreach($results as $result) {
            if (gettype($result->measurements) === 'array' && isset($result->measurements['B'])) {
                $result->width = $result->measurements['B'];
            } else {
                $result->width = '';
            }
            if (gettype($result->measurements) === 'array' && isset($result->measurements['D'])) {
                $result->height = $result->measurements['D'];
            } else {
                $result->height = '';
            }
        }
		return $results;
	}
    
	public function getEstimateList_wsubitems($estimate_id, $names=true) {
        if (!$names) {
            $sql = "SELECT  items.id as id,
                            room,
                            location,
                            width,
                            height,
                            floor,
                            product_id,
                            unit_num,
                            items.product_types_id,
                            edging_id,
                            special_geom,
                            extension,
                            items.price as price,
                            window_shape_id,
                            manufacturing_status,
                            drafty,
                            notes,
                            frame_step,
                            frame_depth_id
                FROM        items
                INNER JOIN  estimates_has_item ON items.id = estimates_has_item.item_id
                INNER JOIN  product_types ON product_types.id = items.product_types_id
                WHERE estimates_has_item.deleted = 0 AND estimate_id = ?
                ;
               ";
        } else {
                $sql = "SELECT      items.id as id,
                        room,
                        location,
                        width,
                        height,
                        floor,
                        product_id,
                        products.product,
                        unit_num,
                        items.product_types_id,
                        product_types.product_type,
                        edging_id,
                        edging.name as edging,
                        special_geom,
                        extension,
                        IF(special_geom=1,'Yes','No') AS special_geom_yn,
                        items.price as price,
                        window_shape_id,
                        manufacturing_status,
                        drafty,
                        notes,
                        frame_step,
                        frame_depth_id
                FROM        items
                INNER JOIN  estimates_has_item ON items.id = estimates_has_item.item_id
                INNER JOIN  product_types ON product_types.id = items.product_types_id
                JOIN products ON product_id=products.id
                JOIN edging ON edging.id=edging_id
                WHERE estimates_has_item.deleted = 0 AND estimate_id = ?
            ;
           ";
        }
		$results = $this->db->query($sql, $estimate_id)->result();
        $this->attach_subproducts($results);
        //$this->attach_measurements($results);
		return $results;
	}
}
