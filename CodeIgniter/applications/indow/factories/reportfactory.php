<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ReportFactory extends MM_Factory
{
    public function getList()
    {
        $sql = "
                SELECT reports.id, name, created, DATE_FORMAT(created, '%m/%d/%y') as created_f FROM reports
                INNER JOIN      users_groups as ugroup 
                ON              ugroup.user_id = reports.created_by
                WHERE   ugroup.group_id IN (SELECT group_id FROM users_groups Where users_groups.user_id =  ?) AND reports.deleted=0
                ;
                ";
        $user_id = $this->ion_auth->get_user_id();
        $results = $this->db->query($sql,$user_id)->result();
        return $results;
    }

    public function getReport($report_id) {
        $this->load->model('report_model');
        $report = $this->report_model->get($report_id);
        $sql = "
            SELECT
            CONCAT(customer.first_name,' ', customer.last_name) as customer,
            COUNT(*) units,
            COUNT(*) windows,
            estimates.estimate_total as price,
            estimates_has_customers.customer_id as cus_id,
            if(estimates.closed = 1, 'Closed','Open') as status

            FROM 		 estimates
            LEFT JOIN    estimates_has_item ON estimates_has_item.estimate_id = estimates.id
            LEFT JOIN    estimates_has_customers ON estimates_has_customers.estimate_id = estimates.id  AND estimates_has_customers.primary=1
            LEFT JOIN    users AS customer ON customer.id = estimates_has_customers.customer_id
            LEFT JOIN   users_groups as ugroup ON ugroup.user_id = estimates.created_by_id
            WHERE estimates.deleted = 0 AND estimates.created >= ? AND estimates.created <= ? 
                ";

//        $sql = "SELECT
//                CONCAT(first_name,' ', last_name) as customer,
//                (SELECT count(*) FROM orders_has_item WHERE order_id=orders.id AND deleted=0) as units,
//                (SELECT count(*) FROM orders_has_item WHERE order_id=orders.id AND deleted=0) as windows,
//                total as price,
//                IF(code>=700, 'Closed', 'Open') as status
//                FROM orders
//                LEFT JOIN orders_has_customers ON orders_has_customers.order_id = orders.id
//                LEFT JOIN users ON orders_has_customers.customer_id = users.id
//                INNER JOIN      status_codes ON status_code = status_codes.id
//                INNER JOIN      users_groups as ugroup
//                ON              ugroup.user_id = orders.created_by
//                WHERE orders.created >= ? AND orders.created <= ? AND orders.deleted=0 AND
//                ";

        if ($report->display_type == 1) {
            $sql .= "ugroup.group_id IN (SELECT group_id FROM users_groups Where users_groups.user_id =  ?) ";
        } else {
            $sql .= "estimates.created_by_id = ? ";
        }

        $sql .= ' Group by estimates.id ';

        $user_id = $this->ion_auth->get_user_id();
        $results = $this->db->query($sql,array($report->from, $report->to, $user_id))->result();

        return $results;
    }

    public function getAdminReport($report_id) {
        $this->load->model('report_model');
        $report = $this->report_model->get($report_id);
        $sql = "
                SELECT
                CONCAT(customer.first_name,' ', customer.last_name) as customer,
                COUNT(*) units,
                COUNT(*) windows,
                estimates.estimate_total as price,
                estimates_has_customers.customer_id as cus_id,
                if(estimates.closed = 1, 'Closed','Open') as status

                FROM 		 estimates
                LEFT JOIN    estimates_has_item ON estimates_has_item.estimate_id = estimates.id
                LEFT JOIN    estimates_has_customers ON estimates_has_customers.estimate_id = estimates.id  AND estimates_has_customers.primary=1
                LEFT JOIN    users AS customer ON customer.id = estimates_has_customers.customer_id
                LEFT JOIN   users_groups as ugroup ON ugroup.user_id = estimates.created_by_id
                WHERE estimates.deleted = 0 AND estimates.created >= ? AND estimates.created <= DATE_ADD( ? , INTERVAL 1 DAY) Group by estimates.id
            ;
        ";

        $results = $this->db->query($sql,array($report->from, $report->to))->result();

        return $results;
    }
}
