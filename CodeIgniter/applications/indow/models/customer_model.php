<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customer_model extends MM_Model
{
    protected  $_table      = 'customers';
    protected  $soft_delete = TRUE;
    protected  $_key        = 'customers.user_id';

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array('site_model'));

        $this->load->library('form_validation');
        $this->load->helper('functions');
    }

    public function update_customer($id, $data)
    {
        if ($this->form_validation->run() == TRUE || 1) 
        {
            $customer_fields = array( 'customer_referred_by',
                                      'customer_preferred_contact',
                                      'customer_company_name',
                                      'sales_modifier_id'
                                     );

            $user_fields     = array( 'first_name',
                                      'last_name',
                                      'address_type_1',
                                      'address_1',
                                      'address_1_ext',
                                      'city_1',
                                      'state_1',
                                      'zipcode_1',
                                      'email_1',
                                      'email_type_1',
                                      'phone_1',
                                      'phone_type_1',
                                      'address_type_2',
                                      'address_2',
                                      'address_2_ext',
                                      'city_2',
                                      'state_2',
                                      'zipcode_2',
                                      'email_2',
                                      'email_type_2',
                                      'phone_2',
                                      'phone_type_2',
                                      'phone_3',
                                      'phone_type_3',
                                      'company_id',
                                      'organization_name',
                                     );

            $customer = array();
            $user     = array();
            foreach ( $data as $key => $value )
            {
                if ( $key   !== 'submit'
                //&&   $value !== '' 
                &&   in_array($key, $customer_fields))
                {
                    $customer[$key] = $value;
                }
                else if ( $key   !== 'submit'
                //&&        $value !== '' 
                &&        in_array($key, $user_fields))
                {
                    $user[$key] = $value;
                }
            }
            if (!empty($customer['customer_preferred_contact'])) {
                $dbcust = $this->db->where('user_id', $id)->get('customers')->row();
                if ($dbcust->customer_preferred_contact != $customer['customer_preferred_contact']) {
                    $this->db->insert('rep_history', array(
                        'customer_id' => $id,
                        'rep_id' => $customer['customer_preferred_contact'],
                        'changed' => date('Y-m-d H:i:s')
                    ));
                }
            }
            if ($customer['customer_preferred_contact'] === '') {
                $customer['customer_preferred_contact'] = null;
            }
            $this->_update_customer($id, $customer, $user);

        } else {
            //var_dump(validation_errors()); exit;
            return array('success' => false, 'message' => validation_errors());
        }

        return array('success' => true, 'message' => 'Updated customer successfully.');
    }

    public function update_contact_info($id, $data)
    {
        $customer_fields = array( 'customer_company_name',);

        $user_fields     = array( 'first_name',
                                  'last_name',
                                  'email_1',
                                  'email_type_1',
                                  'phone_1',
                                  'phone_type_1',
                                  'address_type_2',
                                  'email_2',
                                  'email_type_2',
                                  'phone_2',
                                  'phone_type_2',
                                );

        $customer = array();
        $user     = array();
        foreach ( $data as $key => $value )
        {
            if ( $key   !== 'submit'
            &&   $value !== '' 
            &&   in_array($key, $customer_fields))
            {
                $customer[$key] = $value;
            }
            else if ( $key   !== 'submit'
            &&        $value !== '' 
            &&        in_array($key, $user_fields))
            {
                $user[$key] = $value;
            }
        }
        if ($customer['customer_preferred_contact'] === '') {
            $customer['customer_preferred_contact'] = null;
        }

        $this->_update_customer($id, $customer, $user);

        return array('success' => true, 'message' => 'Updated customer successfully.');
    }

    public function fetch_contact_info($id, $data)
    {
        $sql = '
            SELECT  first_name,
                    last_name,
                    email_1,
                    email_type_1,
                    phone_1,
                    phone_type_1,
                    email_2,
                    email_type_2,
                    phone_2,
                    phone_type_2
            FROM    customers
            INNER JOIN users ON customers.user_id = users.id 
            WHERE   customers.user_id = ?;
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

    public function getProfile($id)
    {
        $sql = "SELECT  
                    id as user_id,
                    first_name,
                    last_name,
                    company_id,
                    email_1,
                    email_type_1,
                    phone_1,
                    phone_type_1,
                    email_2,
                    email_type_2,
                    phone_2,
                    phone_type_2,
                    customer_referred_by,
                    customer_preferred_contact
            FROM    customers
            INNER JOIN users ON customers.user_id = users.id 
            WHERE   customers.user_id = ?;
            ";

        $query = $this->db->query($sql, $id);

        if ( ! empty($query)  && $query->num_rows() > 0) 
        {
            return $query->row();
        }
        else{
            return false;
        }

    }

    public function defaultSettings()
    {
        $customer = new stdClass();
        $customer->address           = '';
        $customer->address_ext       = '';
        $customer->city              = '';
        $customer->state             = '';
        $customer->zipcode           = '';
        $customer->address_type      = 'residential';
        $customer->deleted           = '0';

        return $customer;
    }

    public function fetch_customers($limit, $start)
    {
        $sql = "SELECT      customers.user_id as id,
                            CONCAT(`first_name`,' ',`last_name`) as name,
                            sites.address as address,
                            users.phone_1 as phone,
                            users.email_1 as email
                FROM        customers
                INNER JOIN  users ON user_id  = users.id
                LEFT JOIN   site_customers ON customer_id  = customers.user_id
                LEFT JOIN   sites ON site_id  = sites.id
                WHERE       customers.deleted = 0
                LIMIT       ?, ?";

        $start = (int) $start;
        $limit = (int) $limit;
        $query = $this->db->query($sql, array($start, $limit));

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

    public function fetch_customer($user_id)
    {
        $sql = '
            SELECT
                first_name,
                last_name,
                customer_company_name AS company,
                
                IF (email_1 IS NULL,"",email_1) as email_1,
                IF (email_type_1 IS NULL,"",email_type_1) as email_type_1,
                IF (email_2 IS NULL,"",email_2) as email_2,
                IF (email_type_2 IS NULL,"",email_type_2) as email_type_2,

                IF (phone_1 IS NULL,"",phone_1) as phone_1,
                IF (phone_type_1 IS NULL,"",phone_type_1) as phone_type_1,
                IF (phone_2 IS NULL,"",phone_2) as phone_2,
                IF (phone_type_2 IS NULL,"",phone_type_2) as phone_type_2,
                IF (phone_3 IS NULL,"",phone_3) as phone_3,
                IF (phone_type_3 IS NULL,"",phone_type_3) as phone_type_3

            FROM customers
            INNER JOIN users ON customers.user_id = users.id
            WHERE customers.user_id = ? 
            LIMIT 1
            ;
            ';

        $query = $this->db->query($sql, $user_id);

        if ( ! empty($query)  && $query->num_rows() > 0) 
        {
            $data = $query->result();
            return $data[0];
        }

        return false;
    }
    
    public function delete($id)
    {
        $this->db->trans_start();
        $sql = "DELETE FROM " . $this->_table . " WHERE  " . $this->_key . " = ? ;";
        $query = $this->db->query($sql,$id);
        $this->db->trans_complete();

        return $query;
    }
    public function delete_customer($id)
    {
        $this->db->where('user_id', $id);
        $this->db->update('customers', array('deleted' => 1));

        $this->db->where('id', $id);
        $this->db->update('users', array('deleted' => 1));
    }

    public function delete_address($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('user_addresses');
    }

    protected function is_unique_group_email($email, $group, $id=null) {
        if (!$email) {
            return true; //other form validation will prevent blank emails
        }
        if ($id) {
            $this->db->where('id <>', $id);
        }
        $row = $this->db->where('email_1', $email)->where('company_id', $group)->where('deleted', 0)->get('users')->row();
        return $row ? false : true;
    }

    public function add_customer($data)
    {
        $company_id = empty($data['company_id']) ? (empty($this->_user->group_ids) ? 1 : $this->_user->group_ids[0]) : $data['company_id'];
        $email = empty($data['email_1']) ? '' : $data['email_1'];
        if (!$this->is_unique_group_email($this->input->post('email_1'), $company_id)) {
            return array('success' => false, 'message' => 'A customer with that email address already exists.');
        }
        
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('email_1', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('email_2', 'Email', 'valid_email');
        $this->form_validation->set_rules('phone_1', 'Phone', 'required|regex_match[/^[0-9().-]+$/]');
        $this->form_validation->set_rules('phone_2', 'Phone', 'regex_match[/^[0-9().-]+$/]');
        $this->form_validation->set_rules('phone_3', 'Phone', 'regex_match[/^[0-9().-]+$/]');
        /*
        if (isset($data['address'])) {
            $this->form_validation->set_rules('address[address]', 'Address', 'required');
            $this->form_validation->set_rules('address[city]', 'City', 'required');
            $this->form_validation->set_rules('address[state]', 'State', 'required');
            $this->form_validation->set_rules('address[zipcode]', 'Zipcode', 'required');
        }*/

        if ($this->form_validation->run() == TRUE) 
        {
            $user_keys = array('first_name', 'last_name', 'organization_name', 'email_1', 'email_type_1', 'email_2', 'email_type_2', 'phone_1', 'phone_type_1', 'phone_2', 'phone_type_2', 'phone_3', 'phone_type_3', 'company_id');
            $userdata = array();
            foreach ($user_keys as $key) {
                if (isset($data[$key])) {
                    $userdata[$key] = $data[$key];
                }
            }

            $userdata['is_customer'] = 1;
            if (empty($userdata['company_id'])) {
                $userdata['company_id'] = $company_id;
            }

            $this->db->insert( 'users', $userdata );
            $userid = $this->db->insert_id();

            $customer = array(
              'user_id' => $userid,
              'customer_referred_by' => $this->ion_auth->get_user_id(),
              'customer_preferred_contact' => isset($data['customer_preferred_contact']) ? $data['customer_preferred_contact'] : $this->_user->id,
              'sales_modifier_id' => 1
            );
            if ($customer['customer_preferred_contact'] === '') {
                $customer['customer_preferred_contact'] = null;
            }
            $this->db->insert( 'customers', $customer );
            if (isset($data['address'])) {
                if (!empty($data['address']['address_type'])) {
                    $data['address']['address_type'] = implode(' / ', $data['address']['address_type']);
                }
                $data['address']['user_id'] = $userid;
                $this->db->insert('user_addresses', $data['address']);
                if(isset($data['createSite']) && $data['createSite']){
                    $site_id = $this->site_model->create_from_address($this->db->insert_id());
                }
            }
/*
            if (isset($data['customer_address'])) {
                $address = $data['customer_address'];
                if (!isset($address['address_type'])) {
                    $address['address_type'] = array();
                }

                if (!in_array('other', $address['address_type'])) {
                    $address['address_type_other'] = '';
                }

                $address['address_type'] = implode(' / ', $address['address_type']);

                $address['user_id'] = $userid;
                $this->db->insert('user_addresses', $address);
            } */

        } else {
            return array('success' => false, 'message' => validation_errors());
        }
        $return = array('success' => true, 'message' => 'Added customer successfully.', 'userid' => $userid);
        if (isset($site_id)) {
            $return['site_id'] = $site_id;
            $return['site'] = $data['address'];
            $return['site']['address_type'] = 'Residential';
        }
        return $return;
    }

    public function fetch_sitelist($customer_id)
    {
        $sql = "SELECT     sites.id, sites.address, sites.address_ext, sites.city , sites.state, sites.zipcode
                FROM       sites
                LEFT JOIN site_customers ON sites.id = site_customers.site_id
                INNER JOIN customers ON customers.user_id = site_customers.customer_id
                WHERE customers.user_id  = ? AND sites.deleted=0
                ;
                ";

        $query = $this->db->query($sql, $customer_id);

        $data = array();

        if ( ! empty($query)  && $query->num_rows() > 0) 
        {
            foreach ($query->result() as $row) 
            {
                $site    = array();
                $site_id = $row->id;

                $site['id']        = $row->id;
                $site['address']   = $row->address . ' ' . $row->address_ext . ' ' . $row->city . ' ' . $row->state  . ' ' . $row->zipcode;
                $site['estimates'] = $this->site_model->fetch_estimates($site_id);
                $site['quotes']    = $this->site_model->fetch_quotes($site_id);

                $data[$row->id] = $site;
            }

            return $data;
        }

        return false;
    }

    public function fetch_customer_info($user_id)
    {
        // TODO refactor types into separate tables or an enum
        $sql = "SELECT     id,
                           first_name,
                           last_name,
                           CONCAT(`first_name`, ' ', `last_name`) as name,
                           organization_name as company_name,
                           phone_1,
                           users.phone_type_1 as phone_type_1_int,
                           users.phone_type_2 as phone_type_2_int,
                           users.phone_type_3 as phone_type_3_int,
                           users.email_type_1 as email_type_1_int,
                           users.email_type_2 as email_type_2_int,
                           organization_name,
                           CASE users.phone_type_1
                             WHEN  '0' THEN (SELECT 'Home Phone')
                             WHEN  '1' THEN (SELECT 'Work Phone')
                             WHEN  '2' THEN (SELECT 'Mobile Phone')
                             ELSE (SELECT 'Home')
                           END as phone_type_1,
                           phone_3,
                           CASE users.phone_type_3
                             WHEN  '0' THEN (SELECT 'Home Phone')
                             WHEN  '1' THEN (SELECT 'Work Phone')
                             WHEN  '2' THEN (SELECT 'Mobile Phone')
                             ELSE (SELECT 'Home')
                           END as phone_type_3,
                           phone_2,
                           CASE users.phone_type_2
                             WHEN  '0' THEN (SELECT 'Home Phone')
                             WHEN  '1' THEN (SELECT 'Work Phone')
                             WHEN  '2' THEN (SELECT 'Mobile Phone')
                             ELSE (SELECT 'Home')
                           END as phone_type_2,
                           email_1,
                           CASE users.email_type_1
                             WHEN  '0' THEN (SELECT 'Home Email')
                             WHEN  '1' THEN (SELECT 'Work Email')
                             WHEN  '2' THEN (SELECT 'Mobile Email')
                             ELSE (SELECT 'Home')
                           END as email_type_1,
                           email_2,
                           CASE users.email_type_2
                             WHEN  '0' THEN (SELECT 'Home Email')
                             WHEN  '1' THEN (SELECT 'Work Email')
                             WHEN  '2' THEN (SELECT 'Mobile Email')
                             ELSE (SELECT 'Home')
                           END as email_type_2,
                           IF (password='', 0, 1) as has_login
                FROM       users
                WHERE      id = ?
                ;
                ";

        $query = $this->db->query($sql, $user_id);

        $data = array();

        if ( ! empty($query)  && $query->num_rows() > 0) 
        {
            foreach ($query->result() as $row) 
            {
                $data['name']          = $row->name;
                $data['first_name']    = $row->first_name;
                $data['last_name']     = $row->last_name;
                $data['company_name']  = $row->company_name;
                $data['phone_type_1']  = $row->phone_type_1;
                $data['phone_1']       = $row->phone_1;
                $data['phone_type_2']  = $row->phone_type_2;
                $data['phone_2']       = $row->phone_2;
                $data['phone_type_3']  = $row->phone_type_3;
                $data['phone_3']       = $row->phone_3;
                $data['email_type_1']  = $row->email_type_1;
                $data['email_1']       = $row->email_1;
                $data['email_type_2']  = $row->email_type_2;
                $data['email_2']       = $row->email_2;
                $data['organization_name'] = $row->organization_name;
                $data['phone_type_1_int'] = $row->phone_type_1_int;
                $data['phone_type_2_int'] = $row->phone_type_2_int;
                $data['phone_type_3_int'] = $row->phone_type_3_int;
                $data['email_type_1_int'] = $row->email_type_1_int;
                $data['email_type_2_int'] = $row->email_type_2_int;
            }

        }

        if ( ! empty($data) )
        {
            return $data;
        }

        return false;
    }

    public function save_address($address) {
        $this->form_validation->set_rules('address', 'Address', 'required');
        $this->form_validation->set_rules('city', 'City', 'required');
        $this->form_validation->set_rules('state', 'State', 'required');
        $this->form_validation->set_rules('zipcode', 'Zipcode', 'required');

        if ($this->form_validation->run() == TRUE) {
            if (!isset($address['address_type'])) {
                $address['address_type'] = array();
            }

            if (!in_array('other', $address['address_type'])) {
                $address['address_type_other'] = '';
            }

            $address['address_type'] = implode(' / ', $address['address_type']);
            if (!empty($address['id'])) {
                $this->update_table($address['id'], $address, 'user_addresses', 'id');
            } else {
                $this->db->insert('user_addresses', $address);
            }
        }else{
            return array('success' => false, 'message' => validation_errors());
        }

        return array('success' => true, 'message' => 'Updated site successfully.');
    }

    public function fetch_address($id) {
        $row = $this->db->get_where('user_addresses', array('id' => $id))->row_array();
        $row['address_type'] = explode(' / ', $row['address_type']);
        if (!in_array('other', $row['address_type'])) {
            $row['address_type_other'] = '';
        }
        return $row;
    }
    public function fetch_address_list($customer_id)
    {
        $sql = "
            SELECT user_addresses.id,
                   address_type, address_type_other, 
                   CONCAT(user_addresses.address, ' ', user_addresses.address_ext , ' ' , city , ' ' , state , ' ' , zipcode  ) as address
            FROM   customers
            INNER JOIN users ON customers.user_id = users.id
            INNER JOIN user_addresses ON user_addresses.user_id = users.id
            WHERE customers.user_id = ?
            ;
            ";

        $query = $this->db->query($sql, $customer_id);

        if ( ! empty($query)  && $query->num_rows() > 0) 
        {
            foreach ($query->result() as $row) 
            {
                $type = explode(' / ', $row->address_type);

                if (in_array('other', $type)) {
                    $othertype = 'Other (' . $row->address_type_other . ')';
                    $row->address_type = str_replace('other', $othertype, $row->address_type);
                }

                $data[] = $row;
            }
        }

        if ( ! empty($data) )
        {
            return $data;
        }

        return false;
    }

    public function customer_manager_get_customer($ids, $primary = null) {
        if (!count($ids)) {
            return array();
        }
        $customers = $this->db
                ->select("
                    users.id,
                    user_id,
                    users.first_name,
                    users.last_name,
                    users.email_1,
                    users.email_2,
                    users.phone_1,
                    users.phone_2,
                    users.phone_3,
                    users.phone_type_1,
                    users.phone_type_2,
                    users.phone_type_3,
                    users.email_type_1,
                    users.email_type_2,
                    users.organization_name,
                    CONCAT(rep.first_name, ' ', rep.last_name) AS rep
                ", false)
                ->from('customers')
                ->join('users', 'users.id=customers.user_id')
                ->join('users rep', 'rep.id=customer_preferred_contact', 'left')
                ->where_in('users.id', $ids)->get()->result();
        $ret = array();
        foreach ($customers as $customer) {
            $addresses = $this->db->where('user_id', $customer->user_id)->limit(1)->get('user_addresses')->result();
            if (count($addresses)) {
                $customer->address = $addresses[0];
            }
            if ($customer->id == $primary) {
                $customer->primary = 1;
            }
            $customer->name = $customer->first_name . ' ' . $customer->last_name;
            $ret[] = $customer;
        }
        return $ret;
    }

    private function _update_customer($id, $customer, $user)
    {
        if ( ! empty($customer) )
        {
            $customer_table = "customers";
            $customer_key   = "user_id";
            $this->update_table($id, $customer, $customer_table, $customer_key);
        }

        if ( ! empty($user) )
        {
            $user_table = "users";
            $user_key   = "id";
            $this->update_table($id, $user, $user_table, $user_key);
        }
    }

    public function get_customer_list()
    {

        $query = $this->get_all();
        $data = array();
        foreach($query as $row){

            $data[] = array(
                'label' => $row->name,
                'value' => $row->name,
                'id' => $row->id
            );

        }

        if(empty($data))
        {
            return false;
        }
        else
        {
            return $data;
        }


    }

    public function get_shipping_address($customer_id, $array=false) {
        $q = $this->db->where('user_id', $customer_id)->like('address_type', 'shipping')->get('user_addresses');
        return $array ? $q->row_array() : $q->row();
    }

    public function get_shipping_list($ids, $dealer_id=null) {
        $this->load->model('group_model');
        $customer_addresses = $this->db->select('*')->where_in('user_id', $ids)->get('user_addresses')->result();

        $addresses = array();
        if ($dealer_id) {
            $dealer_addresses = $this->db->where('group_id', $dealer_id)->get('group_addresses')->result();
            foreach ($dealer_addresses as $dealer_address) {
                $dealer_address->id = 'd' . $dealer_address->id;
                $addresses[] = $dealer_address;
            }
        }

        $addresses = array_merge($addresses, $customer_addresses);
        return $addresses;
    }

    public function get_customers($parent_id, $parent_id_name, $assoc_table) {
        $customers = $this->db->where($parent_id_name, $parent_id)->get($assoc_table)->result();
        $ids = array();
        $primary = null;
        foreach($customers as $customer) {
            $ids[] = $customer->customer_id;
            if ($customer->primary ==1) {
                $primary = $customer->customer_id;
            }
        }
        return array($ids, $primary);
    }

    public function get_estimate_customers($estimate_id) {
        return $this->get_customers($estimate_id, 'estimate_id', 'estimates_has_customers');
    }
    public function get_quote_customers($quote_id) {
        return $this->get_customers($quote_id, 'quote_id', 'quotes_has_customers');
    }
    public function get_order_customers($quote_id) {
        return $this->get_customers($quote_id, 'order_id', 'orders_has_customers');
    }
    public function get_site_customers($site_id) {
        return $this->get_customers($site_id, 'site_id', 'site_customers');
    }

    public function set_customers($customers, $parent_id, $parent_id_name, $assoc_table) {
        $this->db->where($parent_id_name, $parent_id);
        $this->db->delete($assoc_table);
        foreach ($customers as $customer) {
            $customer[$parent_id_name] = $parent_id;
            $customer['customer_id'] = $customer['id'];
            unset($customer['id']);
            $this->db->insert($assoc_table, $customer);
        }
    }
    public function set_estimate_customers($customers, $estimate_id) {
        $this->set_customers($customers, $estimate_id, 'estimate_id', 'estimates_has_customers');
    }
    public function set_quote_customers($customers, $quote_id) {
        $this->set_customers($customers, $quote_id, 'quote_id', 'quotes_has_customers');
    }
    public function set_order_customers($customers, $order_id) {
        $this->set_customers($customers, $order_id, 'order_id', 'orders_has_customers');
    }

    public function copy_customers($from_type, $from_id, $to_type, $to_id) {
        if ($from_type === 'estimate') {
            list($customers, $primary) = $this->get_estimate_customers($from_id);
        } else if($from_type === 'quote') {
            list($customers, $primary) = $this->get_quote_customers($from_id);
        } else if ($from_type === 'jobsite') {
            list($customers, $primary) = $this->get_site_customers($from_id);
        }
        $icust = array();
        foreach ($customers as $customer) {
            $icust[] = array('id' => $customer, 'primary' => $customer == $primary ? 1 : 0);
        }
        if ($to_type === 'order') {
            $this->set_order_customers($icust, $to_id);
        } elseif ($to_type === 'quote') {
            $this->set_quote_customers($icust, $to_id);
        } elseif ($to_type === 'estimate') {
            $this->set_estimate_customers($icust, $to_id);
        }
    }
}
