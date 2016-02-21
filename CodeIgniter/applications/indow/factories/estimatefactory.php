<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class EstimateFactory extends MM_Factory
{
	protected $_model = "Estimate_model";
	protected $_table = "estimates";
	protected $_primaryKey = "estimates.id";

    public function __construct()
    {
        parent::__construct();
	}
	public function getList($cid, $order_id=null)
	{
        $sql = "
            SELECT   estimates.id,
                                DATE_FORMAT(estimates.created, '%m/%d/%Y') AS created,
				CONCAT(sites.address, ', ', sites.city, ', ', sites.state, ',', sites.zipcode) as jobsite,
                CONCAT(customer.first_name, ' ', customer.last_name) AS customer_name,
				if(estimates.closed = 1, 'Closed','Open') as status,
                ( SELECT       count(*)
                  FROM         estimates_has_item 
                  INNER JOIN   items ON estimates_has_item.item_id = items.id
                  INNER JOIN  product_types ON product_types.id = items.product_types_id
                  INNER JOIN  products ON products.id = product_types.product_id
                  WHERE        estimates_has_item.deleted=0
                  AND          estimates_has_item.estimate_id = estimates.id
                  AND          product_types.product_id != 3
                ) as item_count,
				CONCAT(creation.first_name, ' ',creation.last_name) as created_by,
                                (
                                        CASE
                                            WHEN estimates.created_by_id = ? THEN 'users'
                                            ELSE
                                            'usersall'
                                        END
                                        ) as check_user,
				groups.name as dealer,
                estimate_total AS price
				FROM 			estimates
				LEFT JOIN 		sites
				ON 				estimates.site_id = sites.id AND sites.deleted = 0
				INNER JOIN 		users as creation
				ON 				estimates.created_by_id = creation.id
				LEFT JOIN 		groups
				ON 				groups.id = estimates.dealer_id 
                LEFT JOIN       estimates_has_customers ON estimates_has_customers.estimate_id=estimates.id AND estimates_has_customers.primary=1
                LEFT JOIN       users customer ON customer.id = estimates_has_customers.customer_id"
                ;
        if (!$order_id) {
            $sql .= "
                WHERE parent_estimate_id=0 AND " . $this->permissionslibrary->get_where_string(2, 'estimates.created_by_id', 'dealer_id');
        } else {
            $sql .= " WHERE estimates.id IN (SELECT estimates.id FROM orders LEFT JOIN estimates ON estimates.site_id=orders.site_id AND orders.site_id != 0 WHERE orders.id = ?) AND parent_estimate_id=0";
        }
            $sql .= "
                AND estimates.deleted = 0
                Group by estimates.id 
                ;
                ";
        if ($order_id) {
            $results = $this->db->query($sql,array($cid,$order_id))->result();
        } else {
            $results = $this->db->query($sql,array($cid,$cid))->result();
        }
        //prd($this->db->last_query());
		return $results;
	}

	public function getFunnelData()
	{
		$sql = "SELECT COUNT(id) as active_estimates,
             (SELECT AVG(width*height / 144) FROM items JOIN estimates_has_item ON items.id=estimates_has_item.item_id AND estimates_has_item.deleted=0 WHERE estimate_id=estimates.id) AS avg_sqft
            FROM estimates WHERE deleted = 0 AND parent_estimate_id = 0 AND closed = 0";
		$query = $this->db->query($sql);
		$result = $query->row();
		$sql = "SELECT avg(count) as avg_panels FROM (SELECT count(*) as count FROM estimates INNER JOIN estimates_has_item ON estimates.id = estimates_has_item.estimate_id group by estimates_has_item.estimate_id) as counts";
		$query = $this->db->query($sql);
		$result->avg_panels = $query->row()->avg_panels;
		return $result;
	}
}
