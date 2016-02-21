<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class sales_modifiers_model extends MM_Model 
{
    protected	$_table 		= 	'sales_modifiers';
    protected   $_key        = 'id';
    protected	$_group_table 	= 	'sales_modifiers_groups';
    /*
		TODO ->	DO ABSOLUTELY WITH NO EXCEPTION AND AS SOON AS POSSIBLE UPDATE THIS ONE
		LINE OF CODE.  VERY BAD THINGS WILL HAPPEN IF YOU DO NOT.
    */
    protected	$_user_id		=	1;
    
    /*
    	Function addModifier -> Add a new sales modifier
    	
    	$modifier_type 	-> discount, or fee
    	$code 			-> modifier code
    	$description	-> human readable modifier description
    	$amount			-> amount modifier applied
    	$modifier		-> dollar, or percent
    	$start_date		-> discount start date (null for fee)
    	$end_date		-> discount end date (null for fee)
    */
    function addModifier 	( $modifier_type, $code, $description
    						, $amount, $modifier, $start_date = NULL
    						, $end_date = NULL )
    {
    	/*
    		If this modifier is a fee, we set the start and end date to NULL, this reduces
    		the chances for SQL errors, or confusing results.
    	*/
    	if ( $modifier_type == 'fee' )
    	{
    		$start_date 	= NULL;
    		$end_date		= NULL;
    	}
    	
    	// Build array for inserting data into the table
    	$insert = array(
    		'modifier_type'		=>	$modifier_type,
    		'code'				=>	$code,
    		'description'		=>	$description,
    		'amount'			=>	$amount,
    		'modifier'			=>	$modifier,
    		'start_date'		=>	$start_date,
    		'end_date'			=>	$end_date
    	);
    	
    	// Inserting the data into the table
    	$this->db->insert($this->_table, $insert);
    	
    	// Retrieving modifier_id
    	$modifier_id = $this->db->insert_id();
    	
    	// Get user groups
    	$groups = $this->_getGroups();
    	
    	// Save modifiers in groups table
    	foreach( $groups as $group )
    	{
    		$this->db->insert($this->_group_table, array(
    			'sales_modifiers_id' => $modifier_id, 
    			'groups_id' => $group->group_id
    			)
    		);
    	}
    }
    
    /*
    	Function updateModifier -> Update an existing sales modifier
    	
    	$id				-> ID of sales modifier
    	$modifier_type 	-> discount, or fee
    	$code 			-> modifier code
    	$description	-> human readable modifier description
    	$amount			-> amount modifier applied
    	$modifier		-> dollar, or percent
    	$start_date		-> discount start date (null for fee)
    	$end_date		-> discount end date (null for fee)    	
    */
    function updateModifier	( $id, $post )
    {
	    /*
    		If this modifier is NOT a (fee, wholesale, msrp), we set the start and end date to NULL, this reduces
    		the chances for SQL errors, or confusing results.
    	*/
    	if ( $post['modifier_type'] != 'discount' )
    	{
    		$start_date 	= NULL;
    		$end_date		= NULL;
    	} else {
            $start_date 	= date('Y-m-d', strtotime($post['start_date']));
            $end_date		= date('Y-m-d', strtotime($post['end_date']));
        }
    	
    	// Build array for updating data in the table
    	$update = array(
    		'modifier_type'		=>	$post['modifier_type'],
    		'code'				=>	$post['code'],
    		'description'		=>	$post['description'],
    		'amount'			=>	$post['amount'],
    		'modifier'			=>	$post['modifier'],
    		'start_date'		=>	$start_date,
    		'end_date'			=>	$end_date
    	);
    	
    	// Identify the existing Modifier
    	$this->db->where('id', $id);
    	// Updating the modifier
    	$this->db->update($this->_table, $update);
        $this->db->delete($this->_group_table, array('sales_modifiers_id' => $id));


        // Save modifiers in groups table
        $this->db->insert($this->_group_table, array(
                'sales_modifiers_id' => $id,
                'groups_id' => $post['groups_id']
            )
        );
    }
    
    /*
    	Function deleteModifier -> Deletes an exisiting sales modifier
    	
    	$id				->	ID of sales modifier
    */
    function deleteModifier($id)
    {
        /* Only update field deleted to 1 for deleting record */
        $this->db->where('id', $id);
        return $this->db->update('sales_modifiers', array('deleted' => 1));
    	//return $this->db->delete('sales_modifiers', array('id' => $id));
    }  
    
    /*
    	Function getModifier -> Retrieves data about modifier(s)
    
    	$id				->	ID of the sales modifiers (optional)
    */
    function getModifier ( $limit, $start, $id = NULL )
    {
    	// Get group IDs first, will help prevent security problems from arising
    	$groups = $this->_getGroups();
    	
    	// Cycle through and build a simple array out of the groups
        $in_groups = array();
    	foreach($groups as $group)
    	{
    		$in_groups[] = $group->group_id;
    	}
    	
    	// Get sales modifier ids based off of what groups the user is in
    	$this->db->where_in('groups_id', $in_groups);
        $query = $this->db->get('sales_modifiers_groups');
    	$modifiers = $query->result();
    	
    	// Cycle through and build a simple array to get the right modifiers
    	foreach($modifiers as $modifier)
    	{
    		$modifier_ids[] = $modifier->sales_modifiers_id;
    	}
    	
    	// Apply modifier ids to the query
    	$this->db->where_in('id', $modifier_ids);
    	
    	// If the ID is sent, just get the info on the desired modifier
    	if($id != NULL)
    	{
	    	$this->db->where('id', $id);
	    } else {
		    $this->db->limit($limit,$start);
		}
	    $return = $this->db->get( $this->_table );
	    return $return->result();
    }
    
    /*
    	Function _getGroups -> Retrieves all the groups a user is in
    */
    function _getGroups ( )
    {
    	$query = $this->db->get_where('users_groups', array('user_id' => $this->_user_id));
    	return $query->result();

    }
    
    public function fetch_discounts($limit, $start) {
        $sql = 'SELECT  *
                FROM   `sales_modifiers`
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
    
    
    /*Added new functions to Add/Edit/Delete Discounts*/
    public function add_discounts($data) {

        if(isset($data['start_date']) || isset($data['end_date'])){
                $timestamp = strtotime($data['start_date']);
                $data['start_date'] = date("Y-m-d H:i:s", $timestamp);
                $timestamp = strtotime($data['end_date']);
                $data['end_date'] = date("Y-m-d H:i:s", $timestamp);
        }

        $discountdata = array();
        $discount_keys = array('modifier_type', 'description', 'amount', 'modifier', 'code', 'start_date', 'end_date', 'created_by');
        foreach ($discount_keys as $key) {
            if (isset($data[$key])) {
                $discountdata[$key] = $data[$key];
            }
        }
        $discountdata['created_by'] = $this->_user->id;
        $res = $this->db->insert($this->_table, $discountdata);
        $discount_id = $this->db->insert_id();

        if(isset($data['groups_id']) && $data['groups_id']){
            $groups = array(
                'sales_modifiers_id'=>$discount_id,
                'groups_id'=>$data['groups_id']
            );
            $this->db->insert($this->_group_table, $groups);
        }

        if ($res) {
            return array('success' => true, 'message' => 'Your information has been saved.');
        } else {
            return array('success' => false, 'message' => 'Error.');
        }
    }
    
    public function get_discount($id = NULL) {
        /*$sql = '
            SELECT  *
            FROM    sales_modifiers
            WHERE   sales_modifiers.id = ?;
            ';
           */

        $this->db->join($this->_group_table, 'sales_modifiers_groups.sales_modifiers_id = sales_modifiers.id', 'left');
        $query = $this->db->get_where($this->_table, array('sales_modifiers.id' => $id));


        if (!empty($query) && $query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function get_estimate_fee_ids($estimate_id, $all=false) {
        $this->db
                ->from('estimates_has_fees')
                ->join('sales_modifiers', 'sales_modifier_id=sales_modifiers.id')
                ->where('estimate_id', $estimate_id);
        if (!$all) {
            $this->db->where('single_use', 0);
        }
        $fees = $this->db->get()->result();
        $simple = array();
        foreach ($fees as $fee) {
            $simple[] = (integer) $fee->sales_modifier_id;
        }
        return $simple;
    }
    public function get_quote_fee_ids($estimate_id, $all=false) {
        $this->db
                ->from('quotes_has_fees')
                ->join('sales_modifiers', 'sales_modifier_id=sales_modifiers.id')
                ->where('quote_id', $estimate_id);
        if (!$all) {
            $this->db->where('single_use', 0);
        }
        $fees = $this->db->get()->result();
        $simple = array();
        foreach ($fees as $fee) {
            $simple[] = (integer) $fee->sales_modifier_id;
        }
        return $simple;
    }

    protected function assoc_id($list) {
        $assoc = array();
        foreach($list as $item) {
            $assoc[$item->id] = $item;
        }
        return $assoc;
    }

    public function get_order_fees($order_id, $single_use=0) {
        $this->db->select('sales_modifiers.*')
                ->from('orders_has_fees')
                ->join('sales_modifiers', 'sales_modifier_id=sales_modifiers.id')
                ->where('order_id', $order_id);
        if ($single_use != 'all') {
            $this->db->where('single_use', $single_use);
        }
        $results = $this->db->get()->result();
        foreach ($results as $result) {
            $result->quantity = $result->single_use ? $result->quantity : 1;
        }
        if ($single_use) {
            return $this->assoc_id($results);
        } else {
            return $results;
        }
    }

    public function get_quote_fees($quote_id, $single_use=0) {
        $results = $this->db->select('sales_modifiers.*')
                ->from('quotes_has_fees')
                ->join('sales_modifiers', 'sales_modifier_id=sales_modifiers.id')
                ->where('quote_id', $quote_id)->where('single_use', $single_use)
                ->get()->result();
        if ($single_use) {
            return $this->assoc_id($results);
        } else {
            return $results;
        }
    }

    public function get_estimate_fees($estimate_id, $single_use=0) {
        $results = $this->db->select('sales_modifiers.*')
                ->from('estimates_has_fees')
                ->join('sales_modifiers', 'sales_modifier_id=sales_modifiers.id')
                ->where('estimate_id', $estimate_id)->where('single_use', $single_use)
                ->get()->result();
        if ($single_use) {
            return $this->assoc_id($results);
        } else {
            return $results;
        }
    }

    public function get_order_fee_ids($order_id) {
        $fees = $this->db
                ->from('orders_has_fees')
                ->join('sales_modifiers', 'sales_modifier_id=sales_modifiers.id')
                ->where('order_id', $order_id)
                ->where('single_use', 0)
                ->get()->result();
        $simple = array();
        foreach ($fees as $fee) {
            $simple[] = (integer) $fee->sales_modifier_id;
        }
        return $simple;
    }

    public function get_modifier_amount ($m, $subtotal) {
        if ($m->modifier === 'dollar') {
            $amount = (float) $m->amount;
        } if ($m->modifier === 'percent') {
            $amount = $m->amount * $subtotal / 100;
        }
        if ($m->modifier_type === 'discount') {
            return $amount * -1 * $m->quantity;
        } else {
            return $amount * $m->quantity;
        }
    }

    public function get_modifier_totals($modifiers) {
        $totals = array(
            'fees_dollars'      => 0,
            'discounts_dollars' => 0,
            'fees_percent'      => 0,
            'discounts_percent' => 0,
            'taxes_percent' => 0,
            'taxes_dollars' => 0,
        );
        foreach ($modifiers as $m) {
            if ($m->modifier_type === 'fee') {
                if ($m->modifier === 'dollar') {
                    $totals['fees_dollars'] += $m->amount;
                } elseif ($m->modifier === 'percent') {
                    $totals['fees_percent'] += $m->amount;
                }
            } elseif ($m->modifier_type === 'discount') {
                if ($m->modifier === 'dollar') {
                    $totals['discounts_dollars'] += $m->amount;
                } elseif ($m->modifier === 'percent') {
                    $totals['discounts_percent'] += $m->amount;
                }
            } elseif ($m->modifier_type === 'tax') {
                if ($m->modifier === 'dollar') {
                    $totals['taxes_dollars'] += $m->amount;
                } elseif ($m->modifier === 'percent') {
                    $totals['taxes_percent'] += $m->amount;
                }
            }
        }
        return $totals;
    }

    public function get_active_fees($estimate_id=null, $exclude_code = true, $group_id=null, $exclude_single_use=true) {
        if ($estimate_id != null) {
            $estimate_id = $this->db->escape($estimate_id);
            $estimate_id = "estimates_has_fees.estimate_id=$estimate_id || ";
        } else {
            $estimate_id = '';
        }
        
        $this->db->select('*, estimates_has_fees.id AS ehf_id, sales_modifiers.id AS id');
        $this->db->from('sales_modifiers');
        $this->db->join('sales_modifiers_groups', 'sales_modifiers_id=sales_modifiers.id', 'left');
        $this->db->join('estimates_has_fees', 'estimates_has_fees.sales_modifier_id = sales_modifiers.id', 'LEFT');
        $where = "";
        if ($exclude_single_use) {
            $where .= 'sales_modifiers.single_use = 0 && ';
        }
        $where = "sales_modifiers.deleted = 0 && sales_modifiers.modifier_type IN ('fee', 'discount', 'tax') && (".$estimate_id."sales_modifiers.start_date IS NULL || sales_modifiers.end_date IS NULL || ((sales_modifiers.start_date < NOW() ) && (sales_modifiers.end_date >= NOW())) )";
        $this->db->where($where);
        if ($group_id) {
            $this->db->where('groups_id', $group_id);
        }
        $this->db->group_by('sales_modifiers.id');



        $fees = $this->db->get()->result();

        $fees_assoc = array();

        foreach ($fees as $fee) {
            if ($exclude_code) {
                unset($fee->code);
            }
            $fees_assoc[$fee->id] = $fee;
        }

        return $fees_assoc;
    }

    public function sortmodifiers($sales_modifiers) {
        $fees = array();
        $discounts = array();
        $taxes = array();
        foreach ($sales_modifiers as $sm) {
            if ($sm->modifier_type === 'fee') {
                $fees[] = $sm;
            } elseif ($sm->modifier_type === 'discount') {
                $discounts[] = $sm;
            } elseif ($sm->modifier_type === 'tax') {
                $taxes[] = $sm;
            }
        }
        return array('fees' => $fees, 'discounts' => $discounts, 'taxes' => $taxes);
    }

    public function setmodifiers($fees, $parent_id, $parent_id_name, $assoc_table, $user_fees, $delete_user_fees) {
        $delete_res = $this->db
                ->select('sales_modifier_id')->from($assoc_table)
                ->join('sales_modifiers', 'sales_modifier_id=sales_modifiers.id')
                ->where($parent_id_name, $parent_id)->where('single_use', 0)->get()->result();
        $delete_ids = array();
        foreach($delete_res as $d) {
            $delete_ids[] = $d->sales_modifier_id;
        }
        if (!empty($delete_ids)) {
            $this->db->where($parent_id_name, $parent_id)->where_in('sales_modifier_id', $delete_ids);
            $this->db->delete($assoc_table);
        }
        foreach ($fees as $fee) {
            $this->db->insert($assoc_table, array(
                $parent_id_name => $parent_id,
                'sales_modifier_id' => $fee
            ));
        }
        foreach ($user_fees as $fee_id => $fee) {
            if (substr($fee_id, 0, 4) === 'new_') {
                $fee['single_use'] = 1;
                $this->db->insert('sales_modifiers', $fee);
                $fee = $this->db->insert_id();
                $this->db->insert($assoc_table, array(
                    $parent_id_name => $parent_id,
                    'sales_modifier_id' => $fee
                ));
            }
        }
        if (!empty($delete_user_fees)) {
            $this->db->where_in('sales_modifier_id', $delete_user_fees)->delete($assoc_table);
        }
    }
    public function set_quote_modifiers($fees, $quote_id, $user_fees=array(), $delete_user_fees=array()) {
        $this->setmodifiers($fees, $quote_id, 'quote_id', 'quotes_has_fees', $user_fees, $delete_user_fees);
    }
    public function set_order_modifiers($fees, $quote_id, $user_fees=array(), $delete_user_fees=array()) {
        $this->setmodifiers($fees, $quote_id, 'order_id', 'orders_has_fees', $user_fees, $delete_user_fees);
    }
    public function set_estimate_modifiers($fees, $estimate_id, $user_fees=array(), $delete_user_fees=array()) {
        $this->setmodifiers($fees, $estimate_id, 'estimate_id', 'estimates_has_fees', $user_fees, $delete_user_fees);
    }
}
