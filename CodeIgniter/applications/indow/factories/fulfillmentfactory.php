<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class FulfillmentFactory extends MM_Factory {

    protected $_model = "Product_model";
    protected $_table = "products";
    protected $_primaryKey = "orders.id";
    protected $_limit = " limit 5;";
    protected $_limit_num = 5;
    protected $_no_limit = " ;";
    protected $_final = " ";

    public function __construct() {
        parent::__construct();
        $this->load->helper('array');
    }

    protected function changemode($flag, &$temp) {
        if ($flag == 0) {
            $temp = $this->_limit;
        } else {
            $temp = $this->_no_limit;
        }
    }

    protected function status_id($code) {
        return $this->db->where('code', $code)->get('status_codes')->row()->id;
    }

    public function getScheduleList($flag = 0) {
        $this->changemode($flag, $this->_final);
        $status_code_id = $this->status_id(200);

        $query = "SELECT    
                        orders.id AS id,
                        orders.order_number as name,
                        DATE_FORMAT(orderstatus.status_changed,'%m/%d/%Y') AS status_update,
                        CONCAT(users.first_name,' ',users.last_name) AS customer,
                        CONCAT(creator.first_name,' ',creator.last_name) AS creator,
                        groups.name As company,
                        CONCAT(sites.city,', ',sites.state) as address,                          
                        DATE_FORMAT(orderstatus.status_changed,'%m/%d/%Y') as msr
                    FROM orders
                    INNER JOIN  orders_status_codes as orderstatus ON orderstatus.order_id=orders.id AND order_status_code_id = $status_code_id
                    LEFT JOIN orders_has_customers ON orders_has_customers.order_id=orders.id AND orders_has_customers.primary = 1
                    LEFT JOIN users ON users.id = orders_has_customers.customer_id
                    JOIN users creator ON creator.id=orders.created_by
                    INNER JOIN sites ON sites.id=orders.site_id
                    LEFT JOIN groups ON groups.id=orders.dealer_id
                    WHERE orders.status_code = $status_code_id AND orders.deleted=0
                    GROUP BY orders.id ORDER BY orders.created";
//                    INNER JOIN estimates_estimate_status_codes as estimatestatus ON estimatestatus.estimate_id=orders.estimate_id 

        $query.=$this->_final;

        $results = $this->db->query($query)->result();

        return $results;
    }

    public function getNewOrderList($flag = 0) {
        $this->changemode($flag, $this->_final);
        $status_code_id = $this->status_id(300);

        $query = "SELECT    
                        orders.id AS id,
                        orders.order_number as name,
                        CONCAT(users.first_name,' ',users.last_name) AS customer,
                        groups.name As company,
                        CONCAT(sites.city, ', ', sites.state) as address,
                        DATE_FORMAT(orders.created,'%m/%d/%Y') as created,
                        CONCAT(creator.first_name,' ',creator.last_name) AS creator
                    FROM orders
                    LEFT JOIN orders_has_customers ON orders_has_customers.order_id=orders.id AND orders_has_customers.primary = 1
                    LEFT JOIN users ON users.id = orders_has_customers.customer_id
                    INNER JOIN sites ON sites.id=orders.site_id
                    LEFT JOIN groups ON groups.id=orders.dealer_id 
                    JOIN users creator ON creator.id=orders.created_by
                    WHERE status_code = $status_code_id AND orders.deleted=0
                    ORDER BY orders.created 
                    
                    ";
        $query.=$this->_final;
        $results = $this->db->query($query)->result();

        return $results;
    }

    public function getFinalReviewList($flag = 0) {
        $this->changemode($flag, $this->_final);
        $status_code_id = $this->status_id(330);

        $query = "SELECT    
                        orders.id AS id,
                        orders.order_number as name,
                        CONCAT(users.first_name,' ',users.last_name) AS customer,
                        groups.name As company,
                        (SELECT DATE_FORMAT(status_changed,'%m/%d/%Y') 
                                FROM orders_status_codes JOIN status_codes ON order_status_code_id=status_codes.id
                                WHERE code=300 AND order_id=orders.id ORDER BY status_changed DESC LIMIT 1
                        ) as submitted,
                         (
                            CASE 
                                WHEN orders.final_payment_received = '1' THEN 'checked'
                                WHEN orders.final_payment_received = '0' THEN ''
                            END
                          ) AS payment,
                        (SELECT DATE_FORMAT(status_changed,'%m/%d/%Y') FROM orders_status_codes WHERE order_id=orders.id ORDER BY status_changed DESC LIMIT 1) as modified,
                        CONCAT(creator.first_name,' ',creator.last_name) AS creator,
                        DATE_FORMAT(created,'%m/%d/%Y') AS created,
                        credit,
                        total,
                        groups.credit_hold,
                        (SELECT count(*) FROM payments
                                JOIN payment_types ON payment_type_id=payment_types.id
                                WHERE deleted = 0 AND order_id=orders.id AND payment_types.name LIKE '%down%'
                        ) AS down_payment
                    FROM orders
                    LEFT JOIN orders_has_customers ON orders_has_customers.order_id=orders.id AND orders_has_customers.primary = 1
                    LEFT JOIN users ON users.id = orders_has_customers.customer_id
                    LEFT JOIN groups ON groups.id=orders.dealer_id
                    JOIN users creator ON creator.id=orders.created_by
                    WHERE status_code = $status_code_id AND orders.deleted=0
                     ORDER BY orders.created 
                     
                    ";
        $query.=$this->_final;
        $results = $this->db->query($query)->result();
        foreach ($results as $result) {
            if ($result->credit_hold) {
                $result->payment = 0;
            } else {
                $result->payment = ($result->down_payment || ($result->credit >= $result->total / 2)) ? 1 : 0;
            }
        }
        //prd($results);
        return $results;
    }

    public function get_window_product_count($order_id) {
        $rows = $this->db
            ->select('(SELECT sum(quantity) FROM subitems WHERE item_id=orders_has_item.item_id AND deleted=0) AS count', false)
            ->where('orders_has_item.order_id', $order_id)
            ->where('orders_has_item.deleted', 0)
            ->get('orders_has_item')->result();
        $total = 0;
        foreach ($rows as $row) {
            $total += 1 + $row->count;
        }
        return $total;
    }

    public function getApprovedList($flag = 0) {
        $this->changemode($flag, $this->_final);
        $status_code_id = $this->status_id(350);
        $query = "SELECT    
                        orders.id AS id,
                        orders.order_number as name,
                        CONCAT(users.first_name,' ',users.last_name) AS customer,
                        groups.name As company,
                        DATE_FORMAT(orders.created,'%m/%d/%Y') as created,
                        0 as pcode,
                       DATE_FORMAT(orderstatus.status_changed ,'%m/%d/%Y') as schanged
                    FROM orders
                    LEFT JOIN orders_has_customers ON orders_has_customers.order_id=orders.id AND orders_has_customers.primary = 1
                    LEFT JOIN users ON users.id = orders_has_customers.customer_id
                    LEFT JOIN groups ON groups.id=orders.dealer_id
                    INNER JOIN orders_status_codes as orderstatus ON orderstatus.order_id=orders.id AND order_status_code_id = $status_code_id 
                    WHERE status_code = $status_code_id AND orders.deleted=0
                    Group BY orders.id ORDER BY orders.created
                     
                    ";
        $query.=$this->_final;
        $results = $this->db->query($query)->result();
        foreach ($results as $result) {
            $result->pcode = $this->get_window_product_count($result->id);
        }

        return $results;
    }

    public function get_orders_in_status($status_code, $apply_limit = false, $include_product_count = false) {
        $status_code_id = $this->status_id($status_code);
        $this->db
            ->select("
                orders.id,
                CONCAT(users.first_name,' ',users.last_name) AS customer,
                ( SELECT DATE_FORMAT(status_changed,'%m/%d/%Y') FROM orders_status_codes WHERE order_id=orders.id AND order_status_code_id = $status_code_id ORDER BY status_changed DESC LIMIT 1) as status_date,
                ( SELECT DATE_FORMAT(status_changed,'%m/%d/%Y') FROM orders_status_codes JOIN status_codes ON order_status_code_id=status_codes.id WHERE order_id=orders.id AND code = 300 ORDER BY status_changed DESC LIMIT 1) as submitted_date,
                CONCAT(creator.first_name,' ',creator.last_name) AS creator,
                groups.name As company,
				groups.credit,
				groups.credit_hold,
				total,
				(SELECT count(*) FROM payments
							JOIN payment_types ON payment_type_id=payment_types.id
							WHERE deleted = 0 AND order_id=orders.id AND payment_types.name LIKE '%down%'
					) AS down_payment
            ", false)
            ->from('orders')
            ->join('orders_has_customers', 'orders_has_customers.order_id=orders.id AND orders_has_customers.primary = 1')
            ->join('users', 'users.id = orders_has_customers.customer_id')
            ->join('users creator', 'creator.id = orders.created_by')
            ->join('groups', 'groups.id=orders.dealer_id', 'left')
            ->where('orders.status_code', $status_code_id)
            ->where('orders.deleted', 0);
        if ($apply_limit) {
            $this->db->limit($this->_limit_num);
        }
        $results = $this->db->get()->result();
		foreach ($results as $result) {
            if ($result->credit_hold) {
                $result->payment = 0;
            } else {
                $result->payment = ($result->down_payment || ($result->credit >= $result->total / 2)) ? 1 : 0;
            }
        }
        if ($include_product_count) {
            foreach ($results as $result) {
                $result->pcount = $this->get_window_product_count($result->id);
            }
        }
        return $results;
    }

    public function getPlanningData() {
        $query = "SELECT    
                        orders.id AS id,
                        orders.order_number As number,
                        groups.name As company,
                        statuscodes.code as code,
                        (SELECT DATE_FORMAT(MAX(status_changed), '%m/%d/%Y') FROM orders_status_codes WHERE order_id=orders.id) as statuschanged,
                        DATE(build_date) AS build_date,
                        COUNT(orderhasitem.order_id)  as panel,
                        statuscodes.description as status_name,
                        CONCAT(product_types.max_width,' * ',product_types.max_height) as sqftpanel,
                        CONCAT(sites.address,' ',sites.address_ext,' ',sites.state,' ',sites.zipcode) as address,
                        orders.expedite,
                         
                        IF(orders.commit_date IS NULL,'',DATE_FORMAT(orders.commit_date,'%Y-%m-%d')) As commit_date,
                        orders.expedite as expedite,
                        CONCAT(users.first_name, ' ', users.last_name) As customer,
                        materials_ordered
                    FROM orders
                    LEFT  JOIN groups ON groups.id=orders.dealer_id
                    INNER JOIN status_codes as statuscodes ON orders.status_code=statuscodes.id
                    INNER JOIN sites ON sites.id=orders.site_id
                    INNER JOIN orders_has_item as orderhasitem ON orderhasitem.order_id=orders.id
                    INNER JOIN items ON orderhasitem.item_id=items.id
                    
                    INNER JOIN product_types ON items.product_types_id=product_types.id
                    INNER JOIN products ON product_types.product_id=products.id
                    INNER JOIN users ON orders.created_by=users.id
                    WHERE orderhasitem.deleted = 0 AND code >= 300 AND code <= 680 AND orders.deleted=0
                   
                     Group by orders.id
                   
                    ";
        $results = $this->db->query($query)->result();
        foreach ($results as $result) {
            $result->sqftpanel = $this->order_model->get_total_sqft($result->id);
        }

        return $results;
    }

    public function getManufactureOrders() {
        /*
        $sql = "SELECT  
                        orders.id as id,
                        orders.order_number as order_number,
                        DATE_FORMAT(orders.build_date ,'%m/%d/%Y') as created,
                        DATE_FORMAT(orders.commit_date ,'%m/%d/%Y') as commit_date,
                        COUNT(orders_has_item.order_id) as sq_ft
                    FROM orders 
                    INNER JOIN orders_status_codes ON orders.id = orders_status_codes.order_id
                    INNER JOIN status_codes ON status_codes.id= orders_status_codes.order_status_code_id
                    INNER JOIN orders_has_item ON orders_has_item.order_id=orders.id
                    WHERE status_codes.code =500
                    GROUP BY orders.id";
                  

        $orders = $this->db->query($sql)->result();
        */
        $orders = $this->db
            ->select("
                        orders.id as id,
                        orders.expedite,
						code as status,
                        orders.order_number as order_number,
                        DATE_FORMAT(orders.build_date ,'%m/%d/%Y') as created,
                        DATE_FORMAT(orders.build_date ,'%Y-%m-%d') as created_c,
                        DATE_FORMAT(orders.commit_date ,'%m/%d/%Y') as commit_date,
                        DATE_FORMAT(orders.commit_date ,'%Y-%m-%d') as commit_date_c"
            , false)
            ->join('status_codes', 'status_codes.id=orders.status_code')
            ->where_in('status_codes.code', array(400, 500))
            ->where('orders.deleted', 0)
            ->get('orders')->result();
        $orders_assoc = array();
        foreach ($orders as $order) {
            $order->calculations = true;
            $order->items = array();
            $orders_assoc[$order->id] = $order;
            $order->sq_ft = $this->order_model->get_total_sqft($order->id);
        }
        $order_ids = array_keys($orders_assoc);
        if (!count($order_ids)) {
            return array();
        }
        $items = $this->db
                ->select("
                    items.*,
                    orders_has_item.order_id AS r_order_id,
                    product_type,
                    product,
                    (SELECT count(*) FROM subitems JOIN product_types ON product_types.id=product_type_id WHERE product_type='Storage Sleeves' AND item_id=items.id AND subitems.deleted=0) AS sleeves,
                    cut_calculations.id AS calcs
                ", false)
                ->join('items', 'items.id=orders_has_item.item_id')
                ->join('product_types', 'product_types.id=product_types_id')
                ->join('products', 'products.id=product_types.product_id')
                ->join('cut_calculations', 'cut_calculations.item_id=items.id AND cut_calculations.status=1 AND cut_calculations.deleted=0', 'left')
                ->where_in('orders_has_item.order_id', $order_ids)
                ->where('orders_has_item.deleted', 0)
                ->where('items.manufacturing_status', 1)
                ->where('product_types.product_id <>', 3)
                ->group_by('items.id')
                ->get('orders_has_item')->result();
        foreach ($items as $item) {
            $order = $orders_assoc[$item->r_order_id];
            $orders_assoc[$item->r_order_id]->items[] = $item;
            $order->calculations = $order->calculations && $item->calcs;
        }

        return $orders;
    }

    public function order_related_items($order_id) {

        $sql = "SELECT item.room,CONCAT(sites.address,', ',sites.city,' ,',sites.state,' ',sites.zipcode) address,pro.product,pro_type.product_type
                FROM items AS item
                INNER JOIN orders_has_item AS ord_item ON ord_item.item_id = item.id
                INNER JOIN product_types AS pro_type ON pro_type.id = item.product_types_id
                INNER JOIN sites ON sites.id = item.site_id
                INNER JOIN products AS pro ON pro.id = pro_type.product_id
                WHERE ord_item.order_id = ?";
        $result = $this->db->query($sql, $order_id)->result();
        return $result;
    }

    public function getdownloaddata($order_id) {
        $sql = "SELECT 
                orders.id as id, 
                 CONCAT(users.first_name, ' ', users.last_name) as customer,
                 orders.group_id as group_id_company ,
                 'TBD' as TBD,
                 items.id as unit_id,
                 CONCAT(products.product,' / ',product_types.product_type) as product,
                 rooms.name as room_name,
                 items.location as Window_Location,
                 measurements.data_set as data_set,
                 CONCAT(orders.id, '-', items.id) as subject_change,
                 items.acrylic_panel_size as panel_size,
                 items.acrylic_panel_sq_ft as panel_sq,
                 items.acrylic_panel_linear_ft as panel_linear,
                 items.acrylic_panel_thickness as thick,
                 'TBD' as unit_retail,
                 items.top_spine as top,
                 items.side_spines as side,
                 items.frame_depth as depth,
                 CONCAT(sites.address, ' ', sites.address_ext) as address,
                 users.phone_1 as ph,
                  users.email_1 as email,
                  groups.name as gname,
                 CONCAT(group_addresses.addressnum,' ',group_addresses.address_type) as addr,
                 CONCAT(group_addresses.address,' ',group_addresses.address_ext) as add1,
                 'TBD' as TBD1,
                 'TBD' as TBD2
                 
              FROM orders
              INNER JOIN users ON orders.created_by=users.id
              INNER JOIN groups ON groups.id=orders.group_id
              INNER JOIN group_addresses ON groups.id=group_addresses.group_id
              INNER JOIN orders_has_item as orderhasitem ON orderhasitem.order_id=orders.id
              INNER JOIN items ON orderhasitem.item_id=items.id
              INNER JOIN product_types ON items.product_types_id=product_types.id
              INNER JOIN products ON product_types.product_id=products.id
              INNER JOIN sites ON orders.site_id= sites.id
              INNER JOIN rooms_items ON rooms_items.item_id=items.id
              INNER JOIN rooms ON rooms_items.room_id=rooms.id
              INNER JOIN items_measurements ON items_measurements.item_id=items.id
              INNER JOIN measurements ON measurements.id=items_measurements.measurement_id
              WHERE orders.id= ? ";
        $result = $this->db->query($sql, $order_id)->result_array();

        return $result;
    }

    public function shipments_orders() {
        $sql = "SELECT 
                orders.id as id,
                CONCAT(users.first_name,' ',users.last_name) as name,
                CONCAT(site.address,', ',site.city,' ,',site.state,' ',site.zipcode) address,
                status_codes.code as status,
                DATE_FORMAT(orders.build_date ,'%m/%d/%Y') as build_date,
                DATE_FORMAT(orders.commit_date ,'%m/%d/%Y') as commit_date,
                (SELECT COUNT(*) FROM orders_has_item WHERE order_id=orders.id AND deleted=0) as panel,
                ship_method,
                orders.expedite,
                shipping_address_id,
                dealer_shipping_address_id,
                groups.name as dealer,
				tracking_num,
                carrier
                FROM orders
                JOIN status_codes ON status_codes.id = orders.status_code
                JOIN orders_has_customers ON orders_has_customers.order_id = orders.id AND orders_has_customers.primary = 1
                JOIN users ON users.id=orders_has_customers.customer_id
                JOIN sites as site ON site.id = orders.site_id
                LEFT JOIN groups ON groups.id=orders.dealer_id
                WHERE status_codes.code BETWEEN 350 AND 680 
                AND not exists(select null from orders_combined as oc where oc.order_id = orders.id)
                AND orders.deleted = 0
                GROUP BY orders.id
                ORDER BY orders.commit_date, groups.name
                ;
        ";
        $result = $this->db->query($sql)->result();
        foreach ($result as $order) {
            $address = $this->order_model->get_shipping_address($order);
            if ($address) {
                $order->address = $address->address . ' ' . $address->address_ext . ', ' . $address->city . ', ' . $address->state . ' ' . $address->zipcode;
            } else {
                $order->address = '';
            }
            $order->ship_fee = $this->order_model->get_shipping_fee($order->id);
        }
        return $result;
    }
    
    public function packaging_orders() {
        $sql = "SELECT 
                orders.id as id,
                CONCAT(site.address,', ',site.city,' ,',site.state,' ',site.zipcode) address,
                status_codes.code as status,
                DATE_FORMAT(orders.build_date ,'%m/%d/%Y') as build_date,
                DATE_FORMAT(orders.commit_date ,'%m/%d/%Y') as commit_date,
                groups.name AS dealer,
                (SELECT COUNT(*) FROM orders_has_item WHERE order_id=orders.id AND deleted=0) as panel,
                ship_method,
                orders.expedite,
                carrier
                FROM orders
                INNER JOIN status_codes ON status_codes.id = orders.status_code AND status_codes.code >= 600 AND status_codes.code <= 680
                INNER JOIN orders_has_customers ON orders_has_customers.order_id = orders.id AND orders_has_customers.primary = 1
                INNER JOIN users ON users.id=orders_has_customers.customer_id
                INNER JOIN sites AS site ON site.id = orders.site_id 
                INNER JOIN groups on orders.dealer_id = groups.id
                WHERE not exists(select null from orders_combined as oc where oc.order_id = orders.id)
                AND orders.deleted = 0
                GROUP BY orders.id
                ORDER BY orders.commit_date
                ;
            ";
        $result = $this->db->query($sql)->result();
        return $result;
    }
    
    public function sleeve_cut_list($order_id)
    {
        $sql = "
            SELECT
            items.id as item_id,
            items.room,
            items.location,
            items.width,
            items.height,
            '' AS checkbox
            FROM items
            INNER JOIN orders_has_item ON orders_has_item.item_id = items.id
            WHERE orders_has_item.order_id = ?
            ;
        ";
        $result = $this->db->query($sql, $order_id)->result();
        return $result;
    }

    public function combined_orders($status_min=0, $status_max=9000) {
            //$sql = "SELECT ocb.id,ocb.status,ocb.build_date,ocb.commit_date,ocb.total_panels,ocb.ship_method,ocb.carrier FROM orders_combined_batch as ocb INNER JOIN orders_combined ON orders_combined.batch_id = ocb.id";
        $sql = "
            SELECT 
                ocb.id, 
                ocb.status, 
                ocb.total_panels,
                ocb.ship_method,
                ocb.carrier 
            FROM orders_combined_batch as ocb 
            INNER JOIN orders_combined ON orders_combined.batch_id = ocb.id
            JOIN orders ON orders.id=orders_combined.order_id AND orders.status_code IN (SELECT id FROM status_codes WHERE code BETWEEN $status_min AND $status_max)
            GROUP BY ocb.id;
            ;
        ";

        $result = $this->db->query($sql)->result_array();
        foreach ($result as &$row) {
            $order_sql = "SELECT CONCAT(first_name, ' ', last_name) AS name,
                    orders.id,
                    (SELECT CONCAT(address, ' ', address_ext, ', ', city, ', ', state, ' ', zipcode) FROM user_addresses WHERE id=orders.dealer_shipping_address_id) AS dealer_shipping,
                    (SELECT CONCAT(address, ' ', address_ext, ', ', city, ', ', state, ' ', zipcode) FROM user_addresses WHERE id=orders.shipping_address_id) AS shipping,
                    (SELECT count(*) FROM orders_has_item WHERE order_id=orders.id AND deleted=0) AS panels,
                    carrier,
                    DATE_FORMAT(build_date ,'%m/%d/%Y') as build_date, 
                    DATE_FORMAT(commit_date ,'%m/%d/%Y') as commit_date,
                    groups.name as dealer,
                    ship_method,
					tracking_num,
                    status_codes.code AS status
                    FROM orders_combined
                    JOIN orders ON orders_combined.order_id = orders.id
                    JOIN orders_has_customers ON orders_has_customers.order_id = orders.id AND orders_has_customers.primary = 1
                    JOIN users ON customer_id=users.id
                    JOIN groups ON groups.id=orders.dealer_id
                    JOIN status_codes ON status_codes.id = orders.status_code
                    WHERE batch_id=?
            ";
            $orders = $this->db->query($order_sql, $row['id'])->result();
            $names = array();
            $addresses = array();
            $methods = array();
            $carriers = array();
            $dealers = array();
            $build_dates = array();
            $commit_dates = array();
            $panels = 0;
            $status = 10000;
            $total_ship = 0;
			$tracking_nums = array();
            foreach ($orders as $order) {
                $total_ship += $this->order_model->get_shipping_fee($order->id);
                $carriers[$order->carrier] = $order->carrier;
                $methods[$order->ship_method] = $order->ship_method;
                $address = $order->dealer_shipping ? $order->dealer_shipping : $order->shipping;
                $names[$order->name] = $order->name;
                $build_dates[$order->build_date] = $order->build_date;
                $commit_dates[$order->commit_date] = $order->commit_date;
                $dealers[$order->dealer] = $order->dealer;
                $addresses[$address] = $address;
                $panels += $order->panels;
                $status = min($status, $order->status);
				if ($order->tracking_num) {
					$tracking_nums[] = $order->tracking_num;
				}
            }
            $row['status'] = $status == 10000 ? '' : $status;
            $row['name'] = count($names) == 1 ? reset($names) : '_________';
            $row['address'] = count($addresses) == 1 ? reset($addresses) : '_________';
            $row['build_date'] = count($build_dates) == 1 ? reset($build_dates) : '_________';
            $row['commit_date'] = count($commit_dates) == 1 ? reset($commit_dates) : '_________';
			$row['tracking_num'] = count($tracking_nums) ? reset($tracking_nums) : '';
            $row['ship_method'] = reset($methods);
            $row['carrier'] = reset($carriers);
            $row['dealer'] = reset($dealers);
            $row['panel'] = $panels;
            $row['combined'] = 1;
            $row['ship_fee'] = $total_ship;
        }
        return $result;
    }

    protected function get_dimension_totals($order, $items) {
        $max_string = null;
        $max_num = 0;
        $total_sqft = 0;
        $total_weight = 0;
        foreach($items as $item) {
            $a = empty($item->measurements['A']) ? 0 : $item->measurements['A'];
            $b = empty($item->measurements['B']) ? 0 : $item->measurements['B'];
            $c = empty($item->measurements['C']) ? 0 : $item->measurements['C'];
            $d = empty($item->measurements['D']) ? 0 : $item->measurements['D'];
            $width = max(ceil($a), ceil($b));
            $height = max(ceil($c), ceil($d));
            $sqft = $width * $height;
            if ($sqft > $max_num) {
                $max_num = $sqft;
                $total_sqft += $sqft;
                $max_string = ($width + 1) . 'x' . ($height + 1);
            }
            $item->weight = $this->item_model->get_weight($item->product_types_id, $sqft / 144);
            $total_weight += $item->weight;
        }
        $order->dimension = $max_string;
        $order->sqft = $max_num / 144;
        $order->total_sqft = $total_sqft;
        $order->total_weight = $total_weight;
    }
    
    public function search_orders($searchText) {
        $searchText = urldecode($searchText);
        $sql = "SELECT
                grp.id as group_id,
                ord.id as id,
                ord.build_date as build_date,
                CONCAT(user.first_name,' ',user.last_name) as name,
                dealer_shipping_address_id,
                shipping_address_id,
                status_codes.code as status,
                DATE_FORMAT(ord.build_date ,'%m/%d/%Y') as build_date,
                DATE_FORMAT(ord.commit_date ,'%m/%d/%Y') as commit_date,
                (SELECT count(*) FROM orders_has_item WHERE order_id=ord.id AND deleted=0) as panel
                FROM orders as ord 
                INNER JOIN status_codes ON status_codes.id= ord.status_code
                INNER JOIN orders_has_customers ON orders_has_customers.order_id = ord.id AND orders_has_customers.primary = 1
                INNER JOIN users as user ON user.id = orders_has_customers.customer_id
                INNER JOIN sites as site ON site.id = ord.site_id
                INNER JOIN groups as grp ON grp.id = ord.dealer_id
                WHERE grp.name = ? AND (status_codes.code BETWEEN 300 AND 600) AND not exists(select null from orders_combined as oc where oc.order_id = ord.id) AND ord.deleted=0
                GROUP BY ord.id
                ORDER BY ord.commit_date DESC";
                $result = $this->db->query($sql, $searchText)->result();
       

        /* Filter result for largest dimension */
        foreach ($result as &$value){
            $sql2 = "SELECT items.id, product_types_id FROM orders_has_item JOIN items ON orders_has_item.item_id=items.id WHERE orders_has_item.order_id=? AND orders_has_item.deleted=0";
            $items = $this->db->query($sql2, $value->id)->result();
            $this->item_model->attach_measurements($items);
            $address = $this->order_model->get_shipping_address($value);
            if ($address) {
                $value->address = $address->address . ' ' . $address->address_ext . ', ' . $address->city . ', ' . $address->state . ' ' . $address->zipcode;
            } else {
                $value->address = '';
            }
            $this->get_dimension_totals($value, $items);
        }
        /*END FILTERING*/

        return $result;
    }
    
    public function search_batch_orders($batch_id){
        
        $sql = "SELECT
                ord_comb.id as combine_id,
                grp.id as group_id,
                ord.id as id,
                ord.build_date as build_date,
                CONCAT(users.first_name,' ',users.last_name) as name,
                status_codes.code as status,
                dealer_shipping_address_id,
                shipping_address_id,
                DATE_FORMAT(ord.build_date ,'%m/%d/%Y') as build_date,
                DATE_FORMAT(ord.commit_date ,'%m/%d/%Y') as commit_date,
                (SELECT count(*) FROM orders_has_item WHERE order_id=ord.id AND deleted=0) as panel
                FROM orders as ord 
                INNER JOIN status_codes ON status_codes.id= ord.status_code
                JOIN orders_has_customers ON orders_has_customers.order_id=ord.id AND orders_has_customers.primary=1
                JOIN users ON users.id=orders_has_customers.customer_id
                INNER JOIN sites as site ON site.id = ord.site_id
                INNER JOIN groups as grp ON grp.id = ord.dealer_id
                INNER JOIN orders_combined as ord_comb ON ord_comb.order_id = ord.id
                INNER JOIN orders_combined_batch as ocb ON ocb.id = ord_comb.batch_id
                WHERE ocb.id = ?
                GROUP BY ord.id
                ORDER BY ord.commit_date DESC";
       
        $result = $this->db->query($sql, $batch_id)->result();

        /* Filter result for largest dimension */
        foreach ($result as &$value){
            $address = $this->order_model->get_shipping_address($value);
            if ($address) {
                $value->address = $address->address . ' ' . $address->address_ext . ', ' . $address->city . ', ' . $address->state . ' ' . $address->zipcode;
            } else {
                $value->address = '';
            }
            $sql2 = "SELECT item_id AS id, product_types_id FROM orders_has_item JOIN items ON items.id=orders_has_item.item_id WHERE orders_has_item.order_id=? AND orders_has_item.deleted=0";
            $items = $this->db->query($sql2, $value->id)->result();
            $this->item_model->attach_measurements($items);
            $this->get_dimension_totals($value, $items);
        }
        /*END FILTERING*/

        return $result;
        
    }
    public function find_max($arr , $field){
        
        $max = $arr[0][$field];
        $max_array = $arr[0];
        foreach($arr as $key=> $val){
            if($val[$field] > $max){
                $max = $val[$field];
                $max_array = $val;
            }
        }
        return $max_array;
        
    }
    public function getAllDealers($search_text) {
        $search_text = $this->db->escape_str($search_text);

        $sql = "SELECT * FROM groups where name LIKE '%{$search_text}%' ORDER BY name LIMIT 15";
        $query = $this->db->query($sql);
        $html = "<div>";
        if (!empty($query) && $query->num_rows() > 0) {
            $results = $query->result();
            foreach ($results as $row) {
                $html .= "<a href='#' class='select-users' dealer='" . $row->id . "'>" . $row->name . "</a><br>";
            }
        } else {
            $html .= 'No results.';
        }
        $html .="</div>";
        return $html;
    }

    public function save_combined_order($data, $group_id) {
        if(isset($data['check-all'])){
            unset($data['check-all']);
        }
        unset($data['dealer_id']);
        
        $ord_ids = array();
        $combined_orders = array();
        $now = date('Y-m-d H:i:s');
        foreach ($data as $key => $value) {
            $sql = "SELECT * FROM orders_combined WHERE group_id = {$group_id} AND order_id = {$key}";
            $res = $this->db->query($sql)->result();
            if (empty($res)) {
                $ord_ids[] = $key;
                $combined_orders[] = array('order_id' => $key, 'group_id' => $group_id);
            }
        }
        if (!empty($combined_orders)) {
           
            $ord = implode(',', $ord_ids);
            $sql_ord = "SELECT MAX(orders.commit_date) as commit_date, 
                               MAX(orders.build_date) as build_date, 
                               MIN(status_codes.code) as status,
                               COUNT(orderhasitem.order_id) as total_panels
                               FROM `orders`
                               INNER JOIN status_codes ON status_codes.id = orders.status_code
                               INNER JOIN orders_has_item as orderhasitem ON orderhasitem.order_id = orders.id
                               WHERE orders.id IN({$ord})";
                               
            $batch_data = $this->db->query($sql_ord)->result_array();
            /*Insert MAX MIN date and status for one combined ship batch*/
            $this->db->insert('orders_combined_batch', array('created' => $now, 
                                                             'updated' => $now, 
                                                             'status' => $batch_data[0]['status'],
                                                             'build_date' => $batch_data[0]['build_date'], 
                                                             'commit_date' => $batch_data[0]['commit_date'],
                                                             'ship_method' => 'Air',
                                                             'carrier' => 'DHL',
                                                             'total_panels' => $batch_data[0]['total_panels']
                                                            ));
            $batch_id = $this->db->insert_id();
            $batch_id = $this->db->insert_id();
            foreach ($combined_orders as &$value){
                    $value['batch_id'] = $batch_id;
            }
            
            $ret = $this->db->insert_batch('orders_combined', $combined_orders);
        }

        return $batch_id;
    }

    public function search_combined_orders($group_id) {
        $sql = "SELECT
                grp.id as group_id,
                ord.id as id,
                ord.build_date as build_date,
                CONCAT(user.first_name,' ',user.last_name) as name,
                CONCAT(site.address,', ',site.city,' ,',site.state,' ',site.zipcode) address,
                status_codes.code as status,
                DATE_FORMAT(ord.build_date ,'%m/%d/%Y') as build_date,
                DATE_FORMAT(ord.commit_date ,'%m/%d/%Y') as commit_date,
                COUNT(orderhasitem.order_id) as panel,
                CONCAT(item.width,' X ', item.height) as dimension
                FROM orders as ord 
                INNER JOIN orders_status_codes ON ord.id = orders_status_codes.order_id
                INNER JOIN status_codes ON status_codes.id= orders_status_codes.order_status_code_id
                INNER JOIN users as user ON user.id = ord.customer_user_id
                INNER JOIN sites as site ON site.id = ord.site_id
                INNER JOIN orders_has_item as orderhasitem ON orderhasitem.order_id=ord.id
                INNER JOIN groups as grp ON grp.id = ord.group_id
                INNER JOIN items as item ON item.id = orderhasitem.item_id
                INNER JOIN orders_combined as ord_comb ON ord_comb.order_id = ord.id
                WHERE ord_comb.group_id = 1 AND (status_codes.code =500 OR status_codes.code =600) 
                GROUP BY ord.id
                ORDER BY ord.commit_date DESC";
        $result = $this->db->query($sql, $group_id)->result();
        return $result;
    }
    public function delete_combine_order($combine_order_ids = array()){
        foreach($combine_order_ids as $key => $val){
            $this->db->delete('orders_combined', array('id' => $val));
        }
        return true;
    }

}
