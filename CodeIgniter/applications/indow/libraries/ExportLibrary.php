<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    class ExportLibrary {
        public function __construct() {
            $this->ci =& get_instance();
            $this->export_type = null;
        }

        public function export_csv($ids, $type='planning', $get_by='orders') {
            $this->export_type = $type;

            if (gettype($ids) !== 'array') { //allow a single id to be passed
                $ids = array($ids);
            }

            if ($get_by == 'items') {
                $order_ids = $this->get_orders_from_items($ids);
                $filter = $ids;
            } else {
                $order_ids = $ids;
                $filter = null;
            }

            $all_rows = array();
            foreach ($order_ids as $order_id) {
                $all_rows = array_merge($all_rows, $this->get_order_rows($order_id, $filter));
            }
            $this->create_csv($all_rows);
        }

        protected function csv_headers($filename = null, $force_download = true) {
            if (!$filename) {
                $filename = 'orders_' . date('Y_m_d') . '.csv';
            }
            if ($force_download) {
                header("Content-Type: application/force-download");
            } else {
                header("Content-Type: text/csv");
            }
            header("Content-Disposition: attachment; filename=" . $filename);
            // Disable caching
            header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
            header("Pragma: no-cache"); // HTTP 1.0
            header("Expires: 0"); // Proxies
            
            
            $output = fopen("php://output", "w");
        }

        public function create_csv($rows, $filename = null) {
            //prd($rows);
            $this->csv_headers($filename);
            $output = fopen("php://output", "w");
            if (count($rows)) {
                fputcsv($output, array_keys($rows[0]));
                foreach($rows as $row) {
                    fputcsv($output, array_values($row));
                }
            } else {
                fputcsv($output, array('No Results'));
            }
            fclose($output);
        }

        protected function get_order_rows($order_id, $filter) {
            $order = $this->get_order($order_id);
            $order_items = $this->get_order_items($order_id);
            $rows = array();
            foreach ($order_items as $item) {
                if ($this->export_type === 'planning' && (!$filter || in_array($item->id, $filter))) {
                    $rows[] = $this->get_planning_row($order, $item);
                }
            }
            return $rows;
        }

        protected function get_planning_row(&$order, &$item) {
            return array(
                'Product Serial #'      => $item->id,
                'Order #'               => $order->order_number . '-' . $order->order_number_type_sequence,
                'Item #'                => $item->unit_num,
                'Room Name'             => $item->room,
                'Window Location'       => $item->location,
                'Window Notes'          => $item->notes,
                'Product'               => $item->product,
                'Product Type'          => $item->product_type,
                'Tubing'                => $item->edging,
                'Width Top'             => @$item->measurements['A'],
                'Width Bottom'          => @$item->measurements['B'],
                'Height Left'           => @$item->measurements['C'],
                'Height Right'          => @$item->measurements['D'],
                'Diagonal Left'         => @$item->measurements['E'],
                'Diagonal Right'        => @$item->measurements['F'],
                'Panel Thickness'       => $item->acrylic_panel_thickness,
                'Order Date'            => $order->order_date,
                'Sq Ft of Panel'        => $item->panel_sqft,
                'Linear ft of panel'    => $item->panel_linear_feet,
                'Top spines'            => $item->top_spine ? '1' : '0',
                'Side spines'           => $item->side_spines ? '1' : '0',
                'Frame Depth'           => $item->frame_depth,
                'Drafty Window'         => $item->drafty,
                'Customer First Name'   => $order->first_name,
                'Customer Last Name'    => $order->last_name,
                'Job Site Address'      => $order->site_address,
                'Job Site Address Ext'  => $order->site_address_ext,
                'Customer Phone 1'      => $order->phone_1,
                'Customer Phone 2'      => $order->phone_2,
                'Customer Email 1'      => $order->email_1,
                'Customer Email 2'      => $order->email_2,
                'Job Site City'         => $order->site_city,
                'Job Site State'        => $order->site_state,
                'Job Site Zip'          => $order->site_zip,
                'Dealer'                => $order->dealer,
                'Group Address'         => $order->group_address,
                'Group Address Ext'     => $order->group_address_ext,
                'Group City'            => $order->group_city,
                'Group State'           => $order->group_state,
                'Group Zip'             => $order->group_zip,
                'Primary User\'s First Name' => $order->creator_first_name,
                'Primary User\'s Last Name' => $order->creator_last_name,
                'Primary User\'s Email' => $order->creator_email,
                'Primary User\'s Phone' => $order->creator_phone,
                'Dealer PO#'            => $order->po_num,
                'Dim W'                 => $item->sheet_width,
                'Dim H'                 => $item->sheet_height,
                'MSRP'                  => $item->price
            );
        }

        protected function get_order($order_id) {
            $order = $this->ci->db
                    ->select("
                            orders.*,
                            CONCAT(customer.first_name, ' ', customer.last_name) AS customer_name,
                            customer.first_name,
                            customer.last_name,
                            CONCAT(users.first_name, ' ', users.last_name) AS creator_name,
                            users.first_name AS creator_first_name,
                            users.last_name AS creator_last_name,
                            groups.name as dealer,
                            CONCAT(sites.address, ' ', sites.address_ext, ', ', sites.city, ', ', sites.state, ' ', sites.zipcode) AS site_address,
                            sites.address AS site_address,
                            sites.address_ext AS site_address_ext,
                            sites.city AS site_city,
                            sites.state AS site_state,
                            sites.zipcode AS site_zip,
                            group_addresses.address AS group_address,
                            group_addresses.address_ext AS group_address_ext,
                            group_addresses.city AS group_city,
                            group_addresses.state AS group_state,
                            group_addresses.zipcode AS group_zip,
                            customer.phone_1,
                            customer.phone_2,
                            customer.email_1,
                            customer.email_2,
                            IF(customer.email_1 <> '', customer.email_1, customer.email_2) AS customer_email,
                            IF(users.email_1 <> '', users.email_1, users.email_2) AS creator_email,
                            IF(customer.phone_1 <> '', customer.phone_1, customer.phone_2) AS customer_phone,
                            IF(users.phone_1 <> '', users.phone_1, users.phone_2) AS creator_phone,
                            (SELECT CONCAT(address, ' ', address_ext, ', ', city, ', ', state, ' ', zipcode) FROM group_addresses WHERE group_id=orders.dealer_id AND address_type='Shipping' LIMIT 1) as dealer_shipping_address,
                            (SELECT CONCAT(address, ' ', address_ext, ', ', city, ', ', state, ' ', zipcode) FROM group_addresses WHERE group_id=orders.dealer_id AND address_type='Billing' LIMIT 1) as dealer_billing_address,
                            (SELECT status_changed FROM orders_status_codes JOIN status_codes ON status_codes.id=order_status_code_id WHERE code=350 AND order_id=orders.id ORDER BY orders_status_codes.id DESC LIMIT 1) as order_date
                    ", false)
                    ->join('orders_has_customers', 'orders_has_customers.order_id=orders.id', 'left')
                    ->join('users customer', 'customer.id=orders_has_customers.customer_id', 'left')
                    ->join('users', 'users.id=orders.created_by', 'left')
                    ->join('groups', 'groups.id=orders.dealer_id', 'left')
                    ->join('group_addresses', 'group_addresses.group_id=orders.dealer_id AND group_addresses.addressnum=1', 'left')
                    ->join('sites', 'sites.id=orders.site_id', 'left')
                    ->where('orders.id', $order_id)
                    ->get('orders')->row();
            return $order;
        }

        protected function get_order_items($order_id) {
            $items = $this->ci->db
                ->select("
                        items.*,
                        product_types.product_type,
                        products.product,
                        sheet_width,
                        sheet_height,
                        frame_depth.name AS frame_depth,
                        edging.name AS edging
                ")
                ->join('items', 'items.id = orders_has_item.item_id')
                ->join('product_types', 'product_types.id=items.product_types_id')
                ->join('products', 'product_types.product_id=products.id')
                ->join('cut_calculations', 'cut_calculations.item_id=items.id AND cut_calculations.deleted=0', 'left')
                ->join('edging', 'items.edging_id=edging.id', 'left')
                ->join('frame_depth', 'frame_depth_id=frame_depth.id', 'left')
                ->where('orders_has_item.order_id', $order_id)
                ->where('items.manufacturing_status', 1)
                ->where('orders_has_item.deleted', 0)
                ->get('orders_has_item')->result();
            $this->ci->item_model->attach_measurements($items);
            foreach ($items as $item) {
                $item->acrylic_panel_size = (!empty($item->sheet_width) || !empty($item->sheet_height)) ? $item->sheet_width . ' x ' . $item->sheet_height : 'N/A';
                $a = !empty($item->measurements['A']) ? $item->measurements['A'] : 0;
                $b = !empty($item->measurements['B']) ? $item->measurements['B'] : 0;
                $c = !empty($item->measurements['C']) ? $item->measurements['C'] : 0;
                $d = !empty($item->measurements['D']) ? $item->measurements['D'] : 0;
                $width = max($a,$b);
                $height = max($c, $d);
                $item->panel_sqft = round($width * $height / 144, 2);
                $item->panel_linear_feet = round(($a + $b + $c + $d) / 12, 2);
                $item->panel_sqft = $item->panel_sqft ? round($item->panel_sqft, 2) : 'N/A';
            }
            return $items;
        }

        protected function get_orders_from_items($item_ids) {
            $order_ids = array();
            if (!count($item_ids)) {
                return array();
            }
            $rows = $this->ci->db->select('DISTINCT order_id', false)->where_in('item_id', $item_ids)->get('orders_has_item')->result();
            foreach ($rows as $row) {
                $order_ids[] = $row->order_id;
            }
            return $order_ids;
        }

        public function get_order_report_data($code_from, $code_to, $dealer, $date_filter, $date_from, $date_to) {
            $db = &$this->ci->db;
            $db
                    ->select("
                        orders.id,
                        (SELECT DATE_FORMAT(status_changed, '%m/%d/%Y') FROM orders_status_codes JOIN status_codes ON order_status_code_id=status_codes.id WHERE order_id=orders.id AND code=300 ORDER BY orders_status_codes.id DESC LIMIT 1) AS order_date,
                        (SELECT DATE_FORMAT(status_changed, '%m/%d/%Y') FROM orders_status_codes JOIN status_codes ON order_status_code_id=status_codes.id WHERE order_id=orders.id AND code=320 ORDER BY orders_status_codes.id DESC LIMIT 1) AS oc_sent,
                        (SELECT DATE_FORMAT(status_changed, '%m/%d/%Y') FROM orders_status_codes WHERE order_id=orders.id AND order_status_code_id=orders.status_code ORDER BY orders_status_codes.id DESC LIMIT 1) AS status_date,
                        DATE_FORMAT(orders.commit_date, '%m/%d/%Y') AS commit_date,
                        expedite,
                        customer_id,
                        groups.name,
                        code,
                        CONCAT(users.first_name, ' ', users.last_name) AS user_name,
                        users.username,
                        customer.first_name,
                        customer.last_name,
                        sites.address,
                        sites.address_ext,
                        sites.city,
                        sites.state,
                        sites.zipcode,
                        customer.phone_1,
                        customer.phone_2,
                        customer.email_1,
                        customer.email_2,
                        orders.deleted,
                        subtotal,
                        DATE_FORMAT(commit_date, '%m/%d/%Y') as commit_date,
                        carrier,
                        tracking_num
                    ", false)
                    ->join('status_codes', 'status_codes.id=orders.status_code')
                    ->join('groups', 'groups.id=orders.dealer_id', 'left')
                    ->join('orders_has_customers', 'orders_has_customers.order_id=orders.id AND `primary`=1', 'left')
                    ->join('users customer', 'customer.id=orders_has_customers.customer_id', 'left')
                    ->join('users', 'users.id=orders.created_by')
                    ->join('sites', 'sites.id=orders.site_id', 'left');
            if ($code_from && $code_from !== 'all') {
                $db->where('code >=', $code_from);
            }
            if ($code_to && $code_from !== 'all') {
                $db->where('code <=', $code_to);
            }
            if ($dealer !== 'all' && $dealer) {
                $db->where('dealer_id', $dealer);
            }
            if ($date_from) {
                $db->where('orders.' . $date_filter . ' >=', $date_from);
            }
            if ($date_to) {
                $db->where('orders.' . $date_filter . ' <=', $date_to);
            }
            $orders = $db->get('orders')->result();
            $this->ci->order_model->attach_sqft($orders);
            $orders_clean = array();
            foreach ($orders as $order) {
                $orders_clean[] = array(
                    'Order Date'    => $order->order_date,
                    'OC Sent Date'   => $order->oc_sent,
                    'Customer Trak' => $order->customer_id,
                    'Order #'       => $order->id,
                    'Order Status'  => $order->code,
                    'Status Date'   => $order->status_date,
                    'User'          => $order->user_name,
                    'Username'      => $order->username,
                    'Group'         => $order->name,
                    'Customer First' => $order->first_name,
                    'Customer Last' => $order->last_name,
                    'Address'       => $order->address . ' ' . $order->address_ext,
                    'City'          => $order->city,
                    'State'         => $order->state,
                    'Zip'           => $order->zipcode,
                    'Phone'         => empty($order->phone_1) ? $order->phone_2 : $order->phone_1,
                    'Email'         => empty($order->email_1) ? $order->email_2 : $order->email_1,
                    'No. Windows'   => count($order->items),
                    'Total Sq. Ft.' => $order->sqft,
                    'Order Subtotal'=> '$' . $order->subtotal,
                    'Commit Date'   => $order->commit_date,
                    'Carrier'       => $order->carrier,
                    'Tracking Num.' => $order->tracking_num,
                    'Deleted'       => $order->deleted,
                    'Expedite'      => $order->expedite ? 'Yes' : 'No',
                );
            }
            return $orders_clean;
        }

        public function get_manufacturing_report_data($code_from, $code_to, $dealer, $date_filter, $date_from, $date_to) {
            $db = &$this->ci->db;
            $db
                    ->select("
                        orders.id,
                        (SELECT DATE_FORMAT(status_changed, '%m/%d/%Y') FROM orders_status_codes JOIN status_codes ON order_status_code_id=status_codes.id WHERE order_id=orders.id AND code=300 ORDER BY orders_status_codes.id DESC LIMIT 1) AS order_date,
                        (SELECT DATE_FORMAT(status_changed, '%m/%d/%Y') FROM orders_status_codes WHERE order_id=orders.id AND order_status_code_id=orders.status_code ORDER BY orders_status_codes.id DESC LIMIT 1) AS status_date,
                        DATE_FORMAT(orders.commit_date, '%m/%d/%Y') AS commit_date,
                        (
                            SELECT
                            IF(count( distinct product_types.product_id) > 1, 'mixed', products.product) as ptype
                            FROM orders_has_item, items
                            INNER JOIN product_types ON items.product_types_id=product_types.id
                            INNER JOIN products ON product_types.product_id=products.id
                            WHERE orders_has_item.item_id = items.id
                            AND orders_has_item.order_id = orders.id
                            group by orders_has_item.order_id

                        ) AS product_type,
                        expedite,
                        groups.name,
						materials_ordered,
                        code,
                        DATE_FORMAT(build_date, '%m/%d/%Y') as build_date
                    ", false)
                    ->join('status_codes', 'status_codes.id=orders.status_code')
                    ->join('groups', 'groups.id=orders.dealer_id', 'left')
                    ->where('orders.deleted', 0);
            if ($code_from && $code_from !== 'all') {
                $db->where('code >=', $code_from);
            }
            if ($code_to && $code_from !== 'all') {
                $db->where('code <=', $code_to);
            }
            if ($dealer !== 'all' && $dealer) {
                $db->where('dealer_id', $dealer);
            }
            if ($date_from) {
                $db->where('orders.' . $date_filter . ' >=', $date_from);
            }
            if ($date_to) {
                $db->where('orders.' . $date_filter . ' <=', $date_to);
            }
            $orders = $db->order_by('build_date')->get('orders')->result();
            $this->ci->order_model->attach_sqft($orders);
            $orders_clean = array();
            foreach ($orders as $order) {
                $orders_clean[] = array(
                    'Status Date'   => $order->status_date,
                    'Order Date'    => $order->order_date,
                    'Commit Date'   => $order->commit_date,
                    'Order #'       => $order->id,
                    'Order Status'  => $order->code,
                    'No. Windows'   => count($order->items),
                    'Sq. Ft.'       => $order->sqft,
                    'Product Type'  => $order->product_type,
                    'Group Name'    => $order->name,
                    'Build Date'    => $order->build_date,
					'Acrylic Delivery' => $order->materials_ordered,
                    'Expedite'      => $order->expedite ? 'Yes' : 'No'
                );
            }
            return $orders_clean;
        }

        public function get_estimate_report_data($dealer, $date_from, $date_to) {
            $db = &$this->ci->db;
            $db
                    ->select("
                        estimates.id,
                        groups.name,
                        CONCAT(users.first_name, ' ', users.last_name) AS user_name,
                        users.username,
                        customer.first_name,
                        customer.last_name,
                        sites.address,
                        sites.address_ext,
                        sites.city,
                        sites.state,
                        sites.zipcode,
                        customer.phone_1,
                        customer.phone_2,
                        customer.email_1,
                        customer.email_2,
                        closed,
                        estimates_has_customers.customer_id,
                        DATE_FORMAT(estimates.created, '%m/%d/%Y') AS created,
                        estimates.deleted
                    ", false)
                    ->join('groups', 'groups.id=estimates.dealer_id', 'left')
                    ->join('estimates_has_customers', 'estimates_has_customers.estimate_id=estimates.id AND `primary`=1', 'left')
                    ->join('users customer', 'customer.id=estimates_has_customers.customer_id', 'left')
                    ->join('users', 'users.id=estimates.created_by_id')
                    ->join('sites', 'sites.id=estimates.site_id', 'left');
            if ($dealer !== 'all' && $dealer) {
                $db->where('dealer_id', $dealer);
            }
            if ($date_from) {
                $db->where('estimates.created >=', $date_from);
            }
            if ($date_to) {
                $db->where('estimates.created <=', $date_to);
            }
            $estimates = $db->get('estimates')->result();
            $this->ci->order_model->attach_sqft($estimates, 'estimates_has_item', 'estimate_id');
            $estimates_clean = array();
            foreach ($estimates as $estimate) {
                $estimates_clean[] = array(
                    'Username'      => $estimate->username,
                    'Estimate'      => 'http://' . $_SERVER['HTTP_HOST'] . '/estimates/edit/' . $estimate->id,
                    'Customer Trak' => $estimate->customer_id,
                    'Created'       => $estimate->created,
                    'Estimate Status' => $estimate->closed ? 'Closed' : 'Active',
                    'No. Windows'   => count($estimate->items),
                    'Customer First' => $estimate->first_name,
                    'Customer Last' => $estimate->last_name,
                    'Address'       => $estimate->address . ' ' . $estimate->address_ext,
                    'City'          => $estimate->city,
                    'State'         => $estimate->state,
                    'Zip'           => $estimate->zipcode,
                    'Phone'         => empty($estimate->phone_1) ? $estimate->phone_2 : $estimate->phone_1,
                    'Email'         => empty($estimate->email_1) ? $estimate->email_2 : $estimate->email_1,
                    'User'          => $estimate->user_name,
                    'Group'         => $estimate->name,
                    'Total Sq Ft'   => $estimate->sqft,
                    'Subtotal'      => '$' . $estimate->isubtotal,
                    'Deleted'       => $estimate->deleted
                );
            }
            return $estimates_clean;
        }

        public function get_panels_report_data($date_from, $date_to) {
            $db = &$this->ci->db;
            $db
                    ->select("
                        items.id,
                        groups.name,
                        orders.id AS order_id,
                        product,
                        product_type,
                        acrylic_panel_thickness,
                        edging.name AS edging,
                    ")
                    ->join('items', 'orders_has_item.item_id=items.id')
                    ->join('orders', 'orders_has_item.order_id=orders.id')
                    ->join('groups', 'orders.dealer_id=groups.id', 'left')
                    ->join('product_types', 'items.product_types_id=product_types.id')
                    ->join('products', 'product_types.product_id=products.id')
                    ->join('edging', 'edging_id=edging.id', 'left')
                    ->where('orders.deleted', '0');
            if ($date_from) {
                $db->where('orders.created >=', $date_from);
            }
            if ($date_to) {
                $db->where('orders.created <=', $date_to);
            }
            $panels = $db->order_by('items.id')
                    ->get('orders_has_item')->result();
            $this->ci->item_model->attach_measurements($panels);
            $ret = array();
            foreach ($panels as $panel) {
                $width = max(@$panel->measurements['A'], @$panel->measurements['B']);
                $height = max(@$panel->measurements['C'], @$panel->measurements['D']);
                $ret[] = array(
                    'Dealer'    => $panel->name,
                    'Order #'   => $panel->order_id,
                    'Width'     => $width,
                    'Height'    => $height,
                    'Product'   => $panel->product,
                    'Product Type' => $panel->product_type,
                    'Thickness' => $panel->acrylic_panel_thickness,
                    'Tubing Color' => $panel->edging,
                );
            }
            return $ret;
        }

        public function get_users_report_data() {
            $users = $this->ci->db
                    ->select('users.*, groups.name AS groupname, gp.name AS gperm, up.name AS uperm', false)
                    ->join('users_groups', 'users_groups.user_id=users.id')
                    ->join('groups', 'groups.id=users_groups.group_id')
                    ->join('customers', 'users.id=customers.user_id', 'left')
                    ->join('permission_presets gp', 'gp.id=groups.permissions_id', 'left')
                    ->join('permission_presets up', 'up.id=users.permission_set', 'left')
                    ->where('customers.user_id IS NULL')
                    ->get('users')->result();
            $ret = array();
            foreach ($users as $user) {
                $ret[] = array(
                    'First Name'    => $user->first_name,
                    'Last Name'     => $user->last_name,
                    'Username'      => $user->username,
                    'Email'         => $user->email_1,
                    'Group'         => $user->groupname,
                    'Permission Set'=> $user->uperm ? $user->uperm : $user->gperm,
                    'Disabled'      => $user->active ? 0 : 1,
                    'Last Login'    => $user->last_login ? date('m/d/Y', $user->last_login) : ''
                );
            }
            return $ret;
        }

        function get_balance_report_data($dealer, $date_from, $date_to) {
            $db = &$this->ci->db;
            $db
                    ->select("
                        orders.id,
                        status_changed,
                        (SELECT status_changed FROM orders_status_codes JOIN status_codes ON status_codes.id=order_status_code_id WHERE code=700 AND order_id=orders.id ORDER BY orders_status_codes.id DESC LIMIT 1) as closed,
                        groups.name AS groupn,
                        total,
                        (SELECT SUM(payment_amount) FROM payments WHERE order_id=orders.id AND deleted=0) AS payments
                    ", false)
                    ->join('orders_status_codes', 'orders_status_codes.order_id=orders.id AND order_status_code_id=7', 'left')
                    ->join('groups', 'groups.id=orders.dealer_id', 'left');
            if ($dealer !== 'all' && $dealer) {
                $db->where('dealer_id', $dealer);
            }
            if ($date_from) {
                $db->where('status_changed >=', $date_from);
            }
            if ($date_to) {
                $db->where('status_changed <=', $date_to);
            }
            $orders = $db->get('orders')->result();
            $orders_clean = array();
            foreach ($orders as $order) {
                if ($order->total - $order->payments > 1) {
                    $orders_clean[] = array(
                        'Dealer'        => $order->groupn,
                        'Order Number'  => $order->id,
                        'Order Date'    => $order->status_changed,
                        'Order Closed Date' => $order->closed,
                        'Balance'       => '$' . money_format('%i', $order->total - $order->payments)
                    );
                }
            }
            return $orders_clean;
        }

        public function get_jobsites_report_data($type) {
            $db = &$this->ci->db;
            $q = $db
                    ->select("
                            items.*, first_name, last_name, customer.email_1, groups.name AS dealer, created, product_type, product, edging.name as edging
                        ", false)
                    ->join('items', 'site_has_items.item_id=items.id')
                    ->join('sites', 'sites.id=site_has_items.site_id')
                    ->join('site_customers', 'site_customers.site_id=sites.id AND `primary`=1', 'left')
                    ->join('users customer', 'customer.id=site_customers.customer_id', 'left')
                    ->join('users_groups', 'users_groups.user_id=sites.created_by', 'left')
                    ->join('groups', 'users_groups.group_id=groups.id', 'left')
                    ->join('product_types', 'product_types.id=product_types_id')
                    ->join('products', 'products.id=product_id')
                    ->join('edging', 'edging.id=edging_id', 'left')
                    ->where('site_has_items.deleted', 0)
                    ->where('measured', 1);
            if ($type !== 'both') {
                if ($type == 'ordered') {
                    $db->where('order_id IS NOT NULL');
                } else if ($type == 'unordered') {
                    $db->where('order_id IS NULL');
                }
            }
            $items = $q->get('site_has_items')->result();
            $this->ci->item_model->attach_measurements($items);
            $citems = array();
            foreach ($items as $item) {
                $citems[] = array(
                    'Customer First Name'   => $item->first_name,
                    'Customer Last Name'    => $item->last_name,
                    'Customer Email'        => $item->email_1,
                    'Dealer'                => $item->dealer,
                    'Created Date'          => $item->created,
                    'Width'                 => max(@$item->measurements['A'], @$item->measurements['B']),
                    'Height'                => max(@$item->measurements['C'], @$item->measurements['D']),
                    'Product'               => $item->product,
                    'Product Type'          => $item->product_type,
                    'Thickness'             => $item->acrylic_panel_thickness,
                    'Tubing'                => $item->edging
                );
            }
            return $citems;
        }
    }
