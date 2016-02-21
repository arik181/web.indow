<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Site_model extends MM_Model {

    protected $_table = 'sites';
    protected $soft_delete = TRUE;
    protected $_key = 'sites.id';
    public $tmp_userid = NULL;

    public function __construct() {
        parent::__construct();

        $this->load->library('form_validation');
        $this->load->helper(array('functions','array'));
    }

    public function create($data, &$customers=FALSE, $full_cust=false) {
        $this->form_validation->set_rules('site_address', 'Address', 'required');
        $this->form_validation->set_rules('site_city', 'City', 'required');
        $this->form_validation->set_rules('site_state', 'State', 'required');
        $this->form_validation->set_rules('site_zipcode', 'Zipcode', 'required');

        if ($this->form_validation->run() == TRUE)
        {  //tEMPORARY fALSE
            $this->db->insert('sites', $data);
            $id = $this->db->insert_id();
            if (!$full_cust) {
                if($customers){
                    foreach($customers as $c){
                        $customer['site_id'] = $id;
                        $customer['customer_id'] = $c;
                        $customer['primary'] = 0;
                        $this->db->insert('site_customers', $customer);
                    }
                }
            } else {
                $this->set_site_customers($id, $customers);
            }
        }
        else
        {
            return array('success' => false, 'message' => validation_errors());
        }

        return array('success' => true, 'message' => 'Saved site successfully.', 'id' => $id);
    }

    public function create_site($data) { /* Redundant... but set_rules */
        $this->db->insert('sites', $data);
        return $this->db->insert_id();
    }

    public function update_site($id, $data) {

        $this->form_validation->set_rules('address', 'Address', 'required');
        $this->form_validation->set_rules('city', 'City', 'required');
        $this->form_validation->set_rules('state', 'State', 'required');
        $this->form_validation->set_rules('zipcode', 'Zipcode', 'required');
        
        if ($this->form_validation->run() == TRUE) {  //tEMPORARY fALSE
            $site = array();
            foreach ($data as $key => $value) {
                if ($key !== 'submit' && $value !== '') {
                    $site[$key] = $value;
                }
            }
            $this->update($id, $site);
        } else {

            return array('success' => false, 'message' => validation_errors());
        }

        return array('success' => true, 'message' => 'Updated site successfully.');
    }

    public function get_dealer_id($site_id) {
        /*
        $row = $this->db->select('group_id')
                        ->from('site_customers')
                        ->join('customers', 'customers.user_id=site_customers.customer_id')
                        ->join('users_groups', 'users_groups.user_id=customer_preferred_contact AND site_customers.primary = 1')
                        ->where('site_customers.site_id', $site_id)->get()->row();
        if ($row) {
            return $row->group_id;
        } else {
            return null;
        }*/
        $job_site = $this->db->where('id', $site_id)->get('sites')->row();
        return $this->user_model->get_group_id($job_site->created_by);
    }

    public function set_site_customer($site_id, $customer_id) {
        $customer['site_id'] = $site_id;
        $customer['customer_id'] = $customer_id;
        $customer['primary'] = 1;
        $this->db->insert('site_customers', $customer);
    }

    public function set_site_customers($id, $customers) {
        $newcustomers = array();
        foreach ($customers as $customer) {
            $newcustomers[] = $customer['id'];
        }

        $custq = $this->db->where('site_id', $id)->get('site_customers')->result();
        foreach($custq as $c) {
            if (!in_array($c->customer_id, $newcustomers)) {
                $this->db->insert('site_customer_history', array(
                    'site_id'       => $id,
                    'customer_id'   => $c->customer_id,
                    'removed'       => date('Y-m-d H:i:s')
                ));
            }
        }
        
        $this->db->where('site_id', $id);
        $this->db->delete('site_customers');
        foreach ($customers as $customer) {
            $customer['site_id'] = $id;
            $customer['customer_id'] = $customer['id'];
            $customer['primary'] = $customer['primary'];
            unset($customer['id']);
            $this->db->insert('site_customers', $customer);
        }
    }

    public function get_customer_history($site_id) {
        return $this->db
                ->select("users.id, first_name, last_name, 'Customer' as type, groups.name as groupname, zipcode, username", false)
                ->from('site_customer_history')
                ->join('users', 'customer_id=users.id')
                ->join('users_groups', 'users_groups.user_id=users.id', 'left')
                ->join('groups', 'users_groups.group_id=groups.id', 'left')
                ->join('user_addresses', 'user_addresses.user_id=users.id', 'left')
                ->group_by('users.id')
                ->where('site_id', $site_id)
                ->get()->result();
        
    }

    public function get_dealer_history($customer_ids) {
        if (!count($customer_ids)) {
            return array();
        }
        return $this->db
                ->select("groups.id, groups.name as groupname, group_addresses.zipcode, 'Dealer' AS type, '' as first_name, '' as last_name, '' as username", false)
                ->from('rep_history')
                ->join('users_groups', 'users_groups.user_id=rep_history.rep_id')
                ->join('groups', 'groups.id=users_groups.group_id')
                ->join('group_addresses', 'group_addresses.group_id=groups.id AND addressnum=1', 'left')
                ->where_in('customer_id', $customer_ids)
                ->group_by('groups.id')
                ->get()->result();
    }

    public function set_site_users($id, $users) {
        $this->db->where('site_id', $id);
        $this->db->delete('users_sites');
        foreach ($users as $user) {
            $duser = array(
                'site_id' => $id,
                'user_id' => $user
            );
            $this->db->insert('users_sites', $duser);
        }
    }

    public function getProfile($id) {
        $sql = '
            SELECT  *
            FROM    sites
            WHERE   sites.id = ?;
            ';

        $query = $this->db->query($sql, $id);

        if (!empty($query) && $query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function defaultSettings() {
        $site = new stdClass();
        $site->address = '';
        $site->address_ext = '';
        $site->city = '';
        $site->state = '';
        $site->zipcode = '';
        $site->address_type = 'residential';
        $site->deleted = '0';

        return $site;
    }

    public function fetch_sites($limit, $start) {
        $sql = 'SELECT  *
                FROM   `sites`
                WHERE   sites.deleted = 0
                LIMIT   ?, ?';

        $start = (int) $start;
        $limit = (int) $limit;
        $query = $this->db->query($sql, array($start, $limit));

        if (!empty($query) && $query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }

            return $data;
        }

        return false;
    }

    public function fetch_customers_ulist($id) {
        $sql = "SELECT          CONCAT(`first_name`,' ',`last_name`) as name
                FROM            sites
                INNER JOIN      site_customers
                ON sites.id     = site_customers.site_id
                INNER JOIN      customers
                ON customers.id = site_customers.customer_id
                INNER JOIN      users
                ON customers.user_id = users.id
                WHERE sites.id  = ?
                ;
                ";

        $query = $this->db->query($sql, $id);

        if (!empty($query) && $query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }

            return object_to_array($data);
        }
        return false;
    }

    public function fetch_users_association_list($id) {
        $sql = "SELECT          users.id as user_id, CONCAT(`first_name`,' ',`last_name`) as name
                FROM            users
                INNER JOIN      users_sites
                ON users_sites.user_id = users.id
                WHERE           users_sites.site_id  = ?
                ;
                ";

        $query = $this->db->query($sql, $id);

        if (!empty($query) && $query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }

            return object_to_array($data);
        }

        return false;
    }

    public function fetch_primary_customer($id) {
        /*$sql = "SELECT          CONCAT(`first_name`,' ',`last_name`) as name,
                                phone_1,
                                email_1
                FROM            sites
                INNER JOIN      site_customers
                ON sites.id     = site_customers.site_id
                INNER JOIN      customers
                ON customers.user_id = site_customers.customer_id
                INNER JOIN      users
                ON customers.user_id = users.id
                WHERE sites.id  = ?
                AND             `site_customers`.primary = 1
                ;
                ";*/
        $sql = "SELECT          CONCAT(users.first_name,' ',users.last_name) as name,
                                users.id as user_id,
                                users.company_id,
                                users.email_1,
                                email_type_1,
                                users.phone_1,
                                users.phone_type_1,
                                users.email_2,
                                users.email_type_2,
                                users.phone_2,
                                users.phone_type_2,
                                customers.customer_referred_by,
                                customers.customer_preferred_contact
                FROM            sites
                INNER JOIN      site_customers
                ON sites.id     = site_customers.site_id
                INNER JOIN      customers
                ON customers.user_id = site_customers.customer_id
                INNER JOIN      users
                ON customers.user_id = users.id
                WHERE sites.id  = ?
                AND             `site_customers`.primary = 1
                ;
                ";

        $query = $this->db->query($sql, $id);

        if (!empty($query) && $query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }

            return $data[0];
        }

        return false;
    }

    public function fetch_existing_customers($id) {
        $sql = "SELECT          CONCAT(`first_name`,' ',`last_name`) as name
                FROM            sites
                INNER JOIN      site_customers
                ON sites.id     = site_customers.site_id
                INNER JOIN      customers
                ON customers.user_id = site_customers.customer_id
                INNER JOIN      users
                ON customers.user_id = users.id
                WHERE sites.id  = ?
                ;
                ";

        $query = $this->db->query($sql, $id);

        if (!empty($query) && $query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }

            return object_to_array($data);
        }

        return false;
    }
    
    public function fetch_existing_customers_new($id) {
        $sql = "SELECT          CONCAT(`first_name`,' ',`last_name`) as name,users.id
                FROM            sites
                INNER JOIN      site_customers
                ON sites.id     = site_customers.site_id
                INNER JOIN      customers
                ON customers.user_id = site_customers.customer_id
                INNER JOIN      users
                ON customers.user_id = users.id
                WHERE sites.id  = ?
                ;
                ";

        
        /*$query = $this->db->query($sql, $id);

        if (!empty($query) && $query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }

            return object_to_array($data);
        }

        return false;*/
        $result = $this->db->query($sql, $id)->result();
        return $result;
    }

    public function fetch_site_info($id) {
        $sql = "SELECT          address, 
                                address_ext,
                                CONCAT(city, ', ', state, ' ', zipcode) as city_state_zipcode, 
                                IF( address_type = 0, 'Type: Residential', 'Type: Business' ) as address_type
                FROM            sites
                WHERE sites.id  = ?
                ;
                ";

        $query = $this->db->query($sql, $id);

        if (!empty($query) && $query->num_rows() == 1) {
            $data = $query->result();
            return object_to_array($data[0]);
        }

        return false;
    }

    public function fetch_site_info_popup($id) {
        $sql = "SELECT          address, 
                                address_ext,
                                city,
                                state,
                                zipcode, 
                               address_type
                FROM            sites
                WHERE sites.id  = ?
                ;
                ";


        $query = $this->db->query($sql, $id);

        if (!empty($query) && $query->num_rows() == 1) {
            $data = $query->result();
            return object_to_array($data[0]);
        }

        return false;
    }

    public function fetch_window_totallist($id,$returndata = "array") {
        /*$sql = "SELECT              items.id AS window, 
                                    room, 
                                    window_attributes.max_width,
                                    window_attributes.max_height, 
                                    product_types.product_id,
                                    product_types.product_type as product_type,
                                    edging_id,
                                    items.price as retail
                FROM                estimates
                INNER JOIN          estimates_has_item
                ON                  estimates.id = estimates_has_item.estimate_id
                INNER JOIN          items
                ON                  items.id = estimates_has_item.item_id
                INNER JOIN          window_attributes
                ON                  items.product_attributes_id = window_attributes.id
                INNER JOIN          products
                ON                  items.product_types_id = products.id
                INNER JOIN          product_types
                ON                  products.id = product_types.id
                WHERE               estimates.site_id  = ?";*/
        $sql = "SELECT              items.id AS id,
                                    items.id AS window, 
                                    room, 
                                    window_attributes.max_width,
                                    window_attributes.max_height, 
                                    product_types.product_id,
                                    product_types.product_type as product_type,
                                    edging_id,
                                    items.price as retail
                FROM                estimates
                INNER JOIN          estimates_has_item
                ON                  estimates.id = estimates_has_item.estimate_id
                INNER JOIN          items
                ON                  items.id = estimates_has_item.item_id
                INNER JOIN          window_attributes
                ON                  items.product_attributes_id = window_attributes.id
                INNER JOIN          products
                ON                  items.product_types_id = products.id
                INNER JOIN          product_types
                ON                  products.id = product_types.product_id
                WHERE               estimates.site_id  = ?
                GROUP BY items.id
                ;
                 ";

        
        if($returndata == "object"){
            $result = $this->db->query($sql, $id)->result();
            return $result;
        }else{
            $query = $this->db->query($sql, $id);
        }
        if (!empty($query) && $query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
               
            return object_to_array($data);
        }
        return false;
    }

    public function fetch_customer_notes_history($id) {
        $sql = "SELECT          notes.*, CONCAT(users.first_name,  ' ', users.last_name) AS name
                FROM            sites
                INNER JOIN      site_customer_notes
                ON sites.id     = site_customer_notes.site_id
                INNER JOIN      notes
                ON notes.id     = site_customer_notes.note_id
                LEFT JOIN users ON users.id = notes.user_id
                WHERE sites.id  = ?
                ;
                ";

        return $this->db->query($sql, $id)->result();
    }

    public function fetch_internal_notes_history($id) {
        $sql = "SELECT          notes.*, CONCAT(users.first_name,  ' ', users.last_name) AS name
                FROM            sites
                INNER JOIN      site_internal_notes
                ON sites.id     = site_internal_notes.site_id
                INNER JOIN      notes
                ON notes.id     = site_internal_notes.note_id
                LEFT JOIN users ON users.id = notes.user_id
                WHERE sites.id  = ?
                ;
                ";

        return $this->db->query($sql, $id)->result();
    }

    public function fetch_quotes_estimates_boxlist($id) {
        return false; // TODO: Uncluster this fuck
        $sql = "SELECT          customer_id, 
                                sites.address, 
                                created,
                                CONCAT(users.first_name, ' ', users.last_name) as created_by,
                                dealer_id
                FROM            sites
                INNER JOIN      site_customers
                ON sites.id     = site_customers.site_id
                INNER JOIN      customers
                ON customers.id = site_customers.customer_id
                INNER JOIN      users
                ON customers.user_id = users.id
                WHERE sites.id  = ?
                ";

        $query = $this->db->query($sql, $id);

        if (!empty($query) && $query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }

            return object_to_array($data);
        }
        return false;
    }
    
    public function fetch_estimates_sites($id){
         return false;
        $sql = "SELECT          customer_id, 
                                sites.address, 
                                created,
                                CONCAT(users.first_name, ' ', users.last_name) as created_by,
                                dealer_id
                FROM            sites
                INNER JOIN      site_customers
                ON sites.id     = site_customers.site_id
                INNER JOIN      customers
                ON customers.id = site_customers.customer_id
                INNER JOIN      users
                ON customers.user_id = users.id
                WHERE sites.id  = ?
                ";
    }
    public function fetch_quotes_sites($id){
         return false;
         $sql = "SELECT          customer_id, 
                                sites.address, 
                                created,
                                CONCAT(users.first_name, ' ', users.last_name) as created_by,
                                dealer_id
                FROM            sites
                INNER JOIN      site_customers
                ON sites.id     = site_customers.site_id
                INNER JOIN      customers
                ON customers.id = site_customers.customer_id
                INNER JOIN      users
                ON customers.user_id = users.id
                WHERE sites.id  = ?
                ";
    }

    public function fetch_users_boxlist($id) {
        $sql = "SELECT          CONCAT(`first_name`,' ',`last_name`) as name
                FROM            sites
                INNER JOIN      site_customers
                ON              sites.id = site_customers.site_id
                INNER JOIN      customers
                ON              customers.user_id = site_customers.customer_id
                INNER JOIN      users
                ON              customers.user_id = users.id
                WHERE sites.id  = ?";

        $query = $this->db->query($sql, $id);

        if (!empty($query) && $query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }

            return object_to_array($data);
        }
        return false;
    }

    public function fetch_orders_boxlist($id) {
       // return false;// TODO: Uncluster this fuck
        $sql = "SELECT
  orders.order_number as ordernumber,
  CONCAT(users.first_name, ' ',users.last_name )
  
FROM
orders
INNER JOIN sites ON sites.id = orders.site_id
INNER JOIN site_customers ON site_customers.site_id = sites.id
INNER JOIN users ON users.id = site_customers.customer_id
where sites.id = ?
 group by orders.id               ;
                ";

        $query = $this->db->query($sql, $id);

        if (!empty($query) && $query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }

            return object_to_array($data);
        }
        return false;
    }

    public function fetch_history_boxlist($id) {
        return false; // TODO: Uncluster this fuck
        $sql = "SELECT               groups.name
                FROM                 sites
                INNER JOIN           site_groups
                ON sites.id          = site_customers.site_id
                INNER JOIN           customers
                ON customers.id      = site_customers.customer_id
                INNER JOIN           users
                ON customers.user_id = users.id
                WHERE sites.id  = ?
                ;
                ";

        $query = $this->db->query($sql, $id);

        if (!empty($query) && $query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }

            return object_to_array($data);
        }
        return false;
    }

    /*public function delete($id) {
        $this->db->trans_start();
        $sql = "DELETE FROM " . $this->_table . " WHERE  " . $this->_key . " = ? ;";
        $query = $this->db->query($sql, $id);
        $this->db->trans_complete();

        return $query;
    }*/
    
    public function site_delete($id){
       /*if($id){
           $this->site_customer_notes_delete($id);
           $this->site_internal_notes_delete($id);
           $this->site_has_items_delete($id);
           $this->site_customers_delete($id);
           if($this->db->delete('sites',array('id'=>$id)))
                return true;
           return false;
       }*/
       if(!empty($id)){
           $this->db->where('id', $id);
           if($this->db->update('sites',array('deleted' => 1))){
               return true;
           }
           return false;
       }else{
           return false;
       }
        
    }
    
    public function site_customer_notes_delete($site_id){
        if($site_id){
            $this->db->or_where_in('site_id',$site_id);
            $this->db->delete('site_customer_notes'); 
        }
    }
    public function site_internal_notes_delete($site_id){
        if($site_id){
            $this->db->or_where_in('site_id',$site_id);
            $this->db->delete('site_internal_notes');
           
        }
    }
    public function site_has_items_delete($site_id){
        if($site_id){
            $this->db->or_where_in('site_id',$site_id);
            $this->db->delete('site_has_items');
        }
    }
    public function site_customers_delete($site_id){
         if($site_id){
            $this->db->or_where_in('site_id',$site_id);
            $this->db->delete('site_customers');
        }
    }

    public function get_opportunity()
    {
        $this->db->select('opportunity_id');  
        $query = $this->db->get('customers');
        $results = $query->result();
        foreach($results as $result)
        {
            $return[] = $result->opportunity_id;
        }
        return $return;
    }

    public function add_site($data, $onlycustomer=false) {
        $now = date('Y-m-d h:i:s');
        $time = time();
        $user_keys = array('first_name', 'last_name', 'organization_name', 'email_1', 'email_type_1', 'email_2', 'email_type_2', 'phone_1', 'phone_type_1', 'phone_2', 'phone_type_2', 'phone_3', 'phone_type_3', 'company_id');
        $user_address_keys = array('address_type', 'address', 'address_ext', 'country', 'city', 'state', 'zipcode');
        $site_keys = array('address', 'address_ext', 'city', 'state', 'zipcode', 'address_type', 'opportunity_id');
        
        $logged_user_id = $this->session->userdata['user_id'];
        $new_customer_flag = 0;
        
        /*-----------------Add New Customer Data If Any---------------------------------------*/
        if(!empty($data['first_name']) && !empty($data['last_name'])){
            /* Adding Users Table Data */
            $userdata = array();
            foreach ($user_keys as $key) {
                if (isset($data[$key])) {
                    $userdata[$key] = $data[$key];
                }
            }
            $userdata['created_on'] = $time;
            $res0 = $this->db->insert('users', $userdata);
            $userid = $this->db->insert_id();
            $this->tmp_userid = $userid;

            /* Adding User Address data */
            $useraddress = array();
            foreach ($user_address_keys as $key) {
                if (isset($data[$key])) {
                    $useraddress[$key] = $data[$key];
                }
            }
            $useraddress['user_id'] = $userid;
            $res1 = $this->db->insert('user_addresses', $useraddress);
            $new_customer_flag = 1;
        }
        /*-----------------End New Customer Data If Any---------------------------------------*/
        /*------------Dummy data entering in customer don't know role of table--------------------*/
        if($new_customer_flag == 1){
            $this->check_customer_record($userid, @$data['opportunity_id']);
        }
        /*------------End Dummy data entering in customer--------------------*/
        if ($onlycustomer) {
            return array('success' => true, 'message' => 'Your information has been saved.');
        }
        
        
        /*-------------------------- Adding New Site data ---------------------------------------*/
        $sitedata = array();
        foreach ($site_keys as $key) {
            if (isset($data["site_" . $key])) {
                $sitedata[$key] = $data["site_" . $key];
            }
        }
        $sitedata['created_by'] = $logged_user_id;
        $sitedata['created'] = $now;
        $res2 = $this->db->insert('sites', $sitedata);
        $site_id = $this->db->insert_id();
        /*-------------------------- End  New Site data -----------------------------------------*/
        
        
        
        if($new_customer_flag == 1){
            /* Adding site_customers Table Data */
            $site_cutomers_data = array('customer_id' => $userid, 'site_id' => $site_id,'primary' => 1);
            $res4 = $this->db->insert('site_customers', $site_cutomers_data);
        }
        /*----------------------------- Adding associated users/existing users if any --------------------------------*/
        if (isset($data['associatedUsers']) && (!empty($data['associatedUsers']))) {
            $assoc_user = array();
            foreach ($data['associatedUsers'] as $key => $value) {
                $assoc_user['user_id'] = $value;
                $assoc_user['site_id'] = $site_id;
                $this->db->insert('users_sites', $assoc_user);
            }

        }
        
        if (isset($data['addexistingUsers']) && (!empty($data['addexistingUsers']))) {
            $exist_user = array();
            $primary = 0;
            if($new_customer_flag == 0){
                $primary = 1;
            }
            foreach ($data['addexistingUsers'] as $key => $value) {
                $exist_user['customer_id'] = $value;
                $exist_user['site_id'] = $site_id;
                $exist_user['primary'] = $primary;          /*Make First Customer as Primary Customer if any new customer is not added*/
                $this->check_customer_record($value);
                $this->db->insert('site_customers', $exist_user);
                $primary = 0;
            }

           
        }
        
        /*----------------------------- End Adding associated users/existing users --------------------------------*/
        
        if ($res2) {
            return array('success' => true, 'message' => 'Your information has been saved.');
        } else {
            return array('success' => false, 'message' => 'Error.');
        }
    }

    /* public function add_site($data)
      {
      $this->form_validation->set_rules('address', 'Address', 'required');
      $this->form_validation->set_rules('city', 'City', 'required');
      $this->form_validation->set_rules('state', 'State', 'required');
      $this->form_validation->set_rules('zipcode', 'Zipcode', 'required');

      if ($this->form_validation->run() == TRUE)
      {
      foreach ( $data as $key => $value )
      {
      if ( $key   !== 'submit'
      &&   $value !== '' )
      {
      $site[$key] = $value;
      }
      }

      $this->insert($site);

      } else {
      return array('success' => false, 'message' => validation_errors());
      }

      return array('success' => true, 'message' => 'Added site successfully.');
      } */
    public function check_customer_record($userid, $opp_id = NULL){
        $sql = "SELECT * FROM customers where user_id= ?";
            $query = $this->db->query($sql, $userid);
            $res3 = TRUE;
            if (!empty($query) && $query->num_rows() <= 0) {
                /* Adding Customer Table Data */
                $customerdata = array('customer_preferred_contact' => 1, 'sales_modifier_id' => 1, 'user_id' => $userid, 'deleted' => 0, 'customer_referred_by' => 1, 'opportunity_id' => $opp_id);
                $res3 = $this->db->insert('customers', $customerdata);
            }
    }
