<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

    class QuoteFactory extends MM_Factory
    {
        protected $_model = "Quote_model";
        protected $_table = "quotes";
        protected $_primaryKey = "quotes.id";

        public function __construct()
        {
            parent::__construct();
        }

        public function getList($cid)
        {
            $sql = "SELECT          quotes.id,
                        CONCAT(sites.address, ', ', sites.city, ', ', sites.state, ',', sites.zipcode) as jobsite,
                        if(quotes.closed = 1, 'Closed','Open') as status,
                        customer.first_name as customer_name,
                        groups.name AS dealer,
                        CONCAT(creator.first_name, ' ',creator.last_name) AS created_by_name,
                       (
                             CASE
                                WHEN quotes.created_by = ? THEN 'users'
                                ELSE
                                'usersall'
                            END
                        ) as check_user,
                        DATE_FORMAT(quotes.created,'%m/%d/%y') as created
                FROM            quotes
                LEFT JOIN      sites
                ON              quotes.site_id = sites.id AND sites.deleted = 0
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
                 WHERE   ugroup.group_id IN (SELECT group_id FROM users_groups Where users_groups.user_id =  ?) AND quotes.deleted = 0
                 Group by quotes.id;
                ";

            $results = $this->db->query($sql, array($cid, $cid))->result();

            foreach ($results as $result) {
                $sql                = "SELECT 			count(items.id) as count,
									sum(price) as price
					FROM 			quotes_has_item
					INNER JOIN 		items
					ON 				quotes_has_item.item_id = items.id
					WHERE 			quote_id = ?";
                $row                = $this->db->query($sql, $result->id)->row();
                $result->item_count = $row->count;
                $result->price      = '$ ' . round($row->price, 2);
                $user               = $this->db->from('quotes_has_customers')->join('users', 'users.id=quotes_has_customers.customer_id')->where('quote_id', $result->id)->where('primary', 1)->get()->result();
                if ($user) {
                    $user                  = $user[0];
                    $result->customer_name = $user->first_name . ' ' . $user->last_name;
                } else {
                    $result->customer_name = '';
                }

            }


            return $results;
        }

        public function getAdminList($cid)
        {
            $sql = "SELECT          quotes.id,
                        CONCAT(sites.address, ', ', sites.city, ', ', sites.state, ',', sites.zipcode) as jobsite,
                        if(quotes.closed = 1, 'Closed','Open') as status,
                        customer.first_name as customer_name,
                        groups.name AS dealer,
                        CONCAT(creator.first_name, ' ',creator.last_name) AS created_by_name,
                       (
                             CASE
                                WHEN quotes.created_by = ? THEN 'users'
                                ELSE
                                'usersall'
                            END
                        ) as check_user,
                        DATE_FORMAT(quotes.created,'%m/%d/%y') as created
                FROM            quotes
                LEFT JOIN      sites
                ON              quotes.site_id = sites.id AND sites.deleted = 0
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
                 WHERE   quotes.deleted = 0
                 Group by quotes.id;
                ";

            $results = $this->db->query($sql, $cid)->result();

            foreach ($results as $result) {
                $sql                = "SELECT 			count(items.id) as count,
									sum(price) as price
					FROM 			quotes_has_item
					INNER JOIN 		items
					ON 				quotes_has_item.item_id = items.id
					WHERE 			quote_id = ?";
                $row                = $this->db->query($sql, $result->id)->row();
                $result->item_count = $row->count;
                $result->price      = '$ ' . round($row->price, 2);
                $user               = $this->db->from('quotes_has_customers')->join('users', 'users.id=quotes_has_customers.customer_id')->where('quote_id', $result->id)->where('primary', 1)->get()->result();
                if ($user) {
                    $user                  = $user[0];
                    $result->customer_name = $user->first_name . ' ' . $user->last_name;
                } else {
                    $result->customer_name = '';
                }

            }


            return $results;
        }

    }
