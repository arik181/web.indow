<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

    /**
     * @todo refactor SQL query to account for permissions
     * Class CustomerFactory
     */
    class CustomerFactory extends MM_Factory
    {
        protected $_model = "Customer_model";
        protected $_table = "customers";
        protected $_primaryKey = "customers.id";

        public function __construct()
        {
            parent::__construct();
        }

        /**
         * @param $id
         *
         * @return mixed
         */
        public function getList() {
            $sql = "SELECT
                      customers.user_id as id,
                      CONCAT(`first_name`,' ',`last_name`) as name,
                      (SELECT CONCAT(sites.address, ', ', sites.city, ', ', sites.state, ', ', sites.zipcode)
                             FROM sites
                             JOIN site_customers on site_customers.site_id = sites.id
                             WHERE site_customers.customer_id = customers.user_id ORDER BY id DESC LIMIT 1) as address,
                      IF(users.phone_1!='', users.phone_1, IF(users.phone_2!='', users.phone_2, users.phone_3)) as phone,
                      users.email_1 as email,
                      groups.name as company
                      FROM customers
                      INNER JOIN  users ON user_id  = users.id
                      INNER JOIN  groups ON users.company_id  = groups.id
                      WHERE customers.deleted = 0 AND " . $this->permissionslibrary->get_where_string(1, 'customer_referred_by', 'users.company_id') . "
                      GROUP BY 	users.id
            ;";

            $results = $this->db->query($sql)->result();

            return $results;
        }
    }
