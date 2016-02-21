<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OrderFactory extends MM_Factory
{
    protected $_model = "Order_model";
    protected $_table = "orders";
    protected $_primaryKey = "orders.id";

    public function __construct()
    {
        parent::__construct();
    }

    public function getList($user_id)
    {
        $sql = "SELECT          orders.id,
                                orders.id as order_name,
                                status_codes.code AS status,
                                status_codes.description as status_name,
                                (
                                    SELECT       count(*)
                                    FROM         orders_has_item 
                                    INNER JOIN   items ON orders_has_item.item_id = items.id
                                    INNER JOIN   product_types ON product_types.id = items.product_types_id
                                    INNER JOIN   products ON products.id = product_types.product_id
                                    WHERE        orders_has_item.deleted=0
                                    AND          orders_has_item.order_id = orders.id
                                    AND          product_types.product_id != 3

                                ) as num_win,
                                total,
                                CONCAT(pcustomer.first_name, ' ', pcustomer.last_name) as customer,
                                DATE_FORMAT(orders.created, '%m/%d/%y') AS created,
                                DATE_FORMAT(orders.updated, '%m/%d/%y') AS updated,
                                dealer.name as dealer,
								dealer_id,
                                CONCAT(creator.first_name, ' ', creator.last_name) as created_by,
                                DATE_FORMAT(orders_status_codes.status_changed, '%m/%d/%y') as order_date,
                                po_num,
                                (
                                CASE
                                   WHEN orders.created_by = ? THEN 'users'
                                   ELSE
                                   'usersall'
                                 END
                                 ) as check_user,
                                 IF(code=700, 0, 1) AS open
                FROM            orders
                LEFT JOIN      sites ON `orders`.`site_id` = sites.id
                LEFT JOIN       orders_has_customers ON orders.id = orders_has_customers.order_id AND orders_has_customers.primary=1
                LEFT JOIN       users pcustomer ON orders_has_customers.customer_id=pcustomer.id
                LEFT JOIN       groups dealer ON dealer.id = orders.dealer_id
                LEFT JOIN       orders_status_codes ON orders_status_codes.order_id = orders.id AND order_status_code_id=(SELECT id FROM status_codes WHERE code=300)
                JOIN users creator ON creator.id = orders.created_by

                INNER JOIN      status_codes ON orders.status_code = status_codes.id
                INNER JOIN      users_groups as ugroup 
                ON              ugroup.user_id = orders.created_by
                WHERE orders.deleted=0 AND " . $this->permissionslibrary->get_where_string(6, 'orders.created_by', 'dealer_id') . "
                group by orders.id
                ;
                ";

        return $this->db->query($sql, $user_id)->result();
    }
    public function getPipelineList($group_id, $start_date, $end_date)
    {
        $sql = "SELECT          orders.id,
                                CONCAT(orders.id, '-',order_number_type,'-',order_number_type_sequence) as order_name,
                                status_codes.code AS status,
                                CONCAT(customer.first_name, ' ',customer.last_name) as customer,
                                groups.name as dealer,
                                CONCAT(creator.first_name, ' ',creator.last_name) as created_by_name,
                                orders.commit_date,
                                CONCAT(customer.first_name, ' ', customer.last_name) as customer,
                                groups.name as dealer,
                                CONCAT(creator.first_name, ' ', creator.last_name) as created_by,
                                DATE_FORMAT(orders.created, '%m/%d/%y') as order_date
                FROM            orders
                JOIN orders_has_customers ON (orders_has_customers.order_id=orders.id AND orders_has_customers.primary=1)
                JOIN users as customer ON orders_has_customers.customer_id = customer.id
                INNER JOIN      sites ON `orders`.`site_id` = sites.id
                INNER JOIN      users as creator ON orders.created_by = creator.id
                LEFT JOIN      groups ON groups.id = orders.dealer_id
                INNER JOIN      status_codes ON orders.status_code = status_codes.id
                WHERE           status_codes.code BETWEEN 300 AND 650 AND
                orders.commit_date BETWEEN         ? AND ?
                ";
        $args = array($start_date, $end_date);
        if ($group_id)
        {
            $groups = $this->GroupFactory->getIdsInHeirarchy($group_id);
            $sql .= "AND groups.id IN (";
            $in = array();
            foreach ($groups as $group)
            {
                $args[] = $group;
                $in[] = "?";
            }
            $sql .= implode(",", $in) . ")";
        }
        $results = $this->db->query($sql,$args)->result();

        return $results;
    }
    public function getPipelineCounts($group_id, $start_date, $end_date)
    {
        $list = $this->getPipelineList($group_id, $start_date, $end_date);
        $orderCount = count($list);

        $ids = array();
        $in = array();
        foreach ($list as $item)
        {
            $ids[] = $item->id;
            $in[] = "?";
        }
        $in_clause = implode(",", $in);
        $productCount = 0;
        if ($orderCount)
        {
            $sql = "SELECT count(*) as count FROM orders_has_item WHERE order_id IN ($in_clause)";
            $result = $this->db->query($sql,$ids)->row();
            $productCount = $result->count;
        }
        return array("orderCount"=>$orderCount,"productCount"=>$productCount);
    }

    public function get_bundle_list($dealer_id, $exclude_id=null) {
        if (!$dealer_id) {
            return array();
        } else {
            $this->db
                    ->select("
                        orders.id,
                        CONCAT(order_number, '-', order_number_type_sequence) as order_num,
                        po_num,
                        CONCAT(first_name, ' ', last_name) AS customer,
                        (SELECT COUNT(*) FROM orders_has_item WHERE order_id=orders.id) AS num_win,
                        DATE_FORMAT(orders.created, '%m/%d/%y') as created,
                        code as status_code,
                        status_codes.description
                    ", false)
                    ->join('orders_has_customers', 'orders.id=orders_has_customers.order_id AND orders_has_customers.primary=1')
                    ->join('users', 'users.id=customer_id')
                    ->join('status_codes', 'status_codes.id=status_code')
                    ->where('code >=', 100)
                    ->where('code <=', 600)
                    ->where('orders.deleted', 0)
                    ->where('dealer_id', $dealer_id);
            if ($exclude_id) {
                $this->db->where('orders.id !=', $exclude_id);
            }
            return $this->db->get('orders')->result();
        }
    }

    public function queuedPanelCount()
    {
        $sql = "SELECT          orders_has_item.* 
                FROM            orders
                INNER JOIN      status_codes
                ON              orders.status_code = status_codes.id
                INNER JOIN      orders_has_item
                on              orders.id = orders_has_item.order_id
                WHERE           status_codes.code BETWEEN 300 AND 650";
        return $this->db->query($sql)->num_rows();
    }
    public function getOpeningFunnelData()
    {
        $sql = "SELECT          count(*) as openings
                FROM            orders
                INNER JOIN      status_codes
                ON              orders.status_code = status_codes.id
                INNER JOIN      orders_has_item
                on              orders.id = orders_has_item.order_id
                WHERE           status_codes.code BETWEEN 300 AND 650";
        $result = $this->db->query($sql)->row();
        $sql = "SELECT          AVG(count) as panels_per_location
                FROM
                (SELECT         COUNT(*) as count
                FROM            orders
                INNER JOIN      status_codes
                ON              orders.status_code = status_codes.id
                INNER JOIN      orders_has_item
                on              orders.id = orders_has_item.order_id
                WHERE           status_codes.code BETWEEN 300 AND 650
                GROUP BY order_id) AS counts";
        $result->panels_per_location = $this->db->query($sql)->row()->panels_per_location;
        $sql = "SELECT          AVG(sqft) as avg_sqft
                FROM
                (SELECT         (
                    (SELECT measurement_value FROM items_measurements JOIN measurements ON measurements.id=items_measurements.measurement_id WHERE item_id=items.id AND measurement_key='B')
                    / 12 * 
                    (SELECT measurement_value FROM items_measurements JOIN measurements ON measurements.id=items_measurements.measurement_id WHERE item_id=items.id AND measurement_key='D')
                    / 12) as sqft
                FROM            orders
                INNER JOIN      status_codes
                ON              orders.status_code = status_codes.id
                INNER JOIN      orders_has_item
                on              orders.id = orders_has_item.order_id
                INNER JOIN      items
                ON              items.id = orders_has_item.item_id
                WHERE           status_codes.code BETWEEN 300 AND 650 AND items.manufacturing_status=1
                GROUP BY orders_has_item.order_id) as sqfts";
        $result->avg_sqft = $this->db->query($sql)->row()->avg_sqft;
        return $result;
    }
    public function getProductFunnelData()
    {
        $sql = "SELECT          COUNT(*) as count
                FROM            orders
                INNER JOIN      status_codes
                ON              orders.status_code = status_codes.id
                INNER JOIN      orders_has_item
                on              orders.id = orders_has_item.order_id
                LEFT OUTER JOIN subitems
                ON              subitems.item_id = orders_has_item.item_id
                WHERE           status_codes.code BETWEEN 300 AND 650";
        $count = $this->db->query($sql)->row()->count;
        $sql = "SELECT          COUNT(*) as count
                FROM            orders
                INNER JOIN      status_codes
                ON              orders.status_code = status_codes.id
                INNER JOIN      orders_has_item
                on              orders.id = orders_has_item.order_id
                RIGHT OUTER JOIN subitems
                ON              subitems.item_id = orders_has_item.item_id
                WHERE           status_codes.code BETWEEN 300 AND 650";
        $count += $this->db->query($sql)->row()->count;
        return $count;
    }

}