//    public function add_customer_note($id, $note) {
//        if (!empty($note)) {
//            $customer_id = $this->fetch_primary_customer_id($id);
//
//            if ($customer_id === false) {
//                return false;
//            }
//
//            $sql = 'INSERT INTO notes
//                    (`text`)
//                    VALUES
//                    ( ? );
//            ';
//
//            $this->db->query($sql, $note);
//            $note_id = $this->db->insert_id();
//
//
//            $sql = 'INSERT INTO site_customer_notes
//                   (`site_id`, `customer_id`, `note_id`)
//                   VALUES
//                   ( ? , ? , ? );
//            ';
//            $this->db->query($sql, array($id, $customer_id, $note_id));
//        }
//    }

    public function add_internal_note($id, $note) {
        if (!empty($note)) {
            $sql = 'INSERT INTO notes
                    (`text`)
                    VALUES 
                    ( ? );
            ';

            $this->db->query($sql, $note);
            $note_id = $this->db->insert_id();

            $sql = 'INSERT INTO site_internal_notes 
                    (`site_id`, `note_id`)
                    VALUES 
                    ( ? , ? );
            ';

            $this->db->query($sql, array($id, $note_id));
        }
    }

    public function fetch_primary_customer_id($id) {
        $sql = "SELECT customer_id
                FROM site_customers
                WHERE site_id = ?
                AND `site_customers`.`primary` = 1
                LIMIT 1 ;
        ";

        $query = $this->db->query($sql, $id);

        if (!empty($query) && $query->num_rows() > 0) {
            $result = $query->row();
            $customer_id = $result->customer_id;

            return $customer_id;
        } else {
            return false;
        }
    }

    public function fetch_estimates($site_id) {
        $sql = "SELECT
            estimates.id,
            estimate_total as cost,
            estimates.created,
            created_by_id,
            (SELECT count(*) FROM estimates_has_item WHERE deleted=0 AND estimate_id=estimates.id) as windows,
            CONCAT(`creator`.`first_name`, ' ',`creator`.`last_name`) as created_by,
            CONCAT(`cust`.`first_name`, ' ',`cust`.`last_name`) as customer,
            deal.name as dealer,
            sites.address as job_site
            FROM estimates
            LEFT JOIN estimates_has_customers ON estimates_has_customers.estimate_id=estimates.id AND estimates_has_customers.primary = 1
            LEFT JOIN users cust ON cust.id=estimates_has_customers.customer_id
            INNER JOIN groups AS deal ON estimates.dealer_id = deal.id
            INNER JOIN users AS creator ON estimates.created_by_id = creator.id
            INNER JOIN sites ON estimates.site_id = sites.id
            WHERE estimates.site_id = ? AND estimates.deleted = 0
                ;
                ";

        $query = $this->db->query($sql, $site_id);

        $results = $query->result();

        foreach ($results as $row) {
            $row->job_site_id = $site_id;
        }

        return $results;
    }

    public function fetch_quotes($site_id) {
        $sql = "SELECT quotes.id,
                       quote_total as cost,
                       quotes.created,
                       CONCAT(`creator`.`first_name`, ' ',`creator`.`last_name`) as created_by,
                       CONCAT(`cust`.`first_name`, ' ',`cust`.`last_name`) as customer,
                       (SELECT count(*) FROM quotes_has_item WHERE deleted=0 AND quote_id=quotes.id) as windows,
                       deal.name as dealer,
                       
                       CONCAT(sites.address, ' ', sites.address_ext) as job_site
                FROM   quotes
                INNER JOIN users AS creator ON created_by = creator.id
                LEFT JOIN quotes_has_customers ON quote_id=quotes.id
                LEFT JOIN users cust ON cust.id=quotes_has_customers.customer_id AND quotes_has_customers.primary = 1
                INNER JOIN groups AS deal ON dealer_id = deal.id
                INNER JOIN sites ON site_id = sites.id
                WHERE  site_id = ? AND quotes.deleted = 0
                ;
                ";

        $query = $this->db->query($sql, $site_id);

        $results = $query->result();

        foreach ($results as $row) {
            $row->job_site_id = $site_id;
        }

        return $results;
    }

    /* Added Function to fetch all users to show on Add a user Search box */

    public function getAllUsers($search_text,$class) {
        
        $logged_id = $this->session->userdata['user_id'];
        
        
        $sql = "SELECT users.id as id,users.first_name as first_name,users.last_name as last_name FROM users
                INNER JOIN              users_groups as ugroup
                ON                       ugroup.user_id = users.id
                WHERE ugroup.group_id IN (SELECT group_id FROM users_groups Where users_groups.user_id =  {$logged_id}) AND (users.first_name LIKE '%{$search_text}%' OR users.last_name LIKE '%{$search_text}%' OR users.email_1 LIKE '%{$search_text}%')";
        $query = $this->db->query($sql);
        $html = "<div>";
        if (!empty($query) && $query->num_rows() > 0) {
            $results = $query->result();
            foreach ($results as $row) {
                $html .= "<a href='#' class='".$class."' alt=" . $row->id . ">" . $row->first_name . " " . $row->last_name . "</a><br>";
            }
        }
        $html .="</div>";
        return $html;
        exit;
    }
    
    public function searchUsers($search_text,$class){
        $logged_id = $this->session->userdata['user_id'];
        
        
        $sql = "SELECT * FROM users
                INNER JOIN              users_groups as ugroup
                ON                       ugroup.user_id = users.id
                WHERE ugroup.group_id IN (SELECT group_id FROM users_groups Where users_groups.user_id =  {$logged_id}) AND (users.first_name LIKE '%{$search_text}%' OR users.last_name LIKE '%{$search_text}%' OR users.email_1 LIKE '%{$search_text}%')";
        $query = $this->db->query($sql);
        $html = "<div>";
        if (!empty($query) && $query->num_rows() > 0) {
            $results = $query->result();
            foreach ($results as $row) {
                $html .= "<a href='#' class='".$class."' alt=" . $row->id . ">" . $row->first_name . " " . $row->last_name . "</a><br>";
            }
        }
        $html .="</div>";
        return $html;
        exit;
    }


    public function update_site_form($site_id,$data){
        $info = array();
        $user_keys = array('first_name', 'last_name', 'organization_name', 'email_1', 'email_type_1', 'email_2', 'email_type_2', 'phone_1', 'phone_type_1', 'phone_2', 'phone_type_2', 'phone_3', 'phone_type_3', 'company_id');
        $user_address_keys = array('address_type', 'address', 'address_ext', 'country', 'city', 'state', 'zipcode');
        $logged_user_id = $this->session->userdata['user_id'];
        /* Adding Users Table Data */
        $userdata = elements($user_keys, $data);
        $res0 = $this->db->insert('users', $userdata);
        $userid = $this->db->insert_id();
        $info['id'] = $userid;
        $info['name'] = $userdata['first_name']." ".$userdata['last_name'];
        $info['siteid'] = $site_id;
        /* Adding User Address data */
        $useraddress = elements($user_address_keys, $data);
        $useraddress['user_id'] = $userid;
        $res1 = $this->db->insert('user_addresses', $useraddress);
        
        $sql = "SELECT * FROM customers where user_id= ?";
        $query = $this->db->query($sql, $userid);
        $res3 = TRUE;
        if (!empty($query) && $query->num_rows() <= 0) {
            /* Adding Customer Table Data Currently dummy*/
            $customerdata = array('customer_company_name' => $userdata['organization_name'], 'customer_preferred_contact' => 1, 'sales_modifier_id' => 1, 'user_id' => $userid, 'deleted' => 0, 'customer_referred_by' => 1);
            $res3 = $this->db->insert('customers', $customerdata);
        }
        
        
        /* Adding site_customers Table Data */
        $site_cutomers_data = array('customer_id' => $userid, 'site_id' => $site_id);
        $res2 = $this->db->insert('site_customers', $site_cutomers_data);
        
        if ($res0 && $res1 && $res2) {
            return array('success' => true, 'message' => 'Your information has been saved.','info' => $info);
        } else {
            return array('success' => false, 'message' => 'Error.');
        }
        
    }
    
    
    public function update_site_primary($siteid, $id){
        $sql = "UPDATE site_customers set site_customers.primary=0 WHERE site_id = ?";
        $res1 = $this->db->query($sql, $siteid);
        
        $sql = "UPDATE site_customers set site_customers.primary=1 WHERE site_id = {$siteid} AND customer_id ={$id}";
        $res2 = $this->db->query($sql);
        if($res2)
        {
            return true;
        }
        else{
            return false;
        }
        
        
        
    }

    public function delete_associated_user($id, $siteid){
         $res = $this->db->delete('site_customers', array('customer_id' => $id, 'site_id' => $siteid)); 
         if($res){
             return true;
         }else{
             return false;
         }
    }
    
    public function add_associated_user($id, $siteid){
        $data = array('customer_id' => $id, 'site_id' => $siteid,'primary' => 0);
        $res = $this->db->insert('site_customers', $data);
        if($res){
            return true;
        }else{
            return false;
        }
    }
    
    public function user_info($id){
        $sql = "SELECT          users.id AS id,
                                groups.name AS group_name,
                                users.first_name as first_name,
                                users.last_name as last_name,
                                users.username as username,
                                user_addresses.zipcode as zipcode
                FROM            users
                LEFT JOIN       user_addresses
                ON              user_addresses.user_id = users.id AND user_addresses.address_type = 'address1'
                LEFT JOIN       groups
                ON              groups.id = users.id
                WHERE           users.deleted = 0 AND users.id = ?
                ;
                ";

        $data = $this->db->query($sql,$id)->result();
        return $data;
    }

    public function get_site_options($customer_ids) {
        if (!count($customer_ids)) {
            return;
        }
        $data = $this->db
                    ->select("
                        sites.id,
                        address,
                        address_ext,
                        city,
                        state,
                        zipcode,
                        address_type
                    ", false)
                    ->from('sites')
                    ->join('site_customers', 'site_id=sites.id')
                    ->where('sites.deleted', 0)
                    ->where_in('customer_id', $customer_ids)->get()->result();
        return $data;
    }

    public function create_from_address($address_id) {
        $user_id = $this->ion_auth->get_user_id();
        $address = $this->db->where('id', $address_id)->get('user_addresses')->row();
        $site = array(
            'address' => $address->address,
            'address_ext' => $address->address_ext,
            'city' => $address->city,
            'state' => $address->state,
            'zipcode' => $address->zipcode,
            'created' => date('Y-m-d H:i:s'),
            'created_by' => $user_id,
        );
        $site_id = $this->create_site($site);
        $this->db->insert('site_customers', array(
            'customer_id'   => $address->user_id,
            'site_id'       => $site_id,
            'primary'       => 1,
        ));
        return $site_id;
    }

    public function add_note($sid, $note, $internal=false, $customer_id=null, $user_id=null) {
        if (!empty($note)) {
            $note = array(
                'text' => $note,
                'created' => date('Y-m-d H:i:s'),
                'user_id' => $this->ion_auth->get_user_id() ? $this->ion_auth->get_user_id() : $user_id
            );
            $this->db->insert('notes', $note);
            $note_id = $this->db->insert_id();
            if($internal){
                $this->db->insert('site_internal_notes', array('site_id' => $sid, 'note_id' => $note_id));
            } else {
                $this->db->insert('site_customer_notes', array('site_id' => $sid, 'note_id' => $note_id, 'customer_id' => $customer_id));
            }
        }
    }

    protected function is_legacy($order) {
        $time = empty($order) ? time() : strtotime($order->created);
        return date('Y-m-d H:i:s', $time) < $this->config->item('legacy_date');
    }

    public function copy_from_order_hold_items($order_id, $oitems) {
        $order = $this->db->where('id', $order_id)->get('orders')->row();
        $site = $this->db->where('id', $order->site_id)->get('sites')->row_array();
        if (!$site) {
            return false;
        }

        //Make all items measured, otherwise they won't ever show up on the job site, and that doesn't really make sense.
        //It makes sense for sites measured by techs, but with no tech there's no way to measure the window so it will always be hidden.
        foreach ($oitems as &$item) {
            $item['measured'] = 1;
            if ($this->is_legacy($order)) {
                if ($item['freebird_laser']) {
                    foreach ($item['measurements'] as &$measurement) {
                        $measurement += 1;
                    }
                }
            } else {
                if (empty($item['own_tools'])) {
                    if (isset($item['measurements']['E']) && $item['measurements']['E'] !== '') {
                        @$item['measurements']['E'] += 2;
                    }
                    if (isset($item['measurements']['F']) && $item['measurements']['F'] !== '') {
                        @$item['measurements']['F'] += 2;
                    }
                }
            }
        }
        $this->item_model->save_site_items($oitems, $order->site_id, true);
        $this->db->query('UPDATE orders_has_item JOIN items ON items.id=orders_has_item.item_id SET orders_has_item.deleted=1 WHERE manufacturing_status=2 AND orders_has_item.order_id=?', $order_id);

        return $order->site_id;
    }

	public function get_tech($site_id) {
		$row = $this->db
				->select("users.id, CONCAT(first_name, ' ', last_name) AS name", false)
				->where('site_id', $site_id)
				->join('users', 'tech_id=users.id')
				->get('sites_techs')->row();
		return $row;
	}

	public function reassign_tech($site_id, $tech_id) {
		$this->db->where('site_id', $site_id)->delete('sites_techs');
		if ($tech_id) {
			$time = date('Y-m-d H:i:s');
			$this->db->insert('sites_techs', array('site_id' => $site_id, 'tech_id' => $tech_id));
		} else {
			$time = null;
			$tech_id = null;
		}
		$this->db->where('site_id', $site_id)->update('estimates', array('tech_id' => $tech_id, 'tech_assigned' => $time));
	}

    public function get_site_orders($site_id) {
        $sites = $this->db
                ->select("orders.id, CONCAT(first_name,  ' ', last_name) AS customer", false)
                ->join('orders_has_customers', 'orders_has_customers.order_id=orders.id AND orders_has_customers.primary=1', 'left')
                ->join('users', 'users.id=orders_has_customers.customer_id', 'left')
                ->where('orders.deleted', 0)
                ->where('site_id', $site_id)
                ->get('orders')->result();
        return $sites;
    }

    public function measurements_editable($site_id) {
        $tech = $this->db
                ->where('site_id', $site_id)
                ->get('sites_techs')
                ->row();
        return !$tech;
    }
}
