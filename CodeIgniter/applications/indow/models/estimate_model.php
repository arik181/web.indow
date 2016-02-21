<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Estimate_model extends MM_Model
{
    protected $_table      = 'estimates';
    protected $soft_delete = TRUE;
    protected $_key        = 'id';
    
    public    $id;                // integer
    public    $history_id;        // integer
    public    $created;           // datetime
    public    $created_by_id;     // integer
    public    $closed;            // boolean
    public    $dealer_id;         // integer
    public    $processed;         // boolean
    public    $total_square_feet; // double
    public    $estimate_total;    // double
    public    $quote_id;          // integer
    public    $quote_created_by;  // integer
    public    $order_id;          // integer
    public    $order_created_by;  // integer
    public    $parent_group_id;   // integer
    public    $customer_id;       // integer
    public    $site_id;           // integer
    protected $user_id = 1; //update

    public function __construct()
    {
        parent::__construct();
        $key = $this->_key;
        if ($this->$key)
            return;
        $this->load->model('History_model');
        $this->created = date("Y-m-d H:i:s");
        $this->created_by_id = $this->ion_auth->get_user_id();
        $this->closed = 0;
        $this->processed = 0;
    }

    public function create($estimate) 
    {
        $this->db->insert('estimates', $estimate);
        return $this->db->insert_id();
    }

    public function update($estimate_id, $data) 
    {
        $keys = array('total_square_feet', 'estimate_total', 'name', 'parent_estimate_id', 'customer_id', 'site_id', 'followup', 'closed', 'reason_for_closing');
        $estimate = array();
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $estimate[$key] = $data[$key];
            }
        }
        $this->db->where('id', $estimate_id);
        $this->db->update('estimates', $estimate);
    }

    public function saveitems($data, $estimate_id, $forcenew=false) 
    {
        foreach ($data as $item) {
            unset($item['checked']);
            $children = false;
            if (isset($item['children']) && count($item['children'])) {
                $children = $item['children'];
            }
            if ((isset($item['room']) && $item['room'] === '') || ($item['id'] === 'new' && empty($item['room']))) {
                $item['room'] = 'unknown';
            }
            unset($item['children']);
            if ($item['id'] === 'new' || $forcenew) {
                unset($item['id']);
                unset($item['product']);
                $item['frame_depth_id'] = 1;
                $this->db->insert('items', $item);
                $itemid = $this->db->insert_id();
                $estimates_has_item = array(
                    'estimate_id' => $estimate_id,
                    'item_id' => $itemid
                );
                $this->db->insert('estimates_has_item', $estimates_has_item);
            } else {
                $this->db->where('id', $item['id']);
                $this->db->update('items', $item);
                $itemid = (integer) $item['id'];            
            }
            if ($children) {
                foreach ($children as $subitem) {
                    $subitem['item_id'] = $itemid;
                    unset($subitem['checked']);
                    unset($subitem['price']);
                    if ($subitem['id'] === 'new' || $forcenew) {
                        unset($subitem['id']);
                        $this->db->insert('subitems', $subitem);
                    } else {
                        $this->db->where('id', $subitem['id']);
                        $this->db->update('subitems', $subitem);
                    }
                }
            }
        }
    }

    public function set_estimate_customers($estimate_id, $customers) {
        $this->db->where('estimate_id', $estimate_id);
        $this->db->delete('estimates_has_customers');
        foreach ($customers as $customer) {
            $customer['estimate_id'] = $estimate_id;
            $customer['customer_id'] = $customer['id'];
            unset($customer['id']);
            $this->db->insert('estimates_has_customers', $customer);
        }
    }
    
    public function delete_items($allitems, $estimate_id) {
        $items = array();
        $subitems = array();
        foreach ($allitems as $item) {
            if ($item['type'] === 'subitem') {
                $subitems[] = $item['id'];
            } else {
                $items[] = $item['id'];
            }
        }

        if (count($items)) {
            $this->db->where_in('item_id', $items)->where('estimate_id', $estimate_id);
            $this->db->update('estimates_has_item', array('deleted' => 1));
        }
        if (count($subitems)) {
            $this->db->where_in('id', $subitems);
            $this->db->update('subitems', array('deleted' => 1));
        }
    }

    public function _save()
    {
        $sql = "";
        if ($this->id > 0) // update operation
        {
            $sql = "UPDATE      $this->_table
                    SET         history_id = ?,
                                created = ?,
                                created_by_id = ?,
                                closed = ?,
                                dealer_id = ?,
                                processed = ?,
                                total_square_feet = ?,
                                estimate_total = ?,
                                quote_id = ?,
                                quote_created_by = ?,
                                order_id = ?,
                                order_created_by = ?,
                                parent_group_id = ?,
                                customer_id = ?,
                                site_id = ?
                    WHERE       id = ?";
        }
        else //insert operation
        {
            $history = new History_model();
            $history->save();
            $this->history_id = $history->id;
            $sql = "INSERT INTO $this->_table
                    SET         history_id = ?,
                                created = ?,
                                created_by_id = ?,
                                closed = ?,
                                dealer_id = ?,
                                processed = ?,
                                total_square_feet = ?,
                                estimate_total = ?,
                                quote_id = ?,
                                quote_created_by = ?,
                                order_id = ?,
                                order_created_by = ?,
                                parent_group_id = ?,
                                customer_id = ?,
                                site_id = ?";
        }
        $this->db->query($sql,array($this->history_id, $this->created, $this->created_by_id, $this->closed, $this->dealer_id, $this->processed, $this->total_square_feet, $this->estimate_total, $this->quote_id, $this->quote_created_by, $this->order_id, $this->order_created_by, $this->parent_group_id, $this->customer_id, $this->site_id,$this->id));
        if ($this->id == 0)
            $this->id = $this->db->insert_id();

        return array('success' => true, 'message' => 'Updated estimate successfully.');
    }

    public function getProfile($id)
    {
        $sql = '
            SELECT  *
            FROM    estimates
            WHERE   estimates.id = ?;
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


    public function fetch_estimates($limit, $start)
    {
        /*
            <td id="estimate"><?= $estimate->name; ?></td>
            <td id="address"><?= $estimate->address; ?></td>
            <td id="windows"><?= $estimate->windows; ?></td>
            <td id="cost"><?= $estimate->cost; ?></td>
            <td id="created"><?= $estimate->created; ?></td>
            <td id="created_by"><?= $estimate->created_by; ?></td>
            <td id="dealer"><?= $estimate->dealer; ?></td>

        $sql = "SELECT      estimates.id,
                            CONCAT(`user`.`first_name`,' ',`user`.`last_name`) as name,
                            sites.address as address,
                            users.phone_1 as phone,
                            users.email_1 as email
                FROM        estimates
                INNER JOIN  customers ON customer_id = customers.id
                INNER JOIN  users ON user_id  = users.id
                INNER JOIN  site_estimates ON estimate_id  = estimates.id
                INNER JOIN  sites ON site_id  = sites.id
                WHERE       estimates.deleted = 0
                LIMIT       ?, ?";
         */
        $sql = "SELECT *
                FROM estimates
            ";

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

    function get_subitems($estimate_id) {
        $this->db->select('subitems.*');
        $this->db->from('estimates_has_item');
        $this->db->join('subitems', 'subitems.item_id = estimates_has_item.item_id');
        $rows = $this->db->where('estimate_id', $estimate_id)->where('estimates_has_item.deleted', 0)->where('subitems.deleted', 0)->get()->result();
        $subitems = array();
        foreach ($rows as $row) {
            if (isset($subitems[$row->item_id])) {
                $subitems[$row->item_id][] = $row;
            } else {
                $subitems[$row->item_id] = array($row);
            }
        }
        return $subitems;
    }
    
    public function fetch_primary_customer($id)
    {
        $sql = "SELECT          CONCAT(`first_name`,' ',`last_name`) as name,
                                phone_1,
                                email_1
                FROM            sites
                INNER JOIN      site_customers
                ON sites.id     = site_customers.site_id
                INNER JOIN      customers
                ON customers.id = site_customers.customer_id
                INNER JOIN      users
                ON customers.user_id = users.id
                WHERE sites.id  = ?
                AND             `site_customers`.primary = 1
                ;
                ";

        $query = $this->db->query($sql, $id);

        if ( ! empty($query)  && $query->num_rows() > 0) 
        {
            foreach ($query->result() as $row) 
            {
                $data[] = $row;
            }

            return $data[0];
        }

        return false;
    }

    public function fetch_existing_customers($id)
    {
        $sql = "SELECT          CONCAT(`first_name`,' ',`last_name`) as name,
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

        if ( ! empty($query)  && $query->num_rows() > 0) 
        {
            foreach ($query->result() as $row) 
            {
                $data[] = $row;
            }

            return object_to_array($data);
        }

        return false;
    }

    public function fetch_site_info($id)
    {
        $sql = "SELECT          address, 
                                address_ext,
                                CONCAT(city, ', ', state, ' ', zipcode) as city_state_zipcode, 
                                IF( address_type = 0, 'Type: Residential', 'Type: Business' ) as address_type
                FROM            sites
                WHERE sites.id  = ?
                ;
                ";

        $query = $this->db->query($sql, $id);

        if ( ! empty($query)  && $query->num_rows() == 1) 
        {
            $data = $query->result();
            return object_to_array($data[0]);
        }

        return false;
    }

    public function fetch_window_totallist($id)
    {
        $sql = "SELECT                item.id AS window, 
                                      room, max_width, max_height, 
                                      product, product_type, edging, retail
                FROM                  sites
                INNER JOIN            site_items
                ON sites.id           = site_items.site_id
                INNER JOIN            items
                ON items.id           = site_items.item_id
                INNER JOIN            window_attributes
                ON items.attribute_id = window_attributes.id
                WHERE sites.id  = ?
                ;
                ";

        $query = $this->db->query($sql, $id);

        if ( ! empty($query)  && $query->num_rows() > 0) 
        {
            foreach ($query->result() as $row) 
            {
                $data[] = $row;
            }

            return object_to_array($data);
        }
        return false;
    }

    public function delete($id)
    {
        $this->db->trans_start();
        $sql = "UPDATE $this->_table 
                SET    deleted = 1
                WHERE  id = ?
        ";
        $query = $this->db->query($sql,$id);
        $this->db->trans_complete();

        return $query;
    }

    public function export_package($data)
    {
    }

    public function fetch_primary_customer_id($id)
    {
        $sql = "SELECT customer_id
                FROM site_customers
                WHERE site_id = ?
                AND `site_customers`.`primary` = 1
                LIMIT 1 ;
        ";

        $query = $this->db->query($sql, $id);

        if ( ! empty($query)  && $query->num_rows() > 0) 
        {
             $result = $query->row();
             $customer_id = $result->customer_id;

             return $customer_id;

        } else {
            return false;
        }
    }

    public function get_primary_customer_name($estimate_id) {
        $user = $this->db
                ->select("CONCAT(users.first_name, ' ', users.last_name) AS name", false)
                ->where('primary', 1)->where('estimate_id', $estimate_id)
                ->join('users', 'users.id=estimates_has_customers.customer_id')
                ->get('estimates_has_customers')->row();
        return $user ? $user->name : false;
    }

    public function promote($data)
    {
        // TODO will need to call order model
        // TODO will need to set links between parent/child
    }

    public function get_active()
    {
        return "";
    }

    public function get_follow_up()
    {
        return "";
    }

    public function get_admin_follow_up()
    {
        $sql = "SELECT concat('/estimates/edit/', e.id) AS `edit`,
                'Estimate' as `type`,
                CONCAT(users.first_name, ' ', users.last_name) AS `customer`,
                concat('<input type=checkbox id=estimate/', e.id, ' onClick=\"done(this);\">') AS `done`
            FROM estimates e
            LEFT JOIN estimates_has_customers ON (estimates_has_customers.estimate_id=e.id AND estimates_has_customers.primary=1)
            LEFT JOIN users ON users.id=estimates_has_customers.customer_id
            WHERE followup = 1";

        $query = $this->db->query($sql);
        return $query->result();
    }

    function get_saved_estimates($id)
    {
        if (!$id) {
            return array();
        }
        $est = $this->db->where('id', $id)->get('estimates')->row();
        if (!empty($est->parent_estimate_id)) {
            $id = $est->parent_estimate_id;
        }
        return $this->db->select('id, name')->where('parent_estimate_id', $id)->where('deleted', 0)->get('estimates')->result();
    }

    public function create_from_site($site_id) {
        $this->load->model(array('customer_model', 'site_model'));
        $dealer_id = $this->site_model->get_dealer_id($site_id);
        $user_id = $this->ion_auth->get_user_id();
        $estimate = array(
            'dealer_id' => $dealer_id,
            'created_by_id' => $user_id,
            'history_id' => 1,
            'created' => date('Y-m-d H:i:s'),
            'site_id' => $site_id
        );
        $estimate_id = $this->create($estimate);
        $this->customer_model->copy_customers('jobsite', $site_id, 'estimate', $estimate_id);
        return $estimate_id;
    }

    public function get_counts ($id)
    {
        $sql = "SELECT COUNT(`id`) AS `count` FROM `estimates`
                    WHERE `created_by_id` = ?
                    AND DATE_FORMAT(`created`, '%Y%m') = DATE_FORMAT(NOW(), '%Y%m');";

        $query = $this->db->query($sql, $id);
        $result = $query->result();
        return $result[0];
    }

    public function get_followup ($id)
    {
        $sql = "SELECT concat('/estimates/edit/', e.id) AS `edit`,
                'Estimate' as `type`,
                CONCAT(users.first_name, ' ', users.last_name) AS `customer`,
                concat('<input type=checkbox id=estimate/', e.id, ' onClick=\"done(this);\">') AS `done`
            FROM estimates e
            LEFT JOIN estimates_has_customers ON (estimates_has_customers.estimate_id=e.id AND estimates_has_customers.primary=1)
            LEFT JOIN users ON users.id=estimates_has_customers.customer_id
            WHERE dealer_id = ? AND followup = 1";

        $query = $this->db->query($sql, $id);
        return $query->result();
    }    

    public function followup ($id)
    {
        $data = array('followup' => 0);
        $this->db->where('id', $id);
        $this->db->update($this->_table, $data); 
    }

    public function add_note($estimate_id, $note) {
        if (!empty($note)) {
            $note = array(
                'text' => $note,
                'created' => date('Y-m-d H:i:s'),
                'user_id' => $this->ion_auth->get_user_id()
            );
            $this->db->insert('notes', $note);
            $note_id = $this->db->insert_id();
            $this->db->insert('estimates_notes', array('estimate_id' => $estimate_id, 'note_id' => $note_id));
        }
    }

    public function fetch_estimate_notes_history($id)
    {
        $sql = "SELECT          notes.*, CONCAT(users.first_name,  ' ', users.last_name) AS name
                FROM      estimates_notes
                INNER JOIN      notes
                ON notes.id     = estimates_notes.note_id
                LEFT JOIN users ON users.id = notes.user_id
                WHERE estimate_id = ?
                ORDER BY notes.id DESC
                ;
                ";

        return $this->db->query($sql, $id)->result();
    }

    public function assign_tech($estimate_id, $tech_id) {
        $this->load->model('email_model');
        $this->db->where('id', $estimate_id)->update('estimates', array(
            'tech_id' => $tech_id,
            'tech_assigned' => date('Y-m-d H:i:s')
        ));
        $site_id = $this->db->select('site_id')->where('id', $estimate_id)->get('estimates')->row()->site_id;
        $row = $this->db->where('site_id', $site_id)->where('tech_id', $tech_id)->get('sites_techs')->row();
        if (!$row) {
            $this->db->insert('sites_techs', array(
                'site_id' => $site_id,
                'tech_id' => $tech_id
            ));
        }
        $this->email_model->send_tech_email($estimate_id, $tech_id);
    }

    public function customer_estimate_csv($estimate_id){
        $this->load->dbutil();
        $sql = "
            SELECT
            items.id as item_id,
            items.room,
            items.location,
            items.width,
            items.height,
            products.product,
            pt2.description as product_type,
            edging.name as tubing,
            IF(special_geom=1,'Yes','No') AS special_geom,
            items.price as retail,
            GROUP_CONCAT(pt.description, ' (Qty:', subitems.quantity,')') AS assoc_products
            FROM estimates_has_item
            LEFT JOIN items ON items.id=estimates_has_item.item_id
            LEFT JOIN subitems ON items.id = subitems.item_id
            LEFT JOIN product_types as pt ON subitems.product_type_id = pt.id
            LEFT JOIN product_types as pt2 ON items.product_types_id = pt2.id
            LEFT JOIN products ON pt2.product_id=products.id
            LEFT JOIN edging ON edging.id=items.edging_id
            WHERE estimates_has_item.deleted = 0 AND estimates_has_item.estimate_id = 1
            GROUP BY items.id
            ;
        ";
        $query = $this->db->query($sql, $estimate_id);
        $result = $this->dbutil->csv_from_result($query);
        return $result;
    }


    public function getEstimatesForCurrentMonth($user_id = 0)
    {


        if($user_id == 0)
        {
            $this->db->where('created >', date("Y-m-01 00:00:00"));
            return $this->db->where('parent_estimate_id', 0)->get('estimates')->result();
        }
        else
        {
            $this->db->where('created_by_id', $user_id);
            $this->db->where('created >', date("Y-m-01 00:00:00"))->where('parent_estimate_id', 0);
            return $this->db->get('estimates')->result();

        }

    }

    public function change_owner($estimate_id, $owner_id) {
        $owner = $this->db->where('user_id', $owner_id)->get('users_groups')->row();
        $group = $owner ? $owner->group_id : null;
        $this->db->where('id', $estimate_id)->update('estimates', array('created_by_id' => $owner_id, 'dealer_id' => $group));
    }

    public function get_tech($estimate_id) {
		$row = $this->db
				->select("users.id, CONCAT(first_name, ' ', last_name) AS name", false)
				->where('estimates.id', $estimate_id)
				->join('users', 'tech_id=users.id')
				->get('estimates')->row();
		return $row;
	}
}
