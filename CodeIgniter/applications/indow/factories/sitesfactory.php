<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

    class SitesFactory extends MM_Factory
    {
        protected $_model = "Customer_model";
        protected $_table = "customers";
        protected $_primaryKey = "customers.id";

        public function __construct()
        {
            parent::__construct();
        }

        public function getList($cid)
        {

            $sql = "SELECT  		sites.id as id,
        			        sites.created,
	                    		CONCAT(user.first_name,' ',user.last_name) as name,
					CONCAT(sites.address, ', ', sites.city, ', ', sites.state, ',', sites.zipcode) as address,
					CONCAT(creator.first_name,' ',creator.last_name) as creator_name,
					sites.id as order_list_id,
					sites.id as estimate_list_id,
                    groups.name as company,
                    (SELECT (
                                            CASE
                                                WHEN COUNT(estimates.id) > 0 THEN 'stroke'
                                            ELSE
                                                'none'
                                            END 
                                            ) as estimate_count FROM estimates WHERE estimates.site_id = sites.id AND estimates.deleted = 0) as estimate_count,
                    (SELECT
                                        (
                                        CASE
                                            WHEN COUNT(quotes.id) > 0 THEN 'stroke'
                                            ELSE
                                            'none'
                                        END 
                                        ) as quotes_count FROM quotes WHERE quotes.site_id = sites.id AND quotes.deleted = 0) as quotes_count,
                    (SELECT  (
                                        CASE
                                            WHEN COUNT(orders.id) > 0 THEN 'stroke'
                                            ELSE
                                            'none'
                                        END 
                                        ) as orders_count FROM orders WHERE orders.site_id = sites.id AND orders.deleted = 0) as orders_count,
					user.id as user_id,
                                        (
                                        CASE
                                            WHEN sites.created_by = ? THEN 'users'
                                            ELSE
                                            'usersall'
                                        END
                                        ) as check_user,
                                        
                                        sites.address_type as address_type
                FROM   			sites
                LEFT JOIN 		site_customers
                ON 			 sites.id = site_customers.site_id
                LEFT JOIN 		users as user
                ON 			 user.id = site_customers.customer_id
                INNER JOIN 		users as creator
                ON 			 sites.created_by = creator.id
                LEFT JOIN              users_groups as ugroup 
                ON                       ugroup.user_id = sites.created_by
                LEFT JOIN    groups ON ugroup.group_id = groups.id 
                WHERE sites.deleted = 0 AND " . $this->permissionslibrary->get_where_string(5, 'sites.created_by', 'group_id') . "
                GROUP BY sites.id ORDER BY sites.id DESC
                ";

		$results = $this->db->query($sql,array($cid,$cid,$cid))->result();
                
                /*
                $sql2 = "SELECT user.id ,site_cust.site_id ,CONCAT(user.first_name, ' ' , user.last_name) as name
                         FROM users as user
                         INNER JOIN site_customers as site_cust ON (site_cust.customer_id = user.id AND site_cust.primary = 1)";
            $primary      = $this->db->query($sql2)->result_array();
            $primary_cust = array();
            foreach ($primary as $p) {
                $primary_cust[$p['site_id']] = $p;
            }

            $pri = array();

            foreach ($results as $result) {

                if (array_key_exists($result->id, $primary_cust)) {
                    $result->name    = $primary_cust[$result->id]['name'];
                    $result->user_id = $primary_cust[$result->id]['id'];
                }
                if ($result->address == '')
                    $result->address = "N/A";
            }*/
            /*
            foreach($results as $result) {
               // $result->estimate_count = '<a class="btn btn-sm btn-blue" href="sites/edit' . $result->id . '#estimates-anchor"' . ( $result->estimate_count == 'none' ? ' disabled="disabled"' : '' ) . '>View</a>';
               // $result->quotes_count = '<a class="btn btn-sm btn-blue" href="sites/edit' . $result->id . '#quotes-anchor"' . ( $result->quotes_count == 'none' ? ' disabled="disabled"' : '' ) . '>View</a>';
               // $result->orders_count = '<a class="btn btn-sm btn-blue" href="sites/edit' . $result->id . '#orders-anchor"' . ( $result->orders_count == 'none' ? ' disabled="disabled"' : '' ) . '>View</a>';
            }*/
            return $results;
        }

        public function getExisitngUserList($siteid)
        {
            $this->load->model('site_model');
            $results = $this->site_model->fetch_existing_customers_new($siteid);

            return $results;

        }

        public function getEstimates($site_id)
        {
            $sql = "SELECT     estimates.id,
                                DATE_FORMAT(estimates.created,'%m/%d/%y') as created,
				CONCAT(sites.address, ', ', sites.city, ', ', sites.state, ',', sites.zipcode) as jobsite,
				if(estimates.closed = 1, 'Estimate','Estimate') as status,
                                CONCAT(creation.first_name, ' ',creation.last_name) as created_by,
				COALESCE(groups.name,'N/A') as dealer
				FROM 			estimates
				LEFT JOIN 		sites
				ON 				estimates.site_id = sites.id AND sites.deleted = 0
				INNER JOIN 		users as creation
				ON 				estimates.created_by_id = creation.id
				LEFT JOIN 		groups
				ON 				groups.id = estimates.dealer_id
                                INNER JOIN              users_groups as ugroup 
                                  ON                       ugroup.user_id = estimates.created_by_id
                                WHERE sites.id = ? AND estimates.deleted=0 AND estimates.parent_estimate_id=0
                                Group by estimates.id ";


            $results = $this->db->query($sql, array($site_id))->result();
            //This could be slow for large data sets, may need to switch to trigger or some other approach
            foreach ($results as $result) {
                $sql                = "SELECT 			count(items.id) as count,
									sum(price) as price
					FROM 			estimates_has_item
					INNER JOIN 		items
					ON 				estimates_has_item.item_id = items.id
					WHERE 			estimate_id = ? AND estimates_has_item.deleted=0";
                $row                = $this->db->query($sql, $result->id)->row();
                $result->item_count = $row->count;
                $result->price      = round($row->price, 2);
                $user               = $this->db->from('estimates_has_customers')->join('users', 'users.id=estimates_has_customers.customer_id')->where('estimate_id', $result->id)->where('primary', 1)->get()->result();
                if ($user) {
                    $user                  = $user[0];
                    $result->customer_name = $user->first_name . ' ' . $user->last_name;
                } else {
                    $result->customer_name = '';
                }

            }

            return $results;
        }

        public function getQuotes($site_id)
        {
            $sql     = "SELECT          quotes.id,
                                    if(status_codes.id,'Quotes','Quotes') AS status,
                                    CONCAT(sites.address, ', ', sites.city, ', ', sites.state, ',', sites.zipcode) as jobsite,
                                    customer.first_name as customer_name,
                                    groups.name AS dealer,
                                    CONCAT(creator.first_name, ' ',creator.last_name) AS created_by_name,
                                    DATE_FORMAT(quotes.created,'%m/%d/%y') as created
                FROM            quotes
                LEFT JOIN      sites
                ON              quotes.site_id = sites.id
                LEFT JOIN       quotes_has_customers as qhcustomer
                ON              qhcustomer.quote_id = quotes.id
                LEFT JOIN       users as customer
                ON              customer.id = qhcustomer.customer_id
                INNER JOIN      users AS creator
                ON              quotes.created_by = creator.id
                INNER JOIN      groups
                ON              groups.id = quotes.dealer_id
                INNER JOIN      status_codes
                ON              quotes.status_code = status_codes.id
                INNER JOIN      users_groups as ugroup 
                ON              ugroup.user_id = quotes.created_by
                 WHERE    sites.id = ? AND quotes.deleted=0
                 Group by quotes.id
                ";
            $results = $this->db->query($sql, array($site_id))->result();

            // This could be slow for large data sets, may need to switch to trigger or some other approach
            foreach ($results as $result) {
                $sql                = "SELECT 			count(items.id) as count,
						       sum(price) as price
					FROM          quotes_has_item
					INNER JOIN 		items
					ON 				quotes_has_item.item_id = items.id
					WHERE 			quote_id = ?";
                $row                = $this->db->query($sql, $result->id)->row();
                $result->item_count = $row->count;
                $result->price      = round($row->price, 2);
            }

            return $results;

        }

        public function getOrders($site_id)
        {
            $sql     = "SELECT    orders.id,
                                CONCAT(orders.id, '-',order_number_type,'-',order_number_type_sequence) as order_name,
                                status_codes.code AS status,
                                groups.name as dealer,
                                signed_purchase_order,
                                CONCAT(creator.first_name, ' ',creator.last_name) as created_by_name,
                                CONCAT(customer.first_name, ' ',customer.last_name) as customer,
                                DATE_FORMAT(orders.created,'%m/%d/%y') as created
                FROM            orders
                LEFT JOIN      sites ON `orders`.`site_id` = sites.id
                INNER JOIN      users as creator ON orders.created_by = creator.id
                LEFT JOIN      groups ON groups.id = orders.dealer_id
                INNER JOIN      status_codes ON orders.status_code = status_codes.id
                LEFT JOIN orders_has_customers ON orders_has_customers.order_id=orders.id AND orders_has_customers.primary=1
                LEFT JOIN users customer ON orders_has_customers.customer_id=customer.id
                INNER JOIN      users_groups as ugroup 
                ON              ugroup.user_id = orders.created_by
                WHERE   sites.id = ? AND orders.deleted=0
                Group by orders.id
                ;
                ";
            $results = $this->db->query($sql, array($site_id))->result();

//                INNER JOIN      users as customer ON orders.customer_id = customer.id
            return $results;

        }


    }
